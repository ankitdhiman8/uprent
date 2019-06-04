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

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: manage_categories.php'); exit(0); }
$smarty->assign("id",$id);

$cat = new categories;
$cat_config = new categories_config;
$category = $cat_config->getCategory($id);
$smarty->assign("category",$category);

$categories_array = $cat->getAllOptions();
$smarty->assign("categories_array",$categories_array);

$info = '';
if(isset($_POST['Submit'])) {

	if(isset($_POST['action']) && $_POST['action']=="delete") {
		// delete categ
		$cat_config->delete($id);
		$info = $lng['categories']['info']['ads_deleted'];
	} else { // move

		if(isset($_POST['move_to']) && is_numeric($_POST['move_to']) && $_POST['move_to']) {
			// move to category $_POST['move_to']
			$cat_config->moveAds($id, escape($_POST['move_to']));
			$cat_config->delete($id);
			$info = $lng['categories']['info']['ads_moved'];
		}
	}
}

$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('delete_category.html');
close();
?>
