<?php
// $request_path = $_REQUEST['path'];
// echo $request_path;
require_once(dirname(__FILE__).'/url_list.php');
require_once(dirname(__FILE__).'/../functions/require.php');

try {
	session_start();

	$request_path = $_REQUEST['path'];
	// サインアップページの場合はログインチェック無し
	if ($request_path == '/signup/') {
		include(dirname(__FILE__).'/models/client/signup.php');
	} else {
		if(!check_client_login()){
			// ログインされていなければ、ログイン画面を表示
			include(dirname(__FILE__).'/models/client/login.php');
		} else {
			// var_dump(check_client_login());
			// exit;
			// ログインされていれば
			if (isset($url_list[$request_path])) {
				// アクセスされたURLのプログラムに処理を移譲
				include(dirname(__FILE__).$url_list[$request_path]);
			}
		}
	}

} catch (Exception $e) {
		var_dump($e);
		exit;
}