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
		'object_types'  => array( 'page','post' ), // Post type
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

	$cmb->add_field( array(
		'name'    => 'Select User',
		'id'      => 'selected_user',
		'desc'    => 'Select user. Drag to reorder.',
		'type'    => 'pw_multiselect',
		'options' => $users_id_and_name
	) );
}	



function add_read_more_button($content){

	global $post;
	$premium_post = get_post_meta($post->ID, 'premium', true);
	$selected_elegible_users = get_post_meta($post->ID, 'selected_user', true);

	if($premium_post ){

		if(is_user_logged_in()){
			$logedin_user_name = wp_get_current_user();
			$final_user_name = $logedin_user_name->user_login;

			if(in_array($final_user_name, $selected_elegible_users)){
				echo 'User ID: ' . get_current_user_id();
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

add_action('the_content','add_read_more_button');






// Register Custom Taxonomy
function custom_taxonomy() {

	$labels = array(
	  'name'                       => _x( 'Departments', 'Departments Name', 'text_domain' ),
	  'singular_name'              => _x( 'Department', 'Department Name', 'text_domain' ),
	  'menu_name'                  => __( 'Departments', 'text_domain' ),
	  'all_items'                  => __( 'All Departments', 'text_domain' ),
	  'parent_item'                => __( 'Parent Department', 'text_domain' ),
	  'parent_item_colon'          => __( 'Parent Department:', 'text_domain' ),
	  'new_item_name'              => __( 'New Department Name', 'text_domain' ),
	  'add_new_item'               => __( 'Add Department', 'text_domain' ),
	  'edit_item'                  => __( 'Edit Department', 'text_domain' ),
	  'update_item'                => __( 'Update Department', 'text_domain' ),
	  'view_item'                  => __( 'View Department', 'text_domain' ),
	  'separate_items_with_commas' => __( 'Separate department with commas', 'text_domain' ),
	  'add_or_remove_items'        => __( 'Add or remove departments', 'text_domain' ),
	  'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
	  'popular_items'              => __( 'Popular Departments', 'text_domain' ),
	  'search_items'               => __( 'Search Departments', 'text_domain' ),
	  'not_found'                  => __( 'Not Found', 'text_domain' ),
	  'no_terms'                   => __( 'No departments', 'text_domain' ),
	  'items_list'                 => __( 'Departments list', 'text_domain' ),
	  'items_list_navigation'      => __( 'Departments list navigation', 'text_domain' ),
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
 * @param object $user The user object currently being edited.
 */
function cb_edit_user_department_section( $user ) {
	global $pagenow;
  
	$tax = get_taxonomy( 'departments' );
  
	/* Make sure the user can assign terms of the departments taxonomy before proceeding. */
	if ( !current_user_can( $tax->cap->assign_terms ) )
	  return;
  
	/* Get the terms of the 'departments' taxonomy. */
	$terms = get_terms( 'departments', array( 'hide_empty' => false ) ); ?>
  
	<h3><?php _e( 'Departments' ); ?></h3>
  
	<table class="form-table">
  
	  <tr>
		<th><label for="departments"><?php _e( 'Allocated Departments' ); ?></label></th>
  
		<td><?php
  
		/* If there are any departments terms, loop through them and display checkboxes. */
		if ( !empty( $terms ) ) {
			echo cb_custom_form_field('departments', $terms, $user->ID);
		}
  
		/* If there are no departments terms, display a message. */
		else {
		  _e( 'There are no departments available.' );
		}
  
		?></td>
	  </tr>
  
	</table>
  <?php }
  
  add_action( 'show_user_profile', 'cb_edit_user_department_section' );
  add_action( 'edit_user_profile', 'cb_edit_user_department_section' );
  add_action( 'user_new_form', 'cb_edit_user_department_section' );






  

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
 		'taxonomies'       => array( 'departments', 'post_tag' ), // Tells CMB2 which taxonomies should have these fields 
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













	
	


