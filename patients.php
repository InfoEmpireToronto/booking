<?php
$title = 'Patient';
require('header.php');
?>
<script src="js/patient.js"></script>
<section class="content">
	<div id="patients">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Patients" active>
								<b-row align-h="end" class="search-row">
									<b-col sm="12" md="6" lg="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="patient.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!patient.filter" @click="patient.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											</b-input-group>
										</b-form-group>
									</b-col>
									<b-col sm="4" md="3" lg="2" class="text-center">
										<label for="page">Per page</label>
									</b-col>
									<b-col sm="8" md="3" lg="2">
										<b-form-select :options="patient.pageOptions" v-model="patient.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-table responsive :hover="patient.hover" :items.sync="patient.items" :fields="patient.fields" 
										:current-page="patient.currentPage" :per-page="patient.perPage" :filter="patient.filter" @filtered="onFiltered" >
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
													<b-col sm="12"><b>Address : </b>{{addressFormatter(row.item)}}</b-col>
														<!-- {{ row.item.address }}, {{row.item.city}}, {{row.item.province_code}}, {{row.item.postalcode}}, {{row.item.country_code}} -->													
												</b-row>
												<!-- <b-row class="mb-2">
													<b-col sm="12"><b>Gender : </b>{{ row.item.gender ? 'Female' : 'Male' }}</b-col>
												</b-row>
												<b-row class="mb-2">
													<b-col sm="12"><b>Marital status : </b>{{row.item.marital_status_name}}</b-col>
												</b-row> -->
												<b-row class="mb-2" v-if="wavetoget">
													<b-col sm="12"><b>Linked to wavetoget : </b>{{row.item.wavetoget ? 'Yes' : 'No'}}</b-col>
												</b-row>
												<b-row class="mb-2">
													<b-col sm="12"><b>Send notification : </b>
													{{row.item.email_notification && row.item.sms_notification? 'Email, SMS' : ''}}
													{{row.item.email_notification && !row.item.sms_notification? 'Email' : ''}}
													{{!row.item.email_notification && row.item.sms_notification? 'SMS' : ''}}
												</b-col>
												</b-row>
											</b-card>
										</template>
								</b-table>
								<table-pagination :item="patient"></table-pagination>
								<!-- modal start-->
								<b-modal id="modalInfo" @hide="resetModal" :title="patient.modalInfo.title" size="lg" :hide-footer="true">
									<b-row>
										<b-col>
											<b-tabs v-model="tabIndex">
												<b-tab title="Profile" active>
													<b-form @submit="updatePatient" id="edit-patient">
														<!-- <modal-edit-form :item="patient"></modal-edit-form> -->
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-firstname">First name</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input v-model="patient.firstname" id="edit-firstname" required></b-form-input>
															</b-col>
															<b-col sm="3" lg="2">
																<label for="edit-lastname">Last name</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input v-model="patient.lastname" id="edit-lastname" required></b-form-input>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-email" >Email</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input v-model="patient.email" id="edit-email" required></b-form-input>
															</b-col>
															<b-col sm="3" lg="2">
																<label for="edit-phone">Phone</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input v-model="patient.phone" id="edit-phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" 
																	maxlength="12" placeholder="xxx-xxx-xxxx" @keyup.native="phoneFormatter(1)" required>
																</b-form-input>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-birthday">Date of birth</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input v-model="patient.birthday" type="date" id="edit-birthday" required></b-form-input>
															</b-col>
															<b-col sm="3" lg="2">
																<label>Reminder</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-checkbox v-model="patient.email_notification" class="registration-notification">Email</b-form-input>&nbsp;&nbsp;
																<b-form-checkbox v-model="patient.sms_notification" class="registration-notification">SMS</b-form-input>
																<!-- v-model="patient.notification"  -->
															</b-col>
															
														</div>
														<!-- <div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-gender">Gender</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-radio-group v-model="patient.gender" :options="patient.genderOptions"></b-form-radio-group>
															</b-col>
															<b-col sm="3" lg="2">
																<label for="edit-marital_status">Marital status</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-select v-model="patient.marital_status" :options="patient.maritalOptions" id="edit-marital_status"/>
															</b-col>
														</div> -->
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-address">Address</label>
															</b-col>
															<b-col sm="9" lg="10">
																<b-form-input v-model="patient.address" id="edit-address"></b-form-input>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-city">City</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input id="edit-city" v-model="patient.city"></b-form-input>
															</b-col>
															<b-col sm="3" lg="2">
																<label for="edit-province">Province</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-select v-model="patient.province" :options="patient.provinceOptions" id="edit-province"/>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="3" lg="2">
																<label for="edit-country">Country</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-select v-model="patient.country" :options="patient.countryOptions" id="edit-country"/>
															</b-col>
															<b-col sm="3" lg="2">
																<label for="edit-postalcode">Postal code</label>
															</b-col>
															<b-col sm="9" lg="4">
																<b-form-input id="edit-postalcode" v-model="patient.postalcode"></b-form-input>
															</b-col>
														</div>
													</b-form>
													<div class="form-group">
														<b-col sm="12" class="text-center">
															<b-button form="edit-patient" type="submit" variant="primary">Save</b-button>
														</b-col>
													</div>
													<div class="form-group">
														<b-col sm="12" lg="8" offset-lg="2">
															<b-alert :show="patient.dismissCountDown" dismissible :variant="patient.alert" @dismissed="patient.dismissCountDown=0">
																{{patient.message}}
															</b-alert>
														</b-col>
													</div>
												</b-tab>
												<!-- <b-tab title="Create an appointment" >
													<b-form @submit="addAppointment">
														<div class="form-group">
															<label for="appt-date" class="col-sm-3">Date</label>
															<b-col sm="9">
																<b-form-input v-model="appointment.date" type="date" id="appt-date" @keyup.native="searchAvailableDoctor()" 
																@change="searchAvailableDoctor()" required></b-form-input>
															</b-col>
														</div>
														<div class="form-group">
															<label for="appt-doctor" class="col-sm-3">Doctor</label>
															<b-col sm="9">
																<b-form-select v-model="doctor" :options="appointment.doctorOptions" id="appt-doctor" 
																	required/>
															</b-col>
														</div>
														<div class="form-group">
															<label for="appt-time" class="col-sm-3">Time</label>
															<b-col sm="9">
																<b-form-select v-model="appointment.time" :options="appointment.timeOptions" id="appt-time" required/>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="3"></b-col>
															<b-col sm="9">
															<strong v-if="appointment.time !== null">Max duration available: {{MaxDuration}} min</strong>
															</b-col>
														</div>
														<div class="form-group">
															<label for="appt-treatment" class="col-sm-3">Treatment</label>
															<b-col sm="9">
																<b-form-select v-model="appointment.firstTreatment" :options="appointment.treatmentOptions" id="appt-treatment" required/>
															</b-col>
														</div>
														<treatment v-for="treatment in appointment.treatment" :item="treatment" v-if="appointment.next > 0"
															:options="appointment.treatmentOptions" :key="treatment.elementID"></treatment>
														<div class="form-group">
															<b-col cols="10" offset-sm="3" sm="6">
															<strong>Total amount: ${{Total}}</strong><br>
															<strong>Duration: {{Duration}} min</strong>
															</b-col>
															<b-col cols="2" sm="3" class="text-right">
																<i class="fa fa-plus-circle" style="font-size:36px;color:#28a745;cursor: pointer" @click="moreTreatment"
																	title="Add more treatment"></i>
															</b-col>
														</div>
														<div class="form-group" style="margin-top:20px;">
															<b-col offset-sm="3" sm="7" class="text-left">
																<b-alert :show="appointment.dismissCountDown" dismissible :variant="appointment.alert" 
																	@dismissed="appointment.dismissCountDown=0">
																{{appointment.message}}
																</b-alert>
															</b-col>
															<b-col sm="2"class="text-right">
																<b-button type="submit" variant="primary">Create</b-button>
															</b-col>
														</div>
													</b-form>
												</b-tab> -->
												<b-tab title="Wavetoget" v-if="wavetoget" @click="loadCardholderInfo(patient.cardholder)">
													<template v-if="patient.cardholder">
														<div class="form-group">
															<b-col sm="12" offset-md="4" md="4" class="text-center">
															<label>Points</label>&nbsp;&nbsp;<span style="line-height: 2.2;">{{patient.w2gPoint}}</span>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="12" offset-md="4" md="4" class="text-center">
															<label>Dollars</label>&nbsp;&nbsp;<span style="line-height: 2.2;">{{patient.w2gDollar}}</span>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="12" offset-md="4" md="4" class="text-center">
																<b-button type="button" variant="primary" @click="UnlinkCardholder">Unlink</b-button>
															</b-col>
														</div>
														<div class="form-group">
															<b-col sm="12" offset-md="3" md="6">
																<b-alert :show="patientWavetoget.dismissCountDown" dismissible :variant="patientWavetoget.alert" 
																	@dismissed="patientWavetoget.dismissCountDown=0">
																	{{patientWavetoget.message}}
																</b-alert>
															</b-col>
														</div>
													</template>
													<template v-else>
														<div class="form-group">
															<b-col class="text-center">
																<b-button type="button" variant="primary" :title="patientWavetoget.email" @click="linkCardholder2">
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
																<label class="col-sm-3" for="w2g-email">Email</label>
																<b-col sm="9">
																	<b-form-input v-model="patient.w2gEmail" id="w2g-email"></b-form-input>
																</b-col>
															</div>
															<div class="form-group">
																<label class="col-sm-3" for="w2g-card">Card number</label>
																<b-col sm="9">
																	<b-form-input v-model="patient.w2gCard" id="w2g-card" placeholder="Wavetoget card number"></b-form-input>
																</b-col>
															</div>
															<div class="form-group">
																<b-col sm="12">
																	<b-button type="submit" variant="primary">Link</b-button>
																</b-col>
															</div>
															<div class="form-group">
																<b-col sm="12" offset-md="3" md="6">
																	<b-alert :show="patientWavetoget.dismissCountDown" dismissible :variant="patientWavetoget.alert" 
																		@dismissed="patientWavetoget.dismissCountDown=0">
																		{{patientWavetoget.message}}
																	</b-alert>
																</b-col>
															</div>
														</b-form>
													</template>
												</b-tab>
											</b-tabs>
										</b-col>
									</b-row>
									<!-- <div slot="modal-footer" class="w-100">
									</div> -->
								</b-modal>
								<!-- modal end-->
							</b-tab>
							<b-tab title="Add a patient">
								<b-button v-b-toggle.accordion1 variant="light" class="btn-toggle-title">
									1. Wavetoget import
								</b-button>
								<b-collapse id="accordion1" visible accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col>
												<b-form-radio-group stacked v-model="addpatient.createOption" :options="addpatient.createOptions"></b-form-radio-group>
											</b-col>
										</b-row>
										<b-row class="mt-3">
											<b-col>
												<b-button variant="primary" @click="gotoStep2">Continue</b-button>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion2 variant="light" class="btn-toggle-title" :style="{display: addpatient.display}">
									2. Import
								</b-button>
								<b-collapse id="accordion2" accordion="my-accordion" :style="{display: addpatient.display}">
									<b-card>
										<div class="form-group">
											Use email or card number to import
										</div>
										<div class="form-group">
											<b-form-radio-group v-model="importOption" :options="addpatient.importOptions"></b-form-radio-group>
											<b-form-input type="text" v-model="addpatient.card" autocomplete="off" :placeholder="addpatient.placeholder"></b-form-input>
										</div>
										<b-row class="mt-3">
											<b-col>
												<b-button variant="primary" @click="gotoStep3" :disabled="!addpatient.card">Continue</b-button>
												<span>&nbsp;{{addpatient.message2}}</span>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion3 variant="light" class="btn-toggle-title" :disabled="!addpatient.step3">
									{{addpatient.stepTitle}} Add patient
								</b-button>
								<b-collapse id="accordion3" accordion="my-accordion">
									<b-card>
										<b-form @submit="addPatient" class="text-center">
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="firstname">First name*</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="mobile-form-margin-bottom">
														<b-form-input v-model="addpatient.firstname" type="text" id="firstname" required></b-form-input>
													</b-col>
													<b-col sm="12" md="3" lg="2">
														<label for="lastname">Last name*</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-input v-model="addpatient.lastname" type="text" id="lastname" required></b-form-input>
													</b-col>
												</div>
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="email">Email*</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="mobile-form-margin-bottom">
														<b-form-input v-model="addpatient.email" type="text" id="email" :disabled="addpatient.cardholder !== null" required></b-form-input>
													</b-col>
													<b-col sm="12" md="3" lg="2">
														<label for="phone">Phone*</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-input v-model="addpatient.phone" type="text" id="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" 
															maxlength="12" placeholder="xxx-xxx-xxxx" @keyup.native="phoneFormatter(2)" required>
														</b-form-input>
													</b-col>
												</div>
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="birthday">Date of birth*</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="mobile-form-margin-bottom">
														<b-form-input v-model="addpatient.birthday" type="date" id="birthday" required></b-form-input>
													</b-col>
													<b-col sm="12" md="3" lg="2">
														<label for="address">Address</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="mobile-form-margin-bottom">
														<b-form-input v-model="addpatient.address" type="text" id="address" ></b-form-input>
													</b-col>
												</div>
												<!-- <div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="gender">Gender</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="text-left">
														<b-form-radio-group v-model="addpatient.gender" :options="addpatient.genderOptions"></b-form-radio-group>
													</b-col>
													<b-col sm="12" md="3" lg="2">
														<label for="marital">Marital status</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-select v-model="addpatient.marital_status" :options="addpatient.maritalOptions" id="marital"/>
													</b-col>
												</div> -->
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="city">City</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-input v-model="addpatient.city" type="text" id="city"></b-form-input>
													</b-col>
													<b-col sm="12" md="3" lg="2" class="mobile-form-margin-bottom">
														<label for="province">Province</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-select v-model="addpatient.province" :options="addpatient.provinceOptions" id="province"/>
													</b-col>
												</div>
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="country">Country</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-select v-model="addpatient.country" :options="addpatient.countryOptions" id="country"/>
													</b-col>
													<b-col sm="12" md="3" lg="2">
														<label for="postalcode">Postal code</label>
													</b-col>
													<b-col sm="12" md="9" lg="4">
														<b-form-input v-model="addpatient.postalcode" type="text" id="postalcode"></b-form-input>
													</b-col>
												</div>
												<div class="form-group">
													<b-col sm="12" md="3" lg="2">
														<label for="notification">Send reminder</label>
													</b-col>
													<b-col sm="12" md="9" lg="4" class="text-left">
														<b-form-checkbox v-model="addpatient.email_notification" class="registration-notification">Email</b-form-input>&nbsp;&nbsp;
														<b-form-checkbox v-model="addpatient.sms_notification" class="registration-notification">SMS</b-form-input>
													</b-col>
												</div>										
												<div class="form-group">
													<b-col sm="12">
														<b-button type="submit" variant="primary" class="form-button">Add</b-button>
													</b-col>
												</div>
												<div class="form-group">
													<b-col sm="12" offset-md="3" md="6">
														<b-alert :show="addpatient.dismissCountDown" dismissible :variant="addpatient.alert" 
															@dismissed="addpatient.dismissCountDown=0">
															{{addpatient.message}}
														</b-alert>
													</b-col>
												</div>
										</b-form>
									</b-card>
								</b-collapse>
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
<script type="x/template" id="add-patient-temp">
	<b-container>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="firstname">First name*</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.firstname" type="text" id="firstname" required></b-form-input>
			</b-col>
			<b-col sm="2">
				<label for="lastname">Last name*</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.lastname" type="text" id="lastname" required></b-form-input>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="birthday">Birthday</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.birthday" type="date" id="birthday"></b-form-input>
			</b-col>
			<b-col sm="2">
				<label for="phone">Phone</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.phone" type="text" id="phone"></b-form-input>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="gender">Gender</label>
			</b-col>
			<b-col sm="4" class="text-left">
				<b-form-group>
					<b-form-radio-group v-model="item.gender" :options="item.genderOptions"></b-form-radio-group>
				</b-form-group>
			</b-col>
			<b-col sm="2">
				<label for="marital">Marital status</label>
			</b-col>
			<b-col sm="4">
				<b-form-select v-model="item.marital_status" :options="item.maritalOptions" id="marital"/>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="address">Address</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.address" type="text" id="address" ></b-form-input>
			</b-col>
			<b-col sm="2">
				<label for="city">City</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.city" type="text" id="city"></b-form-input>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="province">Province</label>
			</b-col>
			<b-col sm="4">
				<b-form-select v-model="item.province" :options="item.provinceOptions" id="province"/>
			</b-col>
			<b-col sm="2">
				<label for="country">Country</label>
			</b-col>
			<b-col sm="4">
				<b-form-select v-model="item.country" :options="item.countryOptions" id="country"/>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="postalcode">Postal code</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.postalcode" type="text" id="postalcode"></b-form-input>
			</b-col>
			<b-col sm="2">
			</b-col>
			<b-col sm="4">
			</b-col>
		</b-row>
		<form-footer :item="item" text="Add"></form-footer>
	</b-container>
</script>

<script type="x/template" id="modal-editform-temp">
	<div>
		<div class="form-group">
			<b-col sm="3" lg="2">
				<label for="edit-firstname">First name</label>
			</b-col>
			<b-col sm="9" lg="4">
				<b-form-input v-model="item.firstname" id="edit-firstname" required></b-form-input>
			</b-col>
			<b-col sm="3" lg="2">
				<label for="edit-lastname">Last name</label>
			</b-col>
			<b-col sm="9" lg="4">
				<b-form-input v-model="item.lastname" id="edit-lastname" required></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-email" class="col-sm-3">Email</label>
			<b-col sm="9">
				<b-form-input v-model="item.email" id="edit-email" required></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-birthday" class="col-sm-3">Birthday</label>
			<b-col sm="9">
				<b-form-input v-model="item.birthday" type="date" id="edit-birthday"></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-phone" class="col-sm-3">Phone</label>
			<b-col sm="9">
				<b-form-input v-model="item.phone" id="edit-phone"></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-gender" class="col-sm-3">Gender</label>
			<b-col sm="9">
				<b-form-radio-group v-model="item.gender" :options="item.genderOptions"></b-form-radio-group>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-marital_status" class="col-sm-3">Marital status</label>
			<b-col sm="9">
				<b-form-select v-model="item.marital_status" :options="item.maritalOptions" id="edit-marital_status"/>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-address" class="col-sm-3">Address</label>
			<b-col sm="9">
				<b-form-input v-model="item.address" id="edit-address"></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-city" class="col-sm-3">City</label>
			<b-col sm="9">
				<b-form-input id="edit-city" v-model="item.city"></b-form-input>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-province" class="col-sm-3">Province</label>
			<b-col sm="9">
				<b-form-select v-model="item.province" :options="item.provinceOptions" id="edit-province"/>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-country" class="col-sm-3">Country</label>
			<b-col sm="9">
				<b-form-select v-model="item.country" :options="item.countryOptions" id="edit-country"/>
			</b-col>
		</div>
		<div class="form-group">
			<label for="edit-postalcode" class="col-sm-3">Postal code</label>
			<b-col sm="9">
				<b-form-input id="edit-postalcode" v-model="item.postalcode"></b-form-input>
			</b-col>
		</div>
	</div>
</script>

<script type="x/template" id="treatment-temp">
	<div class="form-group">
		<label :for="item.elementID" class="col-sm-3"></label>
		<b-col sm="9">
			<b-form-select v-model="item.treatment" :options="options" :id="item.elementID"/>
		 </b-col>
	</div>
</script>

<script>
loadMenu('Patients');
load();

</script>