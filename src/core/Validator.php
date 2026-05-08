<?php

class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value): self
    {
        if (trim((string)$value) === '') {
            $this->errors[$field] = ucfirst($field) . ' is required.';
        }
        return $this;
    }

    public function email(string $field, mixed $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email address.';
        }
        return $this;
    }

    public function minLength(string $field, mixed $value, int $min): self
    {
        if (strlen((string)$value) < $min) {
            $this->errors[$field] = ucfirst($field) . " must be at least {$min} characters.";
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $max): self
    {
        if (strlen((string)$value) > $max) {
            $this->errors[$field] = ucfirst($field) . " must not exceed {$max} characters.";
        }
        return $this;
    }

    public function numeric(string $field, mixed $value): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = ucfirst($field) . ' must be a number.';
        }
        return $this;
    }

    public function positive(string $field, mixed $value): self
    {
        if (!is_numeric($value) || (float)$value <= 0) {
            $this->errors[$field] = ucfirst($field) . ' must be a positive number.';
        }
        return $this;
    }

    public function matches(string $field, mixed $value, mixed $other, string $otherLabel = 'fields'): self
    {
        if ($value !== $other) {
            $this->errors[$field] = ucfirst($field) . " does not match {$otherLabel}.";
        }
        return $this;
    }

    public function inArray(string $field, mixed $value, array $allowed): self
    {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = ucfirst($field) . ' has an invalid value.';
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return array_values($this->errors)[0] ?? '';
    }
}