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
require_once '../classes/packages.php';
require_once '../classes/config/settings_config.php';
require_once '../classes/validator.php';
require_once '../classes/fields.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("smenu", "general");

global $settings;
$errors_str='';
if(isset($_POST['Submit'])){
	$edit_settings=new settings_config;

	require_once '../classes/categories.php';
	require_once '../classes/locations.php';
	require_once '../classes/depending_fields.php';

	if(!$edit_settings->editSettings()) { 
		$errors_str.=$edit_settings->getError();
		$settings=$edit_settings->getTmp();
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 

	$settings_cl=new settings_config(); 
	$settings=$settings_cl->getAllLangSettings(); 
}

$smarty->assign("settings",$settings);
$smarty->assign("error",$errors_str);

$smarty->assign("gmaps",1);

$pkg = new packages;
$plans = $pkg->getAdBasedPlans();
$smarty->assign("plans",$plans);

$fields = array();
$cf = new fields("cf");
$menu_fields = $cf->getFieldsOfType("menu");
$depending_fields = $cf->getDependingFieldsCaptions();
$i = 0;
foreach($menu_fields as $f) $fields[$i++] = $f['caption'];
foreach($depending_fields as $f) $fields[$i++] = $f;

$smarty->assign("fields",$fields);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('settings.html');
close();
?>
