<?php
/**
 * 404 Page Template
 *
 * The template for displaying 404 pages (Not Found).
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */

@header('HTTP/1.1 404 Not Found', true, 404);

get_header(); ?>

<div id="main"><div class="wrap cf">
	
	<div id="content" role="main">
	
		<div id="post-0" class="post post-404 not-found">
			<div id="entry-header">
				<h1 class="entry-title"><?php _e('404 Not Found', 'dp'); ?></h1>
			</div>
			
			<div class="entry-content">
				<p>
					<?php printf(__('<strong>Sorry, but the page you are looking for can not be found.</strong><br />Perhaps you are here beacause the page no longer exists or never exists, or you love 404 page.<br />Please try searching can help or return to <a href="%1$s">homepage</a>.', "dp"), home_url()); ?>
				</p>
			</div>
		</div><!-- #post-0 -->
	
	</div><!-- end #content -->

	<?php get_sidebar(); ?>	
		
</div></div><!-- end #main -->

<?php get_footer(); ?>