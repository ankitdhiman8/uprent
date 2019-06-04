<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/packages.php";
require_once "../classes/groups.php";
require_once "../classes/priorities.php";
require_once "../classes/categories.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else exit(0);
$smarty->assign("id",$id);

$pkg = new packages;
$package = $pkg->getPackage($id);
$smarty->assign("package",$package);

$packages_array = $pkg->getAll();
$smarty->assign("packages_array",$packages_array);

$info = '';
if(isset($_POST['Submit'])) {

	require_once "../classes/config/packages_config.php";
	$pkg_config = new packages_config;

	if(isset($_POST['action']) && $_POST['action']=="delete") {
		// delete plan
		$pkg_config->delete($id);
		$info = $lng['packages']['info']['ads_deleted'];
	} else { // move

		if(isset($_POST['move_to']) && is_numeric($_POST['move_to']) && $_POST['move_to']) {
			// move to plan $_POST['move_to']
			$listing = new listings;
			$listing->moveAds($id, escape($_POST['move_to']), 'plan');
			$pkg_config->delete($id);
			$info = $lng['packages']['info']['ads_moved'];
		}
		
	}

}

$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('delete_plan.html');
close();
?>
