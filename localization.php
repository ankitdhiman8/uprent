<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once $config_abs_path."/admin/include/lists.php";
require_once '../classes/images.php';
require_once '../classes/currencies.php';
require_once '../classes/config/settings_config.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "localization");

global $array_charsets;
$smarty->assign("array_charsets", $array_charsets);

$errors_str='';
if(isset($_POST['Submit'])){

	$sc=new settings_config;
	if(!$sc->editLocales()) { 
		$errors_str.=$sc->getError();
		$appearance_settings=$sc->getTmp();
	}
	else { 
		header("Location: localization.php");
		exit(0);
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 
	$sc=new settings_config;
	$appearance_settings=$sc->getAllLangAppearance();
}

$currencies=new currencies();
if(isset($_POST['add_currencies'])) {

	$currencies->add();
	header("Location: localization.php");
	exit(0);

}
$smarty->assign("array_currencies",$currencies->getAll());

global $array_timezones;
$smarty->assign("array_timezones", $array_timezones);

$other=0; $found=0;
foreach ($array_charsets as $row) if($row['id'] == $appearance_settings['charset']) $found=1;
if(!$found) $other=1;

$smarty->assign("other",$other);
$smarty->assign("appearance_settings",$appearance_settings);

$smarty->assign("error",$errors_str);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('localization.html');
close();
?>
