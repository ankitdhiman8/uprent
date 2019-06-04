<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/users.php";
require_once "../classes/config/settings_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']) $id = $_GET['id']; else $id = 0;

$smarty->assign("lng",$lng);
$smarty->assign("id",$id);
if($id) {
	$smarty->assign("tab","users");
	$usr = new users();
	$username = $usr->getUsername($id);
	$smarty->assign("username",$username);
}
else $smarty->assign("tab","security");

$info='';
$error='';
if(isset($_POST['Submit'])){
	if(!$id) {
		$sett=new settings_config();
		if(!$sett->change_password()) { 
			$error=$sett->getError();
		} else $info=$lng['users']['password_changed'];
	} else {

		if(!$usr->change_password($id)) { 
			$error=$usr->getError();
		} else { 
			$info=$lng['users']['password_changed'];
		}

	}
}

$smarty->assign("info",$info);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('change_password.html');
close();
?>
