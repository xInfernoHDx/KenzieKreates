<?php
/**
 * FAQ page (/faq/): ordering, lead times, pickup, payment, allergens,
 * and the Florida cottage food disclosure.
 */

$kk = kk_config();
get_header();
?>

<section class="section">
	<div class="container">
		<div class="section-heading">
			<h1>Frequently Asked Questions</h1>
			<p>Everything you need to know about ordering from Kenzy Kreates. Not seeing your question? <a href="mailto:<?php echo esc_attr( $kk['email'] ); ?>">Email us</a> or call <a href="<?php echo esc_url( $kk['phone_href'] ); ?>"><?php echo esc_html( $kk['phone'] ); ?></a>.</p>
		</div>

		<div class="faq-list">
			<details class="faq-item">
				<summary>How do I place an order?</summary>
				<p>Three ways: send the <a href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">inquiry form</a> on our menu page, email <a href="mailto:<?php echo esc_attr( $kk['email'] ); ?>"><?php echo esc_html( $kk['email'] ); ?></a>, or call/text <a href="<?php echo esc_url( $kk['phone_href'] ); ?>"><?php echo esc_html( $kk['phone'] ); ?></a>. We confirm every order personally with the details, price, and pickup plan.</p>
			</details>

			<details class="faq-item">
				<summary>How far in advance should I order?</summary>
				<!-- OWNER INPUT: confirm exact lead times with Adam & McKenzie. -->
				<p>The more notice, the better, especially for decorated and custom orders. Reach out with your date and we will tell you right away whether we can make it happen. Holiday weeks fill up fastest.</p>
			</details>

			<details class="faq-item">
				<summary>Do you deliver or ship?</summary>
				<p>Orders are picked up locally. We arrange the pickup time and location with you when we confirm your order. As a Florida cottage food operation we sell directly to our customers and do not ship outside Florida.</p>
			</details>

			<details class="faq-item">
				<summary>How does payment work?</summary>
				<p>We confirm the total with you before anything goes in the oven. <?php echo esc_html( $kk['deposit_note'] ); ?> We will share current payment options when we confirm your order.</p>
			</details>

			<details class="faq-item">
				<summary>What about allergies?</summary>
				<p><?php echo esc_html( $kk['allergen_note'] ); ?></p>
			</details>

			<details class="faq-item">
				<summary>What is a cottage food operation?</summary>
				<p>It is Florida's way of letting home bakers sell directly to their neighbors. Under Florida law (Section 500.80, Florida Statutes), we bake in our home kitchen and sell straight to you, and every product label carries this statement:</p>
				<?php kk_disclosure(); ?>
			</details>
		</div>
	</div>
</section>

<?php get_footer(); ?>
