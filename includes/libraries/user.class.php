<?php

class sendsms_user {

	function ajout_tel(){
		$contact['tel'] = 'T&eacute;l&eacute;phone';
		return $contact;
	}
	function ajoutField (){
		echo '<p>
				<label>'.__('Nom', 'sendsms').'<br />
				<input type="text" name="user_lastName" id="user_lastName" class="input" value="'.$_POST['user_lastName'].'" size="25" tabindex="20" /></label>
			 </p>
			 <p>
				<label>'.__('Pr&eacute;nom', 'sendsms').'<br />
				<input type="text" name="user_firstName" id="user_firstName" class="input" value="'.$_POST['user_firstName'].'" size="25" tabindex="20" /></label>
			 </p>
			 <p>
				<label>'.__('T&eacute;l&eacute;phone', 'sendsms').'<br />
				<input type="text" name="user_tel" id="user_tel" class="input" value="'.$_POST['user_tel'].'" size="25" tabindex="20" /></label>
			 </p>';	
	}
	function registerField (){	
		//recuperation ID + insertion des meta User
		$queryIdUsers = 'SELECT `ID` FROM `'.$wpdb->prefix.'users` WHERE `user_login` = "' . $_POST['user_firstName'] . '"';
		$resultID = $wpdb->get_row($queryIdUsers);

		$id_user = $resultID->ID;

		$prenom = $_POST['user_firstName'];
		$nom = $_POST['user_lastName'];
		$tel = $_POST['user_tel'];

		update_usermeta( $id_user, 'first_name', $prenom );
		update_usermeta( $id_user, 'last_name', $nom );
		update_usermeta( $id_user, 'tel', $tel );
		update_usermeta( $id_user, 'last_update', NOW() );
	}

	function adminUser(){
	
	if ($_POST != NULL &&
		isset($_POST['btn_import']) ){
		sendsms_userAction::importUser(); 
	}
	
	if ($_POST != NULL &&
		isset($_POST['btn_message'])){
		sendsms_message::message(); 
	}

	switch ($select){
		case 'user' : //affiche les utilisateurs
			sendsms_userAction::displayUser();							
		break;
		

		case 'addUser' : //ajouter utilisateur
			sendsms_userAction::addUser();	
		break;
	
		case 'editUser' : // Edit les utilisateurs
			header('Location: user-edit.php?user_id='.$_POST['editUser'].'');
		break;
	
		case 'supprUser' : //supression des utilisateurs
			sendsms_userAction::supprUser();	
		break;

	}

	}

	/*
	*	Get the wordpress' user list
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserList(){
		global $wpdb;

		$query = 
			"SELECT USERS.ID
			FROM wp_users AS USERS";
		$userList = $wpdb->get_results($query);

		return $userList;
	}

	/*
	*	Get the wordpress' user list
	*	@return array $userlist An object containing the different subscriber
	*/
	function getCompleteUserList(){
		$listeComplete = array();

		$listeUtilisateurs = sendsms_user::getUserList();
		foreach($listeUtilisateurs as $utilisateurs)
		{
			if($utilisateurs->ID != 1)
			{
				$user_info = get_userdata($utilisateurs->ID);

				unset($valeurs);
				$valeurs['user_id'] = $user_info->ID;
				$valeurs['user_registered'] = $user_info->user_registered;
				if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
				{
					$valeurs['user_lastname'] = $user_info->user_lastname;
				}
				else
				{
					$valeurs['user_lastname'] = '';
				}
				if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
				{
					$valeurs['user_firstname'] = $user_info->user_firstname;
				}
				else
				{
					$valeurs['user_firstname'] = $user_info->user_nicename;
				}

				$listeComplete[$user_info->ID] = $valeurs;
			}
		}

		return $listeComplete;
	}

	/*
	*	Get the wordpress' user list
	*	@return array $userlist An object containing the different subscriber
	*/
	function getUserInformation($userId)
	{
		$listeComplete = array();

		$user_info = get_userdata($userId);

		unset($valeurs);
		$valeurs['user_id'] = $user_info->ID;
		if( (isset($user_info->user_lastname) && ($user_info->user_lastname != '')) )
		{
			$valeurs['user_lastname'] = $user_info->user_lastname;
		}
		else
		{
			$valeurs['user_lastname'] = '';
		}
		if( (isset($user_info->user_firstname) && ($user_info->user_firstname != '')) )
		{
			$valeurs['user_firstname'] = $user_info->user_firstname;
		}
		else
		{
			$valeurs['user_firstname'] = $user_info->user_nicename;
		}

		$listeComplete[$user_info->ID] = $valeurs;

		return $listeComplete;
	}

	/*
	*	Output a table with the different users binded to an element
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateur($listAffectedUser, $remplissage=true){
		
    //echo '<div>';
		
		$utilisateursMetaBox = '';
		$alreadyLinkedUser = '';
		$affectedUserIdList = '';
    
    $utilisateursMetaBox = '
      <table class="wp-list-table widefat fixed users tableauModif" style="float:left;">
				<thead>
					<tr><th colspan="2" class="big_title">'.__('S&eacute;lectionner des utilisateurs', 'sendsms').'</th></tr>
				</thead>';

		//on r&eacute;cupère les utilisateurs d&eacute;jà affect&eacute;s à l'&eacute;l&eacute;ment en cours.
		$utilisateursLies = explode(', ', $listAffectedUser);
		if($remplissage && is_array($utilisateursLies ) && (count($utilisateursLies) > 0))
		{
			foreach($utilisateursLies as $utilisateurId)
			{
			if($utilisateurId > 0){
					$currentUser = sendsms_user::getUserInformation($utilisateurId);
					$alreadyLinkedUser .= '<div class="selecteduserOP" id="affectedUser' . $utilisateurId . '" title="' . __('Cliquez pour supprimer', 'sendsms') . '" >' . $currentUser[$utilisateurId]['user_lastname'] . ' ' . $currentUser[$utilisateurId]['user_firstname'] . '<div class="ui-icon deleteUserFromList" >&nbsp;</div></div>';
				}
			}
		}
		else
		{
			$alreadyLinkedUser = '<span class="noUserSelected">' . __('Aucun utilisateur affect&eacute;', 'sendsms') . '</span>';
		}
		if($remplissage && $listAffectedUser != ""){
      $affectedUserIdList.= $listAffectedUser . ', ';
		}
    
    $utilisateursMetaBox .= '<tr><td colspan="2">
      <input type="hidden" name="actuallyAffectedUserIdList" id="actuallyAffectedUserIdList" value="' . $affectedUserIdList . '" />
      <input type="hidden" name="affectedUserIdList" id="affectedUserIdList" value="' . $affectedUserIdList . '" />';


	$utilisateursMetaBox .= '
	<div class="addLinkUserElement">
		<div class="clear">
			<span class="searchUserInput ui-icon" >&nbsp;</span>
			<input class="searchUserToAffect users" type="text" name="affectedUser" id="affectedUser" value="' . __('Rechercher dans la liste des utilisateurs', 'sendsms') . '" />
		</div>
	</div>
    </td></tr>
    
		<tr>
      <td colspan="2">
      
	<div style="float:right;width:70%;">
        <div id="completeUserList" class="completeUserList">' . self::afficheListeUtilisateurTable($listAffectedUser, $remplissage) . '</div>
		
		<div id="massAction">
			<span class="checkAll" >' . __('cochez tout', 'sendsms') . '</span>&nbsp;/&nbsp;
			<span class="uncheckAll" >' . __('d&eacute;cochez tout', 'sendsms') . '</span>
		</div>
	</div>

<div id="userListOutput" class="userListOutput ui-widget-content">' . $alreadyLinkedUser . '</div>

<div id="userBlocContainer" class="clear hide" ><div class="selecteduserOP" title="' . __('Cliquez pour supprimer', 'sendsms') . '" >#USERNAME#<span class="ui-icon deleteUserFromList" >&nbsp;</span></div></div>

</td></tr><tr><td style="padding:7px 10px;">';

if(current_user_can('sendsms_import_user')){
$utilisateursMetaBox .= '<span class="lienAjout">+ <a href="' . get_option('siteurl') . '/wp-admin/admin.php?page=sendsms_ajout">' . __('Nouvel utilisateur', 'sendsms') . '</a></span>';
}

		$utilisateursMetaBox .= '<script type="text/javascript" >
			sendsms(document).ready(function(){
			
				/*	Mass action : check / uncheck all	*/
				jQuery("#massAction .checkAll").unbind("click");
				jQuery("#massAction .checkAll").click(function(){
					jQuery("#completeUserList .buttonActionUserLinkList").each(function(){
						if(jQuery(this).hasClass("userIsNotLinked")){
							jQuery(this).click();
						}
					});
				});
				jQuery("#massAction .uncheckAll").unbind("click");
				jQuery("#massAction .uncheckAll").click(function(){
					jQuery("#completeUserList .buttonActionUserLinkList").each(function(){
						if(jQuery(this).hasClass("userIsLinked")){
							jQuery(this).click();
						}
					});
				});

				/*	Action when click on delete button	*/
				jQuery(".selecteduserOP").live("click", function(){
					userDivId = jQuery(this).attr("id").replace("affectedUser", "");
					deleteUserIdFiedList(userDivId, "");
					if(isUserIdFieldListEmpty()) {
						jQuery("#userListOutput").html(\'<span class="noUserSelected">' . __('Aucun utilisateur affect&eacute;', 'sendsms') . '</span>\');
						if(isListsEmpty()) {
							jQuery("#submitMessage").attr("disabled", true);
						}
					}
				});

				/*	User Search autocompletion	*/
				jQuery("#affectedUser").click(function(){
					jQuery(this).val("");
				});
				jQuery("#affectedUser").blur(function(){
					jQuery(this).val("' . __('Rechercher dans la liste des utilisateurs', 'sendsms') . '");
				});
				jQuery("#affectedUser").autocomplete("' . WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/includes/liveSearch/searchUsers.php");
				jQuery("#affectedUser").result(function(event, data, formatted){
					cleanUserIdFiedList(data[1]);
					addUserIdFieldList(data[0], data[1], "");
					jQuery("#submitMessage").attr("disabled", false);

					jQuery("#affectedUser").val("' . __('Rechercher dans la liste des utilisateurs', 'sendsms') . '");
				});
			});
		</script>
    </td>
    </tr>
    </table>';

		return $utilisateursMetaBox;
	}

	/*
	*	Output a table with the different users binded to an element

	*	@param mixed $tableElement The element type we want to get the user list for
	*	@param integer $idElement The element identifier we want to get the user list for
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function afficheListeUtilisateurTable($listAffectedUser, $cochage=true)
	{
		
		$tableElement = $idElement = '';
		$utilisateursMetaBox = '';
		$idBoutonEnregistrer = 'save_group';

		$idTable = 'listeIndividus';
		$titres = array( '', ucfirst(strtolower(__('Nom', 'sendsms'))), ucfirst(strtolower(__('Pr&eacute;nom', 'sendsms'))), ucfirst(strtolower(__('T&eacute;l&eacute;phone', 'sendsms'))), ucfirst(strtolower(__('Inscription', 'sendsms'))));
		unset($lignesDeValeurs);

		//on r&eacute;cupère les utilisateurs d&eacute;jà affect&eacute;s à l'&eacute;l&eacute;ment en cours.
		$listeUtilisateursLies = array();
		$utilisateursLies = explode(', ', $listAffectedUser);
		if($cochage && is_array($utilisateursLies ) && (count($utilisateursLies) > 0))
		{
			foreach($utilisateursLies as $utilisateurId)
			{
				if($utilisateurId > 0){
					$listeUtilisateursLies[$utilisateurId] = $utilisateurId;
				}
			}
		}

		$listeUtilisateurs = sendsms_user::getCompleteUserList();
		if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0))
		{
			foreach($listeUtilisateurs as $utilisateur)
			{
			
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'listeUtilisateurs' . $utilisateur['user_id'];
				$idCbLigne = 'cb_' . $idLigne;
				$moreLineClass = 'userIsNotLinked';
				if(isset($listeUtilisateursLies[$utilisateur['user_id']]))
				{
					$moreLineClass = 'userIsLinked';
				}
				$tel = get_usermeta( $utilisateur['user_id'], 'tel' );
				
				if($tel != ""){
					$valeurs[] = array('value'=>'<span id="actionButtonUserLink' . $utilisateur['user_id'] . '" class="buttonActionUserLinkList ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
					$valeurs[] = array('value'=>$utilisateur['user_lastname']);
					$valeurs[] = array('value'=>$utilisateur['user_firstname']);
					$valeurs[] = array('value'=>$tel);
					$valeurs[] = array('value'=>mysql2date('d M Y', $utilisateur['user_registered'], true));
					$lignesDeValeurs[] = $valeurs;
					$idLignes[] = $idLigne;
				}
			}
			
			$script = 
			'<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#' . $idTable . '").dataTable({
					"bAutoWidth": false,
					"bInfo": false,
					"bPaginate": false,
					"bFilter": false,
					"aaSorting": [[3,"desc"]]
				});
				jQuery("#' . $idTable . '").children("tfoot").remove();
				jQuery("#' . $idTable . '").removeClass("dataTables_wrapper");
				jQuery(".buttonActionUserLinkList").click(function(){
					if(jQuery(this).hasClass("addUserToLinkList")){
						var currentId = jQuery(this).attr("id").replace("actionButtonUserLink", "");
						cleanUserIdFiedList(currentId);
						
						var lastname = jQuery(this).parent("td").next().html();
						var firstname = jQuery(this).parent("td").next().next().html();

						addUserIdFieldList(lastname + " " + firstname, currentId, "");
					}
					else if(jQuery(this).hasClass("deleteUserToLinkList")){
						deleteUserIdFiedList(jQuery(this).attr("id").replace("actionButtonUserLink", ""), "");
					}
					checkUserListModification("' . $idBoutonEnregistrer . '");
				});
				jQuery("#completeUserList .odd, #completeUserList .even").click(function(){
					if(jQuery(this).children("td:first").children("span").hasClass("userIsNotLinked")){
						var currentId = jQuery(this).attr("id").replace("' . $tableElement . $idElement . 'listeUtilisateurs", "");
						cleanUserIdFiedList(currentId);

						var lastname = jQuery(this).children("td:nth-child(2)").html();
						var firstname = jQuery(this).children("td:nth-child(3)").html();
						var tel = jQuery(this).children("td:nth-child(4)").html();

						addUserIdFieldList(lastname + " " + firstname + " " +tel, currentId, "");
            jQuery("#submitMessage").attr("disabled", false);
					}
					else{
						deleteUserIdFiedList(jQuery(this).attr("id").replace("' . $tableElement . $idElement . 'listeUtilisateurs", ""), "");
						if(isUserIdFieldListEmpty()) {
							jQuery("#userListOutput").html(\'<span class="noUserSelected">' . __('Aucun utilisateur affect&eacute;', 'sendsms') . '</span>\');
							if(isListsEmpty()) {
								jQuery("#submitMessage").attr("disabled", true);
							}
						}
					}
					checkUserListModification("' . $idBoutonEnregistrer . '");
				});
			});
		</script>';
		}
		else
		{
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'sendsms'));
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>'');
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('addUserButtonDTable','','','');
		

		$utilisateursMetaBox .= sendsms_display::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $utilisateursMetaBox;
	}

	function user_additionnal_field($user, $output_type = 'normal'){
?>
      <tr>
				<td class="wpsendsms_import_user_main_info_name" id="TelContainer"><?php echo ucfirst(strtolower(__('Telephone', 'sendsms'))); ?></td>
				<td class="wpsendsms_import_user_main_info_input" ><input type="text" value="<?php echo $_POST['telUtilisateur'] ?>" id="telUtilisateur" name="telUtilisateur" /><br/><span style="font-size:9px;" ><?php echo __('Au format international (exemple +336XXXXXXXX)', 'sendsms'); ?></span></td>
			</tr>
<?php
	}
	
	/**
	*
	*/
	function importUser(){
		global $wpdb;
		$separatorExample = '<span class="fieldSeparator" >[fieldSeparator]</span>';

		$importAction = isset($_POST['act']) ? sendsms_tools::IsValid_Variable($_POST['act']) : '';
		$userRoles = isset($_POST['userRoles']) ? sendsms_tools::IsValid_Variable($_POST['userRoles']) : '';
		$fieldSeparator = isset($_POST['fieldSeparator']) ? sendsms_tools::IsValid_Variable($_POST['fieldSeparator']) : '';
		$sendUserMail = isset($_POST['sendUserMail']) ? sendsms_tools::IsValid_Variable($_POST['sendUserMail']) : '';

		$optionEmailDomain = '';
		$checkEmailDomain = get_option('wpsendsms_email_domain', '');
		if(isset($_POST['domaineMail']) && ($checkEmailDomain != $_POST['domaineMail'])){
			update_option('wpsendsms_email_domain', $_POST['domaineMail']);
			$checkEmailDomain = get_option('wpsendsms_email_domain', '');
		}

		if($importAction != ''){
			$userToCreate = array();
			$importResult = '';

			/*	Check if there are lines to create without sending a file	*/
			$userLinesToCreate = isset($_POST['userLinesToCreate']) ? (string) sendsms_tools::IsValid_Variable($_POST['userLinesToCreate']) : '';
			if($userLinesToCreate != '')
			{
				$userToCreate = array_merge($userToCreate, explode("\n", trim($userLinesToCreate)));
			}
			else
			{
				$importResult .= __('Aucun utilisateurs n\'a &eacute;t&eacute; ajout&eacute; depuis le champs texte', 'sendsms') . '<br/>';
			}

			/*	Check if a file has been sending */
			if($_FILES['userFileToCreate']['error'] != UPLOAD_ERR_NO_FILE)
			{
				$file = $_FILES['userFileToCreate'];
				if($file['error'])
				{
					switch ($file['error']){
						case UPLOAD_ERR_INI_SIZE:
							$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'sendsms'), $file['size'], upload_max_filesize);
						break;
						case UPLOAD_ERR_FORM_SIZE:
							$subFileError .= sprintf(__('Le fichier que vous avez envoy&eacute; est trop lourd: %s taille autoris&eacute;e %s', 'sendsms'), $file['size'], upload_max_filesize);
						break;
						case UPLOAD_ERR_PARTIAL:
							$subFileError .= __('Le fichier que vous avez envoy&eacute; n\'a pas &eacute;t&eacute; compl&eacute;tement envoy&eacute;', 'sendsms');
						break;
					}
					$importResult .= '<h4 style="color:#FF0000;">' . __('Une erreur est survenue lors de l\'envoie du fichier', 'sendsms') . '</h4><p>' . $subFileError . '</p>';
				}
				elseif(!is_uploaded_file($file['tmp_name']))
				{
					$importResult .= sprintf(__('Le fichier %s n\'a pas pu &ecirc;tre envoy&eacute;', 'sendsms'), $file['name']);
				}
				else
				{
					$userToCreate = array_merge($userToCreate, file($file['tmp_name']));
				}
			}
			else
			{
				// $importResult .= __('Aucun fichier n\'a &eacute;t&eacute; envoy&eacute;', 'sendsms') . '<br/>';
			}

			if(is_array($userToCreate) && (count($userToCreate) > 0)){
				$createdUserNumber = 0;
				$errors = array();

				foreach($userToCreate as $userInfos) {
					$userInfosComponent = array();
					if(trim($userInfos) != ''){
						$userInfosComponent = explode($fieldSeparator, $userInfos);
						$userInfosComponent[0] = trim(strtolower(sendsms_tools::slugify_noaccent($userInfosComponent[0])));
						$userInfosComponent[1] = trim($userInfosComponent[1]);
						$userInfosComponent[2] = trim($userInfosComponent[2]);
						$userInfosComponent[3] = trim($userInfosComponent[3]);
						$userInfosComponent[4] = trim(strtolower(sendsms_tools::slugify_noaccent($userInfosComponent[4])));
						$userInfosComponent[5] = trim($userInfosComponent[5]);
						$userInfosComponent[6] = trim($userInfosComponent[6]);
						$checkErrors = 0;
						$invalid_email = $already_used_email = array();
						$invalid_identifier = $already_used_identifier = array();
						$invalid_phone_used = $already_used_phone = array();

						/*	Check if the email adress is valid or already exist	*/
						if(!is_email($userInfosComponent[4])){
							$invalid_email[] = $userInfos;
							$checkErrors++;
						}
						$checkIfMailExist = $wpdb->get_row("SELECT user_email FROM " . $wpdb->users . " WHERE user_email = '" . mysql_real_escape_string($userInfosComponent[4]) . "'");
						if($checkIfMailExist){
							$already_used_email[] = $userInfos;
							$checkErrors++;
						}

						/*	Check if the username is valid or already exist	*/
						if(!validate_username($userInfosComponent[0])){
							$invalid_identifier[] = $userInfos;
							$checkErrors++;
						}
						if(username_exists($userInfosComponent[0])){
							$already_used_identifier[] = $userInfos;
							$checkErrors++;
						}

						/*	Check if the phone number has already been used	*/
						if(!empty($userInfosComponent[5])){
							$query = $wpdb->prepare("SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key = %s AND meta_value = %s", 'tel', $userInfosComponent[5]);
							$checkIfPhoneExist = $wpdb->get_row($query);
							if($checkIfPhoneExist != null){
								$already_used_phone[] = $userInfos;
								$checkErrors++;
							}
						}
						else{
							$invalid_phone_used[] = $userInfos;
							$checkErrors++;
						}

						/*	There are no errors on the email and username so we can create the user	*/
						if($checkErrors == 0){
							/*	Check if the password is given in the list to create, if not we generate one */
							if($userInfosComponent[3] == ''){
								$userInfosComponent[3] = substr(md5(uniqid(microtime())), 0, 7);
							}

							/*	Start creating the user	*/
							$newUserID = 0;
							$newUserID = 
								wp_insert_user(
									array(
										"user_login" => $userInfosComponent[0],
										"first_name" => $userInfosComponent[1],
										"last_name" => $userInfosComponent[2],
										"user_pass" => $userInfosComponent[3],
										"user_email" => $userInfosComponent[4]
									)
								);

							if($newUserID <= 0){
								$errors[] = sprintf(__('L\'utilisateur de la ligne %s n\'a pas pu &ecirc;tre ajout&eacute;', 'sendsms'), $userInfos);
							}
							else{
								update_usermeta($newUserID, 'tel', $userInfosComponent[5]);

								if($sendUserMail != ''){
									wp_new_user_notification($newUserID, $userInfosComponent[3]);
								}
								$createdUserNumber++;

								/*	Affect a role to the new user regarding on the import file or lines and if empty the main roe field	*/
								if ($userInfosComponent[6] == ''){
									$userInfosComponent[6] = $userRoles;
								}
								$userRole = new WP_User($newUserID);
								$userRole->set_role($userInfosComponent[6]);
							}
						}
					}
				}

				if($createdUserNumber >= 1){
					$subResult = sprintf(__('%s utilisateur a &eacute;t&eacute; cr&eacute;&eacute;', 'sendsms'), $createdUserNumber);
					if($createdUserNumber > 1){
						$subResult = sprintf(__('%s utilisateurs ont &eacute;t&eacute; cr&eacute;&eacute;s', 'sendsms'), $createdUserNumber);
					}
					
					$importResult .= '<h4 style="color:#00CC00;">' . __('L\'import s\'est termin&eacute; avec succ&eacute;s. Veuillez trouver le r&eacute;sultat ci-dessous', 'sendsms') . '</h4><ul>' . $subResult . '</ul>';

					if($sendUserMail != '')
					{
						$importResult .= '<div style="font-weight:bold;" >' . __('Les nouveaux utilisateurs recevront leurs mot de passe par email', 'sendsms') . '</div>';
					}
				}
				if(!empty($checkErrors)){
					$subresult = '';
					if(!empty($invalid_email)){
						$subresult .= '<h5>' . (count($invalid_email)>1?__('Les adresses email des lignes suivantes sont invalides', 'sendsms'):__('L\'adresse email de la ligne suivante est invalide', 'sendsms')) . '</h5><ul>';
						foreach($invalid_email as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}
					if(!empty($already_used_email)){
						$subresult .= '<h5>' . (count($already_used_email)>1?__('Les adresses email des lignes suivantes sont d&eacute;j&agrave; utilis&eacute;es', 'sendsms'):__('L\'adresse email de la ligne suivante est d&eacute;j&agrave; utilis&eacute;e', 'sendsms')) . '</h5><ul>';
						foreach($already_used_email as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}

					if(!empty($invalid_identifier)){
						$subresult .= '<h5>' . (count($invalid_identifier)>1?__('Les identifiants des lignes suivantes sont invalides', 'sendsms'):__('L\'identifiant de la ligne suivante est invalide', 'sendsms')) . '</h5><ul>';
						foreach($invalid_identifier as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}
					if(!empty($already_used_identifier)){
						$subresult .= '<h5>' . (count($already_used_identifier)>1?__('Les identifiants des lignes suivantes sont d&eacute;j&agrave; utilis&eacute;s', 'sendsms'):__('L\'identifiant de la ligne suivante est d&eacute;j&agrave; utilis&eacute;', 'sendsms')) . '</h5><ul>';
						foreach($already_used_identifier as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}

					if(!empty($invalid_phone_used)){
						$subresult .= '<h5>' . (count($invalid_phone_used)>1?__('Les num&eacute;ros de t&eacute;l&eacute;phone des lignes suivantes sont invalides', 'sendsms'):__('Le num&eacute;ro de t&eacute;l&eacute;phone de la ligne suivante est invalide', 'sendsms')) . '</h5><ul>';
						foreach($invalid_phone_used as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}
					if(!empty($already_used_phone)){
						$subresult .= '<h5>' . (count($already_used_phone)>1?__('Les num&eacute;ros de t&eacute;l&eacute;phone des lignes suivantes sont d&eacute;j&agrave; utilis&eacute;s', 'sendsms'):__('Le num&eacute;ro de t&eacute;l&eacute;phone de la ligne suivante est d&eacute;j&agrave; utilis&eacute;', 'sendsms')) . '</h5><ul>';
						foreach($already_used_phone as $line){
							$subresult .= '<li>' . $line . '</li>';
						}
						$subresult .= '</ul>';
					}

					$importResult .= '<h4 style="color:#FF0000;">' . __('Des erreurs sont survenues. Veuillez trouver la liste ci-dessous', 'sendsms') . '</h4>' . $subresult;
				}
			}
?>
		<div style="width:80%;margin:18px auto;padding:6px;border:1px dashed;"  ><?php echo $importResult; ?></div>
<?php
		}
?>		
<div id="icon-users" class="icon32"><br /></div>
<h2><?php _e('Import d\'utilisateurs pour send sms', 'sendsms'); ?></h2>
<br/>
<div id="ajax-response" style="display:none;" >&nbsp;</div>
<script type="text/javascript" >
	function changeSeparator(){
		sendsms('.fieldSeparator').html(sendsms('#fieldSeparator').val());
	}
	function trim_mail_identifier($identifier){
		$identifier = $identifier.replace(/\s/g, "-");

		return $identifier;
	}
	function mail_identifier(){
		if((jQuery('#prenomUtilisateur').val() != "") && (jQuery('#nomUtilisateur').val() != "")){
			jQuery('#emailUtilisateur').val(trim_mail_identifier(jQuery('#prenomUtilisateur').val()) + '.' + trim_mail_identifier(jQuery('#nomUtilisateur').val()) + '@' + jQuery('#domaineMail').val());
			if(jQuery('#domaineMail').val() == ""){
				jQuery('#email_domain_error').show();
			}
			else{
				jQuery('#email_domain_error').hide();
			}
		}
	}
	sendsms(document).ready(function(){
		changeSeparator();
		jQuery('#fieldSeparator').blur(function(){changeSeparator()});
		jQuery('#userLinesToCreate').blur(function(){
			if(jQuery(this).val() != ''){
				jQuery("#importSubmit_rapid").attr("disabled", false);
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", true);
			}
		});
		jQuery('#userLinesToCreate').keypress(function(){
			if(jQuery(this).val() != ''){
				jQuery("#importSubmit_rapid").attr("disabled", false);
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", true);
			}
		});

		jQuery('#ajouterUtilisateurListe').click(function(){
			var error = 0;
			jQuery('#mailDomainContainer').css('color', '#000000');
			jQuery('#firstNameContainer').css('color', '#000000');
			jQuery('#lastNameContainer').css('color', '#000000');
			jQuery('#TelContainer').css('color', '#000000');
			jQuery('#emailContainer').css('color', '#000000');
			jQuery('#fastAddErrorMessage').hide();
			jQuery('#email_domain_error').hide();

			jQuery('#domaineMail').val(jQuery('#domaineMail').val().replace("@", ""));

			if(jQuery('#domaineMail').val() == ""){
				jQuery('#mailDomainContainer').css('color', '#FF0000');
				error++;
			}
			if(jQuery('#prenomUtilisateur').val() == ""){
				jQuery('#firstNameContainer').css('color', '#FF0000');
				error++;
			}
			if(jQuery('#nomUtilisateur').val() == ""){
				jQuery('#lastNameContainer').css('color', '#FF0000');
				error++;
			}
			if(jQuery('#telUtilisateur').val() == ""){
				jQuery('#TelContainer').css('color', '#FF0000');
				error++;
			}
			if(jQuery('#emailUtilisateur').val() == ""){
				jQuery('#emailContainer').css('color', '#FF0000');
				error++;
			}

			if(error > 0){
				jQuery('#fastAddErrorMessage').show();
			}
			else{
				jQuery("#importSubmit_rapid").attr("disabled", false);
				identifiant = trim_mail_identifier(jQuery('#prenomUtilisateur').val()) + '.' + trim_mail_identifier(jQuery('#nomUtilisateur').val());
				prenom = jQuery('#prenomUtilisateur').val();
				nom = jQuery('#nomUtilisateur').val();
				tel = jQuery('#telUtilisateur').val();
				motDePasse = jQuery('#motDePasse').val();
				emailUtilisateur = jQuery('#emailUtilisateur').val();
				roleUtilisateur = jQuery('#userRoles').val();

				newline = identifiant + jQuery('#fieldSeparator').val() + prenom + jQuery('#fieldSeparator').val() + nom + jQuery('#fieldSeparator').val() + motDePasse + jQuery('#fieldSeparator').val() + emailUtilisateur + jQuery('#fieldSeparator').val() + tel + jQuery('#fieldSeparator').val() + roleUtilisateur;

				if(jQuery('#userLinesToCreate').val() != ''){
					newline = '\r\n' + newline;
				}
				jQuery('#userLinesToCreate').val(jQuery('#userLinesToCreate').val() + newline);
				jQuery('#prenomUtilisateur').val("");
				jQuery('#nomUtilisateur').val("");
				jQuery('#telUtilisateur').val("");
				jQuery('#emailUtilisateur').val("");

<?php echo $optionEmailDomain;	?>
			}
		});

		jQuery('#nomUtilisateur').blur(function(){
			mail_identifier();
		});
		jQuery('#prenomUtilisateur').blur(function(){
			mail_identifier();
		});

		jQuery("#import_user_form_file_container_switcher").click(function(){
			jQuery("#import_user_form_file_container").toggle();
			jQuery("#user_import_container_switcher_icon").toggleClass("user_import_container_opener");
			jQuery("#user_import_container_switcher_icon").toggleClass("user_import_container_closer");
		});

		jQuery("#complementary_fieds_switcher").click(function(){
			goTo("#wpsendsms_import_user_easy_form_container");
			jQuery("#complementary_fieds").toggle();
			jQuery("#complementary_fieds_icon").toggleClass("user_import_container_opener");
			jQuery("#complementary_fieds_icon").toggleClass("user_import_container_closer");
		});
	});
</script>
<form enctype="multipart/form-data" method="post" action="" >
	<input type="hidden" name="act" id="act" value="1" />

	<!-- 	Start of fast add part	-->
	<h3 class="clear" ><?php echo __('Ajout rapide d\'utilisateurs', 'sendsms'); ?></h3>
	<table class="wpsendsms_import_user_easy_form_container" id="wpsendsms_import_user_easy_form_container" >
		<tr>
			<td class="bold" ><?php _e('Informations obligatoires', 'sendsms'); ?></td>
			<td id="complementary_fieds_switcher" class="pointer" >&nbsp;</td>
		</tr>
		<tr>
			<td class="wpsendsms_mandatory_fields_container" >
				<table class="wpsendsms_import_user_easy_form" >
					<tr>
						<td class="wpsendsms_import_user_main_info_name" id="mailDomainContainer"><?php echo ucfirst(strtolower(__('domaine de l\'adresse email', 'sendsms'))); ?></td>
						<td class="wpsendsms_import_user_main_info_input" ><div class="alignleft" ><?php _e('adresse.email', 'sendsms'); ?>@</div><input type="text" value="<?php echo $checkEmailDomain; ?>" id="domaineMail" name="domaineMail" /></td>
					</tr>
					<tr>
						<td class="wpsendsms_import_user_main_info_name" ><?php echo ucfirst(strtolower(__('mot de passe par d&eacute;faut', 'sendsms'))); ?></td>
						<td class="wpsendsms_import_user_main_info_input"><input type="text" value="" id="motDePasse" name="motDePasse" /><br/>
						<span style="font-size:9px;" ><?php echo __('Laissez vide pour un mot de passe al&eacute;atoire', 'sendsms'); ?></span></td>
					</tr>
					<tr>
						<td class="wpsendsms_import_user_main_info_name" >
							<?php echo __('Envoyer le mot de passe aux utilisateurs.', 'sendsms'); ?>
						</td>
						<td class="wpsendsms_import_user_main_info_input" >
							<input type="checkbox" name="sendUserMail" id="sendUserMail" /><span style="font-weight:bold;font-size:9px;" ><?php echo __('(Peut ne pas fonctionner sur certains serveurs)', 'sendsms'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="wpsendsms_import_user_main_info_name" id="lastNameContainer"><?php echo ucfirst(strtolower(__('nom', 'sendsms'))); ?></td>
						<td class="wpsendsms_import_user_main_info_input" ><input type="text" value="" id="nomUtilisateur" name="nomUtilisateur" /></td>
					</tr>
					<tr>
						<td class="wpsendsms_import_user_main_info_name" id="firstNameContainer"><?php echo ucfirst(strtolower(__('prenom', 'sendsms'))); ?></td>
						<td class="wpsendsms_import_user_main_info_input" ><input type="text" value="" id="prenomUtilisateur" name="prenomUtilisateur" /></td>
					</tr>
					<tr>
						<td class="wpsendsms_import_user_main_info_name" id="emailContainer"><?php echo ucfirst(strtolower(__('email', 'sendsms'))); ?></td>
						<td class="wpsendsms_import_user_main_info_input" ><input type="text" value="" id="emailUtilisateur" name="emailUtilisateur" /><div id="email_domain_error" style="display:none;color:#FF0000;" ><?php echo __('Vous pouvez remplir le champs "Domaine de l\'adresse email" pour que vos emails soient automatique cr&eacute;&eacute;s', 'sendsms'); ?></div></td>
					</tr>
					<?php self::user_additionnal_field(null, 'import'); ?>
					<tr>
						<td class="wpsendsms_import_user_main_info_name">
							<?php echo __('R&ocirc;le pour les utilisateurs', 'sendsms'); ?><br/>
							<span style="font-style:italic;font-size:10px;" ><?php echo __('Si aucun r&ocirc;le n\'a &eacute;t&eacute; d&eacute;fini dans le fichier', 'sendsms'); ?></span>
						</td>
						<td class="wpsendsms_import_user_main_info_input" >
							<select name="userRoles" id="userRoles" >
								<?php
									if ( !isset($wp_roles) )
									{
										$wp_roles = new WP_Roles();
									}
									foreach ($wp_roles->get_names() as $role => $roleName)
									{
										$selected = '';
										if(($userRoles == '') && ($role == 'subscriber'))
										{
											$selected = 'selected = "selected"';
										}
										elseif(($userRoles != '') && ($role == $userRoles))
										{
											$selected = 'selected = "selected"';
										}
										echo '<option value="' . $role . '" ' . $selected . ' >' . __($roleName) . '</option>';
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="2" >&nbsp;</td>
		</tr>

		<tr>
			<td colspan="2" style="text-align:center;" ><input type="button" class="button-primary" value="<?php echo __('Ajouter &agrave; la liste des utilisateurs &agrave; importer', 'sendsms'); ?>" id="ajouterUtilisateurListe" name="ajouterUtilisateurListe" /><div id="fastAddErrorMessage" style="display:none;color:#FF0000;" ><?php echo __('Merci de remplir les champs marqu&eacute;s en rouge', 'sendsms'); ?></div></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;" ><textarea name="userLinesToCreate" id="userLinesToCreate" cols="70" rows="5"></textarea></td>
		</tr>
	</table>
	<!-- 	Submit form button	-->
<?php
	if(current_user_can('sendsms_import_user')){
?>
	<div class="user_rapid_import_button" ><input disabled="disabled" type="submit" class="button-primary" name="importSubmit_rapid" id="importSubmit_rapid" value="<?php echo __('Importer les utilisateurs', 'sendsms'); ?>" /></div>
<?php
	}
?>

	<br/>
	<br/>
	<br/>

	<!-- 	Start of file specification part	-->
	<h3 class="pointer" id="import_user_form_file_container_switcher" ><span id="user_import_container_switcher_icon" class="alignleft ui-icon user_import_container_opener" >&nbsp;</span><?php echo __('Ajout d\'utilisateur depuis un fichier', 'sendsms'); ?></h3>
	<div id="import_user_form_file_container" class="hide" >
		<div >
			<div><a href="<?php echo WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/medias/modeles/'; ?>import_users.ods" ><?php echo __('Vous pouvez t&eacute;l&eacute;charger le fichier pour construire l\'import ici', 'sendsms'); ?></a></div>
			<?php echo __('Chaque ligne devra respecter le format ci-apr&egrave;s&nbsp;:', 'sendsms'); ?>
			<br/><span style="font-style:italic;font-size:10px;" ><?php echo '<span style="color:#CC0000;" >' . __('Les champs identifiants et email sont obligatoires.', 'sendsms') . '</span><br/>' . __('Vous n\'&ecirc;tes pas oblig&eacute; de renseigner tous les champs mais tous les s&eacute;parateur doivent &ecirc;tre pr&eacute;sent.', 'sendsms') . '&nbsp;&nbsp;' . __('Exemple&nbsp;', 'sendsms') . '&nbsp;<span style="font-weight:bold;" >' . __('identifiant', 'sendsms')  . '</span>'. $separatorExample . $separatorExample . $separatorExample . $separatorExample . '<span style="font-weight:bold;" >' . __('email', 'sendsms') . '</span>' . $separatorExample . '<span style="font-weight:bold;" >' . __('telephone', 'sendsms') . '</span>' . $separatorExample; ?></span>
			<div style="margin:3px 6px;padding:12px;border:1px solid #333333;width:80%;text-align:center;" ><?php echo '<span style="color:#CC0000;" >' . __('identifiant', 'sendsms') . '</span>' . $separatorExample . __('prenom', 'sendsms') . $separatorExample . __('nom', 'sendsms') . $separatorExample . __('mot de passe', 'sendsms') . $separatorExample . '<span style="color:#CC0000;" >' . __('email', 'sendsms') . '</span>' . $separatorExample . '<span style="color:#CC0000;" >' . __('telephone', 'sendsms') . '</span>' . $separatorExample . __('role', 'sendsms'); ?></div>
		</div>
		<div >
			<table style="margin:0px 36px;" >
				<tr>
					<td>
						<?php echo __('S&eacute;parateur de champs', 'sendsms'); ?>
					</td>
					<td>
						<input type="text" name="fieldSeparator" id="fieldSeparator" value=";" />
					</td>
				</tr>
			</table>
		</div><?php echo __('Vous pouvez envoyer un fichier contenant les utilisateurs &agrave; cr&eacute;er (extension autoris&eacute;e *.odt, *.csv, *.txt)', 'sendsms'); ?>
		<input type="file" id="userFileToCreate" name="userFileToCreate" />
		<!-- 	Submit form button	-->
<?php
	if(current_user_can('sendsms_import_user')){
?>
		<div class="user_import_button" ><input type="submit" class="button-primary" name="importSubmit" id="importSubmit" value="<?php echo __('Importer les utilisateurs', 'sendsms'); ?>" /></div>
<?php
	}
?>
	</div>

</form>
<?php
	}

}
?>