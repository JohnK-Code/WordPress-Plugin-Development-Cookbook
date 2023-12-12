<?php
/*
Plugin Name: Chapter 3 - Individual Options
Description: Creat default user settings on plugin initialization
Version: 1.0.0
Author: John Kelly
*/

register_activation_hook(__FILE__, 'ch3io_set_default_options');

function ch3io_set_default_options() {
    if (false === get_option('ch3io_ga_account_name')) {
        add_option('ch3io_ga_account_name', 'UA-0000000-0');
    }
}