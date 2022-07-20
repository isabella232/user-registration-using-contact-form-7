<?php
/**
 * ZURCF7_Admin_Action Class
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @package Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ZURCF7_Admin_Action' ) ) {

	/**
	 *  The ZURCF7_Admin_Action Class
	 */
	class ZURCF7_Admin_Action {

		function __construct()  {

			add_action( 'admin_init', array( $this, 'action__admin_init' ) );
			add_action( 'add_meta_boxes', 	array( $this, 'action__add_meta_boxes' ) );

			add_action( 'manage_'.ZURCF7_POST_TYPE.'_posts_custom_column',  array( $this, 'action__manage_zurcf7_data_posts_custom_column' ), 10, 2 );

		}

		/*
		   ###     ######  ######## ####  #######  ##    ##  ######
		  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
		 ##   ##  ##          ##     ##  ##     ## ####  ## ##
		##     ## ##          ##     ##  ##     ## ## ## ##  ######
		######### ##          ##     ##  ##     ## ##  ####       ##
		##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##     ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/**
		 * Action: admin_init
		 *
		 * - Register admin min js and admin min css
		 *
		 */
		function action__admin_init() {
			
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			if(!in_array('administrator',$roles)){
				remove_menu_page('edit.php?post_type='.ZURCF7_POST_TYPE); // Pages
				if( isset($_GET['post_type']) && (ZURCF7_POST_TYPE ===  $_GET['post_type']) ){
					wp_die("Access denied",ZURCF7_TEXT_DOMAIN);
				}
			}
			if( isset($_GET['post_type']) && (ZURCF7_POST_TYPE ===  $_GET['post_type']) ){
				wp_register_script( ZURCF7_PREFIX . '-admin-js', ZURCF7_URL . 'assets/js/admin.min.js', array( 'jquery-core' ), ZURCF7_VERSION );
				wp_register_style( ZURCF7_PREFIX . '-admin-css', ZURCF7_URL . 'assets/css/admin.min.css', array(), ZURCF7_VERSION );

				// Localize the script with new data
				$translation_array = array(
					'zurcf7_formid_msg' => __( '<h3>Select Registration Form</h3>' .
								'<p>Select the Form for User Registration.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_skipcf7_email_msg' => __( '<h3>Skip Contact Form 7 Email</h3>' .
								'<p>Tick the checkbox to skip default contact form 7 email.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_debug_mode_status_msg' => __( '<h3>Enable Debug Mode</h3>' .
								'<p>Tick the checkbox to enable debug mode for generating logs.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_email_field_msg' => __( '<h3>Select Email Field</h3>' .
								'<p>Select the field name for User Email Address.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_username_field_msg' => __( '<h3>Select Username Field</h3>' .
								'<p>Select the field name for Username.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_userrole_field_msg' => __( '<h3>Select User Role Field</h3>' .
								'<p>Select the User Role for user registration.</p>', 'zeal-user-reg-cf7' ),
					'zurcf7_successurl_field_msg' => __( '<h3>Select Success URL</h3>' .
								'<p>Select the page for user redirection after the registration process is complete.</p>', 'zeal-user-reg-cf7' ),
				);

				
				wp_localize_script( ZURCF7_PREFIX . '-admin-js', 'cf7forms_data', $translation_array );
			}
		}


		/**
		 * Action: add_meta_boxes
		 *
		 * - Add meta boxes for the CPT "zurcf7_data"
		 */
		function action__add_meta_boxes() {
			add_meta_box( 'zurcf7-data', __( 'Registration Form Data', 'zeal-user-reg-cf7' ), array( $this, 'zurcf7_show_from_data' ), ZURCF7_POST_TYPE, 'normal', 'high' );
		}

		/**
		 * - Use to display the form data in CPT detail page.
		 *
		 * @method zurcf7_show_from_data
		 *
		 * @param  object $post WP_Post
		 */
		function zurcf7_show_from_data( $post ) {
			
			$form_id = get_post_meta( $post->ID, ZURCF7_META_PREFIX.'form_id',true);
			$form_title = get_post_meta( $post->ID, ZURCF7_META_PREFIX.'form_title',true);
			$user_login = get_post_meta( $post->ID, ZURCF7_META_PREFIX.'user_login',true);
			$user_email = get_post_meta( $post->ID, ZURCF7_META_PREFIX.'user_email',true);
			$user_role = get_post_meta( $post->ID, ZURCF7_META_PREFIX.'role',true);
			
			echo "<style>.post-type-zuserreg_data #zurcf7-data .handle-order-higher,
			.post-type-zuserreg_data #zurcf7-data .handle-order-lower {
				display: none;
			}</style>";
			echo '<table class="cf7pap-box-data form-table">' .
				'<style>.inside-field td, .inside-field th{ padding-top: 5px; padding-bottom: 5px;}</style>';

			echo '<tr class="form-field">' .
				'<th scope="row">' .
					'<label for="hcf_author">' . __( 'CF7 Form Name', 'zeal-user-reg-cf7' ) . '</label>' .
				'</th>' .
				'<td>'.$form_title.'</td>' .
			'</tr>';

			echo '<tr class="form-field">' .
				'<th scope="row">' .
					'<label for="hcf_author">' . __( 'User Email', 'zeal-user-reg-cf7' ) . '</label>' .
				'</th>' .
				'<td>'.$user_email.'</td>' .
			'</tr>';

			echo '<tr class="form-field">' .
				'<th scope="row">' .
					'<label for="hcf_author">' . __( 'User Name', 'zeal-user-reg-cf7' ) . '</label>' .
				'</th>' .
				'<td>'.$user_login.'</td>' .
			'</tr>';

			

			echo '<tr class="form-field">' .
				'<th scope="row">' .
					'<label for="hcf_author">' . __( 'User Role', 'zeal-user-reg-cf7' ) . '</label>' .
				'</th>' .
				'<td>'.ucfirst($user_role).'</td>' .
			'</tr>';

			echo '</table>';
		}


		/**
		 * Action: manage_data_posts_custom_column
		 *
		 * @method manage_zurcf7_data_posts_custom_column
		 *
		 * @param  string  $column
		 * @param  int     $post_id
		 *
		 * @return string
		 */
		function action__manage_zurcf7_data_posts_custom_column( $column, $post_id ) {
			
			switch ( $column ) {

				case ZURCF7_META_PREFIX.'user_login' :
					echo get_post_meta( $post_id , ZURCF7_META_PREFIX.'user_login', true );
				break;

				
				case ZURCF7_META_PREFIX.'role' :
					echo ( !empty( get_post_meta( $post_id , ZURCF7_META_PREFIX.'role', true ) ) ? get_post_meta( $post_id , ZURCF7_META_PREFIX.'role', true ) : '' );
				break;

			}
		}


		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/


		


	}

	add_action( 'plugins_loaded', function() {
		ZURCF7()->admin->action = new ZURCF7_Admin_Action;
	} );
}
