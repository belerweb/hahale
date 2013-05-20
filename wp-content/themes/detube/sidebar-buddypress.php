<?php
/**
 * Sidebar Template
 *
 * @package deTube
 * @subpackage Tempalte
 * @since deTube 1.0
 */
?>

<div id="sidebar" role="complementary">
	<?php 
		if(is_active_sidebar('buddypress'))
			dynamic_sidebar('buddypress');
		else
			dynamic_sidebar('main');
	?>
</div><!--end #sidebar-->