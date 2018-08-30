<?php
/*
Template Name: servicios
*/
?>

<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

/*conexion a productos de catalogo*/
$woocommerce = new Client(
    'http://www.catalogoepa.com.ve', 
    'ck_96318ba14ee05ead895c27bbdc2e187140d1f90f', 
    'cs_3f948bb794265d74194347cbd984f644c3c5b4a7',
    [
        'wp_api' => true,
        'version' => 'wc/v2',
    ]
);
$products = $woocommerce->get('products');
$i = 0;


?>



<?php 
global $wp_query, $mkd_options;
$id = $wp_query->get_queried_object_id();
$sidebar = get_post_meta($id, "mkd_show-sidebar", true);  

$enable_page_comments = false;
if(get_post_meta($id, "mkd_enable-page-comments", true) == 'yes') {
	$enable_page_comments = true;
}

if(get_post_meta($id, "mkd_page_background_color", true) != ""){
	$background_color = 'background-color: '.esc_attr(get_post_meta($id, "mkd_page_background_color", true));
}else{
	$background_color = "";
}

$content_style = "";
if(get_post_meta($id, "mkd_content-top-padding", true) != ""){
	if(get_post_meta($id, "mkd_content-top-padding-mobile", true) == 'yes'){
		$content_style = "padding-top:".esc_attr(get_post_meta($id, "mkd_content-top-padding", true))."px !important";
	}else{
		$content_style = "padding-top:".esc_attr(get_post_meta($id, "mkd_content-top-padding", true))."px";
	}
}

$pagination_classes = '';
if( isset($mkd_options['pagination_type']) && $mkd_options['pagination_type'] == 'standard' ) {
	if( isset($mkd_options['pagination_standard_position']) && $mkd_options['pagination_standard_position'] !== '' ) {
		$pagination_classes .= "standard_".esc_attr($mkd_options['pagination_standard_position']);
	}
}
elseif ( isset($mkd_options['pagination_type']) && $mkd_options['pagination_type'] == 'arrows_on_sides' ) {
	$pagination_classes .= "arrows_on_sides";
}

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

?>
	<?php get_header(); ?>
		<?php if(get_post_meta($id, "mkd_page_scroll_amount_for_sticky", true)) { ?>
			<script>
			var page_scroll_amount_for_sticky = <?php echo esc_attr(get_post_meta($id, "mkd_page_scroll_amount_for_sticky", true)); ?>;
			</script>
		<?php } ?>

		<?php get_template_part( 'title' ); ?>
		<?php get_template_part('slider'); ?>





<div class="container hidden-xs">
					 <?php

					$the_query = new WP_Query( array( 'post_type' => 'slider-item' ) );
					 
				    $c = 0; $class = '';

					$taxonomy = 'slideshow_categories';
					if ( $the_query->have_posts() ) {

					    echo '<div id="myCarousel" class=" carousel slide banner-carousel" data-ride="carousel" data-interval="false">
							    <div class="carousel-inner">';

					    while ( $the_query->have_posts() ) {
					        $c++;

					        $the_query->the_post();
					        $post = get_post();

					        $test= get_post_format($post->ID);

					        $terms = get_the_terms( get_the_ID(), $taxonomy);

					        $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
					        
					        $class = ($c == 1) ? 'active' : '';
					       
					        if ($test == "video") {

						        echo ' 

						        <div class="item '.$class .' '.(( $terms && ! is_wp_error( $terms ) )?$terms[0]->slug:" ").'" >
						        <div class="embed-responsive embed-responsive-16by9" >
									<iframe class="embed-responsive-item" src="' .get_field('url_video').'"></iframe> 
								</div>   
								</div>';
					    	}else {

					    		if ($feat_image ){

						        echo ' 
						        <div class="item '.$class .' '.(( $terms && ! is_wp_error( $terms ) )?$terms[0]->slug:" ").'">
									
									<img src="'.(( $feat_image && ! is_wp_error( $feat_image ) )?$feat_image:" ").'" alt="publicidad" class="img-responsive img-banner">
  								
  								<div class="centered-text-banner">'.get_the_title( $post->ID ).'</div>
								
								<a href="'.get_field('url_destino_img').'">
								<button type="button" class="btn-cat button-banner"> '.get_field('texto_enlace').'</button>
								</a>
								</div>
							
								';
								}
					    	}
					    }					       
					    echo '</div> </div>

							';
					} else {
					    // no posts found
					}
					/* Restore original Post Data */
					wp_reset_postdata();

	?>

 <?php

  $terms = get_terms($taxonomy);
echo '
  	<div class="btn-group mybuttoms" role="group" aria-label="Basic example">
	';

     foreach ($terms as $term) {
     	echo '
 			<button type="button" class="btn btn-secondary btn-cat" data-slug= "'. $term->slug.'"><span class="button-slider">'. $term->name.'</span></button>
		';
     }

    echo '
  	</div>
	';
  ?>
</div>


<!-- BANNER RESPONSIVE-->

<div class="container hidden-sm hidden-md hidden-lg">
					 <?php

					$the_query = new WP_Query( array( 'post_type' => 'slider-item' ) );
					$c = 0; $class = '';
					$taxonomy = 'slideshow_categories';
					if ( $the_query->have_posts() ) {

					    echo '<div id="myCarousel2" class=" carousel slide banner-carousel" data-ride="carousel">


							    <div class="carousel-inner">';

					    while ( $the_query->have_posts() ) {
					    	 $c++;

					       $the_query->the_post();
					       $post = get_post();

					       $test= get_post_format($post->ID);

					        $terms = get_the_terms( get_the_ID(), $taxonomy);

					        $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
					        
					        $class = ($c == 1) ? 'active' : '';

					       
					        if ($test != "video") {

						       if ($feat_image ){

						        echo ' 
						        <div class="item '.$class .'">
									
									<img src="'.(( $feat_image && ! is_wp_error( $feat_image ) )?$feat_image:" ").'" alt="publicidad" class="img-responsive img-banner">
  								
  								<div class="centered-text-banner">'.get_the_title( $post->ID ).'</div>
								
								<a href="'.get_field('url_destino_img').'">
								<button type="button" class="btn-cat button-banner"> '.get_field('texto_enlace').'</button>
								</a>
								</div>
							
								';
								}
					    	}
					    }					       
					    echo '</div>

						    <a class="left carousel-control" href="#myCarousel2" data-slide="prev">
						      <span class="glyphicon glyphicon-chevron-left"></span>
						      <span class="sr-only">Previous</span>
						    </a>
						    <a class="right carousel-control" href="#myCarousel2" data-slide="next">
						      <span class="glyphicon glyphicon-chevron-right"></span>
						      <span class="sr-only">Next</span>
						    </a>

					    </div> 

							';
					} else {
					    // no posts found
					}
					/* Restore original Post Data */
					wp_reset_postdata();
	?>


</div>


<!-- CATÁLOGO -->
	<div class="container top-20 top-catalogo-resp">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="section-text">Nuestros productos</h2>
					<p class="sub-section-text">Nuestros productos destacados</p>
				</div>
		    </div>
			<div class="row">
				<div class="col-xs-12">
					<ul id="catalogo">

						<?php foreach ($products as $index=>$productos ){ ?>
					    <li>
							<?php echo '<a href="'.$productos->permalink. '">' .'<img src="' . $productos->images[0]->src .'"' .'"/> </a>'; ?>

							<p class= "top-10 titulo-producto"><?php echo   $productos->name; ?> </p>
							<span class= "top-10 productos-catalogo"><?php echo $productos->short_description; ?> </span>
							 
							<?php if($productos->attributes){  

								foreach ($productos->attributes as $index2=>$value2) {

						  			if($productos->attributes[$index2]->name=="Código EPA:") {

							  			echo "<span class='productos-catalogo'><p class='cod-producto'>".$productos->attributes[$index2]->options[0]."</p></span>";
							  			
							  			}
						  		}
						  	}?>

					    </li> 
					    <?php }   ?>
                                    
					</ul> 
				</div>
			</div>
			<div class="row top-20">
		    <div class="col-xs-12 col-centered">
		      <div class="content-heading" >
			      <a href= "http://www.epa.biz:81/catalogo/"><p>Ver todos los productos</p> </a>
			      <a href= "http://www.epa.biz:81/catalogo/"><img href="" src="<?= get_stylesheet_directory_uri();?>/img/arrow.png"/> </a>
		      </div>
		    </div>
	    </div>
		</div>   
	

	   <!--  End  Catalogo -->
 
		<div class="container"<?php mkd_inline_style($background_color); ?>>

		<div class="header_inner_right" style="margin-right: 120px;">
			<div class="side_menu_button_wrapper right">
				<?php if(is_active_sidebar('header_bottom_right')) { ?>
					<div class="header_bottom_right_widget_holder"><?php dynamic_sidebar('header_bottom_right'); ?></div>
				<?php } ?>
				<?php if(is_active_sidebar('woocommerce_dropdown')) {
					dynamic_sidebar('woocommerce_dropdown');
				} ?>
				<div class="side_menu_button">

				<?php if(isset($mkd_options['enable_search']) && $mkd_options['enable_search'] == 'yes') {
					$search_type_class = 'search_slides_from_top';
					if(isset($mkd_options['search_type']) && $mkd_options['search_type'] !== '') {
						$search_type_class = $mkd_options['search_type'];
					}
					if(isset($mkd_options['search_type']) && $mkd_options['search_type'] == 'search_covers_header') {
						if (isset($mkd_options['search_cover_only_bottom_yesno']) && $mkd_options['search_cover_only_bottom_yesno']=='yes') {
							$search_type_class .= ' search_covers_only_bottom';
						}
					}
					if (isset($mkd_options['search_icon_background_full_height']) && $mkd_options['search_icon_background_full_height'] == 'yes'){
						$search_type_class .= ' search_icon_bckg_full';
					} ?>

					<a class="<?php echo esc_attr($search_type_class); ?> <?php echo esc_attr($header_button_size); ?>" href="javascript:void(0)">
						<?php if(isset($mkd_options['search_icon_pack'])){
							$mkdIconCollections->getSearchIcon($mkd_options['search_icon_pack']);
						} ?>
						<?php if(isset($mkd_options['enable_search_icon_text']) && $mkd_options['enable_search_icon_text'] == 'yes'){?>
							<span class="search_icon_text">
								<?php _e('Search', 'mkd'); ?>
							</span>
						<?php } ?>
					</a>

					<?php if($search_type_class == 'fullscreen_search' && $fullscreen_search_animation=='from_circle'){ ?>
						<div class="fullscreen_search_overlay"></div>
					<?php } ?>

				<?php } ?>

					<?php if($enable_popup_menu == "yes"){ ?>
						<a href="javascript:void(0)" class="popup_menu <?php echo esc_attr($header_button_size.' '.$popup_menu_animation_style); ?>"><span class="popup_menu_inner"><i class="line">&nbsp;</i></span></a>
					<?php } ?>
					<?php if($enable_side_area == "yes" && $enable_popup_menu == 'no'){
						$sidearea_type_class = "";
						if (isset($mkd_options['sidearea_icon_background_full_height']) && $mkd_options['sidearea_icon_background_full_height'] == 'yes'){
							$sidearea_type_class .= ' sidearea_icon_bckg_full';
						}
						?>
						<a class="side_menu_button_link <?php echo esc_attr($header_button_size); echo esc_attr($sidearea_type_class); ?>" href="javascript:void(0)">
						<?php echo mkd_get_side_menu_icon_html(); ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
        <?php if($mkd_options['overlapping_content'] == 'yes') {?>
            <div class="overlapping_content"><div class="overlapping_content_inner">
        <?php } ?>

                <div class="container_inner default_template_holder clearfix" <?php mkd_inline_style($content_style); ?>>
				<?php if(($sidebar == "default")||($sidebar == "")) : ?>
					<!-- banner, catalogo -->
					<?php if (have_posts()) : 
							while (have_posts()) : the_post(); ?>
							<?php the_content(); ?>
							<?php 
								$args_pages = array(
									'before'           => '<div class="single_links_pages ' .$pagination_classes. '"><div class="single_links_pages_inner">',
									'after'            => '</div></div>',
									'pagelink'         => '<span>%</span>'
								);
								wp_link_pages($args_pages);
							?>
							<?php
							if($enable_page_comments){
								comments_template('', true); 
							}
							?> 
							<?php endwhile; ?>
						<?php endif; ?>
						<!-- banner, catalogo2 --><!-- banner, catalogo2 -->
				<?php elseif($sidebar == "1" || $sidebar == "2"): ?>		
					
					<?php if($sidebar == "1") : ?>	
						<div class="two_columns_66_33 background_color_sidebar grid2 clearfix">
							<div class="column1 content_left_from_sidebar">
					<?php elseif($sidebar == "2") : ?>	
						<div class="two_columns_75_25 background_color_sidebar grid2 clearfix">
							<div class="column1 content_left_from_sidebar">
					<?php endif; ?>
							<?php if (have_posts()) : 
								while (have_posts()) : the_post(); ?>
								<div class="column_inner">
								
								<?php the_content(); ?>
								<?php 
									$args_pages = array(
									'before'           => '<div class="single_links_pages ' .$pagination_classes. '"><div class="single_links_pages_inner">',
									'after'            => '</div></div>',
									'pagelink'         => '<span>%</span>'
									);

									wp_link_pages($args_pages);
								?>
								<?php
								if($enable_page_comments){
									comments_template('', true); 
								}
								?> 
								</div>
						<?php endwhile; ?>
						<?php endif; ?>
						
					<!-- FINbanner, catalogo -->
									
							</div>
							<div class="column2"><?php get_sidebar();?></div>
						</div>
					<?php elseif($sidebar == "3" || $sidebar == "4"): ?>
						<?php if($sidebar == "3") : ?>	
							<div class="two_columns_33_66 background_color_sidebar grid2 clearfix">
								<div class="column1"><?php get_sidebar();?></div>
								<div class="column2 content_right_from_sidebar">
						<?php elseif($sidebar == "4") : ?>	
							<div class="two_columns_25_75 background_color_sidebar grid2 clearfix">
								<div class="column1"><?php get_sidebar();?></div>
								<div class="column2 content_right_from_sidebar">
						<?php endif; ?>
								<?php if (have_posts()) : 
									while (have_posts()) : the_post(); ?>
									<div class="column_inner">
										<?php the_content(); ?>
										<?php 
											$args_pages = array(
												'before'           => '<div class="single_links_pages ' .$pagination_classes. '"><div class="single_links_pages_inner">',
												'after'            => '</div></div>',
												'pagelink'         => '<span>%</span>'
											);
											wp_link_pages($args_pages);
										?>
										<?php
										if($enable_page_comments){
											comments_template('', true); 
										}
										?> 
									</div>
							<?php endwhile; ?>
							<?php endif; ?>
										
								</div>
								
							</div>
					<?php endif; ?>
		    </div>
            <?php if($mkd_options['overlapping_content'] == 'yes') {?>
                </div></div>
            <?php } ?>
	    </div>


	<?php get_footer('principal'); ?>