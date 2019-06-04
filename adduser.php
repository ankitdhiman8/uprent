<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/mails.php";
require_once "../classes/mail_templates.php";
require_once "../classes/fields.php";
require_once "../classes/depending_fields.php";
require_once "../classes/images.php";
require_once "../classes/groups.php";
require_once $config_abs_path."/admin/include/lists.php";
require_once "../classes/users.php";
require_once "../classes/validator.php";
require_once "../include/gmaps_util.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['group']) && is_numeric($_GET['group']) && $_GET['group']) $group = $_GET['group']; 
else {

	$group = 0;
	$gr = new groups();
	$no_groups = $gr->getNoActive();
	if($no_groups==1) $group = $gr->noDefGroup();
	else {

		$groups_array = $gr->getAll();
		$smarty->assign("groups_array",$groups_array);

	}
}

if(isset($_POST['Choose_group']) && $_POST['group'] && is_numeric($_POST['group']) && $_POST['group']) {

	$group = $_POST['group'];

}

$smarty->assign("group",$group);

global $default_fields_types;
$smarty->assign("default_fields_types", $default_fields_types);

$info = '';
$error = '';
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
	$tmp=array();
	if(isset($_POST['Submit'])){

		require_once "../classes/fields_process.php";

		$user=new users();
		if(!$user->add($group)) {
			$tmp=$user->getTmp();
			$error=$user->getError();
		}
		else { 
			header("Location: users_list.php");
			exit(0);
		}
	}
	$smarty->assign("tmp",$tmp);

}


$smarty->assign("info",$info);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('adduser.html');
close();
?>
