<?php include_once 'api/finesse-util.php'; ?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <!-- begin meta -->
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="author" content="<?php bloginfo('name');?>">
    <?php if (is_responsive_enabled()) : ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php endif; ?>
    <!-- end meta -->

    <!-- begin CSS -->
    <link href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" rel="stylesheet" id="main-style">
    <?php if (is_responsive_enabled()) : ?>
    <link href="<?php echo get_template_directory_uri(); ?>/responsive.css" type="text/css" rel="stylesheet">
    <?php endif; ?>
    <!--[if IE]> <link href="<?php echo get_template_directory_uri(); ?>/css/ie.css" type="text/css" rel="stylesheet"> <![endif]-->
    <!-- end CSS -->
	
	<link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="icon" type="image/ico" href="/favicon.ico"/>
    <link href="<?php echo get_fav_icon(); ?>" type="image/x-icon" rel="shortcut icon">

    <!-- begin JS -->
    <?php wp_head(); ?>
    <!-- end JS -->

    <link href="<?php echo get_template_directory_uri(); ?>/custom.css" type="text/css" rel="stylesheet">
    <?php if (is_user_logged_in()) : ?>
    <style type="text/css" media="screen">
        html { margin-top: 0 !important; }
        * html body { margin-top: 0 !important; }
    </style>
    <?php endif; ?>

    <title><?php echo get_bloginfo('name'); wp_title();?></title>
</head>

<body <?php body_class(); ?>>
<!-- begin container -->
<div id="wrap">
    <!-- begin header -->
    <header id="header" class="container">
        <!-- begin header top -->
        <section id="header-top" class="clearfix">
            <!-- begin header left -->
            <div class="header-section first">
                <h1 id="logo"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo get_header_logo(); ?>" alt="Houston Sexual Assault Lawyer |  Sex Crime Defense | Scheiner Law Group P.C." /></a></h1>
                <p id="tagline"><?php echo get_tagline(); ?></p>
            </div>
            <!-- end header left -->

            <!-- begin header right -->
            <div class="header-section last">
                <?php if(is_contact_details_displayed_in_header_enabled()) : ?>
                <!-- begin contact info -->
                <div class="contact-info clearfix">
                    <div class="my-contact first">
                        <?php $contact_email = get_contact_email(); if(strlen($contact_email)>0) : ?>
                        <p class="email"><a href="/contact-us"><?php echo $contact_email; ?></a></p>
                        <?php endif;?>
                        <p class="appointment"><a href="https://www.schedulicity.com/scheduling/SLGNCU" title="Online scheduling" target="_blank"><img src="http://www.greaterhoustondefense.com/wp-content/uploads/2015/01/button-schedule-appointment1.png" alt="Schedule online now" border="0" width="200" vspace="5" /></a></p>
                    </div>
                    <div class="my-contact last">
                        <?php $contact_phone = get_contact_phone(); if(strlen($contact_phone)>0) : ?>
                        <p class="phone hide-xs">Phone: <a href="tel:7138079700"><?php echo $contact_phone; ?></a></p>
                        <p class="phone-xs show-xs"><a href="tel:7138079700">Call <?php echo $contact_phone; ?></a></p>
                        <?php endif;?>
                        <p>24-hr. Voice/Text: <a href="tel:7135814540">(713) 581-4540</a></p>
                        <p>Espa&ntilde;ol: <a href="tel:7132269393">(713) 226-9393</a></p>
                    </div>
                </div>
                <!-- end contact info -->
                <?php endif;?>
            </div>
            <!-- end header right -->
        </section>
        <!-- end header top -->

        <!-- begin navigation bar -->
        <section id="navbar" class="clearfix">
            <!-- begin navigation -->
            <?php global $header_menu_walker;
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container' => 'nav',
                        'container_id' => 'nav',
                        'container_class' => ' ',
                        'items_wrap' => '<ul id="navlist" class="clearfix">%3$s</ul>',
                        'walker' => $header_menu_walker ) );
            ?>
            <!-- end navigation -->

            <?php if ( is_search_header_displayed() ) : ?>
            <!-- begin search form -->
            <form id="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
                <input id="s" type="text" name="s" placeholder="<?php _e('Search', 'finesse') ?> &hellip;" style="display: none;">
                <input id="search-submit" type="submit" name="search-submit" value="<?php _e('Search', 'finesse') ?>">
            </form>
            <!-- end search form -->
            <?php endif; ?>
        </section>
        <!-- end navigation bar -->

    </header>
    <!-- end header -->
