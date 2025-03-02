<?php
class Validator {
    private $errors = [];
    private $data = [];
    private $rules = [];
    private $messages = [];
    private $customMessages = [];
    private $db;

    /**
     * Constructor
     */
    public function __construct(array $data = [], array $rules = [], array $messages = []) {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->db = Database::getInstance();
    }

    /**
     * Validate data against rules
     */
    public function validate(): bool {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $value = $this->getValue($field);
            $rules = is_string($rules) ? explode('|', $rules) : $rules;

            foreach ($rules as $rule) {
                $parameters = [];

                if (strpos($rule, ':') !== false) {
                    list($rule, $parameter) = explode(':', $rule, 2);
                    $parameters = explode(',', $parameter);
                }

                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    if (!$this->$method($field, $value, $parameters)) {
                        $this->addError($field, $rule, $parameters);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Get first error for a field
     */
    public function getFirstError(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get value from data
     */
    private function getValue(string $field) {
        $keys = explode('.', $field);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Add validation error
     */
    private function addError(string $field, string $rule, array $parameters = []): void {
        $message = $this->customMessages[$field . '.' . $rule] 
                  ?? $this->customMessages[$field] 
                  ?? $this->getDefaultMessage($field, $rule, $parameters);

        $this->errors[$field][] = $this->replaceParameters($message, $field, $parameters);
    }

    /**
     * Get default error message
     */
    private function getDefaultMessage(string $field, string $rule, array $parameters): string {
        $messages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field must be a valid email address.',
            'min' => 'The :field must be at least :min characters.',
            'max' => 'The :field may not be greater than :max characters.',
            'between' => 'The :field must be between :min and :max.',
            'numeric' => 'The :field must be a number.',
            'integer' => 'The :field must be an integer.',
            'alpha' => 'The :field may only contain letters.',
            'alpha_num' => 'The :field may only contain letters and numbers.',
            'alpha_dash' => 'The :field may only contain letters, numbers, dashes and underscores.',
            'date' => 'The :field is not a valid date.',
            'url' => 'The :field format is invalid.',
            'unique' => 'The :field has already been taken.',
            'exists' => 'The selected :field is invalid.',
            'confirmed' => 'The :field confirmation does not match.',
            'regex' => 'The :field format is invalid.',
            'in' => 'The selected :field is invalid.',
            'not_in' => 'The selected :field is invalid.',
            'image' => 'The :field must be an image.',
            'mimes' => 'The :field must be a file of type: :values.',
            'size' => 'The :field must be :size kilobytes.',
            'digits' => 'The :field must be :digits digits.',
            'digits_between' => 'The :field must be between :min and :max digits.',
            'phone' => 'The :field must be a valid phone number.',
            'password' => 'The :field must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
        ];

        return $messages[$rule] ?? "The :field field is invalid.";
    }

    /**
     * Replace message parameters
     */
    private function replaceParameters(string $message, string $field, array $parameters): string {
        $replace = [
            ':field' => str_replace('_', ' ', $field),
            ':min' => $parameters[0] ?? '',
            ':max' => $parameters[1] ?? '',
            ':size' => $parameters[0] ?? '',
            ':values' => implode(', ', $parameters),
            ':digits' => $parameters[0] ?? ''
        ];

        return str_replace(array_keys($replace), array_values($replace), $message);
    }

    /**
     * Validation Rules
     */

    protected function validateRequired(string $field, $value): bool {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }
        return true;
    }

    protected function validateEmail(string $field, $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin(string $field, $value, array $parameters): bool {
        $length = is_numeric($value) ? $value : mb_strlen($value);
        return $length >= (int)$parameters[0];
    }

    protected function validateMax(string $field, $value, array $parameters): bool {
        $length = is_numeric($value) ? $value : mb_strlen($value);
        return $length <= (int)$parameters[0];
    }

    protected function validateBetween(string $field, $value, array $parameters): bool {
        $length = is_numeric($value) ? $value : mb_strlen($value);
        return $length >= (int)$parameters[0] && $length <= (int)$parameters[1];
    }

    protected function validateNumeric(string $field, $value): bool {
        return is_numeric($value);
    }

    protected function validateInteger(string $field, $value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateAlpha(string $field, $value): bool {
        return preg_match('/^[\pL\pM]+$/u', $value);
    }

    protected function validateAlphaNum(string $field, $value): bool {
        return preg_match('/^[\pL\pM\pN]+$/u', $value);
    }

    protected function validateAlphaDash(string $field, $value): bool {
        return preg_match('/^[\pL\pM\pN_-]+$/u', $value);
    }

    protected function validateDate(string $field, $value): bool {
        return strtotime($value) !== false;
    }

    protected function validateUrl(string $field, $value): bool {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function validateUnique(string $field, $value, array $parameters): bool {
        $table = $parameters[0];
        $column = $parameters[1] ?? $field;
        $except = $parameters[2] ?? null;

        $query = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$value];

        if ($except) {
            $query .= " AND id != ?";
            $params[] = $except;
        }

        return (int)$this->db->getValue($query, $params) === 0;
    }

    protected function validateExists(string $field, $value, array $parameters): bool {
        $table = $parameters[0];
        $column = $parameters[1] ?? $field;

        return (bool)$this->db->exists($table, "{$column} = ?", [$value]);
    }

    protected function validateConfirmed(string $field, $value): bool {
        return $value === ($this->data[$field . '_confirmation'] ?? null);
    }

    protected function validateRegex(string $field, $value, array $parameters): bool {
        return preg_match($parameters[0], $value);
    }

    protected function validateIn(string $field, $value, array $parameters): bool {
        return in_array($value, $parameters);
    }

    protected function validateNotIn(string $field, $value, array $parameters): bool {
        return !in_array($value, $parameters);
    }

    protected function validateImage(string $field, $value): bool {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($_FILES[$field]['type'], $allowedTypes);
    }

    protected function validateMimes(string $field, $value, array $parameters): bool {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        return in_array($extension, $parameters);
    }

    protected function validateSize(string $field, $value, array $parameters): bool {
        if (!isset($_FILES[$field])) {
            return false;
        }

        return $_FILES[$field]['size'] <= ((int)$parameters[0] * 1024);
    }

    protected function validateDigits(string $field, $value, array $parameters): bool {
        return preg_match('/^\d{' . $parameters[0] . '}$/', $value);
    }

    protected function validateDigitsBetween(string $field, $value, array $parameters): bool {
        return preg_match('/^\d{' . $parameters[0] . ',' . $parameters[1] . '}$/', $value);
    }

    protected function validatePhone(string $field, $value): bool {
        return preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $value);
    }

    protected function validatePassword(string $field, $value): bool {
        // At least one uppercase letter, one lowercase letter, one number, and one special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }
}
