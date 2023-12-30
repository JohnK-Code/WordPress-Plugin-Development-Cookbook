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
    $new_options['ga_account_name'] = 'UA-0000000-0';
    $new_options['track_outgoing_links'] = false;
    $new_options['select_list'] = 'First'; // ##### Delete later if required
    $merged_options = wp_parse_args($options, $new_options);
    $compare_options = array_diff_key($new_options, $options);
    if (empty($options) || !empty($compare_options)) {
        update_option('ch3sapi_options', $merged_options);
    }
    return $merged_options;
}

// Action hook called when wordpress admin is opened or refreshed
// Calls 'ch3sapi_admin_init' when wordpress admin is accessed
add_action('admin_init', 'ch3sapi_admin_init');

// Used to define a settings group, section and it's fields
function ch3sapi_admin_init() {
    // Register/define a setting group with a validation
    // function so that post data hadling is done 
    // automatically for us
    register_setting('ch3sapi_settings', 'ch3sapi_options', 'ch3sapi_validate_options');

    // Add a new settings section within the setting group
    add_settings_section('ch3sapi_main_section', 
    'Main Settings', 
    'ch3sapi_main_setting_section_callback',
    'ch3sapi_settings_section');

    // Add each field with its name and function to 
    // use for our new settings, put in new section
    // Adds a field to an admin settings page section
    add_settings_field('ga_account_name', 
    'Account Name', 
    'ch3sapi_display_text_field',
    'ch3sapi_settings_section',
    'ch3sapi_main_section',
    array('name' => 'ga_account_name'));
    add_settings_field('track_outgoing_links',
    'Track Outgoing Links',
    'ch3sapi_display_check_box',
    'ch3sapi_settings_section',
    'ch3sapi_main_section',
    array('name' => 'track_outgoing_links'));
    // ##### Delete later if required
    add_settings_field('select_list',
    'Select List',
    'ch3sapi_select_list', 
    'ch3sapi_settings_section',
    'ch3sapi_main_section',
    array('name' => 'select_list',
    'choices' => array('First', 'Second', 'Third')));
}

// Validates any data entered on the form on the settings page in admin for this plugin
function ch3sapi_validate_options($input) {
    foreach(array('ga_account_name') as $option_name) {
        if (isset($input[$option_name])) {
            $input[$option_name] = sanitize_text_field($input[$option_name]);
        }
    }

    foreach(array('track_outgoing_links') as $option_name) {
        if (isset($input[$option_name])) {
            $input[$option_name] = true;
        } else {
            $input[$option_name] = false;
        }
    }
    return $input;
}

// Function used to echo out any content at the top of the plugin settings section for this plugin settings page in the WP admin
function ch3sapi_main_setting_section_callback() { ?>
    <p>This is the main configuration section.</p>
<?php }

// Function used to output the necessary HTML to display the field from the plugin settings page section in the settings page group
// Basically it is called by the 'add_settings_field' function above to display a field in the settings page form.
function ch3sapi_display_text_field($data = array()) {
    extract($data);
    $options = ch3sapi_get_options();
    ?>

    <input type="text" name="ch3sapi_options[<?php echo esc_html($name); ?>]"
    value="<?php echo esc_html($options[$name]); ?>"/>
    <br/>
<?php }

// Function used to output the necessary HTML to display the field from the plugin settings page section in the settings page group
// Basically it is called by the 'add_settings_field' function above to display a field in the settings page form.
function ch3sapi_display_check_box($data = array()) {
    extract($data);
    $options = ch3sapi_get_options();
    ?>

    <input type="checkbox" name="ch3sapi_options[<?php echo esc_html($name); ?>]"
    <?php checked($options[$name]); ?>/>
<?php }

// ##### Delete later if required
// Function used to output the necessary HTML to display the field from the plugin settings page section in the settings page group
// Basically it is called by the 'add_settings_field' function above to display a field in the settings page form.
function ch3sapi_select_list($data = array()) {
    extract($data);
    $options = ch3sapi_get_options();
    ?>
    <select name="ch3sapi_options[<?php echo esc_html($name); ?>]">
    <?php foreach($choices as $item) { ?>
        <option value="<?php echo esc_html($item); ?>"
        <?php selected($options[$name] == $item); ?>>
        <?php echo esc_html($item); ?>
        </option>;
    <?php } ?>
    </select>
<?php }

add_action('admin_menu', 'ch3sapi_settings_menu');

function ch3sapi_settings_menu() {
    add_options_page(
        'My Google Analytics Configuration',
        'My Google Analytics - Settings API',
        'manage_options',
        'ch3sapi-my-google-analytics',
        'ch3sapi_config_page'
    );
}

function ch3sapi_config_page() { ?>
    <div id="ch3sapi-general" class="wrap">
        <h2>My Google Analytics - Settings API</h2>

        <form name="ch3sapi_options_form_settings_api" method="post" action="options.php">
            <?php settings_fields('ch3sapi_settings'); ?>
            <?php do_settings_sections('ch3sapi_settings_section'); ?>
            <input type="submit" value="Submit" class="button-primary" />
        </form>
    </div>
<?php }



// ######### Test Code Point 