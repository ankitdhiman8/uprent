<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/fieldsets.php";
require_once "../classes/config/fieldsets_config.php";
require_once "../classes/categories.php";

global $db;

$smarty = new Smarty;
$smarty = common($smarty);

global $lng;

$smarty->assign("lng",$lng);
$smarty->assign("tab","settings");

$fields=array();
$tmp=array();
if(isset($_POST['Submit'])){
	$fieldset=new fieldsets_config;
	if(!$fieldset->add()) { 
		$errors_str=$fieldset->getError();
		$tmp=$fieldset->getTmp();
		$smarty->assign("error",$errors_str);
	} else { 
		header ('Location: manage_fieldsets.php');
		exit(0);
	}
}

$smarty->assign("tmp",$tmp);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_fieldset.html');
close();
?>
