<?php

namespace App\DataObjects;

class Setting {
    public function __construct(
        public string $name,                                        // The option name
        public mixed $default            = null,                    // Default value
        public string $sanitize_callback = 'sanitize_text_field',   // Sanitization function
        public bool $autoload            = true                     // Autoload setting
    ) {}
}