<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {
	// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
	$no_login_url = "http://{$_SERVER['HTTP_HOST']}/study/login.php";
	header("Location: {$no_login_url}");
	exit;
}
function viewData($miss, $good) {
	if ($miss > 1) {
		return "bgcolor='feldsper'";
	}else if($good==2){
		return "bgcolor='LightCyan'";		
	}
	return '';
}

//ソートidの計算
function getSortId($var){
	if($var!=null){
	if($var==1||$var==3){
		$var=1;
	}else{
		$var=2;
	}
	return $var;
	}
}

//エラー表示は1
ini_set('display_errors', 0);
$ar = array();
$class_id_ar = array();
$id_ar = array();
$name_ar = array();
$playtime = array();
$power_ar = array();
$kaisu_ar = array();
$bure_ar = array();
$burekaisu_ar = array();
$jikan_ar = array();
$totalCount_ar= array();
$i=0;

$sorttime =$_GET['sorttime'];
$sorttime = getSortId($sorttime);
$sortid =$_GET['sortid'];
$sortid = getSortId($sortid);
$sortday =$_GET['sortday'];
$sortday = getSortId($sortday);
$sortpower =$_GET['sortpower'];
$sortpower = getSortId($sortpower);
$sortbure =$_GET['sortbure'];
$sortbure = getSortId($sortbure);
$sortkaisu =$_GET['sortkaisu'];
$sortkaisu = getSortId($sortkaisu);
$sortpoint =$_GET['sortpoint'];
$sortpoint = getSortId($sortpoint);
$selectClass =$_GET['selectClass'];

if($_POST["selectClass"]!=null){
$_SESSION["selectClass"] = $_POST["selectClass"];
}
?>

<html>
<head>
<title>一覧画面</title>
<link rel="stylesheet" href="list.css" type="text/css" />
</head>
<body>
	<div class="mainviv">
	<div class='buttonDiv'><a HREF="graphview.php">グラフ表示へ切り替え</a></div><BR>
	クラス選択 : 
	<FORM  style="display:inline;" action="listview.php" method="post">
	<SELECT name='selectClass' onChange='this.form.submit()'>
	<OPTION value='0'>すべて</OPTION>
	<OPTION value='1' <?php if($_SESSION["selectClass"] == 1){echo "selected";}?>>1組</OPTION>
	<OPTION value='2' <?php if($_SESSION["selectClass"] == 2){echo "selected";}?>>2組</OPTION>
	<OPTION value='3' <?php if($_SESSION["selectClass"] == 3){echo "selected";}?>>3組</OPTION>
	<OPTION value='4' <?php if($_SESSION["selectClass"] == 4){echo "selected";}?>>4組</OPTION>
	</SELECT>
	</FORM>
	<HR width="1000" size="2" align="left">
	<?php
	try{
		$pdo = new PDO("sqlite:elearning.db");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql_query = "SELECT * FROM datatb ,usertb ON usertb.userid=datatb.userid WHERE typeid = 1 ";
		if($_SESSION["selectClass"]>0){
			$sql_query = $sql_query." AND class_id = ".$_SESSION['selectClass'];
		}
		// サーバからデータ取得
		if($sorttime==1){
			$sql_query = $sql_query." ORDER BY ptime DESC";
		}else if($sorttime==2){
			$sql_query = $sql_query." ORDER BY ptime ASC";
		}else if($sortid==1){
			$sql_query = $sql_query." ORDER BY id ASC";
		}else if($sortid==2){
			$sql_query = $sql_query." ORDER BY id DESC";
		}else if($sortday==1){
			$sql_query = $sql_query." ORDER BY jikan DESC";
		}else if($sortday==2){
			$sql_query = $sql_query." ORDER BY jikan ASC";
		}else if($sortpower==1){
			$sql_query =$sql_query." ORDER BY power ASC";
		}else if($sortpower==2){
			$sql_query =$sql_query." ORDER BY power DESC";
		}else if($sortbure==1){
			$sql_query =$sql_query." ORDER BY burekaisu DESC";
		}else if($sortbure==2){
			$sql_query =$sql_query." ORDER BY burekaisu ASC";
		}else if($sortkaisu==1){
			$sql_query = $sql_query." ORDER BY kaisu DESC";
		}else if($sortkaisu==2){
			$sql_query = $sql_query." ORDER BY kaisu ASC";
		}else if($sortpoint == 1){
			$sql_query = $sql_query." ORDER BY totalCount ASC";
		}else if($sortpoint == 2){
			$sql_query = $sql_query." ORDER BY totalCount DESC";
		}

$sorttime++;
$sortday++;
$sortpower++;
$sortid++;
$sortbure++;
$sortkaisu++;
$sortpoint++;
?>
		<table align='center' valign='middle' class='sample'>
		<TR ALIGN = 'center'><TH>クラス</TH>
		<?php echo "
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortid=$sortid'>番号</a></div></TH>
		<TH>名前</TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortpoint=$sortpoint'>得点</a></div></TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortkaisu=$sortkaisu'>切削回数</a></div></TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sorttime=$sorttime'>切削時間</a></div></TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortpower=$sortpower'>挽く強さ</a></div></TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortbure=$sortbure'>ブレ値</a></div></TH>
		<TH><div class='buttonDiv'><A HREF = 'listview.php?sortday=$sortday'>日時</a></div></TH>";
		foreach($pdo->query($sql_query) as $row){
		array_push($class_id_ar,$row[class_id]);
		array_push($id_ar,$row[id]);
		array_push($ar,$row[data]);
		array_push($name_ar,$row[name]);
		array_push($playtime,$row[ptime]);
		array_push($power_ar,$row[power]);
		array_push($kaisu_ar,$row[kaisu]);
		array_push($bure_ar,$row[bure]);
		array_push($burekaisu_ar,$row[burekaisu]);
		array_push($jikan_ar,$row[jikan]);
		array_push($totalCount_ar,(int)$row[totalCount]);
		$missInt 	= 0;
		$goodInt 	= 0;

			//時間が100秒を超えたら赤文字に
			if($playtime[$i]>100){
				$str_time = "<font color='red'>$playtime[$i]秒</font>";
				$missInt ++;
			}else{
				$str_time = "$playtime[$i]秒";
			}
			//挽く強さが2以下の場合
			if($power_ar[$i]<3){
				$power_ar[$i] = "<font color='red'>★</font>";
				$missInt ++;
			}else if($power_ar[$i]<6){
				$power_ar[$i] = "★★";
			}else{
			$power_ar[$i] = "★★★";
			$goodInt ++;}
			//ブレ値が30以上の場合
			if($burekaisu_ar[$i]>29){
				$burekaisu_ar[$i] = "<font color='red'>☆</font>";
				$missInt ++;
			}else if ($burekaisu_ar[$i]>10){
				$burekaisu_ar[$i] = "☆☆";
			}else{
				$burekaisu_ar[$i] = "☆☆☆";
				$goodInt++;
			}
			$timeview = date("m/d H:i",$jikan_ar[$i]);
			$viewColor = viewData($missInt,$goodInt);
			echo "
			<TR ALIGN = 'center'><TD>$class_id_ar[$i]</TD>
			<TD>$id_ar[$i]</TD>
			<TD $viewColor><A HREF = 'growthRecord.php?userName=$name_ar[$i]'>$name_ar[$i]</A></TD>
			<TD>$totalCount_ar[$i]</TD>
			<TD>$kaisu_ar[$i]</TD>
			<TD>$str_time</TD>
			<TD Align='left'>$power_ar[$i]</TD>
			<TD Align='left'>$burekaisu_ar[$i]</TD>
			<TD>$timeview</TD>
			</TR>";
			$i++;
		}
	}catch(PDOException $e){
		print $e->getMessage();
		die();
	}
	echo "</table>";
	$pdo = null;
	?>
	</div>
	<BR>
	<div class='buttonDiv' align="right">
	<a href="#maindiv">▲トップへ戻る</a>
	</div>
</body>
</html>
