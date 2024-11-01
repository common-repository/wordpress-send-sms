<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');

require_once('../includes.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

$q = strtolower($_GET["lid"]);
if (!$q) return;

$items = array();

$listeUtilisateurs = sendsms_liste::getListeInfo($q);

echo '<h3>Liste d\'envoi : '.$listeUtilisateurs[0].'</h3>';
if(!empty($listeUtilisateurs[1][0]['user_firstname']))
{
	echo '<table id="listeListe" class="widefat post fixed" cellspacing="0">
			<thead>
				<tr valign="top">
				<th class="sorting" scope="col">Pr&eacute;nom</th>
				<th class="sorting_desc" scope="col">Nom</th>
				<th class="sorting" scope="col">T&eacute;l&eacute;phone</th>
				</tr>
			</thead>';
			
	foreach ($listeUtilisateurs[1] as $v)
	{
		echo '
			<tr valign="top">
				<td>'.$v['user_firstname'].'</td>
				<td>'.$v['user_lastname'].'</td>
				<td>'.$v['user_tel'].'</td>
			</tr>';
	}
	echo '</table>';
}
else {
	echo '<p>Liste d\'envoi vide</p>';
}

?>