<?php
/**
 * Menu page (/menu/): full menu from inc/menu-data.php + custom order form.
 */

$kk           = kk_config();
$kk_menu      = kk_menu();
$inquiry      = get_query_var( 'inquiry' );
$order_status = get_query_var( 'order_status' );

get_header();
?>

<div id="order-status" class="container">
	<?php if ( 'sent' === $order_status ) : ?>
		<div class="form-msg form-msg--success order-msg--success" role="status" style="margin-top: 1.5rem;">Your order request is in! Kenzie will confirm the details, final total, and pickup with you within a day or two.</div>
	<?php elseif ( 'error' === $order_status ) : ?>
		<div class="form-msg form-msg--error" role="alert" style="margin-top: 1.5rem;">Something went wrong sending your order. Please try again, or call <?php echo esc_html( $kk['phone'] ); ?> / email <?php echo esc_html( $kk['email'] ); ?>.</div>
	<?php endif; ?>
</div>

<section class="section section--flush-bottom">
	<div class="container">
		<div class="section-heading">
			<h1>Our Delicious Menu</h1>
			<p>Indulge in a delightful array of handcrafted baked goods and sweet treats. Perfect for any occasion, with a variety of flavors and options to satisfy your cravings.</p>
			<p class="menu-cat-tagline"><?php echo esc_html( $kk['allergen_note'] ); ?></p>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<?php foreach ( $kk_menu as $cat_key => $cat ) : ?>
			<div class="menu-cat" id="<?php echo esc_attr( $cat_key ); ?>">
				<div class="menu-cat-head">
					<h2><?php echo esc_html( $cat['title'] ); ?></h2>
					<?php if ( ! empty( $cat['dozen'] ) ) : ?>
						<span class="dozen-chip">A dozen: <?php echo esc_html( $cat['dozen'] ); ?></span>
					<?php endif; ?>
				</div>
				<p class="menu-cat-tagline"><?php echo esc_html( $cat['tagline'] ); ?></p>

				<?php
				$photo_items = array();
				foreach ( $cat['items'] as $item ) {
					if ( ! empty( $item['image'] ) ) {
						$photo_items[] = $item;
					}
				}
				?>
				<?php if ( $photo_items ) : ?>
					<div class="menu-cat-photos">
						<?php foreach ( $photo_items as $item ) : ?>
							<figure class="menu-photo-card">
								<?php kk_stock_img( $item['image'], esc_attr( $item['name'] ) ); ?>
								<figcaption class="menu-photo-caption"><?php echo esc_html( $item['name'] ); ?></figcaption>
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<ul class="menu-items">
					<?php foreach ( $cat['items'] as $item ) : ?>
						<?php $orderable = empty( $item['sold_out'] ); ?>
						<li class="menu-item<?php echo $orderable ? ' menu-item--orderable' : ''; ?>"
							<?php if ( $orderable ) : ?>
							data-item-id="<?php echo esc_attr( kk_item_id( $cat_key, $item['name'] ) ); ?>"
							data-item-name="<?php echo esc_attr( $item['name'] . ( ! empty( $item['note'] ) ? ' (' . $item['note'] . ')' : '' ) ); ?>"
							data-item-price="<?php echo esc_attr( kk_item_price_num( $item['price'] ) ); ?>"
							<?php if ( ! empty( $cat['dozen'] ) ) : ?>
							data-item-dozen-price="<?php echo esc_attr( kk_item_price_num( $cat['dozen'] ) ); ?>"
							<?php endif; ?>
							<?php if ( ! empty( $item['custom_note'] ) ) : ?>
							data-item-note-prompt="<?php echo esc_attr( $item['custom_note'] ); ?>"
							<?php endif; ?>
							<?php endif; ?>
						>
							<span class="menu-item-name">
								<?php echo esc_html( $item['name'] ); ?>
								<?php if ( ! empty( $item['note'] ) ) : ?>
									<span class="menu-item-note">(<?php echo esc_html( $item['note'] ); ?>)</span>
								<?php endif; ?>
								<?php if ( ! empty( $item['sold_out'] ) ) : ?>
									<span class="sold-out-badge">Sold out</span>
								<?php endif; ?>
							</span>
							<span class="menu-item-dots" aria-hidden="true"></span>
							<span class="menu-item-price"><?php echo esc_html( $item['price'] ); ?> <small><?php echo esc_html( $item['per'] ); ?></small></span>
							<?php if ( $orderable ) : ?>
								<span class="menu-item-cart">
									<button type="button" class="qty-btn" data-cart-minus aria-label="Remove one <?php echo esc_attr( $item['name'] ); ?>">&#8722;</button>
									<input type="number" class="qty-input" data-cart-count inputmode="numeric" min="0" max="999" value="0" aria-label="Quantity of <?php echo esc_attr( $item['name'] ); ?>">
									<button type="button" class="qty-btn" data-cart-plus aria-label="Add one <?php echo esc_attr( $item['name'] ); ?>">+</button>
									<?php if ( ! empty( $cat['dozen'] ) ) : ?>
										<button type="button" class="dozen-add-btn" data-cart-dozen aria-label="Add a dozen <?php echo esc_attr( $item['name'] ); ?>">+ Dozen</button>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>

					<?php if ( ! empty( $cat['dozen'] ) ) : ?>
						<li class="menu-item menu-item--orderable menu-item--mix"
							data-item-id="<?php echo esc_attr( 'dozen-' . $cat_key ); ?>"
							data-item-name="<?php echo esc_attr( 'A Dozen ' . $cat['title'] . ' (mix & match)' ); ?>"
							data-item-price="<?php echo esc_attr( kk_item_price_num( $cat['dozen'] ) ); ?>"
							data-item-note-prompt="Which flavors would you like in your dozen?"
						>
							<span class="menu-item-name">
								Mix &amp; Match Dozen
								<span class="menu-item-note">(your choice of flavors)</span>
							</span>
							<span class="menu-item-dots" aria-hidden="true"></span>
							<span class="menu-item-price"><?php echo esc_html( $cat['dozen'] ); ?> <small>per dozen</small></span>
							<span class="menu-item-cart">
								<button type="button" class="qty-btn" data-cart-minus aria-label="Remove one mix and match dozen of <?php echo esc_attr( $cat['title'] ); ?>">&#8722;</button>
								<input type="number" class="qty-input" data-cart-count inputmode="numeric" min="0" max="999" value="0" aria-label="Number of mix and match dozens of <?php echo esc_attr( $cat['title'] ); ?>">
								<button type="button" class="qty-btn" data-cart-plus aria-label="Add one mix and match dozen of <?php echo esc_attr( $cat['title'] ); ?>">+</button>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="section section--tint" id="custom-orders">
	<div class="container">
		<div class="section-heading">
			<h2>Custom Orders &amp; Inquiries</h2>
			<p>Looking for something specific? We love creating custom orders for birthdays, weddings, and special events. Tell us your vision below, or reach us directly, and we will send you a personalized quote.</p>
			<p class="contact-strip contact-strip--left">
				<a class="contact-item" href="<?php echo esc_url( $kk['phone_href'] ); ?>">Call <?php echo esc_html( $kk['phone'] ); ?></a>
				<a class="contact-item" href="mailto:<?php echo esc_attr( $kk['email'] ); ?>"><?php echo esc_html( $kk['email'] ); ?></a>
			</p>
		</div>

		<?php if ( 'sent' === $inquiry ) : ?>
			<div class="form-msg form-msg--success" role="status">Thank you! Your inquiry is on its way. We will get back to you within a day or two to talk details.</div>
		<?php elseif ( 'error' === $inquiry ) : ?>
			<div class="form-msg form-msg--error" role="alert">Something went wrong sending your inquiry. Please try again, or email us directly at <?php echo esc_html( $kk['email'] ); ?>.</div>
		<?php endif; ?>

		<form class="inquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="kk_inquiry">
			<?php wp_nonce_field( 'kk_inquiry', 'kk_inquiry_nonce' ); ?>

			<p class="hp-field" aria-hidden="true">
				<label for="kk_website">Leave this field empty</label>
				<input type="text" id="kk_website" name="kk_website" tabindex="-1" autocomplete="off">
			</p>

			<div class="form-grid">
				<div class="form-field">
					<label for="kk_name">Your name *</label>
					<input type="text" id="kk_name" name="kk_name" required autocomplete="name">
				</div>
				<div class="form-field">
					<label for="kk_email">Email *</label>
					<input type="email" id="kk_email" name="kk_email" required autocomplete="email">
				</div>
				<div class="form-field">
					<label for="kk_phone">Phone</label>
					<input type="tel" id="kk_phone" name="kk_phone" autocomplete="tel">
				</div>
				<div class="form-field">
					<label for="kk_occasion">Occasion</label>
					<input type="text" id="kk_occasion" name="kk_occasion" placeholder="Birthday, wedding, just because...">
				</div>
				<div class="form-field">
					<label for="kk_date">When do you need it?</label>
					<input type="date" id="kk_date" name="kk_date">
				</div>
				<div class="form-field">
					<label for="kk_servings">How many people or pieces?</label>
					<input type="text" id="kk_servings" name="kk_servings" placeholder="e.g. 2 dozen, 25 guests">
				</div>
				<div class="form-field form-field--full">
					<label for="kk_budget">Budget (optional)</label>
					<input type="text" id="kk_budget" name="kk_budget" placeholder="A range is fine">
				</div>
				<div class="form-field form-field--full">
					<label for="kk_message">Tell us your vision *</label>
					<span class="hint">Colors, theme, flavors, anything you have in mind.</span>
					<textarea id="kk_message" name="kk_message" rows="5" required></textarea>
				</div>
			</div>

			<p class="form-note"><?php echo esc_html( $kk['deposit_note'] ); ?></p>
			<button type="submit" class="btn btn--primary">Send My Inquiry</button>
		</form>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php kk_disclosure(); ?>
	</div>
</section>

<div class="cart-bar" id="cartBar" hidden>
	<span class="cart-bar-summary" data-cart-summary>0 items &middot; $0.00</span>
	<button type="button" class="btn btn--primary" id="cartOpen">Review Order</button>
</div>

<div class="cart-overlay" id="cartOverlay" hidden></div>

<aside class="cart-panel" id="cartPanel" aria-label="Your order" hidden>
	<button type="button" class="cart-close" id="cartClose" aria-label="Close order panel">&times;</button>
	<h2>Your order</h2>
	<ul class="cart-lines" id="cartLines"></ul>
	<p class="cart-total-row">Estimated total <strong data-cart-total>$0.00</strong></p>
	<p class="cart-fineprint">Nothing is charged online. Dozen pricing is applied automatically when you order 12 or more of a treat. Kenzie confirms your final total and pickup details with you directly.</p>

	<form class="cart-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="cartForm">
		<input type="hidden" name="action" value="kk_order">
		<?php wp_nonce_field( 'kk_order', 'kk_order_nonce' ); ?>
		<input type="hidden" name="kk_cart_json" id="cartJson" value="">
		<input type="hidden" name="kk_item_notes_json" id="cartNotesJson" value="">

		<p class="hp-field" aria-hidden="true">
			<label for="kk_website_order">Leave this field empty</label>
			<input type="text" id="kk_website_order" name="kk_website" tabindex="-1" autocomplete="off">
		</p>

		<div class="form-field">
			<label for="order_name">Your name *</label>
			<input type="text" id="order_name" name="kk_name" required autocomplete="name">
		</div>
		<div class="form-field">
			<label for="order_email">Email *</label>
			<input type="email" id="order_email" name="kk_email" required autocomplete="email">
		</div>
		<div class="form-field">
			<label for="order_phone">Phone</label>
			<input type="tel" id="order_phone" name="kk_phone" autocomplete="tel">
		</div>
		<div class="form-field">
			<label for="order_date">When do you need it?</label>
			<input type="date" id="order_date" name="kk_date">
		</div>
		<div class="form-field">
			<label for="order_note">Anything else?</label>
			<textarea id="order_note" name="kk_note" rows="2"></textarea>
		</div>
		<button type="submit" class="btn btn--primary" id="cartSubmit">Send Order Request</button>
	</form>
</aside>

<?php get_footer(); ?>
