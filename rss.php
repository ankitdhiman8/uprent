<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/rss.php';
require_once '../classes/config/rss_config.php';

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$rss = new rss_config();
$rss_array = $rss->getAll();
$smarty->assign("rss_array",$rss_array);
$smarty->assign("no_rss",count($rss_array));

$smarty->display('rss.html');
close();
?>
