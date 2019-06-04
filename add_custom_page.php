<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/validator.php";
require_once "../classes/custom_pages.php";
require_once "../classes/config/custom_pages_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$error='';
$tmp=array();
$cp_config=new custom_pages_config();
if(isset($_POST['Submit'])){
	if(!$cp_config->add()) { 
		$error=$cp_config->getError();
		$tmp=$cp_config->getTmp();
	} else { 
		$last = $cp_config->getLast();
		if($_POST['type']==1) header ('Location: edit_content.php?id='.$last);
		else header ('Location: manage_custom_pages.php');
		exit(0);
	}
}

$smarty->assign("tmp",$tmp);
$smarty->assign("error",$error);

$navlinks = common::getMainNavbarLinks();
$smarty->assign("navlinks",$navlinks);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }
$smarty->display('add_custom_page.html');
close();
?>
