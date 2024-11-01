<?php

class sendsms_init
{
	/*
	** Initiation du menu
	** @return void
	*/
	function menu(){
		add_menu_page('send-SMS', 'Send-SMS', 'sendsms_new_message', 'sendsms');
        add_submenu_page('sendsms','send-SMS', __('Message', 'sendsms'), 'sendsms_new_message', 'sendsms', array('sendsms_message','ecrireMessage'));
        add_submenu_page('sendsms','send-SMS', __("Liste d'envoi", 'sendsms'), 'sendsms_list', 'sendsms_liste', array('sendsms_liste', 'adminListe'));
		add_submenu_page('sendsms','send-SMS', __('Historique', 'sendsms'), 'sendsms_histo', 'sendsms_historique', array('sendsms_histo', 'historique'));

		add_options_page('sendsms', __('Send-SMS', 'sendsms'), 'sendsms_options', 'sendsms_option', array('sendsms_config', 'config'));
		add_users_page('sendsms', __('Import utilisateur Send-SMS', 'sendsms'), 'sendsms_import_user_menu', 'sendsms_ajout', array('sendsms_user', 'importUser'));
		add_management_page('sendsms', __('Documentation', 'sendsms'), 'sendsms_documentation', 'sendsms_doc', array('sendsms_doc', 'mydoc'));
	}

	/*
	** Initiation des feuilles de style
	** @return void
	*/
	function css()
	{
		wp_enqueue_style('sendsms_jq_autocomplete', WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/css/jquery.autocomplete.css'); 
		wp_enqueue_style('sendsms_jq_ui',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/css/jquery-ui-1.7.2.custom.css'); 
		wp_enqueue_style('send-SMS',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/css/style.css');
		wp_enqueue_style('send-SMS',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/css/UIdialog.css');
	}
	
	/*
	** Initiation du JS
	** @return void
	*/
	function js()
	{
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-jquery-ui',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/jquery-ui-1.8.16.custom.min.js');
		wp_enqueue_script('sendsms_main',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/main.js');
		wp_enqueue_script('sendsms_jq_datatable',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/jquery.dataTables.js');
		wp_enqueue_script('sendsms_jq_autocomplete',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/jquery.autocomplete.js');
		wp_enqueue_script('sendsms_jq_timepicker',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/jquery-ui-timepicker-addon.js');
		wp_enqueue_script('sendsms_jq_truncatable.js',  WP_PLUGIN_URL . '/' . SENDSMS_PLUGIN_DIR . '/js/jquery.truncatable.js');
	}
	
	/*
	** Initiation du bloc d'édition WYSIWYG
	** @return void
	*/
	function init_wysiwyg()
	{
		wp_enqueue_script('editor');
		add_thickbox();
		wp_enqueue_script('media-upload');
		add_action('admin_print_footer_scripts', 'wp_tiny_mce', 25);
		wp_enqueue_script('quicktags');
	}
}

?>