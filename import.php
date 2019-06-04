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
require_once '../classes/images.php';
require_once "../classes/validator.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "import");

$ie = new import_export;
$ie->setPurpose("import");
$ad_templates = $ie->getAdTemplatesStr();
$user_templates = $ie->getUserTemplatesStr();
$smarty->assign("ad_templates",$ad_templates);
$smarty->assign("user_templates",$user_templates);

$templates = $ie->getAdTemplates();
$smarty->assign("templates",$templates);

$pkg = new packages();
$plans = $pkg->getAllForm();
$smarty->assign("plans",$plans);

$cat = new categories();
$categories = $cat->getAllOptions ();
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

$error=''; $info ='';
if(isset($_POST['CSV_import'])){

	$ie->setType("csv");
	$ie->Import(); 
	$info = $ie->getStats(0);
	$error = $ie->getError();

} 

if(isset($_POST['XML_import'])){

	$ie->setType("xml");
	$ie->Import(); 
	$info = $ie->getStats(0);
	$error = $ie->getError();

} 

$smarty->assign("error",$error);
$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('import.html');
close();
?>
