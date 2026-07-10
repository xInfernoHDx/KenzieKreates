<?php
/**
 * Fallback template.
 */

get_header();
?>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<h1>Nothing here yet</h1>
			<p>Try the <a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>">menu</a> instead. It is much tastier.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
