<?php
/**
 * The template for displaying videos in section box or on archive pages
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
	<div id="post-<?php the_ID(); ?>" <?php $item_format = is_video() ? 'video' : 'post'; post_class('item cf item-'.$item_format); ?>>
		<?php
			// Set image size based on section view, only for section box
			global $section_view;
			$thumb_size = 'custom-medium';
			if(!empty($section_view)) {
				if($section_view == 'list-large')
					$thumb_size = 'custom-large';
				elseif($section_view == 'grid-mini')
					$thumb_size = 'custom-small';
			}
			dp_thumb_html($thumb_size);
		?>
			
		<div class="data">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'dp'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
			
			<p class="entry-meta">
				<span class="author vcard">
				<?php printf( '<a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						esc_attr( sprintf( __( 'View all posts by %s', 'dp' ), get_the_author() ) ),
					get_the_author());
				?>
				</span>
				
				<time class="entry-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php printf(__('%s ago', 'dp'), human_time(get_the_time('U'))); ?></time></a>
			</p>
					
			<p class="stats"><?php echo dp_get_post_stats(); ?></p>

			<p class="entry-summary"><?php dp_excerpt(); ?></p>
		</div>
	</div><!-- end #post-<?php the_ID(); ?> -->