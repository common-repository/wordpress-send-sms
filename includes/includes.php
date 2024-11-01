<?php

/* Get the current language to translate the different text in plugin */
$locale = get_locale();
$moFile = WP_PLUGIN_DIR . '/' . SENDSMS_PLUGIN_DIR . '/includes/languages/wpsendsms_' . $locale . '.mo';
if (!empty($locale) && (is_file($moFile))) {
  load_textdomain('sendsms', $moFile);
}

/* Création des tables */
require_once('initBDD.php');

/* Classes de l'extension */
require_once('libraries/histo.class.php');
require_once('libraries/user.class.php');
require_once('libraries/message.class.php');
require_once('libraries/liste.class.php');
require_once('libraries/doc.class.php');
require_once('libraries/config.class.php');

/* Autres fichiers */
require_once('libraries/init.class.php');
require_once('libraries/display.class.php');
require_once('libraries/sendsms_tools.class.php');

require_once('libraries/permission.class.php');
add_action('admin_init', array('wpsendsms_permission', 'init_permission'));
add_action('edit_user_profile', array('wpsendsms_permission', 'user_permission_management'));
add_action('admin_init', array('wpsendsms_permission', 'user_permission_set'));

?>