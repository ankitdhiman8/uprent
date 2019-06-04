<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/images.php';

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$info='';
$error='';

if(isset($_POST['Remove'])) {

	$pics = new pictures;
	$pics->RemoveUnused();
	header("Location: images_tools.php");
	exit(0);
}


$smarty->assign("info",$info);
$smarty->assign("error",$error);

$smarty->display('images_tools.html');
close();
?>
