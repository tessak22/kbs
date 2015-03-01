<?php
/**
 * General index template
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  2014 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.2.7
 */



/**
 * Sidebar implementation
 *
 * Variables setup
 */


	$page_id = ( is_archive() && get_option( 'page_for_posts' ) ) ? ( get_option( 'page_for_posts' ) ) : ( null );
	$sidebar = wm_sidebar_setup( false, array( 'page_id' => $page_id ) );

	if ( ! function_exists( 'wma_amplifier' ) ) {
		$sidebar['class_main'] = ' eight pane';
	}



/**
 * Actual output
 */

get_header();

?>

<div class="wrap-inner">

	<div class="content-area site-content<?php echo $sidebar['class_main']; ?>">

		<?php get_template_part( 'loop', 'index' ); ?>

	</div>

	<?php
	/**
	 * Sidebar implementation
	 *
	 * Output
	 */

		if ( ! function_exists( 'wma_amplifier' ) ) {
			get_sidebar();
		} else {
			echo $sidebar['output'];
		}
	?>

</div>

<?php get_footer(); ?>