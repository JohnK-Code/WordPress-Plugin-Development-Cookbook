<?php
/*
Plugin Name: Chapter 3 - Multi-level menu
Description: Create a multi-level menu in the WordPress admin
Version: 1.0.0
Author: John Kelly
*/


add_action('admin_menu', 'ch3mlm_admin_menu');

function ch3mlm_admin_menu()
{
    // Icon for custom WP menu using an SVG image
    $menu_icon = '<?xml version="1.0" encoding="utf-8"?>
    <svg version="1.1" id="layer" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 130">
    <style type="text/css">.st0{fill:#EC670F;}</style>
    <g id="g27" transform="translate(-531.85618,-266.66067)">
        <polygon class="st0" fill="black" points="620.6,329.1 612.1,340.5 576.5,385.6 546.9,385.6 592.1,329 547.3,278.8 576.7,278.8 612,319.3" id="polygon25" />
    </g>
    </svg>';

    // Create top-level menu item on WordPress admin page
    add_menu_page(
        'My Complex Plugin Configuration Page',
        'My Complex Plugin',
        'manage_options',
        'ch3mlm-main-menu',
        'ch3mlm_my_complex_main',
        'data:image/svg+xml;base64,'. base64_encode($menu_icon)

    );

    // Create a sub-menu under the top-level menu
    add_submenu_page(
        'ch3mlm-main-menu',
        'My Complex Menu Sub-Config Page',
        'Sub-Config Page',
        'manage_options',
        'ch3mlm-sub-menu',
        'ch3mlm_my_complex_submenu'
    );
}
