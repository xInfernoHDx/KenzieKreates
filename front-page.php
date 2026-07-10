<?php
/**
 * Front page: hero, how ordering works, featured treats, story teaser,
 * custom orders callout, contact strip.
 */

$kk = kk_config();
get_header();
?>

<section class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<h1>Homemade treats, baked just for your table</h1>
			<p class="hero-lede">Cookies, cake pops, brownies, and chocolate covered delights from our Florida home kitchen, baked fresh for every order.</p>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">See the Menu</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Order Treats</a>
		</div>
		<div class="hero-photo photo-frame">
			<?php kk_stock_img( 'hero-bakes', 'A spread of homemade baked treats from Kenzy Kreates', true ); ?>
		</div>
	</div>
</section>

<section class="section section--tint">
	<div class="container">
		<div class="section-heading reveal">
			<h2>How ordering works</h2>
			<p>Three easy steps between you and something sweet.</p>
		</div>
		<div class="steps">
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#9825;</div>
				<h3>Pick your treats</h3>
				<p>Browse the menu, or dream up something custom for your celebration.</p>
			</div>
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#9993;</div>
				<h3>We confirm together</h3>
				<p>Send your request and we confirm the details, date, and price with you directly.</p>
			</div>
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#8962;</div>
				<h3>Pickup day</h3>
				<p>Your order is baked fresh and ready right on time. We arrange pickup when we confirm.</p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-heading reveal">
			<h2>Favorites from the kitchen</h2>
			<p>A few of the treats our customers ask for again and again.</p>
		</div>
		<div class="treats-grid">
			<?php foreach ( kk_featured_items( 6 ) as $item ) : ?>
				<a class="treat-card reveal" href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">
					<?php kk_stock_img( $item['image'], esc_attr( $item['name'] ) ); ?>
					<div class="treat-card-body">
						<span class="treat-cat"><?php echo esc_html( $item['category'] ); ?></span>
						<h3><?php echo esc_html( $item['name'] ); ?></h3>
						<span class="treat-price"><?php echo esc_html( $item['price'] . ' ' . $item['per'] ); ?><?php echo ! empty( $item['note'] ) ? esc_html( ' (' . $item['note'] . ')' ) : ''; ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--tint">
	<div class="container split">
		<div class="split-photo photo-frame reveal">
			<?php kk_stock_img( 'baker-hands', 'Hands at work baking in a home kitchen' ); ?>
		</div>
		<div class="reveal">
			<h2>Baked by Adam &amp; McKenzie</h2>
			<p>Kenzy Kreates started in our own kitchen, baking for the people we love. Now we bake for your birthdays, weddings, and everyday sweet cravings too, one made-to-order batch at a time.</p>
			<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Read Our Story</a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="callout reveal">
			<h2>Planning a celebration?</h2>
			<p>We love creating custom orders for birthdays, weddings, and special events. Tell us your vision and we will make it a little sweeter.</p>
			<p class="contact-strip">
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Order Treats</a>
			</p>
			<p class="contact-strip">
				<a class="contact-item" href="<?php echo esc_url( $kk['phone_href'] ); ?>">Call <?php echo esc_html( $kk['phone'] ); ?></a>
				<a class="contact-item" href="mailto:<?php echo esc_attr( $kk['email'] ); ?>"><?php echo esc_html( $kk['email'] ); ?></a>
			</p>
		</div>
	</div>
</section>

<?php get_footer(); ?>
