<?php 
/**
* Template Name: Game
*
*/
get_header(); 
    $game_id = get_post_meta(get_the_ID(),'ic_game',true);
    embed_game($game_id);
    if(wp_is_mobile()) {
        modal_login();
    } else {
        modal_alert();
    }
get_footer(); ?>