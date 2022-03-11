</main>
<?php //if(wp_is_mobile()) { ?>
<nav class="chat">
    <div class="nav-content">
        <?php echo do_shortcode('[wise-chat channel="'.get_post_meta(get_the_ID(),'ic_game',true).'" emoticons_enabled="0" background_color="#834c69" background_color_input="834c69" mode="0" window_title="" filter_bad_words="0" chat_height="300px"  background_color_chat="#834c69"]'); ?>
    </div>
</nav>
<?php //} ?>
<?php wp_footer(); ?>
</body>
</html>