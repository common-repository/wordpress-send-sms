<?php
/**
* Plugin permissions management
* 
* Define method to manage permission for the software
* @author Eoxia <dev@eoxia.com>
* @version 1.1.3
* @package Wordpress-send-sms
* @subpackage librairies
*/

/**
* Define method to manage permission for the software
* @package Wordpress-send-sms
* @subpackage librairies
*/
class wpsendsms_permission
{
	
	/**
	*	Define the database table to use in the entire script
	*/
	const dbTable = DIGI_DBT_PERMISSION_ROLE;

	/**
	*	Define the permission list available for users into plugin
	*/
	function permission_list(){
		$permission['sendsms_import_user_menu'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'user', 'permission_sub_module' => 'menu');
		$permission['sendsms_options'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'options', 'permission_sub_module' => 'menu');
		$permission['sendsms_new_message'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'message', 'permission_sub_module' => 'menu');
		$permission['sendsms_list'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'user_list', 'permission_sub_module' => 'menu');
		$permission['sendsms_histo'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'histo', 'permission_sub_module' => 'menu');
		$permission['sendsms_documentation'] = array('set_by_default' => 'no', 'permission_type' => 'read', 'permission_sub_type' => '', 'permission_module' => 'documentation', 'permission_sub_module' => 'menu');

		$permission['sendsms_create_list'] = array('set_by_default' => 'no', 'permission_type' => 'write', 'permission_sub_type' => 'add', 'permission_module' => 'user_list', 'permission_sub_module' => '');
		$permission['sendsms_edit_list'] = array('set_by_default' => 'no', 'permission_type' => 'write', 'permission_sub_type' => 'edit', 'permission_module' => 'user_list', 'permission_sub_module' => '');
		$permission['sendsms_delete_list'] = array('set_by_default' => 'no', 'permission_type' => 'delete', 'permission_sub_type' => '', 'permission_module' => 'user_list', 'permission_sub_module' => '');

		$permission['sendsms_send_message'] = array('set_by_default' => 'no', 'permission_type' => 'write', 'permission_sub_type' => 'add', 'permission_module' => 'message', 'permission_sub_module' => '');

		$permission['sendsms_save_options'] = array('set_by_default' => 'no', 'permission_type' => 'write', 'permission_sub_type' => 'edit', 'permission_module' => 'options', 'permission_sub_module' => '');

		$permission['sendsms_import_user'] = array('set_by_default' => 'no', 'permission_type' => 'write', 'permission_sub_type' => 'edit', 'permission_module' => 'user', 'permission_sub_module' => '');

		return $permission;
	}

	/**
	*	Initialise permission for the administrator when installing the plugin
	*/
	function init_permission(){
		/*	Récupération du rôle administrateur	*/
		$role = get_role('administrator');

		/*	Récupération des "nouveaux" droits	*/
		$droits = self::permission_list();
		foreach($droits as $droit => $droit_definition)
		{/*	Lecture des "nouveaux" droits pour affectation à l'administrateur	*/
			if(($role != null) && !$role->has_cap($droit))
			{
				$role->add_cap($droit);
			}
		}

		/*	Vidage de l'objet rôle	*/
		unset($role);
	}

	/**
	*	Call the different element in order to edit rights per user
	*
	*	@return string The html output of the permission list for a specific user
	*/
	function user_permission_management(){
		global $wpsendsms_wp_role;

		/*	Récupération des informations concernant l'utilisateur en cours d'édition	*/
		$user = new WP_User($_REQUEST['user_id']);

		ob_start();
		self::permission_management($user);
		$wpsendsmsPermissionForm = ob_get_contents();
		ob_end_clean();
		echo '<h3>' . __('Droits d\'acc&egrave;s de l\'utilisateur pour l\'utilitaire d\'envoie de sms', 'wpsendsmsrisk') . '</h3>' . $wpsendsmsPermissionForm;
	}


	/**
	*	Update user right's. Check if there is a user id send by post method, if it is the case so we launch user rights' update process
	*
	*/
	function user_permission_set(){
		/*	Vérification qu'il existe bien un utilisateur à mettre à jour avant d'effectuer une action	*/
		if ( ! $_POST['user_id'] ) return;
		/*	Récupération des informations concernant l'utilisateur en cours d'édition	*/
		$user = new WP_User($_POST['user_id']);

		/*	Récupération des permissions envoyées	*/
		$userCapsList = $_POST['wpsendsms_permission'];

		/*	Récupération des permissions existantes	*/
		$existingPermission = self::permission_list();
		foreach($existingPermission as $permission => $permission_definition){
			/*	Vérification de la permission actuelle au cas ou elle serait nulle	*/
			if($permission != ''){
				/*	Si l'utilisateur possède une permission mais que celle ci n'est plus cochée => Suppression de la permission	*/
				if( $user->has_cap($permission) && ((!array_key_exists($permission, $userCapsList)) || (isset($userCapsList[$permission]) && ($userCapsList[$permission] != 'yes'))) )
				{
					$user->remove_cap($permission);
				}
				/*	Si l'utilisateur ne possède pas la permission mais que celle ci est cochée  => Ajout de la permission	*/
				elseif( !$user->has_cap($permission) && ($userCapsList[$permission] == 'yes'))
				{
					$user->add_cap($permission);
				}
			}
		}
	}


	/**
	*	Output the html table with the permission list stored by module and sub-module
	*/
	function permission_management($elementToManage){
		global $wpsendsms_wp_role;
		if(!is_object($wpsendsms_wp_role)){
			/*	Instanciation de l'objet role de worpdress	*/
			$wpsendsms_wp_role = new WP_Roles();
		}
		$permissionList = array();
		$permissionCap = array();

		/*	Récupération des permissions créées pour rangement par module	*/
		$existingPermission = self::permission_list();
		foreach($existingPermission as $permission => $permission_definition)
		{
			$permissionList[$permission_definition['permission_module']][$permission_definition['permission_sub_module']][] = $permission;
			$permissionCap[$permission]['type'] = $permission_definition['permission_type'];
			$permissionCap[$permission]['subtype'] = $permission_definition['permission_sub_type'];
		}

?>
<table class="form-table" >
<?php
		if(($_REQUEST['user_id'] != '') && ($_REQUEST['user_id'] > 0)){
?>
	<tr>
		<td><?php _e('L&eacute;gende', 'sendsms'); ?></td>
		<td>
			<span class="permissionGrantedFromParent" ><input type="checkbox" name="explanationBoxDisabled" id="explanationBoxDisabled" value="" checked="checked" disabled="disabled" />&nbsp;<?php _e('Le droit provient du r&ocirc;le de l\'utilisateur et ne peut &ecirc;tre supprim&eacute; depuis cette interface', 'sendsms'); ?></span><br/>
			<span class="permissionGranted" ><input type="checkbox" name="explanationBoxEnabled" id="explanationBoxEnabled" value="" checked="checked" />&nbsp;<?php _e('Permission ajout&eacute;e en plus de celle du r&ocirc;le de l\'utilisateur', 'sendsms'); ?></span>
		</td>
	</tr>
<?php
		}
?>
	<tr>
		<td><?php _e('Raccourci d\'attribution', 'sendsms'); ?></td>
		<td>
			<span class="checkall_right" id="add_checkall" ><?php _e('Tous les droits', 'sendsms'); ?></span>&nbsp;/&nbsp;<span class="uncheckall_right" id="remove_uncheckall" ><?php _e('Aucun droit', 'sendsms'); ?></span><br/>
			<span class="checkall_link" id="add_menu" ><?php _e('Tous les menus', 'sendsms'); ?></span>&nbsp;/&nbsp;<span class="uncheckall_link" id="remove_menu" ><?php _e('Aucun menu', 'sendsms'); ?></span><br/>
			<span class="checkall_link" id="add_read" ><?php _e('Tous les droits en lecture', 'sendsms'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_read" ><?php _e('Aucun droit en lecture', 'sendsms'); ?></span><br/>
			<span class="checkall_link" id="add_write" ><?php _e('Tous les droits en &eacute;criture', 'sendsms'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_write" ><?php _e('Aucun droit en &eacute;criture', 'sendsms'); ?></span><br/>
			<span class="checkall_link" id="add_delete" ><?php _e('Tous les droits en suppression', 'sendsms'); ?></span>&nbsp;/&nbsp;<span  class="uncheckall_link" id="remove_delete" ><?php _e('Aucun droit en suppression', 'sendsms'); ?></span><br/>
		</td>
	</tr>
	<tr>
		<td colspan="2" >&nbsp;</td>
	</tr>
<?php
		foreach($permissionList as $module => $subModule)
		{
?>
	<tr>
		<th>
			<?php _e('permission_' . $module, 'sendsms'); ?>
			<div class="wpsendsms_permission_check_all" ><span id="check_selector_<?php echo $module; ?>" class="checkall" ><?php _e('Tout cocher', 'sendsms'); ?></span>&nbsp;/&nbsp;<span id="uncheck_selector_<?php echo $module; ?>" class="uncheckall" ><?php _e('Tout d&eacute;cocher', 'sendsms'); ?></span></div>
		</th>
		<td>
<?php
			foreach($subModule as $subModuleName => $moduleContent)
			{
?>
			<div class="sub_module <?php echo ($subModuleName != '') ? 'permission_module_' . $subModuleName : ''; ?>" >
				<div class="sub_module_name" >
<?php
				if($subModuleName)
				{
					_e('permission_' . $module . '_' . $subModuleName, 'sendsms');
				}
				else
				{
					_e('permission_' . $module, 'sendsms');
				}
?>
				</div>
				<div class="sub_module_content" >
					<div class="wpsendsms_permission_check_all" ><span id="check_selector_<?php echo $module . '_' . $subModuleName; ?>" class="checkall" ><?php _e('Tout cocher', 'sendsms'); ?></span>&nbsp;/&nbsp;<span id="uncheck_selector_<?php echo $module . '_' . $subModuleName; ?>" class="uncheckall" ><?php _e('Tout d&eacute;cocher', 'sendsms'); ?></span></div>
<?php
				/*	Liste des permissions pour le module et le sous-module	*/
				foreach($moduleContent as $permission)
				{
					$checked = $permissionNameClass = '';
					$roleToCopy = isset($_REQUEST['roleToCopy']) ? sendsms_tools::IsValid_Variable($_REQUEST['roleToCopy']) : '';
					$action = isset($_REQUEST['save']) ? sendsms_tools::IsValid_Variable($_REQUEST['save']) : '';
					if(($roleToCopy != '') && ($action == 'ok')){
						$roleDetails = $wpsendsms_wp_role->get_role($roleToCopy);
						if($roleDetails->has_cap($permission))
						{
							$checked = 'checked="checked"';
						}
					}
					elseif(($elementToManage != null) && $elementToManage->has_cap($permission)){
						$checked = 'checked="checked"';
						$permissionNameClass = 'permissionGranted';
						if(isset($elementToManage->roles) && (count($elementToManage->caps) >= count($elementToManage->roles)) && apply_filters('additional_capabilities_display', true, $elementToManage)){
							$roleDetails = $wpsendsms_wp_role->get_role(implode('', $elementToManage->roles));
							if($roleDetails->has_cap($permission)){
								$permissionNameClass = 'permissionGrantedFromParent';
								$checked .= ' disabled="disabled" ';
							}
						}
					}
					echo '<input type="checkbox" class="' . $module . ' ' . $subModuleName . ' ' . $module . '_' . $subModuleName . ' ' . $permissionCap[$permission]['type'] . ' ' . $permissionCap[$permission]['subtype'] . ' ' . $permissionCap[$permission]['type'] . '_' . $permissionCap[$permission]['subtype'] . '" name="wpsendsms_permission[' . $permission . ']" id="wpsendsms_permission_' . $permission . '" value="yes" ' . $checked . ' />&nbsp;<label for="wpsendsms_permission_' . $permission . '" class="' . $permissionNameClass . '" >' . __($permission, 'sendsms') . '</label><br/>';
				}
?>
				</div>
			</div>
<?php
			}
?>
		</td>
	</tr>
<?php
		}
?>
</table>
<script type="text/javascript" >
	sendsms(document).ready(function(){
		/**
		*	Define action when clicking on checkall/uncheckall for a module or a sub module
		*/
		jQuery('.checkall').click(function(){
			var module = jQuery(this).attr("id").replace("check_selector_", "");
			jQuery("." + module).each(function(){
				if(!jQuery(this).prop("disabled")){
					jQuery(this).prop("checked", true);
				}
			});
		});
		jQuery('.uncheckall').click(function(){
			var module = jQuery(this).attr("id").replace("uncheck_selector_", "");
			jQuery("." + module).each(function(){
				if(!jQuery(this).prop("disabled")){
					jQuery(this).prop("checked", false);
				}
			});
		});

		/**
		*	Define action chen clicking on checkall/uncheckall into the link
		*/
		jQuery('.checkall_link').click(function(){
			var module = jQuery(this).attr("id").replace("add_", "");
			jQuery("." + module).each(function(){
				if(!jQuery(this).prop("disabled")){
					jQuery(this).prop("checked", true);
				}
			});
		});
		jQuery('.uncheckall_link').click(function(){
			var module = jQuery(this).attr("id").replace("remove_", "");
			jQuery("." + module).each(function(){
				if(!jQuery(this).prop("disabled")){
					jQuery(this).prop("checked", false);
				}
			});
		});

		/**
		*	Define action chen clicking on checkall/uncheckall into the link
		*/
		jQuery('.checkall_right').click(function(){
			var module = jQuery(this).attr("id").replace("add_", "");
			jQuery("." + module).each(function(){
				jQuery(this).click();
			});
		});
		jQuery('.uncheckall_right').click(function(){
			var module = jQuery(this).attr("id").replace("remove_", "");
			jQuery("." + module).each(function(){
				jQuery(this).click();
			});
		});
	});
</script>
<?php
	}

}