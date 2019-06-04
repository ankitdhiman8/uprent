<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/packages.php";
require_once "../classes/categories.php";
require_once "../classes/priorities.php";

global $config_abs_path;
require_once $config_abs_path."/libs/JSON.php";
require_once $config_abs_path."/admin/include/lists.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","listings");
$smarty->assign("lng",$lng);

if(isset($_GET['step']) && is_numeric($_GET['step'])) $step = $_GET['step']; else $step=1;

// do include actions
do_action("newad", $smarty);

if(isset($_GET['id']) && is_numeric($_GET['id'])) { $id = $_GET['id']; $_SESSION['ad_id'] = $id; } else $id='';

global $ads_settings;

function unset_sessions($arr) {

	foreach ($arr as $s) unset($_SESSION["$s"]);

}

function init_sessions($arr) {

	foreach ($arr as $s) $_SESSION["$s"]='';

}

if($ads_settings['enable_featured'] || $ads_settings['enable_highlited'] || $ads_settings['enable_priorities'] || $ads_settings['enable_video']) $extra_options=1; else $extra_options=0;
$smarty->assign("extra_options",$extra_options);

$smarty->assign("id",$id);

// $step uninitialized
// choose category, package and user
if($step==1) {

	require_once "../classes/users.php";

	global $lng;

	if(!isset($_GET['id']) || !$_GET['id']) {
		$sessions_arr = array("package", "category", "ad_id", "user_id", "final");
		unset_sessions($sessions_arr);
		init_sessions($sessions_arr);
	}

	$categ=new categories();
	$categories=$categ->getAllOptions ();
	$smarty->assign("categories", $categories);

	$pkg = new packages();
	$plans_array = $pkg->getAllPlans();
	$smarty->assign("plans_array",$plans_array);

	$usr = new users();
	$no_users = $usr->getNo();
	if($no_users<=100) {
		$users_array = $usr->getAll();
		$smarty->assign("users_array",$users_array);
	}
	$smarty->assign("no_users",$no_users);

	if(isset($_POST['Choose'])) {

	$error='';
	
	if(!isset($_POST['category']) || !$_POST['category']) $error .= $lng['listings']['errors']['category_missing'].'<br>';

	if((!isset($_POST['package']) || !$_POST['package'])) $error .= $lng['listings']['errors']['package_missing'].'<br>';

	//if(!isset($_POST['user_id']) || !$_POST['user_id']) $error .= $lng['listings']['errors']['user_missing'].'<br>';

	if(!$error) {
		if(is_numeric($_POST['category'])) { 
			$_SESSION['category'] = $_POST['category'];
		}
		
		if(is_numeric($_POST['package'])) { 
			$_SESSION['package'] = $_POST['package'];
		}	

		if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) { 
			$_SESSION['user_id'] = escape($_POST['user_id']);
		}
		else {
			$username = escape($_POST['username']);
			$_SESSION['user_id'] = users::getUserId($username);
		}

		if($id) $id_str="&id=$id"; else $id_str='';
		header("Location: newad.php?step=2".$id_str);
		exit(0);
	}
	$smarty->assign("error",$error);

	}



} // end if !$step

else 
// enter ad details
if($step==2) {

	require_once "../classes/fields.php";
	require_once "../classes/depending_fields.php";
	require_once "../classes/validator.php";
	require_once "../include/gmaps_util.php";
	require_once "../classes/users.php";

	global $default_fields_types;
	$smarty->assign("default_fields_types", $default_fields_types);

	if(!isset($_SESSION['category']) || !isset($_SESSION['package']) || !isset($_SESSION['user_id'])) { header("Location: newad.php?step=1"); exit(0); }

	if(isset($_POST['Back'])) {
		if($id) $id_str = "&id=$id"; else $id_str='';
		$back_to=1;
		header("Location: newad.php?step=$back_to".$id_str);
		exit(0);
	}

	$smarty->assign("category", $_SESSION['category']);
	$smarty->assign("package", $_SESSION['package']);
	$smarty->assign("user_id", $_SESSION['user_id']);

	$smarty->assign("no_words", packages::getNoWords($_SESSION['package'])); 

	$categ = new categories();
	$fieldset = $categ->getFieldset($_SESSION['category']);
	$cf=new fields('cf');
	$fields=$cf->getAll($fieldset);
	$smarty->assign("fields", $fields);

	if($ads_settings['enable_price']) {
		$currencies=common::getCurrencies();
		$smarty->assign("currencies", $currencies);
	}

	setGmaps('cf', $fieldset, $smarty);

	if($ads_settings['description_editor']) $htmlarea = 1;
	else $htmlarea = $cf->HTMLAreaFieldExists($fieldset);
	$smarty->assign("htmlarea",$htmlarea);

	$listing=new listings;
	if($id) $tmp = $listing->getListing($id);
	else $tmp = array();
	
	$error='';
	if(isset($_POST['Submit'])) {

		require_once "../classes/listings_process.php";
		require_once "../classes/fields_process.php";
		$lp = new listings_process();

		$errors_str="";

		if(!$id) {

			if(!$lp->add()) {

				$error=$lp->getError();
				$tmp=$lp->getTmp();

			} else { 
				$last=$lp->getLast();
				$_SESSION['ad_id']=$last;
				header('Location: newad.php?step=3');
				exit(0);

			}
		} // add mode
		else 
		// edit before finalize ad
		{
			$lp->setEdit(1);
			if(!$lp->edit($id)) {

				$error=$lp->getError();
				$tmp=$lp->getTmp();

			} else {
				if(isset($_SESSION['final']) && $_SESSION['final']) $back_to=5; else $back_to=3; 
				header("Location: newad.php?id=$id&step=".$back_to);
				exit(0);
			}
		}

	}

	if(packages::getVideo($_SESSION['package'])) $tmp['enable_video'] = 1;

	$smarty->assign("tmp",$tmp);
	$smarty->assign("error",$error);

}

else 
// enter ad photos
if($step==3) {

	require_once "../classes/images.php";

	if(!isset($_SESSION['ad_id'])) { header("Location: newad.php?step=4"); exit(0); }

	if(isset($_POST['Back'])) {
		$id_str = "&id=".$_SESSION['ad_id'];
		$back_to=2;
		header("Location: newad.php?step=$back_to".$id_str);
		exit(0);
	}

	$pkg = new packages();
	$no_photos = $pkg->getNoPictures($_SESSION['package']);
	$smarty->assign("no_photos", $no_photos);

	$pics = new pictures();
	$crt_photos = $pics->getPicturesFormatted($_SESSION['ad_id']);
	$smarty->assign("crt_photos", $crt_photos);

	if(isset($_POST['Add_photos'])) {

		if($id) {
			if(isset($_SESSION['final']) && $_SESSION['final']) $back_to=5; else $back_to=4; 
			header("Location: newad.php?id=$id&step=$back_to");
			exit(0);

		}
		else header('Location: newad.php?step=4'); 
		exit(0);

	}

}

else 
// extra options
if($step==4) {

	global $appearance_settings;

	if(!isset($_SESSION['ad_id']) || !$_SESSION['ad_id']) { header("Location: newad.php?step=2"); exit(0); }
	if(!$ads_settings['enable_featured'] && !$ads_settings['enable_highlited'] && !$ads_settings['enable_priorities'] && !$ads_settings['enable_video'])
	{
		header("Location: newad.php?step=5");
		exit(0);
	}

	if(isset($_POST['Back'])) {
		$id_str = "&id=".$_SESSION['ad_id'];
		$back_to=3;
		header("Location: newad.php?step=$back_to".$id_str);
		exit(0);
	}


	if($_SESSION['package']) {
		$pkg = new packages();
		$pkg_det = $pkg->getPackage($_SESSION['package']);
		$featured = $pkg_det['featured'];
		$highlited = $pkg_det['highlited'];
		$priority = $pkg_det['priority'];
		$video = $pkg_det['video'];
		$plan_name = $pkg_det['name'];

	} else { 
		$featured = 0; $highlited=0; $priority=0; $video = 0;
		$amount = 0;
		$plan_name=0; 
	}

	$smarty->assign("featured",$featured);
	$smarty->assign("highlited",$highlited);
	$smarty->assign("priority",$priority);
	$smarty->assign("video",$video);
	$smarty->assign("plan_name",$plan_name);

	$listing = new listings();

	$options = $listing->getOptions($_SESSION['ad_id']);	
	$smarty->assign("options",$options);

	$pri = new priorities();
	$priorities = $pri->getPriorities();
	$smarty->assign("priorities",$priorities);

	if(isset($_POST['Add_options'])) {

		$listing->editOptions($_SESSION['ad_id']);
		header("Location: newad.php?step=5");
		exit(0);
	}

}

else 
// review
if($step==5) {

	require_once "../classes/fields.php";
	require_once "../classes/depending_fields.php";
	require_once "../classes/users.php";

	global $default_fields_types;
	$smarty->assign("default_fields_types", $default_fields_types);

	$smarty->assign("id",$_SESSION['ad_id']);

	$listing = new listings();
	$listing_array = $listing->getListing($_SESSION['ad_id']);

	if(packages::getVideo($_SESSION['package']))
		$listing_array['enable_video'] = 1;

	$options = $listing->getOptions($_SESSION['ad_id']);
	$smarty->assign("options",$options);

	$cat = new categories();
	$fieldset = $cat->getFieldset($listing_array['category_id']);

	$pri = new priorities();
	$priorities = $pri->getPri();
	$smarty->assign("priorities",$priorities);

	$cf=new fields('cf');
	$fields_array=$cf->getAll($fieldset);
	$smarty->assign("fields_array",$fields_array);

	$gmaps = $cf->gMapsFieldExists($fieldset);
	$smarty->assign("gmaps",$gmaps);

	$smarty->assign("tmp",$listing_array);

}

$smarty->assign("step",$step);
$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('newad.html');
close();
?>
