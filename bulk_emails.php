<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/mails.php";
require_once "../classes/groups.php";
require_once "../classes/users.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);
$smarty->assign("tab","users");

$gr = new groups();
$groups = $gr->getAll();
$smarty->assign("groups",$groups);

global $mail_settings;
$mail_settings = settings::getMailSettings();
$smarty->assign("mail_settings",$mail_settings);

$info = '';
$error = '';
$tmp=array();
if(isset($_POST['Submit'])){

	$group = escape($_POST['group']);
	if($group==-1) $nologin = 1;
	$send_to = $_POST['send_to'];

	if(!$nologin) {

		$where = " where 1 = 1 ";
		if($group) $where .= " and `group` = '$group'";

		$group_str = " group by ".TABLE_USERS.".id";

		if($send_to=="all") {

			$join = " LEFT JOIN ".TABLE_ADS." on ".TABLE_USERS.".id = ".TABLE_ADS.".user_id ";
		}
		else
		if($send_to=="active_users") {

			$where .= " and ".TABLE_USERS.".`active` = 1 ";
			$join = " LEFT JOIN ".TABLE_ADS." on ".TABLE_USERS.".id = ".TABLE_ADS.".user_id ";
	
		}
		else
		if($send_to=="active_ads") {

		
			$join = " LEFT JOIN ".TABLE_ADS." on ".TABLE_USERS.".id = ".TABLE_ADS.".user_id";

		}
		else
		if($send_to=="active_for_sale") {

			$join = " LEFT JOIN ".TABLE_ADS." on ".TABLE_USERS.".id = ".TABLE_ADS.".user_id ";

		}
		else
			if($send_to=="active_for_rent") {

			$join = " LEFT JOIN ".TABLE_ADS." on ".TABLE_USERS.".id = ".TABLE_ADS.".user_id ";

		}

    		$usr = new users();
    		$sql = "select ".TABLE_USERS.".*, count(".TABLE_ADS.".user_id) as listings from ".TABLE_USERS.$join.$where.$group_str;
	} // end not nologin
	else {

		if($send_to=="active_ads") {

			$where .= " where ".TABLE_ADS.".`active` = 1 ";
	
		}
		else
		if($send_to=="active_for_sale") {

			$where .= " where ".TABLE_ADS.".`active` = 1 and sold = 0 ";

		}
		else
		if($send_to=="active_for_rent") {

			$where .= " where ".TABLE_ADS.".`active` = 1 and rented = 0 ";

		}
		
		$sql = "select ".TABLE_ADS_EXTENSION.".* from ".TABLE_ADS_EXTENSION." left join ".TABLE_ADS." on ".TABLE_ADS_EXTENSION.".id = ".TABLE_ADS.".id ".$where." group by ".TABLE_ADS_EXTENSION.".mgm_email";
	}
//    echo $sql;

    $users_array = $db->fetchAssocList($sql);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);

    foreach($users_array as $user) {

	if(!$nologin) {
	if($send_to=="active_ads") {
	    $no_ads = $db->fetchRow("select count(id) from ".TABLE_ADS." where user_id=".$user['id']." and `active`=1");
	    if(!$no_ads) continue;
	}

	if($send_to=="active_for_sale") {
	    $no_ads = $db->fetchRow("select count(id) from ".TABLE_ADS." where user_id=".$user['id']." and `active`=1 and sold=0");
	    if(!$no_ads) continue;
	}

	if($send_to=="active_for_rent") {
	    $no_ads = $db->fetchRow("select count(id) from ".TABLE_ADS." where user_id=".$user['id']." and `active`=1 and rented=0");
	    if(!$no_ads) continue;
	}
	}

	if(!$nologin) {
		$email = $user['email'];
		$contact_name = $user['contact_name'];
	} else {
		$email = $user['mgm_email'];
		$contact_name = $user['mgm_name'];
	}
	$mail = new mails();
	$mail->init($email, $contact_name);
	$mail->setSubject($subject);
	$mail->setMessage($message);
	$mail->send();

    }

    $info = $lng['bulk_emails']['mail_sent'];

}

$smarty->assign("info",$info);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('bulk_emails.html');
close();
?>
