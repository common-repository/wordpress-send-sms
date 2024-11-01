<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);
require_once('../../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');

require_once('../includes.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

$q = strtolower($_GET["q"]);
if (!$q) return;

$items = array();

$listeUtilisateurs = sendsms_user::getCompleteUserList();
if(is_array($listeUtilisateurs) && (count($listeUtilisateurs) > 0))
{
	foreach($listeUtilisateurs as $utilisateur)
	{
		$tel = get_usermeta( $utilisateur['user_id'], 'tel' );
		$items[$utilisateur['user_lastname'] . ' &nbsp; ' . $utilisateur['user_firstname'] . ' &nbsp; '.$tel]  = $utilisateur['user_id'];
	}
}

foreach ($items as $key => $value)
{
	if (strpos(strtolower($key), $q) !== false) 
	{
		echo "$key|$value\n";
	}
}

?>