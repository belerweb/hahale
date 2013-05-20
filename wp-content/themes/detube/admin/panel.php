<?php
/**
 * API for creating admin panel
 *
 * @package DP API
 * @subpackage Admin
 */

/*=============================================================================*
 * Admin Page
 *=============================================================================*/ 
abstract class DP_Panel {
	protected $layout_like = 'dashboard'; // postnew, dashboard, settings
	protected $layout_columns; // postnew(max=>2, default=>2), dashboard(max=>4, default=>1) 
	
	protected $menu_slug;
	protected $plugin_file;
	
	protected $submenu = false;
	protected $buttons = array('save', 'reset', 'toggle');

	/*======================================================================*
	 *	Registration Component
	 *======================================================================*/
	private static $registered = array();
	
	static function register( $class ) {
		if ( isset( self::$registered[$class] ) )
			return false;
			
		self::$registered[$class] = $class;
		
		add_action('_admin_menu', array(__CLASS__, '_register_panel'));
		
		return true;
	}
	
	static function unregister( $class ) {
		if ( ! isset( self::$registered[$class] ) )
			return false;

		unset( self::$registered[$class] );

		return true;
	}
	
	static function _register_panel() {
		foreach(self::$registered as $class) {
			new $class();
		}
	}
	
	/*======================================================================*
	 *	Main Method
	 *======================================================================*/
	function DP_Panel() {
		$this->__construct();
	}
	
	function __construct() {
		global $plugin_page;
		
		if($this->layout_like == 'dashboard')
			$this->layout_columns = !empty($this->layout_columns) ? $this->layout_columns : array('max'=>4, 'default'=>1);
		elseif($this->layout_like == 'postnew')
			$this->layout_columns = !empty($this->layout_columns) ? $this->layout_columns : array('max'=>2, 'default'=>2);

		$this->donate_url = !empty($this->donate_url) ? $this->donate_url : 'http://dedepress.com/donate/';
		$this->support_url = !empty($this->support_url) ? $this->support_url : 'http://dedepress.com/support/';
		$this->translating_url = !empty($this->translating_url) ? $this->translating_url : 'http://dedepress.com/';
		$this->menu_slug = !empty($this->menu_slug) ? $this->menu_slug : '';

		
		/* Add menu pages and meta boxes */
		add_action('admin_menu', array(&$this, 'add_menu_pages'));
		add_action('admin_menu', array(&$this, 'add_meta_boxes'));
		

		add_action('admin_init', array(&$this, 'hello'));
		
		$current_page = !empty( $_GET['page'] ) ? $_GET['page'] : '';
		if( $current_page != $this->menu_slug )
			return;
		
		add_action('admin_menu', array(&$this, 'update'), 2);
		add_action('admin_menu', array(&$this, 'add_settings_sections') );

		/* Set screen layout columns */
		add_action('admin_head', array(&$this, 'custom_screen_options'), 0); // for wp 3.1 or higher
		
		// Print default scripts and styles
		add_action( 'admin_print_scripts', array( &$this,'default_scripts' ) );
		add_action( 'admin_print_styles', array( &$this,'default_styles' ) );

		// Add admin notices
		add_action('admin_notices', array(&$this, 'admin_notices'));
		
		// Filtering pluginn action links and plugin row meta
		add_filter( 'plugin_action_links', array(&$this, 'plugin_action_links'),  10, 2 );
		add_filter( 'plugin_row_meta', array(&$this, 'plugin_row_meta'),  10, 2 );
	}
	
	function add_menu_pages() {
		
	}
	
	function add_meta_boxes() {
	
	}
	
	function add_settings_sections() {
		
	}
	
	function add_default_meta_boxes($meta_boxes = array()) {
		global $plugin_page;
		$page_hook = get_plugin_page_hookname( $plugin_page, '' );
		
		if(in_array('plugin-info', $meta_boxes) && $this->plugin_file)
			add_meta_box('plugin-info-meta-box', __('Plugin Info', 'dp'), array(&$this, 'plugin_info_meta_box'), $page_hook, 'side' );
		
		if(in_array('theme-info', $meta_boxes))
			add_meta_box('theme-info-meta-box', __('Theme Info', 'dp'), array(&$this, 'theme_info_meta_box'), $page_hook, 'side' );
		
		if(in_array('like-this', $meta_boxes))
			add_meta_box('like-this-meta-box', __('Like This?', 'dp'), array(&$this, 'like_this_meta_box'), $page_hook, 'side' );
		
		if(in_array('need-support', $meta_boxes))
			add_meta_box('need-support-meta-box', __('Need Support?', 'dp'), array(&$this, 'need_support_meta_box'), $page_hook, 'side' );
		
		if(in_array('quick-preview', $meta_boxes))
			add_meta_box('quick-preview-meta-box', __('Quick Preview', 'dp'), array(&$this, 'quick_preview_meta_box'), $page_hook, 'side' );
	}
	
	/*======================================================================*
	 *	Default Meta boxes 
	 *======================================================================*/
	function plugin_info_meta_box() {
		if( !$this->plugin_file )
			return;
		
		$plugin_data = get_plugin_data( trailingslashit(WP_PLUGIN_DIR) . $this->plugin_file, false);

		echo '<p>' . __('Name:', 'dp') . ' <a target="_blank" href="'.$plugin_data['PluginURI'].'"><strong>' . $plugin_data['Name'] . '</strong></a></p>';
		echo '<p>' . __('Version:', 'dp') . ' ' .$plugin_data['Version'] . '</p>';
		echo '<p>' . __('Author:', 'dp') . ' <a href="'.$plugin_data['AuthorURI'].'">' . $plugin_data['Author'] . '</a></p>';
		echo '<p>' . __('Description:', 'dp') . ' '. $plugin_data['Description'] . '</span></p>';
	}
	
	function theme_info_meta_box() {
		if(function_exists('wp_get_theme'))
			$theme_data = wp_get_theme();
		else
			return;

		echo '<p>' . __('Name:', 'dp') . ' <a target="_blank" href="'.$theme_data['URI'].'"><strong>' . $theme_data['Name'] . '</strong></a></p>';
		echo '<p>' . __('Version:', 'dp') . ' ' .$theme_data['Version'] . '</p>';
		echo '<p>' . __('Author:', 'dp') . ' <a href="'.$theme_data['AuthorURI'].'">' . $theme_data['Author'] . '</a></p>';
		echo '<p>' . __('Description:', 'dp') . ' '. $theme_data['Description'] . '</span></p>';
	}
	
	function like_this_meta_box() {
		echo '<p>' . __('We spend a lot of effort on Free WordPress development. Any help would be highly appreciated. Thanks!', 'dp') . '</p>';
		echo '<ul>';
		
		$plugin_data = get_plugin_data( trailingslashit(WP_PLUGIN_DIR) . $this->plugin_file, false);
		
		echo '<li class="link-it"><a href="' . $plugin_data['PluginURI']. '">' . __('Link to it so others can find out about it', 'dp') . '</a></li>';

		if( !empty($this->wp_plugin_url) )
			echo '<li class="rating-it"><a href="' . $this->wp_plugin_url . '">' . __('Give it a good rating on WordPress.org', 'dp') . '</a></li>';
		
		if( !empty($this->donate_url) )
			echo '<li class="donate-it"><a href="' . $this->donate_url. '">' . __('Donate something to our team', 'dp') . '</a></li>';
		
		if( !empty($this->translating_url) )
			echo '<li class="trans-it"><a href="' . $this->translating_url. '">' . __('Help us translating it', 'dp') . '</a></li>';
			
		echo '</ul>';
	}
	
	function need_support_meta_box() {
		echo '<p>';
		echo sprintf(__('If you have any problems or ideas for improvements or enhancements, please use the <a href="%s">Our Support Forums</a>.', 'dp'), $this->support_url );
		echo '</p>';
	}
	
	/*======================================================================*
	 *	Default Callback Functions 
	 *======================================================================*/
	/**
	 * Default meta box callback function
	 * @since 0.7
	 */
	function meta_box($object, $box) {
		$defaults = $this->fields();		
		if(!isset($defaults[$box['id']]))
			return;
		$defaults = $defaults[$box['id']];

		$new_fields = dp_instance_fields($defaults);
		dp_form_fields($new_fields);
	}
	
	/**
	 * Default settings section callback function
	 * @since 0.8
	 */
	function settings_section($section) {
		$defaults = $this->fields();		
		if(!isset($defaults[$section['id']]))
			return;
			
		$defaults = $defaults[$section['id']];

		$new_fields = dp_instance_fields($defaults);
		dp_form_fields($new_fields);
	}
	
	/*======================================================================*
	 *	Update and Reset
	 *======================================================================*/
	/**
	 * Update Settings.
	 * @since 0.1
	 */
	function update() {
		$defaults = $this->defaults();
		
		if( !is_array($defaults) || empty($defaults) || !isset($_GET['page']) || $_GET['page'] != $this->menu_slug)
			return;
		
		// Save the settings when user click "Save" Button
		if (isset($_POST['save'])) {
				
			foreach( $defaults as $option_name => $option_value) {
				$value = null;
				if(!empty($_POST[$option_name]))
					$value = $_POST[$option_name];
				if ( !is_array($value) )
					$value = trim($value);
				$value = stripslashes_deep($value);
				
				update_option($option_name, $value);
			}
			
			do_action('dp_update_settings');
			do_action('dp_save_settings');
			
			$args = array_filter(array(
				'page' => $_REQUEST['page'],
				'updated' => true
			));
			wp_redirect( add_query_arg($args, get_current_url(false)) );
			exit();
		}
		
		// Reset settings to defaults when user click "Reset" button or settings is empty.
		elseif(isset($_POST['reset'])) {
			foreach($defaults as $option_name => $option_value) {
				update_option($option_name, $option_value);
			}
			
			do_action('dp_update_settings');
			do_action('dp_reset_settings');
			
			$args = array_filter(array(
				'page' => $_REQUEST['page'],
				'reset' => true
			));
			wp_redirect( add_query_arg($args, get_current_url(false)) );
			exit();
		}
		
		/* global $wp_rewrite;
		$wp_rewrite->flush_rules(); */
	}
	
	function hello() {
		$hello = get_option($this->menu_slug.'_say_hello');
		
		$defaults = $this->defaults();
		
		if( !is_array($defaults) || empty($defaults))
			return;
		
		if(!$hello) {
			foreach($defaults as $option_name => $option_value) {
				update_option($option_name, $option_value);
			}
			
			update_option($this->menu_slug.'_say_hello', true);
		}
		
		/*global $wp_rewrite;
		$wp_rewrite->flush_rules();*/
		
		return;
	}
	
	/*======================================================================* 
	 *	General filters
	 *======================================================================*/
	/**
	 * Generate a standard admin notice
	 * @since 0.7
	 */
	function admin_notices() {
		global $parent_file;
		
		if ( !isset($_GET['page']) || $_GET['page'] != $this->menu_slug )
			return;
			
		global $read_notice;
		$read_notice = false;
		
		if($read_notice)
			return false;
		
		if (!empty($_GET['updated']) && $parent_file != 'options-general.php')
			echo '<div id="message" class="updated"><p><strong>' . __('Settings Saved.', 'dp') . '</strong></p></div>';

		elseif (!empty($_GET['reset']))
			echo '<div id="message" class="updated"><p><strong>' . __('Settings Reset.', 'dp') . '</strong></p></div>';
			
		$read_notice = true;
	}
	
	function screen_layout_columns($columns, $screen) {
		$columns[$screen] = $this->layout_columns;
		
		return $columns;
	}
	
	function custom_screen_options() {
		if($this->layout_columns)
			add_screen_option('layout_columns', $this->layout_columns);
	}

	/*======================================================================* 
	 *	Plugin filters
	 *======================================================================*/
	function plugin_action_links( $actions, $plugin_file ) {
			if ( $plugin_file == $this->plugin_file && $this->settings_url)
				$actions[] = '<a href="'.$this->settings_url.'">' . __('Settings', 'dp') .'</a>';
			
			return $actions;
		}
	
	function plugin_row_meta( $plugin_meta, $plugin_file ){
			if ( $plugin_file == $this->plugin_file ) {
				$plugin_meta[] = '<a href="'.$this->donate_url.'">' . __('Donate', 'dp') .'</a>';
				$plugin_meta[] = '<a href="'.$this->support_url.'">' . __('Support', 'dp') .'</a>';
			}

			return $plugin_meta;
		}
	
	/*======================================================================* 
	 *	Theme filters
	 *======================================================================*/
	function theme_action_links($links) {
		 $links[] = '<a href="'.admin_url('options.php').'">' . __('Settings', 'dp') .'</a>';
		return $links;
	}
	
	/*======================================================================*/
	/* Render Functions
	/*======================================================================*/
	function screen_icon() {
		echo '<a target="_blank" href="http://dedepress.com">' . get_screen_icon('themes') . '</a>';
	}
	
	function submenu() {
		if(!$this->submenu)
			return false;
			
		global $plugin_page, $submenu, $parent_file; 
		$i = 0;
		
		if(!isset($submenu[$parent_file]) || !is_array($submenu[$parent_file]))
			return;
		
		echo '<ul class="subsubsub">';
		foreach($submenu[$parent_file] as $sub) {
			echo '<li>';
			if($i > 0) echo " | ";
			$i++;
			$class = '';
			if($sub[2] == $plugin_page)
				$class = ' class="current"';
			echo '<a'.$class.' href="'.esc_url(admin_url('admin.php?page=' . $sub[2])).'">'.$sub[0].'</a></li>'; 
		}
		echo '</ul>';
	}
	
	function page_buttons($in_top = false) {
		if(in_array('save', $this->buttons))
			echo '<input type="submit" name="save" value="'.__('Save Changes', 'dp').'" class="button-primary"> ';
		
		if(in_array('reset', $this->buttons))
			echo '<input type="submit" value="'.__('Reset Settings', 'dp').'" name="reset" class="reset button button-highlighted"> ';
		
		if(in_array('toggle', $this->buttons) && $this->layout_like != 'settings' && $in_top)
			echo '<input type="button" class="button toggel-all" value="'.__('Toggle Boxes', 'dp').'" />';
	}
	
	function page_title() { ?>
		<h2>
			<?php echo get_admin_page_title(); ?>
			<?php $this->page_buttons(true); ?>
		</h2>
	<?php }
	
	function menu_page() {  
		global $parent_file, $plugin_page, $page_hook, $typenow, $hook_suffix, $pagenow, $current_screen, $wp_current_screen_options, $screen_layout_columns; 
		$screen = get_current_screen();
		?>
		<div class="wrap dp-panel">
			<form method="post" action="">
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
				
				<?php $this->screen_icon(); ?>
				<?php $this->page_title(); ?>
				<?php $this->submenu(); ?>
				
				<br class="clear" />
				
				<?php 
					if($this->layout_like == 'dashboard') {
						$this->metabox_holder_like_dashboard();
					} elseif($this->layout_like == 'postnew') {
						$this->metabox_holder_like_postnew();
					} elseif($this->layout_like == 'settings') {
						do_settings_sections($screen->id); 
					}
				?>
				
				<br class="clear" />
				<?php $this->page_buttons(); ?>
				
				<span class="pickcolor"></span>
				<div id="colorpicker" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
			</form>
		</div><!-- end .wrap -->
	<?php }
	
	function metabox_holder_like_dashboard() {
		global $wp_version, $screen_layout_columns;
	
		$screen = get_current_screen();
		
		$class = $hide2 = $hide3 = $hide4 = '';
		
		if($wp_version >= 3.4)
			$class = 'columns-' . get_current_screen()->get_columns();
		else {
			switch ( $screen_layout_columns ) {
				case 4:
					$width = 'width:25%;';
				break;
				case 3:
					$width = 'width:33.333333%;';
					$hide4 = 'display:none;';
					break;
				case 2:
					$width = 'width:50%;';
					$hide3 = $hide4 = 'display:none;';
					break;
				default:
						$width = 'width:100%;';
				$hide2 = $hide3 = $hide4 = 'display:none;';
			}
		} ?>
		
	<div id="dashboard-widgets" class="metabox-holder <?php echo $class; ?>">
		<div id="postbox-container-1" class="postbox-container" style="<?php echo $width; ?>">
			<?php do_meta_boxes($screen->id, 'normal', null); ?>
		</div><!-- end .postbox-container -->
				
		<div id="postbox-container-2" class="postbox-container" style="<?php echo $hide2.$width; ?>">
			<?php do_meta_boxes($screen->id, 'side', null); ?>
		</div><!-- end .postbox-container -->
					
		<div id="postbox-container-3" class="postbox-container" style="<?php echo $hide3.$width; ?>">
			<?php do_meta_boxes($screen->id, 'column3', null); ?>
		</div><!-- end .postbox-container -->
					
		<div id="postbox-container-4" class="postbox-container" style="<?php echo $hide4.$width; ?>">
			<?php do_meta_boxes($screen->id, 'column4', null); ?>
		</div><!-- end .postbox-container -->
		
	</div><!-- end .metabox-holder -->
	<?php }

	function metabox_holder_like_postnew() {
		global $screen_layout_columns;

		$screen = get_current_screen(); ?>
		<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
			<div class="inner-sidebar">
				<?php do_meta_boxes($screen->id, 'side', null); ?>
			</div><!-- end .innser-sidebar -->
			
			<div id="post-body">
				<div id="post-body-content">
					<?php do_meta_boxes($screen->id, 'normal', null); ?>
				</div><!-- end #post-body-conent -->
			</div><!-- end #post-body -->
		</div><!-- end .metabox-holder -->
	<?php }
	
	/*======================================================================* 
	 * Fields and Defaults
	 *======================================================================*/
	
	function fields() {
		$fields = array();
			
		return $fields;
	}
	
	/* Get default options from fields  */
	function defaults( $fields = array() ) {
		if(!$fields)
			$fields = $this->fields();
			
		if(empty($fields) || !is_array($fields))
			return;
			
		$defaults = array();
		
		foreach($fields as $box_id => $box_fields) {
			foreach(dp_field_options($box_fields) as $name => $field) {
				$defaults[$name] = $field;
			}
		}
		
		return $defaults;
	}
	
	/*======================================================================* 
	 * Scripts and Styles
	 *======================================================================*/
	
	/**
	 * Add default scripts.
	 *
	 * @since 1.0
	 */
	function default_scripts() {
			if( !isset($_GET['page']) || $_GET['page'] != $this->menu_slug )
				return;
			
			wp_enqueue_script('postbox');
			wp_enqueue_script('post');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('dp-admin', trailingslashit( get_template_directory() ) . '/admin/js/admin.js', array('jquery'), '', true);
	}
	
	/**
	 * Add default styles
	 *
	 * @since 1.0
	 */
	function default_styles() {
		if (!isset($_GET['page']) || $_GET['page'] != $this->menu_slug)
			return;
			
		wp_enqueue_style('thickbox');
		wp_enqueue_style('dp-admin', trailingslashit( get_template_directory() ) . 'admin/css/admin.css', false);
	}
}

function dp_register_panel($class) {
	DP_Panel::register($class);
}

function dp_unregister_panel($class) {
	DP_Panel::unregister($class);
}


/*=============================================================================*
 * Post Meta Box
 *=============================================================================*/ 
abstract class DP_Post_Panel {
	protected $name;
	protected $title;
	protected $post_types;
	
	protected $nonce;
	protected $nonce_action;
	
	
	/*======================================================================*
	 *	Registration Component
	 *======================================================================*/

	private static $registered = array();
	
	static function register( $class ) {
		if ( isset( self::$registered[$class] ) )
			return false;
			
		self::$registered[$class] = $class;
		
		add_action('admin_init', array(__CLASS__, '_register'));
		
		return true;
	}
	
	static function unregister( $class ) {
		if ( ! isset( self::$registered[$class] ) )
			return false;

		unset( self::$registered[$class] );

		return true;
	}
	
	static function _register() {
		foreach(self::$registered as $class) {
			new $class();
		}
	}
	
	/*======================================================================*
	 *	Main Method
	 *======================================================================*/
	function DP_Post_Panel() {
		$this->__construct();
	}
	
	function __construct() {
		$fields = $this->fields();
		if(!$this->name || empty($fields) || !is_array($fields))
			return;
		
		$this->nonce = !empty($this->nonce) ? $this->nonce : $this->name.'_nonce';
		$this->nonce_action = !empty($this->nonce_action) ? $this->nonce_action : plugin_basename(__FILE__);
		$this->post_types = !empty($this->post_types ) ? (array) $this->post_types : get_post_types(array(), 'names');
		
		add_action( 'save_post', array(&$this, 'handle'), 10, 2);
		add_action( 'add_meta_boxes', array(&$this, 'add_meta_boxes') );
		
		foreach($fields as $field) {
			if($field['type'] == 'image_id' && class_exists('MultiPostThumbnails'))
				new MultiPostThumbnails(array( 'label' => $field['label'], 'id' => $field['name']) );
		}
	}

	function add_meta_boxes() {
		foreach($this->post_types as $post_type) {
			add_meta_box($this->name, $this->title, array(&$this, 'meta_box'), $post_type, 'normal', 'high');
		}
	}
	
	function meta_box($object, $box) {
		global $post;
		$defaults = $this->fields();
		if(empty($defaults) || !is_array($defaults))
			return;
			
		$new_fields = dp_instance_fields($defaults, 'post_meta');
		
		wp_nonce_field( $this->nonce_action, $this->nonce );
		echo dp_form_fields($new_fields);
	}
	
	function handle($post_id, $post) {
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce( $_POST[$this->nonce], $this->nonce_action ) || !in_array($post->post_type, $this->post_types))
			return $post_id;
			
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
		
		$fields = $this->fields();
		
		if(empty($fields))
			return;

		foreach ( dp_field_options($fields) as $name => $field) {
			
			$meta_value = get_post_meta( $post_id, $name, true );
		
			$new_meta_value = $_POST[ $name ];
			
			if(is_array($new_meta_value))
				$new_meta_value = array_filter($new_meta_value);
			elseif(!empty($file['type']) && $field['type'] == 'password')
				$new_meta_value = md5($new_meta_vlue);
			
			if ( $new_meta_value && empty($meta_value) )
				add_post_meta( $post_id, $name, $new_meta_value, true );

			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $name, $new_meta_value );
			elseif ( empty($new_meta_value) && $meta_value )
				delete_post_meta( $post_id, $name, $meta_value );
			
		}
	}
	
	function fields( $post_type = '') {
		$fields = array();
		
		return $fields;
	}
}

function dp_register_post_panel($class) {
	DP_Post_Panel::register($class);
}

function dp_unregister_post_panel($class) {
	DP_Post_Panel::unregister($class);
}


/*=============================================================================*
 * Term Meta Box
 *=============================================================================*/
abstract class DP_Term_Panel {
	protected $name;
	protected $title;
	protected $taxonomies;
	
	protected $nonce;
	protected $nonce_action;
	
	/*======================================================================*
	 *	Registration Component
	 *======================================================================*/

	private static $registered = array();
	
	static function register( $class ) {
		
		if ( isset( self::$registered[$class] ) )
			return false;
			
		self::$registered[$class] = $class;
		
		add_action('_admin_menu', array(__CLASS__, '_register'));
		
		return true;
	}
	
	static function unregister( $class ) {
		if ( ! isset( self::$registered[$class] ) )
			return false;

		unset( self::$registered[$class] );

		return true;
	}
	
	static function _register() {		
		foreach(self::$registered as $class) {
			new $class();
		}
	}
	
	function DP_Term_Panel() {
		$this->__construct();
	}
	
	function __construct() {
		if(!$this->name)
			return;

		$this->nonce = !empty($this->nonce) ? $this->nonce : $this->name.'_nonce';
		$this->nonce_action = !empty($this->nonce_action) ? $this->nonce_action : plugin_basename(__FILE__);
		
		add_action('admin_menu', array(&$this, 'edit_term_form_action'));
		add_action('edit_term', array(&$this, 'handle'), 10, 3);
	}
	
	function edit_term_form_action() {
		$this->taxonomies = !empty($this->taxonomies ) ? (array)$this->taxonomies : get_taxonomies(array('show_ui' => true));
		
		foreach ($this->taxonomies as $taxonomy) { 
			add_action($taxonomy . '_edit_form', array(&$this, 'meta_box'), 10, 2);
			// add_action($taxonomy . '_add_form_fields', array(&$this, 'tax_form_fields'), 10);
		}
	}
	
	function tax_form_fields($taxonomy) {
		$this->meta_box('', $taxonomy);
	}
	
	function meta_box($term, $taxonomy) {
		if($this->title)
			echo '<h3>'.$this->title.'</h3>';
		
		$defaults = $this->fields();
		if(empty($defaults) || !is_array($defaults))
			return;
			
		$new_fields = dp_instance_fields($defaults, 'term_meta', $term);
		
		wp_nonce_field( $this->nonce_action, $this->nonce );
		echo dp_form_fields($new_fields);
	}
	
	function handle($term_id, $tt_id, $taxonomy) {
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce( $_POST[$this->nonce], $this->nonce_action ))
			return $term_id;

		$fields = $this->fields();

		foreach ( dp_field_options($fields) as $name => $field ) {
			$meta_value = get_term_meta( $term_id, $name, true );
			
			$new_meta_value = $_POST[$name];
			
			if(is_array($new_meta_value))
				$new_meta_value = array_filter($new_meta_value);
			elseif($field['type'] == 'password')
				$new_meta_value = md5($new_meta_vlue);
				
			if ( $new_meta_value && empty($meta_value) )
				add_term_meta( $term_id, $name, $new_meta_value, true );
				
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_term_meta( $term_id, $name, $new_meta_value );
				
			elseif ( empty($new_meta_value) && $meta_value )
				delete_term_meta( $term_id, $name, $meta_value );
		}
	}
	
	function fields($type = '') {
		return $fields;
	}
}

function dp_register_term_panel($class) {
	DP_Term_Panel::register($class);
}

function dp_unregister_term_panel($class) {
	DP_Term_Panel::unregister($class);
}

/*=============================================================================*
 * User Meta Box
 *=============================================================================*/
abstract class DP_User_Panel {
	protected $name;
	protected $title;
	
	protected $nonce;
	protected $nonce_action;
	
	function DP_User_Panel() {
		$this->__construct();
	}
	
	function __construct() {
		if(!$this->name)
			return;
		
		$this->nonce = !empty($this->nonce) ? $this->nonce : $this->name.'_nonce';
		$this->nonce_action = !empty($this->nonce_action) ? $this->nonce_action : plugin_basename(__FILE__);
		
		add_action( 'personal_options_update', array(&$this, 'handle') );
		add_action( 'edit_user_profile_update', array(&$this, 'handle') );
		add_action( 'admin_menu', array(&$this, 'add_meta_boxes') );
	}
	
	function add_meta_boxes() {
		add_action( 'show_user_profile', array(&$this, 'meta_box') );
		add_action( 'edit_user_profile', array(&$this, 'meta_box') );
	}
	
	function meta_box($user) {
		if($this->title)
			echo '<h3>'.$this->title.'</h3>';
		
		$defaults = $this->fields();
		if(empty($defaults) || !is_array($defaults))
			return;
			
		$new_fields = dp_instance_fields($defaults, 'user_meta', $user);
		
		wp_nonce_field( $this->nonce_action, $this->nonce );
		echo dp_form_fields($new_fields);
		
	}
	
	function handle($user_id) {
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce( $_POST[$this->nonce], $this->nonce_action ))
			return $user_id;
		
		$fields = $this->fields();
		
		if(empty($fields))
			return;
		
		foreach ( dp_field_options($fields) as $name => $field) {
			$meta_value = get_user_meta( $user_id, $name, true );
			
		
			$new_meta_value = $_POST[ $name ];
			
			if(is_array($new_meta_value))
				$new_meta_value = array_filter($new_meta_value);
			elseif($field['type'] == 'password')
				$new_meta_value = md5($new_meta_vlue);
				
			if ( $new_meta_value && empty($meta_value) )
				add_user_meta( $user_id, $name, $new_meta_value, true );
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_user_meta( $user_id, $name, $new_meta_value );
			elseif ( empty($new_meta_value) && $meta_value )
				delete_user_meta( $user_id, $name, $meta_value );
		}
	}
	
	function fields( $post_type = '') {
		$fields = array();
		
		return $fields;
	}
	
	/*======================================================================*
	 *	Registration Component
	 *======================================================================*/

	private static $registered = array();
	
	static function register( $class ) {
		if ( isset( self::$registered[$class] ) )
			return false;
			
		self::$registered[$class] = $class;
		
		add_action('_admin_menu', array(__CLASS__, '_register'));
		
		return true;
	}
	
	static function unregister( $class ) {
		if ( ! isset( self::$registered[$class] ) )
			return false;

		unset( self::$registered[$class] );

		return true;
	}
	
	static function _register() {
		foreach(self::$registered as $class) {
			new $class();
		}
	}
}

function dp_register_user_panel($class) {
	DP_User_Panel::register($class);
}

function dp_unregister_user_panel($class) {
	DP_User_Panel::unregister($class);
}

/*=============================================================================*
 * Hacks
 *=============================================================================*/

/**
 * Make a fix for per page screen option on the custom plugin page
 */
add_filter('set-screen-option', 'dp_set_screen_option_filter', 999, 3);
function dp_set_screen_option_filter($status, $option, $value) {
	$value = (int) $value;
	if ( $value < 1 || $value > 999 )
		return false;
	else
		return $value;
}
