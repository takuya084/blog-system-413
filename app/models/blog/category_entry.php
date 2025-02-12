<?php
$client_id = $_SESSION['CLIENT']['id'];
// blog_id を取得
$pdo = connectDb();
$sql = "SELECT id FROM blog WHERE client_id = :client_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':client_id' => $client_id]);
$blog_id = $stmt->fetchColumn(); 

$err = array();
$category_name = '';
$blog_category_slug = '';
$sort_order = '';
//※blog_category_code：カテゴリーデータの通し番号
$blog_category_code = '';

// 編集機能
if (isset($_GET['code'])) {
    $blog_category_code = $_GET['code'];

    // ※blog_category_master：カテゴリー名を管理
    $sql = "SELECT * FROM blog_category_master WHERE client_id = :client_id AND blog_id = :blog_id AND blog_category_code = :blog_category_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':client_id' => $client_id,
        ':blog_id' => $blog_id,
        ':blog_category_code' => $blog_category_code,
    ]);
    
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $category_name = $category['category_name'];
        $blog_category_slug = $category['blog_category_slug'];
        $sort_order = $category['sort_order'];
    } 
}


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    setToken();
} else {
    checkToken();
    $category_name = $_POST['category_name'];
    $blog_category_slug = $_POST['blog_category_slug'];
    $sort_order = $_POST['sort_order'];

    if ($category_name == '') {
        $err['category_name'] = 'カテゴリー名を入力してください。';
    }

    if ($blog_category_slug != '') {
        // スラッグの重複チェック
        $sql = "SELECT COUNT(*) FROM blog_category_master WHERE blog_category_slug = :blog_category_slug";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':blog_category_slug' => $blog_category_slug]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $err['blog_category_slug'] = 'このスラッグは既に存在します。';
        }
    }

    if (empty($err)) {
        if ($blog_category_code) {
            // 編集処理
            $sql = "UPDATE blog_category_master 
                    SET category_name = :category_name, blog_category_slug = :blog_category_slug, sort_order = :sort_order, updated_at = NOW()
                    WHERE client_id = :client_id AND blog_id = :blog_id AND blog_category_code = :blog_category_code";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':client_id' => $client_id,
                ':blog_id' => $blog_id,
                ':category_name' => $category_name,
                ':blog_category_slug' => $blog_category_slug,
                ':sort_order' => $sort_order,
                ':blog_category_code' => $blog_category_code
            ]);
        } else {        
            // 同一クライアント＆ブログ内限定での通し番号を別途付与
            $sql = "SELECT sequence FROM blog_category_code_sequence WHERE client_id = :client_id AND blog_id = :blog_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':client_id' => $client_id, ':blog_id' => $blog_id]);
            $sequence = $stmt->fetchColumn();
            
            if ($sequence === false) {
                $sequence = 1;
                $sql = "INSERT INTO blog_category_code_sequence (client_id, blog_id, sequence, created_at, updated_at) VALUES (:client_id, :blog_id, :sequence, NOW(), NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':client_id' => $client_id, ':blog_id' => $blog_id, ':sequence' => $sequence]);
            } else {
                $sequence++;
                $sql = "UPDATE blog_category_code_sequence SET sequence = :sequence WHERE client_id = :client_id AND blog_id = :blog_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':sequence' => $sequence, ':client_id' => $client_id, ':blog_id' => $blog_id]);
            }
            
            $blog_category_code = $sequence;


            $sql = "INSERT INTO blog_category_master (client_id, blog_id, blog_category_code, blog_category_slug, category_name, sort_order, created_at, updated_at) 
                    VALUES (:client_id, :blog_id, :blog_category_code, :blog_category_slug, :category_name, :sort_order, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':client_id' => $client_id,
                ':blog_id' => $blog_id,
                ':blog_category_code' => $blog_category_code,
                ':blog_category_slug' => $blog_category_slug,
                ':category_name' => $category_name,
                ':sort_order' => $sort_order
            ]);
        }
        header('Location: /blog/category/');
        exit();
    }
}
?>

<?php include(TEMPLATE_PATH . "/template_head.php"); ?>
<h1 class="page-header"><?php echo $blog_category_code ? 'ブログカテゴリー編集' : 'ブログカテゴリー登録'; ?></h1>
<form method="POST" class="form-horizontal form-bordered" id="mainform">
    <div class="panel panel-inverse">
        <div class="panel-body panel-form">

            <div class="form-group row <?php if(isset($err['category_name']) && $err['category_name'] != '') echo 'has-error'; ?>">
                <label class="col-md-2 col-form-label">カテゴリー名</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" name="category_name" value="<?php echo h($category_name) ?>"/>
					<span class="help-block"><?php if(isset($err['category_name'])) echo h($err['category_name']); ?></span>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">スラッグ</label>
                <div class="col-md-10">
                    <input name="blog_category_slug" type="text" class="form-control" value="<?php echo h($blog_category_slug); ?>" />
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">表示順序</label>
                <div class="col-md-10">
                    <input name="sort_order" type="text" class="form-control" value="<?php echo h($sort_order); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="wrapper bg-silver text-right">
        <a href="/blog/category/"><button type="button" class="btn btn-white">キャンセル</button></a>
        <button type="submit" class="btn btn-primary"><?php echo $blog_category_code ? '更新' : '登録'; ?></button>
    </div>
    <input type="hidden" name="token" value="<?php echo h($_SESSION['sstoken']) ?>" />
</form>
<?php include(TEMPLATE_PATH . "/template_bottom.php"); ?>
