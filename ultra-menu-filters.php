<?php
/**
 * Filter navigation menu classes.
 *
 * @package Ultra Menu
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Add an ultra-menu-parent class to the menu item 
 */
function ultra_menu_nav_menu_css_class( $classes, $item, $args, $depth ) {
	// Our extra fields are saved as post meta data
	// Is this the start of an ultra menu?
	$ultra_menu = get_post_meta ( $item->ID, 'ultra-menu-checkbox', true );
	/*
	 * Is this the start of an Ultra Menu?
	 */
	if ( ! empty ( $ultra_menu ) ) {
		$classes[] = 'ultra-menu-parent';
	}
	$classes[] = 'ultra-menu-item ultra-menu-item-' . $depth;
	return $classes;
}
add_filter( 'nav_menu_css_class', 'ultra_menu_nav_menu_css_class', 10, 4 );


/*
 * Insert an image into the title, depth 1
 */
function ultra_menu_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
	$atts = array();
	$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
	$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
	$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
	$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
	$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
	
	$attributes = '';
	foreach ( $atts as $attr => $value ) {
		if ( ! empty( $value ) ) {
			$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}
	}

	$title = apply_filters( 'the_title', $item->title, $item->ID );

	/**
	 * Filter a menu item's title.
	 *
	 * @since 4.4.0
	 *
	 * @param string $title The menu item's title.
	 * @param object $item  The current menu item.
	 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
	 * @param int    $depth Depth of menu item. Used for padding.
	 */
	$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

	$item_output = $args->before;
	$item_output .= '<a'. $attributes .'>';
	
	// Has the menu item got an image uploaded?
	$image_id = get_post_meta( $item->ID, 'ultra-menu-image', true );
	// Only add the image at depth 1
	if ( $depth == 1 && isset ( $image_id ) ) {
		$item_output .= '<div class="ultra-menu-image">';
		$item_output .= wp_get_attachment_image ( $image_id, 'medium' );
		$item_output .= '</div>';
	}
	$item_output .= $args->link_before . $title . $args->link_after;

	$item_output .= '</a>';
	$item_output .= $args->after;
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'ultra_menu_walker_nav_menu_start_el', 10, 4 );

/*
 * Add a chevron to the title, depth 2
 */
function ultra_menu_nav_menu_item_title( $title, $item, $args, $depth ) {
	if ( $depth == 2 ) {
		$title = '<i class="fa fa-angle-right"></i> ' . $title;
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'ultra_menu_nav_menu_item_title', 5, 4 );