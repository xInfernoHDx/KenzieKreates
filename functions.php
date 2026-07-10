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
