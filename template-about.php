<?php
/**
 * About page (/about/): Adam & McKenzie's story.
 *
 * OWNER INPUT NEEDED: the story copy below is intentionally general and
 * factual (home kitchen, Florida, made to order). Replace the marked
 * paragraphs with Adam & McKenzie's real story when they provide it.
 */

$kk = kk_config();
get_header();
?>

<section class="section">
	<div class="container split--flip split">
		<div>
			<h1>The two of us, one sweet idea</h1>
			<p class="hero-lede">Kenzy Kreates is the home bakery of <?php echo esc_html( $kk['owners'] ); ?>, baking treats and sweets from our Florida kitchen.</p>

			<!-- OWNER INPUT: replace this paragraph with how Kenzy Kreates actually started. -->
			<p>What began as baking for family and friends grew into something we get to share with our whole community. Every cookie, cake pop, and brownie that leaves our kitchen is baked to order, decorated by hand, and packed up like it is headed to someone we love. Because it is.</p>

			<!-- OWNER INPUT: a paragraph about Adam & McKenzie themselves (how you met baking, who decorates, who does the dishes...). -->
			<p>We keep our menu small on purpose so that everything is fresh, and we love a challenge. If you can picture it on your party table, chances are we can bake it.</p>

			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Order Treats</a>
		</div>
		<div class="split-photo photo-frame">
			<?php kk_stock_img( 'baker-hands', 'Hands kneading dough in the Kenzy Kreates home kitchen', true ); ?>
		</div>
	</div>
</section>

<section class="section section--tint">
	<div class="container">
		<div class="section-heading">
			<h2>What made to order means to us</h2>
		</div>
		<div class="steps">
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#10047;</div>
				<h3>Baked fresh, never ahead</h3>
				<p>Your order goes in the oven for your date, not into a freezer weeks before.</p>
			</div>
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#9998;</div>
				<h3>Decorated to your theme</h3>
				<p>Colors, characters, and details matched to your celebration.</p>
			</div>
			<div class="step reveal">
				<div class="step-icon" aria-hidden="true">&#8962;</div>
				<h3>A real home kitchen</h3>
				<p>We are a Florida cottage food operation. You are buying straight from the bakers.</p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="callout">
			<h2>Come say hi</h2>
			<p>Questions, cravings, or a celebration on the calendar? We would love to hear from you.</p>
			<p class="contact-strip">
				<a class="contact-item" href="<?php echo esc_url( $kk['phone_href'] ); ?>">Call <?php echo esc_html( $kk['phone'] ); ?></a>
				<a class="contact-item" href="mailto:<?php echo esc_attr( $kk['email'] ); ?>"><?php echo esc_html( $kk['email'] ); ?></a>
			</p>
		</div>
	</div>
</section>

<?php get_footer(); ?>
