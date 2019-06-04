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
require_once '../classes/packages.php';
require_once '../classes/categories.php';
require_once '../classes/groups.php';
require_once '../classes/users.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "export");

$ie = new import_export;
$ad_templates = $ie->getAdTemplatesStr();
$user_templates = $ie->getUserTemplatesStr();
$smarty->assign("ad_templates",$ad_templates);
$smarty->assign("user_templates",$user_templates);

$ie->setPurpose("export");
$templates = $ie->getAdTemplates();
$smarty->assign("templates",$templates);

$pkg = new packages();
$plans = $pkg->getAllForm();
$smarty->assign("plans",$plans);

$cat = new categories();
$categories = $cat->getAllForm();
$smarty->assign("categories",$categories);

$usr = new users();
$no_users = $usr->getNo();
if($no_users<=100) {
	$users = $usr->getAll();
	$smarty->assign("users",$users);
}
$smarty->assign("no_users",$no_users);

$gr = new groups();
$groups = $gr->getAll();
$smarty->assign("groups",$groups);

$error='';
if(isset($_POST['CSV_export'])){

	$ie->FormCSVExport();

} 

if(isset($_POST['XML_export'])){

	$ie->FormXMLExport();

} 

$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('import-export.html');
close();
?>
