<?php 
//* WordPress Snippets




// Disable Dashboard Widgets
// From Beth Soderberg - http://www.slideshare.net/bethsoderberg/empowering-users-modifying-the-admin-experience
function disable_dashboard_widgets() {
    remove_meta_box( 
        'dashboard_quick_press', 
        'dashboard', 
        'side' 
    );
}

add_action( 'wp_dashboard_setup', 'disable_dashboard_widgets' );


// Disable Deactivation of Plugins
// From Beth Soderberg - http://www.slideshare.net/bethsoderberg/empowering-users-modifying-the-admin-experience

add_filter( 'plugin_action_links', 'disable_plugin_actions' );
function disable_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {

    // removes edit link for all plugins
    if ( array_key_exists('edit', actions) )
        unset ( $actions['edit']  );

    // removes deactivate link for selected plugins
    $plugins = array( 'advanced-custom-fields/acf.php' );

    if ( array_key_exists('deactivate', $actions) && in_array( $plugin_file, $plugins ) )
            unset( $actions['deactivate'] );

    return $actions;
}


// Remove WordPress Version from Dashboard
function my_footer_shh() {
    remove_filter( 'update_footer', 'core_update_footer' ); 
}

add_action( 'admin_menu', 'my_footer_shh' );

// Hide WordPress version from everyone but admins
function my_footer_shh() {
    if ( ! current_user_can('manage_options') ) { // 'update_core' may be more appropriate
        remove_filter( 'update_footer', 'core_update_footer' ); 
    }
}

// Alternate options
add_action( 'admin_menu', 'my_footer_shh' );

function wpbeginner_remove_version() {
return '';
}
add_filter('the_generator', 'wpbeginner_remove_version');




// Hacking WordPress: 
https://codex.wordpress.org/Login_Trouble
https://codex.wordpress.org/Resetting_Your_Password


// Disable WordFence
// Place in wp-config
define('WFWAF_ENABLED', false);



// Stop User Enumeration Scans in WordPress
https://perishablepress.com/stop-user-enumeration-wordpress/

// NOTE: IT IS IMPORTANT TO ALSO CHANGE THE USERNAME DISPLAY SO THAT IT DOES NOT SHOW THE LOGIN USERNAME


// block WP enum scans - PHP
// http://m0n.co/enum
if (!is_admin()) {
    // default URL format
    if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
    add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request) {
    // permalink URL format
    if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
    else return $redirect;
}


# Block User ID Phishing Requests - .htaccess
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} ^author=([0-9]*)
    RewriteRule .* http://example.com/? [L,R=302]
</IfModule>



// Quickly Enable/Disable Plugin

https://perishablepress.com/quickly-disable-or-enable-all-wordpress-plugins-via-the-database/
option_value field to: a:0:{}


// Force SSL Login
define('FORCE_SSL_ADMIN', true);

// SQL Queries for Site Migration:

UPDATE wp_options SET option_value = REPLACE(option_value, 'ORIGINAL_URL', 'NEW_URL');
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'ORIGINAL_URL', 'NEW_URL');
UPDATE wp_posts SET guid = REPLACE(guid, 'ORIGINAL_URL', 'NEW_URL');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'ORIGINAL_URL', 'NEW_URL');


UPDATE wp_options SET option_value = REPLACE(option_value, 'https://lunacodesdesign.com/lunacodes', 'https://lunacodesdesign.com/uscorr' );
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'https://lunacodesdesign.com/lunacodes', 'https://lunacodesdesign.com/uscorr' );
UPDATE wp_posts SET guid = REPLACE(guid, 'https://lunacodesdesign.com/lunacodes', 'https://lunacodesdesign.com/uscorr' );
UPDATE wp_posts SET post_content = REPLACE(post_content, 'https://lunacodesdesign.com/lunacodes', 'https://lunacodesdesign.com/uscorr' );

/**
 * Title: Shell Script to Fix WP File Permissions
 * File: fix-wordpress-permissions.sh
 * From: https://gist.github.com/Adirael/3383404 
 */

#!/bin/bash
#
# This script configures WordPress file permissions based on recommendations
# from http://codex.wordpress.org/Hardening_WordPress#File_permissions
#
# Author: Michael Conigliaro <mike [at] conigliaro [dot] org>
#
WP_OWNER=www-data # <-- wordpress owner
WP_GROUP=www-data # <-- wordpress group
WP_ROOT=$1 # <-- wordpress root directory
WS_GROUP=www-data # <-- webserver group

# reset to safe defaults
find ${WP_ROOT} -exec chown ${WP_OWNER}:${WP_GROUP} {} \;
find ${WP_ROOT} -type d -exec chmod 755 {} \;
find ${WP_ROOT} -type f -exec chmod 644 {} \;

# allow wordpress to manage wp-config.php (but prevent world access)
chgrp ${WS_GROUP} ${WP_ROOT}/wp-config.php
chmod 660 ${WP_ROOT}/wp-config.php

# allow wordpress to manage wp-content
find ${WP_ROOT}/wp-content -exec chgrp ${WS_GROUP} {} \;
find ${WP_ROOT}/wp-content -type d -exec chmod 775 {} \;
find ${WP_ROOT}/wp-content -type f -exec chmod 664 {} \;
 


/**
 * Title: Use a Function to Load Scripts and CSS
 * From: https://www.toptal.com/wordpress/tips-and-practices/#remote-developer-job
 * Author: Alex Gurevice
 * 
 */

WordPress already keeps track of all the scripts and CSS that it has loaded, so instead of adding your JS and CSS into a header or footer, let WordPress handle it with its enqueue functionality. This way WordPress will keep dependencies in check and you will avoid potential conflicts. You add enqueue methods to your theme’s function.php file: wp_enqueue_script() or wp_enqueue_style(), respectively. Here is an example with some explanatory comments:

function add_theme_scripts() {
//example script from CDN, true means our script will be in the footer.
wp_enqueue_script( 'particles', '//cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js', array(), null, true );
});

//All referred to when to load your style like: 'screen', 'print' or 'handheld.
wp_enqueue_style( 'slider', get_template_directory_uri() . '/css/slider.css',false, null,'all'); 

//this will actually execute our function above
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' ); 
WordPress.org’s Theme Handbook further explains the many parameters to an enqueue method, but here’s the method signature for both enqueue methods:

wp_enqueue_style($handle, $src, $deps, $ver, $media );
wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
As you can see, the only difference between these two methods is the final parameter. For wp_enqueue_style(), the last parameter sets the media for which this stylesheet has been defined. Such as screen (computer), print (print preview mode), handheld, and so on. The last parameter for wp_enqueue_script() specifies whether or not the script should be loaded in the footer.

Here’s a breakdown for the other parameters in our example:

$handle is the name of the stylesheet, which can be anything you’d like.
$src is where the stylesheet is located (CDN, local, etc). This is the only required parameter.
$deps stands for dependencies. When passed a stylesheet handle, it will load it before executing the source script. When $deps is passed in wp_enqueue_script(), it’s an array.
$ver sets the version number.
$media is the wp_enqueue_style() only parameter. It specifies which type of display media the stylesheet is designed for, such as ‘all’, ‘screen’, ‘print’ or ‘handheld.’
$in_footer is the wp_enqueue_script()’s only parameter, a boolean that allows you to place your scripts in the footer of your HTML rather than in the header, which means it will not delay the loading of the DOM tree.


/**
 * Title: Spring Clean Your WordPress Functions
 * From: https://www.toptal.com/wordpress/tips-and-practices/#remote-developer-job
 * Author: Alex Gurevic
 * 
 */

Although great, WordPress comes out of the box with a lot of things that cannot be turned off in the settings. Here are a handful of steps to take any fresh WordPress install and make it more secure and perform better:

Remove the WordPress Version

Get rid of the WordPress version number to make your site harder to be identified by hackers. To do this, add the following to your functions.php file:

add_filter( 'the_generator', '__return_null' );
Remove Script Versions

Get rid of the version number after scripts. By default, WordPress adds versions to all your scripts. This can lead to issues with caching/minification plugins, as well as helps hackers identify your site better. To prevent this functionality, add the following code to your theme functions file:

function remove_cssjs_ver( $src ) {
        if( strpos( $src, '?ver=' ) )
            $src = remove_query_arg( 'ver', $src );
        return $src;
    }
add_filter( 'style_loader_src', 'remove_cssjs_ver', 1000 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 1000 );
Restrict WooCommerce

Did you install WooCommerce, and now the site is running slowly? Adding this function to your functions.php will prevent WooCommerce from loading its scripts on non-WooCommerce pages:

/**
 * Title: Tweak WooCommerce styles and scripts.
 * From: https://www.toptal.com/wordpress/tips-and-practices/#remote-developer-job
 * Author: Alex Gurevich
 * Author Comment: Original credit goes to Greg from: https://gist.github.com/gregrickaby/2846416
 */

function grd_woocommerce_script_cleaner() {
    
    // Remove the generator tag, to reduce WooCommerce based hacking attacks
    remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
    // Unless we're in the store, remove all the scripts and junk!
    if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
        wp_dequeue_style( 'woocommerce_frontend_styles' );
        wp_dequeue_style( 'woocommerce-general');
        wp_dequeue_style( 'woocommerce-layout' );
        wp_dequeue_style( 'woocommerce-smallscreen' );
        wp_dequeue_style( 'woocommerce_fancybox_styles' );
        wp_dequeue_style( 'woocommerce_chosen_styles' );
        wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
        wp_dequeue_style( 'select2' );
        wp_dequeue_script( 'wc-add-payment-method' );
        wp_dequeue_script( 'wc-lost-password' );
        wp_dequeue_script( 'wc_price_slider' );
        wp_dequeue_script( 'wc-single-product' );
        wp_dequeue_script( 'wc-add-to-cart' );
        wp_dequeue_script( 'wc-cart-fragments' );
        wp_dequeue_script( 'wc-credit-card-form' );
        wp_dequeue_script( 'wc-checkout' );
        wp_dequeue_script( 'wc-add-to-cart-variation' );
        wp_dequeue_script( 'wc-single-product' );
        wp_dequeue_script( 'wc-cart' ); 
        wp_dequeue_script( 'wc-chosen' );
        wp_dequeue_script( 'woocommerce' );
        wp_dequeue_script( 'prettyPhoto' );
        wp_dequeue_script( 'prettyPhoto-init' );
        wp_dequeue_script( 'jquery-blockui' );
        wp_dequeue_script( 'jquery-placeholder' );
        wp_dequeue_script( 'jquery-payment' );
        wp_dequeue_script( 'jqueryui' );
        wp_dequeue_script( 'fancybox' );
        wp_dequeue_script( 'wcqi-js' );

    }
}

add_action( 'wp_enqueue_scripts', 'grd_woocommerce_script_cleaner', 99 );

/**
 * Title: Enable Shortcodes In a Widget Area
 * From: https://www.toptal.com/wordpress/tips-and-practices/#remote-developer-job
 * Author: Alex Gurevic
 * 
 */

Trying to use shortcodes in a widget area and getting nothing? Drop this in into functions.php to use shortcodes in widget areas:

add_filter('widget_text', 'do_shortcode');


Use nickname for login:
// Just to let people know, Alkorr's solution worked for me, and thank you for posting it!

// In the main plugin file quick-chat.php change $current_user->user_login to $current_user->display_name in three places in the file.

// You may need to log out, clear cookies, clear cache, try a different browser, to see the change.

// Good discussion on this, and other aspects of quick-chat also:
// https://wordpress.org/support/topic/plugin-quick-chat-nicknames-instead-of-usernames?replies=23
// Prevent basic hacking by finding out Admin username:
// // domain.com/?author=1 - Redirects to domain.com/author/author-name

// // If you want to hide it with .htaccess:
// RewriteRule   ^author/(.*)$  http://example.com/  [R,L]
// // Dumps everything back to the top of the blog.



Disable wpautop:
remove_filter( 'the_content', 'wpautop' ); the_content();
for excerpt

remove_filter( 'the_excerpt', 'wpautop' ); the_content();



Add Custom Image Sizes:
    if ( function_exists( 'add_image_size' ) ) {
        add_image_size( 'new-size', 300, 100, true ); //(cropped)
    }
    add_filter('image_size_names_choose', 'my_image_sizes');
    function my_image_sizes($sizes) {
            $addsizes = array(
                    "new-size" => __( "New Size")
                    );
            $newsizes = array_merge($sizes, $addsizes);
            return $newsizes;
    }

    // remove version info from head and feeds
function complete_version_removal() {
    return '';
}
add_filter('the_generator', 'complete_version_removal');



Sharpen Uploaded Images When Resized:
function ajx_sharpen_resized_files( $resized_file ) {
 
    $image = wp_load_image( $resized_file );
    if ( !is_resource( $image ) )
        return new WP_Error( 'error_loading_image', $image, $file );
 
    $size = @getimagesize( $resized_file );
    if ( !$size )
        return new WP_Error('invalid_image', __('Could not read image size'), $file);
    list($orig_w, $orig_h, $orig_type) = $size;
 
    switch ( $orig_type ) {
        case IMAGETYPE_JPEG:
            $matrix = array(
                array(-1, -1, -1),
                array(-1, 16, -1),
                array(-1, -1, -1),
            );
 
            $divisor = array_sum(array_map('array_sum', $matrix));
            $offset = 0;
            imageconvolution($image, $matrix, $divisor, $offset);
            imagejpeg($image, $resized_file,apply_filters( 'jpeg_quality', 90, 'edit_image' ));
            break;
        case IMAGETYPE_PNG:
            return $resized_file;
        case IMAGETYPE_GIF:
            return $resized_file;
    }
 
    return $resized_file;
}  
 
add_filter('image_make_intermediate_size', 'ajx_sharpen_resized_files',900);



Include Post/Page IDs in Admin Table:
add_filter('manage_posts_columns', 'posts_columns_id', 5);
add_action('manage_posts_custom_column', 'posts_custom_id_columns', 5, 2);
add_filter('manage_pages_columns', 'posts_columns_id', 5);
add_action('manage_pages_custom_column', 'posts_custom_id_columns', 5, 2);
 
function posts_columns_id($defaults){
    $defaults['wps_post_id'] = __('ID');
    return $defaults;
}
 
function posts_custom_id_columns($column_name, $id){
    if($column_name === 'wps_post_id'){
        echo $id;
    }
}




Force SSL/HTTPS on Specific Post IDs
function wps_force_ssl( $force_ssl, $post_id = 0, $url = '' ) {
    if ( $post_id == 25 ) {
        return true
    }
    return $force_ssl;
}
add_filter('force_ssl' , 'wps_force_ssl', 10, 3);



Force All Scripts into wp_footer():
if(!is_admin()){
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);
 
    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}


