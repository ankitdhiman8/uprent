<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/languages.php";
require_once "../classes/config/languages_config.php";
require_once "../classes/modules.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['language'])) $crt_lng=escape($_GET['language']);
else {
	global $crt_lang;
	$crt_lng=$crt_lang;
}

if(isset($_GET['mod'])) $crt_mod=escape($_GET['mod']); else $crt_mod = 'main';


$smarty->assign("tab","templates");
$smarty->assign("lng",$lng);

$error='';
$lang=new languages_config();
$array_languages=$lang->getLanguagesFiles();
$smarty->assign("array_languages",$array_languages);

$lang->setLanguage($crt_lng);
if($crt_mod!="main") $lang->setModule($crt_mod);

$mods = modules::getAll();
$i=0;
$modules = array();
foreach($mods as $mod) {
	if(file_exists("../modules/".$mod['id']."/lang")) {
		$modules[$i] = $mod;
		$i++;
	}
}
$smarty->assign("modules",$modules);

if($lang->readLanguageFile()) {

	$content=$lang->getContent();
	$warning=$lang->isWriteable();
	$smarty->assign("content",$content);
	$smarty->assign("warning",$warning);

} else $error=$lang->getError();


if(isset($_POST['Save'])) {

	$content=$_POST['content'];
	$content_strip=(get_magic_quotes_gpc ()) ? stripslashes ($_POST['content']) : $_POST['content'];

	if(!$crt_lng) $error=$lng['languages']['errors']['invalid_language_file'];

	else {

		$lang->setLanguage($crt_lng);
		if($crt_mod!="main") $lang->setModule($crt_mod);
		$lang->setContent($content);
		if(!$lang->saveLanguage()) $error=$lang->getError();
		$smarty->assign("content",$content_strip);

	}

}

$smarty->assign("crt_lng",$crt_lng);
$smarty->assign("crt_mod",$crt_mod);
$smarty->assign("crt_lng_file","lang/".$crt_lng.".php");
$smarty->assign("error",$error);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('languages_editor.html');
close();
?>
