<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {

	// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
	$no_login_url = "http://{$_SERVER['HTTP_HOST']}/study/login.php";
	header("Location: {$no_login_url}");
	exit;
} else {
	//	print "ログインきたでー！";
}
?>

<?php
/**
 *  Selected表示
 *
 *  @note $var と $selected_value が同じ値の時には， selected="selected" を返す
 */
function checkSelected($var, $selected_value) {
	if ($var==$selected_value) {
		return ' selected="selected"';
	}
	return '';
}

// 並び替え用の配列
$sort_ary = array(
		"5" => "日時降順",
		"1" => "作業時間が短い順",
		"2" => "作業時間が長い順",
		"3" => "ブレ値の大きい順",
		//		"4" => "番号順"
);

//グラフ得点表示用のデータ整形
function mathGraphData($userData) {
	$number = 0;
	$var = array();
	$var = explode(",",$userData);
	foreach ($var as $value){
		if($number==0){
			$returnData = (($value*2.5)+50);
			$number ++;
		}else if($value==-100){
			$returnData = $returnData.",".(-1);
		}else{
			$returnData = $returnData.",".(($value*2.5)+50);
		}
	}
	return $returnData;
}

//ブレ値グラフの表示整形
function mathGraphGyroData($userData) {
	$number = 0;
	$var = array();
	$var = explode(",",$userData);
	foreach ($var as $value){
		if($number==0){
			$returnData = $value*2;
			$number ++;
		}else if($value==-100){
			$returnData = $returnData.",".(-1);
		}else{
			$returnData = $returnData.",".$value*2;
		}
	}
	return $returnData;
}

//エラー表示は1
ini_set('display_errors', 0);
$class_list 	= $_GET['class_list'];
$kikan 			= $_GET['kikan'];
$sort_num 		= $_GET['sort_num'];
$search_name	= $_GET['search_name'];
$view 			= $_GET['view'];
$p 				= $_GET['p'];
$time 			= time();
$userid_ar 		= array();
$acc_y_ar 		= array();
$playtime_ar 	= array();
$power_ar 		= array();
$kaisu_ar 		= array();
$gyro_ar		= array();
$burekaisu_ar 	= array();
$jikan_ar 		= array();
$kakudo_ar		= array();
$name_ar		= array();
$class_ar 		= array();
$id_ar 			= array();
$totalCount_ar	= array();
$eachCount_ar	= array();

$i = 0;

try {
	$pdo = new PDO("sqlite:elearning.db");
	$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if (!$pdo) {
		die('接続失敗です。' . $sqliteerror);
	} else {
		//			print('接続に成功しました。<br>');
	}

	// SQL文の生成//////////////////////////////////////////////////
	$sql_query1 = "SELECT * FROM datatb ,usertb ON usertb.userid=datatb.userid WHERE typeid = 1 ";
	$sql_query2 = "SELECT count (*) FROM datatb , usertb ON usertb.userid=datatb.userid WHERE typeid = 1 ";
	// 			if ($class_list != null && $class_list != "null") {
	// 				$sql_query = "SELECT * FROM datatb WHERE class_id = $class_list";
	// 			} else {
	// 				$sql_query = "SELECT * FROM datatb";
	// 			}
	//		$ret = strpos($sql_query, "WHERE");

	if ($kikan != null && $kikan != "null") {
		if ($kikan == 1) {
			$viewtime 	= $time - 86400;
			$sql_query 	= $sql_query . " and jikan > $viewtime";
		} else if ($kikan == 2) {
			$viewtime 	= $time - 604800;
			$sql_query 	= $sql_query . " and jikan > $viewtime";
		} else if ($kikan == 3) {
			$viewtime 	= $time - 2419200;
			$sql_query 	= $sql_query . " and jikan > $viewtime";
		}
	}

//直す
	$ret = strpos($sql_query, "WHERE");

	if ($search_name != "") {
//		foreach ($pdo->query("SELECT * FROM usertb WHERE name = '$search_name'") as $row) {
//			$viewuserid = $row["userid"];
//		}
//		$sql_query = $sql_query . " and userid = \"$viewuserid\"";
	$sql_query 	= $sql_query . " and name = '$search_name'";
	}

	if(0<$class_list && $class_list < 5){
		$sql_query = $sql_query . " and class_id = \"$class_list\"";
	}

	if (0<$sort_num && $sort_num<6) {
		if ($sort_num == 1) {
			$sql_query = $sql_query . " ORDER BY ptime ASC";
		} else if ($sort_num == 2) {
			$sql_query = $sql_query . " ORDER BY ptime DESC";
		} else if ($sort_num == 3) {
			$sql_query = $sql_query . " ORDER BY burekaisu DESC";
		} else if ($sort_num == 4) {
			$sql_query = $sql_query . " ORDER BY id ASC";
		} else if ($sort_num == 5) {
			$sql_query = $sql_query . " ORDER BY jikan ASC";
		}
	} else{
		$sql_query = $sql_query . " ORDER BY jikan DESC";

	}
	$sql_query1=$sql_query1.$sql_query;
	$sql_query2=$sql_query2.$sql_query;

	//ページング設定
	if($p>0){
		$offset = $p * 10;
	}else{
		$offset = 0;
	}
	$sql_query1 = $sql_query1 . " LIMIT 10 OFFSET $offset";
	//データベースの行数を取得
	$countSQL = $pdo->query($sql_query2)->fetchAll();
	$maxpage = floor($countSQL[0][0]/10);
	// SQL文の生成　終了//////////////////////////////////////////////////
	?>

<html lang="ja">
<head>
<title>PMS (Practice Management System)</title>
<link rel="stylesheet" href="default.css" type="text/css" />
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.zoomooz.min.js">
</script>
<script type=”text/javascript”>
$(document).ready(function() {
$(“.zoomTarget”).zoomTarget();
});
</script>
<script src="./js/confirm.js"></script>

</head>

<body>
	<div id="main">
		<div id="top">
			<FORM method="get" action="graphview.php">
				<table align='center' valign='middle' class='top'>
					<TR>
						<TH>クラス</TH>
						<TH>Sort by</TH>
						<TH>期間</TH>
						<TH>人名検索</TH>
						<TH>表示方法</TH>
					</TR>
					<TR>
						<TH><select name=class_list>
								<option value="null">すべて</option>
								<?php
								for($classid = 1; $classid < 5; $classid++) {
									echo "<option value=\"{$classid}\"".checkSelected($classid, $class_list).">{$classid}</option>\n";
								}
								?>
						</select>
						</TH>
						<TH><select name=sort_num>
								<option value="null">日時昇順</option>
								<?php
								foreach ($sort_ary as $key => $value) {
									echo "<option value=\"{$key}\"".checkSelected($sort_num, $key).">{$value}</option>\n";
								}
								?>
						</select>
						</TH>
						<TH><select name=kikan>
								<option value="null">指定しない</option>
								<option value="1"
								<?php
								if ($kikan == 1) {echo "selected";
								}
								?>>24時間以内</option>
								<option value="2"
								<?php
								if ($kikan == 2) {echo "selected";
								}
								?>>一週間以内</option>
								<option value="3"
								<?php
								if ($kikan == 3) {echo "selected";
								}
								?>>一ヶ月以内</option>
						</select>
						</TH>
						<TH><INPUT TYPE="text" NAME="search_name" size="8"
						<?php
						if ($search_name != "") {echo "value=$search_name";
						}
						?>>
						</TH>
						<TH><INPUT type="radio" NAME="view" VALUE="1" ID="30"
						<?php
						if ($view == 1 || $view == null) {echo "checked";
						}
						?>> <LABEL FOR="30">30秒</LABEL><BR> <INPUT type="radio"
							NAME="view" VALUE="2" ID="60"
							<?php
							if ($view == 2) {echo "checked";
							}
							?>> <LABEL FOR="60">60秒</LABEL>
						</TH>
						<TH><INPUT type="submit" value="決定">
						</TH>
						</form>
						<form method="get" action="graphview.php">
							<TH><INPUT type="submit" value="リセット">
							</TH>
						</form>
						<form action="" method="post">
							<TH>
								<p>
									<button type="submit" name="logout">ログアウト</button>
								</p>
							</TH>
						</form>
						<td>
							<p>

							<div class='buttonDiv'>
								<BR> <a HREF="listview.php">リスト表示</a>
							</div>
							</p>
						</td>
						<td>
							<p>

							<div class='buttonDiv'>
								<BR> <a HREF="settingview.php">データ出力</a>
							</div>
							</p>
						</td>
					</TR>
				</Table>
				<!--/ #top-->

		</div>
		<?php
		foreach ($pdo->query($sql_query1) as $row) {
			array_push($userid_ar, $row["userid"]);
			array_push($acc_y_ar, $row["acc_y"]);
			array_push($playtime_ar, $row["ptime"]);
			array_push($power_ar, $row["power"]);
			array_push($kaisu_ar, $row["kaisu"]);
			array_push($gyro_ar, $row["gyro"]);
			array_push($burekaisu_ar, $row["burekaisu"]);
			array_push($jikan_ar, $row["jikan"]);
			array_push($kakudo_ar, $row["kakudo"]);
			array_push($totalCount_ar, round($row["totalCount"]));
			array_push($eachCount_ar, $row["eachCount"]);

			//名前の取得
			$SQL = "SELECT * FROM usertb where userid=$userid_ar[$i]";
			foreach ($pdo->query($SQL) as $rowuser) {
				array_push($name_ar, $rowuser["name"]);
				array_push($class_ar,$rowuser["class_id"]);
				array_push($id_ar,$rowuser["id"]);
			}

			if ($view == 1 || $view == null) {
				$data_acc_y = explode(",", $acc_y_ar[$i]);
				$data_gyro = explode(",", $gyro_ar[$i]);
				for ($count = 0; $count < 150; $count++) {
					$datastr = $datastr . $data_acc_y[$count] . ",";
					$databurestr = $databurestr . $data_gyro[$count] . ",";
				}
				$datastr = $datastr . $data_acc_y[149];
				$databurestr = $databurestr . $data_gyro[149];
			} else {
				$data_acc_y = explode(",", $acc_y_ar[$i]);
				$data_gyro = explode(",", $gyro_ar[$i]);
				for ($count = 0; $count < 300; $count++) {
					$datastr = $datastr . $data_acc_y[$count] . ",";
					$databurestr = $databurestr . $data_gyro[$count] . ",";
				}
				$datastr = $datastr . $data_acc_y[300];
				$databurestr = $databurestr . $data_gyro[300];
			}

			//時間が100秒を超えたら赤文字に
			if ($playtime_ar[$i] > 100) {
				$str_time = "<font color='red'>$playtime_ar[$i]秒</font>";
			} else {
				$str_time = "$playtime_ar[$i]秒";
			}
			$timeview = date("m/d H:i", $jikan_ar[$i]);

			//y軸時間の設定
			if ($view == 1 || $view == null) {
				$horizontime = array(0, 7.5, 15, 22.5, 30);
			} else {
				$horizontime = array(0, 15, 30, 45, 60);
			}

			$perkaisu = (round(($kaisu_ar[$i] / $playtime_ar[$i]) * 10)) / 10;
			$perkaisuview = "$perkaisu 回/秒";
			if ($perkaisu < 1.0) {
				$perkaisuview = "<font color='red'>$perkaisu 回/秒</font>";
			}
			$burepoint = 100-$burekaisu_ar[$i];
			$sokudopoint=100/2.5*$perkaisu;
			$kaisuoint=130-$kaisu_ar[$i];
			if($kaisuoint<0){
				$kaisuoint=0;
			}
			$jikanpoint=130-$playtime_ar[$i];
			$powerboint=100/5*$power_ar[$i];
			$kakudopoint = ((100/20)*$kakudo_ar[$i]);
			if($kakudopoint>100){
				$kakudopoint=100-($kakudopoint-100);
			}
			if($kakudopoint<0){
				$kakudopoint=0;
			}
			$tokuten=round(($powerboint+$burepoint+$sokudopoint+$kakudopoint+$kaisuoint)/5);

			//得点表示の計算
			$graphData = mathGraphData($datastr);
			$databurestr = mathGraphGyroData($databurestr);

			echo "
			<div id='userData'>
			<table  align='center' valign='middle'>
			<TR>
			<TD>
			<table  class='sample' >
			<TR>
			<TH>名前</TH>
			<TD><div class='buttonDiv'><A HREF = 'growthRecord.php?userName={$name_ar[$i]}'>$name_ar[$i]</A></div></TD>
			</TR>
			<TR>
			<TH>番号</TH>
			<TD>$class_ar[$i]組$id_ar[$i]番</TD>
			</TR>
			<TR>
			<TH>切削回数</TH>
			<TD>$kaisu_ar[$i]回</TD>
			</TR>
			<TR>
			<TH>練習時間</TH>
			<TD>$str_time</TD>
			</TR>
			<TR>
			<TH>挽く強さ</TH>
			<TD>$power_ar[$i]</TD>
			</TR>
			<TR>
			<TH>ブレ値</TH>
			<TD>$burekaisu_ar[$i] dps</TD>

			</TR>
			<TR>
			<TH>切削速度</TH>
			<TD>$perkaisuview</TD>
			</TR>
			<TR>
			<TH>練習日時</TH>
			<TD>$timeview</TD>
			</TR>
			<TR>
			<TH>引込角度</TH>
			<TD>$kakudo_ar[$i]°</TD>
			</TR>
			<TR>
			<TH>得点</TH>
			<TD>$totalCount_ar[$i] 点</TD>
			</TR>
			</TABLE>
			</TD>
			<TD>
			<div class=zoomTarget>
			<img src=\"http://chart.apis.google.com/chart?cht=lc
			&chs=480x280
			&chg=25,25
			&chtt=$name_ar[$i]さんの切削データ
			&chxt=x,y
			&chxl=0:|0|$horizontime[1]|$horizontime[2]|$horizontime[3]|$horizontime[4] (秒)|1:|-20|-10|0|10|20(m/s^2)
			&chd=t:$graphData|$databurestr|100,100|0,0
			&chco=24518a,dca116,ffffff,ffffff
			&chm=b,f7b518,1,3,0
			\" />" . "<BR><BR>
			</div>
			</TD>
			<TD>
			<div class=zoomTarget>
			<BR><BR><BR><BR>
			<img src=\"http://chart.apis.google.com/chart?cht=r
			&chxt=y,x
			&chls=2|2
			&chco=4082bb
			&chxp=0,0,20,40,60,80,100
			&chd=t:$eachCount_ar[$i]
			&chxl=1:|挽きの強さ|ブレ値|切削速度|引き込み角度|切削回数
			&chm=s,4082bb,0,-1,8,0|s,FFFFFF,0,-1,4,0|B,4082bb40,0,0,5
			&chts=000000,20
			&chs=280x300\"/>
			</div>
			</TD>
			</TR>
			</table>
			</div>";
			$i++;
			$datastr = "";
			$databurestr = "";
		}
} catch(PDOException $e) {
	print $e -> getMessage();
	die();
}
$pdo = null;
?>
		<?php
		echo "<table  align='center' valign='middle'><tr>";
		$urlname="&class_list=$class_list&sort_num=$sort_num&kikan=$kikan&search_name=$search_name&view=$view";
		if($p>0){
			$Prev=$p-1;
			$PrevPage="<td><div class='buttonDiv'><a href='graphview.php?p={$Prev}{$urlname}'>前の10件</a></div></td>";
			echo $TopPage="<td><div class='buttonDiv'><a href='graphview.php?{$urlname}'><<</a></div></td>　";
			echo $PrevPage."　";
		}
		if($p<$maxpage){
			$Next=$p+1;
			$NextPage="<td><div class='buttonDiv'><a href='graphview.php?p={$Next}{$urlname}'>次の10件</a></div></td>　";
			echo $NextPage;
			echo $TopPage="<td><div class='buttonDiv'><a href='graphview.php?{$urlname}&p={$maxpage}'>>></a></div></td>";
		}
		echo "</tr></table><BR>";
		?>
		<div class='buttonDiv' align="right">
			<a href="#maindiv">▲トップへ戻る</a>
		</div>
		<!--/ #maindiv-->
	</div>
	<BR>
</body>
</html>

<?php
//ログアウト処理//////////////////////////////////////////////////
if(isset($_POST['logout'])){

	// セッション変数を全て解除する
	$_SESSION = array();

	// セッションを切断するにはセッションクッキーも削除する。
	// セッション情報だけでなくセッションを破壊する。
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
		);
	}
	//セッションを破壊してリダイレクト
	session_destroy();
	//header("Location:index.php");
}
?>