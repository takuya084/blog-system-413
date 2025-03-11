<?php
require_once(dirname(__FILE__).'/../../../functions/require.php');

$err = [];

$client_id = $_SESSION['CLIENT']['id'];
$blog_title = $_POST['blog_title'] ?? '';
$blog_description = $_POST['blog_description'] ?? '';
$blog_keywords = $_POST['blog_keywords'] ?? '';
$blog_author_name = $_POST['blog_author_name'] ?? '';
$analytics_ua_code = $_POST['analytics_ua_code'] ?? '';

$blog_header_image = file_upload('blog_header_image', $err);
$blog_favicon_image = file_upload('blog_favicon_image', $err);
$blog_favicon180_image = file_upload('blog_favicon180_image', $err);
$blog_default_eye_catch_image = file_upload('blog_default_eye_catch_image', $err);

try {
	$pdo = connectDb();

$stmt = $pdo->prepare("UPDATE blog SET
        blog_title = :blog_title,
        blog_description = :blog_description,
        blog_keywords = :blog_keywords,
        blog_author_name = :blog_author_name,
        blog_header_image = :blog_header_image,
        blog_header_image_ext = :blog_header_image_ext,
        blog_favicon_image = :blog_favicon_image,
        blog_favicon_image_ext = :blog_favicon_image_ext,
        blog_favicon180_image = :blog_favicon180_image,
        blog_favicon180_image_ext = :blog_favicon180_image_ext,
        blog_default_eye_catch_image = :blog_default_eye_catch_image,
        blog_default_eye_catch_image_ext = :blog_default_eye_catch_image_ext,
        analytics_ua_code = :analytics_ua_code,
        updated_at = NOW()
        WHERE client_id = :client_id"); 

  $stmt->execute([
        ':blog_title' => $blog_title,
        ':blog_description' => $blog_description,
        ':blog_keywords' => $blog_keywords,
        ':blog_author_name' => $blog_author_name,
        ':blog_header_image' => $blog_header_image['file'] ?? null,
        ':blog_header_image_ext' => $blog_header_image['ext'] ?? null,
        ':blog_favicon_image' => $blog_favicon_image['file'] ?? null,
        ':blog_favicon_image_ext' => $blog_favicon_image['ext'] ?? null,
        ':blog_favicon180_image' => $blog_favicon180_image['file'] ?? null,
        ':blog_favicon180_image_ext' => $blog_favicon180_image['ext'] ?? null,
        ':blog_default_eye_catch_image' => $blog_default_eye_catch_image['file'] ?? null,
        ':blog_default_eye_catch_image_ext' => $blog_default_eye_catch_image['ext'] ?? null,
        ':analytics_ua_code' => $analytics_ua_code,
				':client_id' => $client_id
    ]);

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>


<?php include(TEMPLATE_PATH."/template_head.php"); ?>
<!-- begin page-header -->
<h1 class="page-header">ブログ基本設定</h1>
<!-- end page-header -->

<form method="POST" class="form-horizontal form-bordered" id="mainForm" enctype="multipart/form-data">
  <!-- begin panel -->
  <div class="panel panel-inverse">
    <!-- begin panel-body -->
    <div class="panel-body panel-form">
      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログタイトル（22-32文字）</label>
        <div class="col-md-10">
          <input name="blog_title" type="text" class="form-control " value="初心者でも自宅で楽しく学べるハルジオン式プログラミング入門講座" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログの説明（80-120文字）</label>
        <div class="col-md-10">
          <textarea class="form-control " name="blog_description"
            rows="10">現役プログラマーであるハルジオンが独自の視点で分かりやすく教える、初心者向けのオンラインプログラミング講座です。</textarea>
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログのキーワード（50文字以内）</label>
        <div class="col-md-10">
          <input name="blog_keywords" type="text" class="form-control " value="プログラミング入門講座" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">投稿者名</label>
        <div class="col-md-10">
          <input name="blog_author_name" type="text" class="form-control " value="ハルジオン" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">Google Analyticsコード</label>
        <div class="col-md-10">
          <input name="analytics_ua_code" type="text" class="form-control " value="UA-XXXXXX" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ヘッダーイメージ（1200px*260px）</label>
        <div class="col-md-10">
          <img src="" alt="" class="width-full m-b-10 img-responsive">
          <input name="blog_header_image" type="file" class="form-control " value="" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">favicon.ico</label>
        <div class="col-md-10">
          <img src="" alt="" class="m-b-10" width="40px">
          <input name="blog_favicon_image" type="file" class="form-control " value="" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">apple-touch-icon-180x180.png</label>
        <div class="col-md-10">
          <img src="" alt="" class="m-b-10" width="40px">
          <input name="blog_favicon180_image" type="file" class="form-control " value="" />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">デフォルトアイキャッチ画像（1200px*630px）</label>
        <div class="col-md-10">
          <img src="" alt="" class="m-b-10" width="300px">
          <input name="blog_default_eye_catch_image" type="file" class="form-control " value="" />
          <div class="invalid-feedback"></div>
        </div>
      </div>
    </div>
    <!-- end panel-body -->
  </div>
  <!-- end panel -->

  <!-- begin wrapper -->
  <div class="wrapper bg-silver text-right">
    <a href="https://demo.flu-x.net"><button type="button" class="btn btn-white p-l-40 p-r-40 m-r-5">キャンセル</button></a>
    <button type="submit" class="btn btn-primary p-l-40 p-r-40" onclick="mainForm.submit();">登録</button>
  </div>
  <!-- end wrapper -->

  <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
  <input type="hidden" name="FLUXDEMOTOKEN" value="f8e2503d3eadb534309cba761c8a4cc0a6bddeff" />
</form>

<?php include(TEMPLATE_PATH."/template_bottom.php"); ?>