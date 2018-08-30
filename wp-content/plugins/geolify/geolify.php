<?php
/*
* Plugin Name: Geolify
* Description: Geo Targeting and Geolocation
* Version: 1.0
* Author: Geolify
* Author URI: https://geolify.com
*/



//ADMIN MENU---------------------------------------------------------------------


add_action( 'admin_menu', 'geolify_wp_add_admin_menu' );
add_action( 'admin_init', 'geolify_wp_settings_init' );


function geolify_wp_add_admin_menu(  ) { 

	add_menu_page( 'Geolify', 'Geolify', 'manage_options', 'geolify_wp', 'geolify_wp_options_page' );

}


function geolify_wp_settings_init(  ) { 

	register_setting( 'pluginPage', 'geolify_wp_settings' );

	add_settings_section(
		'geolify_wp_pluginPage_section', 
		__( '', 'geolify_wp' ), 
		'geolify_wp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'geolify_wp_javascript_ids', 
		__( 'Geo Javascript IDs (comma separated)', 'geolify_wp' ), 
		'geolify_wp_javascript_ids_render', 
		'pluginPage', 
		'geolify_wp_pluginPage_section' 
	);

	add_settings_field( 
		'geolify_wp_redirect_ids', 
		__( 'Geo Redirect IDs (comma separated)', 'geolify_wp' ), 
		'geolify_wp_redirect_ids_render', 
		'pluginPage', 
		'geolify_wp_pluginPage_section' 
	);

       add_settings_field( 
		'geolify_wp_popup_ids', 
		__( 'Geo Popup IDs (comma separated)', 'geolify_wp' ), 
		'geolify_wp_popup_ids_render', 
		'pluginPage', 
		'geolify_wp_pluginPage_section' 
	);


}


function geolify_wp_javascript_ids_render(  ) { 

	$options = get_option( 'geolify_wp_settings' );
	?>
	<input type='text' name='geolify_wp_settings[geolify_wp_javascript_ids]' value='<?php echo $options['geolify_wp_javascript_ids']; ?>'>
	<?php

}


function geolify_wp_redirect_ids_render(  ) { 

	$options = get_option( 'geolify_wp_settings' );
	?>
	<input type='text' name='geolify_wp_settings[geolify_wp_redirect_ids]' value='<?php echo $options['geolify_wp_redirect_ids']; ?>'>
	<?php

}


function geolify_wp_popup_ids_render(  ) { 

	$options = get_option( 'geolify_wp_settings' );
	?>
	<input type='text' name='geolify_wp_settings[geolify_wp_popup_ids]' value='<?php echo $options['geolify_wp_popup_ids']; ?>'>
	<?php

}



function geolify_wp_settings_section_callback(  ) { 

	echo __( '', 'geolify_wp' );

}


function geolify_wp_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">

<h2>GEOLIFY</h2>



<div class="postbox" style="width:70%; padding:30px;">
<h2>Welcome</h2>
<p>Geolify's Wordpress plugin provides powerful geo targeting and geolocation capabilities to your Wordpress blog. </p>
<p>Geolify will allow you to create URL redirects and popups based on visitor geolocation and also allow you to obtain visitor location data directly in your blog posts.</p>
</div>

<div class="postbox" style="width:70%; padding:30px;">
<h2>Create A Geolify Account</h2>
<p>- Geolify is both a free and paid service due to the quality of location data. See free and paid packs here <a href="https://geolify.com/pricing" target="_blank">https://geolify.com/pricing</a></p>
<p>- Only geo redirects and geo popups are free with Geolify branding. The Geo Javascript requires impressions to be purchased.
<p>- To use this plugin you will need a Geolify Account. Create an account at <a href="https://geolify.com" target="_blank">https://geolify.com</a> </p>
<p>- You will need to log into Geolify at <a href="https://geolify.com" target="_blank">https://geolify.com</a> to set up Geolify's services and purchase 'page views'</p>
<p>- This plugin only provides shortcodes to allow you use Geolify in Wordpress easily.</p>
</div>

<div class="postbox" style="width:70%; padding:30px;">
<h2>Support</h2>
<p>- Live chat with us on our website <a href="https://geolify.com/" target="_blank">https://geolify.com</a></p>
<p>- Email us : contact@geolify.com
</div>



<div class="postbox" style="width:70%; padding:30px;">
<h2>Geo Popup</h2>
<p></br></p>
<p>- The Geo Popup service allows you to display a lightbox popup based on the visitors location (country, state or city)</p>
<p></br></p>
<p><strong>Steps to create a Geo Popup</strong></p>
<p>1. Sign into your Geolify dasboard at <a href="https://geolify.com/login" target="_blank">https://geolify.com/login</a></p>
<p>2. Create a new 'Geo Popup' service </p>
<p>3. Follow the instructions to setup the Geo Popup </p>
<p>4. Paste the Geo Popup ID in the 'Settings' at the bottom of this page to make it active.</p>
<p>5. The ID can be found in the 'Code' section of the Geo Popup page in Geolify</p>
</div>


<div class="postbox" style="width:70%; padding:30px;">
<h2>Geo Redirect</h2>
<p></br></p>
<p>- The Geo Redirect service allows you to redirect visitors by their location (country, state or city)</p>
<p></br></p>
<p><strong>Steps to create a Geo Redirect</strong></p>
<p>1. Sign into your Geolify dasboard at <a href="https://geolify.com/login" target="_blank">https://geolify.com/login</a></p>
<p>2. Create a new 'Geo Redirect' service </p>
<p>3. Follow the instructions to setup the Geo Redirect </p>
<p>4. Paste the Geo Redirect ID in the 'Settings' at the bottom of this page to make it active</p>
<p>5. The ID can be found in the 'Code' section of the Geo Redirect setup page in Geolify</p>
</div>



<div class="postbox" style="width:70%; padding:30px;">
<h2>Geo Javascript</h2>
<p></br></p>
<p>- The Geo Javascript service get visitor location data and display it in your blog using shortcodes</p>
<p></br></p>
<p><strong>Steps to create a Geo Javascript</strong></p>
<p>1. Sign into your Geolify dasboard at <a href="https://geolify.com/login" target="_blank">https://geolify.com/login</a></p>
<p>2. Create a new 'Geo Javascript' service </p>
<p>3. Follow the instructions to setup the Geo Javascript </p>
<p>4. Paste the Geo Javascript ID in the 'Settings' at the bottom of this page to make it active</p>
<p>5. The ID can be found in the 'Code' section of the Geo Javascript setup page in Geolify</p>
<p>5. Use the shortcodes below in your blog post.</p>
<p></br></p>
<p><strong>Available Shortcodes</strong></p>
<p><strong>[geolifycontinentcode]</strong> - Displays continent code eg. NA</p>
<p><strong>[geolifycontinentname]</strong> - Displays continent name eg. North America</p>
<p><strong>[geolifycountrycode]</strong> - Displays country code eg. US</p>
<p><strong>[geolifycountryname]</strong> - Displays country name eg. United States</p>
<p><strong>[geolifystatename]</strong> - Displays state name eg. California</p>
<p><strong>[geolifycityname]</strong> - Displays city name eg. Los Angeles</p>
<p><strong>[geolifylatitude]</strong> - Displays latitude eg. 34.0500</p>
<p><strong>[geolifylongitude]</strong> - Displays longitude eg. 118.2500</p>
</div>





<div class="postbox" style="width:70%; padding:30px;">
<h2>Settings</h2>
<?php
settings_fields( 'pluginPage' );
do_settings_sections( 'pluginPage' );
submit_button();
?>
</div>

</form>

</div>
</div>

<?php

}








//GEO JAVASCRIPT------------------------------------------------------------



//ADD JAVASCRIPT TO WP HEAD



add_action( 'wp_enqueue_scripts', 'geolify_wp_javascript');

function geolify_wp_javascript()
{


$geolify_javascript_ids_database = get_option('geolify_wp_settings');

$geolify_javascript_ids_database_string = preg_replace('/\s+/', '', $geolify_javascript_ids_database['geolify_wp_javascript_ids']);

$geolify_javascript_ids_database_array = explode(',', $geolify_javascript_ids_database_string);

$geolify_javascript_ids_database_array = array_filter($geolify_javascript_ids_database_array);


if (!empty($geolify_javascript_ids_database_array)){

for ($i = 0; $i < count($geolify_javascript_ids_database_array); ++$i) {

wp_enqueue_script( 'geolify-javascript-'.$geolify_javascript_ids_database_array[$i], 'https://www.geolify.com/geojavascript.php?id='.$geolify_javascript_ids_database_array[$i], '', '', false);

        
}

}

}





//GEO REDIRECT------------------------------------------------------------



//ADD REDIRECT TO WP HEAD



add_action( 'wp_enqueue_scripts', 'geolify_wp_redirect');

function geolify_wp_redirect()
{


$geolify_redirect_ids_database = get_option('geolify_wp_settings');

$geolify_redirect_ids_database_string = preg_replace('/\s+/', '', $geolify_redirect_ids_database['geolify_wp_redirect_ids']);

$geolify_redirect_ids_database_array = explode(',', $geolify_redirect_ids_database_string);

$geolify_redirect_ids_database_array = array_filter($geolify_redirect_ids_database_array);


if (!empty($geolify_redirect_ids_database_array)){

for ($i = 0; $i < count($geolify_redirect_ids_database_array); ++$i) {
        

wp_enqueue_script( 'geolify-redirect-'.$geolify_redirect_ids_database_array[$i], 'https://www.geolify.com/georedirect.php?id='.$geolify_redirect_ids_database_array[$i],'' ,'' ,false);

         
}

}

}




//GEO POPUP-----------------------------------------------------------



//ADD REDIRECT TO WP HEAD



add_action( 'wp_enqueue_scripts', 'geolify_wp_popup');

function geolify_wp_popup()
{


$geolify_popup_ids_database = get_option('geolify_wp_settings');

$geolify_popup_ids_database_string = preg_replace('/\s+/', '', $geolify_popup_ids_database['geolify_wp_popup_ids']);

$geolify_popup_ids_database_array = explode(',', $geolify_popup_ids_database_string);

$geolify_popup_ids_database_array = array_filter($geolify_popup_ids_database_array);


if (!empty($geolify_popup_ids_database_array)){

for ($i = 0; $i < count($geolify_popup_ids_database_array); ++$i) {
        

wp_enqueue_script( 'geolify-popup-'.$geolify_popup_ids_database_array[$i], 'https://www.geolify.com/geopopup.php?id='.$geolify_popup_ids_database_array[$i],'' ,'' ,false);

         
}

}

}







//GEO IP
function geolify_wp_ip() {return "<script type='text/javascript'>document.write(geolify_ip());</script>";}
add_shortcode("geolifyip", "geolify_wp_ip");


//GEO CONTINENT CODE
function geolify_wp_continent_code() {return "<script type='text/javascript'>document.write(geolify_continent_code());</script>";}
add_shortcode("geolifycontinentcode", "geolify_wp_continent_code");


//GEO CONTINENT NAME
function geolify_wp_continent_name() {return "<script type='text/javascript'>document.write(geolify_continent_name());</script>";}
add_shortcode("geolifycontinentname", "geolify_wp_continent_name");


//GEO COUNTRY CODE
function geolify_wp_country_code() {return "<script type='text/javascript'>document.write(geolify_country_code());</script>";}
add_shortcode("geolifycountrycode", "geolify_wp_country_code");


//GEO COUNTRY NAME
function geolify_wp_country_name() {return "<script type='text/javascript'>document.write(geolify_country_name());</script>";}
add_shortcode("geolifycountryname", "geolify_wp_country_name");


//GEO STATE NAME
function geolify_wp_state_name() {return "<script type='text/javascript'>document.write(geolify_state_name());</script>";}
add_shortcode("geolifystatename", "geolify_wp_state_name");


//GEO CITY NAME
function geolify_wp_city_name() {return "<script type='text/javascript'>document.write(geolify_city_name());</script>";}
add_shortcode("geolifycityname", "geolify_wp_city_name");


//GEO LATITUDE
function geolify_wp_latitude() {return "<script type='text/javascript'>document.write(geolify_latitude());</script>";}
add_shortcode("geolifylatitude", "geolify_wp_latitude");


//GEO LONGITUDE
function geolify_wp_longitude() {return "<script type='text/javascript'>document.write(geolify_longitude());</script>";}
add_shortcode("geolifylongitude", "geolify_wp_longitude");


//GEO METRO CODE
function geolify_wp_metro_code() {return "<script type='text/javascript'>document.write(geolify_metro_code());</script>";}
add_shortcode("geolifymetrocode", "geolify_wp_metro_code");


//GEO POSTAL CODE
function geolify_wp_postal_code() {return "<script type='text/javascript'>document.write(geolify_postal_code());</script>";}
add_shortcode("geolifypostalcode", "geolify_wp_postal_code");
































?>