<?php
$pdo = connectDb();

if (isset($_COOKIE['BLOG'])) {
	$auto_login_key = $_COOKIE['BLOG'];
	setcookie('BLOG', '', time()-86400, '/');

	$sql = "delete from client_auto_login where c_key = :c_key";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":c_key" => $auto_login_key));
}
//セッション内のデータ削除
$_SESSION = array();

//クッキーの無効化
if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-86400, '/');
}

//セッションの破棄
session_destroy();

unset($pdo);

header('Location:'.SITE_URL.'/login/');

?>
