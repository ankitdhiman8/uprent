<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/config/groups_config.php";
require_once "../classes/users.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

$smarty->assign("tab","users");
$smarty->assign("page",$lng['panel']['users']);
$smarty->assign("lng",$lng);
$groups=new groups_config();
$array_groups=$groups->getAll();

// --------------------- delete multiple groups -----------------
if (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) {

	require_once "../classes/users_packages.php";
	foreach($_POST as $key=>$value) {
		if(!preg_match('/^(group)([0-9])+/',$key)) continue;
		if($value!="on") continue;
		$id = substr($key, 5);
		if(!is_numeric($id)) continue;
		$groups->delete($id);
	}

	header("Location: user_groups.php");
	exit(0);
}

// ----------------------- assign -------------------
$smarty->assign("array_groups",$array_groups);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('user_groups.html');
close();
?>
