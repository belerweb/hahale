<?php
/**
 * Loop Navigation Template
 *
 * The template displays the loop navigation on archive pages.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
		<?php global $wp_query; if($wp_query->max_num_pages > 1) { ?>
			<div class="loop-nav pag-nav">
			<div class="loop-nav-inner">
				<?php 
				if(function_exists('wp_pagenavi')) {
					wp_pagenavi();
				} else {
					$label = __('&laquo; Prev', 'dp');
					if($prev = get_previous_posts_link($label))
						echo str_replace('<a', '<a clas="prev"', $prev);
					else
						echo '<span class="prev">'.$label.'</span>';

					$label = __('Next &raquo;', 'dp');
					if($next = get_next_posts_link($label))
						echo str_replace('<a', '<a class="next"', $next);
					else
						echo '<span class="next">'.$label.'</span>';
				} ?>
			</div>
			</div><!-- end .loop-nav -->
		<?php } ?>