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
require_once "../classes/config/packages_config.php";
require_once "../classes/priorities.php";
require_once "../classes/categories.php";
require_once "../classes/groups.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

$smarty->assign("lng",$lng);
$smarty->assign("tab","listings");

$package=array();
$errors_str='';
if(isset($_POST['Submit'])){
	$pkg=new packages_config;
	if(!$pkg->add()) { 
		$errors_str=$pkg->getError();
		$package=$pkg->getTmp();
	} else {
		header ('Location: manage_packages.php');
		exit(0);
	}
}

$smarty->assign("package",$package);
$smarty->assign("error",$errors_str);

$group = new groups();
$groups_list = $group->getShortGroups();
$smarty->assign("groups_list",$groups_list);

$priority = new priorities();
$array_priorities = $priority->getPriorities();
$smarty->assign("array_priorities",$array_priorities);

$categ = new categories();
$categories=$categ->getAllOptions ();
$smarty->assign("categories",$categories);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_package.html');
close();
?>
