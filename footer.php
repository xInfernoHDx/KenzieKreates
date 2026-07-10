<?php
/**
 * Site footer: contact, quick links, cottage food disclosure, dynamic year.
 */

$kk = kk_config();
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div>
				<h3><?php echo esc_html( $kk['business_name'] ); ?></h3>
				<p>Homemade baked treats and sweets from <?php echo esc_html( $kk['owners'] ); ?>, made to order for <?php echo esc_html( $kk['service_area'] ); ?>.</p>
			</div>
			<div>
				<h3>Get in touch</h3>
				<ul>
					<li><a href="<?php echo esc_url( $kk['phone_href'] ); ?>"><?php echo esc_html( $kk['phone'] ); ?></a></li>
					<li><a href="mailto:<?php echo esc_attr( $kk['email'] ); ?>"><?php echo esc_html( $kk['email'] ); ?></a></li>
					<?php foreach ( $kk['social'] as $network => $url ) : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( ucfirst( $network ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h3>Explore</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">Our menu</a></li>
					<li><a href="<?php echo esc_url( home_url( '/menu/#custom-orders' ) ); ?>">Custom orders</a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Our story</a></li>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
				</ul>
			</div>
		</div>

		<div class="footer-disclosure">
			<p><?php echo esc_html( $kk['disclosure'] ); ?></p>
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $kk['business_name'] ); ?>. All rights reserved.</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
