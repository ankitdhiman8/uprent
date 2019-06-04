<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/validator.php';
require_once '../classes/config/settings_config.php';
require_once '../classes/mails.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "mails");

$errors_str='';
if(isset($_POST['Submit'])){
	$sc=new settings_config;
	if(!$sc->editMailSettings()) { 
		$errors_str.=$sc->getError();
		$mail_settings=$sc->getTmp();
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 

	$mail_settings_cl=new settings(); 
	$mail_settings=$mail_settings_cl->getMailSettings();
}

$info = '';
if(isset($_POST['Test'])){
	$mail = new mails();
	$mail->init();
	$mail->setSubject($lng['settings']['test_mail']);
	$mail->setMessage($lng['settings']['test_mail']);
	if($mail->send()) $info = $lng['mailto']['message_sent'];
	else $info = $lng['mailto']['sending_message_failed'];
} 

$smarty->assign("mail_settings",$mail_settings);

$smarty->assign("error",$errors_str);
$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('mails_settings.html');
close();
?>
