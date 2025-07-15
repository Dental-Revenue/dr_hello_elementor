<?php
/*
    Template Name: Sitemap
*/
get_header();

?>
<!-- main -->
<div role="main">
    <div class="row">
        <div class="columns twelve">
            <div class="main-content" style="max-width: 960px; margin: 0 auto; padding: 20px;">
            <?php if ( function_exists('yoast_breadcrumb') ) {
                    yoast_breadcrumb('<p id="breadcrumbs">','</p>');
                    } ?>
                <h1 class="page-header entry-title">Sitemap</h1>
                <ul class="sitemap"><?php echo wp_list_pages( array( 'echo'=>false, 'title_li' => false ) ); ?></ul>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>