<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/database.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("lng",$lng);

$info = '';
if(isset($_POST['nyroModal'])) {

	$database = new database();
	if(isset($_POST['compress']) && $_POST['compress']==1) $compress = 1; else $compress = 0;
	$database->setCompress($compress);

	$fname = $database->saveToFile();

	$database->downloadFile($fname);

}

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('download_db.html');
close();
?>
