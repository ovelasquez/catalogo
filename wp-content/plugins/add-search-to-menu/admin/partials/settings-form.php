<?php
/**
 * Represents the view for the plugin settings page.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user to configure plugin settings.
 *
 * @package IS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exits if accessed directly.
}
?>

<div class="wrap">

	<h1 class="wp-heading-inline"><?php esc_html_e( 'Ivory Search Settings', 'ivory-search' ); ?></h1>

	<hr class="wp-header-end">

	<?php do_action( 'is_admin_notices' ); ?>

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<form id="ivory_search_options" action="options.php" method="post">
			<div id="postbox-container-1" class="postbox-container">
				<?php if ( current_user_can( 'is_edit_search_form' ) ) : ?>
				<div id="submitdiv" class="postbox">
					<h3><?php echo esc_html( __( 'Status', 'ivory-search' ) ); ?></h3>
					<div class="inside">
						<div class="submitbox" id="submitpost">
							<div id="major-publishing-actions">
								<div id="publishing-action">
									<span class="spinner"></span>
									<?php submit_button( 'Save', 'primary', 'ivory_search_options_submit', false ); ?>
								</div>
								<div class="clear"></div>
							</div><!-- #major-publishing-actions -->
						</div><!-- #submitpost -->
					</div>
				</div><!-- #submitdiv -->
				<?php endif; ?>

				<div id="informationdiv" class="postbox">
					<h3><?php echo esc_html( __( 'Information', 'ivory-search' ) ); ?></h3>
					<div class="inside">
						<ul>
							<li><a href="https://ivorysearch.com/documentation/" target="_blank"><?php _e( 'Docs', 'ivory-search' ); ?></a></li>
							<li><a href="https://ivorysearch.com/support/" target="_blank"><?php _e( 'Support', 'ivory-search' ); ?></a></li>
							<li><a href="https://ivorysearch.com/contact/" target="_blank"><?php _e( 'Contact', 'ivory-search' ); ?></a></li>
							<li><a href="https://wordpress.org/support/plugin/add-search-to-menu/reviews/?filter=5#new-post" target="_blank"><?php _e( 'Give us a rating', 'ivory-search' ); ?></a></li>
						</ul>
					</div>
				</div><!-- #informationdiv -->
			</div><!-- #postbox-container-1 -->

			<div id="postbox-container-2" class="postbox-container">
			<div id="search-form-editor">
			<div class="keyboard-interaction"><?php
				echo sprintf(
					/* translators: 1: ◀ ▶ dashicon, 2: screen reader text for the dashicon */
					esc_html( __( '%1$s %2$s keys switch panels', 'ivory-search' ) ),
					'<span class="dashicons dashicons-leftright" aria-hidden="true"></span>',
					sprintf(
						'<span class="screen-reader-text">%s</span>',
						/* translators: screen reader text */
						esc_html( __( '(left and right arrow)', 'ivory-search' ) )
					)
				);
			?></div>

			<?php
				settings_fields( 'ivory_search' );
				$activetab = isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : '0';
				echo '<input type="hidden" id="active-tab" name="active-tab" value="'. $activetab .'" />';

				echo '<ul id="search-form-editor-tabs">';

				$panels = array(
						'search-to-menu' => array(
								'search-to-menu',
								'Search To Menu',
						),
						'settings' => array(
								'settings',
								'Settings',
						)
				);

				foreach ( $panels as $id => $panel ) {
					echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
						esc_attr( $panel[0] ), esc_html( $panel[1] ) );
				}

				echo '</ul>';
					echo '<div class="search-form-editor-panel" id="search-to-menu">';
				do_settings_sections( 'ivory_search' );
				echo '</div>';
			?>
			</div><!-- #search-form-editor -->

			<?php if ( current_user_can( 'is_edit_search_form' ) ) :
				submit_button( 'Save', 'primary', 'ivory_search_options_submit' );
			endif; ?>

			</div><!-- #postbox-container-2 -->
			</form>
		</div><!-- #post-body -->
		<br class="clear" />
		</div><!-- #poststuff -->

</div><!-- .wrap -->

<?php do_action( 'is_admin_footer' );