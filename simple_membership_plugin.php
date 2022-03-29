<?php
/*
Plugin Name: Simple Membership Plugin
Plugin URI: https://younusm.com/
Description: Simple membership.
Version: 4.2.1
Author: younusm
Author URI: https://younusm.com/
License: GPLv2 or later
Text Domain: simple-membership-plugin
Domain Path: /languages/
*/


require_once __DIR__ . '/cmb2/init.php';



add_action( 'cmb2_admin_init', 'cmb2_sample_metaboxes' );

function cmb2_sample_metaboxes() {

	$cmb = new_cmb2_box( array(
		'id'            => 'test_metabox',
		'title'         => __( 'Test Metabox', 'cmb2' ),
		'object_types'  => array( 'page','post', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names'    => true, 
	) );

    $cmb->add_field( array(
		'name' => esc_html__( 'Premium', 'cmb2' ),
		'desc' => esc_html__( 'For premium subscriber', 'cmb2' ),
		'id'   => 'premium',
		'type' => 'checkbox',
	) );

	$terms = get_terms( array(
		'taxonomy' => 'departments',
		'hide_empty' => false,
	) );

	$term_id = [];
	foreach($terms as $term){
		$term_id[] =  $term->term_id;
	}

	$term_name = [];
	foreach($terms as $term){
		$term_name[] =  $term->name;
	}

	$terms_id_and_name = array_combine($term_id, $term_name);

	$cmb->add_field( array(
		'name'    => 'Select membershhip',
		'id'      => 'selected_user12',
		'desc'    => 'Select user. Drag to reorder.',
		'type'    => 'pw_multiselect',
		'options' => $terms_id_and_name
	) );
}	


function add_read_more_button2($content){

	global $post;

	$premium_post = get_post_meta($post->ID, 'premium', true);
	$selected_memberships_term_id = get_post_meta($post->ID, 'selected_user12', true);
	//var_dump($selected_memberships_term_id);

	$category_with_post = get_the_category($post->ID);
	//var_dump($category_with_post);

	$categories_id_in_post = [];
	foreach($category_with_post as $post){
		$categories_id_in_post[]=$post->term_id;
		//var_dump($arr);
	}

	var_dump($categories_id_in_post);

	$selected_memberships_in_categories = [];
	foreach ($categories_id_in_post as $id){
		//get_term_meta($id);
		$selected_memberships_in_categories[]= get_term_meta($id);
	}
	//var_dump($selected_memberships_in_categories);

$arr = $selected_memberships_in_categories;



var_dump($arr);
$var2 = array_values(array_filter($selected_memberships_in_categories));
//print_r(array_values(array_filter($arr)));
var_dump($var2);
var_dump($var2[0]['selected_user_in_category']);


die();





	$category_id_with_membership = $category_with_post[0]->term_id;
	//var_dump($category_id_with_membership);

	$selected_membership_in_category = get_term_meta($categories_id_in_post[2]);
	var_dump($selected_membership_in_category);
	$term_vals23 = get_term_meta(4);
	var_dump($term_vals23);
	die();

	$all_selected_user_in_post = [];
	foreach($selected_memberships_term_id as $term_id){
	
		$term_vals = get_term_meta($term_id);

		// $all_selected_user_in_post = [];
		foreach($term_vals as $key=>$val){

			$selected_user_in_post = $val[0];
			//var_dump($selected_user_in_post);
			$final_selected_user_in_post = unserialize($selected_user_in_post);
			$all_selected_user_in_post = array_merge($all_selected_user_in_post, $final_selected_user_in_post);
			//var_dump($all_selected_user_in_post);

		}
	}
	

	if($premium_post ){

		if(is_user_logged_in()){
			$logedin_user_value= wp_get_current_user();
			
			$current_logedin_user_id = strval($logedin_user_value->ID);
			//var_dump($current_logedin_user_id);
			

			if(in_array($current_logedin_user_id, $all_selected_user_in_post)){
				//echo 'User ID: ' . get_current_user_id();
				$content .= 'premium content and user loged in';
				return $content;
			}else{
				?>
				<p>You are loged in but not permitted for this content</p>
				<?php
			}	
		}else{
			
			?>
				<p>This content is premium. If you want to read please login</p>
				<a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login"> Login</a>
			<?php
		}
		
	}else{
		return $content;
	}
}

add_action('the_content','add_read_more_button2');




// Register Custom Taxonomy
function custom_taxonomy() {

	$labels = array(
	  'name'                       => _x( 'Memberships', 'Memberships Name', 'text_domain' ),
	  'singular_name'              => _x( 'Membership', 'Membership Name', 'text_domain' ),
	  'menu_name'                  => __( 'Memberships', 'text_domain' ),
	  'all_items'                  => __( 'All Memberships', 'text_domain' ),
	  'parent_item'                => __( 'Parent Membership', 'text_domain' ),
	  'parent_item_colon'          => __( 'Parent Membership:', 'text_domain' ),
	  'new_item_name'              => __( 'New Membership Name', 'text_domain' ),
	  'add_new_item'               => __( 'Add Membership', 'text_domain' ),
	  'edit_item'                  => __( 'Edit Membership', 'text_domain' ),
	  'update_item'                => __( 'Update Membership', 'text_domain' ),
	  'view_item'                  => __( 'View Membership', 'text_domain' ),
	  'separate_items_with_commas' => __( 'Separate Membership with commas', 'text_domain' ),
	  'add_or_remove_items'        => __( 'Add or remove Memberships', 'text_domain' ),
	  'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
	  'popular_items'              => __( 'Popular Memberships', 'text_domain' ),
	  'search_items'               => __( 'Search Memberships', 'text_domain' ),
	  'not_found'                  => __( 'Not Found', 'text_domain' ),
	  'no_terms'                   => __( 'No Memberships', 'text_domain' ),
	  'items_list'                 => __( 'Memberships list', 'text_domain' ),
	  'items_list_navigation'      => __( 'Memberships list navigation', 'text_domain' ),
	);
	$args = array(
	  'labels'                     => $labels,
	  'hierarchical'               => true,
	  'public'                     => true,
	  'show_ui'                    => true,
	  'show_admin_column'          => true,
	  'show_in_nav_menus'          => true,
	  'show_tagcloud'              => true,
	);
	register_taxonomy( 'departments', 'user', $args );
  
  }
  add_action( 'init', 'custom_taxonomy', 0 );





/**
 * Admin page for the 'departments' taxonomy
 */
function cb_add_departments_taxonomy_admin_page() {

	$tax = get_taxonomy( 'departments' );
  
	add_users_page(
	  esc_attr( $tax->labels->menu_name ),
	  esc_attr( $tax->labels->menu_name ),
	  $tax->cap->manage_terms,
	  'edit-tags.php?taxonomy=' . $tax->name
	);
  
  }
  add_action( 'admin_menu', 'cb_add_departments_taxonomy_admin_page' );

/**
 * Unsets the 'posts' column and adds a 'users' column on the manage departments admin page.
 */
function cb_manage_departments_user_column( $columns ) {

	unset( $columns['posts'] );
  
	$columns['users'] = __( 'Users' );
  
	return $columns;
  }
  add_filter( 'manage_edit-departments_columns', 'cb_manage_departments_user_column' );

/**
 * @param string $display WP just passes an empty string here.
 * @param string $column The name of the custom column.
 * @param int $term_id The ID of the term being displayed in the table.
 */
function cb_manage_departments_column( $display, $column, $term_id ) {

	if ( 'users' === $column ) {
	  $term = get_term( $term_id, 'departments' );
	  echo $term->count;
	}
  }
  add_filter( 'manage_departments_custom_column', 'cb_manage_departments_column', 10, 3 );




/** 
  * Add metabox in user membership  page 
*/ 


  add_action( 'cmb2_admin_init', 'yourprefix_register_taxonomy_metabox' ); 
 /** 
  * Hook in and add a metabox to add fields to taxonomy terms 
  */ 
 function yourprefix_register_taxonomy_metabox() { 
 	$prefix = 'yourprefix_term_'; 
  
 	/** 
 	 * Metabox to add fields to categories and tags 
 	 */ 
 	$cmb_term = new_cmb2_box( array( 
 		'id'               => $prefix . 'edit', 
 		'title'            => esc_html__( 'Category Metabox', 'cmb2' ), // Doesn't output for term boxes 
 		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta 
 		'taxonomies'       => array( 'departments', ), // Tells CMB2 which taxonomies should have these fields 
 		// 'new_term_section' => true, // Will display in the "Add New Category" section 
 	) ); 
   

	 $all_users_data = get_users();
	 $users_id = [];
	 foreach($all_users_data as $user){	
		 $users_id[] =  $user->ID;
	 };
	 
	 $users_name = [];
	 foreach($all_users_data as $username){	
		 $users_name[] =  $username->display_name;
	 };
	 $users_id_and_name = array_combine($users_id, $users_name);
 
	 $cmb_term->add_field( array(
		 'name'    => 'Select User',
		 'id'      => 'selected_user11',
		 'desc'    => 'Select user. Drag to reorder.',
		 'type'    => 'pw_multiselect',
		 'options' => $users_id_and_name
	 ) ); 

  
 } 






/** 
  * Add metabox in category page 
*/ 



 add_action( 'cmb2_admin_init', 'yourprefix_register_taxonomy_metabox_for_category' ); 
 /** 
  * Hook in and add a metabox to add fields to taxonomy terms 
  */ 
 function yourprefix_register_taxonomy_metabox_for_category() { 
 	$prefix = 'yourprefix_term_'; 
  
 	/** 
 	 * Metabox to add fields to categories and tags 
 	 */ 
 	$cmb_term = new_cmb2_box( array( 
 		'id'               => $prefix . 'edit2', 
 		'title'            => esc_html__( 'Category Metabox', 'cmb2' ), // Doesn't output for term boxes 
 		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta 
 		'taxonomies'       => array( 'category', ), // Tells CMB2 which taxonomies should have these fields 
 		// 'new_term_section' => true, // Will display in the "Add New Category" section 
 	) ); 
   

	 $terms = get_terms( array(
		'taxonomy' => 'departments',
		'hide_empty' => false,
	) );

	$term_id = [];
	foreach($terms as $term){
		$term_id[] =  $term->term_id;
	}

	$term_name = [];
	foreach($terms as $term){
		$term_name[] =  $term->name;
	}

	$terms_id_and_name = array_combine($term_id, $term_name);

	$cmb_term->add_field( array(
		'name'    => 'Select membershhip',
		'id'      => 'selected_user_in_category',
		'desc'    => 'Select user. Drag to reorder.',
		'type'    => 'pw_multiselect',
		'options' => $terms_id_and_name
	) );

	 

  
 } 









	
	


