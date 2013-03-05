<?php
ini_set('display_errors', 0);
$userName = $_GET['userName'];
$writeCsvDecision = $_GET['write'];

//データの連結
function connectionStr($var,$useData,$totalData){
	if($totalData==0){
		$var = round($useData);
	}else{
		$var = $var.",".round($useData);
	}
	return $var;
}
//グラフ共通部分
function graphWrite($useData,$totalData,$totalDataHalf,$title){
	$var="<BR><div id=\"graphDiv\">
	<img src=\"http://chart.apis.google.com/chart?cht=lc
	&chs=300x150
	&chg=50,20
	&chtt=$title
	&chxt=x,y
	&chxl=0:|1|$totalDataHalf|$totalData (回)|1:|0|20|40|60|80|100 (点)
	&chm=o,24518a,0,-1,8,0|o,FFFFFF,0,-1,5,0
	&chd=t:$useData
	&chco=24518a
	\" /></div>";
	return $var;
}
try {
	$pdo = new PDO("sqlite:elearning.db");
	$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if (!$pdo) {
		die('接続失敗です。' . $sqliteerror);
	} else {
		//					print('接続に成功しました。<br>');
	}
}catch(PDOException $e) {
	print $e -> getMessage();
	die();
}
$sql_query = "SELECT * FROM datatb ,usertb ON usertb.userid=datatb.userid WHERE typeid = 1 and name = '$userName'  ORDER BY jikan ASC";
$totalPoint = "";
$power = "";
$bure = "";
$speed = "";
$angle = "";
$count = "";
$totalData=0;
$eachCountAr=array();
foreach ($pdo->query($sql_query) as $row) {
	$eachCountAr=explode(",",$row["eachCount"]);
	$eachCountArItemNum = 6;
	if (count($eachCountAr)!=$eachCountArItemNum) {
		for($i=0; $i<$eachCountArItemNum; $i++) {
			$eachCountAr[]=0;
		}
	}
	//	echo $eachCountAr[0];
	$totalPoint = connectionStr($totalPoint,$row["totalCount"],$totalData);
	$power = connectionStr($power,$eachCountAr[0],$totalData);
	$bure = connectionStr($bure,$eachCountAr[1],$totalData);
	$speed = connectionStr($speed,$eachCountAr[2],$totalData);
	$angle = connectionStr($angle,$eachCountAr[3],$totalData);
	$count = connectionStr($count,$eachCountAr[4],$totalData);

	array_splice($eachCountAr, 0,0);
	$totalData++;
}
//echo $power;
$totalDataHalf = round($totalData / 2);
?>
<html>
<head>
<title><?php echo $userName?>さんの記録</title>
<link rel="stylesheet" href="mobile.css" type="text/css" />
</head>
<body align="center">
		<p>
			<font size="3"><?php echo $userName?>さんの記録</font>
		</p>
		<?php
				$point_array = explode(",", $totalPoint);
				$lastPoint = end(array_values($point_array));
		?>
		<p>	今回の総合得点 : <font size="4" color="red"><?php echo $lastPoint ?>点</font>
		</p>
	<div id="main" align="center">
		<?php
		echo graphWrite($totalPoint,$totalData,$totalDataHalf,"総合得点");
		echo graphWrite($power,$totalData,$totalDataHalf,"ひきの強さ");
		echo graphWrite($bure,$totalData,$totalDataHalf,"のこぎりのブレ");
		echo graphWrite($speed,$totalData,$totalDataHalf,"ひきの速さ");
		echo graphWrite($angle,$totalData,$totalDataHalf,"ひき込み角度");
		echo graphWrite($count,$totalData,$totalDataHalf,"ひいた回数");
		?>
	</div>
</body>
</html>