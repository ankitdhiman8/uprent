<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/paginator.php";
require_once "../classes/users.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['page']) && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
$no_per_page=20;

if(isset($_GET['search'])) $search=escape($_GET['search']); else $search='';
// ----------------------- search ------------------------
if(isset($_POST['search'])) {
	$search=escape($_POST['search']);
} else $search='';

$smarty->assign("tab","security");
$smarty->assign("lng",$lng);

$users=new users();

$no=$users->getNo($search);
$array_users=$users->getLoginHistory($page,$no_per_page,$search);
$smarty->assign("array_users",$array_users);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no);
$paginator->setAdmin(1);
$paginator->setNoSeo(1);
$paginator->setExtraFieldsArray(array("search"));
global $seo_settings;
$paginator->paginate($smarty);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('login_history.html');
close();
?>
