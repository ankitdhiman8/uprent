<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/fieldsets.php";
require_once "../classes/config/fieldsets_config.php";
require_once "../classes/categories.php";
require_once "../classes/config/categories_config.php";
require_once "../classes/fields.php";
require_once "../classes/config/fields_config.php";
global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: manage_fieldsets.php'); exit(0); }
$smarty->assign("id",$id);

$fset = new fieldsets;
$fieldset = $fset->getFieldset($id);
$smarty->assign("fieldset",$fieldset);

$fieldsets_array = $fset->getFieldsets();
$smarty->assign("fieldsets_array",$fieldsets_array);

$info = '';
if(isset($_POST['Submit'])) {
	$fset_config = new fieldsets_config;

	if(isset($_POST['move_to']) && is_numeric($_POST['move_to']) && $_POST['move_to']) {
		// move to fset $_POST['move_to']
		$categ = new categories_config;
		$categ->changeFieldset($id, escape($_POST['move_to']));
		$fset_config->delete($id);
		$info = $lng['fieldsets']['info']['categories_moved'];
	}

}

$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('delete_fieldset.html');
close();
?>
