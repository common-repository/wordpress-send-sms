<?php
/*
Plugin Name: Wordpress Send SMS
Description: This plugin allows to add an users list and send them a message on their mobile phone
Version: 1.2.5
Author: Eoxia
Author URI: http://www.eoxia.com
*/

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'sendsms_install'); 

DEFINE('SENDSMS_PLUGIN_DIR', basename(dirname(__FILE__)));

/* On démarre la session */
session_start();

require_once('includes/includes.php');

/* Creation of the menu */
add_action('admin_menu', array('sendSMS_init','menu'));
add_action('admin_init', array('sendSMS_init','css'));
add_action('admin_init', array('sendSMS_init','js'));

/* Ajout tel dans meta */
add_action('user_contactmethods', array('sendSMS_user', 'ajout_tel'));
add_action('register_form', array('sendSMS_user', 'ajoutField'));
add_action('register_form', array('sendSMS_user', 'registerField'));


/* On initialise le formulaire seulement dans la page de création/édition */
if (isset($_GET['page'],$_GET['action']) && ($_GET['page']=='sendsms_doc') && ($_GET['action']=='edit')){
	add_action('admin_init', array('sendsms_doc', 'init_wysiwyg'));
}
/* On récupère la liste des pages documentées afin de les comparer a la page courante */
$pages_list = sendsms_doc::get_doc_pages_name_array();
if(isset($_GET['page']) && in_array($_GET['page'], $pages_list)) {
	add_action('contextual_help', array('sendsms_doc', 'pippin_contextual_help'), 10, 3);
}

?>