<?php

class IS_Search_Editor {

	private $search_form;
	private $panels = array();

	public function __construct( IS_Search_Form $search_form ) {
		$this->search_form = $search_form;
	}

	function is_name( $string ) {
		return preg_match( '/^[A-Za-z][-A-Za-z0-9_:.]*$/', $string );
	}

	public function add_panel( $id, $title, $callback ) {
		if ( $this->is_name( $id ) ) {
			$this->panels[$id] = array(
				'title' => $title,
				'callback' => $callback,
			);
		}
	}

	public function display() {
		if ( empty( $this->panels ) ) {
			return;
		}

		echo '<ul id="search-form-editor-tabs">';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
				esc_attr( $id ), esc_html( $panel['title'] ) );
		}

		echo '</ul>';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<div class="search-form-editor-panel" id="%1$s">',
				esc_attr( $id ) );

			$this->notice( $id, $panel );
			$callback = $panel['callback'];
			$this->$callback( $this->search_form );

			echo '</div>';
		}
	}

	public function notice( $id, $panel ) {
		echo '<div class="config-error"></div>';
	}

	/**
	 * Gets all public meta keys of post types
	 *
	 * @global Object $wpdb WPDB object
	 * @return Array array of meta keys
	 */
	function is_meta_keys( $post_types ) {
		global $wpdb;
		$post_types = implode( "', '", $post_types );
		$is_fields = $wpdb->get_results( apply_filters( 'is_meta_keys_query', "select DISTINCT meta_key from $wpdb->postmeta pt LEFT JOIN $wpdb->posts p ON (pt.post_id = p.ID) where meta_key NOT LIKE '\_%' AND post_type IN ( '$post_types' ) ORDER BY meta_key ASC" ) );
		$meta_keys = array();

		if ( is_array( $is_fields ) && ! empty( $is_fields ) ) {
			foreach ( $is_fields as $field ) {
				if ( isset( $field->meta_key ) ) {
					$meta_keys[] = $field->meta_key;
				}
			}
		}

		/**
		 * Filter results of SQL query for meta keys
		 */
		return apply_filters( 'is_meta_keys', $meta_keys );
	}

	public function includes_panel( $post ) {
		$id = '_is_includes';
		$includes = $post->prop( $id );
		$settings = $post->prop( '_is_settings' );
		$excludes = $post->prop( '_is_excludes' );
	?>

		<div class="search-form-editor-box-includes" id="<?php echo $id; ?>">
		<fieldset>
		<legend>
			<?php
				_e( "Configure the below options to make specific content searchable.", 'ivory-search' );
			?>
		</legend>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post_type"><?php echo esc_html( __( 'Post Types', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$args = array( 'public' => true );

					if ( isset( $settings['exclude_from_search'] ) ) {
						$args = array( 'public' => true, 'exclude_from_search' => false );
					}
					$posts = get_post_types( $args );

					if ( ! empty( $posts ) ){
						foreach ( $posts as $key => $post_type ) {
							$checked = isset( $includes['post_type'][ esc_attr( $key )] ) ? $includes['post_type'][ esc_attr( $key )] : 0;

							echo '<div class="col-wrapper check-radio"><input type="checkbox" id="'. $id . '-post_type-' . esc_attr( $key ) . '" name="'. $id . '[post_type][' . esc_attr( $key ) . ']" value="' . esc_attr( $key ) . '" ' . checked( $key, $checked, false ) . '/>';
							echo '<label for="'. $id . '-post_type-' . esc_attr( $key ) . '"> ' . ucfirst( esc_html( $post_type ) ) . '</label></div>';
						}
						$checked = ( isset( $includes['search_title'] ) && $includes['search_title'] ) ? 1 : 0;
						echo '<br /><br /><p><input type="checkbox" id="'. $id . '-search_title" name="'. $id . '[search_title]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_title">' . esc_html__( "Search in post title.", 'ivory-search' ) . '</label></p>';
						$checked = ( isset( $includes['search_content'] ) && $includes['search_content'] ) ? 1 : 0;
						echo '<p><input type="checkbox" id="'. $id . '-search_content" name="'. $id . '[search_content]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_content">' . esc_html__( "Search in post content.", 'ivory-search' ) . '</label></p>';
						$checked = ( isset( $includes['search_excerpt'] ) && $includes['search_excerpt'] ) ? 1 : 0;
						echo '<p><input type="checkbox" id="'. $id . '-search_excerpt" name="'. $id . '[search_excerpt]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_excerpt">' . esc_html__( "Search in post excerpt.", 'ivory-search' ) . '</label></p>';
						echo '<br /><select name="'. $id . '[post_type_qs]" >';
						$checked = isset( $includes['post_type_qs'] ) ? $includes['post_type_qs'] : 'none';
						echo '<option value="none" ' . selected( 'none', $checked, false ) . '>' . esc_html( __( 'None', 'ivory-search' ) ) . '</option>';
						foreach ( $posts as $key => $post_type ) {
							echo '<option value="' . $key . '" ' . selected( $key, $checked, false ) . '>' . ucfirst( esc_html( $post_type ) ) . '</option>';
						}
						echo '</select><label for="'. $id . '-post_type_qs"> ' . esc_html( __( 'Display this post type in the search query URL and restrict search to it.', 'ivory-search' ) ) . '</label>';
					} else {
						_e( 'No post types registered on your site.', 'ivory-search' );
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Post Types', 'ivory-search' ); 
					$content = '<p>' . __( 'Select post types that you want to make searchable.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-tax_query"><?php esc_html_e( 'Taxonomy Terms', 'ivory-search' ); ?></label>
				</th>
				<td>
					<?php

					$args = array( 'post', 'page' );

					if ( isset( $includes['post_type'] ) && ! empty( $includes['post_type'] ) && is_array( $includes['post_type'] ) ) {
						$args = array_values( $includes['post_type'] );
					}

					$tax_objs = get_object_taxonomies( $args, 'objects' );

					if ( ! empty( $tax_objs ) ){
						foreach ( $tax_objs as $key => $tax_obj ) {

						$terms = get_terms( array(
							'taxonomy' => $key,
							'hide_empty' => false,
						) );

						if ( ! empty( $terms ) ){

							echo '<div class="col-wrapper"><div class="col-title">' . ucwords( str_replace( '-', ' ', str_replace( '_', ' ', esc_html( $key ) ) ) ) . '</div>';
							echo '<input type="hidden" id="'. $id . '-tax_post_type" name="'. $id . '[tax_post_type]['.$key.']" value="'. implode( ',', $tax_obj->object_type ) .'" />';
							echo '<select name="'. $id . '[tax_query]['.$key.'][]" multiple size="8" >';
							foreach ( $terms as $key2 => $term ) {

								$checked = ( isset( $includes['tax_query'][$key] ) && in_array( $term->term_taxonomy_id, $includes['tax_query'][$key] ) ) ? $term->term_taxonomy_id : 0;
								echo '<option value="' . esc_attr( $term->term_taxonomy_id ) . '" ' . selected( $term->term_taxonomy_id, $checked, false ) . '>' . esc_html( $term->name ) . '</option>';
							}
							echo '</select></div>';
						}
						}
						echo '<br /><label for="'. $id . '-tax_query" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';

						$checked = ( isset( $includes['tax_rel'] ) && "OR" == $includes['tax_rel'] ) ? "OR" : "AND";
						echo '<br /><p><input type="radio" id="'. $id . '-tax_rel_and" name="'. $id . '[tax_rel]" value="AND" ' . checked( 'AND', $checked, false ) . '/>';
						echo '<label for="'. $id . '-tax_rel_and" >' . esc_html__( "AND - Search posts having all the above selected terms.", 'ivory-search' ) . '</label></p>';
						echo '<p><input type="radio" id="'. $id . '-tax_rel_or" name="'. $id . '[tax_rel]" value="OR" ' . checked( 'OR', $checked, false ) . '/>';
						echo '<label for="'. $id . '-tax_rel_or" >' . esc_html__( "OR - Search posts having any one of the above selected terms.", 'ivory-search' ) . '</label></p>';
						$checked = ( isset( $includes['search_tax_title'] ) && $includes['search_tax_title'] ) ? 1 : 0;
						echo '<br /><p><input type="checkbox" id="'. $id . '-search_tax_title" name="'. $id . '[search_tax_title]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_tax_title" >' . esc_html__( "Search in taxonomy terms title.", 'ivory-search' ) . '</label></p>';
						$checked = ( isset( $includes['search_tax_desp'] ) && $includes['search_tax_desp'] ) ? 1 : 0;
						echo '<p><input type="checkbox" id="'. $id . '-search_tax_desp" name="'. $id . '[search_tax_desp]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_tax_desp" >' . esc_html__( "Search in taxonomy terms description.", 'ivory-search' ) . '</label></p>';
					} else {
						_e( 'No taxonomies registered for slected post types.', 'ivory-search' );
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Taxonomy Terms', 'ivory-search' ); 
					$content = '<p>' . __( 'The non empty terms created in the registered taxonomies of above selected post types display here.', 'ivory-search' ) . '</p>';
					$content .= '<p>' . __( 'Select terms here to restrict search to the posts having selected terms.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-custom_field"><?php echo esc_html( __( 'Custom Fields', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$args = array( 'post', 'page' );

					if ( isset( $includes['post_type'] ) && ! empty( $includes['post_type'] ) && is_array( $includes['post_type'] ) ) {
						$args = array_values( $includes['post_type'] );
					}
					$meta_keys = $this->is_meta_keys( $args );
					if ( ! empty( $meta_keys ) ) {
						echo '<select name="'. $id . '[custom_field][]" multiple size="8" >';
						foreach ( $meta_keys as $meta_key ) {
							$checked = ( isset( $includes['custom_field'] ) && in_array( $meta_key, $includes['custom_field'] )  ) ? $meta_key : 0;
							echo '<option value="' . esc_attr( $meta_key ) . '" ' . selected( $meta_key, $checked, false ) . '>' . esc_html( $meta_key ) . '</option>';
						}
						echo '</select>';
						echo '<br /><br /><label for="'. $id . '-custom_field" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Custom Fields', 'ivory-search' ); 
					$content = '<p>' . __( 'Select custom fields to make their values searchable.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-woocommerce"><?php echo esc_html( __( 'WooCommerce', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$args = array( 'post', 'page' );

					if ( isset( $includes['post_type'] ) && ! empty( $includes['post_type'] ) && is_array( $includes['post_type'] ) ) {
						$args = array_values( $includes['post_type'] );
					}
					if ( in_array( 'product', $args ) ) {
						$woo_sku_disable = is_fs()->is_plan( 'pro_plus' ) ? '' : ' disabled ';
						$checked = ( isset( $includes['woo']['sku'] ) && $includes['woo']['sku'] ) ? 1 : 0;
						echo '<p><input type="checkbox" ' . $woo_sku_disable . ' id="'. $id . '-sku" name="'. $id . '[woo][sku]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-sku" >' . esc_html__( "Search in WooCommerce products SKU.", 'ivory-search' ) . '</label>';
						echo IS_Admin::pro_link( 'pro_plus' ) . '</p>';
					} else {
						_e( 'WooCommerce product post type is not included in search.', 'ivory-search' );
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'WooCommerce', 'ivory-search' ); 
					$content = '<p>' . __( 'Configure WooCommerce products search options here.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-author"><?php echo esc_html( __( 'Authors', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php

					if ( ! isset( $excludes['author'] ) ) {
					$author_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';

					$authors = get_users( array(
							'fields' => array( 'ID', 'display_name' ),
							'orderby' => 'post_count',
							'order'   => 'DESC',
							'who'     => 'authors',
					) );

					if ( ! empty( $authors ) ) {
						if ( '' !== $author_disable ) {
							echo '<div class="col-wrapper check-radio">' . IS_Admin::pro_link() . '</div>';
						}
					foreach ( $authors as $author ) {

						$post_count = count_user_posts( $author->ID );

						// Move on if user has not published a post (yet).
						if ( ! $post_count ) {
							continue;
						}

						$checked = isset( $includes['author'][ esc_attr( $author->ID )] ) ? $includes['author'][ esc_attr( $author->ID )] : 0;

						echo '<div class="col-wrapper check-radio"><input type="checkbox" ' . $author_disable . ' id="'. $id . '-author-' . esc_attr( $author->ID ) . '" name="'. $id . '[author][' . esc_attr( $author->ID ) . ']" value="' . esc_attr( $author->ID ) . '" ' . checked( $author->ID, $checked, false ) . '/>';
						echo '<label for="'. $id . '-author-' . esc_attr( $author->ID ) . '"> ' . ucfirst( esc_html( $author->display_name ) ) . '</label></div>';
					}
					} 
					} else {
						echo '<label>' . esc_html__( "Search has been already limited by excluding specific authors posts in the Excludes section.", 'ivory-search' ) . '</label>';
					}

					$checked = ( isset( $includes['search_author'] ) && $includes['search_author'] ) ? 1 : 0;
					echo '<br /><br /><input type="checkbox" id="'. $id . '-search_author" name="'. $id . '[search_author]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-search_author" >' . esc_html__( "Search in author Display name and display the posts created by that author.", 'ivory-search' ) . '</label>';
					?>
				<td>
				<?php 
					$title = __( 'Authors', 'ivory-search' ); 
					$content = '<p>' . __( 'Make specific author posts searchable.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post_status"><?php echo esc_html( __( 'Post Status', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					if ( ! isset( $excludes['post_status'] ) ) {
					$post_statuses = get_post_stati();

					$post_status_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';

					if ( ! empty( $post_statuses ) ) {
						if ( '' !== $post_status_disable ) {
							echo '<div class="col-wrapper check-radio">' . IS_Admin::pro_link() . '</div>';
						}
					foreach ( $post_statuses as $key => $post_status ) {

						$checked = isset( $includes['post_status'][ esc_attr( $key )] ) ? $includes['post_status'][ esc_attr( $key )] : 0;

						echo '<div class="col-wrapper check-radio"><input type="checkbox" ' . $post_status_disable . ' id="'. $id . '-post_status-' . esc_attr( $key ) . '" name="'. $id . '[post_status][' . esc_attr( $key ) . ']" value="' . esc_attr( $key ) . '" ' . checked( $key, $checked, false ) . '/>';
						echo '<label for="'. $id . '-post_status-' . esc_attr( $key ) . '"> ' . ucwords( str_replace( '-', ' ', esc_html( $post_status ) ) ) . '</label></div>';
					}
					}
					} else {
						echo '<label>' . esc_html__( "Search has been already limited by excluding specific posts statuses from search in the Excludes section.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Post Status', 'ivory-search' ); 
					$content = '<p>' . __( 'Configure options to search posts having specific post statuses.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-comment_count"><?php echo esc_html( __( 'Comments', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
						$comment_count_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
						echo '<select name="'. $id . '[comment_count][compare]" ' . $comment_count_disable . ' style="min-width: 50px;">';
						$checked = isset( $includes['comment_count']['compare'] ) ? htmlspecialchars_decode( $includes['comment_count']['compare'] ) : '=';
						$compare = array( '=', '!=', '>', '>=', '<', '<=' );
						foreach ( $compare as $d ) {
							echo '<option value="' . htmlspecialchars_decode( $d ) . '" ' . selected( $d, $checked, false ) . '>' . esc_html( $d ) . '</option>';
						}
						echo '</select><label for="'. $id . '-comment_count-compare"> ' . esc_html( __( 'The search operator to compare comments count.', 'ivory-search' ) ) . '</label>';
						echo IS_Admin::pro_link() . '</div>';

						echo '<br /><select name="'. $id . '[comment_count][value]" ' . $comment_count_disable . ' >';
						$checked = isset( $includes['comment_count']['value'] ) ? $includes['comment_count']['value'] : 'na';
						echo '<option value="na" ' . selected( 'na', $checked, false ) . '>' . esc_html( __( 'NA', 'ivory-search' ) ) . '</option>';
						for ( $d = 0; $d <= 999; $d++ ) {
							echo '<option value="' . $d . '" ' . selected( $d, $checked, false ) . '>' . $d . '</option>';
						}
						echo '</select><label for="'. $id . '-comment_count-value"> ' . esc_html( __( 'The amount of comments your posts has to have.', 'ivory-search' ) ) . '</label>';
						echo IS_Admin::pro_link() . '</div>';

						$checked = ( isset( $includes['search_comment'] ) && $includes['search_comment'] ) ? 1 : 0;
						echo '<br /><br /><input type="checkbox" id="'. $id . '-search_comment" name="'. $id . '[search_comment]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-search_comment" >' . esc_html__( "Search in approved comments content.", 'ivory-search' ) . '</label>';
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Comments', 'ivory-search' ); 
					$content = '<p>' . __( 'Make posts searchable having specific comments count.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-date_query"><?php echo esc_html( __( 'Date', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$range = array( 'after', 'before' );
					foreach ( $range as $value ) {
						echo '<div class="col-wrapper ' . $value . '"><div class="col-title">' . ucfirst( $value ) . '</div>';

						echo '<select name="'. $id . '[date_query]['.$value.'][day]" >';
						$checked = isset( $includes['date_query'][$value]['day'] ) ? $includes['date_query'][$value]['day'] : 'day';
						echo '<option value="day" ' . selected( 'day', $checked, false ) . '>' . esc_html( __( 'Day', 'ivory-search' ) ) . '</option>';
						for ( $d = 1; $d <= 31; $d++ ) {
							echo '<option value="' . $d . '" ' . selected( $d, $checked, false ) . '>' . $d . '</option>';
						}
						echo '</select>';

						echo '<select name="'. $id . '[date_query]['.$value.'][month]" >';
						$checked = isset( $includes['date_query'][$value]['month'] ) ? $includes['date_query'][$value]['month'] : 'month';
						echo '<option value="month" ' . selected( 'month', $checked, false ) . '>' . esc_html( __( 'Month', 'ivory-search' ) ) . '</option>';
						for ( $m = 1; $m <= 12; $m++ ) {
							echo '<option value="' . $m . '" ' . selected( $m, $checked, false ) . '>' . date( 'F', mktime( 0, 0, 0, $m, 1 ) ) . '</option>';
						}
						echo '</select>';

						echo '<select name="'. $id . '[date_query]['.$value.'][year]" >';
						$checked = isset( $includes['date_query'][$value]['year'] ) ? $includes['date_query'][$value]['year'] : 'year';
						echo '<option value="year" ' . selected( 'year', $checked, false ) . '>' . esc_html( __( 'Year', 'ivory-search' ) ) . '</option>';
						for ( $y = date("Y"); $y >= 1995; $y-- ) {
							echo '<option value="' . $y . '" ' . selected( $y, $checked, false ) . '>' . $y . '</option>';
						}
						echo '</select></div>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Date', 'ivory-search' ); 
					$content = '<p>' . __( 'Make posts searchable created in the specific date range.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-has_password"><?php echo esc_html( __( 'Password', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
						$checked = ( isset( $includes['has_password'] ) ) ? $includes['has_password'] : 'null';
						echo '<p><input type="radio" id="'. $id . '-has_password" name="'. $id . '[has_password]" value="null" ' . checked( 'null', $checked, false ) . '/>';
						echo '<label for="'. $id . '-has_password" >' . esc_html__( "Search all posts with and without passwords.", 'ivory-search' ) . '</label></p>';
						echo '<p><input type="radio" id="'. $id . '-has_password_1" name="'. $id . '[has_password]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-has_password_1" >' . esc_html__( "Search only posts with passwords.", 'ivory-search' ) . '</label></p>';
						echo '<p><input type="radio" id="'. $id . '-has_password_0" name="'. $id . '[has_password]" value="0" ' . checked( 0, $checked, false ) . '/>';
						echo '<label for="'. $id . '-has_password_0" >' . esc_html__( "Search only posts without passwords.", 'ivory-search' ) . '</label></p>';
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Password', 'ivory-search' ); 
					$content = '<p>' . __( 'Configure options to search posts with or without password.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<?php
				global $wp_version;
				if ( 4.9 <= $wp_version ) {
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post_file_type"><?php echo esc_html( __( 'File Types', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					if ( ! isset( $excludes['post_file_type'] ) ) {
					$file_types = get_allowed_mime_types();
					if ( ! empty( $file_types ) ) {
						$file_type_disable = is_fs()->is_plan( 'pro_plus' ) ? '' : ' disabled ';
						ksort($file_types);
						echo '<select name="'. $id . '[post_file_type][]" ' . $file_type_disable . ' multiple size="8" >';
						foreach ( $file_types as $key => $file_type ) {
							$checked = ( isset( $includes['post_file_type'] ) && in_array( $file_type, $includes['post_file_type'] )  ) ? $file_type : 0;
							echo '<option value="' . esc_attr( $file_type ) . '" ' . selected( $file_type, $checked, false ) . '>' . esc_html( $key ) . '</option>';
						}
						echo '</select>';
						echo IS_Admin::pro_link( 'pro_plus' );
						echo '<br /><br /><label for="'. $id . '-post_file_type" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					}
					} else {
						echo '<label>' . esc_html__( "Search has been already limited by excluding specific File type in the Excludes section.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'File Type', 'ivory-search' ); 
					$content = '<p>' . __( 'Configure options to search posts having specific MIME or file types specially media attachment posts.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<?php
				}
			?>
		</tbody>
		</table>
		</fieldset>
		</div>

	<?php
	}

	public function excludes_panel( $post ) {
		$id = '_is_excludes';
		$excludes = $post->prop( $id );
		$includes = $post->prop( '_is_includes' );
	?>
		<div class="search-form-editor-box-excludes" id="<?php echo $id; ?>">
		<fieldset>
		<legend>
			<?php
				_e( "Configure the options to exclude specific content from search.", 'ivory-search' );
			?>
		</legend>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post__not_in"><?php echo esc_html( __( 'Posts', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$post_types = array( 'post', 'page' );

					if ( isset( $includes['post_type'] ) && ! empty( $includes['post_type'] ) && is_array( $includes['post_type'] ) ) {
						$post_types = array_values( $includes['post_type'] );
					}

					foreach ( $post_types as $post_type ) {

						$posts = get_posts( array(
							'post_type'		=> $post_type,
							'posts_per_page'=> -1,
							'orderby'       => 'title',
							'order'         => 'ASC',
						) );

						if ( ! empty( $posts ) ) {
							echo '<div class="col-wrapper"><div class="col-title">' . ucwords( $post_type ) . '</div>';
							echo '<select name="'. $id . '[post__not_in][]" multiple size="8" >';
							foreach ( $posts as $post2 ) {
								$checked = ( isset( $excludes['post__not_in'] ) && in_array( $post2->ID, $excludes['post__not_in'] ) ) ? $post2->ID : 0;
								$post_title = ( isset( $post2->post_title ) && '' !== $post2->post_title  ) ? esc_html( $post2->post_title ) : $post2->post_name;
								echo '<option value="' . esc_attr( $post2->ID ) . '" ' . selected( $post2->ID, $checked, false ) . '>' . $post_title . '</option>';
							}
							echo '</select></div>';
						}
					}
					echo '<br /><label for="'. $id . '-post__not_in" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Posts', 'ivory-search' ); 
					$content = '<p>' . __( 'The posts and pages of searchable post types display here. You can make post types searchable in the form Includes section.', 'ivory-search' ) . '</p>';
					$content .= '<p>' . __( 'Select the posts to exclude from the search.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-tax_query"><?php esc_html_e( 'Taxonomy Terms', 'ivory-search' ); ?></label>
				</th>
				<td>
					<?php
					$tax_objs = get_object_taxonomies( $post_types, 'objects' );

					if ( ! empty( $tax_objs ) ) {
						foreach ( $tax_objs as $key => $tax_obj ) {

						$terms = get_terms( array(
							'taxonomy' => $key,
							'hide_empty' => false,
						) );

						if ( ! empty( $terms ) ){

							echo '<div class="col-wrapper"><div class="col-title">' . ucwords( str_replace( '-', ' ', str_replace( '_', ' ', esc_html( $key ) ) ) ) . '</div>';
							echo '<select name="'. $id . '[tax_query]['.$key.'][]" multiple size="8" >';
							foreach ( $terms as $key2 => $term ) {

								$checked = ( isset( $excludes['tax_query'][$key] ) && in_array( $term->term_taxonomy_id, $excludes['tax_query'][$key] ) ) ? $term->term_taxonomy_id : 0;
								echo '<option value="' . esc_attr( $term->term_taxonomy_id ) . '" ' . selected( $term->term_taxonomy_id, $checked, false ) . '>' . esc_html( $term->name ) . '</option>';
							}
							echo '</select></div>';
						}
						}
						echo '<br /><label for="'. $id . '-tax_query" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					} else {
						_e( 'No taxonomies registered for slected post types.', 'ivory-search' );
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Taxonomy Terms', 'ivory-search' ); 
					$content = '<p>' . __( 'The taxonomies and terms attached to searchable post types display here. You can make post types searchable in the form Includes section.', 'ivory-search' ) . '</p>';
					$content .= '<p>' . __( 'Exclude posts from the search having selected terms.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-custom_field"><?php echo esc_html( __( 'Custom Fields', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$args = array( 'post', 'page' );

					if ( isset( $excludes['post_type'] ) && ! empty( $excludes['post_type'] ) && is_array( $excludes['post_type'] ) ) {
						$args = array_values( $excludes['post_type'] );
					}
					$meta_keys = $this->is_meta_keys( $args );
					if ( ! empty( $meta_keys ) ) {
						$custom_field_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
						echo '<select name="'. $id . '[custom_field][]" ' . $custom_field_disable . ' multiple size="8" >';
						foreach ( $meta_keys as $meta_key ) {
							$checked = ( isset( $excludes['custom_field'] ) && in_array( $meta_key, $excludes['custom_field'] )  ) ? $meta_key : 0;
							echo '<option value="' . esc_attr( $meta_key ) . '" ' . selected( $meta_key, $checked, false ) . '>' . esc_html( $meta_key ) . '</option>';
						}
						echo '</select>';
						echo IS_Admin::pro_link();
						echo '<br /><br /><label for="'. $id . '-custom_field" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Custom Fields', 'ivory-search' ); 
					$content = '<p>' . __( 'Exclude posts from the search having selected custom fields.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-woocommerce"><?php echo esc_html( __( 'WooCommerce', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					$args = array( 'post', 'page' );

					if ( isset( $includes['post_type'] ) && ! empty( $includes['post_type'] ) && is_array( $includes['post_type'] ) ) {
						$args = array_values( $includes['post_type'] );
					}
					if ( in_array( 'product', $args ) ) {
						$outofstock_disable = is_fs()->is_plan( 'pro_plus' ) ? '' : ' disabled ';
						$checked = ( isset( $excludes['woo']['outofstock'] ) && $excludes['woo']['outofstock'] ) ? 1 : 0;
						echo '<input type="checkbox" ' . $outofstock_disable . ' id="'. $id . '-outofstock" name="'. $id . '[woo][outofstock]" value="1" ' . checked( 1, $checked, false ) . '/>';
						echo '<label for="'. $id . '-outofstock" >' . esc_html__( "Exclude 'out of stock' WooCommerce products.", 'ivory-search' ) . '</label>';
						echo IS_Admin::pro_link( 'pro_plus' );
					} else {
						_e( 'WooCommerce product post type is not included in search.', 'ivory-search' );
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'WooCommerce', 'ivory-search' ); 
					$content = '<p>' . __( 'Exclude specific WooCommerce products from the search.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-author"><?php echo esc_html( __( 'Authors', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					if ( ! isset( $includes['author'] ) ) {

					$author_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
					$authors = get_users( array(
							'fields' => array( 'ID', 'display_name' ),
							'orderby' => 'post_count',
							'order'   => 'DESC',
							'who'     => 'authors',
					) );
					if ( ! empty( $authors ) ) {
						if ( '' !== $author_disable ) {
							echo '<div class="col-wrapper check-radio">' . IS_Admin::pro_link() . '</div>';
						}
					foreach ( $authors as $author ) {

						$post_count = count_user_posts( $author->ID );

						// Move on if user has not published a post (yet).
						if ( ! $post_count ) {
							continue;
						}

						$checked = isset( $excludes['author'][ esc_attr( $author->ID )] ) ? $excludes['author'][ esc_attr( $author->ID )] : 0;

						echo '<div class="col-wrapper check-radio"><input type="checkbox" ' . $author_disable . ' id="'. $id . '-author-' . esc_attr( $author->ID ) . '" name="'. $id . '[author][' . esc_attr( $author->ID ) . ']" value="' . esc_attr( $author->ID ) . '" ' . checked( $author->ID, $checked, false ) . '/>';
						echo '<label for="'. $id . '-author-' . esc_attr( $author->ID ) . '"> ' . ucfirst( esc_html( $author->display_name ) ) . '</label></div>';
					}
					}
					} else {
						echo '<label>' . esc_html__( "Search has been already limited to posts created by specific authors in the Includes section.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Authors', 'ivory-search' ); 
					$content = '<p>' . __( 'Exclude posts from the search created by slected authors.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post_status"><?php echo esc_html( __( 'Post Status', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					if ( ! isset( $includes['post_status'] ) ) {
					$post_statuses = get_post_stati();
					$post_status_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';

					if ( ! empty( $post_statuses ) ) {
						if ( '' !== $post_status_disable ) {
							echo '<div class="col-wrapper check-radio">' . IS_Admin::pro_link() . '</div>';
						}
					foreach ( $post_statuses as $key => $post_status ) {

						$checked = isset( $excludes['post_status'][ esc_attr( $key )] ) ? $excludes['post_status'][ esc_attr( $key )] : 0;

						echo '<div class="col-wrapper check-radio"><input type="checkbox" ' . $post_status_disable . ' id="'. $id . '-post_status-' . esc_attr( $key ) . '" name="'. $id . '[post_status][' . esc_attr( $key ) . ']" value="' . esc_attr( $key ) . '" ' . checked( $key, $checked, false ) . '/>';
						echo '<label for="'. $id . '-post_status-' . esc_attr( $key ) . '"> ' . ucwords( str_replace( '-', ' ', esc_html( $post_status ) ) ) . '</label></div>';
					}
					}
					} else {
						echo '<label>' . esc_html__( "Search has been already limited to posts statuses set in the Includes section.", 'ivory-search' ) . '</label>';
					}

					$checked = ( isset( $excludes['ignore_sticky_posts'] ) && $excludes['ignore_sticky_posts'] ) ? 1 : 0;
					echo '<br /><br /><input type="checkbox" id="'. $id . '-ignore_sticky_posts" name="'. $id . '[ignore_sticky_posts]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-ignore_sticky_posts" >' . esc_html__( "Exclude sticky posts from search.", 'ivory-search' ) . '</label>';
					?>
				</td>
				<td>
				<?php 
					$title = __( 'Post Status', 'ivory-search' ); 
					$content = '<p>' . __( 'Exclude posts from the search having selected post statuses.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<?php
				global $wp_version;
				if ( 4.9 <= $wp_version ) {
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-post_file_type"><?php echo esc_html( __( 'File Types', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
					if ( ! isset( $includes['post_file_type'] ) ) {
					$file_types = get_allowed_mime_types();
					if ( ! empty( $file_types ) ) {
						$file_type_disable = is_fs()->is_plan( 'pro_plus' ) ? '' : ' disabled ';
					ksort( $file_types );
					echo '<select name="'. $id . '[post_file_type][]" ' . $file_type_disable . ' multiple size="8" >';
					foreach ( $file_types as $key => $file_type ) {
						$checked = ( isset( $excludes['post_file_type'] ) && in_array( $file_type, $excludes['post_file_type'] )  ) ? $file_type : 0;
						echo '<option value="' . esc_attr( $file_type ) . '" ' . selected( $file_type, $checked, false ) . '>' . esc_html( $key ) . '</option>';
					}
					echo '</select>';
					echo IS_Admin::pro_link( 'pro_plus' );
					echo '<br /><br /><label for="'. $id . '-post_file_type" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Press CTRL key to select multiple terms or deselect them.", 'ivory-search' ) . '</label>';
					}
					} else {
						echo '<label>' . esc_html__( "Search has been already limited to specific File type set in the Includes section.", 'ivory-search' ) . '</label>';
					}
					?>
				</td>
				<td>
				<?php 
					$title = __( 'File Type', 'ivory-search' ); 
					$content = '<p>' . __( 'Exclude posts specially media attachment posts from the search having selected file types.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<?php
				}
			?>
		</tbody>
		</table>
		</fieldset>
		</div>
	<?php
	}

	public function settings_panel( $post ) {
		$id = '_is_settings';
		$settings = $post->prop( $id );
	?>
		<div class="search-form-editor-box-excludes" id="<?php echo $id; ?>">
		<fieldset>
		<legend>
			<?php
				_e( "Configure the options here to control search of this search form.", 'ivory-search' );
			?>
		</legend>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-posts_per_page"><?php echo esc_html( __( 'Posts Per Page', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
						echo '<select name="'. $id . '[posts_per_page]" >';
						$checked = isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : get_option( 'posts_per_page', 10 );
						for ( $d = 1; $d <= 1000; $d++ ) {
							echo '<option value="' . $d . '" ' . selected( $d, $checked, false ) . '>' . $d . '</option>';
						}
						echo '</select><label for="'. $id . '-posts_per_page"> ' . esc_html( __( 'Number of posts to display on search results page.', 'ivory-search' ) ) . '</label></div>';
					?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-order"><?php echo esc_html( __( 'Order By', 'ivory-search' ) ); ?></label>
				</th>
				<td>
					<?php
						$orderby_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
						echo '<select name="'. $id . '[orderby]" ' . $orderby_disable . ' >';
						$checked = isset( $settings['orderby'] ) ? $settings['orderby'] : 'date';
						$orderbys = array( 'date', 'relevance', 'none', 'ID', 'author', 'title', 'name', 'type', 'modified', 'parent', 'rand',
										'comment_count', 'menu_order', 'meta_value', 'meta_value_num', 'post__in', 'post_name__in', 'post_parent__in' );
						foreach ( $orderbys as $orderby ) {
							echo '<option value="' . $orderby . '" ' . selected( $orderby, $checked, false ) . '>' . ucwords( str_replace( '_', ' ', esc_html( $orderby ) ) ) . '</option>';
						}

						echo '</select><select name="'. $id . '[order]" ' . $orderby_disable . ' >';
						$checked = isset( $settings['order'] ) ? $settings['order'] : 'DESC';
						$orders = array( 'DESC', 'ASC' );
						foreach ( $orders as $order ) {
							echo '<option value="' . $order . '" ' . selected( $order, $checked, false ) . '>' . ucwords( str_replace( '_', ' ', esc_html( $order ) ) ) . '</option>';
						}
						echo '</select>';
						echo IS_Admin::pro_link();
					?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-highlight_terms"><?php echo esc_html( __( 'Highlight Terms', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['highlight_terms'] ) && $settings['highlight_terms'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-highlight_terms" name="'. $id . '[highlight_terms]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-highlight_terms" >' . esc_html__( "Highlight searched terms on search results page.", 'ivory-search' ) . '</label>';
					$color = ( isset( $settings['highlight_color'] ) ) ? $settings['highlight_color'] : '#FFFFB9';
					echo '<br /><br /><input size="10" type="text" id="'. $id . '-highlight_color" name="'. $id . '[highlight_color]" value="' . $color . '" />';
					echo '<label for="'. $id . '-highlight_color" > ' . esc_html__( "Set highlight color.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-term_rel"><?php echo esc_html( __( 'Search Terms Relation', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$term_rel_disable = is_fs()->is_plan( 'pro' ) ? '' : ' disabled ';
					$checked = ( isset( $settings['term_rel'] ) && "OR" === $settings['term_rel'] ) ? "OR" : "AND";
					echo '<p><input type="radio" ' . $term_rel_disable . ' id="'. $id . '-term_rel_or" name="'. $id . '[term_rel]" value="OR" ' . checked( 'OR', $checked, false ) . '/>';
					echo '<label for="'. $id . '-term_rel_or" >' . esc_html__( "OR - Display content having any of the searched terms.", 'ivory-search' ) . '</label>' . IS_Admin::pro_link() . '</p>';
					echo '<p><input type="radio" ' . $term_rel_disable . ' id="'. $id . '-term_rel_and" name="'. $id . '[term_rel]" value="AND" ' . checked( 'AND', $checked, false ) . '/>';
					echo '<label for="'. $id . '-term_rel_and" >' . esc_html__( "AND - Display content having all the searched terms.", 'ivory-search' ) . '</label></p>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-fuzzy_match"><?php echo esc_html( __( 'Fuzzy Matching', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = isset( $settings['fuzzy_match'] ) ? $settings['fuzzy_match'] : '2';
					echo '<p><input type="radio" id="'. $id . '-whole" name="'. $id . '[fuzzy_match]" value="1" ' . checked( '1', $checked, false ) . '/>';
					echo '<label for="'. $id . '-whole" >' . esc_html__( "Whole - Search posts that include the whole search term.", 'ivory-search' ) . '</label></p>';
					echo '<p><input type="radio" id="'. $id . '-partial" name="'. $id . '[fuzzy_match]" value="2" ' . checked( '2', $checked, false ) . '/>';
					echo '<label for="'. $id . '-partial" >' . esc_html__( "Partial - Also search words in the posts that begins or ends with the search term.", 'ivory-search' ) . '</label></p>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-keyword_stem"><?php echo esc_html( __( 'Keyword Stemming', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$stem_disable = is_fs()->is_plan( 'pro_plus' ) ? '' : ' disabled ';
					$checked = ( isset( $settings['keyword_stem'] ) && $settings['keyword_stem'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-keyword_stem" ' . $stem_disable . ' name="'. $id . '[keyword_stem]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-keyword_stem" >' . esc_html__( "Also search base word of searched keyword.", 'ivory-search' ) . '</label>';
					echo IS_Admin::pro_link( 'pro_plus' );
					echo '<br /><label for="'. $id . '-keyword_stem" style="font-size: 10px;clear:both;display:block;">' . esc_html__( "Not recommended to use when Fuzzy Matching option is set to Whole.", 'ivory-search' ) . '</label>';
				?>
				</td>
				<td>
				<?php 
					$title = __( 'Keyword Stemming', 'ivory-search' ); 
					$content = '<p>' . __( 'It also searches base word of searched keyword.', 'ivory-search' ) . '</p>';
					$content .= '<p>' . __( 'For Example: If you search "doing" then it also searches base word of "doing" that is "do" in the posts.', 'ivory-search' ) . '</p>';
					$content .= '<p>' . __( 'If you want to search whole exact searched term then do not use this options and in this case it is not recommended to use when Fuzzy Matching option is set to Whole.', 'ivory-search' ) . '</p>';
					IS_Help::help_tooltip( $title, $content );
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-move_sticky_posts"><?php echo esc_html( __( 'Sticky Posts', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['move_sticky_posts'] ) && $settings['move_sticky_posts'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-move_sticky_posts" name="'. $id . '[move_sticky_posts]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-move_sticky_posts" >' . esc_html__( "Move sticky posts to the start of the search results page.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-empty_search"><?php echo esc_html( __( 'Empty Search', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['empty_search'] ) && $settings['empty_search'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-empty_search" name="'. $id . '[empty_search]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-empty_search" >' . esc_html__( "Display an error for empty search query.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-exclude_from_search"><?php echo esc_html( __( 'Respect exclude_from_search', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['exclude_from_search'] ) && $settings['exclude_from_search'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-exclude_from_search" name="'. $id . '[exclude_from_search]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-exclude_from_search" >' . esc_html__( "Do not search post types which are excluded from search.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-demo"><?php echo esc_html( __( 'Demo', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['demo'] ) && $settings['demo'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-demo" name="'. $id . '[demo]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-demo" >' . esc_html__( "Display search form only for site administrator.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $id; ?>-disable"><?php echo esc_html( __( 'Disable', 'ivory-search' ) ); ?></label>
				</th>
				<td>
				<?php
					$checked = ( isset( $settings['disable'] ) && $settings['disable'] ) ? 1 : 0;
					echo '<input type="checkbox" id="'. $id . '-disable" name="'. $id . '[disable]" value="1" ' . checked( 1, $checked, false ) . '/>';
					echo '<label for="'. $id . '-disable" >' . esc_html__( "Disable this search form.", 'ivory-search' ) . '</label>';
				?>
				</td>
			</tr>
			</tbody>
			</table>
			</fieldset>
			</div>
		<?php
	}
}
