<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once $config_abs_path."/classes/languages.php";
require_once $config_abs_path."/classes/config/languages_config.php";
require_once $config_abs_path."/classes/images.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id'])) $id = escape($_GET['id']); else { header ('Location: languages.php'); exit(0); }

$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("id",$id);

global $array_maps;
$smarty->assign("array_maps",$array_maps);

$lang = new languages();
$language = $lang->getLanguage($id);

if(isset($_GET['delete']) && $_GET['delete']=="image") { 

	$lang->deleteImage($id);
	header("Location: edit_language.php?id=".$id);
	exit(0);

}

$error='';
if(isset($_POST['Submit'])) {

	$lang_config = new languages_config();
	if(!$lang_config->edit($id)) {
		$language = $lang_config->getTmp();
		$error = $lang_config->getError();
	} else { 
		header("Location: languages.php");
		exit(0);
	}
}
$smarty->assign("language",$language);

$smarty->assign("error",$error);
$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_language.html');
?>
