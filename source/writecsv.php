<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {
	// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
	$no_login_url = "http://{$_SERVER['HTTP_HOST']}/study/login.php";
	header("Location: {$no_login_url}");
	exit;
}

// CSVファイル名の設定
$csv_file = "test.csv";

// CSVデータの初期化
$csv_data = "";

//エラー表示は1
ini_set('display_errors', 0);
$class_id_ar = array();
$id_ar = array();
$name_ar = array();
$playtime = array();
$power_ar = array();
$kaisu_ar = array();
$burekaisu_ar = array();
$jikan_ar = array();
$totalCount_ar= array();
$kakudo_ar = array();
$speedPoint_ar = array();
$i=0;

$csv_data = "クラス,番号,名前,UNIXTIME,総合得点,作業時間（秒）,切削回数,ブレ値,ひきの強さ,引きこみ角度（度）,切削速度（回/秒）\r\n";
try{
	$pdo = new PDO("sqlite:elearning.db");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql_query = "SELECT * FROM datatb ,usertb ON usertb.userid=datatb.userid WHERE typeid = 1 ";

	foreach($pdo->query($sql_query) as $row){
		array_push($class_id_ar,$row[class_id]);
		array_push($id_ar,$row[id]);
		array_push($name_ar,$row[name]);
		array_push($playtime,$row[ptime]);
		array_push($power_ar,$row[power]);
		array_push($kaisu_ar,$row[kaisu]);
		array_push($burekaisu_ar,$row[burekaisu]);
		array_push($jikan_ar,$row[jikan]);
		array_push($totalCount_ar,(int)$row[totalCount]);
		array_push($kakudo_ar,$row[kakudo]);
		array_push($speedPoint_ar,$row[speedPoint]);
		
		//CSV書き出し文字列
		$csv_data .= $class_id_ar[$i].",".$id_ar[$i].",".$name_ar[$i].",".$jikan_ar[$i].",".$totalCount_ar[$i].",".$playtime[$i].",".$kaisu_ar[$i].",".$burekaisu_ar[$i].",".$power_ar[$i].",".$kakudo_ar[$i].",".$speedPoint_ar[$i];
		$csv_data .= "\r\n";
		
		$i++;
	}
}catch(PDOException $e){
	print $e->getMessage();
	die();
}

// ファイルを追記モードで開く
$fp = fopen($csv_file, 'ab');
// ファイルを排他ロックする
flock($fp, LOCK_EX);
// ファイルの中身を空にする
ftruncate($fp, 0);

//fwriteを実行する前にmb_convert_encodingで変換
$csv_data = mb_convert_encoding($csv_data,"SJIS","UTF-8");

// データをファイルに書き込む
fwrite($fp, $csv_data);
// ファイルを閉じる
fclose($fp);
// 完了メッセージ
echo("おっしゃ！CSVで出力したった！");
?>
<html>
<head>
<title>CSV書き出し</title>
</head>
<body>
	<div class="mainviv"></div>
</body>
</html>
