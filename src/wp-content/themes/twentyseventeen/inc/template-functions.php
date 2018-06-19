<?php
/**
 * Additional features to allow styling of the templates
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function twentyseventeen_body_classes( $classes ) {
	// Add class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Add class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Add class if we're viewing the Customizer for easier styling of theme options.
	if ( is_customize_preview() ) {
		$classes[] = 'twentyseventeen-customizer';
	}

	// Add class on front page.
	if ( is_front_page() && 'posts' !== get_option( 'show_on_front' ) ) {
		$classes[] = 'twentyseventeen-front-page';
	}

	// Add a class if there is a custom header.
	if ( has_header_image() ) {
		$classes[] = 'has-header-image';
	}

	// Add class if sidebar is used.
	if ( is_active_sidebar( 'sidebar-1' ) && ! is_page() ) {
		$classes[] = 'has-sidebar';
	}

	// Add class for one or two column page layouts.
	if ( is_page() || is_archive() ) {
		if ( 'one-column' === get_theme_mod( 'page_layout' ) ) {
			$classes[] = 'page-one-column';
		} else {
			$classes[] = 'page-two-column';
		}
	}

	// Add class if the site title and tagline is hidden.
	if ( 'blank' === get_header_textcolor() ) {
		$classes[] = 'title-tagline-hidden';
	}

	// Get the colorscheme or the default if there isn't one.
	$colors = twentyseventeen_sanitize_colorscheme( get_theme_mod( 'colorscheme', 'light' ) );
	$classes[] = 'colors-' . $colors;

	/*
	 * Add has-header-video body class in PHP when AMP since the wp-custom-header-video-loaded
	 * event will not be available.
	 */
	if ( twentyseventeen_is_amp() && has_header_video() ) {
		$classes[] = 'has-header-video';
	}

	/*
	 * This logic is taken from definition of isFrontPage in jQuery code:
	 * https://github.com/WordPress/wordpress-develop/blob/0a81ac7e0471320167e90d25111e57d0e24806df/src/wp-content/themes/twentyseventeen/assets/js/global.js#L16
	 */
	if ( ( is_front_page() && 'posts' !== get_option( 'show_on_front' ) ) || ( is_home() && is_front_page() ) ) {
		$classes[] = 'is-front-page-layout';
	}

	return $classes;
}
add_filter( 'body_class', 'twentyseventeen_body_classes' );

/**
 * Filter the HTML output of a nav menu item to add the AMP dropdown button to reveal the sub-menu.
 *
 * This is only used for AMP since in JS it is added via initMainNavigation() in navigation.js.
 *
 * @param string $item_output   Nav menu item HTML.
 * @param object $item          Nav menu item.
 * @param int    $depth         Depth.
 * @param array  $nav_menu_args Args to wp_nav_menu().
 * @return string Modified nav menu item HTML.
 */
function twentyseventeen_add_nav_sub_menu_buttons( $item_output, $item, $depth, $nav_menu_args ) {
	if ( ! twentyseventeen_is_amp() ) {
		return $item_output;
	}

	unset( $depth );

	// Skip adding buttons to nav menu widgets for now.
	if ( empty( $nav_menu_args->theme_location ) ) {
		return $item_output;
	}

	if ( ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
		return $item_output;
	}
	static $nav_menu_item_number = 0;
	$nav_menu_item_number++;

	$expanded = in_array( 'current-menu-ancestor', $item->classes, true );

	$expanded_state_id = 'navMenuItemExpanded' . $nav_menu_item_number;

	// Create new state for managing storing the whether the sub-menu is expanded.
	$item_output .= sprintf(
		'<amp-state id="%s"><script type="application/json">%s</script></amp-state>',
		esc_attr( $expanded_state_id ),
		wp_json_encode( $expanded )
	);

	$dropdown_button  = '<button';
	$dropdown_class   = 'dropdown-toggle';
	$toggled_class    = 'toggled-on';
	$dropdown_button .= sprintf(
		' class="%s" [class]="%s"',
		esc_attr( $dropdown_class . ( $expanded ? " $toggled_class" : '' ) ),
		esc_attr( sprintf( "%s + ( $expanded_state_id ? %s : '' )", wp_json_encode( $dropdown_class ), wp_json_encode( " $toggled_class" ) ) )
	);
	$dropdown_button .= sprintf(
		' aria-expanded="%s" [aria-expanded]="%s"',
		esc_attr( wp_json_encode( $expanded ) ),
		esc_attr( "$expanded_state_id ? 'true' : 'false'" )
	);
	$dropdown_button .= sprintf(
		' on="%s"',
		esc_attr( "tap:AMP.setState( { $expanded_state_id: ! $expanded_state_id } )" )
	);
	$dropdown_button .= '>';

	$dropdown_button .= twentyseventeen_get_svg( array(
		'icon'     => 'angle-down',
		'fallback' => true,
	) );

	$dropdown_button .= sprintf(
		'<span class="screen-reader-text" [text]="%s">%s</span>',
		esc_attr( sprintf( "$expanded_state_id ? %s : %s", wp_json_encode( __( 'collapse child menu', 'twentyseventeen' ) ), wp_json_encode( __( 'expand child menu', 'twentyseventeen' ) ) ) ),
		esc_html( $expanded ? __( 'collapse child menu', 'twentyseventeen' ) : __( 'expand child menu', 'twentyseventeen' ) )
	);

	$dropdown_button .= '</button>';

	$item_output .= $dropdown_button;
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'twentyseventeen_add_nav_sub_menu_buttons', 10, 4 );

/**
 * Set quotes icon.
 *
 * Formerly this was done in global.js via setQuotesIcon().
 *
 * @param string $content Content.
 * @return string Updated content.
 */
function twentyseventeen_set_quotes_icon( $content ) {
	if ( 'quote' === get_post_format() ) {
		$icon    = twentyseventeen_get_svg( array( 'icon' => 'quote-right' ) );
		$content = preg_replace( '#(<blockquote.*?>)#s', '$1' . $icon, $content );
	}
	return $content;
}
add_filter( 'the_content', 'twentyseventeen_set_quotes_icon' );

/**
 * Add sticky nav menu.
 *
 * This is implemented by copying the top nav menu and then giving it a fixed position outside of the viewport,
 * and then showing it at the top of the window as soon as the original nav begins to get scrolled out of view.
 * In order to improve accessibility, the nav copy gets aria-hidden=true and all of the links get tabindex=-1
 * to prevent the keyboard from focusing on elements off the screen; it is not necessary to focus on the elements
 * in the fixed nav menu because as soon as the original nav menu is focused then the window is scrolled to the
 * top anyway.
 *
 * @param string $nav_menu The HTML content for the navigation menu.
 * @param object $args     An object containing wp_nav_menu() arguments.
 * @return string Nav menu modified.
 */
function twentyseventeen_filter_top_menu_fixed( $nav_menu, $args ) {
	if ( 'top-menu-fixed' === $args->menu_id ) {
		$nav_menu = preg_replace( '/(?=\sid="top-menu-fixed")/', ' aria-hidden="true" ', $nav_menu );
		$nav_menu = preg_replace( '/(?=\shref=)/', ' tabindex="-1" ', $nav_menu );
	}
	return $nav_menu;
}
add_filter( 'wp_nav_menu', 'twentyseventeen_filter_top_menu_fixed', 10, 2 );

/**
 * Output the amp-animation required for implementing a fixed nav menu in AMP.
 *
 * AMP currently requires amp-animation elements to be direct descendants of the body
 * and this is why it is added to the wp_footer action.
 */
function twentyseventeen_add_fixed_nav_menu_amp_animation() {
	if ( ! twentyseventeen_is_amp() ) {
		return;
	}
	$animations = array(
		'navigationTopShow' => array(
			'duration'   => 0,
			'fill'       => 'both',
			'animations' => array(
				'selector'  => '.navigation-top.site-navigation-fixed',
				'media'     => '(min-width: 48em)',
				'keyframes' => array(
					'opacity'   => 1.0,
					'transform' => 'translateY( 0 )',
				),
			),
		),
		'navigationTopHide' => array(
			'duration'   => 0,
			'fill'       => 'both',
			'animations' => array(
				'selector'  => '.navigation-top.site-navigation-fixed',
				'media'     => '(min-width: 48em)',
				'keyframes' => array(
					'opacity'   => 0.0,
					'transform' => 'translateY( -72px )',
				),
			),
		),
	);
	?>
	<?php foreach ( $animations as $animation_id => $animation ) : ?>
		<amp-animation id="<?php echo esc_attr( $animation_id ); ?>" layout="nodisplay">
			<script type="application/json"><?php echo wp_json_encode( $animation ); ?></script>
		</amp-animation>
	<?php endforeach; ?>
	<?php
}
add_action( 'wp_footer', 'twentyseventeen_add_fixed_nav_menu_amp_animation' );

/**
 * Count our number of active panels.
 *
 * Primarily used to see if we have any panels active, duh.
 */
function twentyseventeen_panel_count() {

	$panel_count = 0;

	/**
	 * Filter number of front page sections in Twenty Seventeen.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $num_sections Number of front page sections.
	 */
	$num_sections = apply_filters( 'twentyseventeen_front_page_sections', 4 );

	// Create a setting and control for each of the sections available in the theme.
	for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {
		if ( get_theme_mod( 'panel_' . $i ) ) {
			$panel_count++;
		}
	}

	return $panel_count;
}

/**
 * Checks to see if we're on the homepage or not.
 */
function twentyseventeen_is_frontpage() {
	return ( is_front_page() && ! is_home() );
}

/**
 * Determine whether this is an AMP response.
 *
 * Note that this must only be called after the parse_query action.
 *
 * @return bool Is AMP endpoint (and AMP plugin is active).
 */
function twentyseventeen_is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}
