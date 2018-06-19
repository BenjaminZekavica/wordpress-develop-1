<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>
<?php if ( twentyseventeen_is_amp() ) : ?>
	<amp-state id="navMenuToggledOn">
		<script type="application/json">false</script>
	</amp-state>
<?php endif; ?>

<nav
	id="site-navigation"
	class="main-navigation"
	role="navigation"
	aria-label="<?php esc_attr_e( 'Top Menu', 'twentyseventeen' ); ?>"
	<?php if ( twentyseventeen_is_amp() ) : ?>
		[class]="'main-navigation' + ( navMenuToggledOn ? ' toggled-on' : '' )"
	<?php endif; ?>
>
	<button
		class="menu-toggle"
		aria-controls="top-menu"
		aria-expanded="false"
		<?php if ( twentyseventeen_is_amp() ) : ?>
			on="tap:AMP.setState( { navMenuToggledOn: ! navMenuToggledOn } )"
			[class]="'menu-toggle' + ( navMenuToggledOn ? ' toggled-on' : '' )"
			[aria-expanded]="navMenuToggledOn ? 'true' : 'false'"
		<?php endif; ?>
	>
		<?php
		echo twentyseventeen_get_svg( array( 'icon' => 'bars' ) );
		echo twentyseventeen_get_svg( array( 'icon' => 'close' ) );
		_e( 'Menu', 'twentyseventeen' );
		?>
	</button>

	<?php wp_nav_menu( array(
		'theme_location' => 'top',
		'menu_id'        => 'top-menu',
	) ); ?>

	<?php if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && has_custom_header() ) : ?>
		<a href="#content" <?php if ( twentyseventeen_is_amp() ) : ?> on="tap:content.scrollTo(duration=600)" <?php endif; ?> class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'twentyseventeen' ); ?></span></a>
	<?php endif; ?>
</nav><!-- #site-navigation -->
