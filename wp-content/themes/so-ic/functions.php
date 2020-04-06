<?php

require_once(get_stylesheet_directory() . '/includes/ic-functions.php');

function squabble_scripts() {
    //s$rand = rand( 1, 99999999999 );
    $rand = '1.0';
    wp_enqueue_style( 'ic-stylez', get_stylesheet_directory_uri() . '/style.css','', $rand );
    //wp_enqueue_script( 'imagesloaded-js', get_stylesheet_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), null, true ); 
    if(!is_singular('games')) {
        wp_enqueue_script( 'ic-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'), $rand,true ); 
        wp_localize_script('ic-scripts', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    if(is_singular('games')) {
        wp_enqueue_script( 'ic-game', get_stylesheet_directory_uri() . '/js/game.js', array('jquery'), $rand,true ); 
        wp_localize_script('ic-game', 'settings', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }
    
    /*wp_enqueue_script( 'lozad', get_stylesheet_directory_uri() . '/js/lozad.min.js', array('jquery'), null, true ); 
    wp_enqueue_script( 'slick', get_stylesheet_directory_uri() . '/includes/slick/slick.js', array('jquery'), null, true ); 
    
    wp_enqueue_script( 'masonry-js', get_stylesheet_directory_uri() . '/js/masonry.pkgd.min.js', array('jquery'), null, true ); 
   
    
    wp_enqueue_style( 'twentynineteen-style', get_stylesheet_directory_uri() . '/style.css', array(), filemtime( get_stylesheet_directory() . '/style.css' )  );

    
    wp_enqueue_script( 'countdown', get_stylesheet_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), null, false,true ); 
    /*wp_enqueue_style( 'stripe_styles' );
    wp_enqueue_script( 'woocommerce_stripe' );
    wp_enqueue_script( 'wc-checkout' );*/

}
add_action( 'wp_enqueue_scripts', 'squabble_scripts' );

show_admin_bar(false);

add_image_size( 'ic-thumb', 828, 1729 );
remove_image_size('1536x1536');
remove_image_size('2048x2048');

update_option('medium_large_size_w', 0);
update_option('medium_large_size_h', 0);

add_filter( 'big_image_size_threshold', '__return_false' );

function shapeSpace_disable_thumbnail_images($sizes) {

    unset( $sizes['thumbnail']);
    unset( $sizes['medium']);
    unset( $sizes['large']);
    unset( $sizes['medium_large']);
    unset( $sizes['1536x1536']);
    unset( $sizes['2048x2048']);

	return $sizes;

}
add_action('intermediate_image_sizes_advanced', 'shapeSpace_disable_thumbnail_images',1);