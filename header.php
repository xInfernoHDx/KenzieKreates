<?php
/**
 * Site header: <head>, sticky nav, mobile drawer.
 */

$kk = kk_config();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php kk_falling_treats(); ?>

<a class="screen-reader-text" href="#main">Skip to content</a>

<header class="site-header">
	<div class="container nav-bar">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="brand-name"><?php echo esc_html( $kk['business_name'] ); ?></span>
			<span class="brand-tag"><?php echo esc_html( $kk['tagline'] ); ?></span>
		</a>

		<nav aria-label="Main navigation">
			<ul class="nav-links">
				<li><a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">Menu</a></li>
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
				<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
				<li><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Order Treats</a></li>
			</ul>
		</nav>

		<button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
			<span class="nav-toggle-bar"></span>
			<span class="nav-toggle-bar"></span>
			<span class="nav-toggle-bar"></span>
		</button>
	</div>
</header>

<div class="nav-overlay" id="navOverlay" hidden></div>

<nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation" hidden>
	<button class="mobile-nav-close" id="mobileNavClose" aria-label="Close menu">&times;</button>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
		<li><a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">Menu</a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
		<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
		<li><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Order Treats</a></li>
	</ul>
</nav>

<main id="main">
