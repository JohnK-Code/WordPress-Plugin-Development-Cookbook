<?php
/*
Plugin Name: Chapter 3 - Settings API
Description: Create a plugin settings configuration page using the Settings API
Version: 1.0.0
Author: John Kelly
*/


// Register a function that will be called when the plugin is activated 
register_activation_hook(__FILE__, 'ch3sapi_set_default_options');

// Define function to be called when plugin activated
// This function calls a seperate function to be called to set the default plugin options
function ch3sapi_set_default_options() {
    ch3sapi_get_options();
}

// Function used to set default plugin options
// Called from the registered plugin activation function above but can be called seperatly as well
// Kept seperate so can be called seperatly from activation function & used at anytime 
function ch3sapi_get_options() {
    $options = get_option('ch3sapi_options', array());
    $new_options['ga_account'] = 'UA-0000000-0';
    $new_options['track_outgoing_links'] = false;
    $merged_options = wp_parse_args($options, $new_options);
    $compare_options = array_diff_key($new_options, $options);
    if (empty($options) || !empty($compare_options)) {
        update_option('ch3sapi_options', $merged_options);
    }
    return $merged_options;
}

// Action hook called when wordpress admin is opened 
// Calls 'ch3sapi_admin_init' when wordpress admin is accessed
add_action('admin_init', 'ch3sapi_admin_init');

