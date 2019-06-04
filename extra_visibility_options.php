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
require_once '../classes/priorities.php';
require_once '../classes/config/priorities_config.php';
require_once '../classes/config/settings_config.php';
require_once '../classes/validator.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "visibility");

$errors_str='';
$priorities_errors_str='';
if(isset($_POST['Submit'])){
	$sc=new settings_config;
	if(!$sc->editVisibilityOptions()) { 
		$errors_str.=$sc->getError();
		$ads_settings=$sc->getTmp();
	}
	else { 
		header("Location: extra_visibility_options.php");
		exit(0);
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 
	$settings=new settings(); 
	$ads_settings=$settings->getAdsSettings();
}

$priorities=new priorities();
$tmp=array();
if(isset($_POST['Add'])){
	$priorities_config=new priorities_config();
	if(!$priorities_config->add()) { 
		$priorities_errors_str.=$priorities_config->getError();
		$tmp=$priorities_config->getTmp();
	} else { 
		header("Location: extra_visibility_options.php");
		exit(0);
	}
} 

$array_priorities = $priorities->getAll();

$no_priorities = count($array_priorities);
$smarty->assign("array_priorities",$array_priorities);
$smarty->assign("no_priorities",$no_priorities);

$smarty->assign("ads_settings",$ads_settings);
$smarty->assign("tmp",$tmp);

$smarty->assign("error",$errors_str);
$smarty->assign("priorities_error",$priorities_errors_str);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('extra_visibility_options.html');
close();
?>
