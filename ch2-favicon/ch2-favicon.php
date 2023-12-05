<?php
/*
Plugin Name: Chapter 2 - Favicon
Description: Plugin used to add an icon to the page header
Version: 1.0.0
Author: Jonathon Kelly
*/

add_action( 'wp_head', 'ch2fi_page_header_output' );

function ch2fi_page_header_output() {
    $site_icon_url = get_site_icon_url();
    if ( !empty($site_icon_url )) {
        wp_site_icon();
    } else {
        $icon = plugins_url('favicon.ico', __FILE__); ?>
        <link rel="shortcut icon" href="<?php echo esc_url($icon); ?>" />
    <?php
    }
}