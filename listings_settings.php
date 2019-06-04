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
$smarty->assign("smenu", "listings");

if(isset($_GET['delete']) && $_GET['delete']=="watermark") { 
	$sc=new settings_config;
	$sc->deleteWatermark();
	header("Location: listings_settings.php");
	exit(0);

}

$errors_str='';
$priorities_errors_str='';
if(isset($_POST['Submit'])){
	$sc=new settings_config;
	if(!$sc->editAdsSettings()) { 
		$errors_str.=$sc->getError();
		$ads_settings=$sc->getTmp();
	}
} 

if(!isset($_POST['Submit']) || $errors_str=='') { 
	$settings=new settings; 
	$ads_settings=$settings->getAdsSettings();
}

$smarty->assign("ads_settings",$ads_settings);


$smarty->assign("error",$errors_str);

$html_tags = array("a","b","blockquote","br","caption","center","cite","code","div","em","font","frame","h1","h2","h3","h4","h5","h6","hr","i","img","label","li","object","ol","p","pre","script","span","strong","style","table","tbody","td","th","tr","u","ul");
$smarty->assign("html_tags",$html_tags);

$fields = $db->getTextTableFields(TABLE_ADS);
$smarty->assign("fields",$fields);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('listings_settings.html');
close();
?>
