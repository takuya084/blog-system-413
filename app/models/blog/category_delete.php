test

<?php
require_once(dirname(__FILE__).'/../../../functions/require.php');
session_start();
checkToken(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_category_code = isset($_POST['blog_category_code']) ? $_POST['blog_category_code'] : null;

    if ($blog_category_code) {
        $client_id = $_SESSION['CLIENT']['id'];

        $pdo = connectDb();
        $sql = "DELETE FROM blog_category_master WHERE blog_category_code = :blog_category_code AND client_id = :client_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':blog_category_code' => $blog_category_code,
            ':client_id' => $client_id
        ]);

        header('Location: /blog/category/');
        exit();
    }
}

header('Location: /blog/category/');
exit();