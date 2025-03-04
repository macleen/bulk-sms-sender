<?php
/*
Template Name: Fallback Template
*/

// Prevent direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Load WordPress header functions
wp_head();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
</head>
<body <?php body_class(); ?>>
    <p>This is the fallback template. The requested template was not found.</p>
</body>
<?php
// Load WordPress footer functions
wp_footer();
?>
</html>