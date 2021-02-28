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
	<input type="text" id="search_keyword" name="search_keyword" class="form-control input-white" placeholder="検索キーワードを入力してください。" value="" />
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
									<th class="width-150 text-center text-white"></th>
								</tr>
							</thead>
							<tbody>
																<tr id="item_60">
									<td>プログラマーを知る</td>
									<td></td>
									<td class="text-center">2</td>

									<td class="text-center">
										<a href="/blog/category_entry/?code=60" class="btn btn-primary">編集</a>
										<a href="javascript:;" class="btn btn-danger" data-id="60" data-click="delete-confirm">削除</a>
									</td>
								</tr>
																<tr id="item_61">
									<td>プログラマーになる方法</td>
									<td></td>
									<td class="text-center">1</td>

									<td class="text-center">
										<a href="/blog/category_entry/?code=61" class="btn btn-primary">編集</a>
										<a href="javascript:;" class="btn btn-danger" data-id="61" data-click="delete-confirm">削除</a>
									</td>
								</tr>
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

<?php include(TEMPLATE_PATH."/template_bottom.php"); ?>
