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
require_once "../classes/validator.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: ie_templates.php'); exit(0); }

$smarty->assign("lng",$lng);
$smarty->assign("smenu", "templates");
$smarty->assign("id", $id);

$ie = new import_export();
$fields = $ie->getTemplateFields($id);
$smarty->assign("fields", $fields);

$error='';
if(isset($_POST['Submit'])){
	
	$ie->editAliases($id); 
	$error = $ie->getError();
	if(!$error) { 
		header("Location: ie_templates.php");
		exit(0);
	}
} 

$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('edit_ie_templates_aliases.html');
close();
?>
