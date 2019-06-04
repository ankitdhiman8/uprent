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

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","security");

if(isset($_GET['user'])) $user=escape($_GET['user']); else { header ('Location: login_history.php'); exit(0); }
if(isset($_GET['page']) && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
$no_per_page=20;

$smarty->assign("lng",$lng);
$smarty->assign("user",$user);

$auth=new auth();

$no_auth=$auth->authCount($user);
$array_login=$auth->getLoginHistory($user,$page,$no_per_page);
$smarty->assign("array_login",$array_login);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no_auth);
$paginator->setAdmin(1);
$paginator->setNoSeo(1);
$paginator->setExtraFieldsArray(array("search"));
global $seo_settings;
$paginator->paginate($smarty);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('login_history_user.html');
close();
?>
