<?php
/*
Plugin Name: Chapter 2 - Generator Filter
Description: Modify generator meta tag found in the site's HTML source code
Version: 1.0.0
Author: Jonathon Kelly
*/

add_filter('the_generator', 'ch2gf_generator_filter', 10, 2);

function ch2gf_generator_filter ( $html, $type ) {
    if ($type == 'xhtml' ) {
        $html = preg_replace( '("WordPress.*?")',
        '"Yannick Lefebvre"', $html );
    }
    return $html;
}