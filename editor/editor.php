<?php
require('../basicsite/init.php');
date_default_timezone_set('America/Toronto');
?>
<html>
	<head>
		<title>Editor</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="squire/squire-ui.css" rel="stylesheet" type="text/css" />
		<link href="imageupload.css" rel="stylesheet" type="text/css" />
		<link type="text/css" rel="stylesheet" href="../css/bootstrap.min.css"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.0/js/bootstrap.min.js" integrity="sha384-7aThvCh9TypR7fIc2HV4O/nFMVCBwyIUKL8XCtKE+8xgCgl/PQGuFsvShjr74PBp" crossorigin="anonymous"></script>
		<script src="looper.js"></script>
		<script src="squire/squire-raw.js"></script>
		<script src="squire/squire-ui.js"></script>
		<script src="imageupload.js"></script>
	</head>
<style>
	form
	{
		position:relative;
	}
	form.processing::after
	{
		content: " ";
		display: block;
		width: 100%;
		height: 100%;
		position: absolute;
		background-color: rgba(255,255,255,0.8);
		top: 0;
		bottom: 0;
		z-index: 999999999;
		background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjQwcHgiIHZpZXdCb3g9IjAgMCA0MCA0MCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWw6c3BhY2U9InByZXNlcnZlIiBzdHlsZT0iZmlsbC1ydWxlOmV2ZW5vZGQ7Y2xpcC1ydWxlOmV2ZW5vZGQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjEuNDE0MjE7IiB4PSIwcHgiIHk9IjBweCI+CiAgICA8ZGVmcz4KICAgICAgICA8c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWwogICAgICAgICAgICBALXdlYmtpdC1rZXlmcmFtZXMgc3BpbiB7CiAgICAgICAgICAgICAgZnJvbSB7CiAgICAgICAgICAgICAgICAtd2Via2l0LXRyYW5zZm9ybTogcm90YXRlKDBkZWcpCiAgICAgICAgICAgICAgfQogICAgICAgICAgICAgIHRvIHsKICAgICAgICAgICAgICAgIC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoLTM1OWRlZykKICAgICAgICAgICAgICB9CiAgICAgICAgICAgIH0KICAgICAgICAgICAgQGtleWZyYW1lcyBzcGluIHsKICAgICAgICAgICAgICBmcm9tIHsKICAgICAgICAgICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDBkZWcpCiAgICAgICAgICAgICAgfQogICAgICAgICAgICAgIHRvIHsKICAgICAgICAgICAgICAgIHRyYW5zZm9ybTogcm90YXRlKC0zNTlkZWcpCiAgICAgICAgICAgICAgfQogICAgICAgICAgICB9CiAgICAgICAgICAgIHN2ZyB7CiAgICAgICAgICAgICAgICAtd2Via2l0LXRyYW5zZm9ybS1vcmlnaW46IDUwJSA1MCU7CiAgICAgICAgICAgICAgICAtd2Via2l0LWFuaW1hdGlvbjogc3BpbiAxLjVzIGxpbmVhciBpbmZpbml0ZTsKICAgICAgICAgICAgICAgIC13ZWJraXQtYmFja2ZhY2UtdmlzaWJpbGl0eTogaGlkZGVuOwogICAgICAgICAgICAgICAgYW5pbWF0aW9uOiBzcGluIDEuNXMgbGluZWFyIGluZmluaXRlOwogICAgICAgICAgICB9CiAgICAgICAgXV0+PC9zdHlsZT4KICAgIDwvZGVmcz4KICAgIDxnIGlkPSJvdXRlciI+CiAgICAgICAgPGc+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMCwwQzIyLjIwNTgsMCAyMy45OTM5LDEuNzg4MTMgMjMuOTkzOSwzLjk5MzlDMjMuOTkzOSw2LjE5OTY4IDIyLjIwNTgsNy45ODc4MSAyMCw3Ljk4NzgxQzE3Ljc5NDIsNy45ODc4MSAxNi4wMDYxLDYuMTk5NjggMTYuMDA2MSwzLjk5MzlDMTYuMDA2MSwxLjc4ODEzIDE3Ljc5NDIsMCAyMCwwWiIgc3R5bGU9ImZpbGw6YmxhY2s7Ii8+CiAgICAgICAgPC9nPgogICAgICAgIDxnPgogICAgICAgICAgICA8cGF0aCBkPSJNNS44NTc4Niw1Ljg1Nzg2QzcuNDE3NTgsNC4yOTgxNSA5Ljk0NjM4LDQuMjk4MTUgMTEuNTA2MSw1Ljg1Nzg2QzEzLjA2NTgsNy40MTc1OCAxMy4wNjU4LDkuOTQ2MzggMTEuNTA2MSwxMS41MDYxQzkuOTQ2MzgsMTMuMDY1OCA3LjQxNzU4LDEzLjA2NTggNS44NTc4NiwxMS41MDYxQzQuMjk4MTUsOS45NDYzOCA0LjI5ODE1LDcuNDE3NTggNS44NTc4Niw1Ljg1Nzg2WiIgc3R5bGU9ImZpbGw6cmdiKDIxMCwyMTAsMjEwKTsiLz4KICAgICAgICA8L2c+CiAgICAgICAgPGc+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMCwzMi4wMTIyQzIyLjIwNTgsMzIuMDEyMiAyMy45OTM5LDMzLjgwMDMgMjMuOTkzOSwzNi4wMDYxQzIzLjk5MzksMzguMjExOSAyMi4yMDU4LDQwIDIwLDQwQzE3Ljc5NDIsNDAgMTYuMDA2MSwzOC4yMTE5IDE2LjAwNjEsMzYuMDA2MUMxNi4wMDYxLDMzLjgwMDMgMTcuNzk0MiwzMi4wMTIyIDIwLDMyLjAxMjJaIiBzdHlsZT0iZmlsbDpyZ2IoMTMwLDEzMCwxMzApOyIvPgogICAgICAgIDwvZz4KICAgICAgICA8Zz4KICAgICAgICAgICAgPHBhdGggZD0iTTI4LjQ5MzksMjguNDkzOUMzMC4wNTM2LDI2LjkzNDIgMzIuNTgyNCwyNi45MzQyIDM0LjE0MjEsMjguNDkzOUMzNS43MDE5LDMwLjA1MzYgMzUuNzAxOSwzMi41ODI0IDM0LjE0MjEsMzQuMTQyMUMzMi41ODI0LDM1LjcwMTkgMzAuMDUzNiwzNS43MDE5IDI4LjQ5MzksMzQuMTQyMUMyNi45MzQyLDMyLjU4MjQgMjYuOTM0MiwzMC4wNTM2IDI4LjQ5MzksMjguNDkzOVoiIHN0eWxlPSJmaWxsOnJnYigxMDEsMTAxLDEwMSk7Ii8+CiAgICAgICAgPC9nPgogICAgICAgIDxnPgogICAgICAgICAgICA8cGF0aCBkPSJNMy45OTM5LDE2LjAwNjFDNi4xOTk2OCwxNi4wMDYxIDcuOTg3ODEsMTcuNzk0MiA3Ljk4NzgxLDIwQzcuOTg3ODEsMjIuMjA1OCA2LjE5OTY4LDIzLjk5MzkgMy45OTM5LDIzLjk5MzlDMS43ODgxMywyMy45OTM5IDAsMjIuMjA1OCAwLDIwQzAsMTcuNzk0MiAxLjc4ODEzLDE2LjAwNjEgMy45OTM5LDE2LjAwNjFaIiBzdHlsZT0iZmlsbDpyZ2IoMTg3LDE4NywxODcpOyIvPgogICAgICAgIDwvZz4KICAgICAgICA8Zz4KICAgICAgICAgICAgPHBhdGggZD0iTTUuODU3ODYsMjguNDkzOUM3LjQxNzU4LDI2LjkzNDIgOS45NDYzOCwyNi45MzQyIDExLjUwNjEsMjguNDkzOUMxMy4wNjU4LDMwLjA1MzYgMTMuMDY1OCwzMi41ODI0IDExLjUwNjEsMzQuMTQyMUM5Ljk0NjM4LDM1LjcwMTkgNy40MTc1OCwzNS43MDE5IDUuODU3ODYsMzQuMTQyMUM0LjI5ODE1LDMyLjU4MjQgNC4yOTgxNSwzMC4wNTM2IDUuODU3ODYsMjguNDkzOVoiIHN0eWxlPSJmaWxsOnJnYigxNjQsMTY0LDE2NCk7Ii8+CiAgICAgICAgPC9nPgogICAgICAgIDxnPgogICAgICAgICAgICA8cGF0aCBkPSJNMzYuMDA2MSwxNi4wMDYxQzM4LjIxMTksMTYuMDA2MSA0MCwxNy43OTQyIDQwLDIwQzQwLDIyLjIwNTggMzguMjExOSwyMy45OTM5IDM2LjAwNjEsMjMuOTkzOUMzMy44MDAzLDIzLjk5MzkgMzIuMDEyMiwyMi4yMDU4IDMyLjAxMjIsMjBDMzIuMDEyMiwxNy43OTQyIDMzLjgwMDMsMTYuMDA2MSAzNi4wMDYxLDE2LjAwNjFaIiBzdHlsZT0iZmlsbDpyZ2IoNzQsNzQsNzQpOyIvPgogICAgICAgIDwvZz4KICAgICAgICA8Zz4KICAgICAgICAgICAgPHBhdGggZD0iTTI4LjQ5MzksNS44NTc4NkMzMC4wNTM2LDQuMjk4MTUgMzIuNTgyNCw0LjI5ODE1IDM0LjE0MjEsNS44NTc4NkMzNS43MDE5LDcuNDE3NTggMzUuNzAxOSw5Ljk0NjM4IDM0LjE0MjEsMTEuNTA2MUMzMi41ODI0LDEzLjA2NTggMzAuMDUzNiwxMy4wNjU4IDI4LjQ5MzksMTEuNTA2MUMyNi45MzQyLDkuOTQ2MzggMjYuOTM0Miw3LjQxNzU4IDI4LjQ5MzksNS44NTc4NloiIHN0eWxlPSJmaWxsOnJnYig1MCw1MCw1MCk7Ii8+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4K);
		background-repeat: no-repeat;
		background-position: 50% 50%;
		background-origin: border-box;
		background-size: 10%;
		cursor:progress;
	}
	@keyframes fadeout {
		from {
			opacity:1;
		}
		to {
			opacity:0;
		}
	}
	.fadeout
	{
		animation:fadeout 1s;
		animation-delay: 3s;
		animation-fill-mode: forwards;
	}
	.editor-posts-nav
	{
		max-height: 800px;
		overflow-y: auto;
	}
	.img-responsive{
	display: block;
	max-width: 100%;
	height: auto;
	}
	.nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
		color: #fff;
		background-color: #337ab7;
	}
	.nav>li>a {
		position: relative;
		display: block;
		padding: 10px 15px;
		text-decoration: none;
	}
	.nav-pills>li>a {
		border-radius: 4px;
	}
	.editor-posts-nav, .editor-sites-nav {
		max-height: 800px;
		overflow-y: auto;
	}
	.nav {
		margin-bottom: 0;
		padding-left: 0;
		list-style: none;
	}
	.nav>li {
		position: relative;
		display: block;
	}
	.nav-stacked>li {
		float: none;
		width: 100%;
	}
	.nav > li {
		vertical-align: top;
	}
	.nav>li>a:hover, .nav>li>a:focus {
    text-decoration: none;
    background-color: #eee;
	}
	@media (min-width: 768px){
		.form-horizontal .control-label {
		    text-align: right;
		    margin-bottom: 0;
		    padding-top: 7px;
		}
	}
	.nav-pills>li>a.active, .nav-pills>li>a.active:hover, .nav-pills>li>a.active:focus {
	    color: #fff;
	    background-color: #337ab7;
	}
</style>
<body>
	<div class="container" style="margin-top:40px;">
		<div class="editor_client_selected">
			<div class="row">
				<div class="col-sm-12">
					<ul class="nav nav-pills" role="tablist">
						<li role="presentation">
							<a href="#editor_tab_post" class="active" aria-controls="editor_tab_post" role="tab" data-toggle="tab">
								Post Article/FAQ
							</a>
						</li>
						<li role="presentation">
							<a href="#editor_tab_sites" aria-controls="editor_tab_sites" role="tab" data-toggle="tab">
								Manage Sites
							</a>
						</li>
					</ul>
				</div>
			</div>
			<hr>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="editor_tab_post">
					<!-- <h4>Manage Content</h4> -->
					<form class="form-horizontal editor-form" action="editor-post.php" method="post">
						<div class="form-group">
							<div class="row">
								<label for="editor_sites" class="col-sm-2 control-label">Site</label>
								<div class="col-sm-10">
									<select id="editor_sites" name="site" class="form-control react" data-react-source="sites" data-react-write="fillSitesSelect" required>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<label for="editor_type" class="col-sm-2 control-label">Post Type</label>
								<div class="col-sm-10">
									<select class="form-control" name="type" id="editor_type" required>
										<option value="article">Article</option>
										<option value="faq">FAQ</option>
									</select>
								</div>
							</div>
						</div>
						<hr>
						<div class="row editor-site-selected">
							<div class="col-sm-3">
								<ul class="nav nav-pills nav-stacked react editor-posts-nav" data-react-source="posts" data-react-write="fillPostsNav">
								</ul>
							</div>
							<div class="col-sm-9">
								<input type="hidden" name="id" value="0" id="editor_id">
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_category">Category</label>
										<div class="col-sm-10">
											<input class="form-control" type="text" name="category" id="editor_category">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_title">Title</label>
										<div class="col-sm-10">
											<input class="form-control" type="text" name="title" id="editor_title" required>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_content">Content</label>
										<div class="col-sm-10 squire-container">
											<textarea name="content" id="editor_content"></textarea>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_status">Status</label>
										<div class="col-sm-10">
											<select class="form-control" name="status" id="editor_status">
												<option value="1">Published</option>
												<option value="0">Hidden</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_metatitle">Meta Title</label>
										<div class="col-sm-10">
											<input class="form-control" type="text" name="metatitle" id="editor_metatitle" maxlength="60">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_metadescription">Meta Description</label>
										<div class="col-sm-10">
											<textarea class="form-control vresize" name="metadescription" id="editor_metadescription" maxlength="300"></textarea>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label class="col-sm-2 control-label" for="editor_date">Publication Date</label>
										<div class="col-sm-4">
											<input class="form-control" type="date" name="date" id="editor_date" value="<?=date('Y-m-d');?>">
										</div>
										<label class="col-sm-2 control-label" for="editor_time">Time</label>
										<div class="col-sm-4">
											<input class="form-control" type="time" name="time" id="editor_time" value="<?=date('H:i:00');?>">
										</div>
									</div>
								</div>
								<!-- <div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<div class="checkbox">
											<label>
												<input name="social" type="checkbox"> Publish to Social Media
											</label>
										</div>
									</div>
								</div> -->
								<div class="form-group">
									<div class="row">
										<div class="col-sm-10 message-container">
										</div>
										<div class="col-sm-2">
											<button class="pull-right btn btn-primary" id="editor_post_button">Post</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
					<form id="editor_image_upload"
						data-af-progress=".iu-progress" data-af-callback="imageUploaded" data-af-files="1">
					</form>
				</div>
				<div role="tabpanel" class="tab-pane" id="editor_tab_sites">
					<!-- <h4>Manage Sites</h4> -->
					<div class="row">
						<div class="col-sm-4 col-md-3">
							<ul class="nav nav-pills nav-stacked react editor-sites-nav" data-react-source="sites" data-react-write="fillSitesNav">
							</ul>
						</div>
						<div class="col-sm-8 col-md-9">
							<form class="form-horizontal editor-site-form" action="editor-site-save.php" method="post">
								<input type="hidden" value="" name="site" id="editor_site_id">
								<div class="form-group">
									<div class="row">
										<label for="editor_site_name" class="col-sm-3 control-label">Site Name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="name" id="editor_site_name">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_faq" class="col-sm-3 control-label">Site FAQ Link</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="faqLink" id="editor_site_faq">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_news" class="col-sm-3 control-label">Site News Link</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="newsLink" id="editor_site_news">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_api" class="col-sm-3 control-label">Site Api Key</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="apiKey" id="editor_site_api">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_api_secret" class="col-sm-3 control-label">Site Api Secret</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="apiSecret" id="editor_site_api_secret">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_token" class="col-sm-3 control-label">Site Token</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="token" id="editor_site_token">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<label for="editor_site_token_secret" class="col-sm-3 control-label">Site Token Secret</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" value="" name="tokenSecret" id="editor_site_token_secret">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-sm-9 message-container">
										</div>
										<div class="col-sm-3">
											<button type="submit" class="pull-right btn btn-primary">Save</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		function fillSitesNav(sites)
		{
			let out = '<li role="presentation" class="active"><a href="#">New Site</a></li>';
			__.forEach(sites, function(){
				out += '<li role="presentation"><a href="#" data-id="' + this.id + '">' + this.name + '</a></li>';
			})
			updateManagedSite(0);
			return out;
		}
	
		function selectManagedSite(siteID)
		{
			$(".editor-sites-nav a[data-id=" + siteID + "]").click();
		}
		function updateManagedSite(siteID)
		{
			let $form = $("form.editor-site-form");
			siteID = parseInt(siteID);
			let site = __.filter(sites, 'id', siteID);
			if(site.length)
			{
				site = site[0];
				$form.find("#editor_site_id").val(site.id);
				$form.find("#editor_site_name").val(site.name);
				$form.find("#editor_site_faq").val(site.faq_link);
				$form.find("#editor_site_news").val(site.news_link);
				$form.find("#editor_site_api").val(site.api_key);
				$form.find("#editor_site_api_secret").val(site.api_secret);
				$form.find("#editor_site_token").val(site.token);
				$form.find("#editor_site_token_secret").val(site.token_secret);
			}
			else
			{
				$form[0].reset();
				$form.find("#editor_site_id").val(0);
			}
		}
		$(".editor-sites-nav").on("click", "a",function(e){
			let $a = $(this);
			$a.closest("li").addClass("active").siblings().removeClass("active");
			e.preventDefault();
			updateManagedSite($a.data("id"));
		});
		$(".editor-site-form").submit(function(e){
			e.preventDefault();
			let $form = $(this);
			$form.addClass("processing");

			let action = $form.attr("action");
			let method = $form.attr("method");
			let formdata = new FormData(this);

			formdata.append("client", $("#editor_clients").val());

			$.ajax(
				{
					url: action,
					data: formdata,
					processData: false,
					contentType: false,
					type: method
				})
				.done(function(result){
					if(result.success)
					{
						sites = result.sites;
						if(typeof(result.newSiteID) !== 'undefined')
						{
							showMessage($form, "Site added", true);
							fillSites(sites);
							selectManagedSite(result.newSiteID);
						}
						else
						{
							showMessage($form, "Site updated", true);
							fillSites(sites);
							selectManagedSite(result.siteID);
						}
					}
				})
				.fail(function(xhr){
					if(xhr.status == 500)
					{
						showMessage($form, "Could not process fields", false);
					}
					else if(xhr.status == 422)
					{
						showMessage($form, "Please fill all required fields", false);
					}
					else
					{
						showMessage($form, "Could not connect", false);
					}
				})
				.always(function(){
					$form.removeClass("processing");
				});
		});
	</script>
	<script src="editor.js"></script>
	<script>
	$(document).ready(function() {
		updateSites();
		// updatePosts('article', 'editor-posts.php');
	});
	</script>
</body>
</html>