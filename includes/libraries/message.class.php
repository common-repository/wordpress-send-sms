<?php

class sendsms_message{

	/* Verifie l'existance de la config concernant l'envoi de sms OVH 
	** @return void
	*/
	function verifConf(){
		
		global $wpdb;
		
		$configOption = get_option('sendsms_config', '' );
				
			$ligne = unserialize($configOption);
		
			$nicOVH = $ligne['nicOVH'];
			$passOVH = $ligne['passOVH'];
			$compteSMS = $ligne['compteSMS'];
			$tel_admin = $ligne['tel_admin'];
			
			if($nicOVH == "" || $passOVH == "" || $compteSMS == "" || $tel_admin == "" ){
			   
				   echo '<div id="NoConfig">';
				   _e("Vous devez d'abord entrer les informations de votre compte OVH pour pouvoir envoyer des SMS.<br/>
					   Veuillez vous rendre &agrave; la page de configuration.", 'sendsms');
				   echo '</div>
				   
				   <script type="text/javascript" >
					sendsms(document).ready(function(){
						sendsms("#NoConfig").dialog({
							autoOpen: true,
							width: 850,
							minHeight: 100,
							modal: true,
							resizable: false,
							draggable: false,
							buttons: { "Valider": function() {   document.location.href="options-general.php?page=sendsms_option";  } }
						});
					});
					</script>';
			   }
	}

	function ecrireMessage(){
	
		global $wpdb;
    
		// On spécifie le fuseau horaire pour &eacute;viter les décalages !!
		$configOption = get_option( 'sendsms_config', '' );
		$ligne = unserialize($configOption);
		date_default_timezone_set($ligne['fuseau_horaire']);
		
		echo self::verifConf();
		
		$messageUser = '';
		$msgclass = '';
		$time_to_differed = 0;
			
		if ($_POST['message'] != ''){
					
			if($_POST['affectedListIdList'] != '' || $_POST['affectedUserIdList'] != '' ){
          
				// Envoi en différé
				$send=true;
				if(isset($_POST['datepicker']) && $_POST['datepicker']=='tout de suite') {
					$time_to_differed = 0;
				}
				else {
					$date=explode(', ', trim($_POST['datepicker']));
					$time=explode(':',$date[1]);
					$date=explode('/',$date[0]);
				  
					if(checkdate($date[1], $date[0], $date[2]))
					{
						$timestamp_date = strtotime($date[2].'-'.$date[1].'-'.$date[0].' '.$time[0].':'.$time[1].':00');
						$timestamp_actuel = time();
						if($timestamp_date>$timestamp_actuel) {
							$difference = $timestamp_date-$timestamp_actuel;
							$time_to_differed = ceil($difference/60);
						}
						// On estime env. 180 secondes pour envoyer le message
						elseif($timestamp_date>$timestamp_actuel-180) {
							$time_to_differed = 0;
						}
						else {
							$send=false;
							$msgclass = 'error'; $messageUser = __("La date que vous avez sp&eacute;cifi&eacute;e est d&eacute;j&agrave; pass&eacute;e.", 'sendsms');
						}
					}
					else {
						$send=false;
						$msgclass = 'error'; $messageUser = __("La date sp&eacute;cifi&eacute;e n'est pas au bon format.", 'sendsms');
					}
				}
          
				if($send) {
					$messageUser = self::envoiMessage($time_to_differed);
					$msgclass = 'updated';
				}
			}
			else{
				$msgclass = 'error'; $messageUser = __("Vous devez choisir au moins un destinataire", 'sendsms');
			}
		}
		elseif ($_POST!=NULL){
			$msgclass = 'error';
			$messageUser = __("Il n'y a pas de message &agrave; envoyer", 'sendsms');
		}
			
			echo '
				<div class="wrap">
					<div id="icon-edit-comments" class="icon32"><br /></div>
					<h2>Nouveau message</h2>';
			
      if($msgclass!='updated'){
        echo '<div id="wpsendsms_message" class="'.$msgclass.'">'.$messageUser.'</div>';
      }
      
      // On affiche le résultat de l'envoi
      if(!empty($_SESSION['sendsms_result'])):
        foreach($_SESSION['sendsms_result'] as $r):
          echo '<div id="wpsendsms_message" class="'.$r[0].'">'.$r[1].'</div>';
        endforeach;
      endif;
	  $_SESSION['sendsms_result']=array();
			
			echo ' <form action="" method="post" name="ecrireMessage">';
			
			echo 	sendsms_user::afficheListeUtilisateur($_POST['affectedUserIdList'], ($msgclass=='error'));
				
			echo 	sendsms_liste::getUserList($_POST['affectedListIdList'], ($msgclass=='error'));
			
			
			echo '
			<table class="wp-list-table widefat fixed users tableauModif" style="margin:0;width:30%;float:right;">
				<thead>
					  <tr>
						<th class="big_title">'.__('Envoyer', 'sendsms').'</th>
					  </tr>
				</thead>';
			if(current_user_can('sendsms_send_message')){
				echo '
				<tr>
					<td style="padding:8px 12px;">
						<div class="curtime">
							<span id="timestamp">
								Envoyer<input type="text" name="datepicker" id="datepicker" value="'.($msgclass=='error'?$_POST['datepicker']:'tout de suite').'" /> <small>(cliquez pour modifier)</small>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<td  style="padding:8px 12px;">
						<input type="submit" id="submitMessage" class="button-primary" name="sendMessage" value="'.__('Envoyer le message', 'sendsms').'" style="margin-bottom:0;float:right;" disabled="disabled" />
					</td>
				</tr>';
			}
			else{
				echo '
				<tr>
					<td style="padding:8px 12px;">
						' . __('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; envoyer les messages', 'sendsms') . '
					</td>
				</tr>';
			}
			echo '</table>
			';
			
			echo '<table class="wp-list-table widefat fixed users tableauModif" style="margin: 0; width: 68%; clear:left;">
						<thead>
              <tr>
                <th class="big_title">'.__('Votre message', 'sendsms').'</th>
              </tr>
						</thead>
						
			<tr><td>
				<textarea name="message" rows="6" cols="62" class="big">'.(!empty($_GET['mess'])?urldecode($_GET['mess']):($msgclass=='error'?$_POST['message']:null)).'</textarea>
			</td></tr>
			<tr><td style="padding:15px 10px;">
				<big id="nbCaractere">0</big> <span id="textCaractere">'.__('caract&egrave;re', 'sendsms').'</span> - 
				<big id="nbSMS">0</big> '.__('SMS', 'sendsms').'
			</td></tr>
			</table>
						
			</form>
			</div>
				<script type="text/javascript">
					document.forms["ecrireMessage"].elements["message"].onkeyup=function()
					{
						document.getElementById("nbCaractere").innerHTML=document.forms["ecrireMessage"].elements["message"].value.length;
						
						if(document.getElementById("nbCaractere").innerHTML == 0) {
							document.getElementById("nbSMS").innerHTML = 0;
						}
						else{
							document.getElementById("nbSMS").innerHTML = Math.floor(document.forms["ecrireMessage"].elements["message"].value.length/160)+1;
						}
						if(document.getElementById("nbCaractere").innerHTML > 1) {
							document.getElementById("textCaractere").innerHTML = "'.__('caract&egrave;res', 'sendsms').'";
						}
						else document.getElementById("textCaractere").innerHTML = "'.__('caract&egrave;re', 'sendsms').'";
					}
				</script>';
	}
	
	/* Envoi un message
	** @param $time_to_differed : temps en minutes pour un envoi en différé
	** @return void
	*/
	function envoiMessage($time_to_differed=0){
		
		global $wpdb;			
		
		$message = $_POST['message'];

		$queryMessage = 'INSERT INTO '.$wpdb->prefix.'sms__message(message, creation_date, last_update)
						VALUES ("' . $message . '",  NOW() , NOW() )';
									
		$result = $wpdb->query($queryMessage);
		$id_message = $wpdb->insert_id;
						
		// Recuperation des infos du compte
		$configOption = get_option( 'sendsms_config', '' );
				
		$ligne = unserialize($configOption);
		
		$nicOVH = $ligne['nicOVH']; $passOVH = $ligne['passOVH']; $compteSMS = $ligne['compteSMS']; $tel_admin = $ligne['tel_admin'];
			
		$userList = array();

		// Boucle d'envoi
									
		/* recuperation user selectionn&eacute;s */
		// Si liste
		if(isset($_POST['affectedListIdList']) && $_POST['affectedListIdList'] != '')
		{		
			$listeSelect = explode(',', $_POST['affectedListIdList']);
						
			foreach($listeSelect as $ListeID) 
			{							
				$id_liste = trim($ListeID);

				// Requette liste
				$queryListe = $wpdb->prepare('SELECT LIST.nomList, LIST_DETAILS.id_user
											FROM ' . $wpdb->prefix . 'sms__liste_envoi AS LIST
											LEFT JOIN '.$wpdb->prefix.'sms__liste_envoi_details AS LIST_DETAILS ON (LIST_DETAILS.id_nomList_fk = LIST.id_nomList)
											WHERE 
												LIST.id_nomList = %d AND 
												LIST.status = "valid" AND 
												LIST_DETAILS.status = "valid" 
											GROUP BY LIST_DETAILS.id_user', $id_liste);
				$infosList = $wpdb->get_results($queryListe);
				foreach ($infosList as $info) {							
					$tel = get_usermeta( $info->id_user, 'tel' );
					$userList[$info->id_user]['from'][] = $info->nomList;
					$userList[$info->id_user]['tel'] = $tel;	
				}
			}
		}

		if(isset($_POST['affectedUserIdList']) && $_POST['affectedUserIdList'] != '')
		{	
			$userSelect = explode(',', $_POST['affectedUserIdList']);
			foreach($userSelect as $id_user)
			{
				$id_user = trim($id_user);
						
				if($id_user != ''){
					$tel = get_usermeta( $id_user, 'tel' );
					$userList[$id_user]['from'][] = 'userlist';
					$userList[$id_user]['tel'] = $tel;
				}
			}
		}

		if(is_array($userList) && (count($userList) > 0))
		{
			// Traitement caracteres speciaux
			$message = str_replace ("\'", " ' ", $message);
			$message = str_replace ('\"', ' " ', $message);
			$message = utf8_encode($message);
					
			// Envoi du message
			$_SESSION['sendsms_result']=array();
			foreach($userList as $user_id => $userInfos)
			{
				$statusEnvoi = 'send';
				try {
					$soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.8.wsdl");
					$session = $soap->login($nicOVH, $passOVH, "fr", false);
					$result = $soap->telephonySmsSend($session, $compteSMS, $tel_admin, $userInfos['tel'], $message, "", "1", $time_to_differed, "");
					$soap->logout($session); 
				}
				catch(SoapFault $fault) {
					// Affichage des erreurs soap			
					if($fault != '') {
						$statusEnvoi = 'erreur';
						$_SESSION['sendsms_result'][] = array('error', 'Impossible d\'envoyer le message au num&eacute;ro <b>'.$userInfos['tel'].'</b>');
					}
				}
            
				if(empty($userInfos['tel']) && $statusEnvoi!='erreur') {
					$statusEnvoi = 'erreur';
					$_SESSION['sendsms_result'][] = array('error', 'Impossible d\'envoyer le message a ce destinataire car son num&eacute;ro de t&eacute;l&eacute;phone est vide');
				}
				elseif($statusEnvoi=='send'){
					$_SESSION['sendsms_result'][] = array('updated', 'Le message a bien &eacute;t&eacute; transmis au num&eacute;ro <b>'.$userInfos['tel'].'</b>');
				}
            
				// Si c'est en différé on met le status en attente
				if($time_to_differed>0){
					$statusEnvoi='waiting';
				}
				$differed_date = $time_to_differed>0?date('Y-m-d H:i:s',$time_to_differed*60+time()):'0000-00-00 00:00:00';
				$wpdb->insert($wpdb->prefix . 'sms__historique', array('id_hist' => '', 'id_message_fk' => $id_message, 'id_user_fk' => $user_id, 'numTel' => $userInfos['tel'], 'sendFrom' => serialize($userInfos['from']), 'statusEnvoi' => $statusEnvoi, 'creation_date' => date('Y-m-d H:i:s'), 'last_update' => date('Y-m-d H:i:s'), 'differed_date' => $differed_date));
			}
		}
	}
}

?>