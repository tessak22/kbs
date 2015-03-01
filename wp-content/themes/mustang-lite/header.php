<?php
/**
 * Website header template
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  2014 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.2.1
 */





/**
 * HTML
 */

	wmhook_html_before();

?>

<!--[if lte IE 8]><html class="ie ie8 lie9 lie8 no-js"<?php echo wm_schema_org( 'html' ); ?> <?php language_attributes(); ?>><![endif]-->
<!--[if IE 9]><html class="ie ie9 lie9 no-js"<?php echo wm_schema_org( 'html' ); ?> <?php language_attributes(); ?>><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html class="no-js"<?php echo wm_schema_org( 'html' ); ?> <?php language_attributes(); ?>><!--<![endif]-->

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php

	/**
	 * HTML head
	 */

	wmhook_head_top();

	?>

	<title><?php wp_title( '' ); ?></title>

	<?php

	wmhook_head_bottom();

	wp_head();

	?>
</head>


<body id="top" <?php body_class(); ?>>

<?php

	/**
	 * Body
	 */

		wmhook_body_top();



	if ( ! apply_filters( 'wmhook_disable_header', false ) ) {

		/**
		 * Header
		 */

			wmhook_header_before();

			wmhook_header_top();

			wmhook_header();

			wmhook_header_bottom();

			wmhook_header_after();



		/**
		 * Content
		 */

			wmhook_content_before();

			wmhook_content_top();

	} // /wmhook_disable_header

?>