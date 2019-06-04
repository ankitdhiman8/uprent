<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/categories.php";
require_once "../classes/config/categories_config.php";
require_once "../classes/images.php";
require_once "../classes/fieldsets.php";
require_once "../classes/groups.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_categories.php'); exit(0); }
$smarty->assign("id",$id);

$tmp=array();
$cat=new categories();
$cat_config=new categories_config();

$tmp=$cat_config->getCategoryLang($id);

if(isset($_GET['delete_picture'])) {

	$cat_config->deletePicture($id);
	header("Location: edit_category.php?id=".$id);
	exit(0);

}
if(isset($_GET['delete_icon'])) {

	$cat_config->deleteIcon($id);
	header("Location: edit_category.php?id=".$id);
	exit(0);

}
$error='';
if(isset($_POST['Submit'])){

	if(!$cat_config->edit($id)) { 
		$error=$cat_config->getError();
		$tmp=$cat_config->getTmp();
	} else { header ('Location: manage_categories.php'); exit(0); }

}

$array_categories=$cat->getAllOptions();
$smarty->assign("array_categories",$array_categories);

$set=new fieldsets();
$array_sets=$set->getFieldsets();
$smarty->assign("array_sets",$array_sets);

$group = new groups();
$groups_list = $group->getShortGroups();
$smarty->assign("groups_list",$groups_list);

$smarty->assign("tmp",$tmp);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_category.html');
close();
?>
