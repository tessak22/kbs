<?php
/**
 * Posts shortcode item template
 *
 * Default post item template
 * Consist of:
 * 		image,
 * 		title,
 * 		excerpt,
 * 		meta:date+comments+permalink
 *
 * @package     WebMan Amplifier
 * @subpackage  Shortcodes
 *
 * @uses        array $helper  Contains shortcode $atts array plus additional helper variables.
 *
 * @since    1.0
 * @version  1.2.7
 */



$link_output = array( '', '' );

if ( $helper['link'] ) {
	$link_output = array( '<a' . $helper['link'] . wm_schema_org( 'bookmark' ) . '>', '</a>' );
}
?>

<article class="<?php echo $helper['item_class']; ?>"<?php echo wm_schema_org( 'article' ); ?>>

	<?php
	if ( has_post_thumbnail( $helper['post_id'] ) ) {
		echo '<div class="wm-posts-element wm-html-element image image-container scale-rotate"' . wm_schema_org( 'image' ) . '>';

			echo $link_output[0];

			the_post_thumbnail( $helper['image_size'], array( 'title' => esc_attr( get_the_title( get_post_thumbnail_id( $helper['post_id'] ) ) ) ) );

			echo $link_output[1];

		echo '</div>';
	}
	?>

	<div class="wm-posts-element wm-html-element title"><?php
		echo '<' . $helper['atts']['heading_tag'] . wm_schema_org( 'name' ) . '>';

			echo $link_output[0];

			the_title();

			echo $link_output[1];

		echo '</' . $helper['atts']['heading_tag'] . '>';
	?></div>

	<?php
		if ( 0 < $helper['excerpt_length'] ) {
			echo '<div class="wm-posts-element wm-html-element content"' . wm_schema_org( 'description' ) . '>' . wp_trim_words( get_the_excerpt(), $helper['excerpt_length'], '&hellip;' ) . '</div>';
		}
	?>

	<?php
	echo wm_post_meta( apply_filters( 'wmhook_shortcode_posts_meta_info', array(
			'class'       => 'wm-posts-element wm-html-element meta entry-meta',
			'date_format' => trim( str_replace( array( 'Y' ), '', get_option( 'date_format' ) ), ', ' ),
			'meta'        => array( 'date', 'comments', 'permalink' )
		) ) );
	?>

</article>