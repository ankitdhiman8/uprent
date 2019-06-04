<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/templates.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","templates");
$smarty->assign("lng",$lng);

if(isset($_GET['template'])) $crt_template=$_GET['template']; 
else {
	global $appearance_settings;
	$crt_template=$appearance_settings["template"];
}
if(isset($_GET['file'])) $crt_file=escape($_GET['file']);

$error='';
$tpl=new templates();
$tpl->setTemplate($crt_template);
$array_files=$tpl->getFiles("css");
$smarty->assign("array_files",$array_files);

$array_templates=$tpl->getTemplates();
$smarty->assign("array_templates",$array_templates);

if((!isset($crt_file) || !$crt_file) && count($array_files)>0) $crt_file = "style.css";

if($tpl->readTemplateFile($crt_file)) {

	$content=$tpl->getContent();
	$warning=$tpl->isWriteable();
	$smarty->assign("content",$content);
	$smarty->assign("warning",$warning);

} else $error=$tpl->getError();

if(isset($_POST['Save'])) {

	//$content=(get_magic_quotes_gpc ()) ? stripslashes ($_POST['content']) : $_POST['content'];

	// fix for backslashes in css file
	$content=$_POST['content'];
	$content = str_replace('\\\\\\', "::BACKSLASH::", $content);
	$content = str_replace("\'", "'", $content);
	$content = str_replace('\"', '"', $content);
	$content = str_replace("::BACKSLASH::", '\\\\', $content);
	// end fix for backslashes in css file

	if(!$crt_template) $error=$lng['templates']['errors']['invalid_template_file'];

	else {

		$tpl->setTemplate($crt_template);
		$tpl->setFile($crt_file);
		$tpl->setContent($content);
		if(!$tpl->saveTemplate()) $error=$tpl->getError();
		$smarty->assign("content",clean($content));

	}
}

$smarty->assign("crt_file",$crt_file);
$smarty->assign("crt_template",$crt_template);
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('css_editor.html');
close();
?>
