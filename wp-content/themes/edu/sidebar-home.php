<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Edutainment_2016
 */

if ( ! is_active_sidebar( 'sidebar-start' ) ) {
	return;
}
?>

<div id="secondary-home" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-start' ); ?>
</div><!-- #secondary -->
