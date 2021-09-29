<?php
require_once(dirname(__FILE__).'/../../../functions/require.php');

session_start();

$pdo = connectDb();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	if (isset($_COOKIE['BLOG'])) {
		$auto_login_key = $_COOKIE['BLOG'];
		$sql = "select * from client_auto_login where c_key = :c_key and expire >= :expire limit 1";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":c_key" => $auto_login_key, "expire" => date('Y:m:d H:i:s')));
		$row = $stmt->fetch();

		if ($row) {
			$client = getClientbyClientId($row['client_id'], $pdo);
			session_regenerate_id(true);
			$_SESSION['CLIENT'] = $client;

			header('Location:'.SITE_URL.'/blog/');

	  		unset($pdo);
			exit;
		}
	}
	setToken();
} else {
	checkToken();
	$mail_address = $_POST['mail_address'];
	$password = $_POST['password'];
	$auto_login =$_POST['auto_login'];

	$err = array();

	if ($mail_address == '') {
			$err['mail_address'] = 'メールアドレスを入力して下さい。';
		} else {
			if (!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
				$err['mail_address'] = 'メールアドレスが不正です。';
			} else {
				if (!checkEmail($mail_address, $pdo)) {
					$err['mail_address'] = 'このメールアドレスが登録されていません。';
				}
			}
	}

	if ($password == '') {
		$err['password'] = 'パスワードを入力して下さい。';
	}

	if ($mail_address && $password) {
	    $client = getClientId($mail_address, $password, $pdo);
	    if (!$client) {
	      $err['password'] = 'パスワードが正しくありません。';
	    }
	}

	if (empty($err)) {
		session_regenerate_id(true);

		$_SESSION['CLIENT'] = $client;

		if (isset($_COOKIE['BLOG'])) {
			$auto_login_key = $_COOKIE['BLOG'];
			setcookie('BLOG', '', time()-86400, '/dev/blog-system-413/app/');
			$sql = "delete from client_auto_login where c_key = :c_key";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":c_key" => $auto_login_key));
		}

		if ($auto_login) {
			$auto_login_key = sha1(uniqid(mt_rand(), true));
			setcookie('BLOG', $auto_login_key, time()+3600*24*365, '/dev/blog-system-413/app/');
			$sql = "insert into client_auto_login
					(client_id, c_key, expire, created_at, updated_at)
					values
					(:client_id, :c_key, :expire, now(), now())";
			$stmt = $pdo->prepare($sql);
			$params = array(
				":client_id" => $client['id'],
				":c_key" => $auto_login_key,
				":expire" => date('Y-m-d H:i:s', time()+3600*24*365)
			);
			$stmt->execute($params);
		}
		header('Location:'.SITE_URL.'/blog/');
		exit;
	}
	unset($pdo);

}

?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="ja" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ja">
<!--<![endif]-->

	<head>
		<meta charset="utf-8" />
		<title>ログイン | BLOG SYSTEM</title>
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
		<meta content="" name="description" />
		<meta content="" name="author" />

		<!-- ================== BEGIN BASE CSS STYLE ================== -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/font-awesome/5.3/css/all.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/animate/animate.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/style.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/style-responsive.min.css" rel="stylesheet" />
		<link href="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/default.css" rel="stylesheet" id="theme" />
		<!-- ================== END BASE CSS STYLE ================== -->

		<!-- ================== BEGIN BASE JS ================== -->
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/pace/pace.min.js"></script>
		<!-- ================== END BASE JS ================== -->

		<link rel="icon" type="image/vnd.microsoft.icon" href="https://demo.flu-x.net/contents/img/favicon.ico">
		<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="https://demo.flu-x.net/contents/img/favicon.ico">
		<link rel="apple-touch-icon" sizes="180x180" href="https://demo.flu-x.net/contents/img/apple-touch-icon-180x180.png">

<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
<!-- ================== END PAGE LEVEL CSS STYLE ================== -->

		<style>
		.border-top {border-top: 1px solid #dcdcdc!important;}
		.border-bottom {border-bottom: 1px solid #dcdcdc!important;}
		.border-left {border-left: 1px solid #dcdcdc!important;}
		.border-right {border-right: 1px solid #dcdcdc!important;}
		</style>
	</head>

	<body class="pace-top bg-white">

		<!-- begin #page-loader -->
<div id="page-loader" class="fade show"><span class="spinner"></span></div>
<!-- end #page-loader -->

		<!-- begin #page-container -->
		<div id="page-container" class="fade">

			<!-- begin login -->
			<div class="login login-with-news-feed">
				<!-- begin news-feed -->
				<div class="news-feed">
					<div class="news-image" style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/login-bg/login-bg-11.jpg)"></div>
					<div class="news-caption">
						<h4 class="caption-title"><b>BLOG SYSTEM</b></h4>
						<p>Blog System Demonstration</p>
					</div>
				</div>
				<!-- end news-feed -->

				<!-- begin right-content -->
				<div class="right-content">
					<!-- begin login-header -->
					<div class="login-header">
						<div class="brand">
							<span class="logo"></span> <b>BLOG</b> Login
							<small>Blog System Demonstration</small>
						</div>
						<div class="icon">
							<i class="fa fa-sign-in"></i>
						</div>
					</div>
					<!-- end login-header -->

					<!-- begin login-content -->
					<div class="login-content">
						<form method="POST" action="" class="margin-bottom-0">
							<div class="form-group m-b-15">
								<input id="mail_address" name="mail_address" type="text" class="form-control form-control-lg " placeholder="メールアドレス" value="" />
								<div class="invalid-feedback"></div>
							</div>
							<div class="form-group m-b-15">
								<input id="password" name="password" type="password" class="form-control form-control-lg " placeholder="パスワード" value="" />
								<div class="invalid-feedback"></div>
							</div>
							<div class="checkbox checkbox-css m-b-30">
								<div class="checkbox checkbox-css m-b-30">
									<input type="checkbox" id="auto_login" name="auto_login" value="1" />
									<label for="auto_login">
										ログイン情報を記憶する
									</label>
								</div>
							</div>
							<div class="login-buttons">
								<input type="submit" class="btn btn-success btn-block btn-lg" value="ログイン">
							</div>
							<hr />
							<p class="text-center text-grey-darker">
								&copy;2019 SENSE SHARE All Rights Reserved.</p>

							<input type="hidden" name="token" value="<?php echo h($_SESSION['sstoken']); ?>" />
						</form>
					</div>
					<!-- end login-content -->
				</div>
				<!-- end right-container -->
			</div>
			<!-- end login -->
		</div>
		<!-- end page container -->
		<!-- ================== BEGIN BASE JS ================== -->
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/jquery/jquery-3.3.1.min.js"></script>
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
		<!--[if lt IE 9]>
		  <script src="<?php echo CONTENTS_SERVER_URL ?>/assets/crossbrowserjs/html5shiv.js"></script>
		  <script src="<?php echo CONTENTS_SERVER_URL ?>/assets/crossbrowserjs/respond.min.js"></script>
		  <script src="<?php echo CONTENTS_SERVER_URL ?>/assets/crossbrowserjs/excanvas.min.js"></script>
		<![endif]-->
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/plugins/js-cookie/js.cookie.js"></script>
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/js/theme/default.min.js"></script>
		<script src="<?php echo CONTENTS_SERVER_URL ?>/assets/js/apps.min.js"></script>
		<!-- ================== END BASE JS ================== -->

		<script>
		  $(document).ready(function() {
		    App.init();
		  });
		</script>
</body>
</html>
