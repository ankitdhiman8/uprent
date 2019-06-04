<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/import_export.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: ie_templates.php'); exit(0); }

$ie = new import_export;
$template = $ie->getTemplateDetails($id);
$fields = $ie->getTemplateFields($id);
$settings = $ie->getSettings();
$smarty->assign("template",$template);
$smarty->assign("fields",$fields);
$smarty->assign("settings",$settings);

if($template['type']=="ad") { 

	$smarty->assign("start_tag","listings");
	$smarty->assign("element_tag","listing");

} else {

	$smarty->assign("start_tag","users");
	$smarty->assign("element_tag","user");

}

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('xml_template.html');
close();
?>
