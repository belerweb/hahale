<?php
/*= Admin Scripts and Styles
 *=============================================================================*/
add_action( 'admin_init', 'dp_register_admin_scripts' );
add_action('admin_print_scripts', 'dp_enqueue_admin_scripts');
add_action('admin_print_styles', 'dp_enqueue_admin_styles');

/**
 * Reigster all scripts and styles we need load on admin. 
 */
function dp_register_admin_scripts() {
	wp_register_style( 'dp-admin', trailingslashit( get_template_directory_uri() ) . 'admin/css/admin.css', false, '', 'screen' );
	wp_register_style( 'dp-colorpicker', trailingslashit( get_template_directory_uri() ) . 'admin/js/colorpicker/colorpicker.css', false, '', 'screen' );
	
	wp_register_script( 'dp-admin', trailingslashit( get_template_directory_uri() ) . 'admin/js/admin.js', array('jquery','media-upload','thickbox'), '', true );
	wp_register_script( 'dp-colorpicker', trailingslashit( get_template_directory_uri() ) . 'admin/js/colorpicker/colorpicker.js', array('jquery'), '', true );
	wp_register_script( 'dp-itembox', trailingslashit( get_template_directory_uri() ) . 'admin/js/itembox.js', array('jquery'), '', true );
}

/**
 * Load admin scripts
 */
function dp_enqueue_admin_scripts() {
	wp_enqueue_script('dp-colorpicker');
	wp_enqueue_script('dp-itembox');
	wp_enqueue_script('dp-admin');
}

/**
 * Load admin styles
 */
function dp_enqueue_admin_styles() {
	wp_enqueue_style('dp-colorpicker');
	wp_enqueue_style('dp-admin');
	wp_enqueue_style('thickbox');
}

/*= Theme Options
 *=============================================================================*/
 
/* General Settings
 *=============================================================================*/
class DP_General_Settings extends DP_Panel {
	function __construct(){
		$this->menu_slug = 'theme-options';
		
		parent::__construct();
	}
	
	function add_menu_pages(){
		$this->page_hook = add_menu_page(__('Theme Options', 'dp'), __('Theme Options', 'dp'), 'edit_themes', $this->menu_slug, array(&$this, 'menu_page'), '', 61);
		add_submenu_page('theme-options', __('General Settings', 'dp'), __('General', 'dp'), 'edit_themes', $this->menu_slug, array(&$this, 'menu_page'));
	}
	
	function add_meta_boxes(){
		add_meta_box( 'dp-general-settings', __('General Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-custom-labels-settings', __('Custom Labels Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		
		add_meta_box( 'dp-design-settings', __('Design Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-header-settings', __('Header Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-footer-settings', __('Footer Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		
		add_meta_box( 'dp-archive-settings', __('Archive Pages Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-cat-featured-settings', __('Category Featured Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-single-settings', __('Single Post Pages Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-post-likes-settings', __('Post Likes Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-hook-settings', __('Hook Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
	}
	
	function fields(){
		$supported_view_types = dp_supported_view_types();
		$fields = array(
			// Fields for Archive Settings
			'dp-archive-settings' => array(
				array(
					'type' => 'description',
					'value' => __('These settings determine how to display content on archive pages.', 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_loop_actions_status',
					'value' => true,
					'title' => __('Loop Actions', 'dp'),
					'label' => __('Check this to show "Loop Actions" bar', 'dp')
				),
				array(
					'name' => 'dp_sort_types_order'
				),
				array(
					'name' => 'dp_sort_types',
					'callback' => 'dp_sort_types_settings',
					'value' => dp_supported_sort_types()
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_sort_order',
					'value' => true,
					'title' => __('Sort Order', 'dp'),
					'label' => __('Check this to show ASC/DESC order', 'dp')
				),
				array(
					'name' => 'dp_view_types_order'
				),
				array(
					'name' => 'dp_view_types',
					'callback' => 'dp_view_types_settings',
					'value' => dp_supported_view_types()
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_ajax_inline_for_list_large_view',
					'value' => true,
					'title' => 'Ajax Inline Video Player',
					'label' => 'Check this to enble "Ajax Inline Video Player" with "List Large" view.'
				)
			),
			
			// Fields for Category Featured Settings
			'dp-cat-featured-settings' => array(
				array(
					'name' => 'dp_cat_featured',
					'callback' => 'dp_cat_featured_settings'
				),
				array(
					'name' => 'dp_cat_featured[posts_per_page]',
					'value' => 15
				)
			),
			
			// Fields for Custom Labels Settings
			'dp-custom-labels-settings' => array(
				array(
					'type' => 'description',
					'value' => __("These settings enable you to change the labels of WordPress built-in post type 'post', to 'Videos', or whatever you want to name it.", 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_post_labels_status',
					'title' => __('Custom Labels', 'dp'),
					'label' => __('check this to enable custom labels for post type "post"?', 'dp'),
					'value' => true
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[name]',
					'title' => __('name', 'dp'),
					'value' => __('Videos', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[singular_name]',
					'title' => __('singular_name', 'dp'),
					'value' => __('Video', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[add_new]',
					'title' => __('add_new', 'dp'),
					'value' => __('Add New', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[add_new_item]',
					'title' => __('add_new_item', 'dp'),
					'value' => __('Add New Video', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[edit_item]',
					'title' => __('edit_item', 'dp'),
					'value' => __('Edit Video', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[new_item]',
					'title' => __('new_item', 'dp'),
					'value' => __('New Video', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[all_items]',
					'title' => __('all_items', 'dp'),
					'value' => __('All Videos', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[view_item]',
					'title' => __('view_item', 'dp'),
					'value' => __('View Video', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[search_items]',
					'title' => __('search_items', 'dp'),
					'value' => __('Search Videos', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[not_found]',
					'title' => __('not_found', 'dp'),
					'value' => __('No videos found.', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[not_found_in_trash]',
					'title' => __('not_found_in_trash', 'dp'),
					'value' => __('No videos found in Trash.', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[menu_name]',
					'title' => __('menu_name', 'dp'),
					'value' => __('Videos', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_post_labels[name_admin_bar]',
					'title' => __('name_admin_bar', 'dp'),
					'value' => __('Video', 'dp')
				)
			),
			
			// Fields for General Settings
			'dp-general-settings' => array(
				array(
					'type' => 'select',
					'name' => 'dp_logo_type',
					'value' => 'image',
					'options' => array('text' => 'Text Logo', 'image'=>'Image Logo'),
					'title' => __('Logo Type', 'dp'),
				),
				array(
					'type' => 'upload',
					'name' => 'dp_logo',
					'title' => __('Image Logo', 'dp'),
					'desc' => __( 'Upload a logo for your theme, or specify the image url of your online logo.', 'dp'),
					'value' => get_template_directory_uri().'/images/logo.png'
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_site_description',
					'title' => __('Tagline', 'dp'),
					'label' => __( 'Show site tagline?', 'dp')
				),
				array(
					'type' => 'upload',
					'name' => 'dp_favicon',
					'title' => __('Favicon', 'dp'),
					'desc' => __( 'Upload a 16px x 16px PNG/GIF image that will represent your website\'s favicon.', 'dp'),
					'value' => 'http://s.wordpress.org/favicon.ico'
				),
				array(
					'type' => 'text',
					'name' => 'dp_rss_url',
					'title' => __('RSS URL', 'dp'),
					'desc' => sprintf(__( 'The default RSS url of your website is <code>%s</code>, if you want to use other feed url(e.g. feedburner), paste it to here.', 'dp'), get_bloginfo('rss2_url')),
				),
				array(
					'type' => 'upload',
					'name' => 'dp_login_logo',
					'title' => __('Login Logo', 'dp'),
					'desc' => __( 'Upload a logo for your wp-login.php page.', 'dp'),
					'value' => get_template_directory_uri().'/images/login-logo.png'
				),
				array(
					'type' => 'custom',
					'title' => __('Main Navigation', 'dp'),
					'label' => __('Check this to enable footer navigation in footer area.', 'dp'),
					'desc' => sprintf(__('By default, the main navigation is a list of your categories, if your want to customize it, add a menu on <a href="%s">Apperance->Menus</a> page and set this menu as "Main Navigation" in "Theme Location" box.', 'dp'), admin_url('nav-menus.php')),
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_responsive',
					'value' => true,
					'title' => __('Responsive', 'dp'),
					'label' => __( 'Check this to enable responsive?', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_addthis_pubid',
					'value' => true,
					'title' => __('AddThis PubID', 'dp'),
					'desc' => __( 'Your AddThis Publisher Profile ID (e.g. xa-502a3a59790da5bd). This required if you want AddThis to track analytics for your site.', 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_fb_ogtags',
					'value' => true,
					'title' => __('Facebook Open Graph Tags', 'dp'),
					'label' => __( 'Check this to insert Facebook Open Graph Tags into head.', 'dp')
				)
			),
			
			// Fields for Single Settings
			'dp-single-settings' => array(
				array(
					'type' => 'text',
					'name' => 'dp_related_posts',
					'title' => __('Related Posts', 'dp'),
					'desc' => __( "How many related posts should be displayed on the single post page? If you don't want to show it leave this field blank or set to 0.", 'dp'),
					'value' => 4,
					'class' => 'small-text'
				),
				array(
					'type' => 'select',
					'name' => 'dp_related_posts_view',
					'title' => __('Related Posts View', 'dp'),
					'value' => 'grid-mini',
					'options' => array(
						'grid-mini' => $supported_view_types['grid-mini'],
						'grid-small' => $supported_view_types['grid-small'],
						'grid-medium' => $supported_view_types['grid-medium']
					)
				),
				array(
					'type' => 'select',
					'name' => 'dp_single_video_layout',
					'title' => __('Single Video Layout', 'dp'),
					'desc' => __( 'Specify a default layout for all of the video posts, and you can override this setting for individual posts in "Video Settings" panel on edit post page.', 'dp'),
					'options' => array(
						'standard' => __('Standard', 'dp'), 
						'full-width' =>__('Full Width', 'dp')
					),
					'value' => 'standard',
					'class' => 'small-text'
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_single_video_autoplay',
					'title' => __('Autoplay', 'dp'),
					'label' => __( 'Check this to autoplay video when viewing a single video post?', 'dp'),
					'value' => true,
					'class' => 'small-text'
				),
				array(
					'type' => 'text',
					'name' => 'dp_info_toggle',
					'title' => __('"More/Less" Toggle', 'dp'),
					'desc' => __( "Enter a number as less height for video detatils area, eg. 100, if you don't need this function, leave this field blank or set to 0. Note: this function is only works on single video post pages.", 'dp'),
					'value' => 100,
					'class' => 'small-text'
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_single_thumb',
					'title' => __('Thumbnail', 'dp'),
					'label' => __( 'Check this to show thumbnail on single posts.', 'dp'),
					'value' => false,
					'class' => 'small-text'
				)
			),
			
			// Fields for Post Likes Settings
			'dp-post-likes-settings' => array(
				array(
					'type' => 'checkbox',
					'name' => 'dp_post_likes[login_required]',
					'value' => true,
					'title' => __('Login Required', 'dp'),
					'label' => __('Users must be registered and logged in to like post ', 'dp')
				),
				array(
					'type' => 'custom',
					'name' => 'dp_post_likes_page',
					'title' => __('Likes Page', 'dp'),
					'custom' => wp_dropdown_pages(array('echo' => false, 'name' => 'dp_post_likes_page', 'selected' => get_option('dp_post_likes_page'), 'show_option_none' => __('&mdash; Select &mdash;', 'dp'))),
					'desc' => 
					sprintf(__('<p>Select a page as user\'s likes page, if the page doesn\'t exist:<br />
					1. <a href="%s">Adding a new page</a><br />
					2. Give this page a title like "My Likes".<br />
					3. Set page template as "Likes".<br />
					<br />
					The "Likes Page" is a page for display user/visitor\'s liked posts.<br />
					<strong>* Logged in:</strong> If the user is logged in, the page will display the user\'s liked posts based on the user\'s ID.<br />
					<strong>* Not Logged in:</strong> If the visitor is not logged in, the page will display the visitor\'s liked posts based on the visitor\'s IP.<br />
					<strong>* Login Required + Not Logged in:</strong> If "Login Required" and the user is not logged in, the page will display a message to remind users to register and login.<br />', 'dp'), admin_url('post-new.php?post_type=page')),
				)
			),
			
			// Fields for Header Settings
			'dp-header-settings' => array(
				array(
					'type' => 'checkbox',
					'name' => 'dp_header_search',
					'value' => true,
					'title' => __('Header Search Box', 'dp'),
					'label' => __('Check this to enable search box in header area.', 'dp')
				)
			),
			
			// Fields for Footer Settings
			'dp-footer-settings' => array(
				array(
					'type' => 'checkbox',
					'name' => 'dp_footer_nav_status',
					'value' => true,
					'title' => __('Footer Navigation', 'dp'),
					'label' => __('Check this to enable footer navigation in footer area.', 'dp'),
					'desc' => sprintf(__('By default, the footer navigation is a list of your pages, if your want to customize it, add a menu on <a href="%s">Apperance->Menus</a> page and set this menu as "Footer Navigation" in "Theme Location" box.', 'dp'), admin_url('nav-menus.php'))
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_footbar_status',
					'value' => true,
					'title' => __('Footbar(Footer Widget Area)', 'dp'),
					'label' => sprintf(__( 'Check this to enable footbar. Add widgets on <a href="%s">Appearance->Widgets</a> page', 'dp'), admin_url('widgets.php')),
				),
				array(
					'type' => 'text',
					'name' => 'dp_site_copyright',
					'title' => __('Text for Copyright', 'dp'),
					'value' => __('Copyright %1$s &copy; %2$s All rights reserved.', 'dp'),
					'desc' => __("<code>%1&#36;s</code> is current year, <code>%2&#36;s</code> is a link with your site name.", 'dp')
				),
				array(
					'type' => 'textarea',
					'name' => 'dp_site_credits',
					'title' => __('Text for Credits', 'dp'),
					'value' => __('Powered by <a target="_blank" href="http://wordpress.org/">WordPress</a> & <a target="_blank" href="http://dedepress.com/themes/detube/" title="Premium Video Theme">deTube</a> by <a target="_blank" href="http://dedepress.com" title="Premium WordPress Themes">DeDePress</a>.', 'dp'),
					'desc' => __('Whether WordPress or DeDePress, No attribution or backlinks are strictly required, but play the game, it\'s always nice to be credited for your site. Any form of spreading the word is always appreciated!', 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_social_nav_status',
					'value' => true,
					'title' => __('Social Navigation', 'dp'),
					'label' => __('Check this to enable social navigation in footer area', 'dp')
				),
				array(
					'type' => 'text',
					'name' => 'dp_social_nav_desc',
					'title' => __('Navigation Description', 'dp'),
					'value' =>  __('Follow us elsewhere', 'dp'),
				),
				array(
					'type' => 'fields',
					'title' => __('Twitter Link', 'dp'),
					'fields' => array(
						array(
							'type' => 'checkbox',
							'name' => 'dp_social_nav_links[twitter][status]',
							'value' => true
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[twitter][url]',
							'prepend' => __('URL:', 'dp'),
							'value' => 'http://twitter.com/dedepress',
							'class' => 'regular-text'
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[twitter][title]',
							'prepend' => __('Title Attribute:', 'dp'),
							'value' => __('Follow us on Twitter', 'dp'),
							'class' => 'regular-text'
						)
					)
				),
				array(
					'type' => 'fields',
					'title' => __('Facebook Link', 'dp'),
					'fields' => array(
						array(
							'type' => 'checkbox',
							'name' => 'dp_social_nav_links[facebook][status]',
							'value' => true
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[facebook][url]',
							'prepend' => __('URL:', 'dp'),
							'value' => 'http://facebook.com/dedepress',
							'class' => 'regular-text'
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[facebook][title]',
							'prepend' => __('Title Attribute:', 'dp'),
							'value' => __('Become a fan on Facebook', 'dp'),
							'class' => 'regular-text'
						)
					)
				),
				array(
					'type' => 'fields',
					'title' => __('Google Plus Link', 'dp'),
					'fields' => array(
						array(
							'type' => 'checkbox',
							'name' => 'dp_social_nav_links[gplus][status]',
							'value' => true
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[gplus][url]',
							'prepend' => __('URL:', 'dp'),
							'value' => 'http://gplus.to/dedepress',
							'class' => 'regular-text'
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[gplus][title]',
							'prepend' => __('Title Attribute:', 'dp'),
							'value' => __('Follow us on Google Plus', 'dp'),
							'class' => 'regular-text'
						)
					)
				),
				array(
					'type' => 'fields',
					'title' => __('RSS Link', 'dp'),
					'fields' => array(
						array(
							'type' => 'checkbox',
							'name' => 'dp_social_nav_links[rss][status]',
							'value' => true
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[rss][url]',
							'prepend' => __('URL:', 'dp'),
							'value' => get_bloginfo('rss2_url'),
							'class' => 'regular-text'
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[rss][title]',
							'prepend' => __('Title Attribute:', 'dp'),
							'value' => __('Subscriber to RSS Feed', 'dp'),
							'class' => 'regular-text'
						)
					)
				),
				array(
					'type' => 'fields',
					'title' => __('Newsletter Link', 'dp'),
					'fields' => array(
						array(
							'type' => 'checkbox',
							'name' => 'dp_social_nav_links[news][status]',
							'value' => true
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[news][url]',
							'prepend' => __('URL:', 'dp'),
							'value' => 'http://dedepress.com',
							'class' => 'regular-text'
						),
						array(
							'type' => 'text',
							'name' => 'dp_social_nav_links[news][title]',
							'prepend' => __('Title Attribute:', 'dp'),
							'value' => __('Premium WordPress Themes', 'dp'),
							'class' => 'regular-text'
						)
					)
				)
			),
			
			// Fields for Hook Settings
			'dp-hook-settings' => array(
				array(
					'type' => 'textarea',
					'name' => 'dp_head_code',
					'title' => __('Head Code', 'dp'),
					'desc' => __( 'Paste any code here. It will be inserted before the <code>&lt;/head&gt;</code> tag of your theme.', 'dp'),
				),
				array(
					'type' => 'textarea',
					'name' => 'dp_footer_code',
					'title' => __('Footer Code', 'dp'),
					'desc' => __( 'Paste any code here, e.g. your Google Analytics tracking code. It will be inserted before the <code>&lt;/body&gt;</code> tag of your theme.', 'dp'),
				)
			),
			
			// Fields for Design Settings
			'dp-design-settings' => array(
				array(
					'type' => 'select',
					'name' => 'dp_wrap_layout',
					'value' => '',
					'options' => array('full-wrap' => __('Full Width', 'dp'), 'boxed-wrap'=>__('Boxed', 'dp')),
					'title' => __('Wrap Layout', 'dp'),
				),
				array(
					'type' => 'color',
					'name' => 'dp_bgcolor',
					'value' => '#EEE',
					'title' => __('Custom Background Color', 'dp'),
					'append' => __("Default color value is #EEEEEE", 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_bgpat',
					'value' => true,
					'title' => __('Background Pattern', 'dp'),
					'label' => __("Check this to enable background pattern.", 'dp')
				),
				array(
					'type' => 'select',
					'name' => 'dp_preset_bgpat',
					'value' => get_template_directory_uri().'/images/bg-pattern.png',
					'options' => dp_get_patterns(),
					'title' => __('Preset Background Pattern', 'dp'),
					'desc' => dp_preset_bgpat_preview()
				),
				array(
					'type' => 'upload',
					'name' => 'dp_custom_bgpat',
					'value' => '',
					'title' => __('Custom Background Pattern', 'dp'),
					'desc' => __('This option will override "Preset Background Pattern" in the above.', 'dp'),
				),
				array(
					'type' => 'select',
					'name' => 'dp_bgrep',
					'value' => 'repeat',
					'options' => array('repeat', 'repeat-x', 'repeat-y', 'no-repeat'),
					'title' => __('Background Repeat', 'dp')
				),
				array(
					'type' => 'select',
					'name' => 'dp_bgatt',
					'value' => 'fixed',
					'options' => array('fixed', 'scroll'),
					'title' => __('Background Attachment', 'dp')
				),
				array(
					'type' => 'checkbox',
					'name' => 'dp_bgfull',
					'value' => false,
					'title' => __('Full Page Background Image', 'dp'),
					'label' => __("Check this to enable full page background image(not working below IE9).", 'dp')
				)
			) 
		);
		
		return $fields;
	}
}
dp_register_panel('DP_General_Settings');

/**
 * Get all patterns from "{theme_direcoty}/patterns/"
 */
function dp_get_patterns() {
	$dir = get_template_directory().'/patterns';
	
	$patterns = array(
		get_template_directory_uri().'/images/bg-pattern.png' => __('Default', 'dp')
	);
	
	if (!is_dir($dir))
		return $patterns;
	
    if ($handler = opendir($dir)) {
        while (($file = readdir($handler)) !== false) {
			// Get file extension
			if(function_exists('pathinfo'))
				$file_ext = pathinfo($file, PATHINFO_EXTENSION);
			else
				$file_ext = end(explode(".", $file));
			
			if ($file != "." && $file != ".." && in_array($file_ext, array('jpg', 'png', 'gif'))) {
				$file_url = get_template_directory_uri().'/patterns/'.$file;
				$patterns[$file_url] = $file;
			}
        }
        closedir($handler);
	}
	
	return $patterns;
}

function dp_preset_bgpat_preview() {
	$pat = get_option('dp_preset_bgpat');
	if(!$pat)
		$pat = get_template_directory_uri().'/images/bg-pattern.png';
	
	$html = '
		<style type="text/css">
			.dp-preset-bgpat-preivew{
				margin:20px 0 0;
				height:100px;
				border:1px solid #CCC;
				background:#EEE url('.$pat.');
			}
		</style>
	';
	$html .= '<div class="dp-preset-bgpat-preivew"></div>';
	 
	return $html;
}

/* Home Settings
 *=============================================================================*/
class DP_Home_Settings extends DP_Panel {
	function __construct(){
		$this->menu_slug = 'home-settings';
		
		parent::__construct();
	}
	
	function add_menu_pages(){
		$this->page_hook = add_submenu_page('theme-options', __('Home Settings', 'dp'), __('Home', 'dp'), 'edit_themes', $this->menu_slug, array(&$this, 'menu_page'));
	}
	
	function add_meta_boxes(){
		add_meta_box( 'dp-home-sections-settings', __('Home Sections Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
		add_meta_box( 'dp-home-featured-settings', __('Home Featured Settings', 'dp'), array(&$this, 'meta_box'), $this->page_hook, 'normal');
	}
	
	function fields(){
		$default_home_sections = array(
			array(
				'title' => __('Newest Videos', 'dp'),
				'view' => 'list-large'
			),
			array(
				'title' => __('Most Viewed', 'dp'),
				'orderby' => 'views',
				'view' => 'grid-mini'
			),
			array(
				'title' => __('Most Liked', 'dp'),
				'orderby' => 'likes',
				'view' => 'grid-medium'
			),
			array(
				'title' => __('Most Commented', 'dp'),
				'orderby' => 'comments',
				'view' => 'list-medium'
			)
		);
		$cats = get_terms('category');
		foreach($cats as $cat) {
			$default_home_sections[] = array('taxonomies'=> array('category'=>$cat->term_id), 'title'=>$cat->name);
		}
		
		$fields = array(
			'dp-home-featured-settings' => array( // Home Featured Settings
				array(
					'name' => 'dp_home_featured',
					'callback' => 'dp_home_featured_settings',
					'value' => array(
						'posts_per_page' => 12
					)
				)
			),
			'dp-home-sections-settings' => array( // Home Sections Settings
				array(
					'name' => 'dp_home_sections',
					'callback' => 'dp_home_sections_settings',
					'value' => $default_home_sections
				)
			)
		);
		
		return $fields;
	}
}
dp_register_panel('DP_Home_Settings');

/**
 * General sort types settings meta box
 */
function dp_sort_types_settings() {
	$supported_types = dp_supported_sort_types();
	$types = get_option('dp_sort_types');
	$types_order = get_option('dp_sort_types_order');
	
	if(empty($types))
		$types = array();
	if(empty($types_order))
		$types_order = array_keys($supported_types);

	echo '<tr><th>'.__('Sort Types', 'dp').'</th> <td><ul class="sortable-list">';
	foreach($types_order as $type) {
		$checked = array_key_exists($type, $types) ? ' checked="checked"' : '';
		$label = $supported_types[$type]['label'];
		echo '<li>
			<input style="display:none;" type="checkbox" name="dp_sort_types_order[]" value="'.$type.'" checked="checked" />
			<input type="checkbox" name="dp_sort_types['.$type.']" value="1" '.$checked.'/> '.$label.
			'</li>';
	}
	echo '</ul>';
	echo __("Check a type to enable it, or drag the types to reorder.", 'dp');
	echo '</td></tr>';
}

/**
 * General view types settings meta box
 */
function dp_view_types_settings() {
	$supported_types = dp_supported_view_types();
	$types = get_option('dp_view_types');
	$types_order = get_option('dp_view_types_order');
	
	if(empty($types))
		$types = array();
	if(empty($types_order))
		$types_order = array_keys($supported_types);

	echo '<tr><th>'.__('View Types', 'dp').'</th><td><ul class="sortable-list">';
	foreach($types_order as $type) {
		$checked = array_key_exists($type, $types) ? ' checked="checked"' : '';
		$label = $supported_types[$type];
		echo '<li>
			<input style="display:none;" type="checkbox" name="dp_view_types_order[]" value="'.$type.'" checked="checked" />
			<input type="checkbox" name="dp_view_types['.$type.']" value="1" '.$checked.'/> '.$label.
			'</li>';
	}
	echo '</ul>';
	echo __("Check a type to enable it, or drag the types to reorder.", 'dp');
	echo '</td></tr>';
}

/**
 * Home featured settings meta box
 */
function dp_home_featured_settings() {
	$defaults = array(
		'cat' => '',
		'post_type' => 'post',
		'taxonomies' => '',
		'orderby' => '',
		'order' => '',
		'posts_per_page' => 18,
		'posts__in' => '',
		'autoplay' => 0,
		'ajaxload' => true,
		'autoscroll' => 0,
		'layout' => 'standard' // standard, full-width
	);
	$args = get_option('dp_home_featured');
	foreach($defaults as $key => $value) {
		if(!array_key_exists($key, $args)) {
			$args[$key] = 0;
		}
	}
	$args = wp_parse_args($args, $defaults);
	
	$dropdown_sort_types = dp_dropdown_sort_types(array(
		'echo' => 0, 
		'name' => 'dp_home_featured[orderby]',
		'selected' => $args['orderby']
	));
	
	$dropdown_order_types = dp_dropdown_order_types(array(
		'echo' => 0, 
		'name' => 'dp_home_featured[order]',
		'selected' => $args['order']
	));
	
	$dropdown_views_timing = dp_dropdown_views_timing(array(
		'echo' => 0, 
		'name' => 'dp_home_featured[views_timing]',
		'selected' => $args['views_timing']
	));
	
	$dropdown_layouts = dp_form_field(array(
		'echo' => 0,
		'type' => 'select',
		'options' => array(
			'standard' => __('Standard', 'dp'), 
			'full-width' => __('Full Width', 'dp')
		),
		'name' => 'dp_home_featured[layout]',
		'value' => $args['layout']
	));
	
	$dropdown_post_types = dp_dropdown_post_types(array(
		'echo' => 0,
		'name' => 'dp_home_featured[post_type]',
		'selected' => $args['post_type']
	));
	
	
	$multi_dropdown_terms = dp_multi_dropdown_terms(array(
		'echo' => 0,
		'name' => 'dp_home_featured[taxonomies]',
		'selected' => $args['taxonomies']
	));
	
	$html = '<table class="form-table">
		<tr>
			<td colspan="2">
				<div class="description">'.__("These settings enable you to show featured posts on home pages. If you don't want to show it, set 'Number of Posts' to 0.", 'dp').'</div>
			</td>
		</tr>
		<tr>
			<th>'.__('Layout', 'dp').'</th>
			<td>'.$dropdown_layouts.'</td>
		</tr>';
	
	if($dropdown_post_types) {
	$html .= '<tr>
			<th><label>'.__('Post Type', 'dp').'</label></th>
			<td>
				'.$dropdown_post_types.'
			</td>
		</tr>';
	}
	$html .= '<tr>
			<th>'.__('Taxonomy Query', 'dp').'</th>
			<td>
				'.$multi_dropdown_terms.'
			</td>
		</tr>
		<tr>
			<th>'.__('Sort', 'dp').'</th>
			<td>
				<label>'.__('Order by:', 'dp').'</label> '.$dropdown_sort_types.'&nbsp;&nbsp;
				<label>'.__('Order:', 'dp').'</label> '.$dropdown_order_types.'&nbsp;&nbsp;
				<label>'.__('Views Timing:', 'dp').'</label> '.$dropdown_views_timing.'&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<th><label>'.__('Number of Posts', 'dp').' </label></th>
			<td>
				<input class="small-text" type="text" value="'.$args['posts_per_page'].'" name="dp_home_featured[posts_per_page]" />
			</td>
		</tr>
		<tr>
			<th><label>'.__('Includes', 'dp').'</label></th> 
			<td>
				<input class="widefat" type="text" value="'.$args['post__in'].'" name="dp_home_featured[post__in]" />
				<p class="description">'.__('If you want to display specific posts, enter post ids to here, separate ids with commas, (e.g. 1,2,3,4). <br />if this field is not empty, category will be ignored. <br/>If you want to display posts sort by the order of your enter IDs, set "Sort" field as <strong>Includes</strong>.', 'dp').'</p>
			</td>
		</tr>
		<tr>
			<th><label>'.__('Autoplay', 'dp').'</label></th> 
			<td>
				<label><input type="checkbox" value="1" name="dp_home_featured[autoplay]" '.checked($args['autoplay'], true, false).'/>'.__('Check this to autoplay video in home featured when viewing the home page.', 'dp').'</label>
			</td>
		</tr>
		<tr>
			<th><label>'.__('Ajaxload', 'dp').'</label></th> 
			<td>
				<label><input type="checkbox" value="1" name="dp_home_featured[ajaxload]" '.checked($args['ajaxload'], true, false).'/>'.__('Check this to enable ajax loading with Standard Layout. <strong>Note</strong>: The full width layout will always ajax loading.', 'dp').'</label>
			</td>
		</tr>
		<tr>
			<th><label>'.__('Autoscroll', 'dp').'</label></th> 
			<td>
				<input class="widefat" type="text" value="'.$args['autoscroll'].'" name="dp_home_featured[autoscroll]" />
				<p class="description">'.__('Set autoscrolling interval in milliseconds to make carousel to automatic play (eg. 2500), set it to 0 or leave it blank to disable it . <strong>Note</strong>: The autoscroll only work for "Standard" layout, and it will disable automatic video play and ajax video loading.', 'dp').'</p>
			</td>
		</tr>
	</table>';

	return $html;
}

/**
 * Category featured settings meta box
 */
function dp_cat_featured_settings() {
	$defaults = array(
		'orderby' => '',
		'order' => '',
		'posts_per_page' => '',
		'item'
	);
	$args = get_option('dp_cat_featured');
	$args = wp_parse_args($args, $defaults);
	
	$dropdown_sort_types = dp_dropdown_sort_types(array(
		'echo' => 0, 
		'name' => 'dp_cat_featured[orderby]',
		'selected' => $args['orderby']
	));
	
	$dropdown_order_types = dp_dropdown_order_types(array(
		'echo' => 0, 
		'name' => 'dp_cat_featured[order]',
		'selected' => $args['order']
	));

	$html = '
		<tr>
			<td colspan="2">
				<div class="description">'.__("These settings enable you to show posts of current category with carousel effect on category pages. If you don't want to show it, set 'Number of Posts' to 0.", 'dp').'</div>
			</td>
		</tr>
		<tr>
			<th>'.__('Query', 'dp').'</th>
			<td>
				<label>'.__('Sort:', 'dp').'</label> '.$dropdown_sort_types.'&nbsp;&nbsp;'.$dropdown_order_types.'&nbsp;&nbsp;
				<label>'.__('Number of Posts:', 'dp').' </label>
				<input class="small-text" type="text" value="'.$args['posts_per_page'].'" name="dp_cat_featured[posts_per_page]" />
			</td>
		</tr>
	';

	return $html;
}


/**
 * Home sections settings meta box
 */
function dp_home_sections_settings() {
	$html = '
	<tr><td colspan="2">
	<div class="item-box">
	<p class="description" style="padding:10px;">'.__('To adding a section, click "<strong>Add New Section</strong>" button. <br />Drag sections up or down to change their order of appearance on home page.<br/>Don\'t forget to click "<strong>Save Changes</strong>" button.', 'dp').'</p>
	<div class="item-list-container" id="dp-home-sections-item-list-container">
		<a href="#" class="button add-new-item" data-position="prepend">'.__('Add New Section', 'dp').'</a>
		<ul class="item-list ui-sortable" id="dp-home-sections-item-list">';
		
	$items = get_option('dp_home_sections');
	if(!empty($items) && is_array($items)) {
		foreach($items as $number => $item) {
			$item = array_filter($item);
			if(!empty($item))
				$html .= dp_home_section_item($number, $item);
		}
	}
	
	$html .= '
		</ul>
		<ul class="item-list-sample" id="dp-home-sections-item-list-sample" style="display:none;">'.dp_home_section_item().'</ul>
	<a href="#" class="button add-new-item" data-position="append">'.__('Add New Section', 'dp').'</a>
	
	</div></div>
	</td></tr>';
	
	return $html;
}

/**
 * Single section settings
 */
function dp_home_section_item($number = null, $item = array()) {
	$default_item = array(
		'post_type' => 'post',
		'cat' => '',
		'view' => '',
		'orderby' => '',
		'order' => '',
		'taxonomies' => '',
		'tax_query' => array(),
		'post__in' => '',
		'posts_per_page' => '',
		'title' => '',
		'link' => '',
		'before' => '',
		'after' => ''
	);
	$item = wp_parse_args($item, $default_item);
	if($number === null)
		$number = '##';

	$dropdown_view_types = dp_dropdown_view_types(array(
		'echo' => 0, 
		'name' => 'dp_home_sections['.$number.'][view]',
		'selected' => !empty($item['view']) ? $item['view'] : 'grid-small'
	));
	
	$dropdown_sort_types = dp_dropdown_sort_types(array(
		'echo' => 0, 
		'name' => 'dp_home_sections['.$number.'][orderby]',
		'selected' => $item['orderby']
	));
	
	$dropdown_order_types = dp_dropdown_order_types(array(
		'echo' => 0, 
		'name' => 'dp_home_sections['.$number.'][order]',
		'selected' => $item['order']
	));
	
	$dropdown_views_timing = dp_dropdown_views_timing(array(
		'echo' => 0, 
		'name' => 'dp_home_sections['.$number.'][views_timing]',
		'selected' => $item['views_timing']
	));
	
	$dropdown_post_types = dp_dropdown_post_types(array(
		'echo' => 0, 
		'name' => 'dp_home_sections['.$number.'][post_type]',
		'selected' => $item['post_type']
	));
	
	$taxonomies = get_taxonomies(array('public'=>true), 'objects');
	$multi_dropdown_terms = dp_multi_dropdown_terms(array(
		'echo' => 0,
		'name' => 'dp_home_sections['.$number.'][taxonomies]',
		'selected' => $item['taxonomies']
	));
	
	$section_title = __('Section Box', 'dp');
	$section_title .= !empty($item['title']) ? ': <spanc class="in-widget-title">'.$item['title'].'</span>' : '';
	
	$html = '
	<li rel="'.$number.'">
		<div class="section-box closed">
		<div class="section-handlediv" title="Click to toggle"><br></div><h3 class="section-hndle"><span>'.$section_title.'</span></h3>
		
		<div class="section-inside">
		
		<table class="item-table">
			<tr>

				<td>
					<table class="item-table">';
	
			if($dropdown_post_types) {
				$html .= '<tr>
				<th><label>'.__('Post Type', 'dp').'</label></th>
					<td>
						'.$dropdown_post_types.'
					</td>
				</tr>';
			}
	
			$html .= '
						<tr>
							<th>'.__('Taxomoy Query', 'dp').'</th>
							<td>
								'.$multi_dropdown_terms.'
							</td>
						</tr>
						<tr>
							<th>'.__('Sort', 'dp').'</th>
							<td>
								<label>'.__('Order by:', 'dp').'</label> '.$dropdown_sort_types.'&nbsp;&nbsp;
								<label>'.__('Order:', 'dp').'</label> '.$dropdown_order_types.'&nbsp;&nbsp;
								<label>'.__('Views Timing:', 'dp').'</label> '.$dropdown_views_timing.'
							</td>
						</tr>
						<tr>
							<th><label>'.__('Number of Posts:', 'dp').' </label></th>
							<td>
								<input class="small-text" type="text" value="'.$item['posts_per_page'].'" name="dp_home_sections['.$number.'][posts_per_page]" />&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<th><label>'.__('Includes', 'dp').'</label></th> 
							<td>
								<input class="widefat" type="text" value="'.$item['post__in'].'" name="dp_home_sections['.$number.'][post__in]" />
								<p class="description">'.__('If you want to display specific posts, enter post ids to here, separate ids with commas, (e.g. 1,2,3,4). <br />if this field is not empty, category will be ignored. <br/>If you want to display posts sort by the order of your enter IDs, set "Sort" field as <strong>Includes</strong>.', 'dp').'</p>
							</td>
						</tr>
						<tr>
							<th><label>'.__('View', 'dp').'</label></th> 
							<td>'.$dropdown_view_types.'</td>
						</tr>
						<tr>
							<th><label>'.__('Title', 'dp').'</label></th> 
							<td>
								<input class="widefat" type="text" value="'.$item['title'].'" name="dp_home_sections['.$number.'][title]" />
								<p class="description">'.__('If you specify a category, the default title is the category name, and you can still fill in this field to override it.', 'dp').'</p>
							</td>
						</tr>
						<tr>
							<th><label>'.__('Link', 'dp').'</label></th> 
							<td>
								<input class="widefat" type="text" value="'.$item['link'].'" name="dp_home_sections['.$number.'][link]" />
								<p class="description">'.__('If you specified a category, the default link is the category link, and you can still fill in this field to override it.', 'dp').'</p>
							</td>
						</tr>
						<tr>
							<th><label>'.__('Before', 'dp').'</label></th> 
							<td>
								<textarea rows="5" class="widefat" name="dp_home_sections['.$number.'][before]">'.$item['before'].'</textarea>
								<p class="description">'.__('Maybe you want to insert something before this section, such as your ad code. (support html and shortcode).', 'dp').'</p>
							</td>
						</tr>
						<tr>
							<th><label>'.__('After', 'dp').'</label></th> 
							<td>
								<textarea rows="5" class="widefat" name="dp_home_sections['.$number.'][after]">'.$item['after'].'</textarea>
								<p class="description">'.__('Maybe you want to insert something after this section, such as your ad code. (support html and shortcode).', 'dp').'</p>
							</td>
						</tr>
					</table>
				</td>
				
				<td style="width:50px;">
					<a href="#" class="button delete-item">'.__('Delete', 'dp').'</a>
				</td>
			</tr>
		</table>
		</div>
		</div>
	</li>
	';

	return $html;
}

/**
 * HTML dropdown list of post types
 *
 * @since deTube 1.2.6
 */
function dp_dropdown_post_types($args='') {
	$defaults = array(
		'name' => '',
		'selected' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);
	
	$post_types = get_post_types(array('public'=>true), 'objects');
	unset($post_types['page']);
	unset($post_types['attachment']);
	if(count($post_types) < 2)
		return;

	$post_type_options = array('all'=>__('All', 'dp'));
	foreach($post_types as $type_name=>$type_object)
		$post_type_options[$type_name] = $type_object->labels->singular_name;
		
	$dropdown = dp_form_field(array(
		'echo' => 0,
		'type' => 'select',
		'options' => $post_type_options,
		'name' => $name,
		'value' => $selected
	));
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}

/**
 * HTML dropdown list of taxonomies terms
 *
 * @since deTube 1.2.6
 */
function dp_multi_dropdown_terms($args='') {
	$defaults = array(
		'name' => '',
		'selected' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);


	$taxes = get_taxonomies(array('public'=>true), 'objects');
	// Only category and post_format now
	$taxes = array(
		'category'=>$taxes['category'],
		'post_format'=>$taxes['post_format']
	);
	$dropdown = '';
	foreach($taxes as $tax_name=>$tax_object) {
		$dropdown_args = array(
			'echo' => 0,
			'taxonomy' => $tax_name,
			'name' => $name.'['.$tax_name.']',
			'selected' => !empty($selected[$tax_name]) ? $selected[$tax_name] : array(),
			'show_option_all' => __('All', 'dp'),
			'hide_empty' => false,
			'hide_if_empty' => true,
			'number' => 2000,
			'orderby' => 'name'
		);
		if($tax_name == 'post_format')
			$dropdown_args['show_option_none'] = __('Standard', 'dp');
		$dropdown_terms = wp_dropdown_categories($dropdown_args);
		
		if($dropdown_terms)
			$dropdown .= '<label>'.$tax_object->labels->singular_name.':</label> '.$dropdown_terms.'&nbsp;&nbsp;';
	}
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}


/**
 * HTML dropdown list of view types
 */
function dp_dropdown_view_types($args){
	$defaults = array(
		'name' => '',
		'selected' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);
	
	$view_types = dp_supported_view_types();
	
	$dropdown = '<select name="'.$name.'">';
	foreach($view_types as $type => $label) {
		$dropdown .= '<option value="'.$type.'"'.selected($type, $selected, false).'>'.$label.'</option>';
	}
	$dropdown .= '</select>';
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}

/**
 * HTML dropdown list of sort types
 */
function dp_dropdown_sort_types($args){
	$defaults = array(
		'name' => '',
		'selected' => '',
		'class' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);
	
	$sort_types = dp_supported_sort_types();
	$sort_types['post__in'] = array(
		'label' => __('Includes', 'dp')
	); 
	
	$dropdown = '<select class="'.$class.'" name="'.$name.'">';
	foreach($sort_types as $type => $args) {
		$dropdown .= '<option value="'.$type.'"'.selected($type, $selected, false).'>'.$args['label'].'</option>';
	}
	$dropdown .= '</select>';
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}

/**
 * HTML dropdown list of views timing
 */
function dp_dropdown_views_timing($args){
	$defaults = array(
		'name' => '',
		'selected' => '',
		'class' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);
	
	$views_timing = dp_views_timings();
	
	$dropdown = '<select class="'.$class.'" name="'.$name.'">';
	foreach($views_timing as $option => $label) {
		$dropdown .= '<option value="'.$option.'"'.selected($option, $selected, false).'>'.$label.'</option>';
	}
	$dropdown .= '</select>';
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}

/**
 * HTML dropdown list of order types
 */
function dp_dropdown_order_types($args){
	$defaults = array(
		'name' => '',
		'selected' => '',
		'class' => '',
		'echo' => true
	);
	$args = wp_parse_args($args, $defaults);
	extract($args);
	
	$order_types = array(
		'DESC' => __('Sort Descending', 'dp'),
		'ASC' => __('Sort Ascending', 'dp')
	);
	
	$dropdown = '<select class="'.$class.'" name="'.$name.'">';
	foreach($order_types as $type => $label) {
		$dropdown .= '<option value="'.$type.'"'.selected($type, $selected, false).'>'.$label.'</option>';
	}
	$dropdown .= '</select>';
	
	if($echo)
		echo $dropdown;
	else
		return $dropdown;
}


/*= Custom Pnale on edit post Page
 *=============================================================================*/

class DP_Video_Settings_Panel extends DP_Post_Panel {

	function __construct() {
		$this->name = 'dp-video-settings';
		$this->title = __('Video Settings', 'dp');
		$this->post_types = array('post');
		
		parent::__construct();
	}
	
	function fields() {
		$single_video_layout = get_option('dp_single_video_layout');
		$video_layout_label = ($single_video_layout == 'standard' || !$single_video_layout) ? __('Standard', 'dp') : __('Full Width', 'dp');
		
		$fields = array(
			array(
				'type' => 'select',
				'name' => 'dp_video_layout',
				'title' => __('Video Layout', 'dp'),
				'desc' => sprintf(__( 'The default single video layout is <b>"%s"</b>, select a layout if you want to use different layout to override it.', 'dp'), $video_layout_label),
				'options' => array(
					'' => '',
					'standard' => __('Standard', 'dp'), 
					'full-width' =>__('Full Width', 'dp')
				),
				'value' => ''
			),
			array(
				'type' => 'description',
				'value' => '<hr class="sepline" style="margin:0 -20px;" />'
			),
			array(
				'type' => 'description',
				'value' => __('Please choose one of the following ways to embed the video into your post, the video is determined in the order: <b>Video Code > Video URL > Video File.</b>', 'dp'),
			),
			array(
				'type' => 'description',
				'value' => '<hr class="sepline" style="margin:0 -20px;" />'
			),
			array(
				'type' => 'textarea',
				'name' => 'dp_video_file',
				'title' => __('Video File', 'dp'),
				'desc' => __( 'Paste your video file url to here. <b>Supported Video Formats:</b> mp4, m4v, webmv, webm, ogv and flv.<br /><br/>
				<b>About Cross-platform and Cross-browser Support</b><br/>
				If you want your video works in all platforms and browsers(HTML5 and Flash), you should provide various video formats for same video, if the video files are ready, enter one url per line. For Example: <br />
				<code>http://yousite.com/sample-video.m4v</code><br />
				<code>http://yousite.com/sample-video.ogv</code><br />
				<b>Recommended Format Solution</b>: webmv + m4v + ogv.
				', 'dp'),
			),
			array(
				'type' => 'upload',
				'name' => 'dp_video_poster',
				'title' => __('Video Poster', 'dp'),
				'desc' => __( 'The preview image for video file, recommended size is 960px*540px.', 'dp'),
			),
			array(
				'type' => 'description',
				'value' => '<hr class="sepline" style="margin:0 -20px;" />'
			),
			array(
				'type' => 'text',
				'name' => 'dp_video_url',
				'title' => __('Video URL', 'dp'),
				'desc' => __( 'Paste the url from popular video sites like YouTube or Vimeo. For example: <br/>
				<code>http://www.youtube.com/watch?v=nTDNLUzjkpg</code><br/>
				or<br/>
				<code>http://vimeo.com/23079092</code><br/><br/>
				See <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">Supported Video Sites</a>.', 'dp')
			),
			array(
				'type' => 'description',
				'value' => '<hr class="sepline" style="margin:0 -20px;" />'
			),
			array(
				'type' => 'textarea',
				'name' => 'dp_video_code',
				'title' => __('Video Code', 'dp'),
				'desc' => __( 'Paste the raw video code to here, such as <code>&lt;object&gt;</code>, <code>&lt;embed&gt;</code> or <code>&lt;iframe&gt;</code> code.', 'dp')
			)
		);
		return $fields;
	}
}
dp_register_post_panel('DP_Video_Settings_Panel');