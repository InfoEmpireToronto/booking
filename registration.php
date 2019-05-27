<?php
$title = 'Registration';
require('header.php');
?>
<section>
<div id="registration">
	<template>
		<b-container>
			<b-row>
				<b-col sm="12" lg="8" offset-lg="2">
					<b-button v-b-toggle.accordion1 variant="light" class="btn-toggle-title">
						1. Wavetoget import
					</b-button>
					<b-collapse id="accordion1" visible accordion="my-accordion">
						<b-card>
							<b-row>
								<b-col>
									<b-form-radio-group stacked v-model="createOption" :options="createOptions"></b-form-radio-group>
								</b-col>
							</b-row>
							<b-row class="mt-3">
								<b-col>
									<b-button variant="primary" @click="gotoStep2">Continue</b-button>
								</b-col>
							</b-row>
						</b-card>
					</b-collapse>
					<b-button v-b-toggle.accordion2 variant="light" class="btn-toggle-title" :style="{display: display}">
						2. Import
					</b-button>
					<b-collapse id="accordion2" accordion="my-accordion" :style="{display: display}">
						<b-card>
							<div class="form-group">
								Use email or card number to import
							</div>
							<div class="form-group">
								<b-form-radio-group v-model="importOption" :options="importOptions"></b-form-radio-group>
								<b-form-input type="text" v-model="card" autocomplete="off" :placeholder="placeholder"></b-form-input>
							</div>
							<b-row class="mt-3">
								<b-col>
									<b-button variant="primary" @click="gotoStep3" :disabled="!card">Continue</b-button>
									<span>&nbsp;{{message}}</span>
								</b-col>
							</b-row>
						</b-card>
					</b-collapse>
					<b-button v-b-toggle.accordion3 variant="light" class="btn-toggle-title" :disabled="!step3">
						{{stepTitle}} Create an account
					</b-button>
					<b-collapse id="accordion3" accordion="my-accordion">
						<b-card>
							<b-form @submit="onSubmit" class="registration-form">
								<div class="form-group">
									<label for="firstname" class="mb-0">First name*</label>
									<b-form-input v-model="firstname" type="text" id="firstname" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="lastname" class="mb-0">Last name*</label>
									<b-form-input v-model="lastname" type="text" id="lastname" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="email" class="mb-0">Email*</label>
									<b-form-input v-model="email" type="email" id="email" :disabled="cardholder !== null" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="password" class="mb-0">Password*</label>
									<b-form-input v-model="password" type="password" id="password" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="password2" class="mb-0">Confirm password*</label>
									<b-form-input v-model="password2" type="password" id="password2" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="phone" class="mb-0">Phone*</label>
									<b-form-input v-model="phone" type="text" id="phone" placeholder="xxx-xxx-xxxx" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" maxlength="12" 
									@keyup.native="phoneFormatter" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="birthday" class="mb-0">Date of birth*</label>
										<b-form-input v-model="birthday" type="date" id="birthday" required></b-form-input>
								</div>
								<div class="form-group">
									<label for="address" class="mb-0">Address</label>
									<b-form-input v-model="address" type="text" id="address"></b-form-input>
								</div>
								<div class="form-group">
									<label for="city" class="mb-0">City</label>
									<b-form-input v-model="city" type="text" id="city"></b-form-input>
								</div>
								<div class="form-group">
									<label for="province" class="mb-0">Province</label>
									<b-form-select v-model="province" :options="provinceOptions" id="province"/>
								</div>
								<div class="form-group">
									<label for="postalcode" class="mb-0">Postal code</label>
									<b-form-input v-model="postalcode" type="text" id="postalcode"></b-form-input>
								</div>
								<div class="form-group">
									<label for="country" class="mb-0">Country</label>
									<b-form-select v-model="country" :options="countryOptions" id="country"/>
								</div>
								<div class="form-group mb-0">
									<label class="mb-0">Appointment reminder</label>
								</div>
								<div class="form-group">
									<b-form-checkbox v-model="email_notification" class="registration-notification">Email</b-form-checkbox>
									<b-form-checkbox v-model="sms_notification" class="registration-notification">SMS</b-form-checkbox>
								</div>
								<div class="form-group">
									<p class="mt-2">
										<b-form-checkbox v-model="agreement" id="agreement"></b-form-checkbox>I Agree With the <b-link to="terms.php" target="_blank">Terms and conditions</b-link>
									</p>
								</div>
								<div class="form-group">
									<b-button name="register" value="1" type="submit" variant="primary" class="form-button">Register</b-button>
									<span>&nbsp;{{message2}}</span>
								</div>
							</b-form>
						</b-card>
					</b-collapse>
				</b-col>
			</b-row>
		</b-container>
		<!-- <div class="form-group">
			<b-col cols="12" md="4" lg="3">
				<label for="gender">Gender</label>
			</b-col>
			<b-col cols="12" md="8" lg="9" class="text-left">
				<b-form-radio-group v-model="gender" :options="genderOptions"></b-form-radio-group>
			</b-col>
		</div>
		<div class="form-group">
			<b-col cols="12" md="4">
				<label for="marital">Marital status</label>
			</b-col>
			<b-col cols="12" md="8">
				<b-form-select v-model="marital_status" :options="maritalOptions" id="marital"/>
			</b-col>
		</div> -->
	</template>
</div>
</section>
<?php
require('footer.php');
?>

<script>
axios.post('ajax/ajax-country-province.php',config)
	.then(function (response)
	{
		if(response.data.success)
		{
			registration.provinceOptions = response.data.provinces;
			registration.countryOptions = response.data.countries;
		}
	})
	.catch(function (error) {
	console.log(error);
	});

var registration = new Vue({
	el: '#registration',
	data:
		{
			createOptions: [{text: 'Import information from Wavetoget', value: 1}, {text: 'Create new account', value: 2}],
			createOption: 1,
			importOptions: [{text: 'Email', value: 1}, {text: 'Card number', value: 2}],
			importOption: 1,
			step2: false,
			step3: false,
			stepTitle: '2.',
			cardholder: null,
			display: 'none',
			card: null,
			placeholder: 'Email',
			firstname: null,
			lastname: null,
			birthday: null,
			phone: null,
			email: null,
			username: null,
			password: null,
			password2: null,
			email_notification: true,
			sms_notification: true,
			genderOptions:[{text: 'Male', value: 0}, {text: 'Female', value: 1}],
			gender: null,
			marital_status: null,
			maritalOptions: [{text: 'Unassigned', value: 1}, {text: 'Married', value: 2}, {text: 'Single', value: 3}],
			address: null,
			city: null,
			province: null,
			country: null,
			postalcode: null,
			provinceOptions: [],
			countryOptions: [],
			agreement: false,
			message: null,
			message2: null,
			alert: null,
			dismissSecs: 2,
			dismissCountDown: 0
		},
	watch:
	{
		importOption: function(val, oldVal)
		{
			if(val == 1)
			{
				this.placeholder = 'Email';
			}
			if(val == 2)
			{
				this.placeholder = 'Card number';
			}
		}
	},
	methods:
	{
		onSubmit (evt)
		{
			evt.preventDefault();
			if(!this.agreement)
			{
				this.message2 = 'You must agree to the terms and conditions';
			}
			else if(this.password != this.password2)
			{
				this.message2 = 'passwords do not match.';
			}
			else
			{
				this.message2 = '';
				axios.post('forms/form-registration.php', 
				{
					firstname: this.firstname,
					lastname: this.lastname,
					birthday: this.birthday,
					phone: this.phone,
					gender: this.gender,
					marital_status: this.marital_status,
					address: this.address,
					city: this.city,
					province: this.province,
					country: this.country,
					postalcode: this.postalcode,
					email: this.email,
					username: this.username,
					password: this.password,
					wavetoget: this.cardholder,
					email_notification: this.email_notification,
					sms_notification: this.sms_notification,
					register: 1
				}
				, config)
				.then(function (response)
				{	            	
					if(response.data.success)
					{
						registration.message2 = response.data.message;
						// registration.showAlert();
						setTimeout(function(){
							window.location = 'login.php';
						}, 2800);
					}
					else
					{
						registration.message2 = response.data.message;
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			}
		},
		gotoStep2()
		{
			if(this.createOption == 1)
			{
				this.step2 = true;
				this.step3 = false;
				this.stepTitle = '3.';
				this.display = 'block';
				this.$root.$emit('bv::toggle::collapse', 'accordion2');
			}
			if(this.createOption == 2)
			{
				this.step2 = false;
				this.step3 = true;
				this.display = 'none';
				this.stepTitle = '2.';
				this.$root.$emit('bv::toggle::collapse', 'accordion3');
			}
		},
		gotoStep3()
		{
			if(this.card == null || this.card == '')
			{
				this.message = 'Invalid input';
				setTimeout(function(){
					registration.message = null;
					}, 2500);
			}
			else
			{
				if(this.importOption == 1)
					value = 'email';
				if(this.importOption == 2)
					value = 'card';
				let formData = new FormData();
				formData.append(value, this.card);
				axios.post('forms/form-search-cardholder.php',
				formData
				,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						registration.firstname = response.data.firstname;
						registration.lastname = response.data.lastname;
						registration.email = response.data.email;
						registration.birthday = response.data.birthday;
						registration.phone = response.data.phone;
						registration.address = response.data.address;
						registration.city = response.data.city;
						registration.province = response.data.province;
						registration.country = response.data.country;
						registration.postalcode = response.data.postalcode;
						registration.cardholder = response.data.cardholder;
						registration.step3 = true;
						registration.phoneFormatter();
						registration.$root.$emit('bv::toggle::collapse', 'accordion3');
					}
					else
					{
						registration.message = 'No result found';
						setTimeout(function(){
							registration.message = null;
						}, 2000);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
				}
		},
		phoneFormatter()
		{
			value = this.phone;
			patt = /^[0-9]{3}-[0-9]{3}-[0-9]{4}$/;
			patt1 = /^[0-9]{1,3}$/;
			patt2 = /^[0-9]{3}-$/;
			patt3 = /^[0-9]{3}-[0-9]{1,3}$/;
			patt4 = /^[0-9]{3}-[0-9]{3}-$/;
			patt5 = /^[0-9]{3}-[0-9]{3}-[0-9]{1,3}$/;
			if(!patt.test(value))
			{
				if(!patt1.test(value) && !patt2.test(value) && !patt3.test(value) && !patt4.test(value) && !patt5.test(value))
				{
					value = value.replace(/-/g, '');
					if(value.length > 3 && value.length < 7)
						value = value.replace(/^([0-9]{3})([0-9]{1,3})$/, '$1-$2');
					if(value.length > 6)
						value = value.replace(/^([0-9]{3})([0-9]{3})([0-9]{1,4})$/, '$1-$2-$3');
				}
			}
			this.phone = value;
		}
	}
});
</script>