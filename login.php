<?php
$title = 'Login';
require('header.php');
?>
<section>
<div id='login'>
	<template>
		<b-container>
			<b-form @submit="onSubmit" class="login-form">
				<b-row>
					<b-col sm="8" class="align-button">
					Don't have an account? 
					</b-col>
					<b-col sm="4" class="text-right">
						<b-button href="registration.php" variant="primary">Sign up</b-button>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-form-input v-model="email" type="text" placeholder="Email or username" required></b-form-input>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-form-input v-model="password" type="password" placeholder="Password" required></b-form-input>
					</b-col>
				</b-row>
				<b-row>
					<b-col sm="8" class="align-button">
						<b-link href="forgotpassword.php">Forgot your password?</b-link>
					</b-col>
					<b-col sm="4" class="text-right">
						<b-button name="login" value="1" type="submit" variant="primary">Login</b-button>
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
axios.post('forms/form-login.php?logout', config)
	.then(function (response)
	{
	})
	.catch(function (error) {
	console.log(error);
	});

var login = new Vue({
	el: '#login',
	data: 
		{
			message: null,
			login : 1,
			email: null,
			password: null,
			html: null,
			loggedIn: false,
            dismissCountDown: 0,
            dismissSecs: 1.5,
            variant: null

		},
	methods:
	{
		onSubmit (evt)
		{
			evt.preventDefault();
			axios.post('forms/form-login.php',
			{
				email: this.email,
				password: this.password,
				login: 1
			},
			config)
			.then(function (response)
			{
				if(response.data.success)
				{
                    login.variant = 'success';
                    login.message = 'Logged in, redirecting...';
                    login.showAlert();
					window.location = 'appointments.php';
				}
                else
                {
                    login.variant = 'danger';
                    login.message = response.data.message;
                    login.showAlert();
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