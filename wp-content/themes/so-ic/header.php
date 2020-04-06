<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0" >
    <link rel="icon" href="fav.png">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-user="<?php echo get_current_user_id(); ?>"  data-id="<?php echo get_post_meta(get_the_ID(),'ic_game',true); ?>">
    <?php if(!is_singular('games')) { ?>
        <header>
            <div id="logo"><h1>Eye Spy It</h1></div>
            <?php if(wp_is_mobile()) { ?>
                <div class="navtrigger hamburger hamburger--squeeze js-hamburger">
                    <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                    </div>
                </div>
                <div class="chattrigger">
                    <div class="speech-bubble"></div>
                </div>
            <?php } ?>
            
        </header>
        <?php if(wp_is_mobile()) { ?>
            <nav class="navigation">
                <div class="nav-content">
                    <?php 
                    $user_id = get_current_user_id();
                    if($user_id > 0) {
                        $user = get_user_by('id',$user_id); ?>
                        <div class="displayname">Name:
                            <input id="displayname" value="<?php echo  $user->display_name; ?>" />
                            <div class="checkcontainer"><div class="check"></div></div>
                        </div>

                        <div class="reportexplanation"><strong>Content notice:</strong><br>If an image is reported more than 3 times, it will be removed from the site and a new user will be selected to upload an image. If a user is reported more than 3 times they will automatically be banned from participating.</div>
                        <div id="reportimage">Report Image</div>
                    <?php } ?>
                </div>
                <div id="squab"><a href="https://squabbleable.com" title="Try Squabbleable" target="_blank">Liking Eye Spy It? Try our other game<span><img src="<?php echo get_template_directory_uri(); ?>/images/squabbleable.png" /></span></a>
            </nav>
        <?php } ?>
    <?php } ?>
    <main>