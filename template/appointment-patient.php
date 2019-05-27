<script src="js/appointment-patient.js"></script>
<section class="content">
	<div id="appointments">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs v-model="tabIndex">
							<b-tab title="Book an appointment">
								<b-button v-b-toggle.accordion1 variant="light" class="btn-toggle-title">
									1. Select practitioner
								</b-button>
								<b-collapse id="accordion1" visible accordion="my-accordion">
									<b-card>
										<b-row>
											<template v-for="(item, index) in newappointment.doctors">
												<b-col sm="12" md="4" xl="3" class="sm-mobile-form-margin-bottom">
													<b-button block :variant="item.selected ? 'primary' : 'outline-primary'" @click="selectDoctor(item)">{{item.name}}</b-button>
												</b-col>
											</template>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion2 variant="light" class="btn-toggle-title" :disabled="!newappointment.step2">
									2. Select treatment
								</b-button>
								<b-collapse id="accordion2" accordion="my-accordion">
									<b-card>
										<b-row>
										<template v-for="(item, index) in newappointment.treatmentOptions">
											<b-col sm="12" md="6" xl="6" class="mb-10 text-left">
												<b-button block :variant="item.selected ? 'primary' : 'outline-primary'" @click="selectTreatment(item)" class="text-left"
													style="white-space: normal;">
													{{item.name}} ({{item.duration}} min) - ${{item.price}}
												</b-button>
											</b-col>
										</template>
										</b-row>
										<b-row>
											<b-col sm="12" v-if="newappointment.treatmentOptions" class="text-center">
												<b-button variant="primary" :disabled="!newappointment.step3" @click="gotoStep3">Continue</b-button>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion3 variant="light" class="btn-toggle-title" :disabled="!newappointment.step3">
									3. Select date & time
								</b-button>
								<b-collapse id="accordion3" accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col sm="12" md="4" lg="3" class="mb-10">
												<b-form-input type="date" v-model="newappointment.date" @keyup.native="searchAvailableTimeSlot" @change="searchAvailableTimeSlot" :min="today" required></b-form-input>
											</b-col>
										</b-row>
										<b-row>
											<template v-for="(item, index) in newappointment.timeOptions">
												<b-col sm="12" md="2" lg="2" class="mb-10">
													<b-button block :variant="item.selected ? 'primary' : 'outline-primary'" @click="selectTime(item)">{{item.time}}</b-button>
												</b-col>
											</template>
											<b-col sm="12">
												{{newappointment.message1}}
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
								<b-button v-b-toggle.accordion4 variant="light" class="btn-toggle-title" :disabled="!newappointment.step4">
									4. Confirm
								</b-button>
								<b-collapse id="accordion4" accordion="my-accordion">
									<b-card>
										<b-row>
											<b-col sm="12">
												<label>Practitioner :</label> {{newappointment.doctorName}}
											</b-col>
											<b-col sm="12">
												<label>Date :</label> {{newappointment.date}}
											</b-col>
											<b-col sm="12">
												<label>Time :</label> {{newappointment.timeDisplay}}
											</b-col>
											<b-col sm="12">
												<label>Treatment :</label>
												<template v-for="(item, index) in newappointment.treatments">
													{{index == 0 ? '' : ', '}} {{item.name}}
												</template>
											</b-col>
											<b-col sm="12">
												<label>Duration :</label> {{newappointment.duration}} min
											</b-col>
											<b-col sm="12">
												<label>Total :</label> ${{newappointment.total}}
											</b-col>
											<b-col sm="12">
												<b-button variant="primary" :disabled="!newappointment.step4" @click="addAppointment">Book</b-button>
												<span>&nbsp;{{newappointment.message2}}</span>
											</b-col>
										</b-row>
									</b-card>
								</b-collapse>
							</b-tab>

							<!-- All appointments tab-->
							<b-tab title="My appointments" @click="loadAppointmentData">
								<b-row align-h="end" class="search-row">
									<b-col sm="12" md="6" lg="4">
										<b-form-group class="mb-0">
										   	<b-input-group>
											   	<b-form-input v-model="all.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!all.filter" @click="all.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											   </b-input-group>
										</b-form-group>
									</b-col>
									<b-col sm="12" md="3" lg="2" class="text-center">
									<label for="page">Per page</label>
									</b-col>
									<b-col sm="12" md="3" lg="2">
										<b-form-select :options="all.pageOptions" v-model="all.perPage" id="page"/>
									</b-col>
								</b-row>
								<div class="form-group">
									<b-table responsive :hover="all.hover" :items.sync="all.items" :fields="all.fields" :sort-by.sync="all.sortBy" 
										:current-page="all.currentPage" :per-page="all.perPage" :filter="all.filter" @filtered="onFiltered5" 
										:sort-by.sync="all.sortBy" :sort-desc.sync="all.sortDesc">
										<template slot="status" slot-scope="row">
											{{statusFormatter(row.item.status)}}
										</template>
										<template slot="treatment" slot-scope="row">
											<div v-for="treatment in row.item.treatment">
												{{treatment.name}} ({{treatment.duration}} min)
											</div>
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="apptInfo(row.item, row.index, $event.target)" class="mr-1">
		  										{{row.item.status == 2 ? 'View' : 'Edit'}}
											</b-button>
										</template>
									</b-table>
								</div>								
									<table-pagination :item="all"></table-pagination>				
							</b-tab>
							<!-- End all appointments tab-->
						</b-tabs>

						<!-- Appointment modal for completed -->
						<b-modal id="modalComplete" @hide="resetModal" title="Appointment" :hide-footer="true">
							<div class="form-group">
								<label class="col-sm-3">Date</label>
								<b-col sm="9">
									<b-form-input type="date" v-model="apptTable.date" disabled></b-form-input>
								</b-col>
							</div>
 							<div class="form-group">
								<label for="edit-doctor" class="col-sm-3">Practitioner</label>
								<b-col sm="9">
									<b-form-input :value="practitionerName(modalDoctor)" disabled></b-form-input>
								</b-col>
							</div>
						   <div class="form-group">
								<label class="col-sm-3">Time</label>
								<b-col sm="9">
									<b-form-input v-model="apptTable.displaytime" disabled></b-form-input>
								</b-col>
							</div>
							<hr>
							<div class="form-group">
								<label class="col-sm-3">Treatment</label>
								<b-col sm="9">
									<b-form-select v-model="apptTable.firstTreatment" :options="appointment.treatmentOptions" disabled/>
								</b-col>
							</div>
							<template v-if="apptTable.next > 0" v-for="(item, index) in apptTable.treatment">
								<div class="form-group">
									<b-col sm="9" offset-md="3" md="9">
										<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions" disabled/>
									</b-col>
								</div>
							</template>
							<template v-for="(item, index) in apptTable.products">
								<div class="form-group" v-if="item.product.id !== null">
									<b-col sm="3"><label v-if="index === 0">Product</label></b-col>
									<b-col sm="9">
										<b-form-select v-model="item.product" :options="apptTable.productOptions" disabled/>
									</b-col>
								</div>
							</template>
							<hr>
							<b-row>
								<b-col sm="3">
								</b-col>
								<b-col sm="6" class="total">
										<strong>Sub-total: ${{Subtotal}}</strong><br>
										<strong>Tax: ${{apptTable.tax}}</strong><br>
										<strong>Total: ${{apptTable.total}}</strong><br>
								</b-col>
								<b-col sm="3" class="text-right"></b-col>
							</b-row> 
							<!-- <div slot="modal-footer" class="w-100">
									  		</div> -->
						</b-modal>
						<!-- End appointment modal for completed -->
						<!-- Appointment modal for edit -->
						<b-modal id="modalEdit" @hide="resetModal" title="Edit / Cancel appointment" size="lg">
							<b-form @submit="updateAppt" id="modal-appt-form-edit">
							<div class="form-group">
								<label for="edit-date" class="col-sm-3">Date</label>
								<b-col sm="9">
									<b-form-input type="date" v-model="apptTable.date" id="edit-date" @keyup.native="searchAvailableDoctor(2)" :min="today" required></b-form-input>
								</b-col>
							</div>
 							<div class="form-group">
								<label for="edit-doctor" class="col-sm-3">Practitioner</label>
								<b-col sm="9">
									<b-form-select v-model="modalDoctor" :options="apptTable.availableDoctorOptions" id="edit-doctor" required/>
								</b-col>
							</div>
							<div class="form-group">
								<label for="edit-time" class="col-sm-3">Time</label>
								<b-col sm="9">
									<b-form-select v-model="apptTable.time" id="edit-time" :options="apptTable.availableTimeOptions" required/>
								</b-col>
							</div>
							<div class="form-group">
								<label for="edit-treatment" class="col-sm-3">Treatment</label>
								<b-col sm="9">
									<b-form-select v-model="apptTable.firstTreatment" :options="appointment.treatmentOptions" id="edit-treatment" required/>
								</b-col>
							</div>
							<template v-if="apptTable.next > 0" v-for="(item, index) in apptTable.treatment">
								<div class="form-group">
									<b-col sm="3" class="text-right" style="padding-right:0px;"><i class="fa fa-minus-circle" style="font-size:36px;color:#dc3545;cursor: pointer" 
									@click="apptTable.treatment.splice(index, 1)" title="Remove treatment"></i></b-col>
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
										<strong>Sub-total: ${{Total}}</strong><br>
								 		<strong>Duration: {{Duration}} min</strong>
								</b-col>
								<b-col sm="3" class="text-right">
									<i class="fa fa-plus-circle" style="font-size:36px;color:#28a745;cursor: pointer" @click="moreTreatment"
										title="Add more treatment"></i>
								</b-col>
							</div>
							<div slot="modal-footer" class="w-100">
								<div class="form-group">
									<b-col sm="3">
										<b-button variant="danger" v-if="apptTable.add" title="Cancel this appointment" @click="cancelAppt" v-if="apptTable.cancel">Cancel</b-button>
									</b-col>
									<alert-box :item="apptTable" col="6"></alert-box>
									<b-col sm="3"class="text-right">
										<b-button form="modal-appt-form-edit" type="submit" variant="primary" v-if="apptTable.add" title="Save changes">Save</b-button>
									</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End appointment modal for edit -->
						</b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<script type="x/template" id="treatment-temp">
	<div class="form-group">
		<b-col sm="3"></b-col>
		<b-col sm="9">
			<b-form-select v-model="item.treatment" :options="options" :id="item.elementID"/>
		</b-col>
	</div>
</script>
<script type="x/template" id="treatment-temp2">
	<div class="form-group">
		<b-col sm="3"></b-col>
		<b-col sm="9">
			<b-form-select v-model="item.treatment" :options="options" :id="item.elementID"/>
		</b-col>
	</div>
</script>
<script>
loadMenu('Appointments');
load();
</script>