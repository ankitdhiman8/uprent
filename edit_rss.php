<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/rss.php";
require_once '../classes/config/rss_config.php';
require_once "../classes/categories.php";
require_once "../classes/packages.php";
require_once "../classes/priorities.php";
require_once "../classes/fieldsets.php";
require_once "../classes/groups.php";


global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: rss.php'); exit(0); }

$smarty->assign("lng",$lng);
$smarty->assign("smenu","rss");

$smarty->assign("id",$id);

$rss = new rss();
$tmp = $rss->getRssFeedLang($id);
$smarty->assign("tmp",$tmp);
//_print_r($tmp);
$error='';
if(isset($_POST['Submit'])){
	$rss_config = new rss_config();
	if(!$rss_config->edit($id)) { 
		$error=$rss_config->getError();
		$tmp=$rss_config->getTmp();
		$smarty->assign("tmp",$tmp);
	} else { 
		header ('Location: rss.php');
		exit(0);
	}
}

$array=array();
$categ = new categories;
$categories=$categ->getAllOptions();
$smarty->assign("categories",$categories);

$pkg = new packages();
$packages = $pkg->getAll();
$smarty->assign("packages",$packages);

$smarty->assign("error",$error);

$smarty->display('add_rss.html');
close();
?>
