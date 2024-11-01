var sendsms = jQuery.noConflict();

function convertAccentToJS(text)
{
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/é/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/é/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}

function addUserIdFieldList(name, id){
	sendsms(".noUserSelected").remove();
	sendsms("#userListOutput").attr("scrollTop",0);

	sendsms(sendsms("#userBlocContainer").html()).prependTo("#userListOutput");
	sendsms("#userListOutput" + " div:first").attr("id", "affectedUser" + id);
	sendsms("#affectedUser" + id).html(sendsms("#affectedUser" + id).html().replace("#USERNAME#", name));
}

/* Retourne vrai si la liste d'utilisateur est vide, faux sinon */
function isUserIdFieldListEmpty() {
  return (sendsms("#userListOutput span.noUserSelected").size()==1 || sendsms("#userListOutput").html()=='');
}
/* Retourne vrai si la liste de liste est vide, faux sinon */
function isListIdFieldListEmpty() {
  return (sendsms("#listListOutput span.noListSelected").size()==1 || sendsms("#listListOutput").html()=='');
}
/* Retourne vrai si les deux listes sont vides, faux sinon */
function isListsEmpty() {
  return (isUserIdFieldListEmpty() && isListIdFieldListEmpty());
}

function checkUserListModification(idButton){
	var actualUserList = sendsms("#actuallyAffectedUserIdList").val();
	var userList = sendsms("#affectedUserIdList").val();

	if(actualUserList == userList){
		sendsms("#" + idButton).attr("disabled", "disabled");
		sendsms("#" + idButton).addClass("button-secondary");
		sendsms("#" + idButton).removeClass("button-primary");
	}
	else{
		sendsms("#" + idButton).attr("disabled", "");
		sendsms("#" + idButton).removeClass("button-secondary");
		sendsms("#" + idButton).addClass("button-primary");
	}
}
function cleanUserIdFiedList(id){
	var actualAffectedUserList = sendsms("#affectedUserIdList").val().replace(id + ", ", " ");
	sendsms("#affectedUserIdList").val( actualAffectedUserList + id + ", ");

	if(sendsms("#affectedUser" + id)){
		sendsms("#affectedUser" + id).remove();
	}

	sendsms("#actionButtonUserLink" + id).addClass("userIsLinked");
	sendsms("#actionButtonUserLink" + id).removeClass("userIsNotLinked");
}
function deleteUserIdFiedList(id){

	var actualAffectedUserList = sendsms("#affectedUserIdList").val().replace(id + ", ", "");
	sendsms("#affectedUserIdList").val( actualAffectedUserList );
	sendsms("#affectedUser" + id).remove();

	sendsms("#actionButtonUserLink" + id).removeClass("userIsLinked");
	sendsms("#actionButtonUserLink" + id).addClass("userIsNotLinked");
}

function addListIdFieldList(name, id){
	sendsms(".noListSelected").remove();
	sendsms("#listListOutput").attr("scrollTop",0);

	sendsms(sendsms("#listBlocContainer").html()).prependTo("#listListOutput");
	sendsms("#listListOutput" + " div:first").attr("id", "affectedList" + id);
	sendsms("#affectedList" + id).html(sendsms("#affectedList" + id).html().replace("#USERNAME#", name));
}
function checkListModification(idButton){
	var actualList = sendsms("#actuallyAffectedListIdList").val();
	var listList = sendsms("#affectedListIdList").val();

	if(actualList == listList){
		sendsms("#" + idButton).attr("disabled", "disabled");
		sendsms("#" + idButton).addClass("button-secondary");
		sendsms("#" + idButton).removeClass("button-primary");
	}
	else{
		sendsms("#" + idButton).attr("disabled", "");
		sendsms("#" + idButton).removeClass("button-secondary");
		sendsms("#" + idButton).addClass("button-primary");
	}
}
function cleanListIdFiedList(id){
	var actualAffectedList = sendsms("#affectedListIdList").val().replace(id + ", ", " ");
	sendsms("#affectedListIdList").val( actualAffectedList + id + ", ");

	if(sendsms("#affectedList" + id)){
		sendsms("#affectedList" + id).remove();
	}

	sendsms("#actionButtonListLink" + id).addClass("listIsLinked");
	sendsms("#actionButtonListLink" + id).removeClass("listIsNotLinked");
}
function deleteListIdFiedList(id){
	var actualAffectedList = sendsms("#affectedListIdList").val().replace(id + ", ", "");
	sendsms("#affectedListIdList").val( actualAffectedList );
	sendsms("#affectedList" + id).remove();

	sendsms("#actionButtonListLink" + id).removeClass("listIsLinked");
	sendsms("#actionButtonListLink" + id).addClass("listIsNotLinked");
}

/* Retourne vrai si le numéro de téléphone est au format international, faux sinon */
function isTelephoneValid(tel){
  return tel.match(/^\+[0-9]{2}[0-9]{9}$/i);
}

sendsms(document).ready(function(){

	jQuery.datepicker.regional['fr'] = {
		monthNames: ['Janvier','Fevrier','Marc','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
		monthNamesShort: ['Jan','Fev','Mars','Avril','Mai','Juin','Juil','Aout','Sept','Oct','Nov','Dec'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		firstDay: 1
	};
	
	jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
	
	jQuery('#datepicker').datetimepicker({
		dateFormat: 'dd/mm/yy',
		timeText: 'Date',
		hourText: 'Heure',
		minuteText: 'Minute',
		secondText: 'Seconde',
		currentText: 'Maintenant',
		closeText: 'Valider',
		separator: ', '
	});
	
	jQuery('.truncatable_text').truncatable({limit: 200, more: '.. (Lire la suite)', less: true, hideText: ' [R&eacute;duire]' });
  
	jQuery('input#submitMessage').click(function(){
		jQuery(this).attr('disabled', true);
	});
	// Si les listes ne sont pas vides, on rend la soumission possible
	if(!isListsEmpty()){
		jQuery("input#submitMessage").attr('disabled', false);
	}
	
	jQuery("#doc_url").keyup(function(){
		jQuery("#mylink").show();
		if(jQuery("#doc_url").val()=='')
			jQuery("#mylink").hide();
		else jQuery("#mylink a").attr("href", jQuery("#doc_url").val());
	});
});