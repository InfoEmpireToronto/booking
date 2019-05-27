<?php
$title = 'Information';
require('header.php');
?>
<section id="info">
	<template>
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<p>NEED HELP? CONTACT US</p>
					<p>Local number (Toronto, ON): 416-769-5250</p>
					<p>Long distance caller: 1-877-482-4678</p>
					<p>E-mail us: support@infoempire.com</p>
					<!-- <p>Click  here for an instruction manual in English</p> -->
					<!-- <p>Click  here for an instruction manual in Russian</p> -->
				</div>
			</div>
		</div>
	</template>
</section>
<?php
require('footer.php');
?>

<script>
loadMenu('Info');

new Vue({
	el: '#info'
});
</script>