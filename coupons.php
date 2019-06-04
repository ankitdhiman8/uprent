<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/coupons.php";
require_once "../classes/config/coupons_config.php";
require_once "../classes/groups.php";

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","listings");

if(isset($_GET['page']) && is_int($_GET['page'])) $page=$_GET['page']; else $page=1;
if(isset($_GET['delete']) && is_int($_GET['delete'])) $delete=$_GET['delete'];

$smarty->assign("lng",$lng);

$c=new coupons_config();
$smarty->assign("no",$c->count());

$array_coupons=$c->getAll();
$smarty->assign("array_coupons",$array_coupons);

$smarty->display('coupons.html');
close();
?>
