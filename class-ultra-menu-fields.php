<?php
/**
 * Menu item fields for Ultra Menu
 *
 * @credit Dzikri Aziz <kvcrvt@gmail.com>
 * @link https://github.com/kucrut/wp-menu-item-custom-fields
 */

class Ultra_Menu_Fields {
	/**
	 * Holds our custom fields
	 *
	 * @var    array
	 * @access protected
	 * @since  Menu_Item_Custom_Fields_Example 0.2.0
	 */
	protected static $fields = array();
	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		self::$fields = array(
		//	array (
		//		'id'	=> 'ultra-menu-text',
		//		'label'	=> __( 'Custom Field 1', 'ultra-menu' ),
		//		'type'	=> 'text'
		//	),
			array (
				'id'	=> 'ultra-menu-checkbox',
				'label'	=> __( 'Ultra Menu', 'ultra-menu' ),
				'type'	=> 'checkbox',
				'depth'	=> 0 // Will only display if the menu item is depth 0
			),
			array (
				'id'	=> 'ultra-menu-image',
				'label'	=> __( 'Menu Header Image', 'ultra-menu' ),
				'type'	=> 'image',
				'depth'	=> 1 // Will only display if the menu item is depth 1
			)
		);
	}
	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
		foreach ( self::$fields as $field ) {
			$key = sprintf( 'menu-item-%s', $field['id'] );
			// Sanitize
			if ( ! empty( $_POST[ $field['id'] ][ $menu_item_db_id ] ) ) {
				// Do some checks here...
				$value = $_POST[ $field['id'] ][ $menu_item_db_id ];
			}
			else {
				$value = null;
			}
			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $field['id'], $value );
			}
			else {
				delete_post_meta( $menu_item_db_id, $field['id'] );
			}
		}
	}
	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {
	//	foreach ( self::$fields as $_key => $label ) :
		foreach ( self::$fields as $field ) :
			$key   = sprintf( 'menu-item-%s', $field['id'] );
			$id    = sprintf( 'edit-%s-%s', $field['id'], $item->ID );
			$name  = sprintf( '%s[%s]', $field['id'], $item->ID );
			$value = get_post_meta( $item->ID, $field['id'], true );
			$class = sprintf( 'field-%s', $field['id'] );
			
			// We only add the extra field if we have the specified depth (or depth isn't specified)
			if ( ! isset ( $field['depth'] ) || $field['depth'] == $depth ) {
			
				// What type of field is it
				switch ( $field['type'] ) {
				
					// Checkbox
					case 'checkbox':
						?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php $selected = checked ( 1, $value, false );
							printf(
								'<label for="%1$s"><input type="checkbox" id="%1$s" class="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>',
								esc_attr( $id ),
								esc_attr( $name ),
								$selected,
								esc_html( $field['label'] )
							); ?>					
						</p>
					<?php break;
						
					// Text input
					case 'text': ?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php printf(
								'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
								esc_attr( $id ),
								esc_html( $field['label'] ),
								esc_attr( $name ),
								esc_attr( $value )
							); ?>					
						</p>
					<?php break;
				
					// Image uploader
					case 'image': ?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php printf(
								'<label for="%1$s">%2$s<br />
									<input type="hidden" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" />
								</label>',
								esc_attr( $id ),
								esc_html( $field['label'] ),
								esc_attr( $name ),
								esc_attr( $value )
							); ?>
							<div class="ultra_menu_image_wrapper" id="ultra_menu_image_wrapper_<?php echo esc_attr ( $id ); ?>" style="margin: 5px 0;">
								<?php if ( ! empty ( $value ) ) { ?>
									<?php echo wp_get_attachment_image ( $value, 'thumbnail' ); ?>
								<?php } ?>
							</div>
							<?php printf(
								'<input type="button" class="button button-secondary ultra_menu_upload" id="ultra_menu_upload_%1$s" name="ultra_menu_upload_%1$s" data-itemid="%1$s" value="%2$s" />',
								esc_attr ( $id ),
								__( 'Add Image', 'ultra-menu' )
							); ?>
							<?php printf(
								'<input type="button" class="button button-secondary ultra_menu_remove" id="ultra_menu_remove_%1$s" name="ultra_menu_remove_%1$s" data-itemid="%1$s" value="%2$s" />',
								esc_attr ( $id ),
								__( 'Remove Image', 'ultra-menu' )
							); ?>						
						</p>
					<?php break;
				}
			}
		endforeach;
	}
	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$new_cols = array();
		foreach ( self::$fields as $field ) :
			$new_cols[ $field['id']] = $field['label'];
		endforeach;
		$columns = array_merge( $columns, $new_cols );
		return $columns;
	}
}
Ultra_Menu_Fields::init();