<?php
/**
 * Loop Header Template
 *
 * Displays information at the top of the page about archive and search results when viewing those pages.  
 * This is not shown on the home page and singular views.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
	<?php 
		global $wp_query;
		$loop_title = '';
		$loop_desc = '';
		
		// Get loop title and loop description
		if(is_home() && !is_front_page()) {
			$posts_page_id = get_option('page_for_posts');
			$loop_title = '<em>'.get_the_title($posts_page_id).'</em>';
		} elseif(is_category()) {
			$loop_title = sprintf(__('<span class="prefix">Category:</span> %s', 'dp'), '<em>'.single_cat_title('', false).'</em>');
		} elseif(is_tag()) {
			$loop_title = sprintf(__('<span class="prefix">Tag:</span> %s', 'dp'), '<em>'.single_tag_title('', false).'</em>');
		} elseif(is_author()) {
			$id = get_query_var( 'author' );
			$loop_title = sprintf(__('<span class="prefix">User:</span> %s', 'dp'), '<em>'.get_the_author_meta( 'display_name', $id).'</em>');
		} elseif( is_search()) {
			$loop_title = sprintf(__( '<span class="prefix">Search Results for:</span> %s', 'dp'), '<em>'.esc_attr(get_search_query()).'</em>');
			$loop_desc = sprintf(__( 'About %s results', 'dp'), '<i class="count">'.$wp_query->found_posts.'</i>');
		} elseif(is_archive()) {
			if (is_day()):
				$loop_title = sprintf( __( '<span class="prefix">Daily Archives:</span> %s', 'dp'), '<em>'.get_the_date().'</em>');
			elseif (is_month()):
				$loop_title = sprintf( __( '<span class="prefix">Monthly Archives:</span> %s', 'dp'), '<em>'.get_the_date(_x( 'F Y', 'monthly archives date format', 'dp')).'</em>');
			elseif (is_year()):
				$loop_title = sprintf( __( '<span class="prefix">Yearly Archives:</span> %s', 'dp'), '<em>'.get_the_date(_x( 'Y', 'yearly archives date format', 'dp')).'</em>');
			else :
				$loop_title = '<em>'.__( 'Browse Archives', 'dp' ).'</em>'; 
			endif;
		}
		
		// Output loop title and loop description
		if(!empty($loop_title)) {
			$loop_actions_status = get_option('dp_loop_actions_status');
			$class = (!$loop_actions_status) ? ' below-no-actions' : '';
			echo '
			<div class="loop-header'.$class.'">
				<h1 class="loop-title">'.$loop_title.'</h1>
				<div class="loop-desc">'. $loop_desc.'</div>
			</div><!-- end .loop-header -->';
		}
	?>