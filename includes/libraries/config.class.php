<?php

class sendsms_config{

	/*
	** Enregistre les infos de config OVH
	** @return void
	*/
	function config(){
	
		$msgclass = '';
		
		if ($_POST != NULL && isset($_POST['editConf'])){

			if (isset($_POST['nicOVH'],$_POST['passOVH'],$_POST['compteSMS'],$_POST['tel_admin'],$_POST['fuseau_horaire']) && 
			$_POST['nicOVH'] != '' && $_POST['passOVH'] != '' && $_POST['compteSMS'] != '' && $_POST['tel_admin'] != '' && $_POST['fuseau_horaire'] != ''){
			
				// if(preg_match("#^[+-9]{10,12}$#", $_POST['tel_admin'])){
				if(!empty($_POST['tel_admin'])){
					
					$nicOVH = $_POST['nicOVH'];
					$passOVH = $_POST['passOVH'];
					$compteSMS = $_POST['compteSMS'];
					$tel_admin = $_POST['tel_admin'];
					$fuseau_horaire = $_POST['fuseau_horaire'];
					
					$arrConf = array(
						"nicOVH" => $nicOVH, 
						"passOVH" => $passOVH, 
						"compteSMS" => $compteSMS, 
						"tel_admin" => $tel_admin, 
						"fuseau_horaire" => $fuseau_horaire
					);
					$config = serialize($arrConf);
					$update = update_option("sendsms_config", $config, '', 'yes' );
					if($update){
						$msgclass = 'updated';
						$messageUser = 'Les modifications ont &eacute;t&eacute; prises en compte. <br/>';
					}										
					else {
						$msgclass = 'updated';
						$messageUser = __("Erreur sur la requ&ecirc;te, veuillez r&eacute;essayer", 'sendsms');
					}
				} else {
					$msgclass = 'updated';
					$messageUser = __("Exp&eacute;diteur incorecte", 'sendsms');
					}
			}
			else {
				$msgclass = 'updated';
				$messageUser = __("Veuillez remplir tous les champs", 'sendsms');
			}
				
			echo '<div id="wpsendsms_message" class="'.$msgclass.'">'.$messageUser.'</div>';
		}
		
		$configOption = get_option( 'sendsms_config', '' );
		$ligne = $configOption;
		if(!is_array($configOption)){
			$ligne = unserialize($configOption);
		}
		
		$nicOVH = $ligne['nicOVH'];
		$passOVH = $ligne['passOVH'];
		$compteSMS = $ligne['compteSMS'];
		$tel_admin = $ligne['tel_admin'];
		$fuseau_horaire = $ligne['fuseau_horaire'];
		$fuseaux = array(
			'Europe/Amsterdam', 'Europe/Berlin', 'Europe/London', 'Europe/Paris', 'America/New_York', 'America/Toronto'
		);
			
		echo '<a href="http://www.ovh.com/fr/commande/telephonieSmsFax.cgi" target="_blank" style="display:block;margin:13px 0;">'.__('Cr&eacute;er un compte OVH', 'sendsms').'</a>';

		if(current_user_can('sendsms_save_options')){
			echo '<form action="" method="post">';
		}
									
		echo '<table id="tableauModif" class="wp-list-table widefat fixed users" style="width:98%;">
					
			<thead>
            <tr>
              <th>'.__('Configuration du compte', 'sendsms').'</th>
            </tr>
					</thead>
						<tr>
							<td><label for="nicOVH">'.__('Nic OVH', 'sendsms').' :</label> <input type="text" name="nicOVH" value="'.$nicOVH.'" /></td>	
						</tr>
						<tr>
							<td><label for="passOVH">'.__('Pass OVH', 'sendsms').' :</label> <input type="password" name="passOVH" value="'.$passOVH.'" /></td>
						</tr>
						<tr>
							<td><label for="comteSMS">'.__('Compte SMS', 'sendsms').' :</label> <input type="text" name="compteSMS" value="'.$compteSMS.'" /></td>
						</tr>
						<tr>
							<td><label for="tel_admin">'.__('Exp&eacute;diteur', 'sendsms').' :</label> <input type="text" name="tel_admin" value="'.$tel_admin.'" /><br/>
							' . __('Pour que les sms soient correctement envoy&eacute;s vous devez s&eacute;lectionner un des choix existant dans la liste des exp&eacute;diteurs associ&eacute;s au compte sms ovh', 'sendsms') . '</td>
						</tr>
            <tr>
              <td>
				<label for="fuseau_horaire">'.__('Fuseau horaire', 'sendsms').' :</label>
				  <select name="fuseau_horaire" style="width:250px;">';
					foreach($fuseaux as $f):
						echo '<option value="'.$f.'"'.($fuseau_horaire==$f?' selected="selected"':null).'>'.$f.'</option>';
					endforeach;
					echo '
				  </select>
				</td>
            </tr>
			</table>
			<br />';
			if(current_user_can('sendsms_save_options')){
			echo '<input type="submit" name="editConf" class="button-primary" value="'.__('Modifier la configuration', 'sendsms').'" />
						
			</form>';	
			}
	}
}

?>