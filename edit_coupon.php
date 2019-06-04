<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/groups.php";
require_once "../classes/coupons.php";
require_once "../classes/config/coupons_config.php";

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","listings");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: coupons.php'); exit(0); }
$smarty->assign("id",$id);

$coupon=array();
$cp=new coupons();
$cp_config=new coupons_config();
$coupon=$cp->getCoupon($id);

$error='';
$tmp=array();
if(isset($_POST['Submit'])){

	if(!$cp_config->edit($id)) { 
		$error=$cp_config->getError();
		$coupon=$cp_config->getTmp();
	} else { 
		header ('Location: coupons.php');
		exit(0);
	}
}

$smarty->assign("tmp",$coupon);
$smarty->assign("error",$error);

$group = new groups();
$groups_list = $group->getShortGroups();
$smarty->assign("groups_list",$groups_list);

$smarty->display('add_coupon.html');
close();
?>
