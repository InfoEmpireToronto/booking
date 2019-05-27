<?php
$title = 'Doctor';
require('header.php');
?>
<section class="content">
	<div id="users">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Doctors" active>
								<b-container>
									<b-row align-h="end" class="search-row">
										<b-col sm="12" md="4">
												<b-input-group>
													<b-form-input v-model="employee.filter" placeholder="Search" ></b-form-input>
													<b-input-group-append>
														<b-btn :disabled="!employee.filter" @click="employee.filter = ''">Clear</b-btn>
													</b-input-group-append>  
												</b-input-group>
										</b-col>
										<b-col sm="12" md="4" offset-lg="1" lg="3">
											<b-form-select v-model="showactive" :options="employee.activeOptions"></b-form-select>
										</b-col>
									</b-row>
									<b-row>
										<b-table responsive :hover="employee.hover" :items.sync="employee.items" :fields="employee.fields" :sort-by.sync="employee.sortBy" 
											:current-page="employee.currentPage" :per-page="employee.perPage" :filter="employee.filter" @filtered="onFiltered" 
											:sort-by.sync="employee.sortBy" :sort-desc.sync="employee.sortDesc">
											<template slot="availability" slot-scope="row">
												<b-button size="sm" @click.stop="timetable(row.item, row.index, $event.target)" class="mr-1">
													Edit
												</b-button>
											</template>
											<template slot="detail" slot-scope="row">
												<b-button size="sm" @click.stop="row.toggleDetails" class="mr-1">
													{{ row.detailsShowing ? 'Hide' : 'Details'}} 
												</b-button>
											</template>
											<template slot="edit" slot-scope="row">
												<b-button size="sm" @click.stop="info(row.item, row.index, $event.target)" class="mr-1">
													Edit
												</b-button>
											</template>
											<template slot="row-details" slot-scope="row">
												<b-card>
													<b-row class="mb-2">
														<b-col sm="12"><b>Name : </b>{{ row.item.firstname }} {{row.item.lastname}}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Email : </b>{{ row.item.email }}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Title : </b>{{ row.item.title ? row.item.title : 'NULL'}}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Send notification : </b>
															<!-- {{row.item.email_notification && row.item.sms_notification? 'Email, SMS' : ''}} -->
															{{row.item.email_notification && !row.item.sms_notification? 'Email' : 'Disabled'}}
															<!-- {{!row.item.email_notification && row.item.sms_notification? 'SMS' : ''}} -->
														</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Description : </b>{{ row.item.description ? row.item.description : 'NULL'}}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12">
															<b>Availability : </b>
															<table class="time-table text-center" style="display:inline;">
																<thead>
																	<th>Day</th>
																	<th>Availability</th>
																	<th>Start</th>
																	<th>End</th>
																</thead>
																<tbody>
																	<tr v-for="weekday in row.item.availability" :key="weekday.id">
																		<td>{{weekday.day}}</td>
																		<td>{{weekday.active ? 'Yes' : 'No'}}</td>
																		<td>{{weekday.active ? weekday.start_time : 'x'}}</td>
																		<td>{{weekday.active ? weekday.end_time : 'x'}}</td>
																	</tr>
																</tbody>
															</table>
														</b-col>
													</b-row>
											  </b-card>
											</template>
										</b-table>
									</b-row>
									<table-pagination :item="employee"></table-pagination>
									<b-modal id="modalInfo" @hide="resetModal" :title="employee.modalInfo.title">
										<b-form @submit="onSubmit" id="edit-user"> 
											<b-row class="mb-2">
												<label for="edit-firstname" class="col-sm-3">First name</label>
												<b-col sm="9">
													<b-form-input v-model="employee.firstname" id="edit-firstname" required></b-form-input>
												</b-col>
											</b-row>
											<b-row class="mb-2">
												<label for="edit-lastname" class="col-sm-3">Last name</label>
												<b-col sm="9">
													<b-form-input v-model="employee.lastname" id="edit-lastname" required></b-form-input>
												</b-col>
											</b-row>
											<b-row class="mb-2">
												<label for="edit-email" class="col-sm-3">Email</label>
												<b-col sm="9">
													<b-form-input v-model="employee.email" id="edit-email" required></b-form-input>
												</b-col>
											</b-row>
											<b-row class="mb-2">
												<label class="col-sm-3" for="edit-title">Title</label>
												<b-col sm="9">
													<b-form-input v-model="employee.title" id="edit-title"></b-form-input>
												</b-col>
											</b-row>
											<b-row class="mb-2">
												<label class="col-sm-3">Notification</label>
												<b-col sm="9">
													<b-form-checkbox v-model="employee.email_notification" class="registration-notification">Email</b-form-input>&nbsp;&nbsp;
													<!-- <b-form-checkbox v-model="employee.sms_notification" class="registration-notification">SMS</b-form-input> -->
												</b-col>
											</b-row>
											<b-row class="mb-2">
												<label for="edit-description" class="col-sm-3">Description</label>
												<b-col sm="9">
													<b-form-textarea id="edit-description" v-model="employee.description" :rows="3"></b-form-textarea>
												</b-col>
											</b-row>
											<!-- <b-row class="mb-2">
												<label for="edit-location" class="col-sm-3">Location</label>
												<b-col sm="9">
													<b-form-select v-model="employee.location" :options="employee.options" id="edit-location"/>
												</b-col>
											</b-row> -->
										</b-form>
										<div slot="modal-footer" class="w-100">
											<b-row class="mb-4 pb-4">
												<b-col sm="4" cols="6" order="1" order-sm="1">
													<b-button :variant="employee.variant" @click="updateUser">{{employee.activate}}</b-button>
												</b-col>
												<b-col sm="5" cols="12" order="3" order-sm="2">
													<b-alert :show="employee.dismissCountDown" dismissible :variant="employee.alert" @dismissed="employee.dismissCountDown=0">
														{{employee.message}}
													</b-alert>
												</b-col>
												<b-col sm="3" cols="6" order="2" order-sm="3" class="text-right">
													<b-button form="edit-user" type="submit" variant="primary">Save</b-button>
												</b-col>
											</b-row>
										</div>
									</b-modal>
									<b-modal id="modalTimetable" @hide="resetTimetable" :title="employee.modalTimetable.title" size="lg">
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
															<tr v-for="weekday in employee.availability" :key="weekday.id">
																<td><label :for="weekday.elementID">{{weekday.day}}</label></td>
																<td><b-form-checkbox :id="weekday.elementID" v-model="weekday.active"></b-form-input></td>
																<td><b-form-select v-model="weekday.start" :options="adduser.timeOptions" :disabled="!weekday.active"/></td>
																<td><b-form-select v-model="weekday.end" :options="adduser.timeOptions" :disabled="!weekday.active"/></td>
															</tr>
														</tbody>
													</table>
												</b-col>
											</b-row>
										</b-form>
										<div slot="modal-footer" class="w-100">
												<b-row>
													<b-col sm="4">
													</b-col>
													<alert-box :item="employee"></alert-box>
													<b-col sm="3"class="text-right">
														<b-button form="edit-timetable" type="submit" variant="primary">Save</b-button>
													</b-col>
												</b-row>
										   </div>
									</b-modal>
								</b-container>
							</b-tab>
							<b-tab title="Add a doctor" >
								<b-form @submit="addUser">
									<b-row class="mb-2">
										<b-col sm="12" md="2" lg="2">
											<label for="firstname">First name*</label>
										</b-col>
										<b-col sm="12" md="10" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="adduser.firstname" type="text" id="firstname" autocomplete="off" required></b-form-input>
										</b-col>
										<b-col sm="12" md="2" lg="2">
											<label for="lastname">Last name*</label>
										</b-col>
										<b-col sm="12" md="10" lg="4">
											<b-form-input v-model="adduser.lastname" type="text" id="lastname" autocomplete="off" required></b-form-input>
										</b-col>
									</b-row>
									<b-row class="mb-2">
										<b-col sm="12" md="2" lg="2">
											<label for="email">Email*</label>
										</b-col>
										<b-col sm="12" md="10" lg="4" class="mobile-form-margin-bottom">
											<b-form-input v-model="adduser.email" type="email" id="email" autocomplete="off" required></b-form-input>
										</b-col>
										<b-col sm="12" md="2" lg="2">
											<label for="password">Password*</label>
										</b-col>
										<b-col sm="12" md="10" lg="4">
											<b-form-input v-model="adduser.password" type="password" id="password" autocomplete="off" required></b-form-input>
										</b-col>	
									</b-row>
									<b-row class="mb-2">
										<b-col sm="12" md="2" lg="2">
											<label for="title">Title</label>
										</b-col>
										<b-col sm="12" md="10" lg="4">
											<b-form-input id="title" v-model="adduser.title" type="text"></b-form-input>
										</b-col>
										<b-col sm="12" md="2" lg="2">
											<label>Notification</label>
										</b-col>
										<b-col sm="12" md="10" lg="4">
											<b-form-checkbox v-model="adduser.email_notification" class="registration-notification">Email</b-form-checkbox>
											<!-- <b-form-checkbox v-model="adduser.sms_notification" class="registration-notification">SMS</b-form-checkbox> -->
										</b-col>
									</b-row>
									<!-- <b-row class="mb-2">
										<b-col sm="12" md="2">
											<label for="locations">Location</label>
										</b-col>
										<b-col sm="12" md="4">
											<b-form-select v-model="adduser.location" :options="adduser.options" id="locations"/>
										</b-col>
										<b-col sm="2"></b-col>
										<b-col sm="4"></b-col>
									</b-row> -->
									<b-row class="mb-2">
										<b-col sm="12" md="2">
											<label for="description">Description</label>
										</b-col>
										<b-col sm="12" md="10">
											<b-form-textarea id="description" v-model="adduser.description" :rows="3"></b-form-textarea>
										</b-col>
									</b-row>
									<b-row align-h="center" class="search-row">
										<b-col sm="2">
											<label for="tax">Availability</label>
										</b-col>
										<b-col sm="10">
											<table class="time-table">
												<thead>
													<th width="80">Day</th>
													<th width="120">Availability</th>
													<th>Start</th>
													<th>End</th>
												</thead>
												<tbody>
													<tr v-for="weekday in adduser.weekdays" :key="weekday.id">
														<td><label :for="weekday.elementID">{{weekday.day}}</label></td>
														<td><b-form-checkbox :id="weekday.elementID" v-model="weekday.availability"/></td>
														<td><b-form-select v-model="weekday.start" :options="adduser.timeOptions" :disabled="!weekday.availability"/></td>
														<td><b-form-select v-model="weekday.end" :options="adduser.timeOptions" :disabled="!weekday.availability"/></td>
													</tr>
												</tbody>
											</table>
										</b-col>
									</b-row>
									<div class="form-group text-center">
										<b-col>
											<b-button type="submit" variant="primary" class="form-button">Add</b-button>
										</b-col>
									</div>
									<div class="form-group text-center">
										<b-col sm="12" offset-md="2" md="8" offset-lg="3" lg="6">
											<b-alert :show="adduser.dismissCountDown" dismissible :variant="adduser.alert" @dismissed="adduser.dismissCountDown=0">
												{{adduser.message}}
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
<?php
require('footer.php');
?>
<script>
loadMenu('Users');

// axios.post('ajax/ajax-location.php?options',config)
//             .then(function (response)
//             {
//                 if(response.data.success)
//                 {
//                 	users.adduser.options = response.data.locations;
//                 	users.employee.options = response.data.locations;
//                 }
//             })
//             .catch(function (error) {
//             console.log(error);
//             });

axios.post('ajax/ajax-availability.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.adduser.weekdays = response.data.weekdays;
					users.adduser.timeOptions = response.data.time;
					users.employee.timeOptions = response.data.time;
				}
			})
			.catch(function (error) {
			console.log(error);
			});

axios.post('ajax/ajax-availability.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.adduser.defaultWeekdays = response.data.weekdays;
				}
			})
			.catch(function (error) {
			console.log(error);
			});

loadEmployee(1);

function loadEmployee(filter)
{
	axios.post('ajax/ajax-employee.php',config)
		.then(function (response)
		{
			if(response.data.success)
			{
				users.employee.items = response.data.items;
				users.employee.data = response.data.items;
				users.filterData(filter);
			}
		})
		.catch(function (error) {
		console.log(error);
		});
}

// Vue.component('add-user', {
//   props: ['item'],
//   template: '#add-user-temp'
// });

// Vue.component('modal-edit-form', {
//   props: ['item', 'onSubmit'],
//   template: '#modal-editform-temp'
// });

var users = new Vue({
	el: '#users',
	data:
	{
		adduser:
		{
			firstname: null,
			lastname: null,
			password: null,
			email: null,
			title: null,
			//location: null,
			email_notification: false,
			sms_notification: false,
			description: null,
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null,
			weekdays: [],
			timeOptions:[],
			defaultWeekdays: [],
			options: []
		},
		employee:
		{
			items: [],
			data: [],
			currentPage: 1,
			perPage: 10,
			totalRows: null,
			filter: null,
			hover: true,
			sortBy: 'active',
			sortDesc: true,
			modalInfo: { title: '', content: '' },
			modalTimetable: { title: '', content: '' },
			fields:
				[
					{
						key: 'title',
						sortable: true
					},
					{
						key: 'firstname',
						label: 'First name',
						sortable: true
					},
					{
						key: 'lastname',
						label: 'Last name',
						sortable: true
					},
					// {
					// 	key: 'location_name',
					// 	label: 'Location',
					// 	sortable: true
					// },
					{
						key: 'active',
						label: 'Status',
						sortable: true,
						formatter: (value) => { return (value == 1) ? 'Active' : 'Inactive' },
						thStyle: {width:'90px'}
					},
					{
						key: 'detail', 
						sortable: false,
						thStyle: {width:'90px'}
					},
					{
						key:'availability',
						sortable: false,
						tdClass: 'text-center',
						thStyle: {width:'100px'}
					},
					{
						key: 'edit',
						sortable: false,
						thStyle: {width:'50px'}
					}
				],
			firstname: null,
			lastname: null,
			// location: null,
			email: null,
			description: null,
			email_notification: null,
			sms_notification: null,
			title: null,
			// options: [],
			timeOptions: [],
			message: null,
			active: true,
			doctor: null,
			user: null,
			availability: [],
			activate: null,
			variant: null,
			activeOptions: [{value: 'all', text:'All'}, {value: '1', text: 'Active'}, {value: '0', text: 'Inactive'}],
			alert: null,
			dismissSecs: 2,
			dismissCountDown: 0
		},
		showactive: 1
	},
	watch:
	{
		showactive: function(val, oldVal)
		{
			this.filterData(val);
		}
	},
	methods:
	{
		showAlert(section)
		{
			if(section == 1)
			{
				this.adduser.dismissCountDown = this.adduser.dismissSecs;
			}
			if(section == 2)
			{
				this.employee.dismissCountDown = this.employee.dismissSecs;
			}
		},
		reset()
		{
			this.adduser.firstname = this.adduser.lastname = this.adduser.password = this.adduser.email = null;
			this.adduser.location = this.adduser.description = null;
			this.adduser.email_notification = this.addUser.sms_notification = false;
			this.adduser.weekdays = this.adduser.defaultWeekdays;
		},
		addUser (evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-employee.php?adduser',
			{
				firstname: this.adduser.firstname,
				lastname: this.adduser.lastname,
				password: this.adduser.password,
				email: this.adduser.email,
				description: this.adduser.description,
				availability: this.adduser.weekdays,
				email_notification: this.adduser.email_notification,
				sms_notification: this.adduser.sms_notification,
				title: this.adduser.title
			}
			,config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.adduser.alert = 'success';
					users.adduser.message = response.data.message;
					users.showAlert(1);
					loadEmployee(users.showactive);
					setTimeout(function(){
						users.reset();
						}, 2000);
				}
				else
				{
					users.adduser.alert = 'danger';
					users.adduser.message = response.data.message;
					users.showAlert(1);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		filterData(value)
		{
			let temp = [];
			if(value == 'all')
			{
				this.employee.items = this.employee.data;
			}
			if(value == 1)
			{
				for(var i = 0; i != this.employee.data.length; i++)
				{
					if(this.employee.data[i].active == 1)
						temp.push(this.employee.data[i])
				}
				this.employee.items = temp;
			}
			if(value == 0)
			{
				for(var i = 0; i != this.employee.data.length; i++)
				{
					if(this.employee.data[i].active == 0)
						temp.push(this.employee.data[i])
				}
				this.employee.items = temp;
			}
		},
		info (item, index, button)
		{
			this.employee.modalInfo.title = item.firstname + ' ' +item.lastname;
			this.employee.firstname = item.firstname;
			this.employee.lastname = item.lastname;
			this.employee.email = item.email;
			this.employee.title = item.title;
			this.employee.description = item.description;
			this.employee.doctor = item.doctor;
			this.employee.user = item.user;
			this.employee.email_notification = item.email_notification ? true : false;
			this.employee.sms_notification = item.sms_notification ? true : false;
			this.employee.activate = (item.active == 1) ? 'Deactivate' : 'Activate';
			this.employee.variant = (item.active == 1) ? 'danger' : 'success';
			this.$root.$emit('bv::show::modal', 'modalInfo', button)
		},
		timetable(item, index, button)
		{
			this.employee.modalTimetable.title = item.firstname + ' ' +item.lastname;
			axios.post('ajax/ajax-timetable.php?id=' + item.doctor,config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.employee.availability = response.data.availability;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
			this.$root.$emit('bv::show::modal', 'modalTimetable', button)
		},
		resetModal () {
			this.employee.modalInfo.title = this.employee.firstname = this.employee.lastname = this.employee.location = this.employee.doctor = this.employee.user = null;
			this.employee.message = this.employee.email = this.employee.description = this.employee.title = null;
			this.employee.email_notification = this.employee.sms_notification = false;
			this.employee.dismissCountDown = 0;
		},
		resetTimetable()
		{
			this.employee.availability = [];
			this.employee.dismissCountDown = 0;
		},
		onFiltered (filteredItems)
		{
			this.employee.totalRows = filteredItems.length
			this.employee.currentPage = 1
		},
		onSubmit (evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-employee.php?update',
				{
					user: this.employee.user,
					doctor: this.employee.doctor,
					firstname: this.employee.firstname,
					lastname: this.employee.lastname,
					email: this.employee.email,
					description: this.employee.description,
					location: this.employee.location,
					email_notification: this.employee.email_notification,
					sms_notification: this.employee.sms_notification,
					title: this.employee.title
				},
				config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'success';
					users.showAlert(2);
					loadEmployee(users.showactive);
				}
				else
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'danger';
					users.showAlert(2);
				}
				
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		updateUser()
		{
			axios.post('forms/form-update-employee.php?' + this.employee.activate,
				{
					user: this.employee.user
				},
				config)
			.then(function (response)
			{
				if(response.data.success)
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'success';
					users.showAlert(2);
					loadEmployee(users.showactive);
				}
				else
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'danger';
					users.showAlert(2);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		updateTimetable(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-employee.php?timetable',
				{
					availability: this.employee.availability
				},
				config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'success';
					users.showAlert(2);
					loadEmployee(users.showactive);
				}
				else
				{
					users.employee.message = response.data.message;
					users.employee.alert = 'danger';
					users.showAlert(2);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		}
	}
});

</script>