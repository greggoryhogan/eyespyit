<?php 
/*
 *
 * Functions for IC Game
 * 
 */

//Require Twilio for Phone/Code Verification
require_once(get_stylesheet_directory() . '/includes/twilio-php-master/src/Twilio/autoload.php');
use Twilio\Rest\Client;

/*
 *
 * Make sure verifications table exists
 * 
 */
add_action('admin_init', 'icup_create_db');
function icup_create_db() {
	global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE `{$wpdb->base_prefix}verifications` (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		phone varchar(100),
        code varchar(100),
		PRIMARY KEY  (id)
		) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

/*
 *
 * Create Games CPT
 * 
 */
function create_game_posttype() {
    register_post_type( 'games',
        array(
            'labels' => array(
                'name' => __( 'Games' ),
                'singular_name' => __( 'Game' )
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'games'),
            'show_in_rest' => true,
            'supports' => array('title','custom-fields')
        )
    );
}
add_action( 'init', 'create_game_posttype' );

/*
 *
 * Extends Remember Me session to 1 year (in seconds)
 * 
 */
add_filter( 'auth_cookie_expiration', 'keep_me_logged_in_for_1_year' );
function keep_me_logged_in_for_1_year( $expirein ) {
    return 31556926; 
}

/*
 *
 * Set Up Cron to Delete Old Media
 * 
 */
add_action( 'init', 'delete_old_eyespy_attachments' );
function delete_old_eyespy_attachments() {
    if (! wp_next_scheduled ( 'delete_eyespy_attachments' )) {
        wp_schedule_event(time(), 'daily', 'delete_eyespy_attachments');
    }
}
/*
*
* Cron Function that deletes the media
* 
*/
add_action('delete_eyespy_attachments', 'delete_eyespy_attachments_function');
function delete_eyespy_attachments_function() {
    $saved_ids = array();
    $args = array( 'post_type' => 'games', 'post_status' => 'publish');
    $posts = get_posts($args);
    foreach ( $posts as $post ) {

        $image = get_post_meta($post_id,'ic_image',true);
        $last_image = get_post_meta($post_id,'last_ic_image',true);
    }
    $args = array(
        'post_type' => 'games',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $my_query = null;
    $my_query = new WP_Query($args);
    
    if( $my_query->have_posts() ):
        while ($my_query->have_posts()) : $my_query->the_post();
            $post_id = get_the_ID();
            $image = get_post_meta($post_id,'ic_image',true);
            $last_image = get_post_meta($post_id,'last_ic_image',true);
            if(!in_array($image,$saved_ids)) {
                $saved_ids[] = $image;
            }
            if(!in_array($last_image,$saved_ids)) {
                $saved_ids[] = $last_image;
            }
        endwhile; 
    endif;
    wp_reset_query();

    $args = array(
        'post_type' => 'attachment',
        'numberposts' => null,
        'post_status' => null
    );
    $attachments = get_posts($args);
    if($attachments){
        foreach($attachments as $attachment){
            $attachment_id = $attachment->ID; 
            if(!in_array($attachment_id,$saved_ids)) {
                wp_delete_attachment($attachment_id,true);
            }
        }
    }
}

/*
 *
 * Prevent Frontend View of Users
 * 
 */
function redirect_to_home_if_author_parameter() {
	$is_author_set = get_query_var( 'author', '' );
	if ( $is_author_set != '' && !is_admin()) {
		wp_redirect( home_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'redirect_to_home_if_author_parameter' );

/*
 *
 * Prevent REST View of Users
 * 
 */
function disable_rest_endpoints ( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
}
add_filter( 'rest_endpoints', 'disable_rest_endpoints');

/*
 *
 * Remove Block Editor
 * 
 */
add_action( 'wp_enqueue_scripts', 'wp_juice_cleanse', 200 );
function wp_juice_cleanse() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wc-block-style');
    if(is_singular('games')) {
        wp_dequeue_style('wise_chat_core');
    }
}

/*
 *
 * Remove WP Version
 * 
 */
function remove_wordpress_version() {
    return '';
}
add_filter('the_generator', 'remove_wordpress_version');

/*
 *
 * Disable Emojis
 * 
 */
function remove_unneccessary_wp() {
    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );
    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
    // Remove all embeds rewrite rules.
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    // Remove filter of the oEmbed result before any HTTP requests are made.
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
    //rsd
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    //emojis
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'remove_unneccessary_wp' );
   
/*
 *
 * Disable OEmbed
 * 
 */
function disable_embeds_tiny_mce_plugin($plugins) {
    return array_diff($plugins, array('wpembed'));
}

/*
 *
 * Disable OEmbed Rewrite
 * 
 */
function disable_embeds_rewrites($rules) {
    foreach($rules as $rule => $rewrite) {
        if(false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }
    return $rules;
}

/*
 *
 * Disable Scripts in Footer
 * 
 */
function deregister_footer_scripts(){
    wp_dequeue_script( 'wp-embed' );
    if(is_singular('games')) {
        wp_dequeue_script('wise_chat_messages_history');
		wp_dequeue_script('wise_chat_messages');
		wp_dequeue_script('wise_chat_settings');
		wp_dequeue_script('wise_chat_maintenance_executor');
		wp_dequeue_script('wise_chat_core');
        wp_dequeue_script('wise_chat_3rdparty_momentjs');
        
        wp_deregister_script('wise_chat_core');
        wp_deregister_script('wise_chat_messages_history');
		wp_deregister_script('wise_chat_messages');
		wp_deregister_script('wise_chat_settings');
		wp_deregister_script('wise_chat_maintenance_executor');
		wp_deregister_script('wise_chat_core');
        wp_deregister_script('wise_chat_3rdparty_momentjs');
        
    }
}
add_action( 'wp_footer', 'deregister_footer_scripts' );

/*
 *
 * Disable Emojis in TINYMCE
 * 
 */
function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}
   
/*
 *
 * Disable prefetch emojies in header
 * 
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}


/*
 *
 * Simple function to echo iframe for game, $post_id is game id not homepage id
 * 
 */
function embed_game($post_id) {
    echo '<div class="gameembed"><iframe src="'.get_permalink($post_id).'"></iframe></div>';
}

/*
 *
 * Overall output for the game itself
 * 
 */
function eye_spy_it() {
    global $post;
    $post_id = $post->ID;
    $current_user_id = get_current_user_id();
    $commander = get_post_meta($post_id,'ic_commander',true);
    //set default commander if one isn't active
    if($commander == '') {
        $calculating = get_post_meta($post_id,'calculating',true);
        if(!$calculating) {
            add_post_meta($post_id,'calculating',true);
            update_post_meta($post_id,'ic_commander',get_current_user_id());
            delete_post_meta($post_id,'calculating');
        }
        $commander = get_post_meta($post_id,'ic_commander',true);
    }

    $ready = get_post_meta($post_id,'ic_ready',true);
    if($ready) {
        $image = get_post_meta($post_id,'ic_image',true);
        $url = wp_get_attachment_image_src($image,'ic-thumb');
        $text = get_post_meta($post_id,'ic_text',true);
        if($text) {
            echo '<div class="toggletip">?</div>';
            echo '<div class="ictext"><strong>Hint:</strong> '.$text.'</div>';
        }
        echo '<div class="content-wrapper flex-center">';
            echo '<div class="content full">';
                echo '<div id="inspector-image" data-c="'.get_post_meta($post_id,'ic_commander',true).'"><img src="'.$url[0].'" /></div>';
                position_marker($post_id,'playing');
            echo '</div>';
        echo '</div>';
        if($commander == $current_user_id) {   
            echo '<div class="step step5 on">Players are Guessing!</div>';
        }
    } else { 
        if($commander == $current_user_id) {
            echo '<div class="content-wrapper flex-center">';
                echo '<div class="content">';
                    echo upload_image_form($post_id);
                echo '</div>';
            echo '</div>';
            echo '<div id="commander-content">';
                echo '<div id="commander-image"></div>';
                echo '<div id="commander-overlay"><div class="tempit"></div></div>';  
                echo '<div class="step step1">Tap to set your area</div>';
                echo '<div class="step step2">Tap the checkmark to save your area</div>';
                echo '<div class="step step3 inputarea"><span>What do you spy?</span><input type="text" id="inspectorhint" placeholder="Something Black" value=""  autocomplete="off" data-user="'.$current_user_id.'" data-id="'.get_the_ID().'" /><div class="checkarea"><div class="check"></div></div></div>';
                echo '<div class="step step3 text">Tap the checkmark to continue</div>';
                echo '<div class="step step4">Starting Game...</div>';
                echo '<div class="step step5">Players are Guessing!</div>';
                echo '<div class="step steptimer on"></div>';
            echo '</div>';
        } else {
            $image = get_post_meta($post_id,'last_ic_image',true);
            $url = wp_get_attachment_image_src($image,'ic-thumb');
            $text = get_post_meta($post_id,'last_ic_text',true);
            if($text) {
                echo '<div class="toggletip">?</div>';
                echo '<div class="ictext hidden"><strong>Hint:</strong> '.$text.'</div>';
            }
            $commander_id = get_post_meta($post_id,'ic_commander',true);
            $commander = get_user_by('id',$commander_id);
            echo '<h3 class="waiting">Waiting for '. $commander->display_name.' to upload an image</h3>';
            echo '<div class="content-wrapper flex-center">';
                echo '<div class="content full">';
                    echo '<div id="inspector-image" data-c="'.get_post_meta($post_id,'ic_commander',true).'"><img src="'.$url[0].'" /></div>';
                    position_marker($post_id,'gameover');
                echo '</div>';
            echo '</div>';
           
        }
        $waiting = get_post_meta($post_id,'waiting_on_commander',true);
        if($waiting) {
            $max = $waiting - Game_Timer;
            if($current_user_id > 0 && wp_is_mobile()) {
                echo '<div id="max-timer" data-time="'.$waiting.'" data-max="'.Game_Timer.'"><span></span></div>';
            }
        }
    }
}

/*
 *
 * Checks if we are waiting on user to upload image. If we are and the timer has run out, select a new commander
 * 
 */
add_action('template_redirect','check_for_commanders');
function check_for_commanders() {
    global $post;
    $post_id = $post->ID;
    $waiting = get_post_meta($post_id,'waiting_on_commander',true);
    if($waiting) {
        $time = current_time('timestamp');
        if($waiting < $time) { //time has passed the waiting period
            $commander = get_post_meta($post_id,'ic_commander',true);
            $current_user_id = get_current_user_id();
            if($commander != $current_user_id && $current_user_id != 0 && wp_is_mobile()) {
                $calculating = get_post_meta($post_id,'calculating',true);
                if(!$calculating) {
                    add_post_meta($post_id,'calculating',true);
                    delete_post_meta($post_id,'ic_commander');
                    update_post_meta($post_id,'ic_commander',$current_user_id);
                    update_post_meta($post_id,'booted_commander',$commander);
                    $time = current_time('timestamp');
                    update_post_meta($post_id,'waiting_on_commander',$time + Game_Timer);
                }
            }
            $time = current_time('timestamp');
            $page_version_hash = wp_hash($time);
            $page_version = substr($page_version_hash, 0, 8);
            delete_post_meta( $post_id, 'force_refresh_current_page_version' );
            update_post_meta( $post_id, 'force_refresh_current_page_version', $page_version );
            delete_post_meta($post_id,'calculating');
        }
    }
    
}

/*
 *
 * Show modal login for users not logged in
 * 
 */
function modal_login() {
    if(get_current_user_id() > 0) {
        return;
    }
    echo '<div class="modal-login">';
        echo '<div class="login-form">';
            echo '<div class="wrap">';
                echo '<div class="step1">';
                    echo '<h4>Please verify your phone number to continue</h4>';
                    echo '<input id="phonenumber" type="tel" placeholder="(123) 456-7890" value=""  autocomplete="off" />';
                    echo '<div id="verifyphone">Verify</div>';
                echo '</div>';
                echo '<div class="step2">';
                    echo '<h4>Enter the 6 digit code sent to your phone</h4>';
                    echo '<input id="code" placeholder="123456" value=""  autocomplete="off" />';
                    echo '<div id="verifycode">Confirm</div><div id="verificationerror"></div>';
                echo '</div>';
                echo '<div class="step3">';
                    echo '<h4>What&rsquo;s your name?</h4>';
                    echo '<input id="name" type="text" placeholder="Your Name" value=""  autocomplete="off" />';
                    echo '<div id="addname">Start Playing</div><div id="nameerror"></div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}

/*
 *
 * Show alert if on desktop
 * 
 */
function modal_alert() {
    echo '<div class="modal-login alert">';
        echo '<div class="login-form">';
            echo '<div class="wrap">';
                echo '<div class="step1">';
                    echo '<h4>Sorry, Eye Spy It can only be played on mobile. Please visit us on your phone.</h4>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}

/*
 *
 * Set marker position for game
 * 
 */
function position_marker($post_id,$status) {
    $left = get_post_meta($post_id, 'ic_left',true);
    $top = get_post_meta($post_id, 'ic_top',true);
    $class = 'marker';
    $commander = get_post_meta($post_id,'ic_commander',true);
    $current_user_id = get_current_user_id();
    if($commander == $current_user_id || $status == 'gameover') {
        $class .= ' commander '.$status;
    } else {
        $class .= ' inspector';
    }
    echo '<div class="'.$class.'" data-commander="'.$commander.'" data-id="'.$post_id.'" data-user="'.$current_user_id.'" style="left:'.$left.'%; top: '.$top.'%;"></div>';
}

/*
 *
 * Show form for new uploads after game ends
 * 
 */
function upload_image_form($post_id) {
    $last_commander = get_post_meta($post_id,'last_ic_commander');
    $current = get_current_user_id();
    $form = '<form id="upload-ic" method="post" enctype="multipart/form-data"><div class="image-upload-wrap">
            <input type="hidden" id="post_id" value="'.get_the_ID().'" />
            <input class="file-upload-input" id="ic-image" name="ic-image" type="file" accept="image/*" />
            <div class="drag-text">
                <h3>';
                if($last != $current) {
                    $commander = get_user_by('id',$current);
                    $form .= 'Nice Job '.$commander->display_name.'!<br>';
                } else {
                    $last_commander = get_user_by('id',$last_commander);
                    $form .= $last_commander->display_name.'  took too long!<br>';
                }
                
            $form .= 'Now it&rsquo;s your turn!<br><span>Add an Image</span></h3>'.loading_icon().'
            </div>
        </div>
        <div class="file-upload-content">
            <img class="file-upload-image" src="#" alt="your image" />
            <div class="image-title-wrap">
            <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
            </div>
        </div></form>';
    return $form;
}

/*
 *
 * CSS Spinning Icon during upload
 * 
 */
function loading_icon() {
    return '<div class="loading-icon"><div class="loading"><div><div></div><div></div><div></div></div></div></div>';
}

/*
 *
 * AJAX to upload the image into WP Media Library
 * 
 */
add_action( 'wp_ajax_save_ic_image','save_ic_image' );
add_action( 'wp_ajax_nopriv_save_ic_image','save_ic_image' );
function save_ic_image(){
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    $uploadedfile = $_FILES['file'];
    $upload_overrides = array('test_form' => false);
    $uploaded_file = wp_handle_upload($uploadedfile, $upload_overrides);

    if ($uploaded_file && !isset($uploaded_file['error'])) {
        if( isset( $uploaded_file ["file"] )) {
            $file_name_and_location = $uploaded_file["file"];
            $file_title_for_media_library = 'User Upload';
        
            $attachment = array(
                'post_title' => $uploadedfile['name'],
                'post_content' => '',
                'post_type' => 'attachment',
                'post_mime_type' => $uploadedfile['type'],
                'guid' => $uploaded_file['url']
            );
        
            $id = wp_insert_attachment( $attachment,$uploaded_file[ 'file' ]);
            wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $uploaded_file['file'] ) );

            update_post_meta($post->ID, $field, $id);
            
            $url = wp_get_attachment_image_src($id,'ic-thumb');

            $post_id = $_POST['post_id'];
            update_post_meta($post_id,'ic_image',$id);

            //output url to ajax and print it to screen
            echo $url[0];
        } 
    } else {
        echo $uploaded_file['error'];
    }
    wp_die();
}

/*
 *
 * Start round
 * 
 */
add_action( 'wp_ajax_start_ic_round','start_ic_round' );
add_action( 'wp_ajax_nopriv_start_ic_round','start_ic_round' );
function start_ic_round(){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $text = $_POST['text'];
    $left = $_POST['left'];
    $top = $_POST['top'];
    delete_post_meta($post_id,'waiting_on_commander');
    update_post_meta($post_id, 'ic_text',$text);
    update_post_meta($post_id, 'ic_left',$left);
    update_post_meta($post_id, 'ic_top',$top);
    update_post_meta($post_id, 'ic_ready',true);
    update_post_meta($post_id,'ic_commander',$user_id);
    $time = current_time('timestamp');
    update_post_meta($post_id, 'ic_start_time', $time);
    $page_version_hash = wp_hash($time);
    $page_version = substr($page_version_hash, 0, 8);
    delete_post_meta( $post_id, 'force_refresh_current_page_version' );
    update_post_meta( $post_id, 'force_refresh_current_page_version', $page_version );
    wp_die();
}

/*
 *
 * End round
 * 
 */
add_action( 'wp_ajax_end_ic_round','end_ic_round' );
add_action( 'wp_ajax_nopriv_end_ic_round','end_ic_round' );
function end_ic_round(){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $calculating = get_post_meta($post_id,'calculating',true);
    if(!$calculating) {
        add_post_meta($post_id,'calculating',true);
        $last_commander = get_post_meta($post_id,'ic_commander',true);
        add_user_meta($last_commander,'won_round',true);
        update_post_meta($post_id,'last_ic_commander',$last_commander);
        update_post_meta($post_id,'ic_commander',$user_id);
        delete_post_meta($post_id,'ic_ready');
        //save images
        $last_image = get_post_meta($post_id,'ic_image',true);
        $last_text = get_post_meta($post_id, 'ic_text',true);
        update_post_meta($post_id,'last_ic_image',$last_image);
        update_post_meta($post_id, 'last_ic_text',$last_text);
        $time = current_time('timestamp');
        update_post_meta($post_id,'waiting_on_commander',$time + Game_Timer);
        update_post_meta($post_id, 'ic_start_time', $time);
        $page_version_hash = wp_hash($time);
        $page_version = substr($page_version_hash, 0, 8);
        delete_post_meta( $post_id, 'force_refresh_current_page_version' );
        delete_post_meta($post_id,'calculating');
        update_post_meta( $post_id, 'force_refresh_current_page_version', $page_version );
        
    }
    wp_die();
}

/*
 *
 * Ajax verify phone
 * 
 */
add_action( 'wp_ajax_verify_phone','verify_phone' );
add_action( 'wp_ajax_nopriv_verify_phone','verify_phone' );
function verify_phone(){
    global $wpdb;
    $phone = $_POST['phone'];
    $code = mt_rand(100000, 999999);
    $table = $wpdb->prefix.'verifications';
    $sql_query = $wpdb->prepare("SELECT * FROM $table WHERE phone=%s", $phone);
    $results = $wpdb->get_results( $sql_query ); 
    if($results) {
        $wpdb->update( 
            $table, 
            array( 
                'phone' => $phone,
                'code' => $code
            ), 
            array( 
                'phone' => $phone
            ) 
        );
    } else {
        $data = array('phone' => $phone, 'code' => $code);
        $format = array('%s','%d');
        $wpdb->insert($table,$data,$format);
    }
    
    $message = 'Your Eye Spy It Verification Code is '.$code;
    twilio_message($phone, $message);
    wp_die();

}

/*
 *
 * Ajax verify code
 * 
 */
add_action( 'wp_ajax_verify_code','verify_code' );
add_action( 'wp_ajax_nopriv_verify_code','verify_code' );
function verify_code(){
    global $wpdb;
    $phone = $_POST['phone'];
    $code = $_POST['code'];
    $table = $wpdb->prefix.'verifications';
    $sql_query = $wpdb->prepare("SELECT * FROM $table WHERE phone=%s AND code=%s", $phone, $code);
    $results = $wpdb->get_results( $sql_query ); 
    $pass = 'error';
    if(!empty($results)) {
        $pass = 'Success!';
        $phonefriendly = str_replace('-','',$phone);
        $phonefriendly = str_replace('(','',$phonefriendly);
        $phonefriendly = str_replace(')','',$phonefriendly);
        $phonefriendly = str_replace(' ','',$phonefriendly);
        $username = 'ic'.$phonefriendly;
        $user = get_user_by('login',$username);
        if(!empty( $user )) {
            $user_id = $user->ID;
            //check if we banned them before
            $banned = get_user_meta($user_id,'banned',true);
            if($banned) {
                $pass = 'banned';
            } else {
                wp_clear_auth_cookie();
                wp_set_current_user ( $user_id );
                wp_set_auth_cookie  ( $user_id, true );
                echo $user_id;
            }
        } else {
            if( is_wp_error( $user ) ) {
                print_r($user_id->get_error_message(),true) . '  '.$username;
            }
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $user_id = wp_create_user( $username, $random_password, $username.'@eyespyit.com' );
            wp_clear_auth_cookie();
            wp_set_current_user ( $user_id );
            wp_set_auth_cookie  ( $user_id, true );
            echo $user_id;
            
        }
        //now remove the entry
        $wpdb->delete( $table, array( 'phone' => $phone ) );
    } 
    if($pass == 'error' || $pass = 'banned') {
        echo $pass;
    }  
    wp_die();
}

/*
 *
 * Send Twilio SMS
 * 
 */
function twilio_message($phone, $message) {
    $account_sid = Twilio_Account_ID;
    $auth_token = Twilio_Auth_Token;
    $twilio_number = Twilio_Phone_Number;
    $client = new Client($account_sid, $auth_token);
    $client->messages->create(
        // Where to send a text message (your cell phone?)
        $phone,
        array(
            'from' => $twilio_number,
            'body' => $message
        )
    );
}


/*
 *
 * Ajax Save/Update User Name
 * 
 */
add_action( 'wp_ajax_save_user_name','save_user_name' );
add_action( 'wp_ajax_nopriv_save_user_name','save_user_name' );
function save_user_name(){
    $name = $_POST['name'];
    $user_id = $_POST['user_id'];
    $user = get_userdata( $user_id );
    $args = array(
        'ID'           => $user_id,
        'display_name' => $name,
        'nickname'     => $name
    );
    if(!wp_update_user( $args )) {
        echo 'error';
    }
    wp_die();
}

/*
 *
 * Ajax Report Image/User
 * 
 */
add_action( 'wp_ajax_report_image','report_image' );
add_action( 'wp_ajax_nopriv_report_image','report_image' );
function report_image(){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    $reports = get_post_meta($post_id,'reported_image');
    if(!in_array($user_id,$reports)) {
        add_post_meta($post_id,'reported_image',$user_id);
        $commander = get_post_meta($post_id,'ic_commander',true);
        add_user_meta($commander,'reported_user',true);
        $user_reports = get_post_meta($post_id,'reported_user');
        $user_report_count = count($user_reports);
        if($user_report_count > 3) {
            add_user_meta($commander,'banned',true);
            $sessions = WP_Session_Tokens::get_instance($commander);
            $sessions->destroy_all();
        }
        $reports = get_post_meta($post_id,'reported_image');
        $report_count = count($reports);

        if($report_count > 3) {
            $calculating = get_post_meta($post_id,'calculating',true);
            if(!$calculating) {
                add_post_meta($post_id,'calculating',true);
                delete_post_meta($post_id, 'ic_image');
                delete_post_meta($post_id, 'ic_text');
                $time = current_time('timestamp');
                $page_version_hash = wp_hash($time);
                $page_version = substr($page_version_hash, 0, 8);
                delete_post_meta( $post_id, 'force_refresh_current_page_version' );
                delete_post_meta($post_id,'calculating');
                update_post_meta( $post_id, 'force_refresh_current_page_version', $page_version );
            }
        }
        
        echo 'Image Reported';
    } else {
        echo 'Already Reported';
    }
    wp_die();
}