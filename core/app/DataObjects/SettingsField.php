<?php namespace App\DataObjects;

/**
 * Represents a settings section in the WordPress admin.
 */
class SettingsField {
    public function __construct(
        public string $id,
        public string $title,
        public string $section,
        public string $page,
        public        $callback,    // The function to render the field
        public ?array $args = []    // Extra arguments for customization
    ) {}

}