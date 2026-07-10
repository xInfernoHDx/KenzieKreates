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
			<h1>Meet Kenzie, the baker behind Kenzy Kreates</h1>
			<p class="hero-lede">Kenzy Kreates is Kenzie's home bakery, baking treats and sweets from our Florida kitchen with Adam's help.</p>

			<!-- OWNER INPUT: replace this paragraph with how Kenzy Kreates actually started.
			     If Kenzie wants to share her young-baker story here (how she got started,
			     being a student entrepreneur), it is a story customers love. We left her
			     age out by default for privacy; adding it is the owners' call. -->
			<p>What began as Kenzie baking for family and friends grew into something she gets to share with the whole community. Every cookie, cake pop, and brownie that leaves the kitchen is baked to order, decorated by hand, and packed up like it is headed to someone she loves. Because it is.</p>

			<!-- OWNER INPUT: a paragraph about Kenzie & Adam (who decorates, who does the dishes...). -->
			<p>We keep our menu small on purpose so that everything is fresh, and Kenzie loves a challenge. If you can picture it on your party table, chances are she can bake it.</p>

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
				<p>We are a Florida cottage food operation. You are buying straight from the baker.</p>
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
