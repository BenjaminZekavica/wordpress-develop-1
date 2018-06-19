<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

$classes = array();

// This class will be replaced by 'js' in non-AMP responses via twentyseventeen_javascript_detection().
$classes[] = 'no-js';

/*
 * Fixed background images are supported by most browsers. Otherwise, in non-AMP responses this
 * class is added by supportsFixedBackground() in global.js
 */
if ( twentyseventeen_is_amp() ) {
	$classes[] = 'background-fixed';
}

/*
 * SVG is supported by 97.23%+ of browsers, so in AMP it is safe to assume SVG is available.
 * Otherwise, in non-AMP responses, the np-svg class will be replaced by supportsInlineSVG() in the global.js script.
 */
$classes[] = twentyseventeen_is_amp() ? 'svg' : 'no-svg';

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<?php get_template_part( 'template-parts/header/header', 'image' ); ?>

		<?php if ( has_nav_menu( 'top' ) ) : ?>
			<div class="navigation-top">
				<div class="wrap">
					<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .wrap -->

				<?php if ( twentyseventeen_is_amp() ) : ?>
					<amp-position-observer
						layout="nodisplay"
						intersection-ratios="1"
						on="exit:navigationTopShow.start;enter:navigationTopHide.start"
						<?php if ( is_admin_bar_showing() ) : ?>
							viewport-margins="32px 0"
						<?php endif; ?>
					></amp-position-observer>
				<?php endif; ?>
			</div><!-- .navigation-top -->

			<?php if ( twentyseventeen_is_amp() ) : ?>
				<div class="navigation-top site-navigation-fixed" aria-hidden="true">
					<div class="wrap">
						<nav class="main-navigation">
							<?php
							// The twentyseventeen_filter_top_menu_fixed() function filters the output of this wp_nav_menu() call.
							wp_nav_menu( array(
								'theme_location' => 'top',
								'menu_id'        => 'top-menu-fixed',
							) );
							?>
						</nav>
					</div><!-- .wrap -->
				</div><!-- #site-navigation-fixed -->
			<?php endif; ?>

		<?php endif; ?>

	</header><!-- #masthead -->

	<?php

	/*
	 * If a regular post or page, and not the front page, show the featured image.
	 * Using get_queried_object_id() here since the $post global may not be set before a call to the_post().
	 */
	if ( ( is_single() || ( is_page() && ! twentyseventeen_is_frontpage() ) ) && has_post_thumbnail( get_queried_object_id() ) ) :
		echo '<div class="single-featured-image-header">';
		echo get_the_post_thumbnail( get_queried_object_id(), 'twentyseventeen-featured-image' );
		echo '</div><!-- .single-featured-image-header -->';
	endif;
	?>

	<div class="site-content-contain">
		<div id="content" class="site-content">
