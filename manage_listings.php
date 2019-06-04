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
require_once "../classes/paginator.php";
require_once "../classes/mails.php";
require_once "../classes/mail_templates.php";
require_once "../classes/blocked_ips.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

$listings=new listings();

$array_order_way= array("asc", "desc");
$array_order= array("date_added", "price", "title", "viewed");
$array_show=array("all", "pending", "pending_featured", "expired", "featured", "highlited", "priorities", "video", "active", "inactive", "sold", "rented");
if(isset($_GET['show']) && in_array($_GET['show'], $array_show)) $show=$_GET['show']; else $show="all";
if(isset($_GET['page']) && $_GET['page'] && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) $delete=$_GET['delete'];
if(isset($_GET['order']) && in_array($_GET['order'], $array_order)) $order=$_GET['order']; else $order='date_added';
if(isset($_GET['order_way']) && in_array($_GET['order_way'],$array_order_way)) $order_way=$_GET['order_way']; else $order_way='desc';
if(isset($_GET['no_per_page']) && is_numeric($_GET['no_per_page'])) $no_per_page=$_GET['no_per_page']; else $no_per_page=10;

$post_array = array();

$array_searches = array("id", "keyword", "username", "email", "ip", "category_id", "package_id", "date_from", "date_to");
foreach($array_searches as $key) {
	if(isset($_GET[$key])) $post_array[$key]=escape($_GET[$key]);
}

if($_POST) {

	array_push($array_searches, "show");
	array_push($array_searches, "order");
	array_push($array_searches, "order_way");
	array_push($array_searches, "no_per_page");

	foreach($array_searches as $key) {
		if(isset($_POST[$key])) $post_array[$key]=escape($_POST[$key]);
	}
	if($post_array['show']) $show = $post_array['show'];
	if($post_array['order']) $order = $post_array['order'];
	if($post_array['order_way']) $order_way = $post_array['order_way'];
	if($post_array['no_per_page']) $no_per_page = $post_array['no_per_page'];

	// actions for multiple ads
	foreach($_POST as $key=>$value) {
		if(!preg_match('/^(ad)([0-9])+/',$key)) continue;
		if($value!="on") continue;
		$id = substr($key, 2);
		if(!is_numeric($id)) continue;
		if (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) $listings->delete($id);
		if (isset($_POST['activate_selected']) || isset($_POST['activate_selected_x'])) $listings->Activate($id);
		if (isset($_POST['deactivate_selected']) || isset($_POST['deactivate_selected_x'])) $listings->Deactivate($id);
	}

	if ( (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) 
	|| ( isset($_POST['activate_selected']) || isset($_POST['activate_selected_x']) ) 
	|| (isset($_POST['deactivate_selected']) || isset($_POST['deactivate_selected_x']))) // IE image submit fix

	{
		$location="manage_listings.php?page=".$page;
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
if($show=="expired") $post_array['expired'] = 1;
if($show=="pending") $post_array['pending'] = 1;
if($show=="featured") $post_array['featured'] = 1;
if($show=="highlited") $post_array['highlited'] = 1;
if($show=="priorities") $post_array['priorities'] = 1;
if($show=="video") $post_array['video'] = 1;
if($show=="sold") $post_array['sold'] = 1;
if($show=="rented") $post_array['rented'] = 1;

$smarty->assign("tab","listings");
$smarty->assign("lng",$lng);
$smarty->assign("show",$show);
$smarty->assign("page",$page);
$smarty->assign("order",$order);
$smarty->assign("order_way",$order_way);
$smarty->assign("no_per_page",$no_per_page);
$smarty->assign("post_array",$post_array);

//_print_r($post_array);

$listings_array=$listings->searchListings($post_array, $page,$no_per_page,$order,$order_way);
$no_listings=$listings->noListings();

$smarty->assign("listings_array",$listings_array);
$smarty->assign("total",$no_listings);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no_listings);
$paginator->setAdmin(1);
$paginator->setNoSeo(1);
$paginator->setOrderBy($order);
$paginator->setOrderWay($order_way);
$paginator->setExcludeArray(array("Search", "no_per_page_sel"));
global $seo_settings;
$paginator->paginate($smarty);

// get categories
$categ = new categories();
$categories = $categ->getAllOptions();
$smarty->assign("categories",$categories);

// get plans
$pkg = new packages;
$plans = $pkg->getAllForm();
$smarty->assign("plans",$plans);


$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('manage_listings.html');
close();
?>
