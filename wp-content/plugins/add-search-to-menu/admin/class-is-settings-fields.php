<?php
/**
 * Defines plugin settings fields.
 *
 * This class defines all code necessary to manage plugin settings fields.
 *
 * @package IS
 */

class IS_Settings_Fields {

	/**
	 * Stores plugin options.
	 */
	public $opt;

	/**
	 * Stores flag to know whether new plugin options are saved.
	 */
	public $ivory_search = false;

	/**
	 * Core singleton class
	 * @var self
	 */
	private static $_instance;

	/**
	 * Instantiates the plugin by setting up the core properties and loading
	 * all necessary dependencies and defining the hooks.
	 *
	 * The constructor uses internal functions to import all the
	 * plugin dependencies, and will leverage the Ivory_Search for
	 * registering the hooks and the callback functions used throughout the plugin.
	 */
	public function __construct( $is = null ) {

		$new_opt = get_option( 'ivory_search' );

		if ( ! empty( $new_opt ) ) {
			$this->ivory_search = true;
		}

		if ( null !== $is ) {
			$this->opt = $is;
		} else {
			$old_opt = (array)get_option( 'add_search_to_menu' );
			$this->opt = array_merge( $old_opt, (array)$new_opt );
		}
	}

	/**
	 * Gets the instance of this class.
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Registers plugin settings fields.
	 */
	function register_settings_fields(){
		add_settings_section( 'ivory_search_section', '',  array( $this, 'search_to_menu_section_desc' ), 'ivory_search' );

		add_settings_field( 'ivory_search_locations', __( 'Select Menu', 'ivory-search' ),  array( $this, 'menu_locations' ), 'ivory_search', 'ivory_search_section' );

		$menu_search_form = isset( $this->opt['menu_search_form'] ) ? $this->opt['menu_search_form'] : 0;
		if ( ! $menu_search_form ) {
			add_settings_field( 'ivory_search_posts', __( 'Post Types', 'ivory-search' ),  array( $this, 'menu_post_types' ), 'ivory_search', 'ivory_search_section' );
		}

		add_settings_field( 'ivory_search_form', __( 'Search Form', 'ivory-search' ),  array( $this, 'menu_search_form' ), 'ivory_search', 'ivory_search_section' );
		add_settings_field( 'ivory_search_style', __( 'Form Style', 'ivory-search' ),  array( $this, 'menu_form_style' ), 'ivory_search', 'ivory_search_section' );
		add_settings_field( 'ivory_search_title', __( 'Menu Title', 'ivory-search' ),  array( $this, 'menu_title' ), 'ivory_search', 'ivory_search_section' );
		add_settings_field( 'ivory_search_classes', __( 'Menu Classes', 'ivory-search' ),  array( $this, 'menu_classes' ), 'ivory_search', 'ivory_search_section' );
		add_settings_field( 'ivory_search_gcse', __( 'Google CSE', 'ivory-search' ),  array( $this, 'menu_google_cse' ), 'ivory_search', 'ivory_search_section' );
		add_settings_field( 'ivory_search_close_icon', __( 'Close Icon', 'ivory-search' ),  array( $this, 'menu_close_icon' ), 'ivory_search', 'ivory_search_section' );

		add_settings_section( 'ivory_search_settings', '',  array( $this, 'settings_section_desc'), 'ivory_search' );

		add_settings_field( 'ivory_search_header', __( 'Header', 'ivory-search' ),  array( $this, 'header' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_footer', __( 'Footer', 'ivory-search' ),  array( $this, 'footer' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_display_in_header', __( 'Mobile Display', 'ivory-search' ),  array( $this, 'menu_search_in_header' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_css', __( 'Custom CSS', 'ivory-search' ),  array( $this, 'custom_css' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_stopwords', __( 'Stopwords', 'ivory-search' ),  array( $this, 'stopwords' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_synonyms', __( 'Synonyms', 'ivory-search' ),  array( $this, 'synonyms' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'not_load_files', __( 'Not load files', 'ivory-search' ),  array( $this, 'plugin_files' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_disable', __( 'Disable', 'ivory-search' ),  array( $this, 'disable' ), 'ivory_search', 'ivory_search_settings' );
		add_settings_field( 'ivory_search_default', __( 'Default Search', 'ivory-search' ),  array( $this, 'default_search' ), 'ivory_search', 'ivory_search_settings' );

		register_setting( 'ivory_search', 'ivory_search' );
	}

	/**
	 * Displays Search To Menu section description text.
	 */
	function search_to_menu_section_desc(){
		_e( 'Use below options to display search in menu and configure it.', 'ivory-search' );
	}

	/**
	 * Displays Settings section description text.
	 */
	function settings_section_desc(){
		echo '</div>';
		echo '<div class="search-form-editor-panel" id="settings">';
		_e( 'Use below options to make sitewide changes in search.', 'ivory-search' );
	}

	/**
	 * Displays choose menu locations field.
	 */
	function menu_locations() {
		$html = '';
		$menus = get_registered_nav_menus();

		if ( ! empty( $menus ) ) {
			$check_value = '';
			foreach ( $menus as $location => $description ) {

				if ( $this->ivory_search ) {
					$check_value = isset( $this->opt['menus'][ $location ] ) ? $this->opt['menus'][ $location ] : 0;
				} else {
					$check_value = isset( $this->opt['add_search_to_menu_locations'][ $location ] ) ? $this->opt['add_search_to_menu_locations'][ $location ] : 0;
				}

				$html .= '<p><input type="checkbox" id="is_menus' . esc_attr( $location ) . '" name="ivory_search[menus][' . esc_attr( $location ) . ']" value="' . esc_attr( $location ) . '" ' . checked( $location, $check_value, false ) . '/>';
				$html .= '<label for="is_menus' . esc_attr( $location ) . '"> ' . esc_html( $description ) . '</label></p>';
			}
		} else {
			$html = __( 'No navigation menu registered on your site.', 'ivory-search' );
		}
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Select Menu', 'ivory-search' ); 
			$content = '<p>' . __( 'Select menu here where you want to display search form.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays post types field.
	 */
	function menu_post_types() {
		$html = '';
		$args = array( 'exclude_from_search' => false );

		$posts = get_post_types( $args );

		if ( ! empty( $posts ) ){

			foreach ( $posts as $key => $post ) {

				$check_value = ( isset( $this->opt['add_search_to_menu_posts'][$key] ) && ! $this->ivory_search ) ? $this->opt['add_search_to_menu_posts'][ $key ] : 0;
				$check_value = isset( $this->opt['menu_posts'][$key] ) ? $this->opt['menu_posts'][ $key ] : $check_value;
				$html .= '<p><input type="checkbox" id="is_menu_posts' . esc_attr( $key ) . '" name="ivory_search[menu_posts][' . esc_attr( $key ) . ']" value="' . esc_attr( $key ) . '" ' . checked( $key, $check_value, false ) . '/>';
				$html .= '<label for="is_menu_posts' . esc_attr( $key ) . '"> ' . ucfirst( esc_html( $post ) ) . '</label></p>';
			}
		} else {
			$html = __( 'No post types registered on your site.', 'ivory-search' );
		}
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Post Types', 'ivory-search' ); 
			$content = '<p>' . __( 'Select post types here that you want to make searchable.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}


	/**
	 * Displays menu search form field.
	 */
	function menu_search_form() {
		$html = '';

		$form_disable = is_fs()->is_plan( 'pro' ) ? false : true;
		if ( $form_disable ) {
			$html .= '<p><select disabled id="menu_search_form" name="ivory_search[menu_search_form]" >';
			$html .= '<option value="0">' . __( 'none', 'ivory-search' ) . '</option>';
			$html .= '</select>';
			$html .= IS_Admin::pro_link() . '</p>';
		} else {
			$args = array( 'numberposts' => -1, 'post_type' => 'is_search_form' );
			$posts = get_posts( $args );

			if ( ! empty( $posts ) ) {

				$check_value = isset( $this->opt['menu_search_form'] ) ? $this->opt['menu_search_form'] : 0;
				$html .= '<p><select id="menu_search_form" name="ivory_search[menu_search_form]" >';
				$html .= '<option value="0">' . __( 'none', 'ivory-search' ) . '</option>';
				foreach ( $posts as $post ) {
					$html .= '<option value="' . $post->ID . '"' . selected( $post->ID, $check_value, false ) . ' >' . $post->post_title . '</option>';
				}

				$html .= '</select>';
				if ( $check_value && get_post_type( $check_value ) ) {
					$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search', false ) ) . '&post='.$check_value.'&action=edit">  ' . esc_html__( "Edit", 'ivory-search' ) . '</a>';
				} else {
					$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search-new', false ) ) . '">  ' . esc_html__( "Create New", 'ivory-search' ) . '</a>';
				}
				$html .= '</p><label for="menu_search_form" style="font-size: 10px;">' . esc_html__( "It overwrites above Post Types option.", 'ivory-search' ) . '</label>';
			}
		}
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Search Form', 'ivory-search' ); 
			$content = '<p>' . __( 'Select search form that will control search performed using menu search.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}


	/**
	 * Displays form style field.
	 */
	function menu_form_style() {
		$styles = array(
			'default'	  => __( 'Default', 'ivory-search' ),
			'dropdown'	  => __( 'Dropdown', 'ivory-search' ),
			'sliding'	  => __( 'Sliding', 'ivory-search' ),
			'full-width-menu' => __( 'Full Width', 'ivory-search' ),
			'popup'		  => __( 'Popup', 'ivory-search' )
		);

		$popup_disable = is_fs()->is_plan( 'pro' ) ? false : true;

		if ( empty( $this->opt ) || ( ! isset( $this->opt['add_search_to_menu_style'] ) && ! isset( $this->opt['menu_style'] ) ) ) {
			$this->opt['menu_style'] = 'default';
			update_option( 'ivory_search', $this->opt );
		}

		$html = '';
		$check_value = isset( $this->opt['add_search_to_menu_style'] ) ? $this->opt['add_search_to_menu_style'] : 'default';
		$check_value = isset( $this->opt['menu_style'] ) ? $this->opt['menu_style'] : $check_value;

		foreach ( $styles as $key => $style ) {
			$html .= '<p><input type="radio" id="is_menu_style' . esc_attr( $key ) . '" name="ivory_search[menu_style]"';
			$html .= ( $popup_disable && 'popup' === $key ) ? ' disabled ' : '';
			$html .= 'name="ivory_search[menu_style]" value="' . esc_attr( $key ) . '" ' . checked( $key, $check_value, false ) . '/>';
			$html .= '<label for="is_menu_style' . esc_attr( $key ) . '"> ' . esc_html( $style ) . '</label>';
			if ( $popup_disable && 'popup' === $key ) {
				$html .= IS_Admin::pro_link();
			}
			$html .= '</p>';
		}
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Form Style', 'ivory-search' ); 
			$content = '<p>' . __( 'Select form style for the search form displayed in the menu.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays search menu title field.
	 */
	function menu_title() {
		$this->opt['add_search_to_menu_title'] = isset( $this->opt['add_search_to_menu_title'] ) ? $this->opt['add_search_to_menu_title'] : '';
		$this->opt['menu_title'] = isset( $this->opt['menu_title'] ) ? $this->opt['menu_title'] : $this->opt['add_search_to_menu_title'];
		$html = '<input type="text" class="large-text" id="is_menu_title" name="ivory_search[menu_title]" value="' . esc_attr( $this->opt['menu_title'] ) . '" />';
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Menu Title', 'ivory-search' ); 
			$content = '<p>' . __( 'Displays set menu title text in place of search icon displays in navigation menu.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays search menu classes field.
	 */
	function menu_classes() {
		$this->opt['add_search_to_menu_classes'] = isset( $this->opt['add_search_to_menu_classes'] ) ? $this->opt['add_search_to_menu_classes'] : '';
		$this->opt['menu_classes'] = isset( $this->opt['menu_classes'] ) ? $this->opt['menu_classes'] : $this->opt['add_search_to_menu_classes'];
		$html = '<input type="text" class="large-text" id="is_menu_classes" name="ivory_search[menu_classes]" value="' . esc_attr( $this->opt['menu_classes'] ) . '" />';
		$html .= '<br /><label for="is_menu_classes" style="font-size: 10px;">' . esc_html__( "Add classes seperated by space.", 'ivory-search' ) . '</label>';
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Menu Classes', 'ivory-search' ); 
			$content = '<p>' . __( 'Adds set classes in the search navigation menu item.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays google cse field.
	 */
	function menu_google_cse() {
		$this->opt['add_search_to_menu_gcse'] = isset( $this->opt['add_search_to_menu_gcse'] ) ? $this->opt['add_search_to_menu_gcse'] : '';
		$this->opt['menu_gcse'] = isset( $this->opt['menu_gcse'] ) ? $this->opt['menu_gcse'] : $this->opt['add_search_to_menu_gcse'];
		$html = '<input type="text" class="large-text" id="is_menu_gcse" name="ivory_search[menu_gcse]" value="' . esc_attr( $this->opt['menu_gcse'] ) . '" />';
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Google CSE', 'ivory-search' ); 
			$content = '<p>' . __( 'Add only Google Custom Search( CSE ) search form code in the above text box that will replace default search form.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays display in header field.
	 */
	function menu_search_in_header() {
		$check_value = isset( $this->opt['add_search_to_menu_display_in_header'] ) ? $this->opt['add_search_to_menu_display_in_header'] : 0;

		$check_string =  checked( 'add_search_to_menu_display_in_header', $check_value, false );

		if ( $this->ivory_search ) {
			$check_value = isset( $this->opt['header_menu_search'] ) ? $this->opt['header_menu_search'] : 0;
			$check_string =  checked( 'header_menu_search', $check_value, false );
		}

		$html = '<input type="checkbox" id="is_search_in_header" name="ivory_search[header_menu_search]" value="header_menu_search" ' . $check_string . ' />';
		$html .= '<label for="is_search_in_header"> ' . esc_html__( 'Display search form in header on mobile devices', 'ivory-search' ) . '</label>';
		$html .= '<br /><label for="is_search_in_header" style="font-size: 10px;margin: 5px 0 10px;display: inline-block;">' . esc_html__( "It does not work with caching as this functionality uses WordPress wp_is_mobile function.", 'ivory-search' ) . '</label>';

		$check_value = isset( $this->opt['astm_site_uses_cache'] ) ? $this->opt['astm_site_uses_cache'] : 0;

		$check_string =  checked( 'astm_site_uses_cache', $check_value, false );

		if ( $this->ivory_search ) {
			$check_value = isset( $this->opt['site_uses_cache'] ) ? $this->opt['site_uses_cache'] : 0;
			$check_string =  checked( 'site_uses_cache', $check_value, false );
		}

		$html .= '<br /><input type="checkbox" id="is_site_uses_cache" name="ivory_search[site_uses_cache]" value="site_uses_cache" ' . $check_string . ' />';
		$html .= '<label for="is_site_uses_cache"> ' . esc_html__( 'This site uses cache', 'ivory-search' ) . '</label>';
		$html .= '<br /><label for="is_site_uses_cache" style="font-size: 10px;">' . esc_html__( "Use this option to display search form in site header and hide it on desktop using CSS code.", 'ivory-search' ) . '</label>';
		echo $html;
	}

	/**
	 * Disables search functionality on whole site.
	 */
	function disable() {
		$check_value = isset( $this->opt['disable'] ) ? $this->opt['disable'] : 0;
		$disable =  checked( 1, $check_value, false );
		$html = '<input type="checkbox" id="is_disable" name="ivory_search[disable]" value="1" ' . $disable . ' />';
		$html .= '<label for="is_disable"> ' . esc_html__( 'Disable search functionality on whole site.', 'ivory-search' ) . '</label>';
		echo $html;
	}

	/**
	 * Controls default search functionality.
	 */
	function default_search() {
		$check_value = isset( $this->opt['default_search'] ) ? $this->opt['default_search'] : 0;
		$disable =  checked( 1, $check_value, false );
		$html = '<input type="checkbox" id="is_default_search" name="ivory_search[default_search]" value="1" ' . $disable . ' />';
		$html .= '<label for="is_default_search"> ' . esc_html__( 'Do not use default search form to control WordPress default search functionality.', 'ivory-search' ) . '</label>';
		echo $html;
	}

	/**
	 * Displays search form in site header.
	 */
	function header() {

		$html = '';
		$args = array( 'numberposts' => -1, 'post_type' => 'is_search_form' );

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {

			$check_value = isset( $this->opt['header_search'] ) ? $this->opt['header_search'] : 0;
			$html .= '<select id="is_header_search" name="ivory_search[header_search]" >';
			$html .= '<option value="0">' . __( 'none', 'ivory-search' ) . '</option>';
			foreach ( $posts as $post ) {
				$html .= '<option value="' . $post->ID . '"' . selected( $post->ID, $check_value, false ) . ' >' . $post->post_title . '</option>';
			}

			$html .= '</select>';
			if ( $check_value && get_post_type( $check_value ) ) {
				$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search', false ) ) . '&post='.$check_value.'&action=edit">  ' . esc_html__( "Edit", 'ivory-search' ) . '</a>';
			} else {
				$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search-new', false ) ) . '">  ' . esc_html__( "Create New", 'ivory-search' ) . '</a>';
			}
			$html .= '<br /><label for="is_header_search" style="font-size: 10px;">' . esc_html__( "Displays search form in site header using wp_head hook.", 'ivory-search' ) . '</label>';
		}
		echo $html;
	}

	/**
	 * Displays search form in site footer.
	 */
	function footer() {

		$html = '';
		$args = array( 'numberposts' => -1, 'post_type' => 'is_search_form' );

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {

			$check_value = isset( $this->opt['footer_search'] ) ? $this->opt['footer_search'] : 0;
			$html .= '<select id="is_footer_search" name="ivory_search[footer_search]" >';
			$html .= '<option value="0">' . __( 'none', 'ivory-search' ) . '</option>';
			foreach ( $posts as $post ) {
				$html .= '<option value="' . $post->ID . '"' . selected( $post->ID, $check_value, false ) . ' >' . $post->post_title . '</option>';
			}

			$html .= '</select>';
			if ( $check_value && get_post_type( $check_value ) ) {
				$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search', false ) ) . '&post='.$check_value.'&action=edit">  ' . esc_html__( "Edit", 'ivory-search' ) . '</a>';
			} else {
				$html .= '<a href="' . esc_url( menu_page_url( 'ivory-search-new', false ) ) . '">  ' . esc_html__( "Create New", 'ivory-search' ) . '</a>';
			}
			$html .= '<br /><label for="is_footer_search" style="font-size: 10px;">' . esc_html__( "Displays search form in site footer using wp_footer hook.", 'ivory-search' ) . '</label>';
		}
		echo $html;
	}

	/**
	 * Displays search form close icon field.
	 */
	function menu_close_icon() {
		$check_value = isset( $this->opt['add_search_to_menu_close_icon'] ) ? $this->opt['add_search_to_menu_close_icon'] : 0;

		$check_string =  checked( 'add_search_to_menu_close_icon', $check_value, false );

		if ( $this->ivory_search ) {
			$check_value = isset( $this->opt['menu_close_icon'] ) ? $this->opt['menu_close_icon'] : 0;
			$check_string =  checked( 'menu_close_icon', $check_value, false );
		}

		$html = '<input type="checkbox" id="menu_close_icon" name="ivory_search[menu_close_icon]" value="menu_close_icon" ' . $check_string . ' />';
		$html .= '<label for="menu_close_icon"> ' . esc_html__( 'Display Search Form Close Icon', 'ivory-search' ) . '</label>';
		echo $html;
	}

	/**
	 * Displays custom css field.
	 */
	function custom_css() {
		$this->opt['add_search_to_menu_css'] = isset( $this->opt['add_search_to_menu_css'] ) ? $this->opt['add_search_to_menu_css'] : '';
		$this->opt['custom_css'] = isset( $this->opt['custom_css'] ) ? $this->opt['custom_css'] : $this->opt['add_search_to_menu_css'];
		$html = '<textarea class="large-text" rows="4" id="custom_css" name="ivory_search[custom_css]" >' . esc_attr( $this->opt['custom_css'] ) . '</textarea>';
		$html .= '<br /><label for="custom_css" style="font-size: 10px;">' . esc_html__( "Add custom css code if any to style search form.", 'ivory-search' ) . '</label>';
		echo $html;
	}

	/**
	 * Displays stopwords field.
	 */
	function stopwords() {
		$this->opt['stopwords'] = isset( $this->opt['stopwords'] ) ? $this->opt['stopwords'] : '';
		$html = '<textarea class="large-text" rows="4" id="stopwords" name="ivory_search[stopwords]" >' . esc_attr( $this->opt['stopwords'] ) . '</textarea>';
		$html .= '<br /><label for="stopwords" style="font-size: 10px;">' . esc_html__( "Please separate words with commas.", 'ivory-search' ) . '</label>';
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Stopwords', 'ivory-search' ); 
			$content = '<p>' . __( 'Enter words here to add them to the list of stopwords. The stopwords will not be searched.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays synonyms field.
	 */
	function synonyms() {
		$this->opt['synonyms'] = isset( $this->opt['synonyms'] ) ? $this->opt['synonyms'] : '';
		$html = '<textarea class="large-text" rows="4" id="synonyms" name="ivory_search[synonyms]" >' . esc_attr( $this->opt['synonyms'] ) . '</textarea>';
		$html .= '<br /><label for="synonyms" style="font-size: 10px;">' . esc_html__( 'The format here is key = value;. Please separate every synonyms key = value pairs with semicolon.', 'ivory-search' ) . '</label>';
		$synonyms_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
		$check_value = isset( $this->opt['synonyms_and'] ) ? $this->opt['synonyms_and'] : 0;
		$disable =  checked( 1, $check_value, false );
		$html .= '<br /><br /><input type="checkbox" ' . $synonyms_disable . ' id="synonyms_and" name="ivory_search[synonyms_and]" value="1" ' . $disable . ' />';
		$html .= '<label for="synonyms_and"> ' . esc_html__( 'Disable synonyms for the search forms having AND search terms relation.', 'ivory-search' ) . '</label>';
		if ( '' !== $synonyms_disable ) {
			$html .= IS_Admin::pro_link();
		}
		echo $html; ?>
		<td>
		<?php 
			$title = __( 'Synonyms', 'ivory-search' ); 
			$content = '<p>' . __( 'Add synonyms here to make the searches find better results.', 'ivory-search' ) . '</p>';
			$content .= '<p>' . __( 'If you add bird = crow to the list of synonyms, searches for bird automatically become a search for bird crow and will thus match to posts that include either bird or crow.', 'ivory-search' ) . '</p>';
			$content .= '<p>' . __( 'This only works for search forms and in OR searches. In AND searches the synonyms only restrict the search, as now the search only finds posts that contain both bird and crow.', 'ivory-search' ) . '</p>';
			IS_Help::help_tooltip( $title, $content );
		?>
		</td>
		<?php
	}

	/**
	 * Displays do not load plugin files field.
	 */
	function plugin_files() {
		$styles = array(
			'css' => __( 'Plugin CSS File', 'ivory-search' ),
			'js'  => __( 'Plugin JavaScript File', 'ivory-search' )

		);

		$html = '';
		foreach ( $styles as $key => $file ) {

			$check_value = isset( $this->opt['do_not_load_plugin_files'][ "plugin-$key-file"] ) ? $this->opt['do_not_load_plugin_files'][ "plugin-$key-file" ] : 0;

			$check_string =  checked( "plugin-$key-file", $check_value, false );

			if ( $this->ivory_search ) {
				$check_value = isset( $this->opt['not_load_files'][ $key] ) ? $this->opt['not_load_files'][ $key] : 0;
				$check_string =  checked( $key, $check_value, false );
			}

			$html .= '<input type="checkbox" id="not_load_files[' . esc_attr( $key ) . ']" name="ivory_search[not_load_files][' . esc_attr( $key ) . ']" value="' . esc_attr( $key ) . '" ' . $check_string . '/>';
			$html .= '<label for="not_load_files[' . esc_attr( $key ) . ']"> ' . esc_html( $file ) . '</label>';

			if ( 'css' == $key ) {
				$html .= '<br /><label for="not_load_files[' . esc_attr( $key ) . ']" style="font-size: 10px;">' . esc_html__( 'If checked, you have to add following plugin file code into your child theme CSS file.', 'ivory-search' ) . '</label>';
				$html .= '<br /><a style="font-size: 13px;" target="_blank" href="' . plugins_url( '/public/css/ivory-search.css', IS_PLUGIN_FILE ) . '"/a>' . plugins_url( '/public/css/ivory-search.css', IS_PLUGIN_FILE ) . '</a>';
				$html .= '<br /><br />';
			} else {
				$html .= '<br /><label for="not_load_files[' . esc_attr( $key ) . ']" style="font-size: 10px;">' . esc_html__( "If checked, you have to add following plugin files code into your child theme JavaScript file.", 'ivory-search' ) . '</label>';
				$html .= '<br /><a style="font-size: 13px;" target="_blank" href="' . plugins_url( '/public/js/ivory-search.js', IS_PLUGIN_FILE ) . '"/a>' . plugins_url( '/public/js/ivory-search.js', IS_PLUGIN_FILE ) . '</a>';
				$html .= '<br /><a style="font-size: 13px;" target="_blank" href="' . plugins_url( '/public/js/is-highlight.js', IS_PLUGIN_FILE ) . '"/a>' . plugins_url( '/public/js/is-highlight.js', IS_PLUGIN_FILE ) . '</a>';
			}
		}
		echo $html;
	}
}