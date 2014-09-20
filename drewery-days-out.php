<?php
/*
Plugin Name: Drewery Caravan Park Days Out
Plugin URI: https://github.com/gerrytucker/drewery-days-out
Description: Manage Drewery Caravan Park Days Out
Version: 1.1.7
Author: Gerry Tucker
Author URI: http://github.com/gerrytucker/
GitHub Plugin URI: https://github.com/gerrytucker/drewery-days-out
*/


// Register Custom Post Type
function custom_post_types() {

	$labels = array(
		'name'                => _x( 'Days Out', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Day Out', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Days Out', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Day Out:', 'text_domain' ),
		'all_items'           => __( 'All Days Out', 'text_domain' ),
		'view_item'           => __( 'View Day Out', 'text_domain' ),
		'add_new_item'        => __( 'Add New Day Out', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Day Out', 'text_domain' ),
		'update_item'         => __( 'Update Day Out', 'text_domain' ),
		'search_items'        => __( 'Search Days Out', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'daysout', 'text_domain' ),
		'description'         => __( 'Day Out Post Type', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 6,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'menu_icon' => ''
	);
	register_post_type( 'daysout', $args );

}
// Hook into the 'init' action
add_action( 'init', 'custom_post_types', 0 );

/** Enqueue Admin Scripts */
function ddo_days_out_admin_enqueue_scripts()
{
	wp_register_style( 'ddo_days_out_admin_style', plugins_url( 'css/drewery-days-out.css', __FILE__ ) );
	wp_enqueue_style( 'ddo_days_out_admin_style' );
}
add_action( 'admin_enqueue_scripts', 'ddo_days_out_admin_enqueue_scripts' );



function ddo_days_out_add_meta_box()
{
	add_meta_box(
		'ddo_days_out_meta_box_id',
		__('Day Out Detail', 'drewery-days-out'),
		'ddo_days_out_meta_box_callback',
		'daysout'
	);
}
add_action( 'add_meta_boxes', 'ddo_days_out_add_meta_box' );

function ddo_days_out_meta_box_callback( $post )
{
	// Add a nonce field for later
	wp_nonce_field(
		'ddo_days_out_meta_box',
		'ddo_days_out_meta_box_nonce'
	);

	$address = get_post_meta( $post->ID, '_ddo_address', true );
	$address = wpautop( $address );
	$uri = get_post_meta( $post->ID, '_ddo_uri', true );
	$type = get_post_meta( $post->ID, '_ddo_type', true );

?>

	<div style="margin: 10px;">
		<p>
			<label for="ddo_address">Address: </label><br>
			<textarea id="ddo_address" name="ddo_address" rows="8" cols="30" required><?php echo $address; ?></textarea>
		</p>
		<p>
			<label for="ddo_uri">URI:</label><br>
			<input type="url" id="ddo_uri" name="ddo_uri" required value="<?php echo $uri; ?>">
		</p>
		<p>
			<input type="radio" name="ddo_type" id="att" value="ATT" <?php if ( $type == "ATT" || $type == "" ) echo 'checked="checked"'; ?>><label for="ddo_type"> Attraction</label><br>
			<input type="radio" name="ddo_type" id="poi" value="POI" <?php if ( $type == "POI" ) echo 'checked="checked"'; ?>><label for="ddo_type"> Place Of Interest</label><br>
		</p>
	</div>

<?php
}



function ddo_days_out_save_meta_box_data( $post_id )
{
	if ( ! isset( $_POST['ddo_days_out_meta_box_nonce'] ) )
		return;

	if ( ! wp_verify_nonce( $_POST['ddo_days_out_meta_box_nonce'], 'ddo_days_out_meta_box' ) )
		return;

	if ( defined ( 'DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	if ( isset( $_POST['post_type'] ) && 'event' == $_POST['post_type'] )
	{
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	}
	else
	{
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( ! isset( $_POST['ddo_address'] ) && ! isset( $_POST['ddo_uri'] ) && ! isset( $_POST['ddo_type'] ) )
		return;

	$address = $_POST['ddo_address'];
	update_post_meta( $post_id, '_ddo_address', $address );

	$uri = $_POST['ddo_uri'];
	update_post_meta( $post_id, '_ddo_uri', $uri );

	$type = $_POST['ddo_type'];

	update_post_meta( $post_id, '_ddo_address', $address );

}
add_action( 'save_post', 'ddo_days_out_save_meta_box_data' );


