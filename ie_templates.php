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
require_once "../classes/users.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "templates");

$ie = new import_export();

$import_ad_fields = $ie->getImportCSVFields('ad');
$import_ad_fields .=  ",category,pictures";
global $settings;
if($settings['nologin_enabled']) 
	$import_ad_fields .=  ",mgm_email";
$import_user_fields = $ie->getImportCSVFields('user');

$export_ad_fields = $ie->getExportCSVFields('ad');
$export_user_fields = $ie->getExportCSVFields('user');

$smarty->assign("import_ad_fields", $import_ad_fields);
$smarty->assign("import_user_fields", $import_user_fields);

$smarty->assign("export_ad_fields", $export_ad_fields);
$smarty->assign("export_user_fields", $export_user_fields);

$ie->setData('ad');
$fields = explode(",", $import_ad_fields);
$smarty->assign("fields", $fields);


$array_templates = $ie->getTemplates();
$smarty->assign("array_templates", $array_templates);

$error='';
if(isset($_POST['Add'])){
	
	if(!$ie->addTemplate()) $error = $ie->getError();
	else { 
		header("Location: ie_templates.php");
		exit(0);
	}
} 

$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('ie_templates.html');
close();
?>
