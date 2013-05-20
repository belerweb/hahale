<?php
/**
 * Template Name: Home
 * 
 * If you want to set up an alternate home page, just use this template for your page.
 *
 * @package deTube
 * @subpackage Page Template
 * @since deTube 1.0
 */

get_header(); ?>

<?php 
	$args = (array)get_option('dp_home_featured');
	if(!empty($args['posts_per_page'])) {
		if(!empty($args['layout']) && $args['layout'] == 'full-width')
			get_template_part('home-featured-full-width'); 
		else
			get_template_part('home-featured'); 
	}
?>

<div id="main"><div class="wrap cf">
	<div id="content">
	<?php
		// Output home sections based on user's settings
		$sections = get_option('dp_home_sections');
		if(!empty($sections)) {
			foreach($sections as $section_args) {
				dp_section_box($section_args);
			}
		}
	?>
	</div><!-- end #content -->
	
	<?php get_sidebar(); ?>
</div></div><!-- end #main -->

<?php get_footer(); ?>