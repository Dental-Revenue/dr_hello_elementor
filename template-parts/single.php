
<?php

$h1_main_title = get_post_meta(get_the_ID(), 'h1_main_title', true);
$h1_sub_title = get_post_meta(get_the_ID(), 'h1_sub_title', true);


/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

while ( have_posts() ) :
	the_post();
	?>

<main id="content" <?php post_class( 'site-main' ); ?>>


	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<div class="page-header">
			<?php if ( function_exists('yoast_breadcrumb') && !is_front_page() ) : ?>
				<?= yoast_breadcrumb( '<p id="breadcrumbs">','</p>' ); ?>
  			<?php endif; ?>

			<?php if(!is_front_page()) : ?>
				<?= '<h1 class="entry-title">' . esc_html($h1_main_title) . ' <span class="entry-title-subtitle">' . esc_html($h1_sub_title) . '</span></h1>'; ?>
			
			<?php endif; ?>

		</div>
	<?php endif; ?>

	<div class="page-content">
		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

		<?php if ( has_tag() ) : ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'hello-elementor' ), ', ', '</span>' ); ?>
		</div>
		<?php endif; ?>
	</div>

	<?php comments_template(); ?>

</main>

	<?php
endwhile;
