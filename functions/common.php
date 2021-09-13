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
	$client = $stmt -> fetch();
	return $client ? true : false;
}

function getClientId($mail_address, $password, $pdo) {
    $sql = "select id from client where mail_address = :mail_address and password = :password limit 1";
    $stmt = $pdo->prepare($sql);
	$stmt->bindValue(':mail_address',$mail_address,PDO::PARAM_STR);
	$stmt->bindValue(':password',$password,PDO::PARAM_STR);
    //$stmt->execute(array(":mail_address" => $mail_address, ":password" => $password));
	$stmt->execute();
    $client = $stmt->fetch();
    return $client ? $client['id'] : false;
}

function getClientbyClientId($client_id, $pdo) {
    $sql = "select * from client where id = :client_id limit 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":client_id" => $client_id));
    $client = $stmt->fetch();

    return $client ? $client : false;
}

function client_code(){
    return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 12);
}

function check_client_login(){
	if (!isset($_SESSION['CLIENT'])) {
		return false;
	}else{
		return true;
	}
}
