<?php
$title = 'Reset password';
require('header.php');
?>
<section>
<div id='resetpassword'>
	<template>
		<b-container>
			<b-form @submit="onSubmit" class="forgot-form">
				<b-row>
					<b-col sm="12" class="text-center">
					Reset password
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="12" class="text-center">
					<b-form-input v-model="newpassword" type="password" placeholder="Enter your new password" required></b-form-input>
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="12" class="text-center">
					<b-form-input v-model="confirmpassword" type="password" placeholder="Confirm your password" required></b-form-input>
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="12" class="text-center">
						<b-button name="reset" value="1" type="submit" variant="primary" class="btn-block">Set password</b-button>
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
var reset = new Vue({
	el: '#resetpassword',
	data: 
		{
			message: null,
			newpassword: null,
			confirmpassword: null,
			dismissCountDown: 0,
			dismissSecs: 2,
			variant: null
		},
	methods:
	{
		onSubmit (evt)
		{
			console.log(this.email);
			evt.preventDefault();
			let urlParams = new URLSearchParams(window.location.search);
			let token = urlParams.get('t');
			axios.post('forms/form-resetpassword.php',
			{
				newpassword: this.newpassword,
				confirmpassword: this.confirmpassword,
				token: token,
				reset: 1
			},
			config)
			.then(function (response)
			{
				if(response.data.success)
				{
					reset.variant = 'success';
                    reset.message = response.data.message;
                    reset.showAlert();
                    reset.newpassword = reset.newpassword = '';
                    window.location = 'login.php';
				}
                else
                {
                    reset.variant = 'danger';
                    reset.message = response.data.message;
                    reset.showAlert();
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