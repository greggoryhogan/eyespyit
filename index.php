<?php get_header(); ?>
<?php 
if ( have_posts() ) {

    while ( have_posts() ) {
        
        the_post();
        //do something
        //get_template_part( 'template-parts/content', get_post_type() );

    }
} ?>
<?php get_footer(); ?>