<?php
function connectDb(){
	try{
		$pdo = new PDO(DSN, DB_USER, DB_PASSWORD);
		$pdo->query('SET NAMES utf8');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}catch(PDOException $e){
		echo $e->getMessage();
		exit();
	}
}

function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function setToken(){
	$token = sha1(uniqid(mt_rand(), true));
	$_SESSION['sstoken'] = $token;
}

function checkToken(){
	if(empty($_SESSION['sstoken']) || ($_SESSION['sstoken'] != $_POST['token'])){
		echo '<html><head><meta charset="utf-8"></head><body>不正なアクセスです。</body></html>';
		exit;
	}
}

function checkEmail($mail_address, $pdo){
	$sql = "select * from client where mail_address = :mail_address limit 1";
	$stmt = $pdo -> prepare($sql);
	$stmt -> execute(array(":mail_address" => $mail_address));
	$user = $stmt -> fetch();
	return $user ? true : false;
}

function getClientID($mail_address, $password, $pdo) {
    $sql = "select id from client where mail_address = :mail_address and password = :password limit 1";
    $stmt = $pdo->prepare($sql);
	$stmt->bindValue(':mail_address',$mail_address,PDO::PARAM_STR);
	$stmt->bindValue(':password',$password,PDO::PARAM_STR);
    //$stmt->execute(array(":mail_address" => $mail_address, ":password" => $password));
	$stmt->execute();
    $user = $stmt->fetch();
    return $user ? $user['id'] : false;
}

function client_code(){
    return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 12);
}
