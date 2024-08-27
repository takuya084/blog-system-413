<?php
require_once(dirname(__FILE__).'/../../../functions/require.php');
session_start();

// ログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['CLIENT']['id'])) {
    header('Location: /login.php');
    exit();
}

$client_id = $_SESSION['CLIENT']['id'];  // セッションから client_id を取得

$err = array();
$category_name = '';
$blog_category_slug = '';
$sort_order = '';

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

    if (empty($err)) {
        $pdo = connectDb();
        
        // カテゴリーコードの取得と更新
        $sql = "SELECT sequence FROM blog_category_code_sequence WHERE client_id = :client_id AND blog_id = :blog_id FOR UPDATE";
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

        // カテゴリーの挿入
        $sql = "INSERT INTO blog_category_master (blog_category_code, blog_category_slug, category_name, sort_order, created_at, updated_at) 
                VALUES (:blog_category_code, :blog_category_slug, :category_name, :sort_order, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':blog_category_code' => $blog_category_code,
            ':blog_category_slug' => $blog_category_slug,
            ':category_name' => $category_name,
            ':sort_order' => $sort_order
        ]);
        header('Location: /blog/category/');
        exit();
    }
}
?>

<?php include(TEMPLATE_PATH . "/template_head.php"); ?>
<h1 class="page-header">ブログカテゴリー登録</h1>
<form method="POST" class="form-horizontal form-bordered" id="mainform">
    <div class="panel panel-inverse">
        <div class="panel-body panel-form">
            <div class="form-group row">
                <label class="col-md-2 col-form-label">カテゴリー名</label>
                <div class="col-md-10">
                    <input name="category_name" type="text" class="form-control" value="<?php echo h($category_name); ?>" />
                    <?php echo h($err['category_name'] ?? ''); ?>
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
        <button type="submit" class="btn btn-primary">登録</button>
    </div>
    <input type="hidden" name="token" value="<?php echo h($_SESSION['sstoken']) ?>" />
</form>
<?php include(TEMPLATE_PATH . "/template_bottom.php"); ?>
