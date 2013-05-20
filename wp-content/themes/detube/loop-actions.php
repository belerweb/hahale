<?php
/**
 * Loop Actions Template
 *
 * The template displays the loop actions on archive pages.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
	<?php
		global $wp_query;
		
		$selected_sort_types = dp_selected_sort_types(); // Get selected sort types
		$selected_view_types = dp_selected_view_types(); // Get selected view types
		
		/* Get the loop view of current page based on
		 * user's cookie and the selected sort types
		 */
		global $loop_view;
		if(!empty($_COOKIE['loop_view']) && in_array($_COOKIE['loop_view'], array_keys($selected_view_types)))
			$loop_view = $_COOKIE['loop_view'];
		elseif(!empty($selected_view_types)) {
			$_view_types = array_keys($selected_view_types);
			$loop_view = $_view_types[0];
		} else {
			$loop_view = 'grid-small';
		}
		
		global $section_view;
		if(in_array('list-large', array_keys($selected_view_types)))
			$section_view = 'list-large';
		elseif(in_array('grid-mini', array_keys($selected_view_types)) && count($selected_view_types) == 1)
			$section_view = 'grid-mini';
		
		$loop_actions_status = get_option('dp_loop_actions_status');
		if(!$loop_actions_status)
			return;

		// Output loop action bar if has selected sort types or view types
		if(!empty($selected_sort_types) || !empty($selected_view_types)) {
			echo '<div class="loop-actions cf">';	
			
			/* Output sort(orderby and order) if has selected sort types
			 *=======================================================*/
			if(!empty($selected_sort_types)) {
				// Get base url uses remove_query_arg() function
				$base_url = remove_query_arg(array('meta_key', 'meta_value', 'orderby', 'order'));
				
				// Get query vars from $wp_query uses get_query_var() function
				$meta_key = get_query_var('meta_key');
				$orderby = get_query_var('orderby');
				$order = strtoupper(get_query_var('order'));
				
				// Get 'order' var from url
				$url_order = isset($_GET['order']) ? strtoupper($_GET['order']) : '';
				if($url_order != 'ASC' && $url_order != 'DESC')
					$url_order = '';
				
				// Get current sort type based query vars
				if($orderby == 'meta_value_num' && !empty($meta_key)) {
					$current_sort_type = $meta_key;
				} elseif($orderby == 'comment_count') {
					$current_sort_type = 'comments';
				} else {
					$current_sort_type = $orderby;
				}
	
				// Output orderby
				$out_orderby = array(); 
				$orderby_items = array(); 
				$i = 1;
				foreach($selected_sort_types as $type => $args) {
					// Start build url
					$url = $base_url;
					
					// Add order to surl if has specific correct order in current url 
					if($url_order)
						$url = add_query_arg('order', strtolower($order), $url);
						
					// Add sort type as orderby
					$url = add_query_arg(array('orderby'=>$type), $url);
					
					// Check the sort typs is current? 
					$current = '';
					if($type == $current_sort_type || (empty($current_sort_type) && $i == 1))
						$current = ' current';

					// Output sort type link
					$out_orderby[] = ' <a href="'.$url.'" title="'.esc_attr($args['title']).'" class="'.$type.$current.'"><i>'.$args['label'].'</i></a> ';
					
					$orderby_items[$type] = array(
						'url' => $url,
						'title' => esc_attr($args['title']),
						'label' => esc_attr($args['label']),
						'current' => ($type == $current_sort_type) || (empty($current_sort_type) && $i == 1)
					);
					
					$i++;
				}
				
				// Output order(ASC/DESC) based user's settings
				$out_order = '';
				if(get_option('dp_sort_order')) {
					// Supported order types
					$order_types = array(
						'ASC' => __('Sort Ascending', 'dp'),
						'DESC' => __('Sort Descending', 'dp')
					);

					// Generate reversed order to url
					$to_order = ($order == 'ASC') ? 'desc' : 'asc';
					$to_order_url = add_query_arg('order', $to_order);
					
					// Output order link
					$out_order = '<span class="order"><a class="'.strtolower($order).'" href="'.$to_order_url.'" title="'.esc_attr($order_types[$order]).'">'.$order_types[$order].'</a></span><!-- end .order -->';
				}

				// Output sort html
				echo '<div class="sort"><span class="prefix">'.__('Sort:', 'dp').'</span>';
				echo '<span class="orderby">'.implode('<span class="sep">|</span>', $out_orderby).'</span><!-- end .orderby -->';
				
				echo '<select class="orderby-select">';
				
				foreach($orderby_items as $type => $item) {
					echo '<option value="'.$item['url'].'"'.selected($item['current'], true, false).'>'.$item['label'].'</option>';
				}
				echo '</select>';
				
				echo $out_order;
				echo '</div><!-- end .sort -->';
			}
			
			/* Output view if has selected view types
			 *=======================================================*/
			if(!empty($selected_view_types)) {
				echo '<div class="view"><span class="prefix">'.__('View:', 'dp').'</span>';
				$i = 0;
				foreach($selected_view_types as $type => $label) {
					// Check the view typs is current? 
					$current = '';
					if($type == $loop_view)
						$current = ' current';
					
					// Output view type link
					echo '<a href="#" title="'.esc_attr($label).'" data-type="'.$type.'" class="'.$type.'-link'.$current.'"><i></i></a>';
				}
				
				echo '</div><!-- end .view -->';
			}
			
			echo '</div><!-- end .loop-actions -->';
		}
	?>
