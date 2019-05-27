						<!-- Appointment modal for full edit -->
						<b-modal id="modalFullEdit" @hide="resetModal" title="Edit / Cancel appointment" size="lg">
							<b-form @submit="fullUpdateAppt" id="modal-appt-form-full-edit">
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-date">Date</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-input type="date" v-model="apptTable.date" id="full-edit-date" @keyup.native="searchAvailableDoctor(2)"required></b-form-input>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-doctor">Practitioner</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-select v-model="modalDoctor" :options="apptTable.availableDoctorOptions" id="full-edit-doctor" required/>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-time">Time</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-select v-model="apptTable.time" id="full-edit-time" :options="apptTable.availableTimeOptions" required/>
									</b-col>
								</div>
								<div class="form-group">
									<b-col sm="3" lg="2">
										<label for="full-edit-patient">Patient</label>
									</b-col>
									<b-col sm="9" lg="10">
										<b-form-input v-model="apptTable.name" type="text" id="full-edit-patient" placeholder="Search name" autocomplete="off"
											@keyup.native="searchPatient(2)" @focus.native="onFocus(2)"  @blur.native="apptTable.display = 'none'" required>
										</b-form-input>
										<ul class="dropdown-menu" :style="{display: apptTable.display}">
											<li v-for="item in apptTable.nameOptions">
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
										<b-form-textarea id="full-edit-note" v-model="apptTable.note" :rows="3"></b-form-textarea>
									</b-col>
								</div>
								<div class="form-group">
									<b-col cols="12" sm="3" lg="2">
										<label for="full-edit-treatment">Treatment</label>
									</b-col>
									<b-col cols="12" sm="9" lg="10">
										<b-form-select v-model="apptTable.firstTreatment" :options="appointment.treatmentOptions" id="full-edit-treatment" required/>
									</b-col>
								</div>
								<template v-if="apptTable.next > 0" v-for="(item, index) in apptTable.treatment">
									<div class="form-group">
										<b-col cols="10" offset-sm="3" sm="7" md="7" offset-lg="2" lg="9" class="col-treatment-input">
											<b-form-select v-model="item.treatment" :options="appointment.treatmentOptions">
											</b-form-select>
										</b-col>
										<b-col cols="2" sm="2" md="2" lg="1" class="text-right col-treatment-button">
											<i class="fa fa-minus-circle" style="font-size:36px;color:#dc3545;cursor: pointer" 
												@click="apptTable.treatment.splice(index, 1)" title="Remove treatment">
											</i>
										</b-col>
									</div>
								</template>
								<div class="form-group">
									<b-col offset-sm="3" sm="6" offset-lg="2" lg="6" class="total">
										<strong>Duration: {{Duration2}} min</strong>
									</b-col>
									<b-col sm="3" lg="4" class="text-right">
										<i class="fa fa-plus-circle" style="font-size:36px;color:#28a745;cursor: pointer" @click="moreTreatment(3)"
											title="Add more treatment"></i>
									</b-col>
								</div>
								<template v-for="(product, index) in apptTable.products">
									<div class="form-group">
										<b-col cols="12" sm="3" lg="2">
											<label v-if="index === 0">Product</label>
										</b-col>
										<b-col cols="10" sm="7" md="8" lg="9" class="col-treatment-input" v-if="index > 0">
											<b-form-select v-model="product.product" :options="apptTable.productOptions" :disabled="product.disable"/>
										</b-col>
										<b-col v-else cols="12" sm="9" lg="10">
											<b-form-select v-model="product.product" :options="apptTable.productOptions" :disabled="product.disable"/>
										</b-col>
										<b-col cols="2" sm="2" md="2" lg="1" class="text-right col-treatment-button" v-if="index > 0">
											<i class="fa fa-minus-circle" style="font-size:36px;color:#dc3545;cursor: pointer" 
												@click="apptTable.products.splice(index, 1)" title="Remove treatment">
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
									<b-col sm="3" lg="2" class="text-right"><i v-if="!apptTable.payoff" class="fa fa-plus-circle" style="font-size:36px;color:#28a745;cursor: pointer" 
										@click="moreProduct" title="Add more product"></i></b-col>
								</div>
								<div class="form-group" v-for="(payment, index) in apptTable.editPayment">
									<b-col sm="3" lg="2">
										<label v-if="index === 0">Payment</label>
									</b-col>
									<b-col cols="10" sm="7" md="7" lg="5" order="1" order-sm="1" class="sm-mobile-form-margin-bottom col-treatment-input">
										<b-form-select v-model="payment.payment.method" :options="apptTable.paymentOptions"/>
									</b-col>
									<b-col cols="10" offset-sm="3" sm="7" offset-md="3" order="3" order-sm="3" order-md="3" order-lg="2" offset-lg="0" lg="4" md="7" class="col-treatment-input">
										<b-form-input v-model="payment.payment.paid" min="0" :max="TotalRemaining" step="any" type="number" placeholder="Amount">
										</b-form-input>
									</b-col>
									<b-col v-if="index > 0" cols="2" sm="2" md="2" lg="1" order-sm="2" order="2" order-md="2" order-lg="3" class="sm-mobile-form-margin-bottom col-treatment-button text-right">
										<i class="fa fa-minus-circle" style="font-size:36px;color:#dc3545;cursor: pointer" 
											@click="apptTable.editPayment.splice(index, 1)" title="Remove payment">
										</i>
									</b-col>
								</div>
								<div class="form-group">
									<b-col offset-sm="3" sm="6" offset-lg="2" lg="8" class="total">
										<strong>Remaining to be paid: ${{TotalRemaining}}</strong>
									</b-col>
									<b-col sm="3" lg="2" class="text-right">
									<i class="fa fa-plus-circle" style="font-size:36px;color:#28a745;cursor: pointer" 
										@click="morePayment" title="Add more payment"></i>
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
									<alert-box :item="apptTable" col="6"></alert-box>
									<b-col cols="12" sm="3" class="text-right">
										<b-button form="modal-appt-form-full-edit" type="submit" variant="primary" >Submit</b-button>
									</b-col>
								</div>
							</div>
						</b-modal>
						<!-- End appointment modal for full edit -->



						searchApptInfo(item, index, button) //datatable
			{
				console.log(item);
				if(item.status == 4) //paid
				{
					this.apptTable.appointment = item.id;
					this.apptTable.name = item.patientName;
					this.apptTable.patient = item.patient;
					this.apptTable.date = item.date;
					this.apptTable.time = item.time;
					this.apptTable.doctor = item.doctor;
					this.modalDoctor = item.doctor;
					this.apptTable.doctorName = item.doctorName;
					this.apptTable.displaytime = item.timeDisplay;
					this.searchAvailableDoctor(2);
					this.searchAvailableTime(this.modalDoctor, 2, item.id);
					this.apptTable.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.apptTable.tax = item.tax;
					this.apptTable.total = item.total;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.apptTable.treatment, i - 1 , {elementID: 'at' + this.apptTable.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					if(item.product != null)
					{
						if(item.product.length >= 1)
						{
							for(var i = 0; i != item.product.length; i++)
							{
								Vue.set(this.apptTable.products, i, {product: item.product[i], disable:false});
							}
							// Vue.set(this.apptTable.products, item.product.length, {product:{id: null, price: null, tax: null}, disable: false});
						}
					}
					if(item.payment.length != null)
					{
						for(var i = 0; i != item.payment.length; i++)
							{
								Vue.set(this.apptTable.editPayment, i, {payment:{method: item.payment[i].method, paid: item.payment[i].paid}});
							}
					}
					this.$root.$emit('bv::show::modal', 'modalFullEdit', button)
				}
				if(item.status == 1) //pending
				{
					
						this.apptTable.appointment = item.id;
						this.apptTable.patientname = item.patientName;
						this.apptTable.date = item.date;
						this.apptTable.time = item.time;
						this.apptTable.displaytime = item.timeDisplay;
						this.modalDoctor = item.doctor;
						this.searchAvailableDoctor(2);
						this.searchAvailableTime(this.modalDoctor, 2, item.id);
						this.apptTable.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
						if(item.treatment.length > 1)
						{
							for(var i = 1; i != item.treatment.length; i++)
							{
								Vue.set(this.apptTable.treatment, i - 1 , {elementID: 'at' + this.apptTable.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
							}
						}
						this.$root.$emit('bv::show::modal', 'modalEdit', button)
				}	
				if(item.status == 2 || item.status == 3) //unpaid or partial paid
				{
					this.apptTable.appointment = item.id;
					this.apptTable.name = item.patientName;
					this.apptTable.patient = item.patient;
					this.apptTable.date = item.date;
					this.apptTable.time = item.time;
					this.modalDoctor = item.doctor;
					this.apptTable.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.apptTable.tax = item.tax;
					this.apptTable.total = item.total;
					this.apptTable.payment = item.payment;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.apptTable.treatment, i - 1 , {elementID: 'at' + this.apptTable.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					this.$root.$emit('bv::show::modal', 'modalPayment', button)
				}
			},