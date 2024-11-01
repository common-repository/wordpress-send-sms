<?php

class sendsms_liste{
	
	/*
	* Controleur de la classe sendsms_liste
	* @return void
	**/
	function adminListe(){
		
		global $wpdb;
		
		$messages = array(
			'mess1' => __('Liste cr&eacute;e avec succ&eacute;s', 'sendsms'),
			'mess2' => __('Modifications enregistr&eacute;es avec succ&eacute;s', 'sendsms'),
			'mess3' => __('Liste supprim&eacutee avec succ&eacute;s', 'sendsms')
		);
		if(!empty($_GET['m'])){
			$msgclass = 'updated'; $messageUser = $messages[$_GET['m']];
			echo '<div id="wpsendsms_message" class="'.$msgclass.'">'.$messageUser.'</div>';
		}
	
		
		if ($_GET != NULL && isset($_GET['action']) && $_GET['action']=='ajout') {
			sendsms_liste::newListe(); 					
		}
		elseif ($_GET != NULL && isset($_GET['action']) && $_GET['action']=='edit') {
			sendsms_liste::editListe();
		}
		elseif ($_GET != NULL && (isset($_GET['action']) && $_GET['action']=='delete') || isset($_POST['idconfirm'])){
			sendsms_liste::supprListe();
		}
		else sendsms_liste::displayListe();
	}
	
	/*
	** Ajout d'une nouvelle liste d'envoi
	** @return void;
	**/
	function newListe(){
		
		if(current_user_can('sendsms_create_list')){
		global $wpdb;
	
		$messageUser = '';
		$msgclass = '';
	
		if ($_POST != NULL && isset($_POST['btn_ajoutList'])) 
		{		
			if ($_POST != NULL && isset($_POST['titre_list']) && $_POST['titre_list'] == ''){
				$msgclass = 'updated';
				$messageUser =  __('Veuillez entrer un titre de liste.', 'sendsms');
			}
		}
		
		if ($_POST != NULL && isset($_POST['btn_ajoutList']))
		{
			if ($_POST != NULL && isset($_POST['titre_list']) && $_POST['titre_list'] != ''){
				if(!empty($_POST['affectedUserIdList'])){
				
					$titre_list = $_POST['titre_list'];
					$description = $_POST['description'];

					$queryListe = 'INSERT INTO '.$wpdb->prefix.'sms__liste_envoi(nomList, description, status, creation_date, last_update)
												VALUES ("' . $titre_list . '", "' . $description . '", "valid", NOW(), NOW() ) ';
					$resultListe = $wpdb->query($queryListe);
					
					$id_list = $wpdb->insert_id;
					
					/* ajout des nouveaux &eacute;l&eacute;ments*/
					$listuser = explode(',', $_POST['affectedUserIdList']);
					$subQuery = "  ";
					foreach($listuser as $userID) {
						if($userID > 0){
							$subQuery .= $wpdb->prepare("('', %d, %d, 'valid', '.NOW().'), ", $id_list, $userID);
						}
					}
					$subQuery = trim(substr($subQuery, 0, -2));
					if($subQuery != "") {
						$query = "INSERT INTO ".$wpdb->prefix."sms__liste_envoi_details (id_list, id_nomList_fk, id_user, status, last_update) values " . $subQuery ;	
						$wpdb->query($query);
					}
					
					// Message d'infos
					if($resultListe){
				echo '<script type="text/javascript">window.top.location.href = "'.admin_url('admin.php?page=sendsms_liste&m=mess1').'"</script>';exit;
					}
					else{
						$msgclass = 'updated'; $messageUser =  __('Erreur d&eacute;envoi', 'sendsms');
					}
				}
				else{
					$msgclass = 'error'; $messageUser =  __('Vous devez selectionner au moins un utilisateur pour une liste d\'envoi.', 'sendsms');
				}
			}
		}
			
		echo '<div id="wpsendsms_message" class="'.$msgclass.'">'.$messageUser.'</div>';
			
		echo '<div class="wrap">
					<div id="icon-edit-comments" class="icon32"><br /></div>
					<h2>'.__('Nouvelle liste d\'envoi','sendsms').' <a class="add-new-h2" href="?page=sendsms_liste">'.__('Revenir &agrave; la liste','sendsms').'</a></h2><br />

			<form action="" method="post">
				<table id="tableauModif" class="wp-list-table widefat fixed users">
					<thead>
            <tr>
              <th class="big_title">'.__('Nouvelle liste', 'sendsms').'</th>
            </tr>
					</thead>
					
					<tr>
						<td>
              <label>'.__('Nom de la liste', 'sendsms').' :</label><input type="text" name="titre_list" value="'.$_POST['titre_list'].'" />
            </td>
					</tr>
					<tr>
						<td>
              <label>'.__('Description', 'sendsms').' :</label><textarea name="description" rows="6" cols="62">'.$_POST['description'].'</textarea>
            </td>
					</tr>					
					
				</table>
				
				'.sendsms_user::afficheListeUtilisateur($_POST['affectedUserIdList']).'
							
							<div class="clear" style="margin-top: 20px;">
							<input type="submit" class="button-primary" name="btn_ajoutList" value="'.__('Enregistrer', 'sendsms').'" />
							<a href="?page=sendsms_liste" class="button-secondary">'.__('Retour', 'sendsms').'</a>
							</div>
							
			</form>';
		}
		else{
			_e('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; cr&eacute;er des listes', 'sendsms');
		}
	}
	
	/* 
	** Récupere la liste de listes
	** @return array;
	*/
	function getListe(){
	
		global $wpdb;
		
		$queryList = 'SELECT L.id_nomList, L.nomList, L.description, L.last_update, L.creation_date, COUNT( LD.id_nomList_fk ) AS NB
						FROM '.$wpdb->prefix.'sms__liste_envoi AS L
						LEFT JOIN '.$wpdb->prefix.'sms__liste_envoi_details AS LD ON ((LD.status = "valid") AND (L.id_nomList = LD.id_nomList_fk))
						WHERE L.status = "valid"
						GROUP BY L.id_nomList';
		$resultList = $wpdb->get_results($queryList);
		
		return $resultList;
	}
	
	/* 
	** Récupere plus d'infos concernant une liste précise
	** @param $id : id de la liste
	** @return array
	*/
	function getListeInfo($id) {
	
		global $wpdb;
	
		$id_liste = $id;
			
		$queryList = $wpdb->prepare('SELECT L.id_nomList, L.nomList, L.last_update, GROUP_CONCAT( LD.id_user SEPARATOR ", " ) AS USER
				FROM '.$wpdb->prefix.'sms__liste_envoi AS L
				LEFT JOIN '.$wpdb->prefix.'sms__liste_envoi_details AS LD ON ((LD.status = "valid") AND (L.id_nomList = LD.id_nomList_fk))
				WHERE L.status = "valid" AND L.id_nomList = %d
				GROUP BY L.id_nomList', $id_liste);
		$resultatList = $wpdb->get_row($queryList);

		$nomListe = $resultatList->nomList;
		$users = explode(', ', $resultatList->USER);
		$infos=array();
		foreach($users as $u) {
			$user_info = get_userdata($u);
			$tel = get_usermeta($u, 'tel' );
			$infos[] = array('user_firstname' => $user_info->user_firstname, 'user_lastname' => $user_info->user_lastname, 'user_tel' => $tel);
		}
		return array($nomListe, $infos);
	}
	
	/*
	** Affiche la liste des listes d'envoi
	** @return void
	*/
	function displayListe(){
	
		$resultList = self::getListe();		
		
		echo '<div class="wrap">
					<div id="icon-edit-comments" class="icon32"><br /></div>
					<h2>'.__('Listes d\'envois disponible','sendsms');
		if(current_user_can('sendsms_create_list')){
			echo ' <a class="add-new-h2" href="?page=sendsms_liste&amp;action=ajout">'.__('Ajouter','sendsms').'</a>';
		}
		echo '</h2>
					
					<table id="tableauUser" class="wp-list-table widefat fixed users">
					<thead>
						<tr>
							<th>'.__('Nom de la liste', 'sendsms').'</th>
							<th>'.__('Nombre d\'utilisateurs', 'sendsms').'</th>
							<th>'.__('Description', 'sendsms').'</th>
							<th>'.__('Date de cr&eacute;ation', 'sendsms').'</th>
							<th>'.__('Date de modification', 'sendsms').'</th>
							<th>'.__('Action', 'sendsms').'</th>
						</tr>
					 </thead>';
			
		foreach($resultList as $requette){		
			$nomList = $requette->nomList;
			$id_nomList = $requette->id_nomList;
			$creation_date = $requette->creation_date;
			$last_update = $requette->last_update;
			$nbListe = $requette->NB;
			$description = $requette->description;
												
			echo '<tr>';
			echo '<td>'.$nomList.'</td>';
			echo '<td>'.$nbListe.'</td>';
			echo '<td>'.$description.'</td>';
			echo '<td>'.mysql2date('d M Y, H:i:s', $creation_date, true).'</td>';
			echo '<td>'.mysql2date('d M Y, H:i:s', $last_update, true).'</td>';
			echo '<td>';
			if(current_user_can('sendsms_edit_list')){
				echo '<a href="?page=sendsms_liste&amp;action=edit&amp;lid='.$id_nomList.'" class="edit">'.__('&Eacute;diter','sendsms').'</a>';
			}
			if(current_user_can('sendsms_delete_list')){
				echo '<a href="?page=sendsms_liste&amp;action=delete&amp;lid='.$id_nomList.'" class="delete">'.__('Supprimer','sendsms').'</a>';
			}
			echo	'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}	

	/*
	** Suppression d'une liste
	** @return void
	*/
	function supprListe() {
		if(current_user_can('sendsms_delete_list')){
		global $wpdb;
		
		if(isset($_GET['action']) && $_GET['action']=='delete'){
		
			$id_liste = $_GET['lid'];

			$queryList = 'SELECT * FROM '.$wpdb->prefix.'sms__liste_envoi WHERE id_nomList = '.$id_liste.'';
			$resultatList = $wpdb->get_row($queryList);

			$nomListe = $resultatList->nomList;

			echo '
			<div class="wrap">
				<div id="icon-edit-comments" class="icon32"><br /></div>
				<form method="post" action="admin.php?page=sendsms_liste">
					<h2>Suppression d\'une liste d\'envoi <a class="add-new-h2" href="?page=sendsms_liste">Revenir &agrave; la liste</a></h2>
					<p>Cette page vous permet de supprimer une liste d\'envoi.</p>
					<div id="namediv" class="stuffbox metabox-holder" style="padding-top:0;">
						<h3 style="display:block;height:17px;">Confirmation</h3>
						<div class="inside" style="margin-top:12px;">
							<p>Etes-vous certain de vouloir supprimer la liste d\'envoi <code>'.$nomListe.'</code> ?</p>
						</div>
					</div>
					<input type="hidden" name="idconfirm" value="'.$id_liste.'" />
					<input class="button-primary" type="submit" value="'.__('Confirmer la suppression', 'sendsms').'" name="supprListe" />
					<a href="?page=sendsms_liste" class="button-secondary">'.__('Annuler', 'sendsms').'</a>
				</form>
			</div>';
		}	

		if ($_POST != NULL && isset($_POST['idconfirm']) && $_POST['idconfirm'] != ''){
			$id_confirm = $_POST['idconfirm'];
			
			$queryNomList = 'UPDATE '.$wpdb->prefix.'sms__liste_envoi
						SET status = "deleted", last_update = NOW() WHERE id_nomList ='.$id_confirm;
			$resultatNomList = $wpdb->get_results($queryNomList);
			
			
			$queryList = 'UPDATE '.$wpdb->prefix.'sms__liste_envoi_details
						SET status = "deleted", last_update = NOW() WHERE id_nomList_fk ='.$id_confirm;
			$resultatList = $wpdb->get_results($queryList);
			
				if ($queryNomList){
					echo '<script type="text/javascript">window.top.location.href = "'.admin_url('admin.php?page=sendsms_liste&m=mess3').'"</script>';exit;
				}
				else{
					echo __('Erreur sur la requ&ecirc;te, veuillez r&eacute;essayer.', 'sendsms').'<br />';
				}
		}
		}
		else{
			_e('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; supprimer des listes', 'sendsms');
		}
	}
	
	/*
	** Edition d'une liste
	** @return void
	*/
	function editListe(){
		if(current_user_can('sendsms_edit_list')){
		global $wpdb;
		
		$messageUser = '';
		$msgclass = '';
				
		if ($_POST != NULL && isset($_POST['idconfirm']) && $_POST['idconfirm'] != '') {
		
			$id_confirm = $_POST['idconfirm'];
			$nomListe = $_POST['nomliste'];
			$description = $_POST['description'];
			
			$queryNomList = 'UPDATE '.$wpdb->prefix.'sms__liste_envoi
							SET nomList = "'.$nomListe.'", description = "'.$description.'", last_update = NOW() WHERE id_nomList ='.$id_confirm;
			$resultatNomList = $wpdb->query($queryNomList);
					
			/* suppresion des &eacute;l&eacute;ments non existants*/
			$listuser = explode(',', $_POST['actuallyAffectedUserIdList']);
			$subQuery = "  ";
			foreach($listuser as $userID)
			{
				if($userID > 0){
					$query =  $wpdb->prepare(
						"UPDATE ".$wpdb->prefix."sms__liste_envoi_details 
						SET status = 'deleted', last_update = NOW() 
						WHERE id_user = %d AND status = 'valid'" ,$userID);
					$wpdb->query($query);
				}
			}

			/* ajout des nouveaux &eacute;l&eacute;ments*/
			$listuser = explode(',', $_POST['affectedUserIdList']);
			$subQuery = "  ";
			foreach($listuser as $userID) {
				if($userID > 0){
					$subQuery .= $wpdb->prepare("('', %d, %d, 'valid', '.NOW().'), ", $id_confirm, $userID);
				}
			}
			$subQuery = trim(substr($subQuery, 0, -2));
			if($subQuery != "") {
				$query = "INSERT INTO ".$wpdb->prefix."sms__liste_envoi_details (id_list, id_nomList_fk, id_user, status, last_update) values " . $subQuery ;
				$wpdb->query($query);
			}
					
			//$_POST['id_Liste'] = $id_confirm;
			if ($resultatNomList){
				echo '<script type="text/javascript">window.top.location.href = "'.admin_url('admin.php?page=sendsms_liste&m=mess2').'"</script>';exit;
			}
		}


		if(isset($_GET['action']) && $_GET['action']=='edit'){
		
			$id_liste = $_GET['lid'];
			
			$queryList = 'SELECT * FROM '.$wpdb->prefix.'sms__liste_envoi WHERE id_nomList = '.$id_liste.'';
			$resultatList = $wpdb->get_row($queryList);
			
			$queryList = $wpdb->prepare('SELECT L.id_nomList, L.nomList, L.description, L.last_update, GROUP_CONCAT( LD.id_user SEPARATOR ", " ) AS USER
				FROM '.$wpdb->prefix.'sms__liste_envoi AS L
				LEFT JOIN '.$wpdb->prefix.'sms__liste_envoi_details AS LD ON ((LD.status = "valid") AND (L.id_nomList = LD.id_nomList_fk))
				WHERE L.status = "valid"
					AND L.id_nomList = %d
				GROUP BY L.id_nomList', $id_liste);
			$resultatList = $wpdb->get_row($queryList);
	

			$nomListe = $resultatList->nomList;
			$description = $resultatList->description;
					echo '<div class="wrap">
					<div id="icon-edit-comments" class="icon32"><br /></div>
					<h2>'.__('&Eacute;dition d\'une liste','sendsms').' <a class="add-new-h2" href="?page=sendsms_liste">'.__('Revenir &agrave; la liste','sendsms').'</a></h2>
					<form action="" method="post">
					
							<input type="hidden" name="idconfirm" value="'.$id_liste.'" />	
							<input type="hidden" name="editListe" value="'.$id_liste.'" />	
							
							<table class="wp-list-table widefat fixed users tableauModif">
								<thead>
									<tr><th colspan="2" class="big_title">'.__('Modification', 'sendsms').'</th></tr>
								</thead>
								<tr>
									<td colspan="2">
                    <label>'.__('Titre de la liste', 'sendsms').' :</label><input type="text" name="nomliste" value="'.$nomListe.'" />
                  </td>
								</tr>
								<tr>
									<td colspan="2">
                    <label>'.__('Description', 'sendsms').' :</label><textarea name="description" rows="6" cols="62">'.$description.'</textarea>
                  </td>
								</tr>
							</table>
							
							'.sendsms_user::afficheListeUtilisateur($resultatList->USER).'
							
							<div class="clear" style="margin-top: 20px;">
                <input type="submit" class="button-primary" name="enregistrer" value="'.__('Enregistrer', 'sendsms').'" />
                <a href="?page=sendsms_liste" class="button-secondary">'.__('Retour', 'sendsms').'</a>
							</div>
              </form>';	
		}
		}
		else{
			_e('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; &eacute;diter des listes', 'sendsms');
		}
	}
	
	
	/*
	*	Output a table with the different lists binded to an element
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function getUserList($listAffectedList, $remplissage=true){
  
    echo '<table class="wp-list-table widefat fixed users tableauModif" style="float:right;margin-right:0;clear:none;">
          <thead>
            <tr>
              <th class="big_title">'.__('S&eacute;lectionner des listes d\'envoi', 'sendsms').'</th>
            </tr>
          </thead>
          <tr><td>';
		
		$utilisateursMetaBox = '';
		$alreadyLinkedList = '';
		$affectedListIdList = '';

		//on r&eacute;cupère les listes d&eacute;jà affect&eacute;s à l'&eacute;l&eacute;ment en cours.
		$listeLies = explode(', ', $listAffectedList);
		
		if($remplissage && is_array($listeLies ) && (count($listeLies) > 0))
		{
		

		$currentList = sendsms_liste::getListe();
			foreach($listeLies as $listeID)
			{
				if($listeID > 0){
						
					foreach($currentList as $result){
						if ($result->id_nomList == $listeID){
							$alreadyLinkedList .= '<div class="selectedlistOP" id="affectedList' . $listeID . '" title="' . __('Cliquez pour supprimer', 'sendsms') . '" >' . $result->nomList . ' <div class="ui-icon deleteListFromList" >&nbsp;</div></div>';
						}
					}
				}
			}
		}
		else
		{
			$alreadyLinkedList = '<span class="noListSelected">' . __('Aucune liste affect&eacute;e', 'sendsms') . '</span>';
		}
		if($remplissage && $listAffectedList != ""){
      $affectedListIdList.= $listAffectedList . ', ';
		}
		
		$utilisateursMetaBox = '
      <input type="hidden" name="actuallyAffectedListIdList" id="actuallyAffectedListIdList" value="' . $affectedListIdList . '" />
      <input type="hidden" name="affectedListIdList" id="affectedListIdList" value="' . $affectedListIdList . '" />';

    $utilisateursMetaBox .= '
		<div class="clear addLinkListElement">
				<div class="clear" >
					<span class="searchListInput ui-icon" >&nbsp;</span>
					<input class="searchListToAffect lists" type="text" name="affectedList" id="affectedList" value="' . __('Rechercher dans les listes d\'envoi', 'sendsms') . '" />
				</div>
		</div>
	
	</td></tr>
	<tr><td>
	
		<div style="float:right;width:70%;">
			<div id="completeList" class="completeList" >' . self::getUserListTable($listAffectedList, $remplissage) . '</div>
		</div>
		<div id="listListOutput" class="listListOutput ui-widget-content" >' . $alreadyLinkedList . '</div>
		
	</td></tr>
		
	<tr><td style="padding:7px 10px;">';
	if(current_user_can('sendsms_create_list')){
		$utilisateursMetaBox .= '<span class="lienAjout">+ <a href="' . get_option('siteurl') . '/wp-admin/admin.php?page=sendsms_liste&amp;action=ajout">' . __('Nouvelle liste', 'sendsms') . '</a></span>';
	}

	
	$utilisateursMetaBox .= '<div id="listBlocContainer" class="clear hide"><div class="selectedlistOP" title="' . __('Cliquez pour supprimer', 'sendsms') . '" >#USERNAME#<span class="ui-icon deleteListFromList" >&nbsp;</span></div></div>



		<script type="text/javascript" >
			sendsms(document).ready(function(){
			
				/*	Mass action : check / uncheck all	*/
				jQuery(".massAction .checkAllListe").unbind("click");
				jQuery(".massAction .checkAllListe").click(function(){
					jQuery("#completeList .buttonActionListLinkList").each(function(){
						if(jQuery(this).hasClass("listIsNotLinked")){
							jQuery(this).click();
						}
					});
				});
				jQuery(".massAction .uncheckAllListe").unbind("click");
				jQuery(".massAction .uncheckAllListe").click(function(){
					jQuery("#completeList .buttonActionListLinkList").each(function(){
						if(jQuery(this).hasClass("listIsLinked")){
							jQuery(this).click();
						}
					});
				});

				/*	Action when click on delete button	*/
				jQuery(".selectedlistOP").live("click", function(){/*
					if(isListsEmpty()) {
						jQuery("#submitMessage").attr("disabled", true);
						jQuery("#listListOutput").html(\'<span class="noListSelected">' . __('Aucune liste affect&eacute;e', 'sendsms') . '</span>\');
					}
					listDivId = jQuery(this).attr("id").replace("affectedList", "");
					deleteListIdFiedList(listDivId, "");*/
					
					listDivId = jQuery(this).attr("id").replace("affectedList", "");
					deleteListIdFiedList(listDivId, "");
					if(isListIdFieldListEmpty()) {
						sendsms("#listListOutput").html(\'<span class="noListSelected">' . __('Aucune liste affect&eacute;e', 'sendsms') . '</span>\');
						if(isListsEmpty()) {
							jQuery("#submitMessage").attr("disabled", true);
						}
					}
				});

				/*	List Search autocompletion	*/
				jQuery("#affectedList").click(function(){
					jQuery(this).val("");
				});
				jQuery("#affectedList").blur(function(){
					jQuery(this).val("' . __('Rechercher dans les listes', 'sendsms') . '");
				});
				jQuery("#affectedList").autocomplete("' . WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/includes/liveSearch/searchList.php");
				jQuery("#affectedList").result(function(event, data, formatted){
					cleanListIdFiedList(data[1]);
					addListIdFieldList(data[0], data[1], "");
					jQuery("#submitMessage").attr("disabled", false);

					jQuery("#affectedList").val("' . __('Rechercher dans les listes', 'sendsms') . '");
				});
			});
		</script></td></tr></table>';
	
		return $utilisateursMetaBox;
	}
	
	
	/*
	*	Output a table with the different lists binded to an element

	*	@param mixed $tableElement The element type we want to get the list list for
	*	@param integer $idElement The element identifier we want to get the list list for
	*
	*	@return mixed $utilisateursMetaBox The entire html code to output
	*/
	function getUserListTable($listAffectedList, $cochage=true)
	{
		$tableElement = $idElement = '';
		$utilisateursMetaBox = '';
		$idBoutonEnregistrer = 'save_group';

		$idTable = 'listeListe';
		$titres = array( '', ucfirst(strtolower(__('Nom', 'sendsms'))), ucfirst(strtolower(__('Description', 'sendsms'))), ucfirst(strtolower(__('Nb.', 'sendsms'))), /*ucfirst(strtolower(__('date de cr&eacute;ation', 'sendsms'))), */ucfirst(strtolower(__('D&eacute;tails', 'sendsms'))));
		unset($lignesDeValeurs);

		//on r&eacute;cupère les utilisateurs d&eacute;jà affect&eacute;s à l'&eacute;l&eacute;ment en cours.
		$listeUtilisateursLies = array();
		$listeLies = explode(', ', $listAffectedList);
		if($cochage && is_array($listeLies ) && (count($listeLies) > 0))
		{
			foreach($listeLies as $listeID)
			{
				if($listeID > 0){
					$listeUtilisateursLies[$listeID] = $listeID;
				}
			}
		}

		$resultList = self::getListe();
		if(is_array($resultList) && (count($resultList) > 0))
		{
			foreach($resultList as $Liste)
			{
				unset($valeurs);
				$idLigne = $tableElement . $idElement . 'listeList' . $Liste->id_nomList;
				$idCbLigne = 'cb_' . $idLigne;
				$moreLineClass = 'listIsNotLinked';
				if(isset($listeUtilisateursLies[$Liste->id_nomList]))
				{
					$moreLineClass = 'listIsLinked';
				}
				
				$value = '<a class="plusList" id="liste_'.$Liste->id_nomList.'" href="#">D&eacute;tails</a>';
				
				$valeurs[] = array('value'=>'<span id="actionButtonListLink' . $Liste->id_nomList . '" class="buttonActionListLinkList ' . $moreLineClass . '  ui-icon pointer" >&nbsp;</span>');
				$valeurs[] = array('value'=>$Liste->nomList);
				$valeurs[] = array('value'=>$Liste->description);
				$valeurs[] = array('value'=>$Liste->NB);
				/*$valeurs[] = array('value'=>mysql2date('d M Y', $Liste->creation_date, true));*/
				$valeurs[] = array('value'=>$value);
				$lignesDeValeurs[] = $valeurs;
				$idLignes[] = $idLigne;
			}
			
			echo '<div id="infoListe">Chargement...</div>';
			$script = 
		'<script type="text/javascript">
			jQuery(document).ready(function(){
			
				sendsms("#infoListe").dialog({
					autoOpen: false,
					width: 750,
					minHeight: 100,
					modal: true,
					resizable: false,	
					draggable: true,
					buttons: { "Fermer": function() { sendsms("#infoListe").dialog("close"); } }				
				});

				sendsms(".plusList").click(function(){
					sendsms("#infoListe").dialog("open").html("Chargement...");
					var id = sendsms(this).attr("id").substr(6);
					sendsms.get("' . WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/includes/ajax/infos_Liste.php?lid="+id, function(data){
						sendsms("#infoListe").html(data);
					});
					return false;
				});
			
				jQuery("#' . $idTable . '").dataTable({
					"bAutoWidth": false,
					"bInfo": false,
					"bPaginate": false,
					"bFilter": false,
					"aaSorting": [[2,"desc"]]
				});
				jQuery("#' . $idTable . '").children("tfoot").remove();
				jQuery("#' . $idTable . '").removeClass("dataTables_wrapper");
				jQuery(".buttonActionListLinkList").click(function(){
					if(jQuery(this).hasClass("addListToLinkList")){
						var currentId = jQuery(this).attr("id").replace("actionButtonListLink", "");
						cleanListIdFiedList(currentId);
						
						var lastname = jQuery(this).parent("td").next().html();
						
						addListIdFieldList(lastname, currentId, "");
					}
					else if(jQuery(this).hasClass("deleteListToLinkList")){
						deleteListIdFiedList(jQuery(this).attr("id").replace("actionButtonListLink", ""), "");
					}
					checkListModification("' . $idBoutonEnregistrer . '");
				});
				jQuery("#completeList .odd, #completeList .even").click(function(){
					if(jQuery(this).children("td:first").children("span").hasClass("listIsNotLinked")){
						var currentId = jQuery(this).attr("id").replace("' . $tableElement . $idElement . 'listeList", "");
						cleanListIdFiedList(currentId);

						var lastname = jQuery(this).children("td:nth-child(2)").html();
						
						addListIdFieldList(lastname, currentId, "");
            jQuery("#submitMessage").attr("disabled", false);
					}
					else{
						deleteListIdFiedList(jQuery(this).attr("id").replace("' . $tableElement . $idElement . 'listeList", ""), "");
						if(isListIdFieldListEmpty()) {
							sendsms("#listListOutput").html(\'<span class="noListSelected">' . __('Aucune liste affect&eacute;e', 'sendsms') . '</span>\');
							if(isListsEmpty()) {
								jQuery("#submitMessage").attr("disabled", true);
							}
						}
					}
					checkListModification("' . $idBoutonEnregistrer . '");
				});
			});
		</script>';
		}
		else
		{
			$valeurs[] = array('value'=>'');
			$valeurs[] = array('value'=>__('Aucun r&eacute;sultat trouv&eacute;', 'sendsms'));
			$valeurs[] = array('value'=>'');
			$lignesDeValeurs[] = $valeurs;
			$idLignes[] = $tableElement . $idElement . 'listeUtilisateursVide';
		}

		$classes = array('addListButtonDTable','','');
		
		$utilisateursMetaBox .= sendsms_display::getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script);

		return $utilisateursMetaBox;
	}	
}

?>