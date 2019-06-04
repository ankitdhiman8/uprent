<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/paginator.php";
require_once "../classes/messages.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);
$smarty->assign("tab","users");

$array_order_way= array("asc", "desc");
$array_order = array("date");

if(isset($_GET['page']) && is_numeric($_GET['page'])) $page=$_GET['page']; else $page=1;
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) $delete=$_GET['delete'];
if(isset($_GET['no_per_page']) && is_numeric($_GET['no_per_page'])) $no_per_page=$_GET['no_per_page']; else $no_per_page=10;

$order = 'date';
$order_way = 'desc';

$msg = new messages();
$post_array = array();
$array_searches = array("id", "keyword", "username", "name", "email","date_from", "date_to");

foreach($array_searches as $key) {
	if(isset($_GET[$key])) $post_array[$key]=escape($_GET[$key]);
}

if($_POST) {

	global $settings;

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
		if(!preg_match('/^(msg)([0-9])+/',$key)) continue;
		if($value!="on") continue;
		$id = substr($key, 3);
		if(!is_numeric($id)) continue;
		if (isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) $msg->delete($id);
	}

	if ( isset($_POST['delete_selected']) || isset($_POST['delete_selected_x'])) // IE image submit fix

	{
		$location="messages.php?page=".$page;
		foreach($post_array as $key=>$value) {
			if($value)
				$location.="&$key=$value";
		}
		header("Location: ".$location);
		exit(0);
	}
	// end actions for multiple messages
}

$smarty->assign("lng",$lng);
$smarty->assign("page",$page);
$smarty->assign("order",$order);
$smarty->assign("order_way",$order_way);
$smarty->assign("no_per_page",$no_per_page);
$smarty->assign("post_array",$post_array);

$messages_array=$msg->getMessages($post_array, $page, $no_per_page,$order,$order_way);
$no_messages=$msg->getNoMessages();

$smarty->assign("no_messages",$no_messages);
$smarty->assign("messages_array",$messages_array);

// set pages 
$paginator = new paginator($no_per_page);
$paginator->setItemsNo($no_messages);
$paginator->setNoSeo(1);
$paginator->setAdmin(1);
$paginator->setOrderBy($order);
$paginator->setOrderWay($order_way);
$paginator->setExcludeArray(array("Search"));
global $seo_settings;
$paginator->paginate($smarty);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('messages.html');
close();
?>
