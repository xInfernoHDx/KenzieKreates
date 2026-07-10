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
 * Stable machine id for a menu item (used by the cart and order handler).
 */
function kk_item_id( $cat_key, $item_name ) {
	return sanitize_title( $cat_key . '-' . $item_name );
}

/**
 * Numeric price from a display price string like '$2.50'.
 */
function kk_item_price_num( $price ) {
	return (float) str_replace( array( '$', ',' ), '', $price );
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
	$vars[] = 'order_status';
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
			. '<ellipse cx="20" cy="4" rx="2.6" ry="3.8" fill="#f0b429"/>'
			. '<rect x="18.4" y="6" width="3.2" height="9" rx="1.6" fill="#a678e8"/>'
			. '<path d="M7 26q0-12 13-12t13 12z" fill="#f18ab5"/>'
			. '<path d="M7 26h26l-3.5 15q-.6 4-4 4h-11q-3.4 0-4-4z" fill="#dd5b95"/>'
			. '</svg>',
		'cookie'  => '<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">'
			. '<circle cx="20" cy="20" r="17" fill="#d99a3e"/>'
			. '<circle cx="13" cy="14" r="2.4" fill="#5d3317"/>'
			. '<circle cx="24" cy="11" r="2.4" fill="#5d3317"/>'
			. '<circle cx="27" cy="22" r="2.4" fill="#5d3317"/>'
			. '<circle cx="16" cy="26" r="2.4" fill="#5d3317"/>'
			. '<circle cx="22" cy="30" r="2.2" fill="#5d3317"/>'
			. '</svg>',
		'brownie' => '<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">'
			. '<rect x="5" y="8" width="30" height="26" rx="5" fill="#55301f"/>'
			. '<path d="M5 13q0-5 5-5h20q5 0 5 5v4H5z" fill="#7a4526"/>'
			. '<circle cx="15" cy="25" r="1.8" fill="#9c6a45"/>'
			. '<circle cx="25" cy="28" r="1.6" fill="#9c6a45"/>'
			. '</svg>',
	);

	$treats = array(
		array( 'type' => 'cupcake',  'x' => '2%',  'size' => 52, 'dur' => 26, 'delay' => 0,   'op' => 0.62, 'drift' => '4vw',  'spin' => '18deg' ),
		array( 'type' => 'sprinkle', 'x' => '7%',  'size' => 20, 'dur' => 19, 'delay' => -7,  'op' => 0.58, 'drift' => '-3vw', 'spin' => '220deg',  'c' => 'var(--sprk-teal)', 'mhide' => true ),
		array( 'type' => 'cookie',   'x' => '12%', 'size' => 42, 'dur' => 23, 'delay' => -12, 'op' => 0.60, 'drift' => '3vw',  'spin' => '-160deg' ),
		array( 'type' => 'brownie',  'x' => '17%', 'size' => 46, 'dur' => 28, 'delay' => -4,  'op' => 0.58, 'drift' => '-4vw', 'spin' => '24deg',   'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '22%', 'size' => 18, 'dur' => 17, 'delay' => -9,  'op' => 0.55, 'drift' => '2vw',  'spin' => '300deg',  'c' => 'var(--sprk-gold)', 'mhide' => true ),
		array( 'type' => 'cupcake',  'x' => '27%', 'size' => 44, 'dur' => 24, 'delay' => -16, 'op' => 0.60, 'drift' => '-3vw', 'spin' => '-20deg' ),
		array( 'type' => 'cookie',   'x' => '32%', 'size' => 38, 'dur' => 21, 'delay' => -2,  'op' => 0.56, 'drift' => '4vw',  'spin' => '200deg',  'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '37%', 'size' => 20, 'dur' => 18, 'delay' => -11, 'op' => 0.55, 'drift' => '-2vw', 'spin' => '-260deg', 'c' => 'var(--sprk-lav)', 'mhide' => true ),
		array( 'type' => 'brownie',  'x' => '42%', 'size' => 42, 'dur' => 27, 'delay' => -19, 'op' => 0.60, 'drift' => '3vw',  'spin' => '-18deg' ),
		array( 'type' => 'cupcake',  'x' => '47%', 'size' => 58, 'dur' => 29, 'delay' => -6,  'op' => 0.65, 'drift' => '-5vw', 'spin' => '16deg',   'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '52%', 'size' => 22, 'dur' => 16, 'delay' => -13, 'op' => 0.56, 'drift' => '3vw',  'spin' => '240deg',  'c' => 'var(--sprk-pink)', 'mhide' => true ),
		array( 'type' => 'cookie',   'x' => '57%', 'size' => 44, 'dur' => 22, 'delay' => -8,  'op' => 0.62, 'drift' => '-3vw', 'spin' => '180deg' ),
		array( 'type' => 'brownie',  'x' => '62%', 'size' => 40, 'dur' => 25, 'delay' => -15, 'op' => 0.58, 'drift' => '4vw',  'spin' => '-26deg',  'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '67%', 'size' => 19, 'dur' => 20, 'delay' => -1,  'op' => 0.55, 'drift' => '-3vw', 'spin' => '280deg',  'c' => 'var(--sprk-gold)', 'mhide' => true ),
		array( 'type' => 'cupcake',  'x' => '72%', 'size' => 48, 'dur' => 27, 'delay' => -3,  'op' => 0.62, 'drift' => '-4vw', 'spin' => '22deg' ),
		array( 'type' => 'cookie',   'x' => '77%', 'size' => 40, 'dur' => 24, 'delay' => -18, 'op' => 0.58, 'drift' => '3vw',  'spin' => '-190deg', 'mhide' => true ),
		array( 'type' => 'sprinkle', 'x' => '82%', 'size' => 20, 'dur' => 17, 'delay' => -5,  'op' => 0.56, 'drift' => '2vw',  'spin' => '-310deg', 'c' => 'var(--sprk-teal)', 'mhide' => true ),
		array( 'type' => 'brownie',  'x' => '87%', 'size' => 48, 'dur' => 26, 'delay' => -10, 'op' => 0.60, 'drift' => '-3vw', 'spin' => '20deg' ),
		array( 'type' => 'cupcake',  'x' => '92%', 'size' => 46, 'dur' => 28, 'delay' => -14, 'op' => 0.60, 'drift' => '4vw',  'spin' => '-16deg' ),
		array( 'type' => 'cookie',   'x' => '96%', 'size' => 36, 'dur' => 20, 'delay' => -21, 'op' => 0.56, 'drift' => '-2vw', 'spin' => '170deg',  'mhide' => true ),
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

/* ---------- Cart order-request handler ---------- */

/**
 * Receives the cart as JSON of {itemId: qty}, rebuilds every name and price
 * from inc/menu-data.php (never trusting client prices), and emails the
 * order request. No payment is taken online; Kenzie confirms the final
 * total (including dozen pricing) directly with the customer.
 */
function kk_handle_order() {
	$back = home_url( '/menu/' );

	if ( ! empty( $_POST['kk_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'order_status', 'sent', $back ) . '#order-status' );
		exit;
	}

	if ( ! isset( $_POST['kk_order_nonce'] ) || ! wp_verify_nonce( $_POST['kk_order_nonce'], 'kk_order' ) ) {
		wp_safe_redirect( add_query_arg( 'order_status', 'error', $back ) . '#order-status' );
		exit;
	}

	$name  = isset( $_POST['kk_name'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_name'] ) ) : '';
	$email = isset( $_POST['kk_email'] ) ? sanitize_email( wp_unslash( $_POST['kk_email'] ) ) : '';
	$phone = isset( $_POST['kk_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_phone'] ) ) : '';
	$date  = isset( $_POST['kk_date'] ) ? sanitize_text_field( wp_unslash( $_POST['kk_date'] ) ) : '';
	$note  = isset( $_POST['kk_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kk_note'] ) ) : '';
	$json  = isset( $_POST['kk_cart_json'] ) ? wp_unslash( $_POST['kk_cart_json'] ) : '';

	$cart = json_decode( $json, true );

	// Optional per-item notes (e.g. decoration requests), keyed by item id.
	$notes_json = isset( $_POST['kk_item_notes_json'] ) ? wp_unslash( $_POST['kk_item_notes_json'] ) : '';
	$item_notes = json_decode( $notes_json, true );
	if ( ! is_array( $item_notes ) ) {
		$item_notes = array();
	}

	if ( '' === $name || ! is_email( $email ) || ! is_array( $cart ) || empty( $cart ) ) {
		wp_safe_redirect( add_query_arg( 'order_status', 'error', $back ) . '#order-status' );
		exit;
	}

	// Rebuild lines from the menu data — the single source of truth for prices.
	$lines = array();
	$total = 0.0;
	foreach ( kk_menu() as $cat_key => $cat ) {
		foreach ( $cat['items'] as $item ) {
			$id = kk_item_id( $cat_key, $item['name'] );
			if ( empty( $cart[ $id ] ) ) {
				continue;
			}
			$qty = min( 999, max( 1, (int) $cart[ $id ] ) );
			$price = kk_item_price_num( $item['price'] );
			$line  = $qty * $price;
			$total += $line;
			$line_text = sprintf(
				'%d x %s%s @ %s = $%s',
				$qty,
				$item['name'],
				! empty( $item['note'] ) ? ' (' . $item['note'] . ')' : '',
				$item['price'],
				number_format( $line, 2 )
			);
			// Attach the customer's decoration/customization request, if any.
			if ( ! empty( $item['custom_note'] ) && ! empty( $item_notes[ $id ] ) && is_string( $item_notes[ $id ] ) ) {
				$item_note = sanitize_textarea_field( mb_substr( $item_notes[ $id ], 0, 1000 ) );
				if ( '' !== $item_note ) {
					$line_text .= "\n    Customer request: " . $item_note;
				}
			}
			$lines[] = $line_text;
		}
	}

	if ( empty( $lines ) ) {
		wp_safe_redirect( add_query_arg( 'order_status', 'error', $back ) . '#order-status' );
		exit;
	}

	$config = kk_config();

	$body  = "New order request from the website:\n\n";
	$body .= 'Name: ' . $name . "\n";
	$body .= 'Email: ' . $email . "\n";
	$body .= 'Phone: ' . ( $phone ? $phone : 'not given' ) . "\n";
	$body .= 'Date needed: ' . ( $date ? $date : 'not given' ) . "\n\n";
	$body .= "Order:\n" . implode( "\n", $lines ) . "\n\n";
	$body .= 'Estimated total (per-item pricing): $' . number_format( $total, 2 ) . "\n";
	$body .= "Note: dozen pricing is NOT applied above; adjust when confirming.\n";
	if ( $note ) {
		$body .= "\nCustomer note:\n" . $note . "\n";
	}

	$sent = wp_mail(
		$config['email'],
		'Order request from ' . $name,
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	$flag = $sent ? 'sent' : 'error';
	wp_safe_redirect( add_query_arg( 'order_status', $flag, $back ) . '#order-status' );
	exit;
}
add_action( 'admin_post_kk_order', 'kk_handle_order' );
add_action( 'admin_post_nopriv_kk_order', 'kk_handle_order' );
