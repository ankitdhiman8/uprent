<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/categories.php";
require_once "../classes/fields.php";
require_once "../classes/packages.php";
require_once "../classes/images.php";
require_once "../classes/packages.php";
require_once "../classes/depending_fields.php";
require_once "../classes/users.php";
require_once "../classes/validator.php";
require_once "../include/gmaps_util.php";
global $config_abs_path;

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else { header ('Location: manage_listings.php'); exit(0); }

$smarty->assign("lng",$lng);
$smarty->assign("id",$id);

$listing=new listings;

global $default_fields_types;
$smarty->assign("default_fields_types", $default_fields_types);

$listing_array = $listing->getListing($id);
$category_id = $listing_array['category_id'];
$categ = new categories();
$fieldset = $categ->getFieldset($category_id);
$cf=new fields('cf');
$fields=$cf->getAll($fieldset);
$smarty->assign("fields", $fields);

$package = new packages();
$no_words = $package->getNoWords($listing_array['package_id']);
$smarty->assign("no_words", $no_words);

if($ads_settings['enable_price']) {
	$currencies=common::getCurrencies();
	$smarty->assign("currencies", $currencies);
}

setGmaps('cf', $fieldset, $smarty);

if($ads_settings['description_editor']) $htmlarea = 1;
else $htmlarea = $cf->HTMLAreaFieldExists($fieldset);
$smarty->assign("htmlarea",$htmlarea);

$tmp = array();
$error='';
if(isset($_POST['Submit'])) {

	require_once "../classes/listings_process.php";
	require_once "../classes/fields_process.php";
	$lp = new listings_process();
	$lp->setEdit(1);
	$errors_str="";
	if(!$lp->edit($id)) {
		$error=$lp->getError();
		$listing_array=$lp->getTmp();
	} else { 
		header("Location: manage_listings.php");
		exit(0);
	}
}

$smarty->assign("tmp",$listing_array);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('editad.html');
close();
?>
