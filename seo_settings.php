<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/images.php';
require_once '../classes/config/settings_config.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "seo");

$errors_str='';
if(isset($_POST['Submit'])){

	$sc=new settings_config;
	$sc->editSeoSettings();

} 

if(!isset($_POST['Submit']) || $errors_str=='') { 
	$sc=new settings_config;
	$seo_settings=$sc->getAllLangSeoSettings();
}

$smarty->assign("seo_settings",$seo_settings);
$smarty->assign("error",$errors_str);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('seo_settings.html');
close();
?>
