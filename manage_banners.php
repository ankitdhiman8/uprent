<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/banners.php";
require_once "../classes/config/banners_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","banners");
$smarty->assign("lng",$lng);

//banners location module
global $modules_array;
if(in_array("banners_location", $modules_array)) {
	require_once($config_abs_path.'/admin/modules/banners_location/include.php');
	global $lng_banners_location;
	global $field_name;
	$smarty->assign("lng_banners_location", $lng_banners_location);
	$smarty->assign("field_name", $field_name);
}

$bc=new banners_config();
$array_banners=$bc->getAll();
$smarty->assign("array_banners",$array_banners);
$no_banners = count($array_banners);
$smarty->assign("no_banners",$no_banners);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('manage_banners.html');
close();
?>
