<?php
// ＝＝＝＝＝＝＝＝＝＝＝＝＝Get Method＝＝＝＝＝＝＝＝＝＝＝＝＝＝
// $testid = $_GET["testid"];
// $triggertime = $_GET["triggertime"];
// $duringtime = $_GET["duringtime"];
// $version = $_GET["version"];
// $buildid = $_GET["buildid"];
// $uuid = $_GET["uuid"];
// $performancedata = $_GET["performancedata"];
// ＝＝＝＝＝＝＝＝＝＝＝＝POST Method ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

$testid = $_POST["testid"];
$device = $_POST["device"];
$iOS = $_POST["iOS"];
$version = $_POST["version"];
$buildid = $_POST["buildid"];
$uuid = $_POST["uuid"];
$launchTime = $_POST["launchTime"];
$endTime = $_POST["endTime"];
$isCrash = $_POST["isCrash"];
$crashLog = $_POST["crashLog"];
$crashLogFileTitle = $_POST["crashLogFileTitle"];
$crashFile = 'crashLogs/'.$crashLogFileTitle;


// echo 'testid'=>$testid,'triggertime'=>$triggertime,'duringtime'=>$duringtime,'version'=>$version,'buildid'=>$buildid,'uuid'=>$uuid,'performancedata'=>$performancedata;
include('class/mysql_crud.php');
$db = new Database();
$db->connect();
$db->insert('ios_ninegame_performance_crash_info',array('testid'=>$testid,'device'=>$device,'iOS'=>$iOS,'version'=>$version,'buildid'=>$buildid,'uuid'=>$uuid,'launchTime'=>$launchTime,'endTime'=>$endTime,'isCrash'=>$isCrash,'crashLog'=>$crashLogFileTitle));  // Table name, column names and respective values
$res = $db->getResult();  
print_r($res);

if (strlen($crashLogFileTitle) > 0)
{
	if (!file_exists("crashLogs"))
	{
		mkdir("crashLogs", 0777);
	}
	 
	$fp = fopen($crashFile, 'w');
	fwrite($fp, $crashLog);
	fclose($fp);
}

?>