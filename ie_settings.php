<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/import_export.php";
require_once "../classes/packages.php";
require_once "../classes/priorities.php";
require_once "../classes/categories.php";
require_once "../classes/groups.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "settings");

$ie = new import_export();
$ie->setPurpose("import");
$settings = $ie->getSettings();
$array_templates = $ie->getAdTemplates();
$smarty->assign("settings", $settings);
$smarty->assign("array_templates", $array_templates);

$bulk_fields_list = $ie->getCustomFieldsList();
$smarty->assign("bulk_fields_list", $bulk_fields_list);

$pkg = new packages();
$array_plans = $pkg->getAll();
$smarty->assign("array_plans", $array_plans);

$error='';
if(isset($_POST['SaveBulk'])){
	
	if(!$ie->saveBulkSettings()) $error = $ie->getError();
	else { 
		header("Location: ie_settings.php");
		exit(0);
	}
} 

if(isset($_POST['SaveCSV'])){
	
	if(!$ie->saveCSVSettings()) $error = $ie->getError();
	else { 
		header("Location: ie_settings.php");
		exit(0);
	}
} 

$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('ie_settings.html');
close();
?>
