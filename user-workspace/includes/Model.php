<?php
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDelete = false;
    protected $cache;
    protected $cacheTimeout = 3600; // 1 hour
    protected $relations = [];
    protected $attributes = [];
    protected $original = [];

    /**
     * Constructor
     */
    public function __construct(array $attributes = []) {
        $this->db = Database::getInstance();
        $this->cache = Cache::getInstance();
        $this->fill($attributes);
    }

    /**
     * Fill model with attributes
     */
    public function fill(array $attributes): self {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * Set attribute
     */
    public function setAttribute(string $key, $value): void {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attribute
     */
    public function getAttribute(string $key) {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * Check if attribute exists
     */
    public function hasAttribute(string $key): bool {
        return isset($this->attributes[$key]);
    }

    /**
     * Get original attribute value
     */
    public function getOriginal(string $key = null) {
        if ($key === null) {
            return $this->original;
        }
        return $this->original[$key] ?? null;
    }

    /**
     * Get changed attributes
     */
    public function getDirty(): array {
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }

    /**
     * Save model
     */
    public function save(): bool {
        if (empty($this->attributes[$this->primaryKey])) {
            return $this->insert();
        }
        return $this->update();
    }

    /**
     * Insert new record
     */
    protected function insert(): bool {
        // Add timestamps
        if ($this->timestamps) {
            $this->attributes['created_at'] = date('Y-m-d H:i:s');
            $this->attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert($this->table, $this->attributes);
        if ($id) {
            $this->attributes[$this->primaryKey] = $id;
            $this->original = $this->attributes;
            $this->clearCache();
            return true;
        }
        return false;
    }

    /**
     * Update record
     */
    protected function update(): bool {
        $dirty = $this->getDirty();
        if (empty($dirty)) {
            return true;
        }

        // Add updated timestamp
        if ($this->timestamps) {
            $dirty['updated_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->db->update(
            $this->table,
            $dirty,
            "{$this->primaryKey} = ?",
            [$this->attributes[$this->primaryKey]]
        );

        if ($result) {
            $this->original = $this->attributes;
            $this->clearCache();
            return true;
        }
        return false;
    }

    /**
     * Delete record
     */
    public function delete(): bool {
        if ($this->softDelete) {
            return $this->db->update(
                $this->table,
                ['deleted_at' => date('Y-m-d H:i:s')],
                "{$this->primaryKey} = ?",
                [$this->attributes[$this->primaryKey]]
            );
        }

        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$this->attributes[$this->primaryKey]]
        );
    }

    /**
     * Find record by ID
     */
    public static function find($id): ?self {
        $instance = new static();
        $cacheKey = static::class . ':' . $id;

        // Try to get from cache
        if ($cached = $instance->cache->get($cacheKey)) {
            return new static($cached);
        }

        $result = $instance->db->query(
            "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?",
            [$id]
        )->fetch();

        if ($result) {
            $instance->cache->set($cacheKey, $result, $instance->cacheTimeout);
            return new static($result);
        }

        return null;
    }

    /**
     * Find all records
     */
    public static function all(): array {
        $instance = new static();
        $cacheKey = static::class . ':all';

        // Try to get from cache
        if ($cached = $instance->cache->get($cacheKey)) {
            return array_map(function($item) {
                return new static($item);
            }, $cached);
        }

        $query = "SELECT * FROM {$instance->table}";
        if ($instance->softDelete) {
            $query .= " WHERE deleted_at IS NULL";
        }

        $results = $instance->db->query($query)->fetchAll();
        $instance->cache->set($cacheKey, $results, $instance->cacheTimeout);

        return array_map(function($item) {
            return new static($item);
        }, $results);
    }

    /**
     * Find records by where clause
     */
    public static function where(string $column, $value): array {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE {$column} = ?";
        
        if ($instance->softDelete) {
            $query .= " AND deleted_at IS NULL";
        }

        $results = $instance->db->query($query, [$value])->fetchAll();

        return array_map(function($item) {
            return new static($item);
        }, $results);
    }

    /**
     * Create new record
     */
    public static function create(array $attributes): self {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }

    /**
     * Update multiple records
     */
    public static function updateAll(array $attributes, string $where = '', array $params = []): int {
        $instance = new static();
        if ($instance->timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        $result = $instance->db->update($instance->table, $attributes, $where, $params);
        $instance->clearCache();
        return $result;
    }

    /**
     * Delete multiple records
     */
    public static function deleteAll(string $where = '', array $params = []): int {
        $instance = new static();
        if ($instance->softDelete) {
            return static::updateAll(
                ['deleted_at' => date('Y-m-d H:i:s')],
                $where,
                $params
            );
        }
        $result = $instance->db->delete($instance->table, $where, $params);
        $instance->clearCache();
        return $result;
    }

    /**
     * Count records
     */
    public static function count(string $where = '', array $params = []): int {
        $instance = new static();
        $query = "SELECT COUNT(*) FROM {$instance->table}";
        
        if ($where) {
            $query .= " WHERE {$where}";
            if ($instance->softDelete) {
                $query .= " AND deleted_at IS NULL";
            }
        } elseif ($instance->softDelete) {
            $query .= " WHERE deleted_at IS NULL";
        }

        return (int) $instance->db->getValue($query, $params);
    }

    /**
     * Clear model cache
     */
    protected function clearCache(): void {
        $this->cache->delete(static::class . ':' . ($this->attributes[$this->primaryKey] ?? ''));
        $this->cache->delete(static::class . ':all');
    }

    /**
     * Magic getter
     */
    public function __get(string $key) {
        return $this->getAttribute($key);
    }

    /**
     * Magic setter
     */
    public function __set(string $key, $value) {
        $this->setAttribute($key, $value);
    }

    /**
     * Magic isset
     */
    public function __isset(string $key) {
        return $this->hasAttribute($key);
    }

    /**
     * Convert to array
     */
    public function toArray(): array {
        $array = [];
        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, $this->hidden)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string {
        return json_encode($this->toArray());
    }

    /**
     * String representation
     */
    public function __toString(): string {
        return $this->toJson();
    }
}
