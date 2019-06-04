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
$smarty->assign("tab","listings");
$smarty->assign("lng",$lng);

$pkg=new packages();
$pkg_config=new packages_config();
$smarty->assign("var_num_rows",$pkg_config->count());

$array_packages=$pkg->getAll();
$smarty->assign("array_packages",$array_packages);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('manage_packages.html');
close();
?>
