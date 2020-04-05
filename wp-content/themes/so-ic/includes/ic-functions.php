<?php 
/*
 *
 * Functions for IC Game
 * 
 */
require_once(get_stylesheet_directory() . '/includes/twilio-php-master/src/Twilio/autoload.php');
use Twilio\Rest\Client;

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

function embed_game($post_id) {
    echo '<div class="gameembed"><iframe src="'.get_permalink($post_id).'"></iframe></div>';
}

function inspector_commander($gametype) {
    global $post;
    $post_id = $post->ID;
    $ready = get_post_meta($post_id,'ic_ready',true);
    $commander = get_post_meta($post_id,'ic_commander',true);
    if($commander === '') {
        $calculating = get_post_meta($post_id,'calculating',true);
        if(!$calculating) {
            add_post_meta($post_id,'calculating',true);
            update_post_meta($post_id,'ic_commander',get_current_user_id());
            delete_post_meta($post_id,'calculating');
        }
        $commander = get_post_meta($post_id,'ic_commander',true);
    }
    $current_user_id = get_current_user_id();

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
        if($commander === $current_user_id) {   
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
                echo '<div class="step step3 inputarea"><span>Hint for Inspectors:</span><input type="text" id="inspectorhint" placeholder="Something Black" value=""  autocomplete="off" data-user="'.$current_user_id.'" data-id="'.get_the_ID().'" /><div class="checkarea"><div class="check"></div></div></div>';
                echo '<div class="step step3 text">Tap the checkmark to continue</div>';
                echo '<div class="step step4">Starting Game...</div>';
                echo '<div class="step step5">Players are Guessing!</div>';
                echo '<div class="step steptimer on"></div>';
            echo '</div>';
        } else {
            $image = get_post_meta($post_id,'ic_image',true);
            $url = wp_get_attachment_image_src($image,'ic-thumb');
            $text = get_post_meta($post_id,'ic_text',true);
            if($text) {
                echo '<div class="toggletip">?</div>';
                echo '<div class="ictext hidden"><strong>Hint:</strong> '.$text.'</div>';
            }
            echo '<h3 class="waiting">Waiting for the commander to upload an image</h3>';
            echo '<div class="content-wrapper flex-center">';
                echo '<div class="content full">';
                    echo '<div id="inspector-image" data-c="'.get_post_meta($post_id,'ic_commander',true).'"><img src="'.$url[0].'" /></div>';
                    position_marker($post_id,'gameover');
                echo '</div>';
            echo '</div>';
           
        }
        /*$waiting = get_post_meta($post_id,'waiting_on_commander',true);
        if($waiting) {
            $max = $waiting - Game_Timer;
            echo '<div id="max-timer" data-time="'.$waiting.'" data-max="'.$max.'"><span></span></div>';
        }*/
    }
    
}

/*add_action('template_redirect','check_for_commanders');
function check_for_commanders() {
    global $post;
    $post_id = $post->ID;
    $waiting = get_post_meta($post_id,'waiting_on_commander',true);
    if($waiting) {
        $time = current_time('timestamp');
        if($waiting < $time) { //time has passed the waiting period
            $commander = get_post_meta($post_id,'ic_commander',true);
            $current_user_id = get_current_user_id();
            if($commander != $current_user_id && $current_user_id != 0) {
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
    
}*/


function modal_login() {
    if(get_current_user_id() > 0) {
        $class = 'unneeded';
    }
    echo '<div class="modal-login '.$class.'">';
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
                /*echo '<div class="step3">';
                    echo '<h4>Pick A Display Name</h4>';
                    echo '<input id="name" type="text" placeholder="Your Name" value=""  autocomplete="off" />';
                    echo '<div id="addname">Start Playing</div><div id="nameerror"></div>';
                echo '</div>';*/
            echo '</div>';
        echo '</div>';
    echo '</div>';
}
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
function upload_image_form($post_id) {
    $last = get_post_meta($post_id,'last_ic_commander');
    $current = get_current_user_id();

    $form = '<form id="upload-ic" method="post" enctype="multipart/form-data"><div class="image-upload-wrap">
            <input type="hidden" id="post_id" value="'.get_the_ID().'" />
            <input class="file-upload-input" id="ic-image" name="ic-image" type="file" accept="image/*" />
            <div class="drag-text">
                <h3>';
                if($last != $current) {
                    $form .= 'Nice Job!<br>';
                } else {
                    $form .= 'The Commander took too long!<br>';
                }
                
            $form .= 'Now you&rsquo;re the Commander!<br><span>Add an Image</span></h3>'.loading_icon().'
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

function loading_icon() {
    return '<div class="loading-icon"><div class="loading"><div><div></div><div></div><div></div></div></div></div>';
}
add_action( 'wp_ajax_save_ic_image','save_ic_image' );
add_action( 'wp_ajax_nopriv_save_ic_image','save_ic_image' );
function save_ic_image(){
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
      
    $uploadedfile = $_FILES['file'];
    
    $upload_overrides = array('test_form' => false);
    $uploaded_file = wp_handle_upload($uploadedfile, $upload_overrides);

    // echo $movefile['url'];
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

            //echo 'ID'. $id;
            echo $url[0];
        } // if ()
        //echo $uploaded_file['url'];
    } else {
        /**
         * Error generated by _wp_handle_upload()
         * @see _wp_handle_upload() in wp-admin/includes/file.php
         */
        echo $uploaded_file['error'];
    }
    wp_die();
}
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
add_action( 'wp_ajax_end_ic_round','end_ic_round' );
add_action( 'wp_ajax_nopriv_end_ic_round','end_ic_round' );
function end_ic_round(){
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $calculating = get_post_meta($post_id,'calculating',true);
    if(!$calculating) {
        add_post_meta($post_id,'calculating',true);
        $last_commander = get_post_meta($post_id,'ic_commander',true);
        update_post_meta($post_id,'last_ic_commander',$last_commander);
        update_post_meta($post_id,'ic_commander',$user_id);
        delete_post_meta($post_id,'ic_ready');
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
    
    

    $message = 'Inspector/Commander Verification Code is '.$code;
    twilio_message($phone, $message);
    wp_die();

}

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
            wp_clear_auth_cookie();
            wp_set_current_user ( $user_id );
            wp_set_auth_cookie  ( $user_id, true );
            echo $user_id;
        } else {
            if( is_wp_error( $user ) ) {
                print_r($user_id->get_error_message(),true) . '  '.$username;
            }
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $user_id = wp_create_user( $username, $random_password, NULL );
            wp_clear_auth_cookie();
            wp_set_current_user ( $user_id );
            wp_set_auth_cookie  ( $user_id, true );
            echo $user_id;
            
        }
    } 
    if($pass === 'error') {
        echo $pass;
    }

   
    
    wp_die();

}

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

function create_posttype() {
    register_post_type( 'games',
    // CPT Options
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
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );