<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
my_session_start();
global $db;

$auth=new auth();
$auth->admin_clearlogin();
header("Location: ../index.php"); 
exit(0);

?>
