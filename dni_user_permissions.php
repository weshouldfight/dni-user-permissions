<?php
/*
Plugin Name: Intranet Benutzer-Rechte
Plugin URI: http://www.das-neue-intranet.de
Description: Rechte der WordPress-Benutzer verwalten.
Version: 1.1
Author: studioh8
Author URI: http://www.studioh8.de
License: GPL2
*/



/*-------------------------------------------
	ADD TO ADMIN MENU > USERS
-------------------------------------------*/

// Hook for adding admin menus
add_action('admin_menu', 'dni_add_pages');

// action function for above hook
function dni_add_pages() {
    global $dni_user_permissions;// Add a new submenu under Settings:
    $dni_user_permissions = add_users_page(__('Benutzerrechte','user-permissions'), __('Benutzerrechte','user-permissions'), 'manage_options', 'user-permissions', 'dni_settings_page');
}


/*-------------------------------------------------
	RESTRICT USER QUERY TO ONE ROLE (optional)
-------------------------------------------------*/

$mng_role = '';



/*-------------------------------------------
	DISPLAY THE SETTINGS PAGE
-------------------------------------------*/

function dni_settings_page() {
	
	global $mng_role;
	
	$post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
	//$dont_touch = get_post_types( array( 'capability_type' => 'post' ) );
	//$post_types = array_diff( $post_types, $dont_touch );
	
	?>
			
    
    <div class="wrap">
    	<h2><?php echo __( 'Benutzerrechte', 'user-permissions' ); ?></h2>
    
		<form id="dni-form" action="<?php echo admin_url( 'admin.php' ); ?>" method="POST">
		<input type="hidden" name="action" value="dni" />
		<?php wp_nonce_field( 'testtest' ); ?>
		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th><?php _e('Benutzername', 'dni'); ?></th>
					<th><?php _e('Name', 'dni'); ?></th>
					<th><?php _e('Rechte', 'dni'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Benutzername', 'dni'); ?></th>
					<th><?php _e('Name', 'dni'); ?></th>
					<th><?php _e('Rechte', 'dni'); ?></th>
				</tr>
			</tfoot>
			<tbody>
			<?php $user_query = new WP_User_Query( array( 'role' => $mng_role ) );
			// var_dump($user_query);
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) { 
					$user_role = $user->roles; ?>
					<tr>
						<td><?php echo $user->user_login; ?><?php if ($user_role == 'administrator') {echo ' <small>(Admin)</small>';} ?></td>
						<td><?php echo $user->display_name; ?></td>
						<td>
						<?php foreach( $post_types as $post_type ) { 
							$post_type_details 	= get_post_type_object( $post_type );
							$post_type_cap 		= $post_type_details->capability_type;
							$post_type_caps		= $post_type_details->cap;			
							$post_type_name		= $post_type_details->labels->name;
							$input_id			= $user->ID.'_can_'.$post_type_cap;
							
							if ($user_role != 'administrator') { ?>
								<input name="<?php echo $input_id ?>" id="<?php echo $input_id ?>" type="checkbox" value="1" <?php checked(isset($user->allcaps[$post_type_caps->edit_posts])); ?> /> <?php echo $post_type_name; ?><br />
								<?php } else { ?>
								<input name="<?php echo $input_id ?>" id="<?php echo $input_id ?>" type="hidden" value="1" <?php checked(isset($user->allcaps[$post_type_caps->edit_posts])); ?> />
								<input name="<?php echo $input_id ?>-dis" id="<?php echo $input_id ?>-dis" type="checkbox" value="1" <?php checked(isset($user->allcaps[$post_type_caps->edit_posts])); ?> disabled /> <?php echo $post_type_name; ?><br />
								<?php } ?>
							<?php } ?>
						</td>
					</tr>
					<?php } } else { ?>
					<tr>
						<td>Nichts gefunden.</td>
						<td></td>
						<td></td>
					</tr>
			<?php } ?>
			</tbody>
		</table>
		<p><input type="submit" name="dni-submit" id="dni_submit" class="button-primary" value="<?php _e('Save Permissions', 'dni'); ?>"/></p>
		</form>
	</div>

<?php } 


/*-------------------------------------------
	ADD/REMOVE CAPABILITIES
-------------------------------------------*/

function dni_admin_action() {
	
	check_admin_referer( 'testtest' );	
	global $mng_role;
	
	/* Hier kommt die Aktion */
	
	$list_users = new WP_User_Query( array( 'role' => $mng_role ) );
		if ( ! empty( $list_users->results ) ) {
			
			$post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
			foreach( $post_types as $post_type ) {
			
				$post_type_details 	= get_post_type_object( $post_type );
				$post_type_cap 		= $post_type_details->capability_type;
				$post_type_caps		= $post_type_details->cap;
				
				foreach ( $list_users->results as $user ) {
				
					$input_id = $user->ID.'_can_'.$post_type_cap;
					
					if ( isset( $_POST[$input_id] ) ) {
						foreach( $post_type_caps as $cap ) {
							$user->add_cap( $cap );
						}
					} else {
						foreach( $post_type_caps as $cap ) {
							$user->remove_cap( $cap );
						}
					}
					
				} /* End foreach $user */
			} /* End foreach $post_type */
			
		}/* End if list_users */
		else {}
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	
	/* Ende Aktion */
	
}
add_action('admin_action_dni', 'dni_admin_action');

?>
