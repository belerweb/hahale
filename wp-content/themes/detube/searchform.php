<?php
/**
 * Search Form Template
 *
 * The search form template displays the search form.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>

<div class="searchform-div">
	<form method="get" class="searchform" action="<?php echo home_url(); ?>/">
		<div class="search-text-div"><input type="text" name="s" class="search-text" value="" placeholder="<?php _e('Search...', 'dp') ?>" /></div>
		<div class="search-submit-div btn"><input type="submit" class="search-submit" value="<?php _e('Search', 'dp') ?>" /></div>
	</form><!--end #searchform-->
</div>