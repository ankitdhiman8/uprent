<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/blocked_emails.php";

global $db;

$blocked_emails=new blocked_emails();

if(isset($_POST['add'])) {
	$word=str_replace("\r\n",",",$_POST['blocked_emails']);
	$word=escape($word);
	$blocked_emails->AddBulk($word);
}
if (isset($_POST['delete'])) {

	for($i=0; $i<count($_POST['emails_list']); $i++)
		$blocked_emails->delete($_POST['emails_list'][$i]);
}

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","security");
$smarty->assign("lng",$lng);

$smarty->assign("array_blocked_emails",$blocked_emails->getAll());

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('blocked_emails.html');
close();
?>
