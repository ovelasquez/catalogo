<?php

/**
 * Function that checks if option has value from theme options.
 * It first checks global $mkd_options variable and if option does'nt exists there
 * it checks default theme options
 *
 * @param $name string name of the option to retrieve
 * @return bool
 */
function mkdf_option_has_value($name) {
	global $mkd_options;
	global $mkdFramework;

	if (array_key_exists($name, $mkdFramework->mkdOptions->options)) {
		if(isset($mkd_options[$name])) {
			return true;
		} else {
			return false;
		}
	} else {
		global $post;

		$value = get_post_meta( $post->ID, $name, true );

		if (isset($value) && $value !== "") {
			return true;
		} else {
			return false;
		}

	}
}

/**
 * Function that gets option by it's name.
 * It first checks if option exists in $mkd_options global array and if it does'nt exists there
 * it checks default theme options array.
 *
 * @param $name string name of the option to retrieve
 * @return mixed|void
 */
function mkdf_option_get_value($name) {
	global $mkd_options;
	global $mkdFramework;

	if (array_key_exists($name, $mkdFramework->mkdOptions->options)) {
		if(isset($mkd_options[$name])){
			return $mkd_options[$name];
		} else {
			return $mkdFramework->mkdOptions->getOption($name);
		}
	} else {
		global $post;

		$value = get_post_meta( $post->ID, $name, true );
		if (isset($value) && $value !== "") {
			return $value;
		} else {
			return $mkdFramework->mkdMetaBoxes->getOption($name);
		}

	}
}

/**
 * Function that generates thumbnail filename based on original filename and given dimensions
 * @param $file string original filename
 * @param $w int width of thumbnail
 * @param $h int height of thumbnail
 * @return string generated thumbnail filename
 */
function mkdf_generate_filename( $file, $w, $h ){
	$info         = pathinfo( $file );
	$dir = "";
	if(!empty($info['dirname'])){
		$dir          = $info['dirname'];	
	}
	$ext = "";
	$name = "";
	if(!empty($info['extension'])){
		$ext          = $info['extension'];
		$name         = wp_basename( $file, ".$ext" );
	}
	
	$suffix       = "{$w}x{$h}";

	if (mkdf_url_exists("{$dir}/{$name}-{$suffix}.{$ext}"))
		return "{$dir}/{$name}-{$suffix}.{$ext}";
	else
		return $file;
}

/**
 * Function that checks if url exists
 * @param $url string url to check
 * @return bool
 */
function mkdf_url_exists($url){
    $url = str_replace("http://", "", $url);
    if (strstr($url, "/")) {
        $url = explode("/", $url, 2);
        $url[1] = "/".$url[1];
    } else {
        $url = array($url, "/");
    }

    $fh = fsockopen($url[0], 80);
    if ($fh) {
        fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
        if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
        else { return TRUE;    }

    } else { return FALSE;}
}

/**
 * Function that gets attachment thumbnail url from attachment url
 * @param $attachment_url string url of the attachment
 * @return bool|string
 *
 * @see mkd_get_attachment_id_from_url()
 */
function mkdf_get_attachment_thumb_url($attachment_url) {
	$attachment_id = mkd_get_attachment_id_from_url($attachment_url);

	if(!empty($attachment_id)) {
		return wp_get_attachment_thumb_url($attachment_id);
	} else {
		return $attachment_url;
	}
}

/**
 * Function that enqueue skin style. Wrapper around wp_enqueue_style function,
 * it prepends $src with skin path
 * @param $handle string unique key for style
 * @param $src string path inside skin folder
 * @param array $deps array of handles that style will depend on
 * @param bool|string $ver whether to add version string or not.
 * @param string $media media for which to add style. Defaults to 'all'
 *
 * @see wp_enqueue_style()
 */
function mkd_enqueue_skin_style($handle, $src, $deps = array(), $ver = false, $media = 'all') {
	global $mkdFramework;

	$src = get_template_directory_uri().'/mkdinc/admin/skins/'.$mkdFramework->getSkin().'/'.$src;
	wp_enqueue_style($handle, $src, $deps, $ver, $media);
}

/**
 * Function that registers skin style. Wrapper around wp_register_style function,
 * it prepends $src with skin path
 * @param $handle string unique key for style
 * @param $src string path inside skin folder
 * @param array $deps array of handles that style will depend on
 * @param bool|string $ver whether to add version string or not.
 * @param string $media media for which to add style. Defaults to 'all'
 *
 * @see wp_register_style()
 */
function mkd_register_skin_style($handle, $src, $deps = array(), $ver = false, $media = 'all') {
	global $mkdFramework;

	$src = get_template_directory_uri().'/mkdinc/admin/skins/'.$mkdFramework->getSkin().'/'.$src;
	wp_register_style($handle, $src, $deps = array(), $ver = false, $media = 'all');
}

/**
 * Function that enqueue skin script. Wrapper around wp_enqueue_script function,
 * it prepends $src with skin path
 * @param $handle string unique key for style
 * @param $src string path inside skin folder
 * @param array $deps array of handles that style will depend on
 * @param bool|string $ver whether to add version string or not.
 * @param bool $in_footer whether to include script in footer or not.
 *
 * @see wp_enqueue_script()
 */
function mkd_enqueue_skin_script($handle, $src, $deps = array(), $ver = false, $in_footer = false) {
	global $mkdFramework;

	$src = get_template_directory_uri().'/mkdinc/admin/skins/'.$mkdFramework->getSkin().'/'.$src;
	wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
}

/**
 * Function that registers skin script. Wrapper around wp_register_script function,
 * it prepends $src with skin path
 * @param $handle string unique key for style
 * @param $src string path inside skin folder
 * @param array $deps array of handles that style will depend on
 * @param bool|string $ver whether to add version string or not.
 * @param bool $in_footer whether to include script in footer or not.
 *
 * @see wp_register_script()
 */
function mkd_register_skin_script($handle, $src, $deps = array(), $ver = false, $in_footer = false) {
	global $mkdFramework;

	$src = get_template_directory_uri().'/mkdinc/admin/skins/'.$mkdFramework->getSkin().'/'.$src;
	wp_register_script($handle, $src, $deps, $ver, $in_footer);
}

if (!function_exists('mkd_generate_dynamic_css_and_js')){
	/**
	 * Function that gets content of dynamic assets files and puts that in static ones
	 */
	function mkd_generate_dynamic_css_and_js() {

		$mkd_options = get_option('mkd_options_onyx');
		if(mkd_is_css_folder_writable()) {
			$css_dir = get_template_directory().'/css/';

			ob_start();
			include_once($css_dir.'style_dynamic.php');
			$css = ob_get_clean();
			file_put_contents($css_dir.'style_dynamic.css', $css, LOCK_EX);

			ob_start();
			include_once($css_dir.'style_dynamic_responsive.php');
			$css = ob_get_clean();
			file_put_contents($css_dir.'style_dynamic_responsive.css', $css, LOCK_EX);

			ob_start();
			include_once($css_dir.'custom_css.php');
			$css = ob_get_clean();
			file_put_contents($css_dir.'custom_css.css', $css, LOCK_EX);
		}

		if(mkd_is_js_folder_writable()) {
			$js_dir = get_template_directory().'/js/';

			ob_start();
			include_once($js_dir.'default_dynamic.php');
			$js = ob_get_clean();
			file_put_contents($js_dir.'default_dynamic.js', $js, LOCK_EX);

			ob_start();
			include_once($js_dir.'custom_js.php');
			$js = ob_get_clean();
			file_put_contents($js_dir.'custom_js.js', $js, LOCK_EX);
		}
	}

	if(!is_multisite()) {
		add_action('mkd_after_theme_option_save', 'mkd_generate_dynamic_css_and_js');
	}
}

if (!function_exists('mkd_gallery_upload_get_images')) {
	/**
	 * Function that outputs single image html that is used in multiple image upload field
	 * Hooked to wp_ajax_mkd_gallery_upload_get_images action
	 */
	function mkd_gallery_upload_get_images(){
		$ids=$_POST['ids'];
		$ids=explode(",",$ids);
		foreach($ids as $id):
			$image = wp_get_attachment_image_src($id,'thumbnail', true);
			echo '<li class="mkd-gallery-image-holder"><img src="'.esc_url($image[0]).'"/></li>';
		endforeach;
		exit;
	}

	add_action( 'wp_ajax_mkd_gallery_upload_get_images', 'mkd_gallery_upload_get_images');
}

if (!function_exists('mkd_hex2rgb')) {
	/**
	 * Function that transforms hex color to rgb color
	 * @param $hex string original hex string
	 * @return array array containing three elements (r, g, b)
	 */
	function mkd_hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}
}

if(!function_exists('mkd_addslashes')) {
	/**
	 * Function that checks if magic quotes are turned on (for older versions of php) and returns escaped string
	 * @param $str string string to be escaped
	 * @return string escaped string
	 */
	function mkd_addslashes($str) {
		//is magic quotes turned off in php.ini?
		if(!get_magic_quotes_gpc()) {
			//apply addslashes
			$str = addslashes($str);
		}

		//return escaped string
		return $str;
	}
}

if(!function_exists('mkd_get_attachment_meta')) {
	/**
	 * Function that returns attachment meta data from attachment id
	 * @param $attachment_id
	 * @param array $keys sub array of attachment meta
	 * @return array|mixed
	 */
	function mkd_get_attachment_meta($attachment_id, $keys = array()) {
		$meta_data = array();

		//is attachment id set?
		if(!empty($attachment_id)) {
			//get all post meta for given attachment id
			$meta_data = get_post_meta($attachment_id, '_wp_attachment_metadata', true);

			//is subarray of meta array keys set?
			if(is_array($keys) && count($keys)) {
				$sub_array = array();

				//for each defined key
				foreach($keys as $key) {
					//check if that key exists in all meta array
					if(array_key_exists($key, $meta_data)) {
						//assign key from meta array for current key to meta subarray
						$sub_array[$key] = $meta_data[$key];
					}
				}

				//we want meta array to be subarray because that is what used whants to get
				$meta_data = $sub_array;
			}
		}

		//return meta array
		return $meta_data;
	}
}

if(!function_exists('mkd_get_attachment_id_from_url')) {
	/**
	 * Function that retrieves attachment id for passed attachment url
	 * @param $attachment_url
	 * @return null|string
	 */
	function mkd_get_attachment_id_from_url($attachment_url) {
		global $wpdb;
		$attachment_id = '';

		//is attachment url set?
		if($attachment_url !== '') {
			//prepare query

			$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid=%s", $attachment_url);

			//get attachment id
			$attachment_id = $wpdb->get_var($query);
		}

		//return id
		return $attachment_id;
	}
}

if(!function_exists('mkd_get_attachment_meta_from_url')) {
	/**
	 * Function that returns meta array for give attachment url
	 * @param $attachment_url
	 * @param array $keys sub array of attachment meta
	 * @return array|mixed
	 *
	 * @see mkd_get_attachment_id_from_url()
	 * @see mkd_get_attachment_meta()
	 *
	 * @version 0.1
	 */
	function mkd_get_attachment_meta_from_url($attachment_url, $keys = array()) {
		$attachment_meta = array();

		//get attachment id for attachment url
		$attachment_id 	= mkd_get_attachment_id_from_url($attachment_url);

		//is attachment id set?
		if(!empty($attachment_id)) {
			//get post meta
			$attachment_meta = mkd_get_attachment_meta($attachment_id, $keys);
		}

		//return post meta
		return $attachment_meta;
	}
}

if(!function_exists('mkd_get_image_dimensions')) {
	/**
	 * Function that returns image sizes array. First looks in post_meta table if attachment exists in the database,
	 * if it doesn't than it uses getimagesize PHP function to get image sizes
	 * @param $url string url of the image
	 * @return array array of image sizes that containes height and width
	 *
	 * @see mkd_get_attachment_meta_from_url()
	 * @uses getimagesize
	 *
	 * @version 0.1
	 */
	function mkd_get_image_dimensions($url) {
		$image_sizes = array();

		//is url passed?
		if($url !== '') {
			//get image sizes from posts meta if attachment exists
			$image_sizes = mkd_get_attachment_meta_from_url($url, array('width', 'height'));

			//image does not exists in post table, we have to use PHP way of getting image size
			if(!count($image_sizes)) {
				//can we open file by url?
				if(ini_get('allow_url_fopen') == 1 && file_exists($url)) {
					list($width, $height, $type, $attr) = getimagesize($url);
				} else {
					//we can't open file directly, have to locate it with relative path.
					$image_obj = parse_url($url);
					$image_relative_path = $_SERVER['DOCUMENT_ROOT'].$image_obj['path'];

					if(file_exists($image_relative_path)) {
						list($width, $height, $type, $attr) = getimagesize($image_relative_path);
					}
				}

				//did we get width and height from some of above methods?
				if(isset($width) && isset($height)) {
					//set them to our image sizes array
					$image_sizes = array(
						'width' => $width,
						'height' => $height
					);
				}
			}
		}

		return $image_sizes;
	}
}

if(!function_exists('mkd_get_native_fonts_list')) {
	/**
	 * Function that returns array of native fonts
	 * @return array
	 */
	function mkd_get_native_fonts_list(){

		return array(
			'Arial',
			'Arial Black',
			'Comic Sans MS',
			'Courier New',
			'Georgia',
			'Impact',
			'Lucida Console',
			'Lucida Sans Unicode',
			'Palatino Linotype',
			'Tahoma',
			'Times New Roman',
			'Trebuchet MS',
			'Verdana');

	}
}

if(!function_exists('mkd_get_native_fonts_array')) {
	/**
	 * Function that returns formatted array of native fonts
	 *
	 * @uses mkd_get_native_fonts_list()
	 * @return array
	 */
	function mkd_get_native_fonts_array(){
		$native_fonts_list = mkd_get_native_fonts_list();
		$native_font_index = 0;
		$native_fonts_array = array();

		foreach($native_fonts_list as $native_font){
			$native_fonts_array[$native_font_index] = array('family' => $native_font);
			$native_font_index++;
		}

		return $native_fonts_array;
	}
}

if(!function_exists('mkd_is_native_font')) {
	/**
	 * Function that checks if given font is native font
	 * @param $font_family string
	 * @return bool
	 */
	function mkd_is_native_font($font_family) {
		return  in_array(str_replace('+', ' ', $font_family), mkd_get_native_fonts_list());
	}
}


if(!function_exists('mkd_merge_fonts')) {
	/**
	 * Function that merge google and native fonts
	 *
	 * @uses mkd_get_native_fonts_array()
	 * @return array
	 */
	function mkd_merge_fonts() {
		global $mkd_fonts_array;
		$native_fonts = mkd_get_native_fonts_array();

		if(is_array($native_fonts) && count($native_fonts)){
			$mkd_fonts_array = array_merge($native_fonts, $mkd_fonts_array);
		}
	}

	add_action('admin_init', 'mkd_merge_fonts');
}

if(!function_exists('mkd_is_css_folder_writable')) {
	/**
	 * Function that checks if css folder is writable
	 * @return bool
	 *
	 * @version 0.1
	 * @uses is_writable()
	 */
	function mkd_is_css_folder_writable() {
		$css_dir = get_template_directory().'/css';

		return is_writable($css_dir);
	}
}

if(!function_exists('mkd_is_js_folder_writable')) {
	/**
	 * Function that checks if js folder is writable
	 * @return bool
	 *
	 * @version 0.1
	 * @uses is_writable()
	 */
	function mkd_is_js_folder_writable() {
		$js_dir = get_template_directory().'/js';

		return is_writable($js_dir);
	}
}

if(!function_exists('mkd_assets_folders_writable')) {
	/**
	 * Function that if css and js folders are writable
	 * @return bool
	 *
	 * @version 0.1
	 * @see mkd_is_css_folder_writable()
	 * @see mkd_is_js_folder_writable()
	 */
	function mkd_assets_folders_writable() {
		return mkd_is_css_folder_writable() && mkd_is_js_folder_writable();
	}
}

if(!function_exists('mkd_writable_assets_folders_notice')) {
	/**
	 * Function that prints notice that css and js folders aren't writable. Hooks to admin_notices action
	 *
	 * @version 0.1
	 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 */
	function mkd_writable_assets_folders_notice() {
		global $pagenow;

		$is_theme_options_page = isset($_GET['page']) && strstr($_GET['page'], 'mkd_theme_menu');

		if($pagenow === 'admin.php' && $is_theme_options_page) {
			if(!mkd_assets_folders_writable()) { ?>
				<div class="error">
					<p><?php _e('Note that writing permissions aren\'t set for folders containing css and js files on your server.
					We recommend setting writing permissions in order to optimize your site performance.
					For further instructions, please refer to our documentation.', 'mkd'); ?></p>
				</div>
			<?php }
		}
	}

	add_action('admin_notices', 'mkd_writable_assets_folders_notice');
}

if(!function_exists('mkd_inline_style')) {
	/**
	 * Function that echoes generated style attribute
	 * @param $value string | array attribute value
	 *
	 * @see mkd_get_inline_style()
	 */
	function mkd_inline_style($value) {
		echo mkd_get_inline_style($value);
	}
}

if(!function_exists('mkd_get_inline_style')) {
	/**
	 * Function that generates style attribute and returns generated string
	 * @param $value string | array value of style attribute
	 * @return string generated style attribute
	 *
	 * @see mkd_get_inline_style()
	 */
	function mkd_get_inline_style($value) {
		return mkd_get_inline_attr($value, 'style', ';');
	}
}

if(!function_exists('mkd_class_attribute')) {
	/**
	 * Function that echoes class attribute
	 * @param $value string value of class attribute
	 *
	 * @see mkd_get_class_attribute()
	 */
	function mkd_class_attribute($value) {
		echo mkd_get_class_attribute($value);
	}
}

if(!function_exists('mkd_get_class_attribute')) {
	/**
	 * Function that returns generated class attribute
	 * @param $value string value of class attribute
	 * @return string generated class attribute
	 *
	 * @see mkd_get_inline_attr()
	 */
	function mkd_get_class_attribute($value) {
		return mkd_get_inline_attr($value, 'class');
	}
}

if(!function_exists('mkd_get_inline_attr')) {
	/**
	 * Function that generates html attribute
	 * @param $value string | array value of html attribute
	 * @param $attr string name of html attribute to generate
	 * @param $glue string glue with which to implode $attr. Used only when $attr is array
	 * @return string generated html attribute
	 */
	function mkd_get_inline_attr($value, $attr, $glue = '') {
		if(!empty($value)) {

			if(is_array($value) && count($value)) {
				$properties = implode($glue, $value);
			} elseif($value !== '') {
				$properties = $value;
			}

			return $attr.'="'.esc_attr($properties).'"';
		}

		return '';
	}
}

if(!function_exists('mkd_get_skin_uri')) {
    /**
     * Returns current skin URI
     * @return mixed
     */
    function mkd_get_skin_uri() {
		global $mkdFramework;

		$current_skin = $mkdFramework->getSkin();

		return $current_skin->getSkinURI();
	}
}

if(!function_exists('mkd_core_installed')) {
    /**
     * Checks if core plugin is installed
     * @return bool
     */
    function mkd_core_installed() {
        return defined('MIKADO_CORE_VERSION');
    }
}

if(!function_exists('mkd_core_plugin_message')) {
    /**
     * Function that prints a mesasge in the admin if user hides TGMPA plugin activation message
     */
    function mkd_core_plugin_message() {
        if(get_user_meta(get_current_user_id(), 'tgmpa_dismissed_notice', true) && !mkd_core_installed()) {
            echo apply_filters('mkd_core_plugin_message', '<div class="update-nag">'.__('Installation of the "Mikado
            Core" plugin is
            essential for
            proper
            theme functioning. Please <a href="'.admin_url('themes.php?page=install-required-plugins')
                    .'">install</a> this
            plugin and activate it', 'mkd').'</div>');
        }
    }

    add_action('admin_notices', 'mkd_core_plugin_message');
}

if(!function_exists('mkd_get_theme_info_item')) {
    /**
     * Returns desired info of the current theme
     * @param $item string info item to get
     * @return string
     */
    function mkd_get_theme_info_item($item) {
        if($item !== '') {
            $current_theme = wp_get_theme();

            if($current_theme->parent()) {
                $current_theme = $current_theme->parent();
            }

            if($current_theme->exists() && $current_theme->get($item) != "") {
                return $current_theme->get($item);
            }
        }

        return '';
    }
}