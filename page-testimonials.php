<?php


$h1_main_title = get_post_meta(get_the_ID(), 'h1_main_title', true);
$h1_sub_title = get_post_meta(get_the_ID(), 'h1_sub_title', true);

/*
 * Template Name: Testimonials Page
 * Description: A page template to display testimonials using ACF fields.
 */

get_header();

// Render Elementor content area
if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        the_content(); 
    endwhile;
endif;

// fetch testimonials
$args = array(
    'post_type'      => 'testimonial',
    'posts_per_page' => -1, // Display all testimonials
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$testimonials_query = new WP_Query( $args );

?>



<section id="testimonials-section" class="testimonials-container">






	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<div class="page-header">
			<?php if ( function_exists('yoast_breadcrumb') && !is_front_page() ) : ?>
				<?= yoast_breadcrumb( '<p id="breadcrumbs">','</p>' ); ?>
  			<?php endif; ?>

			<?php if(!is_front_page()) : ?>
				<?= '<h1 class="entry-title">' . esc_html($h1_main_title) . ' <span class="entry-title-subtitle">' . esc_html($h1_sub_title) . '</span></h1>'; ?>
			
			<?php endif; ?>

            <?php if ( is_single() && get_post_type() === 'post' ) : ?>
    			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php endif; ?>

		</div>
	<?php endif; ?>
    <div class="container" style="max-width: 1140px; margin: 0 auto;">
        <div class="testimonials-grid">
            <?php
            if ( $testimonials_query->have_posts() ) :
                while ( $testimonials_query->have_posts() ) : $testimonials_query->the_post();
                    // $testimonial_content = get_field( 'testimonial_content' ); // Textarea or WYSIWYG field
                    $description = get_field( 'description' ); // Text field
                    $testimonial_video = get_field( 'testimonial_video' ); // Image field (returns array or URL based on settings)
                    $rating = get_field( 'rating' ); // Number or select field (e.g., 1-5)
                    ?>
                    <div class="testimonial-item card card-1">
                        <!-- <?php if ( $testimonial_content ) : ?>
                            <div class="testimonial-content"><?php echo wp_kses_post( $testimonial_content ); ?></div>
                        <?php endif; ?> -->




                        <!-- fix this -->
                        
                        <div class="testimonial-img">
                            <?php if(has_post_thumbnail()){the_post_thumbnail( 'sm-square',array('alt' => get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true) ) );}
                            else { ?><img width="120" height="120" src="https://lh5.ggpht.com/-CFYUaGYh6Y8/AAAAAAAAAAI/AAAAAAAAAAA/MWWT48ek100/s128-c0x00000000-cc-rp-mo/photo.jpg" class="attachment-sm-square size-sm-square wp-post-image" alt="<?php the_title(); ?>" /><?php } ?>
                                <h3><?php the_title(); ?></h3>
                        </div>



<!-- fix this -->

                        <?php if ( $description ) : ?>
                        
                            <img src="/wp-content/uploads/2025/07/g-logo.png" alt="Google Logo" class="g-logo" />
                          <div class="google-stars">★★★★★</div>
                          <p>5 out of 5 stars</p>









<!-- fix this -->

                            
                            <p class="testimonial-description"><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                        <?php if ( $testimonial_video ) : 
                            // Check if image is returned as array or URL
                            $testimonial_video = is_array( $testimonial_video ) ? $testimonial_video['url'] : $testimonial_video;
                            ?>
                            <!-- <div class="testimonial-image">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" />
                            </div> -->
                        <?php endif; ?>
                        <!-- <?php if ( $rating ) : ?>
                            <div class="testimonial-rating">
                                <?php echo str_repeat( '★', esc_html( $rating ) ) . str_repeat( '☆', 5 - esc_html( $rating ) ); ?>
                            </div>
                        <?php endif; ?> -->
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <p>No testimonials found.</p>
                <?php
            endif;
            ?>
        </div>
        <a class="elementor-cta__button elementor-button elementor-size-"href="<?= do_shortcode('[client_info field="google_review_url"]'); ?>" class="btn solid google" target="_blank" rel="noopener" style="margin-bottom: 2rem; background: #4285F4;  box-shadow: none !important">Leave a Google Review</a>
    </div>

    <style>
  .google-stars {
    color: #fbbc04; /* Google's yellow/gold star color */
    font-size: 24px;
    letter-spacing: 2px;
  }
  .testimonial-img {
    display: flex;
    align-items: center;
}

.testimonial-item {
    max-width: 800px;
}
.testimonial-item.card.card-1 {
    padding: 1rem;
}
#testimonials-section > div.container > div > div > div.testimonial-img > img {
    margin-right: 8px;
    margin-bottom: 8px;
}
.card {
  background: #fff;
  border-radius: 2px;
  display: inline-block;
  margin: 1rem;
  position: relative;
}
#testimonials-section > div.container > a:hover {
    background: #73a8ff !important;
}
p.testimonial-description {
    margin: 0 !important;
}
.card-1 {
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
  transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}
#testimonials-section > div.container > div > div:hover {
    transform: scale(1.05,1.05);
    box-shadow: rgba(0, 0, 0, 0.16) -3px 5px 14px;
}
</style>    
</section>

<?php
get_footer();



// include google image in master env uploads folder
//include default imaage if no user image is uploaded