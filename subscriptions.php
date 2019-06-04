<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/users_packages.php";
require_once "../classes/packages.php";
require_once "../classes/paginator.php";

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

global $db;
$usr_pkg=new users_packages();

$arr_search= array("username", "package_name");
$arr_order= array("username", "package_name", "ads_left", "date_start", "date_end");
$arr_order_way= array("asc", "desc");

if(isset($_GET['page']) && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
if(isset($_GET['no_per_page']) && is_numeric($_GET['no_per_page'])) $no_per_page=$_GET['no_per_page']; else $no_per_page=20;

if(isset($_GET['search'])) $search=escape($_GET['search']); else $search='';

if(isset($_GET['search_for']) && in_array($_GET['search_for'], $arr_search)) $search_for=$_GET['search_for']; else $search_for='';

if(isset($_GET['order']) && in_array($_GET['order'], $arr_order)) $order=$_GET['order']; else $order='date_start';

if(isset($_GET['order_way']) && in_array($_GET['order_way'], $arr_order_way)) { 
	$order_way=$_GET['order_way']; 
}
else if($order) $order_way='desc';



$post_array = array();

$array_searches = array("id", "username", "email", "package_id", "date_from", "date_to");
foreach($array_searches as $key) {
	if(isset($_GET[$key])) $post_array[$key]=escape($_GET[$key]);
}

if($_POST) {

	array_push($array_searches, "order");
	array_push($array_searches, "order_way");
	array_push($array_searches, "no_per_page");

	foreach($array_searches as $key) {
		if(isset($_POST[$key])) $post_array[$key]=escape($_POST[$key]);
	}
	if($post_array['order']) $order = $post_array['order'];
	if($post_array['order_way']) $order_way = $post_array['order_way'];
	if($post_array['no_per_page']) $no_per_page = $post_array['no_per_page'];

	// actions for multiple ads
	foreach($_POST as $key=>$value) {
		if(!preg_match('/^(usr_pkg)([0-9])+/',$key)) continue;
		if($value!="on") continue;
		$id = substr($key, 2);
		if(!is_numeric($id)) continue;
		if (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) $usr_pkg->delete($id);
	}

	if ( isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) // IE image submit fix

	{
		$location="subscriptions.php?page=".$page;
		foreach($post_array as $key=>$value) {
			if($value)
				$location.="&$key=$value";
		}
		header("Location: ".$location);
		exit(0);
	}
	// end actions for multiple subscriptions
}

$smarty->assign("tab","users");
$smarty->assign("lng",$lng);
$smarty->assign("page",$page);
$smarty->assign("order",$order);
$smarty->assign("order_way",$order_way);
$smarty->assign("no_per_page",$no_per_page);
$smarty->assign("post_array",$post_array);

$array_usr_pkg=$usr_pkg->searchSubscriptions($post_array,$page,$no_per_page,$order,$order_way);
$no_subscriptions=$usr_pkg->getNoSubscriptions();

$smarty->assign("array_usr_pkg",$array_usr_pkg);
$smarty->assign("total",$no_subscriptions);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no_subscriptions);
$paginator->setAdmin(1);
$paginator->setNoSeo(1);
$paginator->setOrderBy($order);
$paginator->setOrderWay($order_way);
$paginator->setExcludeArray(array("Search", "no_per_page_sel"));
global $seo_settings;
$paginator->paginate($smarty);

// get plans
$pkg = new packages;
$plans = $pkg->getSubscriptionBasedPlans();
$smarty->assign("plans",$plans);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('subscriptions.html');
close();
?>
