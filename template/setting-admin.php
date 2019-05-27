<section class="content">
	<div id="settings">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Store setting" active>
								<b-form @submit="updateSetting" class="text-center">
									<div class="form-group">
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="storename">Store name</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.storename" type="text" id="storename" required></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="email">Email</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.email" type="email" id="email" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="apikey">API key</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.apikey" type="text" id="apikey" required placeholder="Wavetoget api key"></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="phone">Phone</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.phone" type="text" id="phone" placeholder="xxx-xxx-xxxx" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" 
												maxlength="12" @keyup.native="phoneFormatter" required>
											</b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="address">Address</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.address" type="text" id="address" required></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="city">City</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.city" type="text" id="city" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="province">Province</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-select v-model="setting.province" :options="setting.provinceOptions" id="province" required/>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="country">Country</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-select v-model="setting.country" :options="setting.countryOptions" id="country"/>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="postalcode">Postalcode</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.postalcode" type="text" id="postalcode" required></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="notification">Receive notification</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="text-left">
											<b-form-checkbox v-model="setting.notification" id="notification"></b-form-checkbox>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" class="mt-4">
											<label>Employee availability</label>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="start">Default start time</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-select v-model="setting.start" :options="setting.timeOptions" id="start" required/>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="end">Default end time</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-select v-model="setting.end" :options="setting.timeOptions" id="end" required/>
										</b-col>
									</div>
									<div class="form-group">
										<b-col>
											<b-button type="submit" variant="primary" class="form-button">Save</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
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
										<b-col cols="12" md="3" offset-lg="2" lg="3">
											<label for="password">Current password</label>
										</b-col>
										<b-col cols="12" md="9" lg="5">
											<b-form-input v-model="password.password" type="password" id="password" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" offset-lg="2" lg="3">
											<label for="newpassword">New password</label>
										</b-col>
										<b-col cols="12" md="9" lg="5">
											<b-form-input v-model="password.newpassword" type="password" id="newpassword" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" md="3" offset-lg="2" lg="3">
											<label for="confirm">Confirm password</label>
										</b-col>
										<b-col cols="12" md="9" lg="5">
											<b-form-input v-model="password.confirm" type="password" id="confirm" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col>
											<b-button type="submit" variant="primary">Update</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
											<b-alert :show="password.dismissCountDown" dismissible :variant="password.alert" @dismissed="password.dismissCountDown=0">
												{{password.message}}
											</b-alert>
										</b-col>
									</div>
								</b-form>
							</b-tab>
							<b-tab title="Notification">
								<b-row>
									<b-col sm="12" md="8" class="mb-4">
										<b-card header="Registration email" class="mb-4">
											<b-form @submit="updateForm($event, 1)">
												<div class="form-group">
													<label for="registration_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.registration_subject" type="text" id="registration_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="registration_body" class="mb-0">Body</label>
													<b-form-textarea id="registration_body" v-model="notification.registration_body" :rows="10" required></b-form-textarea>
												</div>
												<!-- <div class="form-inline">
													<label for="registration-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="registration-enable" v-model="notification.registration_notification"></b-form-checkbox>
												</div> -->
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message1}}</span>
												</div>
											</b-form>
										</b-card>
										<b-card header="Appointment confirmation email" class="mb-4">
											<b-form @submit="updateForm($event, 2)">
												<div class="form-group">
													<label for="confirm_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.confirm_subject" type="text" id="confirm_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="confirm_body"class="mb-0">Body</label>
													<b-form-textarea id="confirm_body" v-model="notification.confirm_body" :rows="10" required></b-form-textarea>
												</div>
												<!-- <div class="form-inline">
													<label for="confirmation-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="confirmation-enable" v-model="notification.confirmation_notification"></b-form-checkbox>
												</div> -->
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message2}}</span>
												</div>
											</b-form>
										</b-card>
										<b-card header="Appointment reminder email" class="mb-4">
											<b-form @submit="updateForm($event, 3)">
												<div class="form-group">
													<label for="reminder_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.reminder_subject" type="text" id="reminder_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="reminder_body"class="mb-0">Body</label>
													<b-form-textarea id="reminder_body" v-model="notification.reminder_body" :rows="10" required></b-form-textarea>
												</div>
												<!-- <div class="form-inline">
													<label for="reminder-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="reminder-enable" v-model="notification.reminder_notification"></b-form-checkbox>
												</div> -->
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message3}}</span>
												</div>
											</b-form>
										</b-card>
										<b-card header="Appointment adjustment email" class="mb-4">
											<b-form @submit="updateForm($event, 4)">
												<div class="form-group">
													<label for="edit_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.edit_subject" type="text" id="edit_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="edit_body"class="mb-0">Body</label>
													<b-form-textarea id="edit_body" v-model="notification.edit_body" :rows="10" required></b-form-textarea>
												</div>
												<!-- <div class="form-inline">
													<label for="edit-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="edit-enable" v-model="notification.adjustment_notification"></b-form-checkbox>
												</div> -->
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message4}}</span>
												</div>
											</b-form>
										</b-card>
										<b-card header="Appointment cancelation email" class="mb-4">
											<b-form @submit="updateForm($event, 5)">
												<div class="form-group">
													<label for="cancel_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.cancel_subject" type="text" id="cancel_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="cancel_body"class="mb-0">Body</label>
													<b-form-textarea id="cancel_body" v-model="notification.cancel_body" :rows="10" required></b-form-textarea>
												</div>
												<!-- <div class="form-inline">
													<label for="cancel-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="cancel-enable" v-model="notification.cancelation_notification"></b-form-checkbox>
												</div> -->
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message5}}</span>
												</div>
											</b-form>
										</b-card>
										<b-card header="Birthday promotion email" class="mb-4">
											<b-form @submit="updateForm($event, 6)">
												<div class="form-group">
													<label for="birthday_subject" class="mb-0">Subject</label>
													<b-form-input v-model="notification.birthday_subject" type="text" id="birthday_subject" required>
													</b-form-input>
												</div>
												<div class="form-group">
													<label for="birthday_body"class="mb-0">Body</label>
													<b-form-textarea id="birthday_body" v-model="notification.birthday_body" :rows="10" required></b-form-textarea>
												</div>
												<div class="form-inline">
													<label for="birthday-enable" class="mb-0">Enable</label>
													<b-form-checkbox class="notification-checkbox" id="birthday-enable" v-model="notification.birthday_notification"></b-form-checkbox>
												</div>
												<div class="form-group mt-3">
													<b-button type="submit" variant="primary" class="form-button">Save</b-button>
													<span class="p-2">{{notification.message6}}</span>
												</div>
											</b-form>
										</b-card>
									</b-col>
									<b-col sm="12" md="4">
										<b-card header="Replacement terms">
											<div class="form-group">
												<b>Patient first name: &nbsp;</b>[patientFname]
											</div>
											<div class="form-group">
												<b>Patient last name: &nbsp;</b>[patientLname]
											</div>
											<div class="form-group">
												<b>Doctor first name: &nbsp;</b>[doctorFname]
											</div>
											<div class="form-group">
												<b>Doctor last name: &nbsp;</b>[doctorLname]
											</div>
											<div class="form-group">
												<b>Appointment day: &nbsp;</b>[day]
											</div>
											<div class="form-group">
												<b>Appointment date: &nbsp;</b>[date]
											</div>
											<div class="form-group">
												<b>Appointment time: &nbsp;</b>[time]
											</div>
											<div class="form-group">
												<b>Appointment treatment: &nbsp;</b>[treatment]
											</div>
											<div class="form-group">
												<b>Store name: &nbsp;</b>[store]
											</div>
											<div class="form-group">
												<b>Old Appointment &nbsp;</b>[oldApointmentDate]
											</div>
										</b-card>
									</b-col>
								</b-row>
							</b-tab>
						</b-tabs>
				   </b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<script>
loadMenu('Settings');

axios.post('ajax/ajax-store-setting.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					settings.setting.timeOptions = response.data.time;
					settings.setting.start = response.data.start;
					settings.setting.end = response.data.end;
					settings.setting.storename = response.data.storename;
					settings.setting.email = response.data.email;
					settings.setting.apikey = response.data.apikey;
					settings.setting.phone = response.data.phone;
					settings.setting.address = response.data.address;
					settings.setting.city = response.data.city;
					settings.setting.province = response.data.province;
					settings.setting.country = response.data.country;
					settings.setting.postalcode = response.data.postalcode;
					settings.setting.provinceOptions = response.data.provinceOptions;
					settings.setting.countryOptions = response.data.countryOptions;
					settings.setting.notification = response.data.notification == 1 ? true : false;
					settings.notification.registration_subject = response.data.registration_subject;
					settings.notification.registration_body = response.data.registration_body;
					settings.notification.confirm_subject = response.data.confirmation_subject;
					settings.notification.confirm_body = response.data.confirmation_body;
					settings.notification.cancel_subject = response.data.cancelation_subject;
					settings.notification.cancel_body = response.data.cancelation_body;
					settings.notification.birthday_subject = response.data.birthday_subject;
					settings.notification.birthday_body = response.data.birthday_body;
					settings.notification.edit_subject = response.data.adjustment_subject;
					settings.notification.edit_body = response.data.adjustment_body;
					settings.notification.reminder_subject = response.data.reminder_subject;
					settings.notification.reminder_body = response.data.reminder_body;
					settings.notification.registration_notification = response.data.registration_notification == 1 ? true : false;
					settings.notification.confirmation_notification = response.data.confirmation_notification == 1 ? true : false;
					settings.notification.adjustment_notification = response.data.adjustment_notification == 1 ? true : false;
					settings.notification.cancelation_notification = response.data.cancelation_notification == 1 ? true : false;
					settings.notification.birthday_notification = response.data.birthday_notification == 1 ? true : false;
					settings.notification.reminder_notification = response.data.reminder_notification == 1 ? true : false;
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
			storename: null,
			email: null,
			apikey: null,
			start: null,
			end: null,
			address: null,
			city: null,
			province: null,
			country: null,
			postalcode: null,
			provinceOptions: [],
			countryOptions: [],
			timeOptions:[],
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null,
			phone: null,
			notification: null
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
		notification:
		{
			registration_subject: null,
			registration_body: null,
			confirm_subject: null,
			confirm_body: null,
			reminder_subject: null,
			reminder_body: null,
			edit_subject: null,
			edit_body: null,
			cancel_subject: null,
			cancel_body: null,
			birthday_subject: null,
			birthday_body: null,
			registration_notification: false,
			confirmation_notification: false,
			adjustment_notification: false,
			cancelation_notification: false,
			birthday_notification: false,
			reminder_notification: false,
			message1: null,
			message2: null,
			message3: null,
			message4: null,
			message5: null,
			message6: null
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
		},
		reset()
		{
			settings.password.password = settings.password.newpassword = settings.password.confirm = null;
		},
		updateSetting(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-store-setting.php',
			{
				store_name: this.setting.storename,
				email: this.setting.email,
				api_key: this.setting.apikey,
				start_time: this.setting.start,
				end_time: this.setting.end,
				address: this.setting.address,
				city: this.setting.city,
				province: this.setting.province,
				country: this.setting.country,
				postalcode: this.setting.postalcode,
				phone: this.setting.phone,
				receive_notification: this.setting.notification == true ? 1 : 0
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
		updateForm(evt, section)
		{
			evt.preventDefault();
			let formData = new FormData();
			switch(section)
			{
				case 1:
					formData.append('registration_subject', this.notification.registration_subject);
					formData.append('registration_body', this.notification.registration_body);
					formData.append('registration_notification', this.notification.registration_notification ? 1 : 0);
					break;
				case 2:
					formData.append('confirmation_subject', this.notification.confirm_subject);
					formData.append('confirmation_body', this.notification.confirm_body);
					formData.append('confirmation_notification', this.notification.confirmation_notification ? 1 : 0);
					break;
				case 3:
					formData.append('reminder_subject', this.notification.reminder_subject);
					formData.append('reminder_body', this.notification.reminder_body);
					formData.append('reminder_notification', this.notification.reminder_notification ? 1 : 0);
					break;
				case 4:
					formData.append('adjustment_subject', this.notification.edit_subject);
					formData.append('adjustment_body', this.notification.edit_body);
					formData.append('adjustment_notification', this.notification.adjustment_notification ? 1 : 0);
					break;
				case 5:
					formData.append('cancelation_subject', this.notification.cancel_subject);
					formData.append('cancelation_body', this.notification.cancel_body);
					formData.append('cancelation_notification', this.notification.cancelation_notification ? 1 : 0);
					break;
				case 6:
					formData.append('birthday_subject', this.notification.birthday_subject);
					formData.append('birthday_body', this.notification.birthday_body);
					formData.append('birthday_notification', this.notification.birthday_notification ? 1 : 0);
					break;
			}
			axios.post('forms/form-update-store-setting.php',
			formData
			,config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					switch(section)
					{
						case 1:
							settings.notification.message1 = response.data.message;
							setTimeout(function(){
								settings.notification.message1 = null;
							}, 2500);
							break;
						case 2:
							settings.notification.message2 = response.data.message;
							setTimeout(function(){
								settings.notification.message2 = null;
							}, 2500);
							break;
						case 3:
							settings.notification.message3 = response.data.message;
							setTimeout(function(){
								settings.notification.message3 = null;
							}, 2500);
							break;
						case 4:
							settings.notification.message4 = response.data.message;
							setTimeout(function(){
								settings.notification.message4 = null;
							}, 2500);
							break;
						case 5:
							settings.notification.message5 = response.data.message;
							setTimeout(function(){
								settings.notification.message5 = null;
							}, 2500);
							break;
						case 6:
							settings.notification.message6 = response.data.message;
							setTimeout(function(){
								settings.notification.message6 = null;
							}, 2500);
							break;
					}
				}
				else
				{

				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		phoneFormatter()
		{
			value = this.setting.phone;
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
			this.setting.phone = value;
		}
	}
});
</script>