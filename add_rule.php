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
require_once "../classes/groups.php";
require_once "../classes/users.php";
require_once "../classes/depending_fields.php";
require_once $config_abs_path."/admin/include/lists.php";
require_once "../classes/rules.php";

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("lng",$lng);

$banner=array();
$errors_str='';
if(isset($_POST['Submit'])){
	$rules=new rules();
	if(!$rules->add()) { 
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
//_print_r($array_fields);
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
