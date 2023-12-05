<?php
/*
Plugin Name: Chapter 2 - Nav Menu Filter
Description: Hide item from nav menu for users who are not logged in 
Version: 1.0.0
Author: Jonathon Kelly
*/

add_filter( 'wp_nav_menu_objects', 'ch2nmf_new_nav_menu_items', 10, 2 );

function ch2nmf_new_nav_menu_items ($sorted_menu_items, $args ) {
    // Check if user is logged in, continue if not logged in
    if ( is_user_logged_in() == FALSE ) {
        // Loop through all menu items received
        // Place each item's key in $key variable
        foreach ( $sorted_menu_items as $key => $sorted_menu_item) {
            // Check if menu item matches search string
            if ( 'Private Area' == $sorted_menu_item->title ) {
                // Remove item from menu array if found
                // using item key
                unset( $sorted_menu_items[ $key ] );
            }
        }
    }
    return $sorted_menu_items;
}