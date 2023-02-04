<?php
/**
 * contact Form 7 Databse Listing
 *
 * @package       CF7DBLIST
 * @author        nidhi@alakmalak.com
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Contact Form 7 Database Listing
 * Plugin URI:    https://www.alakmalak.com
 * Description:   Database listing.
 * Version:       1.0.0
 * Author:        alakmalak
 * Author URI:    https://www.alakmalak.com
 * Text Domain:   cf7db-listing
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Event Listing. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// enqueue css script
 function cf7db_backend_scripts() {
	wp_enqueue_style( 'cf7db_style', plugins_url('assets/css/cf7db-style.css',__FILE__ ) );
}
add_action('wp_enqueue_scripts', 'cf7db_backend_scripts');
 
// create event post type
if ( ! function_exists( 'cf7db_postype' ) ) {
function cf7db_postype() {
	$labels = array(
		'name' => _x( 'Contact Form Enrty', 'cf7db' ),
		'singular_name'         => _x( 'Contact Form Enrty', 'cf7db' ),
		'menu_name'             => __( 'CF7Enrty', 'cf7db' ),
		'name_admin_bar'        => __( 'CF7Enrty', 'cf7db' ),
		'archives'              => __( 'CF7 Enrty Archives', 'cf7db' ),
		'attributes'            => __( 'CF7 Enrty Attributes', 'cf7db' ),
		'parent_item_colon'     => __( 'Parent CF7 Enrty:', 'cf7db' ),
		'all_items'             => __( 'All CF7 Enrty', 'cf7db' ),
		'add_new_item'          => __( 'Add New CF7 Enrty', 'cf7db' ),
		'add_new'               => __( 'Add New CF7 Enrty', 'cf7db' ),
		'new_item'              => __( 'CF7 Enrty', 'cf7db' ),
		'edit_item'             => __( 'Edit CF7 Enrty', 'cf7db' ),
		'update_item'           => __( 'Update CF7 Enrty', 'cf7db' ),
		'view_item'             => __( 'View CF7 Enrty', 'cf7db' ),
		'view_items'            => __( 'View CF7 Enrty', 'cf7db' ),
		'search_items'          => __( 'Search CF7 Enrty', 'cf7db' ),
		'not_found'             => __( 'CF7 Enrty Not found', 'cf7db' ),
		'not_found_in_trash'    => __( 'CF7 Enrty Not found in Trash', 'cf7db' ),
		'featured_image'        => __( 'CF7 Enrty Image', 'cf7db' ),
		'insert_into_item'      => __( 'Insert into CF7 Enrty', 'cf7db' ),
		'uploaded_to_this_item' => __( 'Uploaded to this CF7 Enrty', 'cf7db' ),
		'items_list'            => __( 'CF7 Enrty list', 'cf7db' ),
		'items_list_navigation' => __( 'CF7 Enrty list navigation', 'cf7db' ),
		'filter_items_list'     => __( 'Filter CF7 Enrty list', 'cf7db' ),
	);
	$args = array(
		'label'                 => __( 'CF7 Enrty', 'cf7db' ),
		'labels'                => $labels,
		'supports'              => array( 'title'),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-database-view',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		
		    
	);
	register_post_type( 'cf7db-list', $args );
	
}
add_action( 'init', 'cf7db_postype' );
}

// create metabox
function cf7db_metabox() {
	add_meta_box(
		'cf7db-metabox',
		__( 'Form details', 'cf7db-list' ),
		'cf7db_metabox_callback',
		'cf7db-list',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cf7db_metabox' );

function cf7db_metabox_callback( $post ) {
	
	$name= get_post_meta( $post->ID, 'cf7db-your-name', true );
	$email = get_post_meta( $post->ID, 'cf7db-your-email', true );
	$message = get_post_meta( $post->ID, 'cf7db-your-message', true );
	$formid = get_post_meta( $post->ID, 'cf7db-form-id', true );
?>
<table class="cf7db-list">
	<tr>
		<th>Form ID</th>
		<td><?php echo $formid; ?></td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo $name; ?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?php echo $email; ?></td>
	</tr>
	<tr>
		<th>Message</th>
		<td><?php echo $message; ?></td>
	</tr>
</table>
	<?php
}


add_action('wpcf7_mail_sent','save_form_data');
add_action('wpcf7_mail_failed','save_form_data');
function save_form_data($posted_data){
    $submission = WPCF7_Submission::get_instance();
    if (!$submission){
        return;
    }
    $posted_data = $submission->get_posted_data();
    $new_post = array();
	$wpcf7 = WPCF7_ContactForm::get_current();
    $form_id = $wpcf7->id();
    if(isset($posted_data['your-subject']) && !empty($posted_data['your-subject'])){
        $new_post['post_title'] = $posted_data['your-subject'];
    } else {
        $new_post['post_title'] = 'No subject add';
    }
    $new_post['post_type'] = 'cf7db-list'; //insert here your CPT

    if(isset($posted_data['your-message'])){
        //$new_post['post_content'] = $posted_data['your-message'];
    } else {
        $new_post['post_content'] = 'No Message was submitted';
    }
    $new_post['post_status'] = 'publish';
    //you can also build your post_content from all of the fields of the form, or you can save them into some meta fields
    if(isset($posted_data['your-email']) && !empty($posted_data['your-email'])){
        $new_post['meta_input']['sender_email_address'] = $posted_data['your-email'];
    }
    if(isset($posted_data['my-name']) && !empty($posted_data['your-name'])){
        $new_post['meta_input']['sender_name'] = $posted_data['your-name'];
    }
    //When everything is prepared, insert the post into your Wordpress Database
    if($post_id = wp_insert_post($new_post)){
		update_post_meta($post->ID, 'cf7db-form-id', $form_id);
		update_post_meta($post_id, 'cf7db-your-name', $posted_data['your-name']);
		update_post_meta($post_id, 'cf7db-your-email', $posted_data['your-email']);
		update_post_meta($post_id, 'cf7db-your-message', $posted_data['your-message']);
       //Everything worked, you can stop here or do whatever
    } else {
       //The post was not inserted correctly, do something (or don't ;) )
    }
    return;
}
