<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/searches.php";
require_once "../classes/paginator.php";
require_once "../classes/alerts.php";
require_once "../classes/fields.php";
require_once "../classes/categories.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

$alerts=new alerts();

if(isset($_GET['page']) && $_GET['page'] && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) $delete=$_GET['delete'];
if(isset($_GET['no_per_page']) && is_numeric($_GET['no_per_page'])) $no_per_page=$_GET['no_per_page']; else $no_per_page=20;

$array_show=array("all", "active", "inactive");
if(isset($_GET['show']) && in_array($_GET['show'], $array_show)) $show=$_GET['show']; else $show="all";

$post_array = array();

$array_searches = array("id", "username", "email", "ip", "frequency", "date_from", "date_to");
foreach($array_searches as $key) {
	if(isset($_GET[$key])) $post_array[$key]=escape($_GET[$key]);
}

if($_POST) {

	array_push($array_searches, "no_per_page");
	array_push($array_searches, "show");

	foreach($array_searches as $key) {
		if(isset($_POST[$key])) $post_array[$key]=escape($_POST[$key]);
	}
	if($post_array['no_per_page']) $no_per_page = $post_array['no_per_page'];
	if($post_array['show']) $show = $post_array['show'];

	// actions for multiple ads
	foreach($_POST as $key=>$value) {
		if(!preg_match('/^(sr)([0-9])+/',$key)) continue;
		if($value!="on") continue;
		$id = substr($key, 2);
		if(!is_numeric($id)) continue;
		if (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) $alerts->delete($id);
		if (isset($_POST['activate_selected']) || isset($_POST['activate_selected_x'])) $alerts->Enable($id);
		if (isset($_POST['deactivate_selected']) || isset($_POST['deactivate_selected_x'])) $alerts->Disable($id);


	}

	if ( (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) 
	|| ( isset($_POST['activate_selected']) || isset($_POST['activate_selected_x']) ) 
	|| (isset($_POST['deactivate_selected']) || isset($_POST['deactivate_selected_x']))) // IE image submit fix
	{
		$location="manage_email_alerts.php?page=".$page;
		foreach($post_array as $key=>$value) {
			if($value)
				$location.="&$key=$value";
		}
		header("Location: ".$location);
		exit(0);
	}
	// end actions for multiple ads
}

if($show=="active") $post_array['active'] = 1;
if($show=="inactive") $post_array['inactive'] = 1;

$smarty->assign("tab","users");
$smarty->assign("lng",$lng);
$smarty->assign("page",$page);
$smarty->assign("show",$show);
$smarty->assign("no_per_page",$no_per_page);
$smarty->assign("post_array",$post_array);

$order = "date";
$order_way = "desc";

$alerts_array=$alerts->search($post_array, $page, $no_per_page, $order, $order_way);
$no_alerts=$alerts->noAlerts();

$smarty->assign("alerts_array",$alerts_array);
$smarty->assign("total",$no_alerts);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no_alerts);
$paginator->setAdmin(1);
$paginator->setNoSeo(1);
$paginator->setOrderBy($order);
$paginator->setOrderWay($order_way);
$paginator->setExcludeArray(array("Search", "no_per_page_sel"));
global $seo_settings;
$paginator->paginate($smarty);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('manage_email_alerts.html');
close();
?>
