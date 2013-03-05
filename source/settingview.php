<html>
	<head>
		<title>PHP TEST</title>
	<link rel="stylesheet" href="default.css" type="text/css" />
	</head>
	<body>
		<?php
		//エラー表示は1
		ini_set('display_errors', 0);

		$reset = $_GET['reset'];
		$year_value = $_GET['year_value'];
		$class_value = $_GET['class_value'];
		$id_value = $_GET['id_value'];
		$id_value_next = $id_value + 1;
		$name_value = $_GET['name_value'];
		$sex_value = $_GET['sex_value'];
		?>

		<p>
			<div class='buttonDiv'><a HREF="listview.php">リスト表示へ切り替え</a></div><BR>
			<div class='buttonDiv'><a HREF="writecsv.php">CSVファイルの書き出し</a></div><BR>
		</p>
		<h4>※ユーザー追加を行った後，戻るボタン・更新は使用しないでください</h4>
		<?php
		try {
			$pdo = new PDO("sqlite:elearning.db");
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if (!$pdo) {
				die('接続失敗です。' . $sqliteerror);
			} else {
			//	print('接続に成功しました。<br>');
			}

			if ($reset == 1) {
				//テーブル削除
				$pdo -> query('drop table datatb');
				// テーブル作成
				$pdo -> query('create table datatb(userid INTEGER, acc_y, kaisu INTEGER,ptime INTEGER,power INTEGER,gyro INTEGER,jikan INTEGER,burekaisu INTEGER,kakudo Integer,typeid Integer)');
				print "実行しました";
			}
			if ($reset == 2) {
				//テキスト書き出し
				$pdo -> query('copy usertb to "output.csv" CSV');
				print "実行しました";
			}
			if ($id_value != "" && $name_value != "") {
				$pdo -> query("INSERT INTO usertb VALUES($year_value,$class_value,$id_value,'$name_value','$sex_value',null);");
				//echo "$name_valueさんを追加しました。";
			}else{
				echo "ユーザーの追加に失敗しました";
			}
		} catch(PDOException $e) {
			print $e -> getMessage();
			die();
		}
		$pdo = null;
		//	print('<br>切断しました。<br>');
		?>
		<h3>ユーザーの追加</h3>
		<table>
			<tr>
				<th>学年</th>
				<th>クラス</th>
				<th>番号</th>
				<th>名前</th>
				<th>性別</th>
			</tr>
			<tr>
				<form method="get" action="settingview.php">
					<td>
					<select name=year_value>
						<option value="1"<?php
						if ($year_value == 1) {echo "selected";
						}
						?>>1</option>
						<option value="2"<?php
						if ($year_value == 2) {echo "selected";
						}
						?>>2</option>
						<option value="3"<?php
						if ($year_value == 3) {echo "selected";
						}
						?>>3</option>
					</td>
					<td>
					<select name=class_value>
						<option value="1"<?php
						if ($class_value == 1) {echo "selected";
						}
						?>>1</option>
						<option value="2"<?php
						if ($class_value == 2) {echo "selected";
						}
						?>>2</option>
						<option value="3"<?php
						if ($class_value == 3) {echo "selected";
						}
						?>>3 </option>
						<option value="4"<?php
						if ($class_value == 4) {echo "selected";
						}
						?>>4 </option>
					</td>
					<td>
					<INPUT TYPE="text" NAME="id_value" size="2"<?php
					if ($id_value != "") {$id_value++;
						echo "value=$id_value";
					}
					?>>
					</td>
					<td>
					<INPUT TYPE="text" NAME="name_value" size="8">
					</td>
					<td>
					<select name=sex_value>
						<option value="男"<?php
						if ($sex_value == "男") {echo "selected";
						}
						?>>男</option>
						<option value="女"<?php
						if ($sex_value == "女") {echo "selected";
						}
						?>>女</option>
					</td>
					<td>
					<INPUT  type="submit"  value="ユーザー追加">
					</td>
				</form>
			</tr>
		</table>
	</body>
</html>
