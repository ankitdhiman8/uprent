<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/fields.php";
require_once "../classes/fieldsets.php";
require_once "../classes/depending_fields.php";
require_once "../classes/config/depending_fields_config.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_custom_fields.php'); exit(0); }
if(isset($_GET['type']) && $_GET['type']=="cf" || $_GET['type']=="uf") $type=$_GET['type']; else $type="cf";
if(isset($_GET['fieldset']) && is_numeric($_GET['fieldset'])) $fieldset=$_GET['fieldset']; else $fieldset = 0;

$current = array();
if(isset($_GET['current1']) && is_numeric($_GET['current1'])) $current[1]=$_GET['current1']; else $current[1] = '';
if(isset($_GET['current2']) && is_numeric($_GET['current2'])) $current[2]=$_GET['current2']; else $current[2] = '';
if(isset($_GET['current3']) && is_numeric($_GET['current3'])) $current[3]=$_GET['current3']; else $current[3] = '';

if($type=="cf") $smarty->assign("tab","settings");
$smarty->assign("lng",$lng);
$smarty->assign("id",$id);
$smarty->assign("type",$type);
$smarty->assign("fieldset",$fieldset);

$smarty->assign("current1",$current[1]);
$smarty->assign("current2",$current[2]);
$smarty->assign("current3",$current[3]);

$fields = new fields($type);
$field_name=$fields->getDependingFieldName($id);
$smarty->assign("field_name",$field_name);

$depending = new depending_fields();
$dep = $depending->getDependingField($id);
$smarty->assign("dep",$dep);

if($type=="cf") {
	$field = new fields('cf');
	$fieldsets = $field->getDepFieldsets($id);
	$smarty->assign("fieldsets",$fieldsets);
	if($fieldsets!=0) $multiple_fsets = 1; else $multiple_fsets = 0;
	$smarty->assign("multiple_fsets",$multiple_fsets);
	
}

$table_no = 1;
if(!$current[1] && !$current[2] && !$current[3]) { // first table

	$table = $dep['table1'];
	if($type=="cf") $dep_array = $depending->getTableStrictLang($table, $fieldset);
	else $dep_array = $depending->getTableLang($table, $fieldset);
	$table_no = 1;
	//_print_r($dep_array);

} else if(!$current[2] && !$current[3]) { // second table

	$table = $dep['table2'];
	$dep_array = $depending->getSecondTableLang($table, $current[1]);
	$table_no = 2;
} else if(!$current[3]) { // third table

	$table = $dep['table3'];
	$dep_array = $depending->getSecondTableLang($table, $current[2]);
	$table_no = 3;

} else { // forth table

	$table = $dep['table4'];
	$dep_array = $depending->getSecondTableLang($table, $current[3]);
	$table_no = 4;

}

$smarty->assign("dep_array",$dep_array);

global $config_demo;
if(isset($_POST['Submit']) && !$config_demo) {

	$depending_config = new depending_fields_config();
	foreach($dep_array as $d) {

		foreach($languages as $lang) {

			$lang_id = $lang['id'];
			if($lang_id==$crt_lang) continue;
			$val = escape($_POST[$d['id']."_".$lang_id]);
			$depending_config->updateDependingLang($d['id'], $lang_id, $table, $val);
		}

	}
	$to='';
	if($type) $to .= "&type=".$type;
	if($fieldset) $to .= "&fieldset=".$fieldset;
	if($current[1]) $to .= "&current1=".$current[1];
	if($current[2]) $to .= "&current2=".$current[2];
	if($current[3]) $to .= "&current3=".$current[3];
	header("Location: translate_depending.php?id=$id".$to);
	exit(0);
}


$array1 = array();
$array2 = array();
$array3 = array();
$array4 = array();

if($type=="cf") $array1 = $depending->getTableStrict($dep['table1'], $fieldset);
else $array1 = $depending->getTable($dep['table1']);

if( isset($current[1]) && $current[1]) $array2 = $depending->getSecondTable($dep['table2'], $current[1]);

if($dep['no'] >=3 && isset($current[2]) && $current[2]) $array3 = $depending->getSecondTable($dep['table3'], $current[2]);

if($dep['no'] ==4 && isset($current[3]) &&$current[3]) $array4 = $depending->getSecondTable($dep['table4'], $current[3]);

$smarty->assign("array1",$array1);
$smarty->assign("array2",$array2);
$smarty->assign("array3",$array3);
$smarty->assign("array4",$array4);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('translate_depending.html');
close();
?>
