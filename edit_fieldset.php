<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/fieldsets.php";
require_once "../classes/config/fieldsets_config.php";
require_once "../classes/images.php";
require_once "../classes/categories.php";

global $db;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_fieldsets.php'); exit(0); }

global $lng;
$smarty->assign("lng",$lng);
$smarty->assign("tab","settings");
$smarty->assign("id",$id);

$fields=array();
$f=new fieldsets();
$f_config=new fieldsets_config();
$tmp=$f->getFieldset($id);

if(isset($_POST['Submit'])){

	if(!$f_config->edit($id)) { 
		$errors_str=$f_config->getError();
		$tmp=$f_config->getTmp();
		$smarty->assign("error",$errors_str);
	} else { 
		header ('Location: manage_fieldsets.php');
		exit(0);
	}
}

$smarty->assign("tmp",$tmp);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_fieldset.html');
close();
?>
