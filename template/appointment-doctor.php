<script src="js/appointment-doctor.js"></script>
<section class="content">
	<div id="appointments">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs v-model="tabIndex">
							<!-- Appointment table tab -->
							<b-tab title="View/Edit">
								<b-row style="margin-bottom:0px;">
									<b-col md="12" offset-lg="4" lg="8" class="text-center text-middle pt-2">
										<a title="Previous month" @click="updateTable(appointmentGraphicTable.table.previousmonth)" class="arrow">
											<i class="fa fa-angle-double-left"></i>
										</a>
										<a style="margin-left:15px;" title="Previous week"  @click="updateTable(appointmentGraphicTable.table.previousweek)" 
											class="arrow">
											<i class="fa fa-angle-left"></i>
										</a>
										<div class="mx-4 arrow">
											<strong>{{appointmentGraphicTable.table.monthdisplay}}</strong><br/>
											<strong>{{appointmentGraphicTable.table.yeardisplay}}</strong><br/>
										</div>
										<a title="Next week" @click="updateTable(appointmentGraphicTable.table.nextweek)" class="arrow">
											<i class="fa fa-angle-right"></i>
										</a>
										<a style="margin-left:15px;" title="Next month" @click="updateTable(appointmentGraphicTable.table.nextmonth)" class="arrow">
											<i class="fa fa-angle-double-right"></i>
										</a>
										<br/>
										<a @click="updateTable(appointmentGraphicTable.table.today)" class="arrow">
											<b>Today</b>											
										</a>
										<a title="Today" @click="updateTable(appointmentGraphicTable.table.today)" class="arrow">
											<i class="fa a fa-angle-down"></i>
										</a> 
									</b-col>
								</b-row>
								<b-row style="margin-bottom:20px;">
									<b-col sm="12">
										<b>Legend: </b>
										<i class="fa fa-square legend" style="color: rgb(255, 250, 205);"></i> Booked
										<i class="fa fa-square legend" style="color: rgb(224, 255, 255);"></i> Paid
										<i class="fa fa-square legend" style="color: #efefef;"></i> Available
										<i class="fa fa-square legend" style="color: #abacac;"></i> Unavailable
										<b-button variant="secondary" size="sm" class="pull-right d-md-block d-lg-none" @click="showCalendar">{{showCalendarText}}</b-button>
									</b-col>
								</b-row>
								<b-row>
									<b-col sm="12" lg="3" class="calendar-container d-lg-block text-center" :class="calendarClass">
										<template v-for="month in appointmentGraphicTable.calendar">
											<table class="calendar text-center">
												<tr><th colspan="7">{{month.name}}</th></tr>
												<tr><td>Sun</td> <td>Mon</td> <td>Tue</td> <td>Wed</td> <td>Thu</td> <td>Fri</td> <td>Sat</td></tr>
												<template v-for="weeks in month.weeks">
													<tr>
														<template v-for="days in weeks"><td :class="days.class">
															<a @click="updateTable(days.date)" :style="{'color': days.class === 'grey' ?'#bbbbbb':'#0e54a4'}">{{days.day}}</a></td></template>
													</tr>
												</template>
											</table>
										</template>
									</b-col>
									<b-col sm="12" md="12" lg="9">
										<span style="position: absolute; top:115px; left:350px; z-index:999" v-if="!appointmentGraphicTable.table.table">
											<i class="fa fa-spinner fa-spin" style="font-size:36px; color:#007bff;"></i>
										</span>
											<!-- Appointment table -->
										<table class="appointment-table table-hover">
											<tr class="text-center">
												<td width="85px"></td>
												<template v-for="week in appointmentGraphicTable.table.week">
													<td :class="week.class">{{week.date}} {{week.day}}</td>
												</template>
											</tr>
											<template v-for="days in appointmentGraphicTable.table.table">
												<tr>
													<td>{{days.time}}</td>
													<template v-for="timeslot in days.timeslot">
														<td :class="timeslot.class" class="link" :id="timeslot.elementID" @click.stop="loadModal(timeslot, days, $event.target, $event)" ></td>
														<b-popover :target="timeslot.elementID" triggers="hover" delay="100" v-if="timeslot.statusDisplay !='Unavailable'">
															<template>
																<table class="info-table">
																	<tr><td><strong>Date:</strong></td><td>{{timeslot.date}}</td></tr>
																	<tr><td><strong>Practitioner:</strong></td><td>{{timeslot.doctorName}}</td></tr>
																	<tr><td><strong>Status:</strong></td><td>{{timeslot.statusDisplay}}</td></tr>
																	<tr v-if="timeslot.patientName !== null"><td><strong>Patient:</strong></td><td>{{timeslot.patientName}}</td></tr>
																	<tr v-if="timeslot.phone !== null"><td><strong>Phone:</strong></td><td>{{timeslot.phone}}</td></tr>
																	<template v-if="timeslot.treatment !== null" v-for="(item, index) in timeslot.treatment">
																		<tr><td><strong>{{ index == 0 ? 'Treatment:' : ''}}</strong></td><td>{{item.name}}</td></tr>
																	</template>
																</table>
															</template>
														</b-popover>
													</template>
												</tr>
											</template>
											<tr class="text-center">
												<td></td>
											<template v-for="week in appointmentGraphicTable.table.week">
												<td :class="week.class">{{week.date}} {{week.day}}</td>
											</template>
											</tr>
										</table>
										<!-- End appointment table -->
									</b-col>
								</b-row>
							</b-tab>
							<!-- End Appointment table tab -->
							<!-- Create an appointment tab -->
							<b-tab title="Create an appointment">
								<b-button v-b-toggle.accordion1 variant="light" class="btn-toggle-title">
									1. Select patient
								</b-button>
								<b-collapse id="accordion1" visible accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col>
												Search by 
												<b-form-radio-group v-model="searchOption" :options="appointment.searchOptions">
												</b-form-radio-group>
											</b-col>
										</b-row>
										<b-row>
											<b-col sm="12" md="6" lg="4">
												<b-form-input v-model="appointment.patientInput" :type="appointment.type" autocomplete="off" 
													:pattern="appointment.pattern" :maxlength="appointment.maxlength" 
													@keyup.native="searchPatient(2)" @focus.native="onFocus(2)" 
													@blur.native="appointment.patientNameOptionsDisplay = 'none'">
												</b-form-input>
												<ul class="dropdown-menu" 
													:style="{display: appointment.patientNameOptionsDisplay}">
													<li v-for="item in appointment.patientNameOptions">
														<a :value="item.id" @click="selectPatient(item, 2)" @mousedown.prevent>
															{{item.firstname}}  {{item.lastname}}
														</a></li>
												</ul>
											</b-col>
										</b-row>
										<b-row style="margin-top: 15px;" v-if="appointment.patientName">
											<b-col sm="12">
												<label>Name :</label> {{appointment.patientName}}
											</b-col>
											<b-col sm="12">
												<label>Phone :</label> {{appointment.phone}}
											</b-col>
											<b-col sm="12">
												<label>Email :</label> {{appointment.email}}
											</b-col>
											<b-col sm="12">
												<b-button variant="primary" @click="gotoStep2">Continue</b-button>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion2 variant="light" class="btn-toggle-title" :disabled="!appointment.patientName">
									2. Select treatment
								</b-button>
								<b-collapse id="accordion2" visible accordion="my-accordion">
									<b-card>
										<b-row>
										<template v-for="(item, index) in appointment.treatmentOptions2">
											<b-col sm="12" md="6" xl="6" class="mb-10 text-left">
												<b-button block :variant="item.selected ? 'primary' : 'outline-primary'" @click="selectTreatment(item)" class="text-left"
													style="white-space: normal;">
													{{item.name}} ({{item.duration}} min) - ${{item.price}}
												</b-button>
											</b-col>
										</template>
										</b-row>
										<b-row>
											<b-col sm="12" v-if="appointment.treatmentOptions2" class="text-center">
												<b-button variant="primary" :disabled="!appointment.step3" @click="gotoStep3">Continue</b-button>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion3 variant="light" class="btn-toggle-title" :disabled="!appointment.step3">
									3. Select date & time
								</b-button>
								<b-collapse id="accordion3" visible accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col sm="12" md="4" lg="3" class="mb-10">
												<b-form-input type="date" v-model="appointment.date" @keyup.native="searchAvailableTimeSlot" @change="searchAvailableTimeSlot" :min="today" required></b-form-input>
											</b-col>
										</b-row>
										<b-row>
											<template v-for="(item, index) in appointment.timeOptions">
												<b-col sm="12" md="2" lg="2" class="mb-10">
													<b-button block :variant="item.selected ? 'primary' : 'outline-primary'" @click="selectTime(item)">{{item.time}}</b-button>
												</b-col>
											</template>
											<b-col sm="12">
												{{appointment.message1}}
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion4 variant="light" class="btn-toggle-title" :disabled="!appointment.step4">
									4. Confirm
								</b-button>
								<b-collapse id="accordion4" visible accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col sm="12">
												<label>Patient :</label> {{appointment.patientName}}
											</b-col>
											<b-col sm="12">
												<label>Date :</label> {{appointment.date}}
											</b-col>
											<b-col sm="12">
												<label>Time :</label> {{appointment.timeDisplay}}
											</b-col>
											<b-col sm="12">
												<label>Treatment :</label>
												<template v-for="(item, index) in appointment.treatments">
													{{index == 0 ? '' : ', '}} {{item.name}}
												</template>
											</b-col>
											<b-col sm="12">
												<label>Duration :</label> {{appointment.duration}} min
											</b-col>
											<b-col sm="12">
												<label>Total :</label> ${{appointment.total}}
											</b-col>
											<b-col sm="12" md="8" lg="6">
												<b-form-textarea id="note" v-model="appointment.note" :rows="3" placeholder="Notes"></b-form-textarea>
											</b-col>
										</b-row>
										<b-row style="margin-top: 15px;">
											<b-col sm="12">
												<b-button variant="primary" :disabled="!appointment.step4" @click="addAppointment">Create</b-button>
												<span>&nbsp;{{appointment.message2}}</span>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
							</b-tab>	
							<!-- End create an appointment tab -->
							<!-- All appointments tab-->
							<b-tab title="All" @click="loadAppointmentData(5)">
								<b-row align-h="end" class="search-row">
									<b-col sm="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="all.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!all.filter" @click="all.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col  class="col-sm-2 text-right">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="2">
										<b-form-select :options="all.pageOptions" v-model="all.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-row>
									<b-table responsive :hover="all.hover" :items.sync="all.items" :fields="all.fields" :sort-by.sync="all.sortBy" 
										:current-page="all.currentPage" :per-page="all.perPage" :filter="all.filter" @filtered="onFiltered5" 
										:sort-by.sync="all.sortBy" :sort-desc.sync="all.sortDesc">
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="loadModal(row.item, '', $event.target)" class="mr-1" v-if="row.item.statusDisplay !== 'Canceled'">
												{{buttonTextFormatter(row.item.statusDisplay)}}
											</b-button>
										</template>
									</b-table>
								</b-row>
								<table-pagination :item="all"></table-pagination>
							</b-tab>
							<!-- End all appointments tab-->
							<!-- Pending appointments tab-->
							<b-tab title="Booked" @click="loadAppointmentData(1)">
								<b-row align-h="end" class="search-row">
									<b-col sm="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="pending.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!pending.filter" @click="pending.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col  class="col-sm-2 text-right">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="2">
										<b-form-select :options="pending.pageOptions" v-model="pending.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-row>
									<b-table responsive :hover="pending.hover" :items.sync="pending.items" :fields="pending.fields" :sort-by.sync="pending.sortBy" 
										:current-page="pending.currentPage" :per-page="pending.perPage" :filter="pending.filter" @filtered="onFiltered1" 
										:sort-by.sync="pending.sortBy" :sort-desc.sync="pending.sortDesc">
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="status" slot-scope="row">
											{{statusFormatter(row.item.status)}}
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="loadModal(row.item, '', $event.target)" class="mr-1">
												{{row.item.status == 4 ? 'View' : 'Edit'}}
											</b-button>
										</template>
									</b-table>
								</b-row>								
									<table-pagination :item="pending"></table-pagination>				
							</b-tab>
							<!-- End pending appointments tab-->
							<!-- Unpaid appointments tab-->
							<b-tab title="Unpaid" @click="loadAppointmentData(2)">
								<b-row align-h="end" class="search-row">
									<b-col sm="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="unpaid.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!unpaid.filter" @click="unpaid.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col  class="col-sm-2 text-right">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="2">
										<b-form-select :options="unpaid.pageOptions" v-model="unpaid.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-row>
									<b-table responsive :hover="unpaid.hover" :items.sync="unpaid.items" :fields="unpaid.fields" :sort-by.sync="unpaid.sortBy" 
										:current-page="unpaid.currentPage" :per-page="unpaid.perPage" :filter="unpaid.filter" @filtered="onFiltered2" 
										:sort-by.sync="unpaid.sortBy" :sort-desc.sync="unpaid.sortDesc">
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="status" slot-scope="row">
											{{statusFormatter(row.item.status)}}
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="loadModal(row.item, '', $event.target)" class="mr-1">
												Pay
											</b-button>
										</template>
									</b-table>
								</b-row>								
									<table-pagination :item="unpaid"></table-pagination>				
							</b-tab>
							<!-- End unpaid appointments tab-->
							<!-- Partial paid appointments tab-->
							<b-tab title="Partial paid" @click="loadAppointmentData(3)">
								<b-row align-h="end" class="search-row">
									<b-col sm="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="partial.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!partial.filter" @click="partial.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col  class="col-sm-2 text-right">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="2">
										<b-form-select :options="partial.pageOptions" v-model="partial.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-row>
									<b-table responsive :hover="partial.hover" :items.sync="partial.items" :fields="partial.fields" :sort-by.sync="partial.sortBy" 
										:current-page="partial.currentPage" :per-page="partial.perPage" :filter="partial.filter" @filtered="onFiltered3" 
										:sort-by.sync="partial.sortBy" :sort-desc.sync="partial.sortDesc">
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="status" slot-scope="row">
											{{statusFormatter(row.item.status)}}
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="loadModal(row.item, '', $event.target)" class="mr-1">
												Pay
											</b-button>
										</template>
									</b-table>
								</b-row>								
									<table-pagination :item="partial"></table-pagination>				
							</b-tab>
							<!-- End partial paid appointments tab-->
							<!-- Paid appointments tab-->
							<b-tab title="Paid" @click="loadAppointmentData(4)">
								<b-row align-h="end" class="search-row">
									<b-col sm="4">
										<b-form-group class="mb-0">
											<b-input-group>
												<b-form-input v-model="paid.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!paid.filter" @click="paid.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col  class="col-sm-2 text-right">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="2">
										<b-form-select :options="paid.pageOptions" v-model="paid.perPage" id="page"/>
									</b-col>
								</b-row>
								<b-row>
									<b-table responsive :hover="paid.hover" :items.sync="paid.items" :fields="paid.fields" :sort-by.sync="paid.sortBy" 
										:current-page="paid.currentPage" :per-page="paid.perPage" :filter="paid.filter" @filtered="onFiltered4" 
										:sort-by.sync="paid.sortBy" :sort-desc.sync="paid.sortDesc">
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="status" slot-scope="row">
											{{statusFormatter(row.item.status)}}
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="loadModal(row.item, '', $event.target)" class="mr-1">
												View
											</b-button>
										</template>
									</b-table>
								</b-row>								
									<table-pagination :item="paid"></table-pagination>				
							</b-tab>
							<!-- End paid appointments tab-->
						</b-tabs>
						<!-- Appointment modal for add -->
						<b-modal id="modalAdd" @hide="resetModal" title="Add an appointment">
							<b-form @submit="addAppt" id="modal-appt-form-add">
								<!-- <div class="form-group">
									<label for="add-doctor" class="col-sm-3">Practitioner</label>
									<b-col sm="9">
										<b-form-input v-model="modalAppointment.doctorName" id="add-doctor" required disabled></b-form-input>
									</b-col>
								</div> -->
								<div class="form-group">
									<label for="add-date" class="col-sm-3">Date</label>
									<b-col sm="9">
										<b-form-input v-model="modalAppointment.date" id="add-date" required disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label for="add-time" class="col-sm-3">Time</label>
									<b-col sm="9">
										<b-form-input v-model="modalAppointment.timeDisplay" id="add-time" required disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label for="add-patient" class="col-sm-3">Patient</label>
									<b-col sm="9">
										Search by <b-form-radio-group v-model="modalSearchOption" :options="modalAppointment.searchOptions"></b-form-radio-group>
										<b-form-input v-model="modalAppointment.patientInput" :type="modalAppointment.type" autocomplete="off" 
													:pattern="modalAppointment.pattern" :maxlength="modalAppointment.maxlength" 
													@keyup.native="searchPatient(1)" @focus.native="onFocus(1)" 
													@blur.native="modalAppointment.patientNameOptionsDisplay = 'none'">
										</b-form-input>
										<ul class="dropdown-menu" 
											:style="{display: modalAppointment.patientNameOptionsDisplay}">
											<li v-for="item in modalAppointment.patientNameOptions">
												<a :value="item.id" @click="selectPatient(item, 1)" @mousedown.prevent>
													{{item.firstname}}  {{item.lastname}}
												</a>
											</li>
										</ul>
									</b-col>
								</div>
								<div class="form-group" v-if="modalAppointment.patientName">
									<b-col sm="9" offset-sm="3">
										<label>Name :</label> {{modalAppointment.patientName}}
									</b-col>
									<b-col sm="9" offset-sm="3">
										<label>Phone :</label> {{modalAppointment.phone}}
									</b-col>
									<b-col sm="9" offset-sm="3">
										<label>Email :</label> {{modalAppointment.email}}
									</b-col>
								</div>
								<div class="form-group">
									<label for="add-note" class="col-sm-3">Note</label>
									<b-col sm="9">
										<b-form-textarea v-model="modalAppointment.note" id="add-note" :rows="3">
									</b-form-textarea>
									</b-col>
								</div>
								<div class="form-group">
									<label for="add-treatment" class="col-sm-3">Treatment</label>
									<b-col sm="9">
										<b-form-select v-model="modalAppointment.firstTreatment" :options="appointment.treatmentOptions" id="add-treatment" required/>
									</b-col>
								</div>
								<treatmenttwo v-for="treatment in modalAppointment.treatment" :item="treatment" v-if="modalAppointment.next > 0"
										:options="appointment.treatmentOptions" :key="treatment.elementID"></treatmenttwo>
							</b-form>
							<div class="form-group">
								<b-col sm="3">
								</b-col>
								<b-col sm="6" class="total">
										<strong>Sub-total: ${{Total2}}</strong><br>
										<strong>Duration: {{Duration2}} min</strong>
								</b-col>
								<b-col sm="3" class="text-right">
									<i class="fa fa-plus-circle" @click="moreTreatment(2)"
										title="Add more treatment"></i>
								</b-col>
							</div>
							<div slot="modal-footer" class="w-100">
								<div class="form-group">
									<b-col sm="9" class="text-left">
										<b-alert :show="modalAppointment.dismissCountDown" dismissible :variant="modalAppointment.alert" @dismissed="modalAppointment.dismissCountDown=0">
										{{message}}
										</b-alert>
									</b-col>
									<b-col sm="3" class="text-right">
										<b-button form="modal-appt-form-add" type="submit" variant="primary" v-if="modalAppointment.add">Add</b-button>
									</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End ppointment modal for add -->
						<!-- Appointment modal for edit -->
						<b-modal id="modalEdit" @hide="resetModal" title="Edit / Cancel appointment" size="lg">
							<b-form @submit="updateAppt" id="modal-appt-form-edit">
								<div class="form-group">
									<b-col sm="3">
										<label for="edit-date">Date</label>
									</b-col>
									<b-col sm="9">
										<b-form-input type="date" v-model="modalAppointment.date" id="edit-date" @keyup.native="searchAvailableDoctor(1)"required></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3">
										<label for="edit-doctor">Practitioner</label>
									</b-col>
									<b-col sm="9">
										<b-form-select v-model="modalDoctor" :options="modalAppointment.availableDoctorOptions" id="edit-doctor" required/>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3">
										<label for="edit-time">Time</label>
									</b-col>
									<b-col sm="9">
										<b-form-select v-model="modalAppointment.time" id="edit-time" :options="modalAppointment.availableTimeOptions" required/>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3">
										<label for="edit-patient">Patient</label>
									</b-col>
									<b-col sm="9">
										<b-form-input :value="modalAppointment.patientName" id="edit-patient" required disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3">
										<label for="edit-note">Note</label>
									</b-col>
									<b-col sm="9">
										<b-form-textarea id="edit-note" v-model="modalAppointment.note" :rows="3"></b-form-textarea>
									</b-col>
								</div>
								<div class="form-group">
									<label for="edit-treatment" class="col-sm-3">Treatment</label>
									<b-col sm="9">
										<b-form-select v-model="modalAppointment.firstTreatment" :options="appointment.treatmentOptions" id="edit-treatment" required/>
									</b-col>
								</div>
								<template v-if="modalAppointment.next > 0" v-for="(item, index) in modalAppointment.treatment">
									<div class="form-group">
										<b-col sm="3" class="text-right" style="padding-right:0px;">
											<i class="fa fa-minus-circle" @click="modalAppointment.treatment.splice(index, 1)" title="Remove treatment"></i>
										</b-col>
										<b-col sm="9">
											<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions"/>
										</b-col>
									</div>
								</template>
							</b-form>
							<div class="form-group">
								<b-col sm="3">
								</b-col>
								<b-col sm="6" class="total">
										<strong>Sub-total: ${{Total2}}</strong><br>
										<strong>Duration: {{Duration2}} min</strong>
								</b-col>
								<b-col sm="3" class="text-right">
									<i class="fa fa-plus-circle" @click="moreTreatment(3)"
										title="Add more treatment"></i>
								</b-col>
							</div>
							<div slot="modal-footer" class="w-100 mb-4 pb-4 mb-sm-0 pb-sm-0">
								<div class="form-group">
									<b-col sm="3" cols="6" order="1" order-sm="1">
										<b-button variant="danger" v-if="modalAppointment.add" title="Cancel this appointment" @click="cancelAppt" v-if="modalAppointment.cancel">Delete</b-button>
									</b-col>
									<b-col sm="6" cols="12" order="3" order-sm="2" class="text-left">
										<b-alert :show="modalAppointment.dismissCountDown" dismissible :variant="alert" @dismissed="modalAppointment.dismissCountDown=0">
										{{message}}
										</b-alert>
									</b-col>
									<b-col sm="3" cols="6" order="2" order-sm="3" class="text-right">
										<b-button form="modal-appt-form-edit" type="submit" variant="primary" v-if="modalAppointment.add" title="Save changes">Save</b-button>
										</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End appointment modal for edit -->
						<!-- Appointment modal for payment -->
						<b-modal id="modalPayment" @hide="resetModal" title="Edit / Cancel appointment" size="lg">
							<b-form @submit="addPayment" id="modal-appt-form-payment">
								<div class="form-group">
									<label class="col-sm-3">Date</label>
									<b-col sm="9">
										<b-form-input type="date" v-model="modalAppointment.date" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Practitioner</label>
									<b-col sm="9">
										<b-form-input v-model="modalAppointment.doctorName" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Time</label>
									<b-col sm="9">
										<b-form-input v-model="modalAppointment.timeDisplay" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Patient</label>
									<b-col sm="9">
										<b-form-input :value="modalAppointment.patientName" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Treatment</label>
									<b-col sm="9">
										<b-form-select v-model="modalAppointment.firstTreatment" :options="appointment.treatmentOptions" disabled/>
									</b-col>
								</div>
								<template v-if="modalAppointment.next > 0" v-for="(item, index) in modalAppointment.treatment">
									<div class="form-group">
										<b-col sm="3" class="text-right"></b-col>
										<b-col sm="9">
											<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions" disabled/>
										</b-col>
									</div>
								</template>
								<div class="form-group">
									<b-col sm="3"><label>Note</label></b-col>
									<b-col sm="9">
										<b-form-textarea v-model="modalAppointment.note" :rows="3" :disabled="modalAppointment.noteDisable"></b-form-textarea>
									</b-col>
								</div>
								<template v-for="(product, index) in modalAppointment.products">
									<div class="form-group">
										<b-col sm="3">
											<label v-if="index === 0">Product</label>
										</b-col>
										<b-col sm="9">
											<b-form-select v-model="product.product" :options="modalAppointment.productOptions" :disabled="product.disable"/>
										</b-col>
									</div>
								</template>
								<div class="form-group">
									<b-col sm="3">
									</b-col>
									<b-col sm="6" class="total">
											<strong>Sub-total: ${{Subtotal}}</strong><br>
											<strong>Tax: ${{Tax}}</strong><br>
											<strong>Total: ${{Total3}}</strong><br>
									</b-col>
									<b-col sm="3" class="text-right"><i v-if="!modalAppointment.payoff" class="fa fa-plus-circle"
										@click="moreProduct" title="Add more product"></i></b-col>
								</div> 
								<template v-for="(payment, index) in modalAppointment.partialpayment" v-if="modalAppointment.partialpayment">
									<div class="form-group">
										<b-col sm="3">
											<label v-if="index === 0">Payment</label>
										</b-col>
										<b-col sm="9">
											<b-form-input :value="paymentFormatter(payment)" disabled></b-form-input>
										</b-col>
									</div>
								</template>
								<div v-if="!modalAppointment.payoff" class="form-group">
									<b-col sm="3">
										<label v-if="!modalAppointment.partialpayment">Payment</label>
									</b-col>
									<b-col cols="12" sm="5">
											<b-form-select v-model="modalAppointment.payment" :options="modalAppointment.paymentOptions" required/>
									</b-col>
									<b-col cols="12" sm="4">
											<b-form-input v-model="modalAppointment.amount" min="0" :max="Remaining" step="any"
											type="number" placeholder="Amount"></b-form-input>
									</b-col>
								</div>
							</b-form>
							<div slot="modal-footer" class="w-100 mb-4 pb-4 ms-sm-0 pb-sm-0">
								<div class="form-group">
									<b-col cols="6" sm="3" order="1" order-sm="1">
										<b-button variant="danger" title="Cancel this appointment" @click="cancelAppt" v-if="modalAppointment.cancel">
											Delete
										</b-button>
									</b-col>
									<b-col sm="6" cols="12" order="3" order-sm="2" class="text-left">
										<b-alert :show="modalAppointment.dismissCountDown" dismissible :variant="alert" @dismissed="modalAppointment.dismissCountDown=0">
										{{message}}
										</b-alert>
									</b-col>
									<b-col cols="6" sm="3" order="2" order-sm="3" class="text-right">
										<b-button form="modal-appt-form-payment" type="submit" variant="primary" v-if="!modalAppointment.payoff">Submit</b-button>
									</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End appointment modal for payment -->
						<!-- Appointment modal for completed -->
						<b-modal id="modalComplete" @hide="resetModal" title="Appointment" :hide-footer="true">
							<div class="form-group">
								<label class="col-sm-3">Date</label>
								<b-col sm="9">
									<b-form-input type="date" v-model="modalAppointment.date" disabled></b-form-input>
								</b-col>
							</div>
							<div class="form-group">
								<label for="edit-doctor" class="col-sm-3">Practitioner</label>
								<b-col sm="9">
									<b-form-input v-model="modalAppointment.doctorName" disabled></b-form-input>
								</b-col>
							</div>
							<div class="form-group">
								<label class="col-sm-3">Time</label>
								<b-col sm="9">
									<b-form-input v-model="modalAppointment.timeDisplay" disabled></b-form-input>
								</b-col>
							</div>
							<div class="form-group">
								<label class="col-sm-3">Patient</label>
								<b-col sm="9">
									<b-form-input :value="modalAppointment.patientName" disabled></b-form-input>
								</b-col>
							</div>
							<hr>
							<div class="form-group">
								<label class="col-sm-3">Treatment</label>
								<b-col sm="9">
									<b-form-select v-model="modalAppointment.firstTreatment" :options="appointment.treatmentOptions" disabled/>
								</b-col>
							</div>
							<template v-if="modalAppointment.next > 0" v-for="(item, index) in modalAppointment.treatment">
								<div class="form-group">
									<b-col sm="3"></b-col>
									<b-col sm="9">
										<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions" disabled/>
									</b-col>
								</div>
							</template>
							<template v-for="(product, index) in modalAppointment.products">
								<div class="form-group">
									<b-col sm="3">
										<label v-if="index === 0">Product</label>
									</b-col>
									<b-col sm="9">
										<b-form-select v-model="product.product" :options="modalAppointment.productOptions" :disabled="product.disable"/>
									</b-col>
								</div>
							</template>
							<hr>
							<template v-if="modalAppointment.note">
								<div class="form-group">
									<b-col sm="3"><label>Note</label></b-col>
									<b-col sm="9">
										<b-form-textarea id="note" v-model="modalAppointment.note" :rows="3" disabled></b-form-textarea>
									</b-col>
								</div>
								<hr>
							</template>
							<div class="form-group">
								<b-col sm="3">
								</b-col>
								<b-col sm="6" class="total">
										<strong>Sub-total: ${{Subtotal}}</strong><br>
										<strong>Tax: ${{modalAppointment.tax}}</strong><br>
										<strong>Total: ${{modalAppointment.total}}</strong><br>
								</b-col>
								<b-col sm="3" class="text-right"></b-col>
							</div> 
							<div class="form-group"  v-for="(payment, index) in modalAppointment.payment" :key="index">
								<b-col sm="3">
									<label v-if="index === 0">Payment</label>
								</b-col>
								<b-col sm="9">
									<b-form-input :value="paymentFormatter(payment)" :key="index" disabled></b-form-input>
								</b-col>
							</div> 
						</b-modal>
						<!-- End appointment modal for completed -->
						<!-- Appointment modal for full edit -->
						<b-modal id="modalFullEdit" @hide="resetModal" title="Edit / Cancel appointment" size="lg">
							<b-form @submit="fullUpdateAppt" id="modal-appt-form-full-edit">
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label>Date</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-input type="date" v-model="modalAppointment.date" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-doctor">Practitioner</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-input v-model="modalAppointment.doctorName" disabled></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-time">Time</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-select v-model="modalAppointment.time" id="full-edit-time" :options="modalAppointment.availableTimeOptions" required/>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-patient">Patient</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-input v-model="modalAppointment.name" type="text" id="full-edit-patient" placeholder="Search name" autocomplete="off"
											@keyup.native="searchPatient(2)" @focus.native="onFocus(2)"  @blur.native="modalAppointment.display = 'none'" required>
										</b-form-input>
										<ul class="dropdown-menu" :style="{display: modalAppointment.display}">
											<li v-for="item in modalAppointment.nameOptions">
												<a :value="item.id" @click="selectPatient(item, 2)" @mousedown.prevent>
													{{item.firstname}}  {{item.lastname}}
												</a>
											</li>
										</ul>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-note">Note</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-textarea id="full-edit-note" v-model="modalAppointment.note" :rows="3"></b-form-textarea>
									</b-col>
								</div>
								<div class="form-group">
									<b-col cols="12" sm="3" lg="2">
										<label for="full-edit-treatment">Treatment</label>
									</b-col>
									<b-col cols="12" sm="9" lg="10">
										<b-form-select v-model="modalAppointment.firstTreatment" :options="appointment.treatmentOptions" id="full-edit-treatment" required/>
									</b-col>
								</div>
								<template v-if="modalAppointment.next > 0" v-for="(item, index) in modalAppointment.treatment">
									<div class="form-group">
										<b-col cols="10" offset-sm="3" sm="7" md="7" offset-lg="2" lg="9" class="col-treatment-input">
											<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions">
											</b-form-select>
										</b-col>
										<b-col cols="2" sm="2" md="2" lg="1" class="text-right col-treatment-button">
											<i class="fa fa-minus-circle" @click="modalAppointment.treatment.splice(index, 1)" title="Remove treatment">
											</i>
										</b-col>
									</div>
								</template>
								<div class="form-group">
									<b-col offset-sm="3" sm="6" offset-lg="2" lg="6" class="total">
										<strong>Duration: {{Duration2}} min</strong>
									</b-col>
									<b-col sm="3" lg="4" class="text-right">
										<i class="fa fa-plus-circle" @click="moreTreatment(3)"
											title="Add more treatment"></i>
									</b-col>
								</div>
								<template v-for="(product, index) in modalAppointment.products">
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label v-if="index === 0">Product</label>
										</b-col>
										<b-col cols="10" sm="7" md="8" lg="9" class="col-treatment-input" v-if="index > 0">
											<b-form-select v-model="product.product" :options="modalAppointment.productOptions" :disabled="product.disable"/>
										</b-col>
										<b-col v-else cols="12" sm="9" lg="10">
											<b-form-select v-model="product.product" :options="modalAppointment.productOptions" :disabled="product.disable"/>
										</b-col>
										<b-col cols="2" sm="2" md="2" lg="1" class="text-right col-treatment-button" v-if="index > 0">
											<i class="fa fa-minus-circle" @click="modalAppointment.products.splice(index, 1)" title="Remove treatment">
											</i>
										</b-col>
									</div>
								</template>
								<div class="form-group">
									<b-col offset-sm="3" sm="6" offset-lg="2" lg="8" class="total">
											<strong>Sub-total: ${{Subtotal}}</strong><br>
											<strong>Tax: ${{Tax2}}</strong><br>
											<strong>Total: ${{Total3}}</strong><br>
									</b-col>
									<b-col sm="3" lg="2" class="text-right"><i v-if="!modalAppointment.payoff" class="fa fa-plus-circle" 
										@click="moreProduct" title="Add more product"></i></b-col>
								</div>
								<div class="form-group" v-for="(payment, index) in modalAppointment.editPayment">
									<b-col sm="3" lg="2">
										<label v-if="index === 0">Payment</label>
									</b-col>
									<b-col cols="10" sm="7" md="7" lg="5" order="1" order-sm="1" class="sm-mobile-form-margin-bottom col-treatment-input">
										<b-form-select v-model="payment.payment.method" :options="modalAppointment.paymentOptions"/>
									</b-col>
									<b-col cols="10" offset-sm="3" sm="7" offset-md="3" order="3" order-sm="3" order-md="3" order-lg="2" offset-lg="0" lg="4" md="7" class="col-treatment-input">
										<b-form-input v-model="payment.payment.paid" min="0" :max="TotalRemaining" step="any" type="number" placeholder="Amount">
										</b-form-input>
									</b-col>
									<b-col v-if="index > 0" cols="2" sm="2" md="2" lg="1" order-sm="2" order="2" order-md="2" order-lg="3" class="sm-mobile-form-margin-bottom col-treatment-button text-right">
										<i class="fa fa-minus-circle" @click="modalAppointment.editPayment.splice(index, 1)" title="Remove payment">
										</i>
									</b-col>
								</div>
								<div class="form-group">
									<b-col offset-sm="3" sm="6" offset-lg="2" lg="8" class="total">
										<strong>Remaining to be paid: ${{TotalRemaining}}</strong>
									</b-col>
									<b-col sm="3" lg="2" class="text-right">
									<i class="fa fa-plus-circle" @click="morePayment" title="Add more payment"></i>
									</b-col>
								</div>
							</b-form>
							<div slot="modal-footer" class="w-100">
								<div class="form-group">
									<b-col cols="12"sm="3">
										<b-button variant="danger" title="Cancel this appointment" @click="cancelAppt">
											Delete
										</b-button>
									</b-col>
									<alert-box :item="modalAppointment" col="6"></alert-box>
									<b-col cols="12" sm="3" class="text-right">
										<b-button form="modal-appt-form-full-edit" type="submit" variant="primary" >Submit</b-button>
									</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End appointment modal for full edit -->
						</b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<?php
require('footer.php');
?>
<script type="x/template" id="treatment-temp">
	<div class="form-group">
		<b-col sm="12" offset-md="3" md="9">
			<b-form-select v-model="item.treatment" :options="options" :id="item.elementID"/>
		</b-col>
	</div>
</script>

<script type="x/template" id="treatment-temp2">
	<div class="form-group">
		<b-col sm="12" offset-md="3" md="9">
			<b-form-select v-model="item.treatment" :options="options" :id="item.elementID"/>
		</b-col>
	</div>
</script>
<script>
loadMenu('Appointments');
load();
</script>