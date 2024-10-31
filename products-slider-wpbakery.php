<?php
/**
 * Plugin Name: Products Slider for WPBakery
 * Description: Show WPBakery Products Slider for Featured, OnSale or similar criteria
 * Plugin URI:  https://elementinvader.com
 * Version:     1.0.0
 * Author:      ElementInvader
 * Author URI:  elementinvader.com 
 * Text Domain: psfwpb
 * Domain Path: /locale/
 */

if(version_compare(phpversion(), '5.5.0', '<'))
{
    return false;  
}

define( 'PSFWPB_VERSION', '1.0' );
define( 'PSFWPB_NAME', 'psfwpb' );
define( 'PSFWPB__FILE__', __FILE__ );
define( 'PSFWPB_PATH', plugin_dir_path( __FILE__ ) );
define( 'PSFWPB_URL', plugin_dir_url( __FILE__ ) );


add_action( 'plugins_loaded', 'psfwpb_plugin_laoded' );
function psfwpb_plugin_laoded() {
	// Setup locale
	do_action( 'sw_win_plugins_loaded' );
	load_plugin_textdomain('psfwpb', false, basename( dirname( __FILE__ ) ) . '/locale' );

	// Check WOO required
	if ( !class_exists( 'WooCommerce' ) ) {
		$message = '<p>' . esc_html__( 'Products Slider for WPBakery is not working because you are not use WooCommerce.', 'psfwpb' ) . '</p>';
		
		psfwpb_notify_admin('fail_load_out_of_date', $message, function()
                        {
                            $admin_page = get_current_screen();
                            if( $admin_page->base != "dashboard" ) return true;
                            if( ! current_user_can( 'update_plugins' ) ) return true;
                        }, 'notice notice-warning'
		);
		return;
	}

}


function psfwpb_setup(){
    wp_enqueue_style( 'fontawesome-5', plugins_url( '/assets/libs/fontawesome-5.8/css/fontawesome-5.css', PSFWPB__FILE__), false, false); 
    wp_enqueue_script( PSFWPB_NAME.'-main', plugins_url( '/assets/js/main.js', PSFWPB__FILE__), [ 'jquery' ], PSFWPB_VERSION, true );
    wp_enqueue_style( PSFWPB_NAME.'-main', plugins_url( '/assets/css/main.css', PSFWPB__FILE__),PSFWPB_VERSION, false );
}

add_action('wp_enqueue_scripts', 'psfwpb_setup');

// Load all widget files
if (is_dir(dirname(__FILE__)."/widgets/")){
    if ($dh = opendir(dirname(__FILE__)."/widgets/")){
      while (($file = readdir($dh)) !== false){
          if(strrpos($file, ".php") !== FALSE)
              include_once(dirname(__FILE__)."/widgets/".$file);
      }
      closedir($dh);
    }
}


if(!function_exists('psfwpb_get_current_url'))
{
    function psfwpb_get_current_url()
    {
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        
        return $current_url;
    }
		
}

if(!function_exists('psfwpb_count')) {
    function psfwpb_count($mixed='') {
        $count = 0;
        
        if(!empty($mixed) && (is_array($mixed))) {
            $count = count($mixed);
        } else if(!empty($mixed) && function_exists('is_countable') && version_compare(PHP_VERSION, '7.3', '<') && is_countable($mixed)) {
            $count = count($mixed);
        }
        else if(!empty($mixed) && is_object($mixed)) {
            $count = 1;
        }
        return $count;
    }
}

        
/*
* Add admin notify
* @param (string) $key unique key of notify, prefix included related plugin
* @param (string) $text test of message
* @param (function) $callback_filter custom function should be return true if not need show
* @param (string) $class notify alert class, by default 'notice notice-error'
* @return boolen true 
*/
function psfwpb_notify_admin ($key = '', $text = 'Custom Text of message', $callback_filter = '', $class = 'notice notice-error') {
    $key = 'psfwpb_notify_'.$key;
    $key_diss = $key.'_dissmiss';

    $psfwpb_notinstalled_admin_notice__error = function () use ($key_diss, $text, $class, $callback_filter) {
        global $wpdb;
        $user_id = get_current_user_id();
        if (!get_user_meta($user_id, $key_diss)) {
            if(!empty($callback_filter)) if($callback_filter()) return false;

            printf('<div class="%1$s" style="position:relative;"><p>%2$s</p><a href="?'.esc_attr($key_diss).'"><button type="button" class="notice-dismiss"></button></a></div>', esc_html($class), esc_html($text));  // WPCS: XSS ok, sanitization ok.
        }
    };

    add_action('admin_notices', function () use ($psfwpb_notinstalled_admin_notice__error) {
        $psfwpb_notinstalled_admin_notice__error();
    });

    $psfwpb_notinstalled_admin_notice__error_dismissed = function () use ($key_diss) {
        $user_id = get_current_user_id();
        if (isset($_GET[$key_diss]))
            add_user_meta($user_id, $key_diss, 'true', true);
    };
    add_action('admin_init', function () use ($psfwpb_notinstalled_admin_notice__error_dismissed) {
        $psfwpb_notinstalled_admin_notice__error_dismissed();
    });

    return true;
}
?>