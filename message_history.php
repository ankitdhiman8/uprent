<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
global $config_abs_path;
require_once $config_abs_path."/classes/messages.php";

if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']) $id=$_GET['id']; else exit(0);

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);
$smarty->assign("tab","users");

$post_array = array("history" => $id);

$msg = new messages;
$messages_array = $msg->getMessages($post_array, 0, 0,'date','asc');

$smarty->assign("messages_array", $messages_array);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('message_history.html');
close();
?>
