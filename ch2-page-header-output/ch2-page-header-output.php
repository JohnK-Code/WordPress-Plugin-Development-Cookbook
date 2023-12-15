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


add_filter( 'the_content', 'ch2lfa_link_filter_analytics' );

function ch2lfa_link_filter_analytics ( $the_content ) {
	$new_content = str_replace( 'href', 'onClick="recordOutboundLink(this);return false;" href', $the_content );

	return $new_content;
}


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


register_activation_hook(__FILE__, 'ch2pho_set_default_options_array');

function ch2pho_set_default_options_array() {
    ch2pho_get_options();
}
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


add_action('admin_menu', 'ch2pho_settings_menu', 1);

function ch2pho_settings_menu() {
    add_options_page(
        'My Google Analytics Configuration',
        'My Google Analytics', 'manage_options',
        'ch2pho-my-google-analytics',
        'ch2pho_config_page'
    );
}

function ch2pho_config_page() {
    // Retrieve plugin options from database
    $options = ch2pho_get_options();
    ?>

    <div id="ch2pho-general" class="wrap">
        <h2>My Google Analytics</h2><br/>
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
    wp_redirect(add_query_arg('page', 'ch2pho-my-google-analytics', admin_url('options-general.php')));
    exit;
}
