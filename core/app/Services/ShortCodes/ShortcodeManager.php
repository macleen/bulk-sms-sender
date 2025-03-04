<?php

namespace App\Services\ShortCodes;

use App\Services\Templates\TemplateManager;
use App\Support\Tools;


class ShortcodeManager {

    protected const TOPIC = '_shcd'; // SHortCoDe
    protected string $pluginSlug;
    protected array $registeredShortcodes = [];

    public function __construct( protected TemplateManager $templateManager ) {

        $this->pluginSlug = Tools::generate_wp_slug('', config('app.plugin.unique_id') . self::TOPIC);
        $this->templateManager = new TemplateManager(); // Instantiate TemplateManager
    }

    public function registerShortcode(string $basic_shortcode_name, string $template_slug, string $template_label = '', $callback = null): string {
        if ($callback !== null && !\is_callable($callback)) {
            throw new \InvalidArgumentException("Shortcode Callback must be callable.");
        }

        $shortcode = $this->generateShortcodeName($basic_shortcode_name);
        add_shortcode($shortcode, $callback ?: [$this, 'renderTemplate']);
        $this->registeredShortcodes[] = $shortcode;

        // Register the template for the shortcode
        $this->templateManager->register_template($template_slug, $template_label);

        return $shortcode;
    }

    public function renderTemplate($atts = [], $content = null, $template_slug = ''): mixed {
        if (empty($template_slug)) {
            return ''; // Return empty if no template slug is provided
        }
    
        $template_path = resources_path("templates/{$template_slug}.php"); // Add .php extension
        $fallbackTemplate = resources_path('templates/fallback.php');
        $template = file_exists($template_path) ? $template_path : $fallbackTemplate;
    
        if (file_exists($template)) {
            ob_start();
            extract($atts); // Unpack attributes for use in the template
            $content ??= ''; // Ensure $content is at least an empty string : WP needs it
            include $template;
            return ob_get_clean();
        }
        return ''; // Return empty if no template found
    }

    public function generateShortcodeName(string $basic_shortcode_name): string {
        if (empty($basic_shortcode_name) || !\is_string($basic_shortcode_name)) {
            throw new \InvalidArgumentException("Shortcode name must be a non-empty string.");
        }
        return $this->pluginSlug . '_' . sanitize_key($basic_shortcode_name);
    }

    protected function is_shortcode_used_elsewhere(string $shortcode, callable $expected_callback): bool {
        global $shortcode_tags;

        if (empty($shortcode)) {
            throw new \InvalidArgumentException("Shortcode name cannot be empty.");
        }

        if (!isset($shortcode_tags[$shortcode])) {
            return false; // Not registered at all
        }

        $callback = $shortcode_tags[$shortcode];
        return $callback !== $expected_callback;
    }

    public function unregisterShortcode(string $name): void {
        $shortcode = $this->generateShortcodeName($name);
        remove_shortcode($shortcode);
        $this->registeredShortcodes = array_diff($this->registeredShortcodes, [$shortcode]);
    }

    public function registerShortcodes(array $shortcodes): void {
        foreach ($shortcodes as $name => $callback) {
            $this->registerShortcode($name, $callback);
        }
    }
}