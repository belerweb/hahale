<?php 
/**
 * Page Template
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */

get_header(); ?>

<div id="main"><div class="wrap cf">

	<div id="content" role="main">
	
		<?php while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1 class="page-title"><?php the_title(); ?></h1>

			<div class="page-content rich-content">
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><strong>'.__('Pages:', 'dp').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div><!--end .hentry-->
		<?php endwhile; ?>
		
	</div><!--end #content-->
	
	<?php get_sidebar(); ?>

</div></div><!-- end #main -->

<?php get_footer(); ?>