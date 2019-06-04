<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once $config_abs_path."/classes/badwords.php";

global $db;

$badwords=new badwords();
if(isset($_POST['add_badwords'])) {
	$word=str_replace("\r\n",",",$_POST['badwords']);
	$word=escape($word);
	$badwords->AddBulk($word);
}
if (isset($_POST['delete_badwords'])) {
	for($i=0; $i<count($_POST['badwords_list']); $i++) 
		$badwords->delete($_POST['badwords_list'][$i]);
}

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","security");
$smarty->assign("lng",$lng);

$smarty->assign("array_badwords",$badwords->getAll());

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('forbidden_words.html');
close();
?>
