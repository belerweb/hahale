<?php
/**
 * Functions for create form elements
 *
 * @package DP API
 * @subpackage Admin
 */

function dp_form_fields($fields = '', $args = '') {	
	$defaults = array(
		'before_container' => '<table class="form-table"><tbody>',
		'after_container' => '</tbody></table>',
		'before_row' => '<tr>',
		'after_row' => '</td></tr>',
		'before_title' => '<th scope="row">',
		'after_title' => '</th><td>',
		'callback' => '',
	);
	$args = wp_parse_args( $args, $defaults );

	echo $args['before_container'];
	
	foreach ($fields as $field) {
		if(!is_array($field))
			continue;
		
		$type = !empty( $field['type'] ) ? $field['type'] : '';
		$name = !empty( $field['name'] ) ? $field['name'] : '';
		$types = array('text', 'password', 'upload', 'image_id', 'color', 'textarea', 'radio', 'select', 'multiselect', 'checkbox', 'checkboxes', 'custom');
		
		if( !empty( $field['callback'] ) && is_callable( $field['callback'] ) ) {
				echo call_user_func( $field['callback'], $field );
		} 
		elseif($type == 'description' && !empty($field['value'])) {
			echo '<tr><td colspan="2"><div class="description">'.$field['value'].'</div></td></tr>';
		} 
		elseif($type == 'fields' ) {
			$defaults = array(
				'before_container' => '',
				'after_container' => '',
				'before_row' => '',
				'after_row' => '',
				'before_title' => '',
				'after_title' => '',
				'callback' => ''
			);
			
			echo '<tr><th>'.$field['title'].'</th><td>';
			dp_form_fields( $field['fields'], wp_parse_args( $field['args'], $defaults ) );
			echo '</td></tr>';
		} 
		elseif(!empty($type)) {
			if(!empty( $args['callback'] ) && is_callable( $args['callback'] ))
				$field = call_user_func( $args['callback'], $field);
			
			$field = wp_parse_args($field, $args);
			dp_form_row($field);
		}
	}
	
	echo $args['after_container'];
}

function dp_form_widget($fields = array(), $field_args_callback = '') {
	
	foreach ($fields as $field) {
		if(!is_array($field))
			continue;
		
		$type = !empty($field['type']) ? $field['type'] : '';
		$name = !empty($field['name']) ? $field['name'] : '';
		
		$types = array('text', 'textarea', 'radio', 'select', 'multiselect', 'checkbox', 'checkboxes');
		
		// if callback is set
		if(!empty($field['callback']))
			call_user_func($field['callback'], $field);
			
		// Handle outputs for form elements
		elseif(in_array($type, $types)) {
			if(!empty($field_args_callback) && is_callable($field_args_callback))
				$field = call_user_func($field_args_callback, $field);
				
			if(!empty($field['to_array'])) {
				$to_array = $field['to_array'];
				$field['name'] = "{$to_array}[{$name}]";
				$field['id'] = "$to_array-$name";
			}
			
			$field['before'] = '<p>';
			$field['after'] = '</p>';
			$field['before_title'] = '';
			$field['after_title'] = '';
			
			dp_form_row($field);
		}
		
		// type = description
		elseif($type == 'description' && !empty($field['value']))
			echo '<tr><td colspan="2"><span class="description">'.$field['value'].'</span></td></tr>';
		
		// type = custom
		elseif($type == 'custom' && !empty($field['value']))
			echo '<tr><td colspan="2">'.$field['value'].'</td></tr>';
	}
	echo '</tbody></table>';
}

function dp_form_row($args = '') {
	$defaults = array(
		'before_row' => '<tr>',
		'before_title' => '<th scope="row">',
		'title' => '',
		'after_title' => '</th><td>',
		'after_row' => '</td></tr>',
		'label_for' => '',
		'id' => '',
		'tip' => '',
		'req' => '',
		'desc' => '',
		'prepend' => '',
		'append' => '',
		'field' => ''
	);
	
	$args = wp_parse_args( $args, $defaults ); 
	extract($args);
	
	if(empty($id) && !empty($name))
		$id = $args['id'] = sanitize_field_id($name);
	if(empty($label_for) && !empty($id))
		$label_for = ' for="'.$id.'"';
	
	echo $before_row;
	
	/* Title */
	if($args['type'] != 'checkbox' || $args['type'] == 'checkboxes')
		$title = '<label'.$label_for.'>'.$args['title'].'</label> ';
	/* Tip */
	if($tip)
		$tip = ' <span class="tip">(?)</span><div style="display:none;">'.$tip.'</div>';
	/* Required */
	$req = '';	
	if($args['req'] === true || $args['req'] === 1)
		$req = '*';
	elseif(isset($args['req']))
		$req = $args['req'];
	if(!empty($req))
		$req = ' <span class="required">'.$req.'</span>';
	
	/* Output */
	echo $before_title . $title . $req . $tip . $after_title . ' ';
	
	if(!empty($args['prepend']))
		echo $args['prepend'] . ' ';
	
	if( empty($args['field']) )
		dp_form_field($args);
	
	if($args['type'] == 'custom' && !empty($args['custom']))
		echo $args['custom'];
		
	if(!empty($args['append']))
		echo ' '.$args['append'] . ' ';
		
	if(!empty($desc))
		echo ' <div class="description">'.$desc.'</div>';
		
	echo $after_row;
}

function dp_form_field($args = '') {
	if(empty($args['type']))
		return;

	$defaults = array(
		'name' => '',
		'value' => '',
		'class' => '',
		'id' => '',
		'options' => '',
		'sep' => '',
		'label' => '',
		'label_for' => '',
		'style' => '',
		'field_args' => '',
		'echo' => true
	);
	
	if($args['type'] == 'text')
		$defaults['class'] = 'widefat';
	elseif($args['type'] == 'textarea')
		$defaults['class'] = 'widefat';
	elseif($args['type'] == 'multiselect')
		$defaults['style'] = 'height:8em;';
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	if($args['type'] == 'upload') {
		$class .= ' dp-upload-text';
	} elseif( $args['type'] == 'color' ) {
		$class .= 'dp-color-input';
	}
	
	if(!empty($class)) 
		$class = ' class="'.$class.'"';
	if(empty($id) && !empty($name))
		$id = $args['id'] = sanitize_html_class($name);
	if(empty($label_for) && !empty($id))
		$label_for = ' for="'.sanitize_html_class($id).'"';
	if(!empty($id))
		$id = ' id="'.$id.'"';
	if(!empty($style))
		$style = ' style="'.$style.'"';
		
	$output = null;
	
	/* type = text, password, hidden */
	if($type == 'text' || $type == 'password' || $type == 'hidden') {
		$type = ' type="'.$type.'"';
		if(!empty($name)) $name = ' name="'.$name.'"';
		if($type == 'password') $value="";
		$value = ' value="' . esc_attr($value) . '"';
		

		$output = "<input{$type}{$name}{$value}{$id}{$class}{$style} />";
	}
		
	/* type = upload */
	elseif($type == 'upload') {
		$type = ' type="text"';
		$value = ' value="' . esc_attr(stripslashes($value)) . '"';
		if(!empty($name))
			$name = ' name="'.$name.'"';

		$output = "<input{$type}{$name}{$value}{$id}{$class}{$style} />";
		$output .= ' &nbsp; <a title="" class="thickbox button dp-upload-button" href="'.get_upload_iframe_src('image').'">Upload</a> <a href="#" class="button dp-remove-button">Remove</a> <div class="dp-upload-preview"></div>';
	} 
	
	/* type = image_id */
	elseif($type == 'image_id') {
		$output = apply_filters($args['name'].'_filter', ' ', $args);
	} 
	
	/* type = color */
	elseif($type == 'color') {
		$type = ' type="text"';
		$value = ' value="' . esc_attr(stripslashes($value)) . '"';
		if(!empty($name))
			$name = ' name="'.$name.'"';

		$output = "<span class='dp-color-handle colorSelector'>&nbsp;</span> <input{$type}{$name}{$value}{$id}{$class}{$style}>";
	}
	
	/* type = textarea */
	elseif($type == 'textarea') {
		$value = esc_textarea($value);
		if(!empty($name)) $name = ' name="'.$name.'"';
		if(!isset($args['cols'])) $cols = '10';
		if(!isset($args['rows'])) $rows = '6';
		$cols = ' cols="' . $cols . '"';
		$rows = ' rows="' . $rows . '"';

		$output .= "<textarea{$name}{$id}{$class}{$style}{$rows}{$cols}>{$value}</textarea></div>";
	}
	
	/* type = editor */
	elseif($type == 'editor') {
		$field_args = array_merge(array('textarea_name' => $name, 'textarea_rows' => 4), (array)$field_args);
		wp_editor($value, $args['id'], $field_args);
	}
	
	/* type = radio */
	elseif($type == 'radio' && is_array($options)) {
		foreach ($options as $option => $label) {
			if(!is_assoc($options))
				$option = $label;
				
			$output[] = '<label'.$label_for.'><input name="'.$name.'" type="radio" value="'.$option.'"'.checked($option, $value, false).' />'.$label.'</label>';
		}
	
		$output = implode( ($sep ? $sep : '<br />'), $output);
	}
	
	/* type = select */
	elseif($type == 'select' && is_array($options)) {
		$name = !empty($name) ? 'name="'.$name.'"' : '';
	
		$output .= "<select{$id}{$class}{$name}{$style}>";
		
		if(isset($args['option_none']))
			$output .= '<option value="">'.$args['option_none'].'</option>';
		
		/*foreach ($options as $option => $label) {
				$output .= '<option value="'.$option.'"'.selected($option, $value, false).'>'.$label.'</option>';
			}*/
		if(is_assoc($options)) {
			foreach ($options as $option => $label) {
				$output .= '<option value="'.$option.'"'.selected($option, $value, false).'>'.$label.'</option>';
			}
		} else {
			foreach ($options as $option => $label) {
				$output .= '<option value="'.$label.'"'.selected($label, $value, false).'>'.$label.'</option>';
			}
		}
		
		$output .= '</select> ';
	}
	
	/* type = multiselect */
	elseif($type == 'multiselect' && is_array($options)) {
		$output .= '<select multiple="multiple" name="'.$name.'[]"' . $id . $class . $style . '>';
		foreach ($options as $option => $label) { 
			if(!is_assoc($options))
				$option = $label;

				$selected = (is_array($value) && in_array($option, $value)) ? ' selected="selected"' : '';
				
			$output .= '<option value="'.$option.'"'.$selected.'>'.$label.'</option>';
		} 
		$output .= '</select>';
	}
	
	/* type = checkbox */
	elseif($type == 'checkbox') {
		$output .= '<label'.$label_for.'><input'.$id.' name="'.$name.'" type="checkbox" value="1"'.checked($value, true, false).' /> '.$args['label'].'</label> ';
	}
	
	/* type = checkboxes */
	elseif($type == 'checkboxes' && is_array($options)) {
		
		foreach ($options as $option => $label) {
	
			if(!is_assoc($options))
				$option = $label;
				
			$checked = (is_array($value) && in_array($option, $value)) ? ' checked="checked"' : '';
				
			$output[] = '<label><input'.$class.$style.' name="'.$name.'[]" type="checkbox" value="'.$option.'"'.$checked.' /> '.$label.'</label>';
		}
		
		$output = '<div class="dp-checkboxes">' . implode($args['sep'] ? $args['sep'] : '<br />', $output) . '</div>';
	}
	
	if($echo)
		echo $output;
	else
		return $output;
}

function dp_field_options( $fields = array() ) {
	$options = array();
	
	foreach($fields as $field) {
		global $post;
			
		if( !empty($field['fields']) && $field['type'] == 'fields' ) {
			$options = array_merge_recursive( $options, dp_field_options($field['fields']) );
		} else {
			if(empty($field['name']) )
				continue;
				
			$name = $field['name'];
			$name = str_replace('[]', '', $name);
			$name = str_replace(']', '', $name);
			$name = explode('[', $name);
			
			$option = array();
			
			for($i=count($name) - 1; $i>=0; $i--) {
				if($i == count($name) - 1) {
					$option[$name[$i]] = isset($field['value']) ? $field['value'] : '';
				} else {
					$option[$name[$i]] = $option;
					// $option[$name[$i]] = array( $name[$i+1] => $option[$name[$i+1]] );

					unset( $option[$name[$i+1]] );
				}
			}
			
			$options = array_merge_recursive($options, $option);
		}
	}
	
	return $options;
}

function dp_instance_fields( $fields, $instance_type = '', $object = '') {
	foreach($fields as $field) {
		global $post;
		
		if(!empty($field['fields']) && $field['type'] == 'fields') {
			$field['fields'] = dp_instance_fields($field['fields'], $instance_type);
		} else {
			if(empty($field['name']) ) {
				$new_fields[] = $field;
				continue;
			}
				
			$name = $field['name'];
			$name = str_replace('[]', '', $name);
			$name = str_replace(']', '', $name);
			$name = explode('[', $name);
			
			if( $instance_type == 'post_meta' )
				$value = get_post_meta($post->ID, $name[0], true);
			elseif( $instance_type == 'user_meta' ) {
				$value = get_user_meta($object->ID, $name[0], true);
			} elseif( $instance_type == 'term_meta' )
				$value = get_term_meta($object->term_id, $name[0], true);
			else
				$value = get_option($name[0]);
				
			unset($name[0]);
			foreach($name as $n) {
				if( empty($value[$n]) ) {
					$value = '';
					break;
				}	
						
				$value = $value[$n];
			}
			
			$field['value'] = $value;
		}
			
		$new_fields[] = $field;
	}
		
	return $new_fields;
}

function dp_get_logic_fields( $args = '' ) {
	
	$defaults = array(
		'include' => '',
		'exclude' => ''
	);
	$args = wp_parse_args($args, $defaults);
	extract($args); 
	
	$fields = array();
	
	/* Global */
	$fields['global'] = array(
		'name' => 'global',
		'title' => __('Global Settings', 'dp')
	);
	
	/* Front Page **/
	$fields['front_page'] = array(
		'name' => 'front_page',
		'title' => __('Front Page', 'dp')
	);
	
	// Home
	$fields['home'] = array(
		'name' => 'home',
		'title' => __('Blog Home', 'dp')
	);
	
	/* Single Post */
	$post_types = get_post_types(array('publicly_queryable' => true), 'objects');
	foreach($post_types as $type => $obj) {
		$fields['single_' . $type] = array(
			'name' => 'single_' . $type,
			'title' => sprintf( __('Single %s', 'dp'), $obj->labels->singular_name ),
			'group' => 'single',
		);
	}
	
	/* Post Type Archives */
	$post_types = get_post_types(array('has_archive' => true), 'objects');
	foreach($post_types as $type => $obj) {
		$fields['archive_' . $type] = array(
			'name' => 'archive_' . $type,
			'title' => sprintf( __('Post Type Archive: %s', 'dp' ), $obj->labels->singular_name ),
			'group' => 'post_type_archive'
		);
	}
	
	/* Taxonomy Archives */
	$taxonomies = get_taxonomies(array( 'show_ui' => true ), 'objects');
	foreach($taxonomies as $tax => $obj) {
		$fields['tax_' . $tax] = array(
			'name' => 'tax_' . $tax,
			'title' => sprintf( __('Taxonomy Archive: %s', 'dp'), $obj->labels->singular_name ),
			'group' => 'tax'
		);
	}
	
	/* Author Archives */
	$fields['author'] = array(
		'name' => 'author',
		'title' => __('Author Archive', 'dp')
	);
	
	/* Date Archives */
	$fields['date'] = array(
		'name' => 'date',
		'title' => __('Date Archive', 'dp')
	);
	
	/* Search Pages */ 
	$fields['search'] = array(
		'name' => 'search',
		'title' => __('Search Result Page', 'dp')
	);

	/* 404 */
	$fields['404'] = array(
		'name' => '404',
		'title' => __('404 Page', 'dp')
	);
	
	if( !is_array($include) )
		$include = array_filter(explode(',', $include));
		
	if( !is_array($exclude) )
		$exclude = array_filter(explode(',', $exclude));

	if( !empty($include) || !empty($exclude) ) {
		foreach( $fields as $index => $field ) {
			$key = isset($field['group']) ? $field['group'] : $index;
		
			if( ($include && !in_array($key, $include)) || ($exclude && in_array($key, $exclude)) )
				unset($fields[$key]);
		}
	}

	return $fields;
}

function dp_get_logic_option( $settings = '' ) {
	global $wp_query;
	
	if(empty($settings) || !is_array($settings))
		return;
	
	$defaults = array_keys( dp_get_logic_fields() );
	$settings = wp_parse_args($settings, $defaults);

	if ( is_front_page() )
		$r = $settings['front_page'];

	elseif ( is_home() )
		$r = $settings['home'];
	
	elseif( is_singular() ) {
		global $post;
		$r = $settings['single_'.$post->post_type];
	}

	elseif ( is_archive() ) {

		if ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			
			$r = $settings['tax_'.$term->taxonomy];
		}

		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			
			$r = $settings['archive_'.$post_type];
		}

		elseif ( is_author() ) {
			$r = $settings['author'];

		} elseif ( is_date () ) {
			$r = $settings['date'];
		}
	}

	elseif ( is_search() ) {
		$r = $settings['search'];
	} 
	
	elseif ( is_404() ) {
		$r = $settings['404'];
	} 

	else {
		$r = $settings['global'];
	}
	
	$r = wp_kses_stripslashes($r);
	
	return $r;
}