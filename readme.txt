=== Wordpress Send SMS - Bulk SMS ===
Contributors: Eoxia
Tags: Envoi de sms, envoi de short message service,gestion des liste d'envoi,gestion de historique, OVH web service, pack SMS, Bulk SMS
Donate link: http://www.eoxia.com/
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 1.2.5

Send SMS allows you to send Bulk SMS from your wordpress interface. Management and Import of the mailing list. Compatible with the webservice OVH-sms.

== Description ==

Wordpress Send SMS permet d'envoyer des SMS en masse depuis votre interface wordpress.
Avec le plugin Wordpress Send SMS vous pourrez envoyer, de fa&ccedil;on simple et intuitive, des SMS en masse aux utilisateurs enregistr&eacute;s. Vous pourrez g&eacute;rer les listes d'envoi des utilisateurs, importer des utilisateurs depuis des fichiers .ods, .csv, .txt. Wordpress Send SMS dispose d'un historique complet de vos envois avec la possibilit&eacute; de trier les informations par message, utilisateur, date, ou afficher toutes les informations en meme temps. Le tout accessible depuis l'interface d'administration de wordpress
Le plugin Wordpress Send SMS est compatible avec le web-service OVH-sms.Vous devez avoir le protocole SOAP install&eacute; sur votre serveur et n&eacute;cessite php5 pour utiliser le plugin.
Le plugin est compatible avec les serveurs d&eacute;di&eacute;s ovh sous release 2 (gentoo)
Veillez &agrave; sauvegarder le plugin avant de faire la mise &agrave; jour

== Installation ==

L'installation du plugin peut se faire de 2 fa&ccedil;ons :

* M&eacute;thode 1

1. T&eacute;l&eacute;chargez `Wordpress-send-SMS.zip`
2. Uploader le dossier `Wordpress-send-SMS` dans le r&eacute;pertoire `/wp-content/plugins/`
3. Activer le plugin dans le menu `Extensions` de Wordpress

* M&eacute;thode 2

1. Rechercher le plugin "Wordpress-send-SMS" &agrave; partir du menu "Extension" de Wordpress
2. Lancer l'installation du plugin

== Frequently Asked Questions ==

* Question 1 : Le plugin Wordpress Send SMS est t'il totalement gratuit ?
Oui le logiciel est totalement gratuit, il est publi&eacute; sous une licence Publique G&eacute;n&eacute;rale Affero (GNU). Ce programme est libre et gratuit en (Open Source), vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique G&eacute;n&eacute;rale Affero GNU publi&eacute;e par la Free Software Foundation.

* Question 2 : Comment envoyer un sms ?
Allez dans le menu r&eacute;glage puis WP-sms. Cr&eacuteez un compte. Ensuite, entrez vos identifiants dans les champs pr&eacute;vu.Allez ensuite dans le menu Message de Wordpress Send SMS, choisissez vos utilisateurs, &eacute;crivez votre message et cliquez sur envoyer.

* Question 3 : coment ajouter des utilisateurs ?
Allez dans le menu utilisateur puis importer des utilisateurs. Vous pouvez entrer les informtions vous m&eacirc;me ou choisir un document .odt, .csv, .txt contenant les informations des utilisateurs.

== Screenshots ==

1. Interface d'envoi des messages
2. Interface de gestion des listes d'utilisateurs
3. Interface de modification d'une liste d'utilisateur
4. Historique complet des envois
5. Historique tri&eacute; par message

== Changelog ==

Veillez &agrave; bien sauvegarder vos donn&eacute;es avant d'effectuer une mise &agrave; jour du plugin

= 1.2.5 =

Am&eacute;liorations

* ST197 - Remplacements des espaces par des - dans les adresses emails et identifiants cr&eacute;&eacute;s lors de l'import des utilisateurs
* ST198 - Renommage du menu d'import

Corrections

* ST196 - Affichage d'un message d'erreur php lors de l'enregistrement des configurations du plugin
* ST199 - V&eacute;rification de l'unicit&eacute; des num&eacute;ros de t&eacute;l&eacute;phone


= 1.2.4 = 

Am&eacute;liorations

* ST186 - Gestion des exp&eacute;diteurs (Une fois les exp&eacute;diteurs cr&eacute;&eacute;s dans l'interface de gestion ovh, il y a possibilit&eacute; de choisir celui qui sera utilis&eacute; depuis le plugin) 

Corrections

* ST187 - Style css &eacute;crasant les styles de wordpress et des autres plugin 


= 1.2.1 =

Corrections

* ST181 - Lors de la cr&eacute;ation d'une nouvelle liste, un warning &eacute;tait affich&eacute; du &agrave; un header 


= 1.2.0 =

Am&eacute;liorations

* ST177 - Gestion des droits des utilisateurs pour les diff&eacute;rentes interfaces 
* ST179 - Simplification de l'interface d'import des utilisateurs

Corrections

* ST178 - Fichier mod&egrave;le pour l'import des utilisateurs (Structure du fichier et t&eacute;l&eacute;chargement) 
* ST180 - Interface d'import (Les accents n'&eacute;tait pas supprim&eacute; des identifiants et email g&eacute;n&eacute;r&eacute;s / Le domaine n'&eacute;tait pas sauvegard&eacute;) 
* ST181 - Lors de la cr&eacute;ation d'une nouvelle liste, un warning &eacute;tait affich&eacute; du &agrave; un header 


= 1.1.2 =

Corrections

* Des fonctions de la classe sendsms_user &eacute;taient introuvables lors de l'&eacute;dition d'un utilisateur


= 1.0.0.0 =

* Envoyer un sms via un compte sur OVH-sms : http://www.ovh.com/fr/commande/telephonieSmsFax.cgi
* Entrer les informations du compte OVH-sms pour utiliser le service.
* Cr&eacute;er une liste d'envoi avec le nom de la liste, le nombre d'utilisateurs de la liste, la description, la date de modification, et la possibilit&eacute; de modifier ou supprimer la liste.
* S&eacute;lectionner un utilisateur pr&eacute;alablement import&eacute; dans Wordpress ou une liste d'utilisateur pour leur envoyer un sms.
* Importer des utilisateurs dans Wordpress en entrant soi meme les informations.
* Importer des utilisateurs dans wordpress depuis un fichier .odt, .csv, .tkt
* Les utilisateurs qui s'inscrivent sur le site peuvent entrer leur numero de mobile.

== Am&eacute;liorations Futures ==

* Choix d'autres opp&eacute;rateurs de sms (essendex.fr, campagnesms.com, smsgo.eu)
* rendre l'interface d'import homog&egrave;ne avec le reste
* Pouvoir &eacute;diter les listes d'utilisateurs depuis le menu "message" (en clickant sur details) dans une Jquery UI-dialog
* Mise en place d'un syteme d'alerte par sms accessible par webservice


== Upgrade Notice ==

= 1.0.1 =
Plugin first version

== Contactez l'auteur ==

dev@eoxia.com