<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/banners.php";
require_once "../classes/config/banners_config.php";
require_once "../classes/images.php";
require_once "../classes/categories.php";
require_once "../classes/depending_fields.php";
require_once $config_abs_path."/admin/include/lists.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","banners");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_banners.php'); exit(0); }
$smarty->assign("id",$id);

//banners location module
global $modules_array;
if(in_array("banners_location", $modules_array)) {
	require_once($config_abs_path.'/admin/modules/banners_location/include.php');
	global $lng_banners_location;
	global $field_name;
	global $bl_settings;
	$smarty->assign("lng_banners_location", $lng_banners_location);
	$smarty->assign("field_name", $field_name);

	$cf = new fields("cf");
	$field_type = $cf->getTypeByCaption($bl_settings['location_field']);
	$arr_elements = array("menu", "radio", "radio_group", "multiselect", "checkbox", "checkbox_group");
	if(in_array($field_type, $arr_elements)) {
		$fid = $cf->getIdByCaption($bl_settings['location_field']);
		$bl_elements = $cf->getElementsArray($fid);
		$smarty->assign("bl_menu", 1);
		$smarty->assign("bl_elements", $bl_elements);
	} else if($bl_elements = $cf->getDependingByCaption($bl_settings['location_field'])) {
		$smarty->assign("bl_menu", 1);
		$smarty->assign("bl_elements", $bl_elements);
	}

}

$banners=new banners();
$bc=new banners_config();

$banner = $banners->getBanner($id);

$errors_str='';
if(isset($_POST['Submit'])){
	if(!$bc->edit($id)) { 
		$errors_str=$bc->getError();
		$banner=$bc->getTmp();
	} else { 
		header ('Location: manage_banners.php');
		exit(0);
	}
}

$smarty->assign("banner",$banner);
$smarty->assign("error",$errors_str);

$bc=new banners_config();
$array_banners_categories=$bc->getAllPositions();
$smarty->assign("array_banners_categories",$array_banners_categories);

global $extra_banner_positions;
$smarty->assign("extra_positions", $extra_banner_positions);

global $site_sections;
$smarty->assign("sections", $site_sections);

$array_subcats=array();
$categories=new categories();
$subcats=$categories->getSubcats(0,'',$array_subcats);
$smarty->assign("subcats",$subcats);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_banner.html');
close();
?>
