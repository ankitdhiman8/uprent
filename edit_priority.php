<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/validator.php";
require_once "../classes/priorities.php";
require_once "../classes/config/priorities_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: extra_visibility_options.php'); exit(0); }
$smarty->assign("id",$id);

$tmp=array();
$pri_config=new priorities_config();

$tmp=$pri_config->getPriorityLang($id);

$error='';
if(isset($_POST['Submit'])){

	if(!$pri_config->edit($id)) { 
		$error=$pri_config->getError();
		$tmp=$pri_config->getTmp();
	} else { header ('Location: extra_visibility_options.php'); exit(0); }

}

$smarty->assign("tmp",$tmp);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('edit_priority.html');
close();
?>
