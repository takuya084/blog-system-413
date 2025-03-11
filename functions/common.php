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

function getClient($mail_address, $password, $pdo) {
	$sql = "select id from client where mail_address = :mail_address and password = :password limit 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':mail_address',$mail_address,PDO::PARAM_STR);
	$stmt->bindValue(':password',$password,PDO::PARAM_STR);
	//$stmt->execute(array(":mail_address" => $mail_address, ":password" => $password));
	$stmt->execute();
	// $client = $stmt->fetch();
	// return $client ? $client['id'] : false;
	$client = $stmt->fetch();
	return $client ? $client : false;
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

function file_upload($id, $err) {
	// アップロードを許可する画像タイプ
	// アップロードを許可する画像タイプ（1:GIF、2:JPEG、3:PNG、17:ICO）
	$image_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_ICO);

	$return_array = array();
	$fp = NULL;
	$extension = NULL;

	if ($id) {
		if ($_FILES[$id]['tmp_name']) {
			// 成功の場合も$_FILES[$id]['error']にUPLOAD_ERR_OK:0が入ってくるためここでコード存在チェック
			if (isset($_FILES[$id]['error']) && is_int($_FILES[$id]['error'])) {
				try {
					// $_FILES['upfile']['error'] の値を確認
					switch ($_FILES[$id]['error']) {
						case UPLOAD_ERR_OK: // 0:エラーはなく、ファイルアップロードは成功しています。
							break;
						case UPLOAD_ERR_INI_SIZE: // 1:アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。
						case UPLOAD_ERR_FORM_SIZE: // 2:アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。
							throw new RuntimeException('ファイルサイズが大きすぎます。');
						default:
							throw new RuntimeException('エラーが発生しました。');
					}

					// $_FILES[$id]['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
					$type = @exif_imagetype($_FILES[$id]['tmp_name']);
					if (!in_array($type, $image_types, true)) {
						throw new RuntimeException('未対応の画像形式です。');
					}

					if (!$info = @getimagesize($_FILES[$id]['tmp_name'])) {
						throw new RuntimeException("有効な画像ファイルを指定して下さい。");
					}

					$fp = fopen($_FILES[$id]['tmp_name'], 'rb');
					$extension = pathinfo($_FILES[$id]["name"], PATHINFO_EXTENSION);
				} catch (RuntimeException $e) {
					$err[$id] = $e->getMessage();
				}
			}
		}
	}
	$return_array['file'] = $fp;
	$return_array['ext'] = $extension;
	$return_array['err'] = $err;
	return $return_array;
}