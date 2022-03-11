<?php 
if (is_first_time()) {
    header("Link: </wp-content/themes/so-ic/style.css?ver=1.0>; rel=preload; as=style", false);
    header("Link: </wp-content/themes/so-ic/js/scripts.js?ver=1.0>; rel=preload; as=script", false);
    header("Link: </wp-content/themes/so-ic/js/game.js?ver=1.0>; rel=preload; as=script", false);
} ?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<?php if(!is_singular('games')) { ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-162922731-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-162922731-1');
</script>
<?php } ?>
<title>Eye Spy It | Are you fast enough to win?</title>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0" >
<link rel="icon" href="fav.png">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-user="<?php echo get_current_user_id(); ?>"  data-id="<?php echo get_post_meta(get_the_ID(),'ic_game',true); ?>">
    <div id="loadOverlay" style="background-color:#33313b; position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:2000;"></div>
    <?php $user_id = get_current_user_id(); ?>
    <?php if(!is_singular('games')) { ?>
        <header>
            <div id="logo"><h1>Eye Spy It<img src="<?php echo get_template_directory_uri(); ?>/images/eyespyitlogo.png" /></h1></div>
            <?php /*if(wp_is_mobile()) {*/ ?>
                <div class="navtrigger hamburger hamburger--squeeze js-hamburger">
                    <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                    </div>
                </div>
                <div class="chattrigger">
                    <div class="speech-bubble"></div>
                </div>
            <?php /*}*/ ?>
            
        </header>
        <?php /*if(wp_is_mobile()) {*/ ?>
            <nav class="navigation">
                <div class="nav-content">
                    <?php 
                    if($user_id > 0) {
                        $user = get_user_by('id',$user_id); ?>
                        <div class="displayname">Name:
                            <input id="displayname" value="<?php echo  $user->display_name; ?>" />
                            <div class="checkcontainer"><div class="check"></div></div>
                        </div>
                    <?php } ?>
                    <div class="reportexplanation"><strong>How to Play:</strong><br>Eye Spy It is a modern take on I Spy. Users upload a photo and it&rsquo;s up to you to spot &ldquo;it&rdquo; faster than the competition. When you find it, tap on the screen to see if you&rsquo;re right!</div>

                    <div class="reportexplanation"><strong>Content notice:</strong><br>If an image is reported more than 3 times, it will be removed from the site and a new user will be selected to upload an image. If a user is reported more than 3 times they will automatically be banned from participating.</div>
                    <?php if($user_id > 0) { ?>
                        <div id="reportimage">Report Image</div>
                    <?php } ?>
                    
                </div>
                <div id="squab"><a href="https://squabbleable.com" title="Try Squabbleable" target="_blank">Liking Eye Spy It? Try our other game<span><img src="<?php echo get_template_directory_uri(); ?>/images/squabbleable.png" /></span></a>
            </nav>
        <?php/* } */ ?>
    <?php } ?>
    <main>