<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the top of the file. It is used mostly as an opening
 * wrapper, which is closed with the footer.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]><html class="ie ie6 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 7]><html class="ie ie7 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="ie ie8 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 9]><html class="ie ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head>

<!-- Meta Tags -->
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!-- Title, Keywords and Description -->
<title><?php dp_document_title(); ?></title>
<?php dp_meta_keywords(); ?>
<?php dp_meta_description(); ?>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php if($favicon = get_option('dp_favicon')) echo '<link rel="shortcut icon" href="'.$favicon.'" />'."\n"; ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- Styles ans Scripts -->
<link href='http://fonts.googleapis.com/css?family=Arimo:400,700|Droid+Serif:400,700|Open+Sans:600,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
<?php if(get_option('dp_responsive')) { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/responsive.css" type="text/css" media="all" />
<?php } ?>
<?php wp_head(); ?>
<?php
// Generate CSS Style based on user's settings on Theme Options page
$css = '';

$bgpat = get_option('dp_bgpat');
$bgcolor = get_option('dp_bgcolor');
if($bgpat) {
	$preset_bgpat = get_option('dp_preset_bgpat');
	$custom_bgpat = get_option('dp_custom_bgpat'); 
	$bgpat = !empty($custom_bgpat) ? $custom_bgpat : $preset_bgpat;
	$bgpat = $bgpat ? 'url("'.$bgpat.'")' : '';
	$bgpat = apply_filters('dp_bgpat', $bgpat);
	
	$bgrep = get_option('dp_bgrep');
	$bgatt = get_option('dp_bgatt');
	$bgfull = get_option('dp_bgfull');
	$bgpos = 'center top';
	$bgsize = '';
	if($bgfull) {
		$bgrep = 'no-repeat';
		$bgatt = 'fixed';
		$bgsize .= '-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;';
	}

	$css .= "body{background:".implode(' ', array_filter(array($bgcolor,$bgpat,$bgrep,$bgpos,$bgatt))).";".$bgsize."}\n";
} else {
	$css .= 'body{background:'.$bgcolor.'}';
}

if(!empty($css)) {
	echo "\n<!-- Generated CSS BEGIN -->\n<style type='text/css'>\n";
	echo $css;
	echo "\n</style>\n<!-- Generated CSS END -->\n";
}
?>
</head>

<body <?php body_class(); ?>>

<div id="page">
<header id="header"><div class="wrap cf">
	<div id="branding" class="<?php echo get_option('dp_logo_type', 'text'); ?>-branding" role="banner">
		<?php if(is_front_page()) { ?>
			<h1 id="site-title"><a rel="home" href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<?php } else { ?>
			<div id="site-title"><a rel="home" href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></div>
		<?php } ?>
		
		<?php if (get_option('dp_logo_type') == 'image' && $logo = get_option('dp_logo')) { ?>
			<a id="site-logo" rel="home" href="<?php echo home_url(); ?>"><img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
		<?php } ?>
		
		<?php if(is_front_page()) { ?>
			<h2 id="site-description"<?php if(!get_option('dp_site_description')) echo ' class="hidden"'; ?>><?php bloginfo('description'); ?></h2>
		<?php } else { ?>
			<div id="site-description"<?php if(!get_option('dp_site_description')) echo ' class="hidden"'; ?>><?php bloginfo('description'); ?></div>
		<?php } ?>
	</div><!-- end #branding -->
	
	<?php if(is_user_logged_in()) {  
		$user_id = get_current_user_id();
		$current_user = wp_get_current_user();
		$profile_url = get_author_posts_url($user_id);
		$edit_profile_url  = get_edit_profile_url($user_id);
		$current_url = get_current_url();
		?>
		<div id="account-nav" class="user-nav">
			<a class="dropdown-handle" href="<?php echo $profile_url; ?>">
				<?php echo get_avatar( $user_id, 32 ); ?>
				<span class="display-name btn">
					<span class="arrow-down"><?php echo $current_user->display_name; ?></span> 
					<i class="mini-arrow-down"></i>
				</span>
			</a>
					
			<div class="dropdown-content">
				<ul class="dropdown-content-inner">
					<li><a class="profile-link" href="<?php echo $profile_url; ?>"><?php _e('Profile', 'dp'); ?></a></li>
					<li><a class="account-link" href="<?php echo $edit_profile_url; ?>"><?php _e('Account', 'dp'); ?></a></li>
					<li><a class="logout-link" href="<?php echo esc_url(wp_logout_url($current_url)); ?>"><?php _e('Log out', 'dp'); ?></a></li>
				</ul>
			</div>
		</div><!-- end #account-nav -->
	<?php } elseif(get_option('users_can_register')) { ?>
		<div id="login-nav" class="user-nav">
			<a class="btn btn-green register-link" href="<?php echo site_url('wp-login.php?action=register', 'login'); ?>"><?php _e('Sign up', 'dp'); ?></a>
				
			<div class="dropdown">
				<a class="dropdown-handle btn btn-black login-link" href="<?php echo wp_login_url(); ?>"><?php _e('Log In', 'dp'); ?></a>
					
				<div class="dropdown-content"><div class="dropdown-content-inner">
					<?php wp_login_form(); ?>
				</div></div>
			</div>
		</div><!-- end #login-nav -->
	<?php } elseif($likes_page_id = get_option('dp_post_likes_page')) { ?>
		<div id="guest-nav" class="user-nav">
			<a class="btn likes-page-link btn-red" href="<?php echo get_permalink($likes_page_id); ?>"><?php echo get_the_title($likes_page_id); ?></a>
		</div><!-- end #login-nav -->
	<?php } ?>
	
	<?php if(get_option('dp_header_search')) { ?>
	<div id="top-search">
		<?php get_search_form(); ?>
	</div><!-- end .top-search -->
	<?php } ?>
	
</div></header><!-- end #header-->

<div id="main-nav"><div class="wrap cf">

	<?php 
		$nav_menu = wp_nav_menu(array('theme_location'=>'main', 'container'=>'', 'fallback_cb' => '', 'echo' => 0)); 
		
		// The fallback menu
		if(empty($nav_menu))
			$nav_menu = '<ul class="menu">'.wp_list_categories(array('show_option_all'=>__('Home', 'dp'), 'title_li'=>'', 'echo'=>0)).'</ul>';
		
		echo $nav_menu;
	?>
</div></div><!-- end #main-nav -->

<?php do_action( 'dp_after_header_php' ); ?>