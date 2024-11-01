<?php

class sendsms_histo{

	/* Controleur de la classe */
	function historique(){

		global $wpdb;

		echo '<div class="wrap">
				<div id="icon-edit-comments" class="icon32"><br /></div>
					<h2>Historique d\'envoi</h2><br />
					<form id="histoSelect" action="" method="post">
						'.__('Trier par', 'sendsms').' :';
		
				$verif = 'Message';
				$select = isset($_POST['trier']) ? $_POST['trier'] : $verif;
				
		echo '		
				<select name="trier" onchange="submit()">';
				if($select == 'Tout'){ echo '<option selected="selected">Tout</option>'; }
				else{ echo '<option>'.__('Tout', 'sendsms').'</option>'; }
				
				if($select == 'Message'){ echo '<option selected="selected">Message</option>'; }
				else{ echo '<option>'.__('Message', 'sendsms').'</option>'; }
				
				if($select == 'Utilisateur'){ echo '<option selected="selected">Utilisateur</option>'; }
				else{ echo '<option>'.__('Utilisateur', 'sendsms').'</option>'; }
				
				if($select == 'Date'){ echo '<option selected="selected">Date</option>'; }
				else{ echo '<option>'.__('Date', 'sendsms').'</option>'; }
		echo '	</select>
			 </form>';
					
		/* Test du tri */
		if(isset($_POST['trier']) != ''){
			if (($_POST['trier']) == 'Tout'){
				$verif = 'Tout';
			}	
			elseif (($_POST['trier']) == 'Message'){
				$verif = 'Message';
			}
			elseif (($_POST['trier']) == 'Utilisateur'){
				$verif = 'User';
			}
			elseif (($_POST['trier']) == 'Date'){
				$verif = 'Date';
			}
		}

		$query = 'SELECT * FROM ' . $wpdb->prefix . 'sms__message, ' . $wpdb->prefix . 'users, ' . $wpdb->prefix . 'sms__historique
					  WHERE ID = id_user_fk AND id_message = id_message_fk
					  AND ' . $wpdb->prefix . 'sms__historique.status = "valid"
					  ORDER BY ' . $wpdb->prefix . 'sms__historique.creation_date DESC';
		$result = $wpdb->get_results($query);
		
		$output = '';
    
    /* On spécifie le fuseau horaire pour éviter les décalages */
    $configOption = get_option('sendsms_config', '');
    $ligne = unserialize($configOption);
    date_default_timezone_set($ligne['fuseau_horaire']);
		
		switch ($verif){
		
			/* Tri par message */
			default:
			
				foreach($result as $requette){ 
					$tempResult[$requette->id_message][] = $requette;
				}
				/*	Lecture du tableau par message	*/
        if(!empty($tempResult)):
          foreach($tempResult as $idMessage => $detailsMessage){
          
            $output .= '
			<strong> &nbsp;-&nbsp;' . $detailsMessage[0]->message . '</strong>
            <table class="wp_list-table widefat fixed users tableauHistorique">
              <thead>
				<tr>
					<th>'.__('Nom', 'sendsms').'</th>
					<th>'.__('Pr&eacute;nom', 'sendsms').'</th>
					<th>'.__('T&eacute;l&eacute;phone', 'sendsms').'</th>
					<th>'.__('Envoy&eacute; depuis', 'sendsms').'</th>
					<th>'.__('Date', 'sendsms').'</th>
					<th>'.__('Envoi pr&eacute;vu le', 'sendsms').'</th>
					<th>'.__('&Eacute;tat', 'sendsms').'</th>
					<th class="message">'.__('Message', 'sendsms').'</th>
				</tr>
              </thead>
			  <tbody>';
              
            foreach($detailsMessage as $data){	
              
              $id_user = $data->ID;
              $tel = $data->numTel;
              $sendFrom = unserialize($data->sendFrom);
              $dateEnvoi = $data->creation_date;
              $statusEnvoi = $data->statusEnvoi;
              $message = $data->message;
                
                if($statusEnvoi == 'send'){
                  $statusEnvoi = __('Envoy&eacute;', 'sendsms');
                }
                elseif($statusEnvoi == 'waiting'){
								$statusEnvoi = __('En attente', 'sendsms');
							}
                else{
                  $statusEnvoi = __('Erreur d\'envoi', 'sendsms');
                }
                
                $nom = get_usermeta( $id_user, 'last_name' );
                $prenom = get_usermeta( $id_user, 'first_name' );

              $sendFromList = ' ';
              if(is_array($sendFrom) && (count($sendFrom) > 0)){
              
                foreach($sendFrom as $sendFromElement){
                  if($sendFromElement == 'userlist'){
                    $sendFromElement = __('liste des utilisateurs', 'sendsms');
                  }
                  $sendFromList .= $sendFromElement . ', ';
                }
              }

              $output .=  '
			  <tr>
                <td>'.$nom.'</td>
                <td>'.$prenom.'</td>
                <td>'.$tel.'</td>
                <td>'.trim(substr($sendFromList, 0, -2)).'</td>
                <td>'.mysql2date('d M Y H:i:s', $dateEnvoi, true).'</td>
                <td>'.(($data->differed_date=='0000-00-00 00:00:00')?'--':mysql2date('d M Y H:i', $data->differed_date, true)).'</td>
                <td>'.((strtotime($data->differed_date)>time() OR $data->differed_date=='0000-00-00 00:00:00' OR $statusEnvoi == __('Erreur d\'envoi', 'sendsms')) ? $statusEnvoi : 'Envoy&eacute;').'</td>
                <td><span class="truncatable_text">'.$message.'</span> - <a href="'.site_url().'/wp-admin/admin.php?page=sendsms&amp;mess='.urlencode($message).'">Renvoyer ce message</a></td>
              </tr>';
            }
            $output .= '
                  </tbody>
				  </table>';
          }
        endif;
		break;
		
		/* Pas de tri */
		case 'Tout':
			{
				$output .= '
				<table class="wp_list-table widefat fixed users tableauHistorique">
					<thead>
						<tr>
						  <th>'.__('Nom', 'sendsms').'</th>
						  <th>'.__('Pr&eacute;nom', 'sendsms').'</th>
						  <th>'.__('T&eacute;l&eacute;phone', 'sendsms').'</th>
						  <th>'.__('Envoy&eacute; depuis', 'sendsms').'</th>
						  <th>'.__('Date', 'sendsms').'</th>
						  <th>'.__('Envoi pr&eacute;vu le', 'sendsms').'</th>
						  <th>'.__('&Eacute;tat', 'sendsms').'</th>
						  <th class="message">'.__('Message', 'sendsms').'</th>
						</tr>
					</thead>
					<tbody>';
					
					foreach($result as $requette){	
						
						$id_user = $requette->ID;
						$tel = $requette->numTel;
						$sendFrom = unserialize($requette->sendFrom);
						$dateEnvoi = $requette->creation_date;
						$statusEnvoi = $requette->statusEnvoi;
						$message = $requette->message;
							
							if($statusEnvoi == 'send'){
								$statusEnvoi = __('Envoy&eacute;', 'sendsms');
							}
              elseif($statusEnvoi == 'waiting'){
								$statusEnvoi = __('En attente', 'sendsms');
							}
							else{
								$statusEnvoi = __('Erreur d\'envoi', 'sendsms');
							}
							
							$nom = get_usermeta( $id_user, 'last_name' );
							$prenom = get_usermeta( $id_user, 'first_name' );

						$sendFromList = '  ';
						if(is_array($sendFrom) && (count($sendFrom) > 0)){
							foreach($sendFrom as $sendFromElement){
								if($sendFromElement == 'userlist'){
									$sendFromElement = __('Liste des utilisateurs', 'sendsms');
								}
								$sendFromList .= $sendFromElement . ', ';
							}
						}
            
						$output .=  '
						<tr>
							<td>'.$nom.'</td>
							<td>'.$prenom.'</td>
							<td>'.$tel.'</td>
							<td>'.trim(substr($sendFromList, 0, -2)).'</td>
							<td>'.mysql2date('d M Y H:i:s', $dateEnvoi, true).'</td>
              <td>'.(($requette->differed_date=='0000-00-00 00:00:00')?'--':mysql2date('d M Y H:i', $requette->differed_date, true)).'</td>
							<td>'.((strtotime($requette->differed_date)>time() OR $requette->differed_date=='0000-00-00 00:00:00' OR $statusEnvoi == __('Erreur d\'envoi', 'sendsms')) ? $statusEnvoi : 'Envoy&eacute;').'</td>
							<td><span class="truncatable_text">'.$message.'</span> - <a href="'.site_url().'/wp-admin/admin.php?page=sendsms&amp;mess='.urlencode($message).'">Renvoyer ce message</a></td>
						</tr>';
					}
				
				$output .=  '
					</tbody>
				</table>';
			}
			break;
			
			/* Tri par utilisateur */
			case 'User':

        foreach($result as $requette){ 
					$tempResult[$requette->ID][] = $requette;
				}
        
        if(!empty($tempResult)):
				foreach($tempResult as $idUser => $detailsUser){
				
					$output .= '<strong> &nbsp;-&nbsp;' . $detailsUser[0]->display_name . '</strong>
					<table class="wp_list-table widefat fixed users tableauHistorique">
						<thead>
							<tr>
								<th>'.__('Nom', 'sendsms').'</th>
							    <th>'.__('Pr&eacute;nom', 'sendsms').'</th>
								<th>'.__('T&eacute;l&eacute;phone', 'sendsms').'</th>
								<th>'.__('Envoy&eacute; depuis', 'sendsms').'</th>
								<th>'.__('Date', 'sendsms').'</th>
								<th>'.__('Envoi pr&eacute;vu le', 'sendsms').'</th>
								<th>'.__('&Eacute;tat', 'sendsms').'</th>
								<th class="message">'.__('Message', 'sendsms').'</th>
							</tr>
						</thead>
						<tbody>';
						
					foreach($detailsUser as $data){	
						
						$id_user = $data->ID;
						$tel = $data->numTel;
						$sendFrom = unserialize($data->sendFrom);
						$dateEnvoi = $data->creation_date;
						$statusEnvoi = $data->statusEnvoi;
						$message = $data->message;
							
							if($statusEnvoi == 'send'){
								$statusEnvoi = __('Envoy&eacute;', 'sendsms');
							}
              elseif($statusEnvoi == 'waiting'){
								$statusEnvoi = __('En attente', 'sendsms');
							}
							else{
								$statusEnvoi = __('Erreur d\'envoi', 'sendsms');
							}
							$nom = get_usermeta( $id_user, 'last_name' );
							$prenom = get_usermeta( $id_user, 'first_name' );

						$sendFromList = '  ';
						if(is_array($sendFrom) && (count($sendFrom) > 0)){
						
							foreach($sendFrom as $sendFromElement){
								if($sendFromElement == 'userlist'){
									$sendFromElement = __('liste des utilisateurs', 'sendsms');
								}
								$sendFromList .= $sendFromElement . ', ';
							}
						}

						$output .=  '
						<tr>
							<td>'.$nom.'</td>
							<td>'.$prenom.'</td>
							<td>'.$tel.'</td>
							<td>'.trim(substr($sendFromList, 0, -2)).'</td>
							<td>'.mysql2date('d M Y H:i:s', $dateEnvoi, true).'</td>
              <td>'.(($data->differed_date=='0000-00-00 00:00:00')?'--':mysql2date('d M Y H:i', $data->differed_date, true)).'</td>
							<td>'.((strtotime($data->differed_date)>time() OR $data->differed_date=='0000-00-00 00:00:00' OR $statusEnvoi == __('Erreur d\'envoi', 'sendsms')) ? $statusEnvoi : 'Envoy&eacute;').'</td>
							<td><span class="truncatable_text">'.$message.'</span> - <a href="'.site_url().'/wp-admin/admin.php?page=sendsms&amp;mess='.urlencode($message).'">Renvoyer ce message</a></td>
						</tr>';
					}
					$output .= '
								</tbody>
							</table>';
				}
        endif;
		break;
			
			/* Tri par date */
			case 'Date': 

        foreach($result as $requette){ 
					$tempResult[$requette->creation_date][] = $requette;
				}
        
        if(!empty($tempResult)):
				foreach($tempResult as $creation_date => $detailsDate){
				
					$output .= '
					<strong>- Le ' . mysql2date('d M Y, H:i', $detailsDate[0]->creation_date, true) . '</strong>
					<table class="wp_list-table widefat fixed users tableauHistorique">
						<thead>
							<tr>
								<th>'.__('Nom', 'sendsms').'</th>
								<th>'.__('Pr&eacute;nom', 'sendsms').'</th>
								<th>'.__('T&eacute;l&eacute;phone', 'sendsms').'</th>
								<th>'.__('Envoy&eacute; depuis', 'sendsms').'</th>
								<th>'.__('Date', 'sendsms').'</th>
								<th>'.__('Envoi pr&eacute;vu le', 'sendsms').'</th>
								<th>'.__('&Eacute;tat', 'sendsms').'</th>
								<th class="message">'.__('Message', 'sendsms').'</th>
							</tr>
						</thead>
						<tbody>';
						
					foreach($detailsDate as $data){	
						
						$id_user = $data->ID;
						$tel = $data->numTel;
						$sendFrom = unserialize($data->sendFrom);
						$dateEnvoi = $data->creation_date;
						$statusEnvoi = $data->statusEnvoi;
						$message = $data->message;
						
							if($statusEnvoi == 'send'){
								$statusEnvoi = __('Envoy&eacute;', 'sendsms');
							}
              elseif($statusEnvoi == 'waiting'){
								$statusEnvoi = __('En attente', 'sendsms');
							}
							else{
								$statusEnvoi = __('Erreur d\'envoi', 'sendsms');
							}
									
							$nom = get_usermeta( $id_user, 'last_name' );
							$prenom = get_usermeta( $id_user, 'first_name' );

						$sendFromList = '  ';
						if(is_array($sendFrom) && (count($sendFrom) > 0)){
						
							foreach($sendFrom as $sendFromElement){
								if($sendFromElement == 'userlist'){
									$sendFromElement = __('liste des utilisateurs', 'sendsms');
								}
								$sendFromList .= $sendFromElement . ', ';
							}
						}

						$output .=  '
						<tr>
							<td>'.$nom.'</td>
							<td>'.$prenom.'</td>
							<td>'.$tel.'</td>
							<td>'.trim(substr($sendFromList, 0, -2)).'</td>
							<td>'.mysql2date('d M Y H:i:s', $dateEnvoi, true).'</td>
              <td>'.(($data->differed_date=='0000-00-00 00:00:00')?'--':mysql2date('d M Y H:i', $data->differed_date, true)).'</td>
							<td>'.((strtotime($data->differed_date)>time() OR $data->differed_date=='0000-00-00 00:00:00' OR $statusEnvoi == __('Erreur d\'envoi', 'sendsms')) ? $statusEnvoi : 'Envoy&eacute;').'</td>
							<td><span class="truncatable_text">'.$message.'</span> - <a href="'.site_url().'/wp-admin/admin.php?page=sendsms&amp;mess='.urlencode($message).'">Renvoyer ce message</a></td>
						</tr>';
					}
					$output .= '
							</tbody>
						</table>
						';
				}
        endif;
			break;
		}

		$script = 
		'<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#tableauHistorique").dataTable({
					"bAutoWidth": false,
					"bInfo": false,
					"bPaginate": false,
					"bFilter": false,
					"aaSorting": [[2,"desc"]]
				});

			});
		</script>';
		echo $script;
		echo $output;
	}
}
?>