<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/config/sitemap_config.php';
require_once '../classes/categories.php';
require_once '../classes/custom_pages.php';

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$sitemap = new sitemap();

$smarty->assign("sitemap_link",$sitemap->getLink());
if(isset($_POST['Submit'])){

	$sitemap->edit();

} 

$error='';
$info='';
if(isset($_POST['Generate'])){

	if(!$sitemap->writeSitemap()) $error = $sitemap->getError();
	else $info = $lng['sitemap']['sitemap_generated'];

}

$freq = array ("always" => $lng['database']['always'], "hourly" => $lng['database']['hourly'], "daily" => $lng['database']['daily'], "weekly" =>$lng['database']['weekly'], "monthly" => $lng['database']['monthly'], "yearly" => $lng['database']['yearly'], "never" => $lng['database']['never']);

$auto_freq = array ("daily" => $lng['database']['daily'], "weekly"=>$lng['database']['weekly'], "monthly" => $lng['database']['monthly']);

$sitemap_settings = $sitemap->getAll();

$smarty->assign("sitemap_settings",$sitemap_settings);
$smarty->assign("error",$error);
$smarty->assign("info",$info);
$smarty->assign("freq",$freq);
$smarty->assign("auto_freq",$auto_freq);

$smarty->display('sitemap.html');
close();
?>
