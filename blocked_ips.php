<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/blocked_ips.php";

global $db;

$blocked_ips=new blocked_ips();

if(isset($_POST['add'])) {
	$word=str_replace("\r\n",",",$_POST['blocked_ips']);
	$word=escape($word);
	$blocked_ips->AddBulk($word);
}
if (isset($_POST['delete'])) {

	for($i=0; $i<count($_POST['ips_list']); $i++)
		$blocked_ips->delete($_POST['ips_list'][$i]);
}

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","security");
$smarty->assign("lng",$lng);

$smarty->assign("array_blocked_ips",$blocked_ips->getAll());

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('blocked_ips.html');
close();
?>
