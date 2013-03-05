<?php
//エラー表示は1
ini_set('display_errors', 0);

///ログイン処理/////////////////////////////////////
session_start();
// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {

	// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
	$no_login_url = "http://{$_SERVER["HTTP_HOST"]}/study/login.php";
	header("Location: {$no_login_url}");
	exit;
} else {
}
///ここまで，ログイン処理/////////////////////////////////////
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
	<div class=zoomTarget >
	<img src=\"http://chart.apis.google.com/chart?cht=lc
	&chs=500x400
	&chg=50,20
	&chtt=$title
	&chxt=x,y
	&chxl=0:|1|$totalDataHalf|$totalData (回)|1:|0|20|40|60|80|100 (点)
	&chm=o,24518a,0,-1,8,0|o,FFFFFF,0,-1,5,0
	&chd=t:$useData
	&chco=24518a
	\" /></div></div>";
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
<link rel="stylesheet" href="growth.css" type="text/css" />
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="./js/jquery.zoomooz.min.js">
</script>
<script type=”text/javascript”>
$(document).ready(function() {
$(“.zoomTarget”).zoomTarget();
});
</script>
<script type="text/javascript" src="./js/parts.js"></script>
</head>
<body align="center">
	<div id='growTop'>
		<p>
			<font size="6" color="#ffffff"><?php echo $userName?>さんの記録</font>
		</p>

	</div>

	<div id="main" align="center">
		<?php
		echo ("<table align = 'center'><tr><td>");
		echo graphWrite($totalPoint,$totalData,$totalDataHalf,"総合得点");
		echo ("</td>");
		echo ("<td>");
		echo graphWrite($power,$totalData,$totalDataHalf,"挽きの強さ");
		echo ("</td></tr>");
		echo ("<tr><td>");
		echo graphWrite($bure,$totalData,$totalDataHalf,"のこぎりのブレ");
		echo ("</td>");
		echo ("<td>");
		echo graphWrite($speed,$totalData,$totalDataHalf,"挽きの速さ");
		echo ("</td></tr>");
		echo ("<tr><td>");
		echo graphWrite($angle,$totalData,$totalDataHalf,"引き込み角度");
		echo ("</td>");
		echo ("<td>");
		echo graphWrite($count,$totalData,$totalDataHalf,"挽いた回数");
		echo ("</td></tr></table>");
		?>
	</div>
	<BR><BR>
	<div class='buttonDiv'><A HREF = 'graphview.php?search_name=<?php echo ("$userName"); ?>'>1回ごとの結果</a></div><BR>
	<FORM align="center" method="get" action="growthRecord.php">
	<div class='buttonDiv'>
	<INPUT type="button" value="戻る" onClick="history.back()"> <input
			type="hidden" name="write" value="1" /> 
	</FORM>
</body>
</html>
