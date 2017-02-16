<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package Hola Video Player
 * @since 1.0.0
 */

class Hvp_Scripts {

    //class constructor
    function __construct()
    {
        
    }
    
    /**
     * Enqueue Scripts on Admin Side
     *
     * @package Hola Video Player
     * @since 1.0.0
     */
    public function hvp_admin_scripts() {

        if(current_user_can('manage_options') || current_user_can('edit_posts')) {
            // admin script
            wp_register_script('hvp_admin_script', HVP_INC_URL . '/js/hvp-admin.js', array('jquery'), HVP_VERSION);
            wp_enqueue_script('hvp_admin_script');
            
            // Register admin style for shortcode button and poup
            wp_register_style('hvp_admin_style',  HVP_INC_URL . '/css/hvp-admin-style.css',HVP_VERSION);
            wp_enqueue_style('hvp_admin_style');
            
            if (is_admin ())
                wp_enqueue_media();
        }
    }

    /**
     * Enqueue Scripts on front Side
     *
     * @package Hola Video Player
     * @since 1.0.0
     */
    public function hvp_public_scripts(){
        global $post;        

        // Simple video js
        wp_register_script('hvp_video_script', HVP_INC_URL . '/js/hola_player.js', array(), HVP_VERSION);

        // Youtube videojs support
        wp_register_script('hvp_youtube_video_script', HVP_INC_URL . '/js/Youtube.js', array(), HVP_VERSION);

        // Vimeo videojs support
        wp_register_script('hvp_vimeo_video_script', HVP_INC_URL . '/js/Vimeo.js', array(), HVP_VERSION);

        // IMA ADS support
        wp_register_script('hvp_ima_ads_script', HVP_INC_URL . '/js/ads/ima/videojs.ima.js', array(), HVP_VERSION);

        // IMA ADS SDK loader
        wp_register_script('hvp_ima_ads_sdk_script', '//imasdk.googleapis.com/js/sdkloader/ima3.js', array(), HVP_VERSION);

        // Include ADS support
        wp_register_script('hvp_public_ads_script', HVP_INC_URL . '/js/hvp-ads.js', array(), HVP_VERSION);
        $ads_flashPath = HVP_INC_URL.'/flash/VPAIDFlash.swf';
        wp_localize_script('hvp_public_ads_script', 'HVP', 
            array('flashPath' => $ads_flashPath));

        // Disable right-click
        $no_rtclick = "jQuery(document).on('contextmenu', '.vjs-tech', function(e) { e.preventDefault(); });";
        wp_add_inline_script('hvp_video_script', $no_rtclick);
   }

    /**
     * Enqueue styles on front Side
     *
     * @package Hola Video Player
     * @since 1.0.0
     */
    public function hvp_public_styles() {
        
        // Register front style for default skin
        wp_register_style('hvp_video_style',  HVP_INC_URL . '/css/ver-5.8.8/video-js.css',HVP_VERSION);
        
        // Register front style for hola skin
        wp_register_style('hvp_hola_style',  HVP_INC_URL . '/css/hola-skin.css',HVP_VERSION);

        // Register front style for public style
        wp_register_style('hvp_hola_public_style',  HVP_INC_URL . '/css/hvp-public-style.css',HVP_VERSION);

        // Register front style for IMA ADS
        wp_register_style('hvp_ima_style',  HVP_INC_URL . '/css/ads/videojs.ima.css',HVP_VERSION);

        // Register front style for VAST-VPAID ADS
        wp_register_style('hvp-vast-vpaid-style',  HVP_INC_URL . '/css/ads/videojs.vast.vpaid.min.css',HVP_VERSION);

        wp_enqueue_style('hvp_video_style');
        wp_enqueue_style('hvp_hola_public_style');
        wp_enqueue_style('hvp_ima_style');
        wp_enqueue_style('hvp-vast-vpaid-style');
    }
    
    /**
     * Adding Hooks
     *
     * Adding hooks for the styles and scripts.
     *
     * @package Hola Video Player
     * @since 1.0.0
     */
    function add_hooks(){
        //add admin scripts
        add_action('admin_enqueue_scripts', array($this, 'hvp_admin_scripts'));

        //add public scripts
        add_action('wp_enqueue_scripts', array($this, 'hvp_public_scripts'));

        //add public styles
        add_action('wp_enqueue_scripts', array($this, 'hvp_public_styles'));
    }
}
?>
