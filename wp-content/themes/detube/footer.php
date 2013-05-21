<?php
/**
 * Footer Template 
 *
 * The footer template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the bottom of the file. It is used mostly as a closing
 * wrapper, which is opened with the header.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
	<?php do_action( 'dp_before_footer_php' ); ?>
	
	<footer id="footer">
		<?php // Footbar
		$footbar = get_option('dp_footbar_status'); if($footbar) : ?>
		<div id="footbar"><div class="wrap cf">
			<div class="widget-col widget-col-links widget-col-1">
				<?php dynamic_sidebar('footbar-1'); ?>
			</div>
			<div class="widget-col widget-col-links widget-col-2">
				<?php dynamic_sidebar('footbar-2'); ?>
			</div>
			<div class="widget-col widget-col-links widget-col-3">
				<?php dynamic_sidebar('footbar-3'); ?>
			</div>
			<div class="widget-col widget-col-links widget-col-4">
				<?php dynamic_sidebar('footbar-4'); ?>
			</div>
			<div class="widget-col widget-col-5">
				<?php dynamic_sidebar('footbar-5'); ?>
			</div>
		</div></div><!-- end #footbar -->
		<?php endif; ?>

		<div id="colophon" role="contentinfo"><div class="wrap cf">
			<?php // Footer Navigation
				if(get_option('dp_footer_nav_status')) {
					$nav_menu = wp_nav_menu(array('theme_location'=>'footer', 'container'=>'', 'depth'=>1, 'echo'=>0, 'fallback_cb' => '')); 

					// The fallback menu
					if(empty($nav_menu))
						$nav_menu = '<ul>'.wp_list_pages(array('depth'=>1, 'title_li'=>'', 'echo'=>0)).'</ul>';

					echo '<div id="footer-nav">'.$nav_menu.'</div><!-- end #footer-nav -->';
				}
			?>
			
			<?php  // Copyright
				if($copyright = get_option('dp_site_copyright')) 
					printf('<p id="copyright">'.$copyright.'</p>', date('Y'), '<a href="'.home_url().'">'.get_bloginfo('name').'</a>'); 
			?>
			
		</div></div><!-- end #colophon -->
	</footer><!-- end #footer -->
	
</div><!-- end #page -->
	
	<?php wp_footer(); ?>

</body>
</html>