<?php

/**
 * @package ALM
 * @author WPSAAD
 * @link https://wpsaad.com
 * @since 1.0.0
 */
/**
 * Plugin Name: Alt Manager
 * plugin URI: https://wpsaad.com/alt-manager-wordpress-image-alt-text-plugin/
 * Description: Automatically and dynamically bulk change WordPress images alt text and also generate empty values to a dynamic text generated by the plugin dynamic settings options.
 * Version: 1.6.4
 * Author: WPSAAD
 * Author URI: https://wpsaad.com
 * License: GPLv2 or later
 * Text Domain: alt-manager
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) or die;
if ( function_exists( 'am_fs' ) ) {
    am_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'am_fs' ) ) {
        // Create a helper function for easy SDK access.
        function am_fs() {
            global $am_fs;
            if ( !isset( $am_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $am_fs = fs_dynamic_init( array(
                    'id'             => '5548',
                    'slug'           => 'alt-manager',
                    'type'           => 'plugin',
                    'navigation'     => 'tabs',
                    'public_key'     => 'pk_07c4f76da780308f88546ce3da78a',
                    'is_premium'     => false,
                    'premium_suffix' => 'premium plan',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                        'slug'   => 'alt-manager',
                        'parent' => array(
                            'slug' => 'options-general.php',
                        ),
                    ),
                    'is_live'        => true,
                ) );
            }
            return $am_fs;
        }

        // Init Freemius.
        am_fs();
        // Signal that SDK was initiated.
        do_action( 'am_fs_loaded' );
    }
    //add style
    add_action( 'admin_enqueue_scripts', 'alm_style' );
    function alm_style() {
        wp_enqueue_script( 'switcher-script', plugins_url( '/assets/js/jquery.switcher.min.js', __FILE__ ) );
        wp_enqueue_style( 'switcher-style', plugins_url( '/assets/css/switcher.css', __FILE__ ) );
        wp_enqueue_script( 'select2-script', plugins_url( '/assets/js/select2.min.js', __FILE__ ) );
        wp_enqueue_style( 'select2-style', plugins_url( '/assets/css/select2.min.css', __FILE__ ) );
        wp_enqueue_script( 'alm-admin-script', plugins_url( '/assets/js/alm-admin-script.js', __FILE__ ) );
        wp_enqueue_style( 'alm-admin-style', plugins_url( '/assets/css/alm-admin-styles.css', __FILE__ ) );
        if ( is_rtl() ) {
            wp_enqueue_style( 'alm-admin-style-rtl', plugins_url( '/assets/css/alm-admin-styles-rtl.css', __FILE__ ) );
        }
        // wp_enqueue_script('jquery-ui-sortable');
    }

    //load plugin required files
    add_action( 'init', 'alm_load' );
    function alm_load() {
        require_once plugin_dir_path( __FILE__ ) . 'inc/alm-functions.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/alm-generator.php';
        if ( user_can( get_current_user_id(), 'manage_options' ) ) {
            include_once plugin_dir_path( __FILE__ ) . 'inc/alm-admin.php';
        }
    }

    //Generate activaition class
    if ( !class_exists( 'almActivate' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'inc/alm-activate.php';
    }
    //Activation Hook
    register_activation_hook( __FILE__, array('almActivate', 'activate') );
    //Activation & Reset
    add_action( 'admin_init', 'admin_page_functions' );
    function admin_page_functions() {
        //Reset Action
        if ( user_can( get_current_user_id(), 'manage_options' ) && isset( $_REQUEST['reset'] ) && wp_verify_nonce( $_POST['reset_nonce'], 'alm_reset_nonce' ) ) {
            $activate_reset = new almActivate();
            $activate_reset->reset();
        }
    }

}