<?php
$title = 'Contact us';
require('header.php');
?>
<section class="content">
	<div id="contact">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-form @submit="sendContactForm">
							<div class="form-group">
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
									<label for="email">Email</label>
								</b-col>
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
									<b-input type="text" id="email" v-model="email" required></b-input>
								</b-col>
							</div>
							<div class="form-group">
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
									<label for="message">Message</label>
								</b-col>
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
									<b-form-textarea id="message" v-model="content" :rows="6" required></b-form-textarea>
								</b-col>
							</div>
							<div class="form-group">
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6" class="text-center">
									<b-button type="submit" variant="primary">Send</b-button>
								</b-col>
							</div>
							<div class="form-group">
								<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6" class="text-center">
									<b-alert :show="dismissCountDown" dismissible :variant="alert" @dismissed="dismissCountDown=0">
										{{message}}
									</b-alert>
								</b-col>
							</div>
						</b-form>
				   </b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<?php
require('footer.php');
?>
<script>
loadMenu('Contact');
axios.post('ajax/ajax-patient.php?email',config)
	.then(function (response)
	{
		if(response.data.success)
		{
			contact.email = response.data.email;
		}
	})
	.catch(function (error) {
	console.log(error);
	});
var contact = new Vue({
	el: '#contact',
	data:
	{
		email: null,
		content: null,
		message: null,
		dismissSecs: 2,
		dismissCountDown: 0,
		alert: null,
	},
	methods:
	{
		showAlert()
		{
			this.dismissCountDown = this.dismissSecs;
		},
		reset()
		{
			this.content = null;
		},
		sendContactForm(evt)
		{
			evt.preventDefault();
			if(this.email == null || this.content == null || this.email == '' || this.content == '')
			{
				this.message = 'Some fileds were not completed.';
				this.alert = 'danger';
				showAlert();
			}
			else
			{
				axios.post('forms/form-contact-form.php',
					{
						email: this.email,
						message: this.content
					}
					,config)
					.then(function (response)
					{
						console.log(response.data);
						if(response.data.success)
						{
							contact.message = response.data.message;
							contact.alert = 'success';
							contact.showAlert();
							setTimeout(function(){
							contact.reset();
							}, 1500);
							}
						else
						{
							contact.message = response.data.message;
							contact.alert = 'danger';
							contact.showAlert();
						}
					})
					.catch(function (error) {
					console.log(error);
					});
			}
		}
	}
});
</script>