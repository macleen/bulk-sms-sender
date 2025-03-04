<?php namespace App\DataObjects;

/**
 * Represents a settings section in the WordPress admin.
 */
class SettingsSection {
    public function __construct(
        public string $id,
        public string $title,
        public string $description = '',
        public string $page = ''
    ) {}
}