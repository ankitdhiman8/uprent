<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/fields.php";
require_once "../classes/depending_fields.php";
require_once "../classes/images.php";
require_once "../classes/users.php";
require_once "../classes/groups.php";
require_once "../classes/validator.php";
require_once "../include/gmaps_util.php";
global $config_abs_path;
require_once $config_abs_path."/admin/include/lists.php";

global $db;
global $lng;
$errors_str='';
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header("Location: users_list.php"); exit(0); }
if(isset($_GET['delete'])) $delete = escape($_GET['delete']); else $delete='';

$smarty->assign("tab","users");
$smarty->assign("lng",$lng);
$smarty->assign("id",$id);

if(isset($delete) && $delete) {

	users::emptyField($id, $delete);
	header("Location: edituser.php?id=".$id);
	exit(0);

}

$users=array();
$usr=new users();
$tmp=$usr->getUser($id);
$old_group = $tmp['group'];
$smarty->assign("old_group",$old_group);

if(isset($_GET['group']) && is_numeric($_GET['group']) && $_GET['group']) $group = $_GET['group']; 
else {

	$group = 0;
	$gr = new groups();
	$no_groups = $gr->getNoActive();
	if($no_groups==1) $group = $gr->noDefActiveGroup();
	else {

		$groups_array = $gr->getShortGroups();
		$smarty->assign("groups_array",$groups_array);

	}
}

if(isset($_POST['Choose_group']) && $_POST['group'] && is_numeric($_POST['group']) && $_POST['group']) {

	$group = $_POST['group'];

}

$smarty->assign("group",$group);

global $default_fields_types;
$smarty->assign("default_fields_types", $default_fields_types);

if($group) {

	$gr = new groups();
	$group_settings = $gr->getGroup($group);
	$smarty->assign("group_settings",$group_settings);
	$uf=new fields('uf');
	$fields=$uf->getAll($group);
	$smarty->assign("fields",$fields);

	setGmaps('uf', $group, $smarty);

	$htmlarea = $uf->HTMLAreaFieldExists($group);
	$smarty->assign("htmlarea",$htmlarea);

	$error='';
	$info='';

	if(isset($_POST['Submit'])) {

		require_once "../classes/fields_process.php";

		if(!$usr->edit_info($id)) { 
			$usr_info=$usr->getTmp();
			$error=$usr->getError();
		} else { 
			$info=$usr->getInfo();
			$tmp = $usr->getUser($id);
			header("Location: users_list.php?search=".$tmp['username']."&search_for=username");
			exit(0);
		}
	}

	$smarty->assign("error",$error);
	$smarty->assign("info",$info);
	$smarty->assign("tmp",$tmp);

}

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('adduser.html');
close();
?>
