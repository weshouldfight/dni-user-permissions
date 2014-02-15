<?php
/*
Plugin Name: Intranet Benutzer-Rechte
Plugin URI: http://www.das-neue-intranet.de
Description: Rechte der WordPress-Benutzer verwalten.
Version: 1.0
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


/*-------------------------------------------
	DISPLAY THE SETTINGS PAGE
-------------------------------------------*/

function dni_settings_page() { ?>
    
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
			<?php $user_query = new WP_User_Query( array( 'role' => 'Author' ) );
			var_dump($user_query);
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) { ?>
					<tr>
						<td><?php echo $user->user_login; ?></td>
						<td><?php echo $user->display_name; ?></td>
						<td>
							<input name="<?php echo $user->ID; ?>_can_news" id="<?php echo $user->ID; ?>_can_news" type="checkbox" value="1" <?php checked(isset($user->allcaps['edit_posts'])); ?> /> News<br />
							<input name="<?php echo $user->ID; ?>_can_dokument" id="<?php echo $user->ID; ?>_can_dokument" type="checkbox" value="1" <?php checked(isset($user->allcaps['edit_dokument'])); ?> /> Dokumente<br />
							<input name="<?php echo $user->ID; ?>_can_mitarbeiter" id="<?php echo $user->ID; ?>_can_mitarbeiter" type="checkbox" value="1" <?php checked(isset($user->allcaps['edit_mitarbeiter'])); ?> /> Mitarbeiter<br />
							<input name="<?php echo $user->ID; ?>_can_produkt" id="<?php echo $user->ID; ?>_can_produkt" type="checkbox" value="1" <?php checked(isset($user->allcaps['edit_produkt'])); ?> /> Produkte
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
	
	/* Hier kommt die Aktion */
	
	$list_users = new WP_User_Query( array( 'role' => 'Author' ) );
		if ( ! empty( $list_users->results ) ) {
			$cap_news = array( 'edit_posts', 'read_posts', 'delete_posts' );
			$cap_dokumente = array( 'edit_dokument', 'read_dokument', 'delete_dokument' );
			$cap_mitarbeiter = array( 'edit_mitarbeiter', 'read_mitarbeiter', 'delete_mitarbeiter' );
			$cap_produkte = array( 'edit_produkt', 'read_produkt', 'delete_produkt' );
			foreach ( $list_users->results as $user ) {
				
				/* Check News */
				$user_id = $user->ID;
				if ( isset( $_POST[$user->ID.'_can_news'] ) ) {
					foreach( $cap_news as $cap ) {
						$user->add_cap( $cap );
					}
				} else {
					foreach( $cap_news as $cap ) {
						$user->remove_cap( $cap );
					}
				}
				
				/* Check Dokumente */
				$user_id = $user->ID;
				if ( isset( $_POST[$user->ID.'_can_dokument'] ) ) {
					foreach( $cap_dokumente as $cap ) {
						$user->add_cap( $cap );
					}
				} else {
					foreach( $cap_dokumente as $cap ) {
						$user->remove_cap( $cap );
					}
				}
				
				/* Check Mitarbeiter */
				$user_id = $user->ID;
				if ( isset( $_POST[$user->ID.'_can_mitarbeiter'] ) ) {
					foreach( $cap_mitarbeiter as $cap ) {
						$user->add_cap( $cap );
					}
				} else {
					foreach( $cap_mitarbeiter as $cap ) {
						$user->remove_cap( $cap );
					}
				}
				
				/* Check Produkte */
				$user_id = $user->ID;
				if ( isset( $_POST[$user->ID.'_can_produkt'] ) ) {
					foreach( $cap_produkte as $cap ) {
						$user->add_cap( $cap );
					}
				} else {
					foreach( $cap_produkte as $cap ) {
						$user->remove_cap( $cap );
					}
				}
				
			} /* End foreach $user */
		
		}
		else {}
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	
	/* Ende Aktion */
	
}
add_action('admin_action_dni', 'dni_admin_action');

?>