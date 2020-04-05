<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0" >
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-user="<?php echo get_current_user_id(); ?>"  data-id="<?php echo get_post_meta(get_the_ID(),'ic_game',true); ?>">
    <?php if(!is_singular('games')) { ?>
        <header>
            <div id="logo"><h1>Inspector/Commander</h1></div>
        </header>
    <?php } ?>
    <main>