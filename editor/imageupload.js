let IUActiveData = {};
$( document ).ready(function() {
$("body").on("click", ".gallery-browse", function(){
	let $gallery = $($(this).data("iu-gallery"));
	// let data = {client: $("#editor_clients").val()};
	// $gallery.data("iuData", data);
	// IUActiveData = $gallery.data("iu-data");
	openGallery($gallery);
});
$("body").on("click", ".image-remove", function(){
	let $imageEl = $(this).closest(".gallery-select");
	removeImage($imageEl, "lib/gallery/delete.php");
	return false;
});
$("body").on("click", ".gallery-select", function(){
	let $el = $(this);
	let $gallery = $el.closest(".iu-gallery");
	let field = $gallery.data("iu-field");
	let imageURL = "lib/uploads/" + $el.data("iu-image");
	let callback = $gallery.data("iu-callback");
	$(".gallery-select").removeClass("selected");
	$el.addClass("selected");
	window[callback](imageURL);
	/*
	selectImage(imageURL, field);
	*/
});
$("body").on("click", ".gallery-close", function(){
	let $gallery = $(this).closest(".iu-gallery");
	closeGallery($gallery);
});
$("body").on("click", ".gallery-upload", function(){
	let $inp = $("input.gallery-upload-file");
	let $progress = $(".iu-progress");
	let client = $("#editor_clients").val();
	let file = undefined;
	$inp.each(function(){
		if(this.files.length)
		{
			file = this.files[0];
		}
	});
	let formdata = new FormData();
	formdata.append($inp.attr("name"), file);
	formdata.append("client", client);
	$.ajax({
		type: "POST",
		url: "lib/gallery/upload.php",
		contentType: false,
		processData: false,
		data: formdata,
		beforeSend: function(){
			$progress.show();
		},
		xhr: function(){  // Update Progressbar
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){ // Check if upload property exists
				myXhr.upload.addEventListener('progress',function(e){
					if(e.lengthComputable)
					{
						$progress.attr({value:e.loaded, max:e.total});
					}
				}, false); // For handling the progress of the upload
			}
			return myXhr;
		},
		error: function(result){
			alert( "Could Not Connect" );
		},
		complete: function(result){
			$('html').css( 'cursor', 'initial' );
		},
		success: function(result){
			imageUploaded(result);
		},
	});
	/*
		Need to finish image upload code
	*/
});

});
function closeGallery($gallery)
{
	$gallery.removeClass("open");
	$gallery.find(".contents").html("");
}
function openGallery($gallery)
{
	// let data = $gallery.data("iu-data");
	let dataSource = $gallery.data("iu-load") + ".php";
	$('html').css('cursor', 'progress');
	$.get(dataSource, function(response){
		$gallery.find(".contents").html(response);
		$gallery.addClass("open");
	}, "html")
	.always(function(){
		$('html').css('cursor', 'initial');
	});
}
function selectImage(imageURL, field)
{
	$("img.iu-destination[data-iu-field=" + field + "]").prop("src", "lib/uploads/" + imageURL);
	$("input.iu-destination[data-iu-field=" + field + "]").val(imageURL);
}
function removeImage($imageEl, url)
{
	$imageEl.addClass("removed");
	data.image = $imageEl.data("iu-image");
	$.post(url, function(response)
	{
		if(!response.success)
		{
			$imageEl.removeClass("removed");
		}
	},"json");
}
function imageUploaded(response)
{
	if(response.success)
	{
		$(".iu-progress").hide();
		$(".iu-message").text("Image uploaded");
		let $gallery = $(".iu-gallery.open");
		openGallery($gallery);
	}
	else
	{
		let message = "Image could not be uploaded";
		$(".iu-progress").hide();
		if(response.message)
		{
			message += " - " + response.message;
		}
		$(".iu-message").text(message);
	}
}