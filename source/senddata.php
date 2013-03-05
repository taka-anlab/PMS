<html>
<head>
<title>PHP TEST</title>
</head>
<body>
	<?php

	function calcPoint($var){
		if($var>100){
			$var=100;
		}else if($var<0){
			$var=0;
		}
		return $var;
	}


	$userid = $_GET['userid'];
	$acc_y = $_GET['acc_y'];
	$kaisu = $_GET['kaisu'];
	$ptime = $_GET['ptime'];
	$power =$_GET['power'];
	$gyro =$_GET['gyro'];
	$burekaisu =$_GET['burekaisu'];
	$kakudo =$_GET['kakudo'];
	$typeid =$_GET['typeid'];
	$unixtime=time();
	try{
		$pdo = new PDO("sqlite:elearning.db");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if (!$pdo) {
			die('接続失敗です。'.$sqliteerror);
		}else{
			print('接続に成功しました。<br>');
		}

		$powerPoint = 20 * $power;
		$powerPoint = calcPoint($powerPoint);

		$burePoint = 100 - ($burekaisu*3);
		$burePoint = calcPoint($burePoint);

		//　　2.0回/s以上で100
		$speedPoint = (round(($kaisu / $ptime) * 10)) / 10;
		$speedCount = 50 * $speedPoint;
		$speedCount = calcPoint($speedCount);

		$bairitu = 100/15;
		(int)$anglePoint = $bairitu * $kakudo;
		if($anglePoint > 100){
			$anglePoint = 100 - ($anglePoint - 100);
		}
		$anglePoint =calcPoint($anglePoint);

		$kaisuPoint = 140 - ($kaisu);
		$kaisuPoint = calcPoint($kaisuPoint);

		$eachCount = $powerPoint.",".$burePoint.",".$speedCount.",".$anglePoint.",".$kaisuPoint.",".$powerPoint;
		$totalCount = ($powerPoint + $burePoint + $speedCount + $anglePoint + $kaisuPoint) / 5;

		if($kaisu>20){
		$pdo->exec("INSERT INTO datatb VALUES ('$userid','$acc_y','$kaisu','$ptime','$power','$gyro','$unixtime','$burekaisu','$kakudo','$typeid','$totalCount','$eachCount','$speedPoint')");
		}
	}catch(PDOException $e){
		print $e->getMessage();
		die();
	}
	$pdo = null;
	print('切断しました。<br>');
	?>
</body>
</html>
