<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/payment_actions.php";
require_once "../classes/actions.php";
require_once "../classes/priorities.php";
require_once "../classes/users_packages.php";
require_once "../classes/users.php";
require_once "../classes/fields.php";
require_once "../classes/depending_fields.php";
require_once "../classes/packages.php";
require_once "../classes/categories.php";
require_once "../classes/mails.php";
require_once "../classes/mail_templates.php";

//area search module
global $modules_array;
if(in_array("areasearch", $modules_array)) {
	require_once($config_abs_path.'/modules/areasearch/include.php');
}

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

$type_array = array("ad", "user", "sub", "invoice");
if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id']; else exit(0);
if(isset($_GET['type']) && in_array($_GET['type'], $type_array)) $type = $_GET['type']; else exit(0);

$smarty->assign("lng",$lng);
$smarty->assign("id",$id);
$smarty->assign("type",$type);

$actions = new actions;
$action = $actions->getInvoiceActions($id, $type);

// if show actions for listings get also pending packages
if($type=='ad') {
	$usr_pkg = listings::getUserPackage($id);
	// if package is subscription
	$pkg_type = users_packages::getPackageType($usr_pkg);
	if($pkg_type=="sub") {
		$action_sub = $actions->getInvoiceActions($usr_pkg, 'sub');
		$i = count($action);
		foreach ($action_sub as $a) {
			$action[$i] = $a;
			$i++;
		}
	}
}

$info = '';
if(isset($_POST['Apply'])) {

	$finish_upgrade = 0;
	$newad = 0;
	$featured=0; $highlited=0; $video=0; $priority=0;

	foreach ($action as $a) {

	// skip not pending actions
	if(!$a['pending']) continue;
	if($_POST['type']=="accept") {

	if(isset($_POST['complete_payment'.$a['invoice']]) && $_POST['complete_payment'.$a['invoice']]=="on") {
		$pa = new payment_actions;
		$pa->ActivateInvoice($a['invoice']);
	}

	// options first , when sending mails the options will be already activated 
	if( ( $a['type'] == "featured" ) && isset($_POST['featured'.$a['object_id']]) && $_POST['featured'.$a['object_id']]=="on") {
		$listings = new listings;
		$listings->makeFeatured($a['object_id']);
		$finish_upgrade = 1;
		$user_id = $a['user_id'];
		$ad_id = $a['object_id'];
		$featured=1;
	}

	if( ( $a['type'] == "highlited" ) && isset($_POST['highlited'.$a['object_id']]) && $_POST['highlited'.$a['object_id']]=="on") {
		$listings = new listings;
		$listings->makeHighlited($a['object_id']);
		$finish_upgrade = 1;
		$user_id = $a['user_id'];
		$ad_id = $a['object_id'];
		$highlited=1;
	}

	if( ( $a['type'] == "priority" ) && isset($_POST['priority'.$a['object_id']]) && $_POST['priority'.$a['object_id']]=="on") {
		$listings = new listings;
		$listings->enablePriority($a['object_id'], $a['priority_id']);
		$finish_upgrade = 1;
		$user_id = $a['user_id'];
		$ad_id = $a['object_id'];
		$priority=priorities::getNameByOrder($a['priority_id']);
	}

	if( ( $a['type'] == "video" ) && isset($_POST['video'.$a['object_id']]) && $_POST['video'.$a['object_id']]=="on") {
		$listings = new listings;
		$listings->enableVideo($a['object_id']);
		$finish_upgrade = 1;
		$user_id = $a['user_id'];
		$ad_id = $a['object_id'];
		$video = 1;
	}

	if( ( $a['type'] == "newad" || $a['type'] == "renewad" ) && isset($_POST['ad'.$a['object_id']]) && $_POST['ad'.$a['object_id']]=="on") { 
		$listings = new listings;
		$listings->ActivatePending($a['object_id']);
		$newad = 1;
	}
	if( ( $a['type'] == "newpkg" || $a['type'] == "renewpkg" ) && isset($_POST['pkg'.$a['object_id']]) && $_POST['pkg'.$a['object_id']]=="on") {
		$pkg = new users_packages;
		$pkg->ActivatePending($a['object_id']);
	}

	if( ( $a['type'] == "store" ) && isset($_POST['store'.$a['object_id']]) && $_POST['store'.$a['object_id']]=="on") {
		$usr = new users;
		$usr->enablePendingStore($a['object_id']);
	}

	} else { 
	// reject !

		$act = new actions;
		$act->removePending($a['id']);

		if( ( $a['type'] == "newad" || $a['type'] == "renewad" ) && isset($_POST['ad'.$a['object_id']]) && $_POST['ad'.$a['object_id']]=="on") { 
			$listings = new listings;
			$listings->delete($a['object_id']);
		}
		if( ( $a['type'] == "newpkg" || $a['type'] == "renewpkg" ) && isset($_POST['pkg'.$a['object_id']]) && $_POST['pkg'.$a['object_id']]=="on") {
			$pkg = new users_packages;
			$pkg->delete($a['object_id']);
		}

	}
	} // end foreach 

	if($_POST['type']=="accept") $info = $lng['listings']['invoice_actions_updated'];
	else $info = $lng['listings']['invoice_actions_rejected'];

	$action = $actions->getInvoiceActions($id, $type);

	// send mail to announce upgrade status
	if($finish_upgrade && !$newad) {

		global $config_abs_path;
		require_once $config_abs_path."/classes/users.php";
		$user_details = listings::getUserDetails($ad_id, $user_id);
		$title_arr = listings::getTitle($ad_id, 1);
		$key=''; if($user_details['key']) $key=$user_details['key'];
		$details_link = listings::makeDetailsLink($ad_id, $key);

		// send email
		$mail2send=new mails();
		$mail2send->init($user_details['email'], $user_details['name']);

		$array_subject = array();

		$array_message = array("username"=>$user_details['username'], "contact_name"=> $user_details['name'], "ad_id" => $ad_id, "details_link" => $details_link, "featured" => $featured, "highlited" => $highlited, "priority" => $priority, "video" => $video);

		$mail2send->composeAndSend("ad_options_upgrade_done", $array_message, $array_subject);

	} // end if($finish_upgrade && !$newad)

}

$smarty->assign("action",$action);
$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('selective_invoice_accept.html');
close();
?>
