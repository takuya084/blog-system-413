<?php
require_once(dirname(__FILE__).'/../../../functions/require.php');

session_start();

$err = array();

$client_name ='';
$mail_address ='';
$password ='';
$invitation_code ='';
$agreement_checkbox = '';

if($_SERVER['REQUEST_METHOD'] !='POST'){
	setToken();
}else{
	checkToken();

	$client_name =  $_POST['client_name'];
	$mail_address =  $_POST['mail_address'];
	$password =  $_POST['password'];
	$invitation_code =  $_POST['invitation_code'];
	$agreement_checkbox =  $_POST['agreement_checkbox'];
	$pdo  = connectDb();

	if($client_name == ''){
		$err['client_name'] = 'アカウント名を入力してください。';
	}

	if($mail_address == ''){
		$err['mail_address'] = 'メールアドレスを入力して下さい。';
	}else{
		if(!filter_var($mail_address, FILTER_VALIDATE_EMAIL)){
			$err['mail_address'] = 'メールアドレスが不正です。';
		}else{
			if(checkEmail($mail_address, $pdo)){
				$err['mail_address'] = 'このメールアドレスは既に登録されています。';
			}
		}
	}

	if($password == ''){
		$err['password'] = 'パスワードを入力してください。';
	}

	if($invitation_code == ''){
		$err['invitation_code'] = '招待コードを入力してください。';
	}else{
		if($invitation_code != 'BLOG_SYSTEM'){
			$err['invitation_code'] = '招待コードが無効です。';
		}
	}

	if($agreement_checkbox == 'NULL'){
		$err['agreement_checkbox'] = '規約の同意にチェックをしてください。';
	}


	if(empty($err)){
		$sql = "insert into client
				(status,client_code,client_name,mail_address,password,created_at,updated_at)
				values
				(1,:client_code,:client_name,:mail_address,:password,now(),now())";
		$stmt = $pdo->prepare($sql);
		$client_code = client_code();
		$stmt->bindValue(':client_code',$client_code);
		$stmt->bindValue(':client_name',$client_name);
		$stmt->bindValue(':mail_address',$mail_address);
		$stmt->bindValue(':password',$password);
		$stmt->execute();

		$client_id = (int)getClientId($mail_address, $password, $pdo);
		$sql = "insert into blog
				(status,client_id,created_at,updated_at)
				values
				(1,:client_id,now(),now())";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':client_id',$client_id);
		$stmt->execute();

		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		$mail_title = '新規ユーザー登録がありました';
		$mail_body = $client_name.PHP_EOL;
		$mail_body.= $mail_address;
		mb_send_mail(ADMIN_MAIL_ADDRESS,$mail_title,$mail_body);

		header('Location:'.SITE_URL.'/blog/');
		unset($pdo);
		exit;
	}
}

?>


<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>Color Admin | Register Page</title>
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
</head>
<body class="pace-top bg-white">
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show"><span class="spinner"></span></div>
	<!-- end #page-loader -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade">
		<!-- begin register -->
		<div class="register register-with-news-feed">
			<!-- begin news-feed -->
			<div class="news-feed">
				<div class="news-image" style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/login-bg/login-bg-9.jpg)"></div>
				<div class="news-caption">
					<h4 class="caption-title"><b>BLOG SYSTEM</b></h4>
					<p>
						Blog System Demonstration
					</p>
				</div>
			</div>
			<!-- end news-feed -->
			<!-- begin right-content -->
			<div class="right-content">
				<!-- begin register-header -->
				<h1 class="register-header">
					アカウント作成
					<small>Create your FLUX Account.</small>
				</h1>
				<!-- end register-header -->
				<!-- begin register-content -->
				<div class="register-content">
					<form action="" method="POST" class="margin-bottom-0">
						<div class="form-group <?php if(isset($err['client_name']) && $err['client_name'] != '') echo 'has-error'; ?>"
							<label class="control-label">アカウント名 <span class="text-danger">*</span></label>
							<div class="row m-b-15">
								<div class="col-md-12">
									<input type="text" class="form-control" name="client_name" value="<?php echo h($client_name) ?>" placeholder="" required />
									<span class="help-block"><?php if(isset($err['client_name'])) echo h($err['client_name']); ?></span>
								</div>
							</div>
						</div>

						<div class="form-group <?php if(isset($err['mail_address']) && $err['mail_address'] != '') echo 'has-error'; ?>"
							<label class="control-label">メールアドレス <span class="text-danger">*</span></label>
							<div class="row m-b-15">
								<div class="col-md-12">
									<input type="email" class="form-control" name="mail_address" value="<?php echo h($mail_address) ?>" placeholder="" required />
									<span class="help-block"><?php if(isset($err['mail_address'])) echo h($err['mail_address']); ?></span>
								</div>
							</div>
						</div>

						<div class="form-group <?php if(isset($err['password']) && $err['password'] !='') echo 'has-error'; ?>"
							<label class="control-label">パスワード  <span class="text-danger">*</span></label>
							<div class="row m-b-15">
								<div class="col-md-6">
									<input type="password" class="form-control" name="password" placeholder="8文字以上" required />
									<span class="help-block"><?php if(isset($err['password'])) echo h($err['password']);?></span>
								</div>
								<div class="col-md-6">
									<input type="password" class="form-control" placeholder="再入力" required />
								</div>
							</div>
						</div>

						<div class="form-group <?php if(isset($err['invitation_code']) && $err['invitation_code'] !='') echo 'has-error'; ?>"
							<label class="control-label">招待コード <span class="text-danger">*</span></label>
							<div class="row m-b-15">
								<div class="col-md-12">
									<input type="password" class="form-control" name="invitation_code" placeholder="招待コードをお持ちの方のみがご登録頂けます。" required />
									<span class="help-block"><?php if(isset($err['invitation_code'])) echo h($err['invitation_code']);?></span>
								</div>
							</div>
						</div>

						<div class="form-group <?php if(isset($err['agreement_checkbox']) && $err['agreement_checkbox'] !='') echo 'has-error'; ?>"
							<div class="checkbox checkbox-css m-b-30">
								<div class="checkbox checkbox-css m-b-30">
									<input type="checkbox" id="agreement_checkbox" name="agreement_checkbox"　value="">
									<label for="agreement_checkbox">
										<a href="javascript:;">利用規約</a> 及び <a href="javascript:;">プライバシーポリシー</a>に同意します。
									</label>
									<span class="help-block"><?php if(isset($err['agreement_checkbox'])) echo h($err['agreement_checkbox']);?></span>
								</div>
							</div>
							<div class="register-buttons">
								<input type="submit" class="btn btn-primary btn-block btn-lg" value="アカウント作成">
							</div>
							<div class="m-t-20 m-b-40 p-b-40 text-inverse">
								既にアカウントをお持ちの方は<a href="login.php">こちら</a>
							</div>
							<hr />
							<p class="text-center">
								&copy;2020 SENSE SHARE All Rights Reserved.
							</p>
						</div>
						<input type="hidden" name="token" value="<?php echo h($_SESSION['sstoken']); ?>" />
					</form>
				</div>
				<!-- end register-content -->
			</div>
			<!-- end right-content -->
		</div>
		<!-- end register -->

		<!-- begin theme-panel -->
		<div class="theme-panel theme-panel-lg">
			<a href="javascript:;" data-click="theme-panel-expand" class="theme-collapse-btn"><i class="fa fa-cog"></i></a>
			<div class="theme-panel-content">
				<h5 class="m-t-0">Color Theme</h5>
				<ul class="theme-list clearfix">
					<li><a href="javascript:;" class="bg-red" data-theme="red" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/red.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Red">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-pink" data-theme="pink" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/pink.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Pink">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-orange" data-theme="orange" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/orange.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Orange">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-yellow" data-theme="yellow" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/yellow.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Yellow">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-lime" data-theme="lime" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/lime.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Lime">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-green" data-theme="green" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/green.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Green">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-teal" data-theme="default" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/default.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Default">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-aqua" data-theme="aqua" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/aqua.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Aqua">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-blue" data-theme="blue" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/blue.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Blue">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-purple" data-theme="purple" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/purple.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Purple">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-indigo" data-theme="indigo" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/indigo.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Indigo">&nbsp;</a></li>
					<li><a href="javascript:;" class="bg-black" data-theme="black" data-theme-file="<?php echo CONTENTS_SERVER_URL ?>/assets/css/default/theme/black.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Black">&nbsp;</a></li>
				</ul>
				<div class="divider"></div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Header Styling</div>
					<div class="col-md-6">
						<select name="header-styling" class="form-control form-control-sm">
							<option value="1">default</option>
							<option value="2">inverse</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Header</div>
					<div class="col-md-6">
						<select name="header-fixed" class="form-control form-control-sm">
							<option value="1">fixed</option>
							<option value="2">default</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Sidebar Styling</div>
					<div class="col-md-6">
						<select name="sidebar-styling" class="form-control form-control-sm">
							<option value="1">default</option>
							<option value="2">grid</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Sidebar</div>
					<div class="col-md-6">
						<select name="sidebar-fixed" class="form-control form-control-sm">
							<option value="1">fixed</option>
							<option value="2">default</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Sidebar Gradient</div>
					<div class="col-md-6">
						<select name="content-gradient" class="form-control form-control-sm">
							<option value="1">disabled</option>
							<option value="2">enabled</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Content Styling</div>
					<div class="col-md-6">
						<select name="content-styling" class="form-control form-control-sm">
							<option value="1">default</option>
							<option value="2">black</option>
						</select>
					</div>
				</div>
				<div class="row m-t-10">
					<div class="col-md-6 control-label text-inverse f-w-600">Direction</div>
					<div class="col-md-6">
						<select name="direction" class="form-control form-control-sm">
							<option value="1">LTR</option>
							<option value="2">RTL</option>
						</select>
					</div>
				</div>
				<div class="divider"></div>
				<h5>THEME VERSION</h5>
				<div class="theme-version">
					<a href="../template_html/index_v2.html" class="active">
						<span style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/theme/default.jpg);"></span>
					</a>
					<a href="../template_transparent/index_v2.html">
						<span style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/theme/transparent.jpg);"></span>
					</a>
				</div>
				<div class="theme-version">
					<a href="../template_apple/index_v2.html">
						<span style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/theme/apple.jpg);"></span>
					</a>
					<a href="../template_material/index_v2.html">
						<span style="background-image: url(<?php echo CONTENTS_SERVER_URL ?>/assets/img/theme/material.jpg);"></span>
					</a>
				</div>
				<div class="divider"></div>
				<div class="row m-t-10">
					<div class="col-md-12">
						<a href="javascript:;" class="btn btn-inverse btn-block btn-rounded" data-click="reset-local-storage"><b>Reset Local Storage</b></a>
					</div>
				</div>
			</div>
		</div>
		<!-- end theme-panel -->
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
