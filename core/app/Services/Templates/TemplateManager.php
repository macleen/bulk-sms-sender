<?php

namespace App\Services\Templates;

class TemplateManager {
    protected string $templateDir;

    public function __construct() {
        $this->templateDir = rtrim(resources_path('templates'), '/');
    }

    /**
     * Register a custom template for pages dynamically.
     *
     * @param string $template_slug The template file slug (e.g., 'msp.php').
     * @param string $template_label The label for the template in WP editor.
     */
    public function register_template(string $template_slug, string $template_label): void {
        add_filter('theme_page_templates', function (array $templates, $post) use ($template_slug, $template_label) {
            if ($post->post_type === 'page') {
                $templates[$template_slug] = $template_label;
            }
            return $templates;
        }, 10, 2);

        $this->register_dynamic_template($template_slug);
    }

    /**
     * Dynamically load the template if it's selected in the page editor.
     *
     * @param string $template_slug The template file slug.
     */
    public function register_dynamic_template(string $template_slug): void {
        add_action('template_redirect', function () use ($template_slug) {
            global $post;
    
            if ($post && get_page_template_slug($post->ID) === $template_slug) {
                $custom_template = "{$this->templateDir}/{$template_slug}.php"; // Add .php extension
                if (file_exists($custom_template)) {
                    error_log('Loading custom template: ' . $custom_template); // Debug template loading
                    include $custom_template;
                    exit; // Stop further processing
                } else {
                    error_log('Custom template not found: ' . $custom_template); // Debug template not found
                }
            }
        });
    }
}