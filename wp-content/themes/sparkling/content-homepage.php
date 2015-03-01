<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-item-wrap">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
			 	<?php the_post_thumbnail( 'sparkling-featured', array( 'class' => 'single-featured' )); ?>
			</a>
		<div class="post-inner-content">
			<header class="entry-header page-header">

				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

			</header><!-- .entry-header -->

			<div class="entry-content">

					<?php the_content(); ?>

			</div><!-- .entry-content -->
		</div>
	</div>
</article><!-- #post-## -->
