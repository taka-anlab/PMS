<?php
$userName="admin";
$passWord="pms";
session_start();

// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["login"])) {

//ログインの判定
if ($_POST["user_name"] == $userName && $_POST["password"] == $passWord) {

// ログインが成功した証をセッションに保存
$_SESSION["user_name"] = $_POST["user_name"];

// 管理者専用画面へリダイレクト
$login_url = "http://{$_SERVER["HTTP_HOST"]}/study/graphview.php";
header("Location: {$login_url}");
exit;
}
$error_message = "ユーザー名もしくはパスワードが違っています。";
}
?>

<html>
<head>
<title>ログイン画面</title>
<link rel="stylesheet" href="login.css" type="text/css" />
</head>
<body>
<?php
if ($error_message) {
print '<font color="red">'.$error_message.'</font>';
}
?>
<div id="main">
<form action="login.php" method="POST">
ユーザー名：<input type="text" name="user_name" value="" /><br />
パスワード：<input type="password" name="password" value"" /><br />
<input type="submit" name="login" value="ログイン" />
</form>
</div>
</body>
</html>