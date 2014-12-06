<?php
/**
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */



/**
 * Sweetdate Child Theme Functions
 * Add extra code or replace existing functions
*/ 

add_action('after_setup_theme','kleo_my_hearts_actions');

function kleo_my_hearts_actions() 
{
   /* disable matching on member profile */
    remove_action('kleo_bp_before_profile_name', 'kleo_bp_compatibility_match');      

    /* Replace the heart over images */
    add_filter('kleo_img_rounded_icon', 'my_custom_icon');

    /* Replace the heart from register modal */
    add_filter('kleo_register_button_icon', 'my_custom_icon_register');

    /* Replace the heart from About us widget */
    add_filter('kleo_widget_aboutus_icon', 'my_custom_icon_about_widget');
}


?>