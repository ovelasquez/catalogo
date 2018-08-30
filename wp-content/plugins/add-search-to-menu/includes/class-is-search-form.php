<?php

class IS_Search_Form {

	const post_type = 'is_search_form';

	private static $found_items = 0;
	private static $current = null;

	private $id;
	private $name;
	private $title;
	private $is_locale;
	private $properties = array();
	private $unit_tag;
	private $responses_count = 0;
	private $shortcode_atts = array();

	private function __construct( $post = null ) {
		$post = get_post( $post );

		if ( $post && self::post_type == get_post_type( $post ) ) {
			$this->id = $post->ID;
			$this->name = $post->post_name;
			$this->title = $post->post_title;
			$this->is_locale = get_post_meta( $post->ID, '_is_locale', true );

			$properties = $this->get_properties();

			foreach ( $properties as $key => $value ) {
				if ( metadata_exists( 'post', $post->ID, $key ) ) {
					$properties[$key] = get_post_meta( $post->ID, $key, true );
				}
			}

			$this->properties = $properties;
		}
	}

	public static function get_instance( $post ) {
		$post = get_post( $post );

		if ( ! $post || self::post_type != get_post_type( $post ) ) {
			return false;
		}

		return self::$current = new self( $post );
	}

	public static function count() {
		return self::$found_items;
	}

	public static function get_current() {
		return self::$current;
	}

	public static function register_post_type() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name'			=> __( 'Search Forms', 'ivory-search' ),
				'singular_name' => __( 'Search Form', 'ivory-search' ),
			),
			'rewrite'   => false,
			'query_var' => false,
		) );
	}

	public static function find( $args = '' ) {
		$defaults = array(
			'post_status'	 => 'any',
			'posts_per_page' => -1,
			'offset'		 => 0,
			'orderby'		 => 'ID',
			'order'			 => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::post_type;

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post ) {
			$objs[] = new self( $post );
		}

		return $objs;
	}

	public static function get_template( $args = '' ) {
		global $l10n;

		$defaults = array( 'locale' => null, 'title' => '' );
		$args = wp_parse_args( $args, $defaults );

		$locale = $args['locale'];
		$title = $args['title'];

		if ( $locale ) {
			$mo_orig = $l10n['ivory-search'];
			$is_i18n = IS_I18n::getInstance();
			$is_i18n->load_is_textdomain( $locale );
		}

		self::$current = $search_form = new self;
		$search_form->title =
			( $title ? $title : __( 'Untitled', 'ivory-search' ) );
		$search_form->locale = ( $locale ? $locale : get_locale() );

		$properties = $search_form->get_properties();

		foreach ( $properties as $key => $value ) {
			$properties[$key] = IS_Template::get_default( $key );
		}

		$search_form->properties = $properties;

		$search_form = apply_filters( 'is_search_form_default_pack',
			$search_form, $args );

		if ( isset( $mo_orig ) ) {
			$l10n['ivory-search'] = $mo_orig;
		}

		return $search_form;
	}

	private static function get_unit_tag( $id = 0 ) {
		static $global_count = 0;

		$global_count += 1;

		if ( in_the_loop() ) {
			$unit_tag = sprintf( 'ivory-search-f%1$d-p%2$d-o%3$d',
				absint( $id ), get_the_ID(), $global_count );
		} else {
			$unit_tag = sprintf( 'ivory-search-f%1$d-o%2$d',
				absint( $id ), $global_count );
		}

		return $unit_tag;
	}

	public function __get( $name ) {
		$message = __( '<code>%1$s</code> property of a <code>IS_Search_Form</code> object is <strong>no longer accessible</strong>. Use <code>%2$s</code> method instead.', 'ivory-search' );

		if ( 'id' == $name ) {
			if ( WP_DEBUG ) {
				trigger_error( sprintf( $message, 'id', 'id()' ) );
			}

			return $this->id;
		} elseif ( 'title' == $name ) {
			if ( WP_DEBUG ) {
				trigger_error( sprintf( $message, 'title', 'title()' ) );
			}

			return $this->title;
		} elseif ( $prop = $this->prop( $name ) ) {
			if ( WP_DEBUG ) {
				trigger_error(
					sprintf( $message, $name, 'prop(\'' . $name . '\')' ) );
			}

			return $prop;
		}
	}

	public function initial() {
		return empty( $this->id );
	}

	public function prop( $name ) {
		$props = $this->get_properties();
		return isset( $props[$name] ) ? $props[$name] : null;
	}

	public function get_properties() {
		$properties = (array) $this->properties;

		$properties = wp_parse_args( $properties, array(
			'_is_includes' => '',
			'_is_excludes' => '',
			'_is_settings' => '',
		) );

		$properties = (array) apply_filters( 'is_search_form_properties',
			$properties, $this );

		return $properties;
	}

	public function set_properties( $properties ) {
		$defaults = $this->get_properties();

		$properties = wp_parse_args( $properties, $defaults );
		$properties = array_intersect_key( $properties, $defaults );

		$this->properties = $properties;
	}

	public function id() {
		return $this->id;
	}

	public function name() {
		return $this->name;
	}

	public function title() {
		return $this->title;
	}

	public function set_title( $title ) {
		$title = strip_tags( $title );
		$title = trim( $title );

		if ( '' === $title ) {
			$title = __( 'Untitled', 'ivory-search' );
		}

		$this->title = $title;
	}

	public function locale() {
		if ( $this->is_valid_locale( $this->locale ) ) {
			return $this->locale;
		} else {
			return '';
		}
	}

	public function set_locale( $locale ) {
		$locale = trim( $locale );

		if ( $this->is_valid_locale( $locale ) ) {
			$this->locale = $locale;
		} else {
			$this->locale = 'en_US';
		}
	}

	// Return true if this form is the same one as currently POSTed.
	public function is_posted() {

		if ( empty( $_POST['_is_unit_tag'] ) ) {
			return false;
		}

		return $this->unit_tag == $_POST['_is_unit_tag'];
	}

	/* Generating Form HTML */

	public function form_html( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'html_id'    => '',
			'html_name'  => '',
			'html_class' => '',
			'output'     => 'form',
		) );

		$this->shortcode_atts = $args;

		if ( 'raw_form' == $args['output'] ) {
			return '<pre class="is-raw-form"><code>'
				. esc_html( $this->prop( 'form' ) ) . '</code></pre>';
		}

		$this->unit_tag = self::get_unit_tag( $this->id );

		$lang_tag = str_replace( '_', '-', $this->locale );

		if ( preg_match( '/^([a-z]+-[a-z]+)-/i', $lang_tag, $matches ) ) {
			$lang_tag = $matches[1];
		}

		$html = '';
		$html = apply_filters( 'is_before_search_form', $html );

		$html .= sprintf( '<div %s>',
			IS_Admin_Public::format_atts( array(
				'role' => 'form',
				'class' => 'ivory-search search-form',
				'id' => $this->unit_tag,
				( get_option( 'html_type' ) == 'text/html' ) ? 'lang' : 'xml:lang'
					=> $lang_tag,
				'dir' => is_rtl( $this->locale ) ? 'rtl' : 'ltr',
			) )
		);

		$html .= "\n" . $this->screen_reader_response() . "\n";

		$url = $this->get_request_uri();

		if ( $frag = strstr( $url, '#' ) ) {
			$url = substr( $url, 0, -strlen( $frag ) );
		}

		$url .= '#' . $this->unit_tag;

		$url = apply_filters( 'is_form_action_url', $url );

		$id_attr = apply_filters( 'is_form_id_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $args['html_id'] ) );

		$name_attr = apply_filters( 'is_form_name_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $args['html_name'] ) );

		$class = 'ivory-search-form search-form searchform';

		if ( $args['html_class'] ) {
			$class .= ' ' . $args['html_class'];
		}

		$class = explode( ' ', $class );
		$class = array_map( 'sanitize_html_class', $class );
		$class = array_filter( $class );
		$class = array_unique( $class );
		$class = implode( ' ', $class );
		$class = apply_filters( 'is_form_class_attr', $class );

		$enctype = apply_filters( 'is_form_enctype', '' );
		$autocomplete = apply_filters( 'is_form_autocomplete', '' );

		$atts = array(
			'role'         => 'search',
			'action'       => esc_url( home_url( '/' ) ),
			'method'       => 'get',
			'class'        => $class,
			'id'	       => 'searchform',
			'enctype'      => $this->enctype_value( $enctype ),
			'autocomplete' => $autocomplete,
		);

		if ( '' !== $id_attr ) {
			$atts['id'] = $id_attr;
		}

		if ( '' !== $name_attr ) {
			$atts['name'] = $name_attr;
		}

		$atts = IS_Admin_Public::format_atts( $atts );

		$html .= sprintf( '<form %s>', $atts ) . "\n";

		$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';

		if ( 'html5' == $format ) {
			$html .= '<label>
					<span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>
					<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder' ) . '" value="' . get_search_query() . '" name="s" />
				</label>
				<input type="submit" class="search-submit" id="searchsubmit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />';
		} else {
			$html .= '<label class="screen-reader-text" for="s">' . _x( 'Search for:', 'label' ) . '</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />
					<input type="submit" class="search-submit" id="searchsubmit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />';
		}

		$html .= $this->form_hidden_fields();

		if ( ! $this->responses_count ) {
			$html .= $this->form_response_output();
		}

		$html = apply_filters( 'is_search_form', $html );

		$html .= '</form>';
		$html .= '</div>';

		$html = apply_filters( 'is_after_search_form', $html );

		return $html;
	}

	private function form_hidden_fields() {
		$hidden_fields = array(
			'id' => $this->id(),
		);

		$_includes = $this->prop( '_is_includes' );

		if ( isset( $_includes['post_type_qs'] ) && 'none' !== $_includes['post_type_qs'] ) {
			$hidden_fields = array_merge ( $hidden_fields, array( 'post_type' => $_includes['post_type_qs'] ) );
		}

		$hidden_fields += (array) apply_filters(
			'is_form_hidden_fields', array() );

		$content = '';

		foreach ( $hidden_fields as $name => $value ) {
			$content .= sprintf(
				'<input type="hidden" name="%1$s" value="%2$s" />',
				esc_attr( $name ), esc_attr( $value ) ) . "\n";
		}

		return '<div style="display: none;">' . "\n" . $content . '</div>' . "\n";
	}

	public function form_response_output() {
		$class = 'is-response-output';
		$role = '';
		$content = '';

		if ( $this->is_posted() ) { // Post response output for non-AJAX
			$role = 'alert';
		} else {
			$class .= ' is-display-none';
		}

		$atts = array(
			'class' => trim( $class ),
			'role' => trim( $role ),
		);

		$atts = IS_Admin_Public::format_atts( $atts );

		$output = sprintf( '<div %1$s>%2$s</div>',
			$atts, esc_html( $content ) );

		$output = apply_filters( 'is_form_response_output',
			$output, $class, $content, $this );

		$this->responses_count += 1;

		return $output;
	}

	public function screen_reader_response() {
		$class = 'screen-reader-response';
		$role = '';
		$content = '';

		if ( $this->is_posted() ) { // Post response output for non-AJAX
			$role = 'alert';
		}

		$atts = array(
			'class' => trim( $class ),
			'role' => trim( $role ) );

		$atts = IS_Admin_Public::format_atts( $atts );

		$output = sprintf( '<div %1$s>%2$s</div>',
			$atts, $content );

		return $output;
	}


	/* Settings */

	public function setting( $name, $max = 1 ) {
		$settings = (array) explode( "\n", $this->prop( 'settings' ) );

		$pattern = '/^([a-zA-Z0-9_]+)[\t ]*:(.*)$/';
		$count = 0;
		$values = array();

		foreach ( $settings as $setting ) {
			if ( preg_match( $pattern, $setting, $matches ) ) {
				if ( $matches[1] != $name ) {
					continue;
				}

				if ( ! $max || $count < (int) $max ) {
					$values[] = trim( $matches[2] );
					$count += 1;
				}
			}
		}

		return $values;
	}

	public function is_true( $name ) {
		$settings = $this->setting( $name, false );

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, array( 'on', 'true', '1' ) ) ) {
				return true;
			}
		}

		return false;
	}

	/* Save */

	public function save() {
		$props = $this->get_properties();

		$post_content = implode( "\n", $this->array_flatten( $props ) );

		if ( $this->initial() ) {
			$post_id = wp_insert_post( array(
				'post_type' => self::post_type,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim( $post_content ),
			) );
		} else {
			$post_id = wp_update_post( array(
				'ID' => (int) $this->id,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim( $post_content ),
			) );
		}

		if ( $post_id ) {
			foreach ( $props as $prop => $value ) {
				update_post_meta( $post_id, $prop, $this->normalize_newline_deep( $value ) );
			}

			if ( $this->is_valid_locale( $this->locale ) ) {
				update_post_meta( $post_id, '_is_locale', $this->locale );
			}

			if ( $this->initial() ) {
				$this->id = $post_id;
				do_action( 'is_after_create', $this );
			} else {
				do_action( 'is_after_update', $this );
			}

			do_action( 'is_after_save', $this );
		}

		return $post_id;
	}

	public function copy() {
		$new = new self;
		$new->title = $this->title . '_copy';
		$new->locale = $this->locale;
		$new->properties = $this->properties;

		return apply_filters( 'is_copy', $new, $this );
	}

	public function delete() {
		if ( $this->initial() ) {
			return;
		}

		if ( wp_delete_post( $this->id, true ) ) {
			$this->id = 0;
			return true;
		}

		return false;
	}

	public function shortcode( $args = '' ) {
		$args = wp_parse_args( $args );

		$title = str_replace( array( '"', '[', ']' ), '', $this->title );

		$shortcode = sprintf( '[ivory-search id="%1$d" title="%2$s"]',
			$this->id, $title );

		return apply_filters( 'is_search_form_shortcode', $shortcode, $args, $this );
	}

	function is_valid_locale( $locale ) {
		$pattern = '/^[a-z]{2,3}(?:_[a-zA-Z_]{2,})?$/';
		return (bool) preg_match( $pattern, $locale );
	}

	function normalize_newline( $text, $to = "\n" ) {
		if ( ! is_string( $text ) ) {
			return $text;
		}

		$nls = array( "\r\n", "\r", "\n" );

		if ( ! in_array( $to, $nls ) ) {
			return $text;
		}

		return str_replace( $nls, $to, $text );
	}

	function normalize_newline_deep( $arr, $to = "\n" ) {

		if ( is_array( $arr ) ) {
			$result = array();

			foreach ( $arr as $key => $text ) {
				$result[$key] = $this->normalize_newline_deep( $text, $to );
			}

			return $result;
		}

		return $this->normalize_newline( $arr, $to );
	}

	function array_flatten( $input ) {
		if ( ! is_array( $input ) ) {
			return array( $input );
		}

		$output = array();

		foreach ( $input as $value ) {
			$output = array_merge( $output, $this->array_flatten( $value ) );
		}

		return $output;
	}

	function get_request_uri() {
		static $request_uri = '';

		if ( empty( $request_uri ) ) {
			$request_uri = add_query_arg( array() );
		}

		return esc_url_raw( $request_uri );
	}

	function enctype_value( $enctype ) {
		$enctype = trim( $enctype );

		if ( empty( $enctype ) ) {
			return '';
		}

		$valid_enctypes = array(
			'application/x-www-form-urlencoded',
			'multipart/form-data',
			'text/plain',
		);

		if ( in_array( $enctype, $valid_enctypes ) ) {
			return $enctype;
		}

		$pattern = '%^enctype="(' . implode( '|', $valid_enctypes ) . ')"$%';

		if ( preg_match( $pattern, $enctype, $matches ) ) {
			return $matches[1]; // for back-compat
		}

		return '';
	}
}
