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
require_once '../classes/modules.php';

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","modules");
$smarty->assign("lng",$lng);

global $config_abs_path;
$modules_folder = $config_abs_path."/admin/modules";
$folders_h = dir($modules_folder);

$array_modules=array();
$i = 0;
while ($folder = $folders_h->read()) {

	if($folder!="." && $folder!="..") { $array_modules[$i]['id'] = $folder; $i++; }

} closedir($folders_h->handle);

$mod = new modules;
$i = 0;
foreach ($array_modules as $module) {

	$array_modules[$i] = $mod->getModule($module['id']);
	if(!$array_modules[$i]) { 
		if(!file_exists($modules_folder.'/'.$module['id'].'/install.php')) continue;
		$array_modules[$i] = $mod->xml_parse_file($modules_folder.'/'.$module['id'].'/install.php');
		$array_modules[$i]['installed'] = 0;
	} else 
		$array_modules[$i]['installed'] = 1;
	$i++;

}

$no_modules = count($array_modules);

$smarty->assign("array_modules",$array_modules);
$smarty->assign("no_modules",$no_modules);


$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('modules.html');
close();
?>
