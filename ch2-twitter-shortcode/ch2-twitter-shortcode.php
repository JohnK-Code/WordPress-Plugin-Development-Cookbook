<?php
/*
Plugin Name: Chapter 2 - Twitter Shortcode
Description: Plugin used to create a shortcode to add link to Twitter pages on any post or page.
Version: 1.0.0
Author: John Kelly
*/

add_shortcode('tl', 'ch2ts_twitter_link_shortcode');

function ch2ts_twitter_link_shortcode($atts) {
    $output = '<a href="https://twitter.com/ylefebvre">';
    $output .= 'Twitter Feed</a>';
    return $output;
}