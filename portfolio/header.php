<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png?v=1" />

	<?php wp_head(); ?>

	<title><?php wp_title(''); ?></title>
</head>

<body <?php body_class(); ?>>

    <header id="header">
        <div class="container">
            <a class="logo" href="<?php echo home_url('/'); ?>">
                <img src="<?php echo Theme::asset('logo.png'); ?>" />
            </a>

            <nav id="nav">
                <?php wp_nav_menu(['theme_location' => 'main']); ?>
                <div class="nav-trigger"><span></span></div>
            </nav>
        </div>
    </header>

    <div id="main">

