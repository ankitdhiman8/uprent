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

$bc=new banners_config();

$array_banners=$bc->getAllPositions();
$smarty->assign("array_banners",$array_banners);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('banners_settings.html');
close();
?>
