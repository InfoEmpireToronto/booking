<section class="content">
	<div id="settings">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Profile setting" active>
								<b-form @submit="updateProfile" class="text-center">
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="firstname">First name</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.firstname" type="text" id="firstname" required></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="lastname">Last name</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.lastname" type="text" id="lastname" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="email">Email</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="setting.email" type="email" id="email" required></b-form-input>
										</b-col>
										<b-col cols="12" sm="3" lg="2">
											<label for="title">Title</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4">
											<b-form-input v-model="setting.title" type="text" id="title" required></b-form-input>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label>Notification</label>
										</b-col>
										<b-col cols="12" sm="9" lg="4" class="mobile-form-margin-bottom text-left">
											<b-form-checkbox v-model="setting.email_notification" class="registration-notification">Email</b-form-input>&nbsp;&nbsp;
											<!-- <b-form-checkbox v-model="setting.sms_notification" class="registration-notification">SMS</b-form-input> -->
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label for="description">Description</label>
										</b-col>
										<b-col cols="12" sm="9" lg="10">
											<b-form-textarea id="description" v-model="setting.description" :rows="3"></b-form-textarea>
										</b-col>
									</div>
									<div class="form-group">
										<b-col>
											<b-button type="submit" variant="primary" class="form-button">Save</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
											<b-alert :show="setting.dismissCountDown" dismissible :variant="alert" @dismissed="setting.dismissCountDown=0">
												{{message}}
											</b-alert>
										</b-col>
									</div>
								</b-form>
							</b-tab>
							<b-tab title="Availability">
								<b-form @submit="updateTimetable" id="edit-timetable">
									<b-row>
										<b-col>
											<table class="time-table text-center">
												<thead>
													<th width="80">Day</th>
													<th width="120">Availability</th>
													<th>Start</th>
													<th>End</th>
												</thead>
												<tbody>
													<tr v-for="weekday in setting.timetable" :key="weekday.id">
														<td><label :for="weekday.elementID">{{weekday.dayDisplay}}</label></td>
														<td><b-form-checkbox :id="weekday.elementID" v-model="weekday.active"></b-form-input></td>
														<td><b-form-select v-model="weekday.start" :options="setting.timeOptions" :disabled="!weekday.active"/></td>
														<td><b-form-select v-model="weekday.end" :options="setting.timeOptions" :disabled="!weekday.active"/></td>
													</tr>
												</tbody>
											</table>
										</b-col>
									</b-row>
									<div class="form-group">
										<b-col class="text-center">
											<b-button type="submit" variant="primary">Save</b-button>
										</b-col>
									</div>
									<div class="form-group">
										<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
											<b-alert :show="timetable.dismissCountDown" dismissible :variant="alert" @dismissed="timetable.dismissCountDown=0">
												{{message}}
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
											<b-alert :show="password.dismissCountDown" dismissible :variant="alert" @dismissed="password.dismissCountDown=0">
												{{message}}
											</b-alert>
										</b-col>
									</div>
								</b-form>
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

axios.post('ajax/ajax-employee.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					settings.setting.firstname = response.data.firstname;
					settings.setting.lastname = response.data.lastname;
					settings.setting.email = response.data.email;
					settings.setting.description = response.data.description;
					settings.setting.timetable = response.data.timetable;
					settings.setting.timeOptions = response.data.timeOptions;
					settings.setting.title = response.data.title;
					settings.setting.email_notification = response.data.email_notification == 1 ? true : false;
					settings.setting.sms_notification = response.data.sms_notification == 1 ? true : false;
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
			title: null,
			email_notification: false,
			sms_notification: false,
			description: null,
			timetable: null,
			timeOptions: [],
			dismissSecs: 2,
			dismissCountDown: 0
		},
		timetable:
		{
			dismissSecs: 2,
			dismissCountDown: 0
		},
		password:
		{
			password: null,
			newpassword: null,
			confirm: null,
			dismissSecs: 2,
			dismissCountDown: 0
		},
		alert: null,
		message: null
	},
	methods:
	{
		showAlert(section)
		{
			if(section == 1)
				this.setting.dismissCountDown = this.setting.dismissSecs;
			if(section == 2)
				this.password.dismissCountDown = this.password.dismissSecs;
			if(section == 3)
				this.timetable.dismissCountDown = this.timetable.dismissSecs;
		},
		reset()
		{
			settings.password.password = settings.password.newpassword = settings.password.confirm = null;
		},
		updateProfile(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-employee.php?update',
			{
				firstname: this.setting.firstname,
				lastname: this.setting.lastname,
				email: this.setting.email,
				title: this.setting.title,
				email_notification: this.setting.email_notification,
				sms_notification: this.setting.sms_notification,
				description: this.setting.description
			}
			,config)
			.then(function (response)
			{
				settings.message = response.data.message;
				if(response.data.success)
				{
					settings.alert = 'success';
				}
				else
				{
					settings.alert = 'danger';
				}
				settings.showAlert(1);
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
					settings.message = response.data.message;
					if(response.data.success)
					{
						settings.alert = 'success';
						setTimeout(function(){
						settings.reset();
						}, 2000);
					}
					else
					{
						settings.alert = 'danger';
					}
					settings.showAlert(2);
				})
				.catch(function (error) {
				console.log(error);
				});
			}
			else
			{
				settings.alert = 'danger';
				settings.message = 'New passwords don\'t match';
				settings.showAlert(2);
			}
		},
		updateTimetable(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-employee.php?timetable',
				{
					availability: this.setting.timetable
				},
				config)
			.then(function (response)
			{
				console.log(response.data);
				settings.message = response.data.message;
				if(response.data.success)
				{
					settings.alert = 'success';
				}
				else
				{
					settings.alert = 'danger';					
				}
				settings.showAlert(3);
			})
			.catch(function (error) {
			console.log(error);
			});
		}
	}
});
</script>