<?php

function sendsms_install() {

	global $wpdb;
	/* Creation de la base de donnée */

	/* Table : sms__liste_envoi */
	$queryNomlist = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'sms__liste_envoi (
				id_nomList INT( 10 ) NOT NULL AUTO_INCREMENT ,
				nomList VARCHAR( 30 ) NOT NULL ,
				description VARCHAR( 150 ) NOT NULL ,
				status ENUM( "valid", "moderated", "deleted" ) NOT NULL ,
				creation_date DATETIME NOT NULL ,
				last_update DATETIME NOT NULL ,
				PRIMARY KEY ( id_nomList )
				) ENGINE = MYISAM';
	$resultNomlist = $wpdb->query($queryNomlist);
			   
	/* Table : sms__message */	   
	$queryMessage = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'sms__message (
				id_message INT( 10 ) NOT NULL AUTO_INCREMENT ,
				message VARCHAR( 6000 ) NOT NULL ,
				status ENUM( "valid", "moderated", "deleted" ) NOT NULL ,
				creation_date DATETIME NOT NULL ,
				last_update DATETIME NOT NULL ,
				PRIMARY KEY ( id_message )
				) ENGINE = MYISAM';
	$resultMessage = $wpdb->query($queryMessage);

	/* Table : sms__liste_envoi_details */
	$queryList = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'sms__liste_envoi_details (
				id_list INT( 10 ) NOT NULL AUTO_INCREMENT ,
				id_nomList_fk INT( 10 ) NOT NULL ,
				id_user INT( 10 ) NOT NULL ,
				status ENUM( "valid", "moderated", "deleted" ) NOT NULL ,
				creation_date DATETIME NOT NULL ,
				last_update DATETIME NOT NULL ,
				PRIMARY KEY ( id_list )
				) ENGINE = MYISAM';
	$resultList = $wpdb->query($queryList);			
				
	/* Table : sms__historique */
	$queryHisto = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'sms__historique (
				id_hist INT( 10 ) NOT NULL AUTO_INCREMENT ,
				id_message_fk INT( 10 ) NOT NULL ,
				id_user_fk INT( 10 ) NOT NULL ,
				numTel VARCHAR( 20 ) NOT NULL ,
				sendFrom VARCHAR( 255 ) NOT NULL ,
				statusEnvoi ENUM( "send", "erreur" ) NOT NULL ,
				status ENUM( "valid", "moderated", "deleted" ) NOT NULL ,
				creation_date DATETIME NOT NULL ,
				last_update DATETIME NOT NULL ,
				PRIMARY KEY ( id_hist )
				) ENGINE = MYISAM';
	$resultHisto = $wpdb->query($queryHisto);
	
	/* Table : documentation */	
	$queryDoc = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix.sendsms_doc::prefix . '__documentation (
		doc_id int(11) unsigned NOT NULL AUTO_INCREMENT,
		doc_page_name varchar(255) NOT NULL,
		doc_url varchar(255) NOT NULL,
		doc_html text NOT NULL,
		doc_creation_date datetime NOT NULL,
		PRIMARY KEY ( doc_id )
	) ENGINE=MyISAM';
	$resultDoc = $wpdb->query($queryDoc);
  
	/* Mise à jour de la table sms__historique */
	$sql = 'ALTER TABLE '.$wpdb->prefix.'sms__historique ADD differed_date DATETIME NOT NULL AFTER creation_date';
	$wpdb->query($sql);
	$sql = 'ALTER TABLE ' . $wpdb->prefix . 'sms__historique MODIFY COLUMN statusEnvoi ENUM( "waiting", "send", "erreur" )';
	$wpdb->query($sql);
	
	/* Mise à jour de la table documentation */
	$sql = 'ALTER TABLE ' . $wpdb->prefix.sendsms_doc::prefix . '__documentation ADD doc_active ENUM( "active", "deleted" ) default "active"';
	$wpdb->query($sql);
	
}

?>