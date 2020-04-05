<?php 
/**
* Template Name: Game
*
*/
get_header(); 
$game_id = get_post_meta(get_the_ID(),'ic_game',true);
embed_game($game_id);
modal_login();
get_footer(); ?>