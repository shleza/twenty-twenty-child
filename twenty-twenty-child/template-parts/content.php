
<?php
/**
 * The default template for displaying content
 *
 * Used for both singular and index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage twenty-twenty-child
 * @since 1.0.0
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php

	get_template_part( 'template-parts/entry-header' );

	if ( ! is_search() ) {
		get_template_part( 'template-parts/featured-image' );
	}

	?>

	<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">

		<div class="entry-content">
		<?php 
			$pid = get_the_ID(); 
			$price = get_field('price', $pid);
			$sale_price = get_field('sale_price', $pid);
			
			if( get_post_type() == 'products' ) :
		?>
			<div>
				<ul>
					<li>
						<?=get_field('description', $pid)?>
						<?=get_field('youtube_video', $pid)?>
					</li>
					<?php if( $price ) : ?>
						<li>Price: <?=$price?></li>
					<?php endif; ?>
					<?php if(get_field('is_on_sale', $pid) == 'yes') : ?>
					<?php if( $sale_price ) : ?>
						<li>Sale price: <?=$sale_price?></li>
					<?php endif; ?>						
					<?php endif; ?>
				</ul>
					<?php 
					$images = get_field('image_gallery', $pid);
					if( $images ) :
					$images = explode(',', $images);
					$count = 0;
					
					if( $images ): ?>
						<?php foreach( $images as $image ): ?>
							<?php if( $count++ > 5 ) : break; endif; ?>
							<div class="gallery">
							  <a target="_blank" href="#">
								<img src="<?php echo esc_url(wp_get_attachment_url($image)); ?>" alt="<?php echo esc_attr(get_post_meta($image, '_wp_attachment_image_alt', TRUE)); ?>" >
							  </a>							  
							</div>
						<?php endforeach; 
						endif;
						$related_products = related_products($pid);	
						$i = 0;
						if($related_products) : ?>
							<h5>Related Products</h5>
							<?php foreach($related_products as $rp) : ?>
								<?php if( $i++ > 2 ) : break; endif; ?>
								<div class="row">
								  <div class="column">
									
								<div class="main_image">
								  <a target="_blank" href="<?=$rp['permalink']?>">
									<img src="<?php echo esc_url($rp['thumbnail']); ?>" >
								  </a>
								 </div>
								<h4><?=$rp['title']?></h4>
								  </div>
								</div>
							<?php endforeach; ?>
					<?php endif;
					endif; ?>		
					<?php elseif( get_post_field( 'post_name', get_post() ) == 'hello-world') : 
						$products = get_posts([
						  'post_type' => 'products',
						  'post_status' => 'publish',
						  'numberposts' => -1,
						   'order'    => 'ASC'
						]);
		
						foreach($products as $product) :
							$image = get_field('main_image', $product->ID);
						?>			
						
							<div class="row">
							  <div class="column">
								
							<div class="main_image">
							  <a target="_blank" href="<?=get_permalink($product->ID)?>">
								<img src="<?php echo esc_url($image['url']); ?>" >
							  </a>
							 </div>

							<h4><?=$product->post_title?></h4>
							<?php if(get_field('is_on_sale', $product->ID) == 'yes') : ?>
								<p><span class="badge red">On Sale</span></p>
							<?php endif; ?>
							  </div>
							</div>
					<?php endforeach;
					?>
		</div>
	</section>
</div>					
					
					<?php endif; ?>	
			</div>
			<?php
			if ( is_search() || ! is_singular() && 'summary' === get_theme_mod( 'blog_content', 'full' ) ) {
				the_excerpt();
			} else {
				the_content( __( 'Continue reading', 'twentytwenty' ) );
			}
			?>

		</div><!-- .entry-content -->

	</div><!-- .post-inner -->

	<div class="section-inner">
		<?php
		wp_link_pages(
			array(
				'before'      => '<nav class="post-nav-links bg-light-background" aria-label="' . esc_attr__( 'Page', 'twentytwenty' ) . '"><span class="label">' . __( 'Pages:', 'twentytwenty' ) . '</span>',
				'after'       => '</nav>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			)
		);

		edit_post_link();

		// Single bottom post meta.
		twentytwenty_the_post_meta( get_the_ID(), 'single-bottom' );

		if ( is_single() ) {

			get_template_part( 'template-parts/entry-author-bio' );

		}
		?>

	</div><!-- .section-inner -->

	<?php

	if ( is_single() ) {

		get_template_part( 'template-parts/navigation' );

	}

	/**
	 *  Output comments wrapper if it's a post, or if comments are open,
	 * or if there's a comment number – and check for password.
	 * */
	if ( ( is_single() || is_page() ) && ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
		?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

		<?php
	}
	?>

</article><!-- .post -->
