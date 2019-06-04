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
require_once "../classes/users.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: user_groups.php'); exit(0); }
$smarty->assign("id",$id);

$gr = new groups;
$group = $gr->getGroup($id);
$smarty->assign("group",$group);

$groups_array = $gr->getAll();
$smarty->assign("groups_array",$groups_array);

$info = '';
if(isset($_POST['Submit'])) {
	require_once "../classes/config/groups_config.php";
	$gr_config = new groups_config;
	if(isset($_POST['action']) && $_POST['action']=="delete") {
		// delete group
		$gr_config->delete($id);
		$info = $lng['groups']['info']['users_deleted'];
	} else { // move

		if(isset($_POST['move_to']) && is_numeric($_POST['move_to']) && $_POST['move_to']) {
			// move to group $_POST['move_to']
			$usr = new users;
			$usr->moveUsers($id, escape($_POST['move_to']));
			$gr_config->delete($id);
			$info = $lng['groups']['info']['users_moved'];
		}
		
	}

}

$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('delete_group.html');
close();
?>
