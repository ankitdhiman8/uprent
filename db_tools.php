<?php
/*
	*	
	* OxyClassifieds.com : PHP Classifieds (http://www.oxyclassifieds.com)
	* version 7.0
	* (c) 2011 OxyClassifieds.com (office@oxyclassifieds.com).
	*
*/
require_once "include/include.php";
require_once '../classes/database.php';

global $lng;
$smarty = new Smarty;
$smarty = common($smarty);
$smarty->assign("tab","tools");
$smarty->assign("lng",$lng);

$info='';
$error='';

$database = new database();
$backups_array = $database->readBackups();
$smarty->assign("backups_array",$backups_array);
$smarty->assign("no_backups",count($backups_array));

if(isset($_GET['download'])) {
	$download = escape($_GET['download']); 
	$database->downloadFile($download);
}
global $config_live_site;
if(isset($_GET['restore'])) {
	$restore = escape($_GET['restore']);
	$path = $config_abs_path.'/db_backup/';
	if($database->restoreDB($path.$restore)) {
		$info = $lng['database']['backup_restored'];
	?>
<script type="text/javascript">
	alert("<?php echo $info ?>");
	window.location = "<?php echo $config_live_site ?>/admin/db_tools.php"
</script>
<?php
	}
	else $error = $lng['database']['could_not_restore_backup'];
}

if(isset($_POST['Backup'])) {

	if(isset($_POST['compress']) && $_POST['compress']==1) $compress = 1; else $compress = 0;
	$database->setCompress($compress);

	$database->saveToFile();

	header("Location: db_tools.php");
	exit(0);
}
/*
if(isset($_POST['Backup_download'])) {

	if(isset($_POST['compress']) && $_POST['compress']==1) $compress = 1; else $compress = 0;
	$database->setCompress($compress);

	$fname = $database->saveToFile();

	$database->downloadFile($fname);

	header("Location: db_tools.php");

}*/

if(isset($_POST['Restore'])) {

	global $config_abs_path;
	$path = $config_abs_path.'/temp';
	if($_FILES['upload_restore']['name']) $extension=getExtension($_FILES['upload_restore']['name']);
	$filename = $_FILES['upload_restore']['name'];
	$path.="/".$filename;
	if($extension!='sql' && $extension!='gz') $error =  $lng['database']['invalid_backup_file'];
	else if(!move_uploaded_file($_FILES['upload_restore']['tmp_name'], $path))
	$error.=$lng['images']['errors']['file_not_uploaded'].'<br>';

	if(!$error) {
	if($database->restoreDB($path)) { 
		$info = $lng['database']['backup_restored'];
	?>
<script type="text/javascript">
	alert("<?php echo $info ?>");
	window.location = "<?php echo $config_live_site ?>/admin/db_tools.php"
</script>
<?php		
	}
	else $error = $lng['database']['could_not_restore_backup'];
	}
}

$schedule = $database->getSchedule();

if(isset($_POST['Save_schedule'])) {

	if(!$database->saveSchedule()) { 
		$error = $database->getError();
		$schedule = $database->getTmp();
	}
	else { 
		header("Location: db_tools.php");
		exit(0);
	}
}
$smarty->assign("schedule",$schedule);

$smarty->assign("info",$info);
$smarty->assign("error",$error);

$smarty->display('db_tools.html');
close();
?>
