<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/images.php';
require_once '../classes/config/settings_config.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "appearance");

global $array_charsets;
$smarty->assign("array_charsets", $array_charsets);

if(isset($_GET['delete']) && $_GET['delete']=="header_pic") { 
	$sc=new settings_config;
	$sc->deleteHeaderPic();
	header("Location: appearance.php");
	exit(0);

}

if(isset($_GET['delete']) && $_GET['delete']=="footer_pic") { 
	$sc=new settings_config;
	$sc->deleteFooterPic();
	header("Location: appearance.php");
	exit(0);

}

$errors_str='';
if(isset($_POST['Submit'])){

	$sc=new settings_config;
	if(!$sc->editAppearance()) { 
		$errors_str.=$sc->getError();
		$appearance_array=$sc->getTmp();
	}
	 else {
		header("Location: appearance.php");
		exit(0);
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 
	$sc = new settings_config;
	$appearance_array=$sc->getAllLangAppearance();
}
$smarty->assign("appearance_array",$appearance_array);

$templates_dir = dir("../templates");
$i=0;
global $config_abs_path;
while ($folder = $templates_dir->read()) {
	if($folder != '..' && $folder != '.' && is_dir($config_abs_path."/templates/".$folder)) { $templates[$i]= $folder; $i++; }
} closedir($templates_dir->handle);

$smarty->assign("templates",$templates);

$admin_templates_dir = dir("templates");
$i=0;
while ($folder = $admin_templates_dir->read()) {
	if($folder != '..' && $folder != '.') { $admin_templates[$i]= $folder; $i++; }
} closedir($admin_templates_dir->handle);

$smarty->assign("admin_templates",$admin_templates);

$lang_dir = dir("lang");
$i=0;
while ($l = $lang_dir->read()) {
	if($l != '..' && $l != '.' && getExtension($l)=="php") { $admin_languages[$i]= stripExtension($l); $i++; }
} closedir($lang_dir->handle);

$smarty->assign("admin_languages",$admin_languages);
$smarty->assign("error",$errors_str);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('appearance.html');
close();
?>
