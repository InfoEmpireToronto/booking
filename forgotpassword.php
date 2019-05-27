<?php
$title = 'Forgot password';
require('header.php');
?>
<section>
<div id='forgotpassword'>
	<template>
		<b-container>
			<b-form @submit="onSubmit" class="forgot-form">
				<b-row>
					<b-col sm="12" class="text-center">
					Forgot password
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="12" class="text-center">
					Forgot your password? Enter your email and we'll send you instructions to reset your password
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-form-input v-model="email" type="text" placeholder="Email" required></b-form-input>
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="12" class="text-center">
						<b-button name="forget" value="1" type="submit" variant="primary" class="btn-block">Send instructions</b-button>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-alert :show="dismissCountDown" dismissible :variant="variant" @dismissed="dismissCountDown=0">
						{{message}}
						</b-alert>
					</b-col>
				</b-row>
			</b-form>
		</b-container>
	</template>
</div>
</section>
<?php
require('footer.php');
?>

<script>
var forgot = new Vue({
	el: '#forgotpassword',
	data: 
		{
			message: null,
			login : 1,
			email: null,
			password: null,
			html: null,
			loggedIn: false,
			dismissCountDown: 0,
			dismissSecs: 5,
			variant: null
		},
	methods:
	{
		onSubmit (evt)
		{
			console.log(this.email);
			evt.preventDefault();
			axios.post('forms/form-forgotpassword.php',
			{
				email: this.email,
				forgot: 1
			},
			config)
			.then(function (response)
			{
				if(response.data.success)
				{
					forgot.variant = 'success';
                    forgot.message = response.data.message;
                    forgot.showAlert();
                    setTimeout(function(){
                        forgot.email = '';
                        }, 1800);
				}
                else
                {
                    forgot.variant = 'danger';
                    forgot.message = response.data.message;
                    forgot.showAlert();
                }
			})
			.catch(function (error) {
			console.log(error);
			});
		},
        showAlert ()
        {
            this.dismissCountDown = this.dismissSecs;
        }
	}
});

</script>

<!--
axios.post('test2.php', {
	firstName: 'Fred',
	lastName: 'Flintstone'
  }, config)
  .then(function (response) {
	console.log(response.data);
	// login.message = response.data?'success':'fail'; //
	login.message = response.data.res;
	login.res = response.data?'success':'warning';
	login.result = true;
   
  })
  .catch(function (error) {
	console.log(error);
  });

  var login = new Vue({
	el:'#login',
	data:{
	  message:'',
	  res: false,
	  fail: false,
	  result:false
	}
	  
-->