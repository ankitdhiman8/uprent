<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/custom_pages.php";
require_once "../classes/config/custom_pages_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['page']) && is_int($_GET['page'])) $page=$_GET['page']; else $page=1;

$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$cp=new custom_pages_config();


if(isset($_POST['search'])) {

	$search = escape($_POST['search']);
	$array_custom_pages=$cp->search($search);

	
} else {
	$array_custom_pages=$cp->getAll();

}

$no_pages = count($array_custom_pages);

$smarty->assign("array_custom_pages",$array_custom_pages);
$smarty->assign("no_pages",$no_pages);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('manage_custom_pages.html');
close();
?>
