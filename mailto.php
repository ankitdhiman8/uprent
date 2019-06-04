<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once('../classes/users.php');
require_once('../classes/mails.php');

global $db;
global $lng;
$info='';
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { 
	$id = 0;
	if(isset($_GET['ad_id']) && is_numeric($_GET['ad_id'])) { 
		$ad_id=$_GET['ad_id'];
		$id = listings::getUser($ad_id);
	}
	else exit(0);
}

$users=new users();

if(isset($id) && $id) { 
	$username=$users->getUsername($id);
}
else {
	$listing =new listings;
	$user_details = $listing->getOwnerInfo($ad_id);
	$username = $user_details['mgm_email'];
	$useremail = $user_details['mgm_email'];
	$name = $user_details['mgm_name'];

}

if(isset($_POST['Submit'])) {

	if(isset($id) && $id) { 
		$useremail=$users->getEmail($id);
		$name=$users->getContactName($id);
	}


	$mail2send=new mails();
	$mail2send->init($useremail, $name);

	$mail2send->setSubject(clean($_POST['subject']));
	$msg=nl2br(clean($_POST['content'])).'
';
	$mail2send->setMessage($msg);
	if($mail2send->send()) $info=$lng['mailto']['message_sent'];
	else $info=$lng['mailto']['sending_message_failed'];

}

if(isset($id)) $smarty->assign("id",$id);
if(isset($ad_id)) $smarty->assign("ad_id",$ad_id);
$smarty->assign("username",$username);

$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('mailto.html');
close();
?>
