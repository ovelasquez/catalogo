<?php
/*
Template Name: Blog Masonry
*/
?>
<?php get_header(); ?>
<?php
global $wp_query;
global $mkd_template_name;
global $mkd_page_id;
$mkd_page_id = $wp_query->get_queried_object_id();
$id = $wp_query->get_queried_object_id();
$mkd_template_name = get_page_template_slug($id);
$category = get_post_meta($id, "mkd_choose-blog-category", true);
$post_number = esc_attr(get_post_meta($id, "mkd_show-posts-per-page", true));
if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }
$page_object = get_post( $id );
$mkd_content = $page_object->post_content;

$blog_content_position = "content_above_blog_list";
if(isset($mkd_options['blog_masonry_content_position'])){
	$blog_content_position = $mkd_options['blog_masonry_content_position'];
}

$sidebar = get_post_meta($id, "mkd_show-sidebar", true);

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

if(isset($mkd_options['blog_masonry_number_of_chars'])&& $mkd_options['blog_masonry_number_of_chars'] != "") {
	mkd_set_blog_word_count(esc_attr($mkd_options['blog_masonry_number_of_chars']));
}

$category_filter = "no";
if(isset($mkd_options['blog_masonry_filter'])){
	$category_filter = $mkd_options['blog_masonry_filter'];
}

?>

	<?php if(get_post_meta($id, "mkd_page_scroll_amount_for_sticky", true)) { ?>
		<script>
		var page_scroll_amount_for_sticky = <?php echo esc_attr(get_post_meta($id, "mkd_page_scroll_amount_for_sticky", true)); ?>;
		</script>
	<?php } ?>

	<?php get_template_part( 'title' ); ?>
	<?php get_template_part('slider'); ?>

	<?php
		if(isset($mkd_options['blog_page_range']) && $mkd_options['blog_page_range'] != ""){
			$blog_page_range = esc_attr($mkd_options['blog_page_range']);
		} else{
			$blog_page_range = $wp_query->max_num_pages;
		}
	?>
	<div class="container" <?php mkd_inline_style($background_color); ?>>
		<?php if($mkd_options['overlapping_content'] == 'yes') {?>
			<div class="overlapping_content"><div class="overlapping_content_inner">
		<?php } ?>
		<div class="container_inner default_template_holder clearfix" <?php mkd_inline_style($content_style); ?>>
			<?php if(($sidebar == "default")||($sidebar == "")) : ?>
				<?php
					echo apply_filters('the_content', wp_kses_post($mkd_content));
					query_posts('post_type=post&paged='. $paged . '&cat=' . $category .'&posts_per_page=' . $post_number );
					get_template_part('templates/blog/blog', 'structure');
				?>
			<?php elseif($sidebar == "1" || $sidebar == "2"): ?>
				<?php
					if($blog_content_position != "content_above_blog_list"){
						echo apply_filters('the_content', wp_kses_post($mkd_content));
					}
				?>
				<div class="<?php if($sidebar == "1"):?>two_columns_66_33<?php elseif($sidebar == "2") : ?>two_columns_75_25<?php endif; ?> clearfix grid2 background_color_sidebar">
					<div class="column1 content_left_from_sidebar">
						<div class="column_inner">
							<?php
							if($blog_content_position == "content_above_blog_list"){
								echo apply_filters('the_content', wp_kses_post($mkd_content));
							}
							query_posts('post_type=post&paged='. $paged . '&cat=' . $category .'&posts_per_page=' . $post_number );
							get_template_part('templates/blog/blog', 'structure');
							?>
						</div>
					</div>
					<div class="column2">
						<?php get_sidebar(); ?>
					</div>
				</div>
			<?php elseif($sidebar == "3" || $sidebar == "4"): ?>
				<?php
					if($blog_content_position != "content_above_blog_list"){
						echo apply_filters('the_content', wp_kses_post($mkd_content));
					}
				?>
				<div class="<?php if($sidebar == "3"):?>two_columns_33_66<?php elseif($sidebar == "4") : ?>two_columns_25_75<?php endif; ?> grid2 clearfix background_color_sidebar">
					<div class="column1">
						<?php get_sidebar(); ?>
					</div>
					<div class="column2 content_right_from_sidebar">
						<div class="column_inner">
							<?php
								if($blog_content_position == "content_above_blog_list"){
								echo apply_filters('the_content', wp_kses_post($mkd_content));
								}
								query_posts('post_type=post&paged='. $paged . '&cat=' . $category .'&posts_per_page=' . $post_number );
								get_template_part('templates/blog/blog', 'structure');
							?>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php if(ICL_LANGUAGE_CODE=='es'): ?>
				<div class="vc_row wpb_row section vc_row-fluid grid_section" style=" padding-top:20px; padding-bottom:20px; text-align:left;">
					<div class=" section_inner clearfix">
						<div class="section_inner_margin clearfix">
							<div class="vc_col-sm-12 wpb_column vc_column_container ">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo do_shortcode('[gview file="http://www.epa.biz/wp-content/uploads/2015/07/Balance2014-corregido-version-3.pdf"]'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="vc_row wpb_row section vc_row-fluid grid_section" style=" padding-top:20px; padding-bottom:20px; text-align:left;">
					<div class=" section_inner clearfix">
						<div class="section_inner_margin clearfix">
							<div class="vc_col-sm-12 wpb_column vc_column_container ">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo do_shortcode('[gview file="http://www.epa.biz/wp-content/uploads/2015/07/EPA-Accion-social-2011-2102.pdf"]'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="vc_row wpb_row section vc_row-fluid grid_section" style=" padding-top:20px; padding-bottom:20px; text-align:left;">
					<div class=" section_inner clearfix">
						<div class="section_inner_margin clearfix">
							<div class="vc_col-sm-12 wpb_column vc_column_container ">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo do_shortcode('[gview file="http://www.epa.biz/wp-content/uploads/2015/07/EPA-Accion-Social-2010-2011.pdf"]'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php elseif(ICL_LANGUAGE_CODE=='cr' || ICL_LANGUAGE_CODE=='esv'): ?>

			<?php endif;?>
		</div>
		<?php if($mkd_options['overlapping_content'] == 'yes') {?>
			</div></div>
		<?php } ?>
	</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>