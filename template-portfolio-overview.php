<?php
/**
 * Template Name: Portfolio Overview
 */
get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php _e('Portfolio', 'finesse'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->

    <!-- begin filter -->
    <div id="filter">
        <span><?php _e('Filter', 'finesse'); ?>:</span>
        <ul>
            <li class="active"><a href="#" class="all"><?php _e("All", "finesse"); ?></a></li>
            <?php $categories = get_categories('taxonomy=filter&orderby=name&order=desc');
            foreach ($categories as $cat) {
                echo '<li><a href="#" class="' . $cat->category_nicename . '">'.___($cat->cat_name).'</a></li>';
            } ?>
        </ul>
    </div>
    <!-- end filter -->

    <!-- begin gallery -->
    <ul id="gallery" class="portfolio-grid clearfix">
        <?php
            if ( get_query_var('paged') ) {
                $paged = get_query_var('paged');
            } elseif ( get_query_var('page') ) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            wp_reset_query();
            $posts_per_page = 4 * get_portfolio_rows_count();
            query_posts('post_type=portfolio&posts_per_page='.$posts_per_page.'&order=DESC&paged='.$paged);
            $i = 1;
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    $data_id = 'id-'+$i;

                    $terms = get_the_terms($post->ID, 'filter');
                    $data_type = '';
                    if($terms){
                        foreach ($terms as $term) {
                            $data_type .= ' ' . $term->slug;
                        }
                    }
                    $data_type = trim($data_type);

                    $wrap_start = '<li data-id="'.$data_id.'" data-type="'.$data_type.'" class="entry one-fourth">';
                    $wrap_end = '</li>';
                    echo Posts_Carousel_Shortcode::get_portfolio_carousel_item($wrap_start, $wrap_end);
                    $i++;
                }
            }
        ?>
    </ul>
    <!-- end gallery -->

    <!-- begin pagination -->
    <?php do_finesse_pagination($wp_query->max_num_pages); ?>
    <!-- end pagination -->

    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>
