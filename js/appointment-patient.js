function load()
{
	Vue.component('treatment', {
	  props: ['item', 'options'],
	  template: '#treatment-temp'
	});

	Vue.component('treatmenttwo', {
	  props: ['item', 'options'],
	  template: '#treatment-temp2'
	});

	axios.post('ajax/ajax-appointment-init.php',config)
				.then(function (response)
				{
					if(response.data.success)
					{
						appointments.newappointment.doctors = response.data.doctorOptions;
						appointments.newappointment.treatmentOptions = response.data.treatmentOptions;
						appointments.appointment.treatmentOptions = response.data.treatments;
						appointments.today = response.data.today;
						appointments.apptTable.doctorOptions = response.data.doctors;
						appointments.apptTable.paymentOptions = response.data.methods;
						appointments.apptTable.productOptions = response.data.products;
					}
				})
				.catch(function (error) {
				console.log(error);
				});

	axios.post('ajax/ajax-calendar.php',config)
				.then(function (response)
				{
					if(response.data.success)
					{
						appointments.apptTable.calendar = response.data.calendar;
					}
				})
				.catch(function (error) {
				console.log(error);
				});

	loadApptTable();

	function loadApptTable(doctor, date)
	{
		if(doctor != null && date != null)
		{
			axios.post('ajax/ajax-appointment-table.php',
			{
				doctor: doctor,
				date: date
			}
			,config)
			.then(function (response)
			{
				if(response.data.success)
				{
					appointments.apptTable.table = response.data.appointmentable;
					appointments.apptTable.monday = response.data.monday;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		}
		else if(doctor != null)
		{
			axios.post('ajax/ajax-appointment-table.php',
			{
				doctor: doctor
			}
			,config)
			.then(function (response)
			{				
				if(response.data.success)
				{
					appointments.apptTable.table = response.data.appointmentable;
					appointments.apptTable.monday = response.data.monday;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		}
		else
		{
			axios.post('ajax/ajax-appointment-table.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					appointments.apptTable.table = response.data.appointmentable;
					appointments.apptTable.monday = response.data.monday;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		}
	}

	function loadApptList(status)
	{
		axios.post('ajax/ajax-appointment.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					appointments.all.items = response.data.appointments;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		
	}

	var appointments = new Vue({
		el: '#appointments',
		data:
		{
			tabIndex: 0,
			today: null,
			newappointment:
			{
				doctors: [],
				treatmentOptions: [],
				timeOptions: null,
				doctor: null,
				doctorName: null,
				treatments: [],
				date: null,
				time: null,
				timeDisplay: null,
				duration: 0,
				total: 0,
				step2: false,
				step3: false,
				step3: false,
				step4: false,
				message1: null,
				message2: null
			},
			apptTable:
			{
				doctor: null,
				doctorOptions: [],
				availableDoctorOptions: [],
				availableTimeOptions: [],
				nameOptions: [],
				display: 'none',			
				calendar: [],
				table:[],
				monday: null,
				date: null,
				time: null,
				displaytime: null,
				patient: null,
				patientname: null,
				name: null,
				email: null,
				firstTreatment: {id: null, price: null, duration: null},
				treatment:
					[
						{elementID: 'at' + 0, treatment: {id: null, price: null, duration: null}}
					],
				next: 0,
				t_duration: null,
				message: null,
				dismissSecs: 1,
				dismissCountDown: 0,
				alert: null,
				add: true,
				cancel: true,
				appointment: null,
				paymentOptions: [],
				payment: null,
				subtotal: 0,
				total: 0,
				tax: 0,
				newtax: 0,
				amount: 0,
				partialpayment: null,
				productOptions: [],
				products:
					[
						{product: {id: null, price: null, tax: null}, disable: false}
					],
				payoff:false,
				tabledate: null
			},
			appointment:
			{
				treatmentOptions: []
			},
			all:
			{
				loaded: false,
				update: false,
				items:[],
				data:[],
				currentPage: 1,
				perPage: 10,
				totalRows: null,
				filter: null,
				hover: true,
				sortBy: 'date',
				sortDesc: true,
				fields:
					[
						{
							key: 'date',
							sortable: true,
							thStyle: {width:'130px'}
						},
						{
							key: 'timeDisplay',
							label: 'Time',
							sortable: true,
							thStyle: {width:'120px'}
						},
						{
							key: 'doctorName',
							label: 'Doctor',
							sortable: true
						},
						{
							key: 'treatment',
							label: 'Treatments',
							sortable: true
						},
						{
							key: 'total',
							sortable: true,
							formatter: value => {
								return '$' + value
							}
						},
						{
							key: 'status',
							sortable: true,
							thStyle: {width:'110px'}
						},
						{
							key: 'edit',
							sortable: false,
							thStyle: {width:'70px'}
						}
					],
				alert: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				pageOptions: [10, 25, 50, 100]
			},
			doctor: null,
			practitioner: null,
			modalDoctor: null
		},
		watch:
		{
			modalDoctor: function(val, oldVal)
			{
				if(oldVal != null)
				this.searchAvailableTime(val, 2, this.apptTable.appointment);
			}
		},
		computed:
		{
			Total: function()
			{
				let total = 0;
				if(this.apptTable.firstTreatment.price != null)
				{
					total += parseFloat(this.apptTable.firstTreatment.price);
				}
				for (var i = 0; i !=this.apptTable.treatment.length; i++)
				{
					if(this.apptTable.treatment[i].treatment.price != null)
					total += parseFloat(this.apptTable.treatment[i].treatment.price);
				}
				this.apptTable.subtotal = total;
				return total.toFixed(2);
			},
			Duration: function()
			{
				let total = 0;
				if(this.apptTable.firstTreatment.duration != null)
				{
					total += parseFloat(this.apptTable.firstTreatment.duration);
				}
				for (var i = 0; i !=this.apptTable.treatment.length; i++)
				{
					if(this.apptTable.treatment[i].treatment.duration != null)
					total += parseFloat(this.apptTable.treatment[i].treatment.duration);
				}
				this.apptTable.t_duration = total;
				return total;
			},
			Subtotal: function()
			{
				let total = 0;
				if(this.apptTable.firstTreatment.price != null)
				{
					total += parseFloat(this.apptTable.firstTreatment.price);
				}
				for (var i = 0; i !=this.apptTable.treatment.length; i++)
				{
					if(this.apptTable.treatment[i].treatment.price != null)
						total += parseFloat(this.apptTable.treatment[i].treatment.price);
				}
				for(var i = 0; i!= this.apptTable.products.length; i++)
				{
					if(this.apptTable.products[i].product.price != null)
						total += parseFloat(this.apptTable.products[i].product.price);
				}
				this.apptTable.subtotal = total;
				return total.toFixed(2);
			},
			Tax: function()
			{
				let total = parseFloat(this.apptTable.tax);
				for(var i = 0; i!= this.apptTable.products.length; i++)
				{
					if(this.apptTable.products[i].product.tax != null)
						total += parseFloat(this.apptTable.products[i].product.tax);
				}
				this.apptTable.newtax = total;
				return total.toFixed(2);
			}
		},
		methods:
		{
			showAlert(section)
			{
				if(section == 2)
				{
					this.apptTable.dismissCountDown = this.apptTable.dismissSecs;
					this.apptTable.dismissSecs = 2;
				}
			},
			reset()
			{
				this.newappointment.step2 = this.newappointment.step3 = this.newappointment.step4 = false;
				this.newappointment.treatments = [];
				for (var i = 0; i <  this.newappointment.treatmentOptions.length; i++)
				{
					this.newappointment.treatmentOptions[i].selected = false;
				}
				for (var i = 0; i <  this.newappointment.doctors.length; i++)
				{
					this.newappointment.doctors[i].selected = false;
				}
			},
			addAppointment(evt)
			{
				evt.preventDefault();
				axios.post('forms/form-update-appointment.php?create',
					{
						doctor: this.newappointment.doctor,
						date: this.newappointment.date,
						time: this.newappointment.time,
						treatments: this.newappointment.treatments,
						duration: this.newappointment.duration
					},
					config)
				.then(function (response)
				{
					appointments.newappointment.message2 = response.data.message;
					setTimeout(function(){
						appointments.newappointment.message2 = null;
						}, 2500);
					if(response.data.success)
					{
						setTimeout(function(){
						appointments.reset();
						appointments.$root.$emit('bv::toggle::collapse', 'accordion1');
						}, 2500);
						
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			moreTreatment(section)
			{
				if(this.apptTable.next == 0)
					this.apptTable.next++;
				else
				{
					this.apptTable.treatment.push({elementID: 'at' + this.apptTable.next++, treatment: {id: null, price: null, duration: null}});
				}
			},
			moreProduct()
			{
				this.apptTable.products.push({product: {id: null, price: null, tax: null}, disable:false});
			},
			updateTable(date)
			{
				if(date == null || date == '')
					date = this.apptTable.tabledate;
				loadApptTable(this.practitioner, date);
				this.apptTable.tabledate = date;
			},
			resetModal()
			{
				this.apptTable.date = this.apptTable.time = this.apptTable.email = this.apptTable.name = this.apptTable.patient = this.apptTable.doctor = this.apptTable.payment = null;
				this.apptTable.availableTimeOptions = [];
				this.apptTable.treatment = [{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}];
				this.apptTable.firstTreatment = {id: null, price: null, duration: null};
				this.apptTable.next = this.apptTable.subtotal = this.apptTable.tax = this.apptTable.total = this.apptTable.amount = 0;
				this.apptTable.add = this.apptTable.cancel = true;
				this.apptTable.payoff = false;
				this.apptTable.partialpayment = null;
				this.apptTable.products = [{product:{id: null, price: null, tax: null}, disable: false}];
			},
			searchAvailableDoctor(date)
			{
				if(this.modalDoctor != null)
				{
					axios.post('ajax/ajax-available-appointment.php?date',
					{
						date: date
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.apptTable.availableDoctorOptions = response.data.doctors;
							appointments.apptTable.availableDoctorOptions.splice(0, 1);
						}
						else
						{
							appointments.apptTable.availableDoctorOptions = [];
							appointments.apptTable.message = response.data.message;
							appointments.apptTable.alert = 'danger';
							appointments.showAlert(2);
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			searchAvailableTime(doctor, date, appt)
			{
				if(doctor != null && date != null)
				{
					axios.post('ajax/ajax-available-appointment.php?time',
					{
						doctor: doctor,
						date: date,
						appointment: appt
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.apptTable.availableTimeOptions = response.data.time;
						}
						else
						{
							appointments.apptTable.availableTimeOptions = [];
							appointments.apptTable.message = response.data.message;
							appointments.apptTable.alert = 'danger';
							appointments.showAlert(2);
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			searchAvailableTimeSlot()
			{
				if(this.newappointment.date != null && this.newappointment.doctor != null)
				{
					axios.post('ajax/ajax-available-time.php',
					{
						doctor: this.newappointment.doctor,
						date: this.newappointment.date,
						duration: this.newappointment.duration
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.newappointment.timeOptions = response.data.timeOptions;
							appointments.newappointment.message1 = null;
						}
						else
						{
							appointments.newappointment.timeOptions = null;
							appointments.newappointment.message1 = response.data.message;
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			apptInfo(item, index, button)
			{
				if(item.status == 1) //Booked
				{
					this.apptTable.appointment = item.id;
					this.apptTable.patientname = item.patientName;
					this.apptTable.date = item.date;
					this.apptTable.time = item.time;
					this.apptTable.displaytime = item.timeDisplay;
					this.modalDoctor = item.doctor;
					this.searchAvailableDoctor(item.date);
					this.searchAvailableTime(item.doctor, item.date, item.id);
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
					this.$root.$emit('bv::show::modal', 'modalEdit', button)
				}
				if(item.status == 2) //Finished
				{
					this.apptTable.appointment = item.appointment;
					this.apptTable.date = item.date;
					this.apptTable.time = item.appointmentTime;
					this.apptTable.displaytime = item.timeDisplay;
					this.modalDoctor = item.doctor;
					this.apptTable.tax = item.tax;
					this.apptTable.total = item.total;
					this.apptTable.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
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
								Vue.set(this.apptTable.products, i, {product: item.product[i]});
							}
						}
					}
					
					this.$root.$emit('bv::show::modal', 'modalComplete', button)
				}		
			},
			practitionerName(doctor)
			{
				let result = '';
				for(var i = 0; i != this.apptTable.doctorOptions.length; i++)
				{
					if(this.apptTable.doctorOptions[i].value == doctor)
					{
						result = this.apptTable.doctorOptions[i].text;
						break;
					}
				}
				return result;
			},
			cancelAppt()
			{
				console.log(this.apptTable);
				axios.post('forms/form-update-appointment.php?cancel',
					{
						appointment: this.apptTable.appointment
					},
					config)
				.then(function (response)
				{
					console.log(response.data);
					if(response.data.success)
					{
						appointments.apptTable.message = response.data.message;
						appointments.apptTable.alert = 'success';
						appointments.apptTable.add = false;
						appointments.apptTable.cancel = false;
						appointments.showAlert(2);
						appointments.updateStatus();
					}
					else
					{
						appointments.apptTable.message = response.data.message;
						appointments.apptTable.alert = 'danger';
						appointments.showAlert(2);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			updateAppt(evt)
			{
				evt.preventDefault();
				axios.post('forms/form-update-appointment.php?update',
					{
						appointment: this.apptTable.appointment,
						date: this.apptTable.date,
						time: this.apptTable.time,
						doctor: this.modalDoctor,
						firsttreatment: this.apptTable.firstTreatment,
						treatments: this.apptTable.treatment,
						duration: this.apptTable.t_duration
					},
					config)
				.then(function (response)
				{
					console.log(response.data);
					if(response.data.success)
					{
						appointments.apptTable.message = response.data.message;
						appointments.apptTable.alert = 'success';
						appointments.showAlert(2);
					}
					else
					{
						appointments.apptTable.message = response.data.message;
						appointments.apptTable.alert = 'danger';
						appointments.showAlert(2);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			onFiltered5 (filteredItems) //all
			{
				this.all.totalRows = filteredItems.length
				this.all.currentPage = 1
			},
			statusFormatter(status)
			{
				result = null;
				switch(status)
				{
					case 0:
						result = 'Canceled';
						break;
					case 1:
						result = 'Booked';
						break;
					case 2:
						result = 'Finished';
						break;
				}
				return result;
			},
			loadAppointmentData()
			{
				axios.post('ajax/ajax-appointment.php',config)
				.then(function (response)
				{
					if(response.data.success)
					{
						appointments.all.items = response.data.appointments;
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			selectDoctor(item)
			{
				if(this.newappointment.doctor != item.id)
				{
					this.newappointment.doctor = item.id;
					this.newappointment.doctorName = item.name;
					this.searchAvailableTimeSlot();
					this.newappointment.time = this.newappointment.timeDisplay = null;
					for (var i = 0; i <  this.newappointment.doctors.length; i++)
					{
						this.newappointment.doctors[i].selected = false;
					}
					item.selected = true;
					this.newappointment.step2 = true;
					this.newappointment.step4 = false;
				}
				this.$root.$emit('bv::toggle::collapse', 'accordion2');
			},
			selectTreatment(item)
			{
				item.selected = !item.selected;
				if(item.selected)
				{
					this.newappointment.treatments.push({id: item.id, name: item.name, price: item.price, duration: item.duration, tax: item.tax});
					this.newappointment.duration += item.duration;
					this.newappointment.total += parseFloat(item.price);
				}
				else
				{
					index = this.newappointment.treatments.findIndex(x => x.id === item.id);
					this.newappointment.treatments.splice(index, 1);
					this.newappointment.duration -= item.duration;
					this.newappointment.total -= parseFloat(item.price);
				}
				this.newappointment.time = null;
				if(this.newappointment.treatments.length > 1)
					this.searchAvailableTimeSlot();
				this.newappointment.step3 = this.newappointment.treatments.length > 0 ? true : false;
				this.newappointment.step4 = false;
			},
			gotoStep3()
			{
				this.$root.$emit('bv::toggle::collapse', 'accordion3');
			},
			selectTime(item)
			{
				this.newappointment.time = item.id;
				this.newappointment.timeDisplay = item.time;
				for (var i = 0; i <  this.newappointment.timeOptions.length; i++)
				{
					this.newappointment.timeOptions[i].selected = false;
				}
				item.selected = true;
				this.newappointment.step4 = true;
				this.$root.$emit('bv::toggle::collapse', 'accordion4');
			}
		}
	});

}