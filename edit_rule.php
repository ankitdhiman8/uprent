<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "../classes/categories.php";
require_once "../classes/fieldsets.php";
require_once "../classes/fields.php";
require_once "../classes/depending_fields.php";
require_once $config_abs_path."/admin/include/lists.php";
require_once "../classes/rules.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);

if(isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id']; else { header ('Location: manage_banners.php'); exit(0); }
$smarty->assign("id",$id);

$rules=new rules();
$rule = $rules->getRule($id);

$errors_str='';
if(isset($_POST['Submit'])){
	$rules=new rules();
	if(!$rules->edit($id)) { 
		$errors_str=$rules->getError();
		$rule=$rules->getTmp();
	} else {
		header ('Location: manage_rules.php');
		exit(0);
	}
}

$smarty->assign("rule",$rule);
$smarty->assign("error",$errors_str);

$fields=new fields('cf');
$array_fields=$fields->getIndividualFields();
$smarty->assign("fields",$array_fields);


$array_subcats=array();
$cats=new categories();
$categories=$cats->getSubcats(0,'',$array_subcats);
$smarty->assign("categories",$categories);


$gr=new groups();
$groups=$gr->getAll();
$smarty->assign("groups",$groups);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('add_rule.html');
close();
?>
