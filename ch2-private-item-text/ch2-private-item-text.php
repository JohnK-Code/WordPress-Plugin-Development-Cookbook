<?php
/*
Plugin Name: Chapter 2 - Private Item Text
Description: Create enclosing shortcode to make certain parts of website only available to logged in users.
Author: John Kelly
Version: 1.0.0
*/

add_shortcode('private', 'ch2pit_private_shortcode');

function ch2pit_private_shortcode($atts, $content = null) {
    if(is_user_logged_in()) {
        return '<div class="private">' . $content . '</div>';
    } else {
        $output = '<div class="register">';
        $output .= 'You need to become a member to ';
        $output .= 'access this content.</div>';
        return $output;
    }
}


add_action('wp_enqueue_scripts', 'ch2pit_queue_stylesheet');

function ch2pit_queue_stylesheet() {
    wp_enqueue_style('privateshortcodestyle', plugins_url('stylesheet.css', __FILE__));
}