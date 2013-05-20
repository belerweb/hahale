<?php
/**
 * Sidebar Template
 *
 * @package deTube
 * @subpackage Tempalte
 * @since deTube 1.0
 */
?>

<div id="sidebar" role="complementary">
	<?php 
		if(is_front_page() && is_active_sidebar('home'))
			dynamic_sidebar('home');
		elseif(is_single() && is_video() && is_active_sidebar('single-video'))
			dynamic_sidebar('single-video');
		elseif(is_single() && is_active_sidebar('single-post'))
			dynamic_sidebar('single-post');
		elseif(is_page() && is_active_sidebar('page'))
			dynamic_sidebar('page');
		elseif(is_category() && is_active_sidebar('category'))
			dynamic_sidebar('category');
		elseif(is_author() && is_active_sidebar('author'))
			dynamic_sidebar('author');
		else
			dynamic_sidebar('main');
	?>
</div><!--end #sidebar-->