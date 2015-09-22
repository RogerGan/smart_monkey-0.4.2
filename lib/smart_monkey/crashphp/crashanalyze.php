<!doctype html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title>Monkey Test</title>
  <link rel="stylesheet" type="text/css" href="bootstrap.css"/>
</head>
<body>

<?php
// ＝＝＝＝＝＝＝＝＝＝＝＝＝Get Method＝＝＝＝＝＝＝＝＝＝＝＝＝＝
$days = $_GET["days"]; 
if ($days < 0.01) {
	$days = 1.0;
}

// echo 'testid'=>$testid,'triggertime'=>$triggertime,'duringtime'=>$duringtime,'version'=>$version,'buildid'=>$buildid,'uuid'=>$uuid,'performancedata'=>$performancedata;
include('class/mysql_crud.php');
$db = new Database();
$db->connect();
// echo "World!";

// $db->insert('ios_ninegame_performance_crash_info',array('testid'=>$testid,'device'=>$device,'iOS'=>$iOS,'version'=>$version,'buildid'=>$buildid,'uuid'=>$uuid,'launchTime'=>$launchTime,'endTime'=>$endTime,'isCrash'=>$isCrash,'crashLog'=>$crashLog));  // Table name, column names and respective values
// $db->select('ios_ninegame_performance_crash_info', 'id,device,iOS,launchTime,endTime,isCrash', NULL, 'launchTime>"2015-09-19 17:41:53"','launchTime DESC');

date_default_timezone_set("Asia/Shanghai");
$time = time();
$fromTime = $time - $days * 24 * 60 * 60;
 echo "Query ";
 echo $days;
 echo " days<br>FromTime:";
// echo date("Y-m-d H:i:s", $fromTime);
$fromStr = date("Y-m-d H:i:s", $fromTime);
echo $fromStr;
$db->select('ios_ninegame_performance_crash_info', 'id,device,iOS,isSimulator,launchTime,endTime,isCrash,crashLog', NULL, 'launchTime>"'.$fromStr.'"', 'launchTime DESC');
echo "<br>";
$res = $db->getResult();  
// print_r($res);

$count = 0; // $res.count();
// echo "<br><hr>";

$crashCount = 0;
$totalTime = 0;
echo "<table class='table table-bordered'>";
echo "<tr><td>Index</td><td>Device</td><td>iOS</td><td>type</td><td>LaunchTime</td><td>EndTime</td><td>LaunchLength</td><td>State</td><td>crashLog</td></tr>";
foreach($res as $output){
	echo ($output["isCrash"] == "YES") ? "<tr class='error'>" : "<tr class='success'>";
	// echo "<tr class='success'>";
	echo "<td>";
	echo $count."(".$output["id"].")";
	echo "</td>";
	// echo " ";

	echo "<td>";
	echo $output["device"];
	echo "</td>";

	echo "<td>";
	echo $output["iOS"];
	echo "</td>";

	echo "<td>";
	echo $output["isSimulator"] == "YES" ? "sim" : "dev";
	echo "</td>";

	echo "<td>";
	echo $output["launchTime"];
	echo "</td>";
	// echo " ";
	echo "<td>";
	echo $output["endTime"];
	echo "</td>";
	$length = strtotime($output["endTime"]) - strtotime($output["launchTime"]);
	echo "<td>";
	echo $length;
	echo "</td>";
	// echo "<br/>";
	echo "<td>";
	echo ($output["isCrash"] == "YES") ? "Crash" : "OK";
	echo "</td>";

	echo "<td>";
	echo "<a href='crashLogs/".$output["crashLog"]."'>".$output["crashLog"]."</a>";
	echo "</td>";

	echo "</tr>";

	if ($output["isCrash"] == "YES") {
		$crashCount ++;
	}

	$totalTime += $length;
	
	$count ++;
}
echo "</table>";

$crashRate = $crashCount/($totalTime/3600);

// echo "<hr><br>";
echo "crash count:".$crashCount."<br/>";
echo "total launch count:".$count."<br/>";
echo "total time:".$totalTime." seconds, as ".number_format(($totalTime/3600), 1, '.', '')."hours.<br/>";
echo "crash "."<font color=red>".number_format($crashRate, 1, '.', '')."</font>"."times per hour.<br>";
echo "crash once per "."<font color=red>".number_format(1.0/$crashRate, 1, '.', '')."</font>"." hours.<br>";

echo "End!";

?>

</body>
</html>