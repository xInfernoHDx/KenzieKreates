<?php
/**
 * Kenzy Kreates theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- Data loaders (cached per request) ---------- */

function kk_config() {
	static $config = null;
	if ( null === $config ) {
		$config = require get_stylesheet_directory() . '/inc/compliance-config.php';
	}
	return $config;
}

function kk_menu() {
	static $menu = null;
	if ( null === $menu ) {
		$menu = require get_stylesheet_directory() . '/inc/menu-data.php';
	}
	return $menu;
}

/**
 * Items that carry an image, for the front-page featured grid.
 */
function kk_featured_items( $limit = 6 ) {
	$featured = array();
	foreach ( kk_menu() as $cat ) {
		foreach ( $cat['items'] as $item ) {
			if ( ! empty( $item['image'] ) && empty( $item['sold_out'] ) ) {
				$item['category'] = $cat['title'];
				$featured[]       = $item;
			}
		}
	}
	return array_slice( $featured, 0, $limit );
}

/* ---------- Theme setup ---------- */

function kk_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'kk_theme_setup' );

/* ---------- Assets ---------- */

function kk_enqueue_assets() {
	wp_enqueue_style(
		'kk-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fredoka:wght@400;500;600&display=swap',
		array(),
		null
	);

	$css_path = get_stylesheet_directory() . '/assets/css/custom.css';
	wp_enqueue_style(
		'kk-custom',
		get_stylesheet_directory_uri() . '/assets/css/custom.css',
		array( 'kk-fonts' ),
		file_exists( $css_path ) ? filemtime( $css_path ) : '1.0'
	);

	$js_path = get_stylesheet_directory() . '/assets/js/script.js';
	wp_enqueue_script(
		'kk-script',
		get_stylesheet_directory_uri() . '/assets/js/script.js',
		array(),
		file_exists( $js_path ) ? filemtime( $js_path ) : '1.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kk_enqueue_assets' );

/* ---------- Routes: /menu/, /about/, /faq/ ---------- */

function kk_register_routes() {
	add_rewrite_rule( '^menu/?$', 'index.php?kk_view=menu', 'top' );
	add_rewrite_rule( '^about/?$', 'index.php?kk_view=about', 'top' );
	add_rewrite_rule( '^faq/?$', 'index.php?kk_view=faq', 'top' );
}
add_action( 'init', 'kk_register_routes' );

function kk_register_query_vars( $vars ) {
	$vars[] = 'kk_view';
	$vars[] = 'inquiry';
	return $vars;
}
add_filter( 'query_vars', 'kk_register_query_vars' );

function kk_template_include( $template ) {
	$view = get_query_var( 'kk_view' );
	$map  = array(
		'menu'  => '/template-menu.php',
		'about' => '/template-about.php',
		'faq'   => '/template-faq.php',
	);
	if ( isset( $map[ $view ] ) ) {
		$custom = get_stylesheet_directory() . $map[ $view ];
		if ( file_exists( $custom ) ) {
			status_header( 200 );
			return $custom;
		}
	}
	return $template;
}
add_filter( 'template_include', 'kk_template_include' );

/**
 * Flush rewrite rules once per route-version bump.
 * Bump KK_ROUTE_VERSION whenever routes change.
 */
function kk_maybe_flush_rewrites() {
	$current = '1.0.0';
	if ( get_option( 'kk_route_version' ) !== $current ) {
		kk_register_routes();
		flush_rewrite_rules( false );
		update_option( 'kk_route_version', $current );
	}
}
add_action( 'init', 'kk_maybe_flush_rewrites', 20 );

/* ---------- Image helper ---------- */

/**
 * Render a stock/product image with WebP-sibling fallback.
 * Checks the filesystem with get_stylesheet_directory() (paths), builds URLs
 * with get_stylesheet_directory_uri(). Renders a styled placeholder if the
 * file is missing so a forgotten upload never breaks the layout.
 *
 * @param string $stem  Filename without extension, in assets/images/stock/.
 * @param string $alt   Alt text.
 * @param bool   $eager True for above-the-fold (LCP) images.
 */
function kk_stock_img( $stem, $alt, $eager = false ) {
	$dir  = get_stylesheet_directory() . '/assets/images/stock/';
	$uri  = get_stylesheet_directory_uri() . '/assets/images/stock/';
	$jpg  = $dir . $stem . '.jpg';
	$webp = $dir . $stem . '.webp';

	if ( ! file_exists( $jpg ) && ! file_exists( $webp ) ) {
		printf(
			'<div class="img-placeholder" role="img" aria-label="%s"></div>',
			esc_attr( $alt )
		);
		return;
	}

	$loading = $eager
		? 'loading="eager" fetchpriority="high"'
		: 'loading="lazy" decoding="async"';

	$img = sprintf(
		'<img src="%s" alt="%s" %s>',
		esc_url( $uri . $stem . ( file_exists( $jpg ) ? '.jpg' : '.webp' ) ),
		esc_attr( $alt ),
		$loading
	);

	if ( file_exists( $webp ) && file_exists( $jpg ) ) {
		printf(
			'<picture><source srcset="%s" type="image/webp">%s</picture>',
			esc_url( $uri . $stem . '.webp' ),
			$img
		);
	} else {
		echo $img; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
	}
}

/* ---------- SEO: LocalBusiness JSON-LD + Open Graph ---------- */

function kk_head_meta() {
	$config   = kk_config();
	$hero_uri = get_stylesheet_directory_uri() . '/assets/images/stock/hero-bakes.jpg';

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Bakery',
		'name'        => $config['business_name'],
		'description' => 'Homemade baked treats and sweets, made to order. A Florida cottage food operation.',
		'telephone'   => $config['phone'],
		'email'       => $config['email'],
		'url'         => home_url( '/' ),
		'image'       => $hero_uri,
		'servesCuisine' => 'Baked goods and desserts',
		'address'     => array(
			'@type'          => 'PostalAddress',
			'addressRegion'  => 'FL',
			'addressCountry' => 'US',
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $config['business_name'] ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $hero_uri ) );
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $config['business_name'] . ': homemade cookies, cake pops, brownies, and chocolate covered treats, baked to order in Florida. Custom orders welcome.' ) );
}
add_action( 'wp_head', 'kk_head_meta', 5 );

/* ---------- Shared components ---------- */

/**
 * The F.S. 500.80 cottage food disclosure block.
 */
function kk_disclosure() {
	$config = kk_config();
	printf(
		'<div class="disclosure"><p>%s</p></div>',
		esc_html( $config['disclosure'] )
	);
}

/* ---------- Falling treats background ---------- */

/**
 * Sitewide falling-treats decor: cupcakes with candles, cookies, brownies,
 * and sprinkles raining slowly behind the content. Fixed layer at z-index -1,
 * so it shows only in blank background space and hides behind opaque cards
 * and tinted sections. Decorative only: aria-hidden, pointer-events none,
 * removed entirely under prefers-reduced-motion (see custom.css).
 */
function kk_falling_treats() {
	$svgs = array(
		'cupcake' => '<svg viewBox="0 0 40 46" xmlns="http://www.w3.org/2000/svg">'
			. '<ellipse cx="20" cy="4" rx="2.4" ry="3.6" fill="#ecc35c"/>'
			. '<rect x="18.4" y="6" width="3.2" height="9" rx="1.6" fill="#bfa1e8"/>'
			. '<path d="M7 26q0-12 13-12t13 12z" fill="#f6c7db"/>'
			. '<path d="M7 26h26l-3.5 15q-.6 4-4 4h-11q-3.4 0-4-4z" fill="#ec93bb"/>'
			. '</svg>',
		'cookie'  => '<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">'
			. '<circle cx="20" cy="20" r="17" fill="#e6b566"/>'
			. '<circle cx="13" cy="14" r="2.2" fill="#7a4a2d"/>'
			. '<circle cx="24" cy="11" r="2.2" fill="#7a4a2d"/>'
			. '<circle cx="27" cy="22" r="2.2" fill="#7a4a2d"/>'
			. '<circle cx="16" cy="26" r="2.2" fill="#7a4a2d"/>'
			. '<circle cx="22" cy="30" r="2" fill="#7a4a2d"/>'
			. '</svg>',
		'brownie' => '<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">'
			. '<rect x="5" y="8" width="30" height="26" rx="5" fill="#6b4230"/>'
			. '<path d="M5 13q0-5 5-5h20q5 0 5 5v4H5z" fill="#8a5a41"/>'
			. '<circle cx="15" cy="25" r="1.6" fill="#a97c5e"/>'
			. '<circle cx="25" cy="28" r="1.4" fill="#a97c5e"/>'
			. '</svg>',
	);

	$treats = array(
		array( 'type' => 'cupcake',  'x' => '4%',  'size' => 38, 'dur' => 26, 'delay' => 0,   'op' => 0.42, 'drift' => '4vw',  'spin' => '18deg' ),
		array( 'type' => 'sprinkle', 'x' => '9%',  'size' => 16, 'dur' => 19, 'delay' => -7,  'op' => 0.38, 'drift' => '-3vw', 'spin' => '220deg',  'c' => 'var(--sprk-teal)', 'mhide' => true ),
		array( 'type' => 'cookie',   'x' => '15%', 'size' => 30, 'dur' => 23, 'delay' => -12, 'op' => 0.40, 'drift' => '3vw',  'spin' => '-160deg' ),
		array( 'type' => 'brownie',  'x' => '22%', 'size' => 34, 'dur' => 28, 'delay' => -4,  'op' => 0.38, 'drift' => '-4vw', 'spin' => '24deg',   'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '28%', 'size' => 14, 'dur' => 17, 'delay' => -9,  'op' => 0.35, 'drift' => '2vw',  'spin' => '300deg',  'c' => 'var(--sprk-gold)', 'mhide' => true ),
		array( 'type' => 'cupcake',  'x' => '35%', 'size' => 30, 'dur' => 24, 'delay' => -16, 'op' => 0.40, 'drift' => '-3vw', 'spin' => '-20deg' ),
		array( 'type' => 'cookie',   'x' => '43%', 'size' => 26, 'dur' => 21, 'delay' => -2,  'op' => 0.36, 'drift' => '4vw',  'spin' => '200deg',  'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '50%', 'size' => 15, 'dur' => 18, 'delay' => -11, 'op' => 0.35, 'drift' => '-2vw', 'spin' => '-260deg', 'c' => 'var(--sprk-lav)', 'mhide' => true ),
		array( 'type' => 'brownie',  'x' => '57%', 'size' => 30, 'dur' => 27, 'delay' => -19, 'op' => 0.40, 'drift' => '3vw',  'spin' => '-18deg' ),
		array( 'type' => 'cupcake',  'x' => '64%', 'size' => 42, 'dur' => 29, 'delay' => -6,  'op' => 0.44, 'drift' => '-5vw', 'spin' => '16deg',   'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '71%', 'size' => 16, 'dur' => 16, 'delay' => -13, 'op' => 0.36, 'drift' => '3vw',  'spin' => '240deg',  'c' => 'var(--sprk-pink)', 'mhide' => true ),
		array( 'type' => 'cookie',   'x' => '78%', 'size' => 32, 'dur' => 22, 'delay' => -8,  'op' => 0.42, 'drift' => '-3vw', 'spin' => '180deg' ),
		array( 'type' => 'brownie',  'x' => '85%', 'size' => 28, 'dur' => 25, 'delay' => -15, 'op' => 0.38, 'drift' => '4vw',  'spin' => '-26deg',  'mhide' => true ),
		array( 'type' => 'cupcake',  'x' => '92%', 'size' => 34, 'dur' => 27, 'delay' => -3,  'op' => 0.40, 'drift' => '-4vw', 'spin' => '22deg' ),
	);

	echo '<div class="treat-rain" aria-hidden="true">';
	foreach ( $treats as $t ) {
		$style = sprintf(
			'--x:%s; --size:%dpx; --dur:%ds; --delay:%ds; --op:%.2f; --drift:%s; --spin:%s;%s',
			$t['x'],
			$t['size'],
			$t['dur'],
			$t['delay'],
			$t['op'],
			$t['drift'],
			$t['spin'],
			isset( $t['c'] ) ? ' --c:' . $t['c'] . ';' : ''
		);
		$class = 'treat treat--' . $t['type'] . ( ! empty( $t['mhide'] ) ? ' treat--m-hide' : '' );
		printf(
			'<span class="%s" style="%s">%s</span>',
			esc_attr( $class ),
			esc_attr( $style ),
			isset( $svgs[ $t['type'] ] ) ? $svgs[ $t['type'] ] : ''
		);
	}
	echo '</div>';
}

/* ---------- Custom order inquiry form handler ---------- */

function kk_handle_inquiry() {
	$back = home_url( '/menu/' );

	// Honeypot: bots fill it, humans never see it. Pretend success.
	if ( ! empty( $_POST['kk_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'sent', $back ) . '#custom-orders' );
		exit;
	}

	if ( ! isset( $_POST['kk_inquiry_nonce'] ) || ! wp_verify_nonce( $_POST['kk_inquiry_nonce'], 'kk_inquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'error', $back ) . '#custom-orders' );
		exit;
	}

	$name    = isset( $_POST['kk_name'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_name'] ) ) : '';
	$email   = isset( $_POST['kk_email'] ) ? sanitize_email( wp_unslash( $_POST['kk_email'] ) ) : '';
	$phone   = isset( $_POST['kk_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_phone'] ) ) : '';
	$event   = isset( $_POST['kk_occasion'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_occasion'] ) ) : '';
	$date    = isset( $_POST['kk_date'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_date'] ) ) : '';
	$serves  = isset( $_POST['kk_servings'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_servings'] ) ) : '';
	$budget  = isset( $_POST['kk_budget'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_budget'] ) ) : '';
	$message = isset( $_POST['kk_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kk_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'inquiry', 'error', $back ) . '#custom-orders' );
		exit;
	}

	$config = kk_config();

	$body  = "New custom order inquiry from the website:\n\n";
	$body .= 'Name: ' . $name . "\n";
	$body .= 'Email: ' . $email . "\n";
	$body .= 'Phone: ' . ( $phone ? $phone : 'not given' ) . "\n";
	$body .= 'Occasion: ' . ( $event ? $event : 'not given' ) . "\n";
	$body .= 'Date needed: ' . ( $date ? $date : 'not given' ) . "\n";
	$body .= 'Servings / quantity: ' . ( $serves ? $serves : 'not given' ) . "\n";
	$body .= 'Budget: ' . ( $budget ? $budget : 'not given' ) . "\n\n";
	$body .= "Their vision:\n" . $message . "\n";

	$sent = wp_mail(
		$config['email'],
		'Custom order inquiry from ' . $name,
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	$flag = $sent ? 'sent' : 'error';
	wp_safe_redirect( add_query_arg( 'inquiry', $flag, $back ) . '#custom-orders' );
	exit;
}
add_action( 'admin_post_kk_inquiry', 'kk_handle_inquiry' );
add_action( 'admin_post_nopriv_kk_inquiry', 'kk_handle_inquiry' );
