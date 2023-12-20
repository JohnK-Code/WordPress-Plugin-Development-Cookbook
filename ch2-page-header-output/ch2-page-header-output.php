<?php
/*
Plugin Name: Chapter 2 - Page Header Output
Plugin URI:
Description: Plugin used for analytics, working with wp options and WordPress admin config
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca
License: GPLv2
*/


// Adding output to page headers using plugin actions - Adds Goolgle Analytics code to to page header using action hook
add_action('wp_head', 'ch2pho_page_header_output');

function ch2pho_page_header_output()
{ ?>
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                },
                i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })
        (window, document, 'script',
            'https://www.google-analytics.com/analytics.js',
            'ga');
        ga('create', 'UA-0000000-0', 'auto');
        ga('send', 'pageview');
    </script>
<?php }


// Inserting link tracking code in the page body using plugin filters - Use WP plugin filter to add a javascript onclick function to any href link in a page or posts content
// Replaces the href with JS onclick code then adds href back at the end 
add_filter( 'the_content', 'ch2lfa_link_filter_analytics' );

function ch2lfa_link_filter_analytics ( $the_content ) {
	$new_content = str_replace( 'href', 'onClick="recordOutboundLink(this);return false;" href', $the_content );

	return $new_content;
}


// Continued from above - Inserting link tracking code in the page body using plugin filters
// Use action hook to add JavaScript code to wordpress footer - the JavaScript code below defines the onClick="recordOutboundLink" function called above
add_action( 'wp_footer', 'ch2lfa_footer_analytics_code' );

function ch2lfa_footer_analytics_code() { ?>
    
<script type="text/javascript">
  function recordOutboundLink( link ) {
	ga('send', 'event', 'Outbound Links', 'Click',
		link.href, {
			'transport': 'beacon',
			'hitCallback': function() { 
				document.location = link.href; 
			}
		} );
	}
</script>

<?php }


// Creating default user settings on plugin initialization
// Create defult options in wp database for plugin when first activated
// Options stored in an array in database
register_activation_hook(__FILE__, 'ch2pho_set_default_options_array');

function ch2pho_set_default_options_array() {
    ch2pho_get_options();
}
// Seperate function used to get and update wp database options
// Is used above when plugin first activated but can be called from other parts of the plugin if required
function ch2pho_get_options() {
    $options = get_option('ch2pho_options', array());
    $new_options['ga_account_name'] = 'UA-0000000-0';
    $new_options['track_outgoing_links'] = false;
    $merged_options = wp_parse_args($options, $new_options);
    $compare_options = array_diff_key($new_options, $options);
    if (empty($options) || !empty($compare_options)) {
        update_option('ch2pho_options', $merged_options);
    }
    return $merged_options;
}


// Creating an administration page menu item in the settings menu
// First need to change WP_DEBUG varaiable to true in wp-config.php, this is a default file in the root wp folder
// Use action hook to call a user defined function - function creates a settings menu item for the plugin in the wp admin settings menu area
add_action('admin_menu', 'ch2pho_settings_menu', 1);

function ch2pho_settings_menu() {
    $options_page = add_options_page(
        'My Google Analytics Configuration',
        'My Google Analytics', 'manage_options',
        'ch2pho-my-google-analytics',
        'ch2pho_config_page'
    );
    if(!empty($options_page)) {
        add_action('load-' . $options_page, 'ch2pho_help_tabs');
    }
}

// Rendering the plugin admin page contents using HTML - page for plugin seetings menu item above
// Define function call 'ch2pho_config_page' from add_option_page function above
// Basically creates the page (In HTML) for the plugin settings to be edited in the wp admin 
// Page accessed by using settings menu item created above in wp admin 
// NOTE * - wp_nonce_field is a security field in a wordpress form to make sure data has been submitted from the wp admin pages 
function ch2pho_config_page() {
    // Retrieve plugin options from database
    $options = ch2pho_get_options();
    ?>

    <div id="ch2pho-general" class="wrap">
        <h2>My Google Analytics</h2><br/>
        <?php 
        if(isset($_GET['message']) && $_GET['message'] == '1') { ?>
        <div id="message" class="updated fade">
            <p><strong>Settings Saved</strong></p>
        </div>
        <?php } ?>
        <form method="post" action="admin-post.php">
            <input type="hidden" name="action" value="save_ch2pho_options">

            <!-- Adding hidden security referrer field -->
            <?php wp_nonce_field('ch2pho'); ?>
            Account Name: <input type="text" name="ga_account_name" value="<?php echo esc_html(
                $options['ga_account_name']
            ); ?>"/>
            <br/>
            Track Outgoing Links: <input type="checkbox" name="track_outgoing_links" <?php checked(
                $options['track_outgoing_links']
            ); ?>/>
            <br><br>
            <input type="submit" value="Submit" class="button-primary">
        </form>
    </div>
<?php }


// Processing and storing plugin configuration data
// Action hook to call function that uses data entered in the wp admin plugin settings page created above 
// Basically - This code takes the settings entered in the plugin settings page I created earlier (wp admin) and stores the updated settings in the database
// Checks user is authorized to change settings and form is from wp admin (check_admin_referer('ch2pho'))
// Redirects back to plugin settings page with updated plugin options/settings data
add_action('admin_init', 'ch2pho_admin_init');

function ch2pho_admin_init() {
    add_action('admin_post_save_ch2pho_options', 'process_ch2pho_options');
}
function process_ch2pho_options() {
    // Check that user has the proper security level
    if (!current_user_can('manage_options')) {
        wp_die('Not allowed');
    }
    // Check if nonce field configuration form is present
    check_admin_referer('ch2pho');
    // Retrieve original plugin options array
    $options = ch2pho_get_options();

    // Cycle through all text form fields and store their
    // values in the options array
    foreach(array('ga_account_name') as $option_name) {
        if (isset($_POST[$option_name])) {
            $options[$option_name] = sanitize_text_field($_POST[$option_name]);
        }
    }

    // Cycle through all check box form fields and set the 
    // options array to true or false values
    foreach(array('track_outgoing_links') as $option_name) {
        if(isset($_POST[$option_name])) {
            $options[$option_name] = true;
        } else {
            $options[$option_name] = false;
        }
    }

    // Store updated options array to the database
    update_option('ch2pho_options', $options);
    // Redirect the page to the configuration form
    wp_redirect(add_query_arg(array('page' => 'ch2pho-my-google-analytics', 'message' => '1'), admin_url('options-general.php')));
    exit;
}


function ch2pho_help_tabs() {
    $screen = get_current_screen();
    $screen->add_help_tab(array(
        'id' => 'ch2pho-plugin-help-instruments',
        'title' => 'Instructions',
        'callback' => 'ch2pho_plugin_help_instructions',
    ));

    $screen->add_help_tab(array(
        'id' => 'ch2pho-plugin-help-faq',
        'title' => 'FAQ',
        'callback' => 'ch2pho_plugin_help_faq',        
    ));

    $screen->set_help_sidebar(
        '<p>This is the sidebar content</p>'
    );
}


function ch2pho_plugin_help_instructions() { ?>
    <p>These are instructions explaining how to use this plugin.</p>
    <?php }


function ch2pho_plugin_help_faq() { ?>
    <p>These are the most frequently asked questions on the use of this plugin.</p>
    <?php }