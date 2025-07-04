<?php
require_once(dirname(__FILE__) . '/../../../functions/require.php');

$err = [];
$client_id = $_SESSION['CLIENT']['id'];
$pdo = connectDb();
$data = [];

// 初期表示データ取得
$stmt = $pdo->prepare("SELECT * FROM blog WHERE client_id = :client_id");
$stmt->execute([':client_id' => $client_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $blog_title = $_POST['blog_title'] ?? '';
  $blog_description = $_POST['blog_description'] ?? '';
  $blog_keywords = $_POST['blog_keywords'] ?? '';
  $blog_author_name = $_POST['blog_author_name'] ?? '';
  $analytics_ua_code = $_POST['analytics_ua_code'] ?? '';

  //  // バリデーション
  //   if ($blog_title === '') {
  //     $err['blog_title'] = 'ブログタイトルを入力してください。';
  //   }

  //   if ($blog_description === '') {
  //     $err['blog_description'] = '説明文を入力してください。';
  //   }

  //   if ($blog_keywords !== '') {
  //     $err['blog_keywords'] = 'キーワードを入力してください。';
  //   }

  //   if ($blog_author_name === '') {
  //     $err['blog_author_name'] = '投稿者名を入力してください。';
  //   }

  // バリデーション
  if ($blog_title === '' || mb_strlen($blog_title) < 22 || mb_strlen($blog_title) > 32) {
    $err['blog_title'] = 'ブログタイトルは22〜32文字で入力してください。';
  }

  if ($blog_description === '' || mb_strlen($blog_description) < 80 || mb_strlen($blog_description) > 120) {
    $err['blog_description'] = '説明は80〜120文字で入力してください。';
  }

  if ($blog_keywords !== '' && mb_strlen($blog_keywords) > 50) {
    $err['blog_keywords'] = 'キーワードは50文字以内で入力してください。';
  }

  if ($blog_author_name === '') {
    $err['blog_author_name'] = '投稿者名は必須です。';
  }

  $blog_header_image = file_upload('blog_header_image', $err);
  $blog_favicon_image = file_upload('blog_favicon_image', $err);
  $blog_favicon180_image = file_upload('blog_favicon180_image', $err);
  $blog_default_eye_catch_image = file_upload('blog_default_eye_catch_image', $err);

  if (empty($err)) {
    try {
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

      // $stmt->execute([
      //   ':blog_title' => $blog_title,
      //   ':blog_description' => $blog_description,
      //   ':blog_keywords' => $blog_keywords,
      //   ':blog_author_name' => $blog_author_name,
      //   ':blog_header_image' => $blog_header_image['file'] ?? null,
      //   ':blog_header_image_ext' => $blog_header_image['ext'] ?? null,
      //   ':blog_favicon_image' => $blog_favicon_image['file'] ?? null,
      //   ':blog_favicon_image_ext' => $blog_favicon_image['ext'] ?? null,
      //   ':blog_favicon180_image' => $blog_favicon180_image['file'] ?? null,
      //   ':blog_favicon180_image_ext' => $blog_favicon180_image['ext'] ?? null,
      //   ':blog_default_eye_catch_image' => $blog_default_eye_catch_image['file'] ?? null,
      //   ':blog_default_eye_catch_image_ext' => $blog_default_eye_catch_image['ext'] ?? null,
      //   ':analytics_ua_code' => $analytics_ua_code,
      //   ':client_id' => $client_id
      // ]);

      $stmt->bindValue(':blog_title', $blog_title, PDO::PARAM_STR);
      $stmt->bindValue(':blog_description', $blog_description, PDO::PARAM_STR);
      $stmt->bindValue(':blog_keywords', $blog_keywords, PDO::PARAM_STR);
      $stmt->bindValue(':blog_author_name', $blog_author_name, PDO::PARAM_STR);
      $stmt->bindValue(':blog_header_image_ext', $blog_header_image['ext'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':blog_favicon_image_ext', $blog_favicon_image['ext'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':blog_favicon180_image_ext', $blog_favicon180_image['ext'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':blog_default_eye_catch_image_ext', $blog_default_eye_catch_image['ext'] ?? null, PDO::PARAM_STR);
      $stmt->bindValue(':analytics_ua_code', $analytics_ua_code, PDO::PARAM_STR);
      $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
      $stmt->bindValue(':blog_header_image', $blog_header_image['file'] ?? null, PDO::PARAM_LOB);
      $stmt->bindValue(':blog_favicon_image', $blog_favicon_image['file'] ?? null, PDO::PARAM_LOB);
      $stmt->bindValue(':blog_favicon180_image', $blog_favicon180_image['file'] ?? null, PDO::PARAM_LOB);
      $stmt->bindValue(':blog_default_eye_catch_image', $blog_default_eye_catch_image['file'] ?? null, PDO::PARAM_LOB);

      $stmt->execute();

      header('Location: ?success=1');
      // header('Location: ' . SITE_URL . 'blog/setting/?success=1');
      exit;
    } catch (PDOException $e) {
      echo "エラー: " . $e->getMessage();
    }
  }
  // POST値をフォーム再表示用に上書き
  $data = array_merge($data, $_POST);
}
?>


<?php include(TEMPLATE_PATH . "/template_head.php"); ?>
<!-- begin page-header -->
<h1 class="page-header">ブログ基本設定</h1>
<!-- end page-header -->

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">設定が保存されました！</div>
<?php endif; ?>

<form method="POST" class="form-horizontal form-bordered" id="mainForm" enctype="multipart/form-data">
  <!-- begin panel -->
  <div class="panel panel-inverse">
    <!-- begin panel-body -->
    <div class="panel-body panel-form">

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログタイトル（22-32文字）</label>
        <div class="col-md-10">
          <input name="blog_title" type="text" class="form-control <?= isset($err['blog_title']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars($data['blog_title'] ?? '') ?>" />
          <?php if (isset($err['blog_title'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_title'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログの説明（80-120文字）</label>
        <div class="col-md-10">
          <textarea class="form-control <?= isset($err['blog_description']) ? 'is-invalid' : '' ?>"
            name="blog_description" rows="10"><?= htmlspecialchars($data['blog_description'] ?? '') ?></textarea>
          <?php if (isset($err['blog_description'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_description'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ブログのキーワード（50文字以内）</label>
        <div class="col-md-10">
          <input name="blog_keywords" type="text"
            class="form-control <?= isset($err['blog_keywords']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars($data['blog_keywords'] ?? '') ?>" />
          <?php if (isset($err['blog_keywords'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_keywords'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">投稿者名</label>
        <div class="col-md-10">
          <input name="blog_author_name" type="text"
            class="form-control <?= isset($err['blog_author_name']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars($data['blog_author_name'] ?? '') ?>" />
          <?php if (isset($err['blog_author_name'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_author_name'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">Google Analyticsコード</label>
        <div class="col-md-10">
          <input name="analytics_ua_code" type="text"
            class="form-control <?= isset($err['analytics_ua_code']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars($data['analytics_ua_code'] ?? '') ?>" />
          <?php if (isset($err['analytics_ua_code'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['analytics_ua_code'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">ヘッダーイメージ（1200px*260px）</label>
        <div class="col-md-10">

          <?php if (!empty($data['blog_header_image'])): ?>
            <img
              src="<?php echo get_base64_header_string($data['blog_header_image_ext']) ?><?php echo base64_encode($data['blog_header_image']) ?>"
              alt="header" class="width-full m-b-10 img-responsive">
          <?php endif; ?>

          <input name="blog_header_image" type="file"
            class="form-control <?= isset($err['blog_header_image']) ? 'is-invalid' : '' ?>" />
          <?php if (isset($err['blog_header_image'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_header_image'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">favicon.ico</label>
        <div class="col-md-10">
          <?php if (!empty($data['blog_favicon_image'])): ?>
            <img
              src="<?php echo get_base64_header_string($data['blog_favicon_image_ext']) ?><?php echo base64_encode($data['blog_favicon_image']) ?>"
              alt="favicon" class="m-b-10" width="40px">
          <?php endif; ?>
          <input name="blog_favicon_image" type="file"
            class="form-control <?= isset($err['blog_favicon_image']) ? 'is-invalid' : '' ?>" />
          <?php if (isset($err['blog_favicon_image'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_favicon_image'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">apple-touch-icon-180x180.png</label>
        <div class="col-md-10">
          <?php if (!empty($data['blog_favicon180_image'])): ?>
            <img
              src="<?php echo get_base64_header_string($data['blog_favicon180_image_ext']) ?><?php echo base64_encode($data['blog_favicon180_image']) ?>"
              alt="apple-touch-icon" class="m-b-10" width="40px">
          <?php endif; ?>
          <input name="blog_favicon180_image" type="file"
            class="form-control <?= isset($err['blog_favicon180_image']) ? 'is-invalid' : '' ?>" />
          <?php if (isset($err['blog_favicon180_image'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_favicon180_image'] ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-2 col-form-label">デフォルトアイキャッチ画像（1200px*630px）</label>
        <div class="col-md-10">
          <?php if (!empty($data['blog_default_eye_catch_image'])): ?>
            <img
              src="<?php echo get_base64_header_string($data['blog_default_eye_catch_image_ext']) ?><?php echo base64_encode($data['blog_default_eye_catch_image']) ?>"
              alt="default-eye-catch" class="m-b-10" width="300px">
          <?php endif; ?>
          <input name="blog_default_eye_catch_image" type="file"
            class="form-control <?= isset($err['blog_default_eye_catch_image']) ? 'is-invalid' : '' ?>" />
          <?php if (isset($err['blog_default_eye_catch_image'])): ?>
            <div class="invalid-feedback text-danger"><?= $err['blog_default_eye_catch_image'] ?></div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <div class="wrapper bg-silver text-right">
    <a href="https://demo.flu-x.net"><button type="button" class="btn btn-white p-l-40 p-r-40 m-r-5">キャンセル</button></a>
    <button type="submit" class="btn btn-primary p-l-40 p-r-40" onclick="mainForm.submit();">登録</button>
  </div>

  <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
  <input type="hidden" name="FLUXDEMOTOKEN" value="f8e2503d3eadb534309cba761c8a4cc0a6bddeff" />
</form>

<?php include(TEMPLATE_PATH . "/template_bottom.php"); ?>