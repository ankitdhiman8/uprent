<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/info.php";

global $db;

$info_templates=new info();
if(isset($_GET['template'])) {
	$template_code = escape($_GET['template']);
	$crt_template=$info_templates->getRegLang($template_code);
}
else { 
	$template_code=$info_templates->getFirst();
	$crt_template = $info_templates->getRegLang($template_code);
}

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","templates");
$smarty->assign("lng",$lng);

$array_templates=$info_templates->getAll();
$smarty->assign("array_templates",$array_templates);

if(isset($_POST['Save'])) {

	$info_templates->edit($template_code);
	$crt_template=$info_templates->getRegLang($template_code);

}

$smarty->assign("crt_template",$crt_template);
$smarty->assign("template_code",$template_code);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('info_templates.html');
close();
?>
