<?php
	$client_id = $_SESSION['CLIENT']['id'];

	// blog_id を取得
	$pdo = connectDb();
	$sql = "SELECT id FROM blog WHERE client_id = :client_id LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([':client_id' => $client_id]);
	$blog_id = $stmt->fetchColumn();

	// 検索キーワードを取得
	$search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';

 	// カテゴリー情報を取得
	$sql = "SELECT id, category_name, blog_category_code, blog_category_slug, sort_order 
	FROM blog_category_master 
	WHERE client_id = :client_id AND blog_id = :blog_id";
	
	// 検索キーワードが指定されている場合
	if (!empty($search_keyword)) {
			$sql .= " AND category_name LIKE :search_keyword";
	}	
	// 並び順の指定
	$sql .= " ORDER BY sort_order, created_at";
	
	$stmt = $pdo->prepare($sql);

	$params = [
			':client_id' => $client_id,
			':blog_id' => $blog_id,
	];
	// 検索キーワードが指定されている場合
	if (!empty($search_keyword)) {
			$params[':search_keyword'] = '%' . $search_keyword . '%';
	}

	$stmt->execute($params);
	$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include(TEMPLATE_PATH."/template_head.php"); ?>
<!-- begin page-header -->
	<h1 class="page-header">ブログカテゴリー <small>header small text goes here...</small></h1>
<!-- end page-header -->

<form method="GET" id="mainform">

	<!-- begin row -->
	<div class="row">
		<!-- begin col-12 -->
		<div class="col-md-12">
			<!-- begin result-container -->
			<div class="result-container">

				<!-- begin input-group -->
<div class="input-group input-group-lg m-b-20">
	<input type="text" id="search_keyword" name="search_keyword" class="form-control input-white" placeholder="検索キーワードを入力してください。" value="<?php echo h($search_keyword); ?>" />
	<div class="input-group-append">
		<button type="submit" class="btn btn-primary"><i class="fa fa-search fa-fw"></i></button>
	</div>
</div>
<!-- end input-group -->


				<div class="width-150 pull-right m-b-10">
					<a href="/blog/category_entry/" class="btn btn-inverse btn-block">新規作成</a>
				</div>

				<!-- begin panel -->
				<div class="panel" style="clear:both">
					<div class="panel-body panel-form">
						<table class="table table-bordered table-valign-middle m-b-0">
							<thead>
								<tr class="bg-inverse">
									<th class="text-center text-white">カテゴリー名</th>
									<th class="width-300 text-center text-white">スラッグ</th>
									<th class="width-80 text-center text-white">記事数</th>
									<th class="width-150 text-center text-white">表示順序</th>
									<th class="width-150 text-center text-white"></th>
								</tr>
							</thead>
							<tbody>


								<?php foreach ($categories as $category): ?>
										<tr id="item_<?php echo h($category['blog_category_code']); ?>">
												<td><?php echo h($category['category_name']); ?></td>
												<td><?php echo h($category['blog_category_slug']); ?></td>
												<td class="text-center"></td>
												<td class="text-center"><?php echo h($category['sort_order']); ?></td>
												<td class="text-center">
														<a href="/blog/category_entry/?code=<?php echo h($category['blog_category_code']); ?>" class="btn btn-primary">編集</a>
														<a href="#" data-id="<?php echo h($category['blog_category_code']); ?>" class="btn btn-danger" data-click="delete-confirm">削除</a>


												</td>
										</tr>
								<?php endforeach; ?>


							</tbody>
						</table>
					</div>
				</div>
				<!-- end panel -->

			</div>
			<!-- end result-container -->
		</div>
		<!-- end col-12 -->
	</div>
	<!-- end row -->
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-click="delete-confirm"]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (confirm('本当に削除しますか？')) {
                var blog_category_code = this.getAttribute('data-id');
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/blog/category_delete/';

                // CSRFトークンを追加
                var tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = 'token';
                tokenInput.value = '<?php echo h($_SESSION['sstoken']); ?>';
                form.appendChild(tokenInput);

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'blog_category_code';
                input.value = blog_category_code;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
<?php include(TEMPLATE_PATH."/template_bottom.php"); ?>
