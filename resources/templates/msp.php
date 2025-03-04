<?php
/*
Template Name: MSP Page
*/

// Prevent direct access to the template

use App\Support\Tools;
use App\Support\HashedStorage;

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
    <style>
        .loader {
            border: 9px solid #f3f3f3;
            border-radius: 50%;
            border-top: 9px solid #3498db;
            width: 90px;
            height: 90px;
            animation: spin 1s linear infinite;
            margin: 10% auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body <?php body_class(); ?>>
<?php
    $redirect_code = $atts['redirect_code'] ?? null; // Access the GET parameter passed via shortcode
    if ($redirect_code) {
        $res = HashedStorage::update_meta_data( $redirect_code);
        if ( $res['ok']) {
            echo '<div class="loader"></div>';
            Tools::submit_form_with_redirect(get_option('redirect_to'), $res['data']);
        }else {
            wp_die(esc_html($res['message']));
        }   
    } else wp_die( 'Missing hash-index');    
    ?>
</body>
<?php
// Load WordPress footer functions
wp_footer();
?>
</html>