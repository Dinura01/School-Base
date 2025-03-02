<?php

class Auth implements IAuth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login(string $email, string $password, bool $remember = false): bool {
        // Implement login logic here
        return true;
    }

    public function register(string $name, string $email, string $password, string $role, ?string $phone = null): bool {
        // Implement registration logic here
        return true;
    }

    public function hasPermission(string $permission): bool {
        // Implement permission check logic here
        return true;
    }

    public function getCurrentUser(): ?array {
        // Implement get current user logic here
        return null;
    }

    public function logout(): void {
        // Implement logout logic here
    }
}