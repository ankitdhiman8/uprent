<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once "include/lists.php";

global $config_abs_path;
require_once($config_abs_path."/classes/payment.php");
require_once($config_abs_path."/classes/payment_processors.php");

if(isset($_GET['processor']) && $_GET['processor']) $processor = escape($_GET['processor']); else $processor = '';

if(!$processor) {
	$pp_list = payment_processors::getAllPPList();

	foreach($pp_list as $pp) { 
		require_once($config_abs_path.'/classes/payment/'.$pp.'.php');
	}
}
else {
	$class_name = payment_processors::getPaymentProcessorClass($processor);
	require_once($config_abs_path.'/classes/payment/'.$class_name.'.php');
}	

global $db;
global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","settings");
$smarty->assign("smenu","payment");
$smarty->assign("lng",$lng);

if(!$processor || $processor=="paypal") {
	global $paypal_currencies;
	$smarty->assign("paypal_currencies",$paypal_currencies);
}

if(!$processor || $processor=="mb") {
	global $mb_currencies;
	$smarty->assign("mb_currencies",$mb_currencies);

	global $mb_languages;
	$smarty->assign("mb_languages",$mb_languages);
}

if(!$processor) {

	$pp = new payment_processors;
	$processors = payment_processors::getAll();
	$array_processors = array();
	$i=0;
	$other_paypal_currency = 0;
	foreach($processors as $proc) {

		$array_processors[$i] = $proc;
		$array_processors[$i]['settings'] = $pp->getSettings($proc['processor_code']);
		$i++;
	}
	$smarty->assign("array_processors",$array_processors);
	$smarty->assign("other_paypal_currency",$other_paypal_currency);

} else {

	$pp = new payment_processors;
	$processor_settings = $pp->getSettings($processor);
	$other_paypal_currency = 0;
	if($processor=="paypal") {
		if(!in_array($processor_settings['paypal_currency'], $paypal_currencies)) $other_paypal_currency = 1;
	}
	$smarty->assign("processor_settings",$processor_settings);
	$smarty->assign("other_paypal_currency",$other_paypal_currency);

}

$error='';
$info='';

if($processor && isset($_POST['Submit_'.$processor])){
	if(!$pp->saveSettings($processor)) { 
		$error = $pp->getErrors();
		$tmp = $pp->getTmp();
	}
	else { 
		$processor_settings = $pp->getSettings($processor);
		$other_paypal_currency = 0;
		if($processor=="paypal") {
			if(!in_array($processor_settings['paypal_currency'], $paypal_currencies)) $other_paypal_currency = 1;
		}
		$info = $lng['settings']['settings_saved'];
		$smarty->assign("processor_settings",$processor_settings);
		$smarty->assign("other_paypal_currency",$other_paypal_currency);

	}
}

if($processor && isset($_POST['Add'])) {
	$pr = new $class_name;
	$pr->addProduct();
	header("Location: payment_settings.php?processor=".$processor);
	exit(0);
}

$smarty->assign("processor",$processor);
$processor_full = $pp->getPaymentProcessor($processor);
$smarty->assign("processor_name",$processor_full['processor_name']);
$smarty->assign("error",$error);
$smarty->assign("info",$info);

$db->close();
if($db->error!='') { $db_error = $db->getError(); $smarty->assign('db_error',$db_error); }

$smarty->display('payment_settings.html');
close();

?>