<?php
/**
 * Template Name: Front Page
 */
get_header(); ?>

    <!-- begin content -->
    <section id="content" class="container clearfix">
        <!-- begin slider -->
        <section id="slider-home">
        <?php Page_Media_Manager::render_page_media(); ?>
        </section>
        <!-- end slider -->

        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
        <?php endwhile;?>
    </section>
    <!-- end content -->

<?php get_footer(); ?>