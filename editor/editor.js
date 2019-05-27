var sites = [];
function updateSites()
{
	$.post('editor-sites.php', function(response){
		if(response.success)
		{
			sites = response.sites;
			fillSites(sites);
		}
	}, "json");
}
function fillSites(sites)
{
	let $els = $(".react[data-react-source=sites]");
	$els.each(function(){
		let $el = $(this);
		if(typeof(window[$el.data("react-write")]) != 'undefined')
		{
			let output = window[$el.data("react-write")](sites);
			$el.html(output);
			if($el.is("input,textarea,select"))
			{
				$el.change();
			}
		}
	});
}
function showMessage($form, message, success)
{
	let $el = $form.find(".message-container");
	let alertclass = success ? "success" : "danger";
	let html =
		'<div class="fadeout alert alert-' + alertclass + '">' +
			message +
		'</div>';
	$el.html(html);
}
function fillSitesSelect(sites)
{
	let out = '';
	if(sites.length)
	{
		for(i = 0; i != sites.length; i++)
		{
			let site = sites[i];
			out += '<option value="' + site.id + '">' + site.name + '</option>';
		}
	}
	else
	{
		out += '<option value="" selected disabled>No sites for this client</option>';
	}
	return out;
}
function updatePosts(site, type, url)
{
	$.post(url, {site: site, type: type}, function(response){
			if(response.success)
			{
				posts = response.posts;
				posts.sort(function (a, b) {
					if (a.utc > b.utc) {
						return -1;
					}
					if (a.utc < b.utc) {
						return 1;
					}
					return 0;
				});
				fillPosts(posts);
			}
		}, "json");
}
function fillPosts(posts)
{
	let $els = $(".react[data-react-source=posts]");
	$els.each(function(){
		let $el = $(this);
		if(typeof(window[$el.data ("react-write")]) != 'undefined')
		{
			let output = window[$el.data("react-write")](posts);
			$el.html(output);
			if($el.is("input,textarea,select"))
			{
				$el.change();
			}
		}
	});
}
function fillPostsNav(posts)
{
	
	let out = '<li role="presentation" class="active"><a href="#">New Post</a></li>';
	__.forEach(posts, function(){
		let statusClass = this.status == 1 ? '' : ' class="text-danger"';
		out += '<li role="presentation"><a href="#" data-id="' + this.id + '"' + statusClass + '>'
			+ this.title + '</a></li>';
	});
	updateManagedPost(0);
	return out;
}
function selectManagedPost(postID)
{
	$(".editor-posts-nav a[data-id=" + postID + "]").click();
}
function updateManagedPost(postID)
{
	let $form = $("form.editor-form");
	postID = parseInt(postID);
	let post = __.filter(posts, 'id', postID);
	if(post.length)
	{
		post = post[0];
		$form.find("#editor_id").val(post.id);
		
		$form.find("#editor_title").val(post.title);
		$form.find("#editor_status").val(post.status);

		let valCategory = post.category ? post.category : '';
		let valMetatitle = post.metatitle ? post.metatitle : '';
		let valMetadescription = post.metadescription ? post.metadescription : '';
		let valDate = post.date ? post.date : '';
		let valTime = post.time ? post.time : '';

		$form.find("#editor_category").val(valCategory);
		$form.find("#editor_metatitle").val(valMetatitle)
		$form.find("#editor_metadescription").val(valMetadescription)
		$form.find("#editor_date").val(valDate)
		$form.find("#editor_time").val(valTime)

		squireObj.setHTML(post.content);
		$("#editor_post_button").text("Update");
		$(".editor-new-only").hide();
	}
	else
	{
		let site = $("#editor_sites").val();
		let type = $("#editor_type").val();
		$form[0].reset();
		$form.find("#editor_id").val(0);
		$form.find("#editor_sites").val(site);
		$form.find("#editor_type").val(type);
		squireObj.setHTML("");
		$("#editor_post_button").text("Post");
		$(".editor-new-only").show();
	}
}
function addImage(url)
{
	squireObj.insertImage(url);
}
$(function(){
	new SquireUI({buildPath: "squire/", replace: 'textarea#editor_content', height: 300});

	$(".editor-form").submit(function(e){
		e.preventDefault();
		let $form = $(this);
		$form.addClass("processing");

		let action = $form.attr("action");
		let method = $form.attr("method");

		let formdata = new FormData(this);
		let content = squireObj.getHTML();

		formdata.append("client", $("#editor_clients").val());
		formdata.append("content", content);

		$.ajax(
			{
				url: action,
				data: formdata,
				processData: false,
				contentType: false,
				type: method
			})
			.done(function(result){
				posts = result.posts;
				posts.sort(function (a, b) {
					if (a.id > b.id) {
						return -1;
					}
					if (a.id < b.id) {
						return 1;
					}
					return 0;
				});
				if(typeof(result.newPostID) !== 'undefined')
				{
					showMessage($form, "Content posted", true);
					fillPosts(posts);
					selectManagedPost(0);
				}
				else
				{
					showMessage($form, "Content updated", true);
					fillPosts(posts);
					selectManagedPost(result.postID);
				}
			})
			.fail(function(xhr){
				if(xhr.status == 500)
				{
					showMessage($form, "Could not process post", false);
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
	$("#editor_type, #editor_sites").change(function(){
		let site = $("#editor_sites").val();
		let type = $("#editor_type").val();
		$(".editor-site-selected").hide();
		if(type)
		{
			updatePosts(site, type, "editor-posts.php");
			$(".editor-site-selected").show();
		}
	});
	$(".editor-posts-nav").on("click", "a",function(e){
		let $a = $(this);
		$a.closest("li").addClass("active").siblings().removeClass("active");
		e.preventDefault();
		updateManagedPost($a.data("id"));
	});
	$("#editor_groups").change(function(){
		var viewGroup = $("#editor_groups").val();
		if(viewGroup == '')
		{
			$("#editor_clients option").show();
		}
		else if(viewGroup == '1')
		{
			$("#editor_clients option:not(.client-group-all)").hide();
			$("#editor_clients option.client-group-1:not(.client-group-2)").show();
		}
		else
		{
			$("#editor_clients option:not(.client-group-all)").hide();
			$("#editor_clients option.client-group-" + viewGroup).show();
		}
	});
	// $("#editor_clients").change(function(){
		
	// 	let $this = $(this);
	// 	if($this.val())
	// 	{
	// 		updateSites($this.val(), $this.data("source"))
	// 		$(".editor_client_selected").show();
	// 		// $(".editor-gallery").data("iu-data", {client: $this.val()});
	// 	}
	// 	else
	// 	{
	// 		$(".editor_client_selected").hide();
	// 	}
	// });
	$('#editor_groups').change();
	$(".ajax-update").change(function(){
		let $this = $(this);
		let val = $this.val();
		let url = $this.data("update-source");
		let target = $this.data("update-target");
		$.post(url, {id: val}, function(response){
			$(target).html(response);
		}, "html");
	});
});
	