<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/custom_pages.php";
require_once "../classes/config/custom_pages_config.php";

global $crt_lang;
global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_custom_pages.php'); exit(0); }
if(isset($_GET['lang_id'])) $lang_id=escape($_GET['lang_id']); else $lang_id = $crt_lang;
$smarty->assign("lang_id",$lang_id);

$cp=new custom_pages();
$content = $cp->getContent($id, $lang_id);

$error='';
if(isset($_POST['Submit'])){

	$cp_config=new custom_pages_config();

	if(!$cp_config->edit_content($id)) { 

		$error=$cp_config->getError();

	} else { $content = $cp->getContent($id, $lang_id); }

}

$smarty->assign("content",$content);
$smarty->assign("error",$error);
$smarty->assign("id",$id);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('edit_content.html');
close();
?>
