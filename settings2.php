<?php
$title = 'Setting';
require('header.php');
?>
<section class="content">
	<div id="settings">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Profile" active>
								<b-form class="text-center" @submit="updateSetting">
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="firstname">First name</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.firstname" type="text" id="firstname" required></b-form-input>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="lastname">Last name</label>
										</b-col>
										<b-col cols="12" md="9" lg="4">
											<b-form-input v-model="setting.lastname" type="text" id="lastname" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="email">Email</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.email" type="text" id="email" required></b-form-input>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="birthday">Birthday</label>
										</b-col>
										<b-col cols="12" md="9" lg="4">
											<b-form-input v-model="setting.birthday" type="date" id="birthday" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="phone">Phone</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.phone" type="text" id="phone" required></b-form-input>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="gender">Gender</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="text-left">
											<b-form-radio-group v-model="setting.gender" :options="setting.genderOptions" id="gender"></b-form-radio-group>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="marital">Marital status</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-select v-model="setting.marital" :options="setting.maritalOptions" id="marital"/>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="address">Address</label>
										</b-col>
										<b-col cols="12" md="9" lg="4">
											<b-form-input v-model="setting.address" id="address"></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="city">City</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input id="city" v-model="setting.city"></b-form-input>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="province">Province</label>
										</b-col>
										<b-col cols="12" md="9" lg="4">
											<b-form-select v-model="setting.province" :options="setting.provinceOptions" id="edit-province"/>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" lg="2">
											<label for="country">Country</label>
										</b-col>
										<b-col cols="12" md="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-select v-model="setting.country" :options="setting.countryOptions" id="country"/>
										</b-col>
										<b-col cols="12" md="3" lg="2">
											<label for="postalcode">Postal code</label>
										</b-col>
										<b-col cols="12" md="9" lg="4">
											<b-form-input id="postalcode" v-model="setting.postalcode"></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12">
											<b-button type="submit" variant="primary" class="form-button">Save</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="3" md="6">
											<b-alert :show="setting.dismissCountDown" dismissible :variant="setting.alert" @dismissed="setting.dismissCountDown=0">
												{{setting.message}}
											</b-alert>
										</b-col>
									</div>
								</b-form>
							</b-tab>
							<b-tab title="Reset password">
								<b-form @submit="updatePassword" class="text-center">
									<div class="form-group">
										<b-col sm="4" offset-md="2" md="3" offset-lg="2" lg="3" offset-xl="3" xl="2">
											<label for="password">Current password</label>
										</b-col>
										<b-col sm="8" md="5" lg="5" xl="4">
											<b-form-input v-model="password.password" type="password" id="password" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="4" offset-md="2" md="3" offset-lg="2" lg="3" offset-xl="3" xl="2">
											<label for="newpassword">New password</label>
										</b-col>
										<b-col sm="8" md="5" lg="5" xl="4">
											<b-form-input v-model="password.newpassword" type="password" id="newpassword" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="4" offset-md="2" md="3" offset-lg="2" lg="3" offset-xl="3" xl="2">
											<label for="confirm">Confirm</label>
										</b-col>
										<b-col sm="8" md="5" lg="5" xl="4">
											<b-form-input v-model="password.confirm" type="password" id="confirm" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="3" md="6">
											<b-button type="submit" variant="primary">Update</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12"  offset-md="3" md="6">
											<b-alert :show="password.dismissCountDown" dismissible :variant="password.alert" @dismissed="password.dismissCountDown=0">
												{{password.message}}
											</b-alert>
										</b-col>
									</div>
								</b-form>
							</b-tab>
							<b-tab title="Wavetoget" @click="loadWavetogetInfo">
								<template v-if="setting.wavetoget">
									<div class="form-group">
										<b-col sm="12" offset-md="4" md="4" class="text-center">
											<label>Points</label>&nbsp;&nbsp;<span style="line-height: 2.2;">{{wavetoget.w2gPoint}}</span>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="4" md="4" class="text-center">
											<label>Dollars</label>&nbsp;&nbsp;<span style="line-height: 2.2;">{{wavetoget.w2gDollar}}</span>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="4" md="4" class="text-center">
											<b-button type="button" variant="primary" @click="UnlinkCardholder">Unlink</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="3" md="6">
											<b-alert :show="wavetoget.dismissCountDown" dismissible :variant="wavetoget.alert" @dismissed="wavetoget.dismissCountDown=0">
												{{wavetoget.message}}
											</b-alert>
										</b-col>
									</div>
								</template>
								<template v-else>
									<div class="form-group">
										<b-col class="text-center">
											<b-button type="button" variant="primary" :title="wavetoget.email" @click="linkCardholder2">
												Link with the same email
											</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col class="text-center">
											or
										</b-col>
									</div>
									<div class="form-group">
										<b-col class="text-center">
											Enter email or card number to link to Wavetoget
										</b-col>
									</div>
									<b-form @submit="linkCardholder" class="text-center">
										<div class="form-group">
											<b-col sm="12" offset-md="3" md="2">
												<label for="w2g-email">Email</label>
											</b-col>
											<b-col sm="12" md="4">
												<b-form-input v-model="wavetoget.w2gEmail" id="w2g-email"></b-form-input>
											</b-col>
										</div>
										<div class="form-group">
											<b-col sm="12" offset-md="3" md="2">
												<label for="w2g-card">Card number</label>
											</b-col>
											<b-col sm="12" md="4">
												<b-form-input v-model="wavetoget.w2gCard" id="w2g-card" placeholder="Wavetoget car number"></b-form-input>
											</b-col>
										</div>
										<div class="form-group">
											<b-col sm="12" offset-md="3" md="6">
												<b-button type="submit" variant="primary">Link</b-button>
											</b-col>
										</div>
										<div class="form-group">
											<b-col sm="12" offset-md="3" md="6">
												<b-alert :show="wavetoget.dismissCountDown" dismissible :variant="wavetoget.alert" @dismissed="wavetoget.dismissCountDown=0">
													{{wavetoget.message}}
												</b-alert>
											</b-col>
										</div>
									</b-form>
								</template>
							</b-tab>
						</b-tabs>
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
loadMenu('Settings');
axios.post('ajax/ajax-country-province.php',config)
	.then(function (response)
	{
		if(response.data.success)
		{
			settings.setting.provinceOptions = response.data.provinces;
			settings.setting.countryOptions = response.data.countries;
		}
	})
	.catch(function (error) {
	console.log(error);
	});
axios.post('ajax/ajax-patient.php',config)
	.then(function (response)
	{
		if(response.data.success)
		{
			settings.setting.firstname = response.data.firstname;
			settings.setting.lastname = response.data.lastname;
			settings.setting.gender = response.data.gender;
			settings.setting.marital = response.data.marital;
			settings.setting.phone = response.data.phone;
			settings.setting.address = response.data.address;
			settings.setting.email = response.data.email;
			settings.setting.birthday = response.data.birthday;
			settings.setting.city = response.data.city;
			settings.setting.province = response.data.province;
			settings.setting.country = response.data.country;
			settings.setting.postalcode = response.data.postalcode;
			settings.setting.wavetoget = response.data.wavetoget;
			settings.wavetoget.email = response.data.email;
		}
	})
	.catch(function (error) {
	console.log(error);
	});

var settings = new Vue({
	el: '#settings',
	data:
	{
		setting:
		{
			firstname: null,
			lastname: null,
			email: null,
			birthday: null,
			gender: null,
			marital: null,
			phone: null,
			address: null,
			city: null,
			province: null,
			country: null,
			postalcode: null,
			wavetoget: null,
			provinceOptions: [],
			countryOptions: [],
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null,
			message: null,
			genderOptions:[{text: 'Male', value: 0}, {text: 'Female', value: 1}],
			maritalOptions: [{text: 'Unassigned', value: 1}, {text: 'Married', value: 2}, {text: 'Single', value: 3}]
		},
		password:
		{
			password: null,
			newpassword: null,
			confirm: null,
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0
		},
		wavetoget:
		{
			email: null,
			w2gEmail: null,
			w2gCard: null,
			w2gPoint: null,
			w2gDollar: null,
			message: null,
			alert: null,
			dismissSecs: 2,
			dismissCountDown: 0
		}
	},
	methods:
	{
		showAlert(section)
		{
			if(section == 1)
			{
				this.setting.dismissCountDown = this.setting.dismissSecs;
			}
			if(section == 2)
			{
				this.password.dismissCountDown = this.password.dismissSecs;
			}
			if(section == 3)
			{
				this.wavetoget.dismissCountDown = this.wavetoget.dismissSecs;
			}
		},
		reset()
		{
			settings.password.password = settings.password.newpassword = settings.password.confirm = null;
		},
		updateSetting(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-patient.php',
			{
				firstname: this.setting.firstname,
				email: this.setting.email,
				lastname: this.setting.lastname,
				birthday: this.setting.birthday,
				gender: this.setting.gender,
				marital_status: this.setting.marital,
				address: this.setting.address,
				city: this.setting.city,
				province: this.setting.province,
				country: this.setting.country,
				postalcode: this.setting.postalcode,
				phone: this.setting.phone
			}
			,config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					settings.setting.alert = 'success';
					settings.setting.message = response.data.message;
					settings.showAlert(1);
					settings.wavetoget.email = settings.setting.email;
				}
				else
				{
					settings.setting.alert = 'danger';
					settings.setting.message = response.data.message;
					settings.showAlert(1);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		updatePassword(evt)
		{
			evt.preventDefault();
			if(this.password.newpassword == this.password.confirm)
			{
				axios.post('forms/form-update-credential.php',
				{
					password: this.password.password,
					newpassword: this.password.newpassword
				}
				,config)
				.then(function (response)
				{
					console.log(response.data);
					if(response.data.success)
					{
						settings.password.alert = 'success';
						settings.password.message = response.data.message;
						settings.showAlert(2);
						setTimeout(function(){
						settings.reset();
						}, 2000);
					}
					else
					{
						settings.password.alert = 'danger';
						settings.password.message = response.data.message;
						settings.showAlert(2);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			}
			else
			{
				settings.password.alert = 'danger';
				settings.password.message = 'New passwords don\'t match';
				settings.showAlert(2);
			}
		},
		linkCardholder(evt)
		{
			evt.preventDefault();
			if(this.wavetoget.w2gEmail == null && this.wavetoget.w2gCard ==  null)
			{
				this.wavetoget.message = 'Invalid input';
				this.wavetoget.alert = 'danger';
				this.showAlert(3);
			}
			else
			{
				axios.post('forms/form-cardholder.php?link',
				{
					email: this.wavetoget.w2gEmail,
					card: this.wavetoget.w2gCard
				}
				,config)
				.then(function (response)
				{
					console.log(response.data);
					if(response.data.success)
					{
						settings.wavetoget.w2gPoint = response.data.points;
						settings.wavetoget.w2gDollar = response.data.dollars;
						settings.wavetoget.message = response.data.message;
						settings.wavetoget.alert = 'success';
						settings.showAlert(3);
						settings.setting.wavetoget = response.data.cardholder;
					}
					else
					{
						settings.wavetoget.message = response.data.message;
						settings.wavetoget.alert = 'danger';
						settings.showAlert(3);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			}
		},
		linkCardholder2()
		{
			axios.post('forms/form-cardholder.php?link',
			{
				email: this.wavetoget.email,
			}
			,config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					settings.wavetoget.w2gPoint = response.data.points;
					settings.wavetoget.w2gDollar = response.data.dollars;
					settings.setting.wavetoget = response.data.cardholder;
					settings.wavetoget.message = response.data.message;
					settings.wavetoget.alert = 'success';
					settings.showAlert(3);
				}
				else
				{
					settings.wavetoget.message = response.data.message;
					settings.wavetoget.alert = 'danger';
					settings.showAlert(3);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		UnlinkCardholder()
		{
			axios.post('forms/form-cardholder.php?unlink',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					settings.wavetoget.message = response.data.message;
					settings.wavetoget.alert = 'success';
					settings.showAlert(3);
					settings.setting.wavetoget = null;
				}
				else
				{
					settings.wavetoget.message = response.data.message;
					settings.wavetoget.alert = 'danger';
					settings.showAlert(3);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		loadWavetogetInfo()
		{
			if(this.wavetoget.w2gPoint == null)
			{
				axios.post('forms/form-cardholder.php?getinfo',
				{
					cardholder: this.setting.wavetoget
				}
				,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						settings.wavetoget.w2gPoint = response.data.info.points;
						settings.wavetoget.w2gDollar = response.data.info.dollars;
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