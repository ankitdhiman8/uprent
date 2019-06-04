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
require_once "../classes/packages.php";
require_once "../classes/priorities.php";
require_once "../classes/groups.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

global $appearance_settings;
header('Content-type: text/html; charset='.$appearance_settings['charset']);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else exit(0);
$smarty->assign("id",$id);

$categ=new categories();
$categories=$categ->getAllOptions ();

$pkg=new packages();
$packages=$pkg->getAll();// only the ad only ads

$pri=new priorities();
$priorities=$pri->getAll();

$listings = new listings;
$expires = $listings->getDateExpires($id);

$months_list = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");

$crt_year = date("Y");

$years_list = array();
for ($i=0; $i<10;$i++) 
 $years_list[$i] = $crt_year+$i;

$smarty->assign("months_list",$months_list);
$smarty->assign("years_list",$years_list);

$options = $listings->getOptions($id);

$smarty->assign("categories",$categories);
$smarty->assign("packages",$packages);
$smarty->assign("priorities",$priorities);

$error=''; $info='';
if(isset($_POST['Submit'])) {

	if(isset($_POST['category']) && is_numeric($_POST['category']) && $_POST['category']!=$options['category_id']) {

		$listings->setCategory($id,$_POST['category']);

	}
	if(isset($_POST['package']) && is_numeric($_POST['package']) && $_POST['package']!=$options['package_id']) { 

		$listings->setPackage($id,$_POST['package']);

	}

	if(isset($_POST['priority']) && is_numeric($_POST['priority']) && $_POST['priority']!=$options['priority']) { 

		$listings->setPriority($id,$_POST['priority']);

	}

	$featured = checkbox_value("featured");
	if($featured!=$options['featured']) $listings->setFeatured($id, $featured);

	$highlited = checkbox_value("highlited");
	if($highlited!=$options['highlited']) $listings->setHighlited($id, $highlited);

	$video = checkbox_value("video");
	if($video!=$options['video']) $listings->setVideo($id, $video);

	$listings->changeExpireDate($id);	

	$options = $listings->getOptions($id);
	$expires = $listings->getDateExpires($id);

	$info = $lng['settings']['settings_saved'];
}

$smarty->assign("expires",$expires);
$smarty->assign("options",$options);

$smarty->assign("error",$error);
$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('edit_ad_settings.html');
close();
?>
