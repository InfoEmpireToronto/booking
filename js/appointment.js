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
						appointments.appointment.treatmentOptions = response.data.treatments;
						appointments.appointment.treatmentOptions2 = response.data.treatmentOptions;
						appointments.appointment.doctors = response.data.doctorOptions;
						appointments.appointmentGraphicTable.doctorOptions = response.data.doctors;
						appointments.practitioner = response.data.doctor;
						appointments.modalAppointment.paymentOptions = response.data.methods;
						appointments.modalAppointment.treatmentTax = response.data.treatmentTax;
						appointments.financial.doctorOptions = response.data.financialDoctors;
						appointments.search.doctorOptions = response.data.searchDoctors;
						appointments.modalAppointment.productOptions = response.data.products;
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
						appointments.appointmentGraphicTable.calendar = response.data.calendar;
					}
				})
				.catch(function (error) {
				console.log(error);
				});

	loadmodalAppointment();

	function loadmodalAppointment(doctor, date)
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
					appointments.appointmentGraphicTable.table = response.data.appointmentable;
					appointments.appointmentGraphicTable.monday = response.data.monday;
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
					appointments.appointmentGraphicTable.table = response.data.appointmentable;
					appointments.appointmentGraphicTable.monday = response.data.monday;
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
					appointments.appointmentGraphicTable.table = response.data.appointmentable;
					appointments.appointmentGraphicTable.monday = response.data.monday;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		}
	}

	function loadApptList(status)
	{
		if(status == 6)
		{
			axios.post('ajax/ajax-financial-activity.php',
				{
					from: appointments.financial.from,
					to: appointments.financial.to,
					doctor: appointments.financialDoctor
				}
				,config)
				.then(function (response)
				{
						appointments.financial.items = response.data.financial;
						appointments.financial.total = response.data.total;
						if(response.data.today != null)
						{
							appointments.financial.from = response.data.today;
							appointments.financial.to = response.data.today;
						}						
				})
				.catch(function (error) {
				console.log(error);
				});
		}
		else
		{
			axios.post('ajax/ajax-appointment.php?status=' + status,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						switch(status)
						{
							case 5:
								appointments.all.items = response.data.appointments;
								break;
							case 1:
								appointments.pending.items = response.data.appointments;
								break;
							case 2:
								appointments.unpaid.items = response.data.appointments;
								break;
							case 3:
								appointments.partial.items = response.data.appointments;
								break;
							case 4:
								appointments.paid.items = response.data.appointments;
								break;
						}
					}
				})
				.catch(function (error) {
				console.log(error);
				});
		}
	}
	var fieldDefine = [
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
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
					];
	var fieldDefine2 = [
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
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
					];
	var appointments = new Vue({
		el: '#appointments',
		data:
		{
			tabIndex: 0,
			message: null,
			alert: null,
			doctor: null,
			practitioner: null,
			modalDoctor: null,
			financialDoctor: 0,
			searchOption: 1,
			modalSearchOption: 1,
			today: null,
			showCalendarText: 'Show calendar',
			calendarClass: 'd-none',
			spinner: true,
			appointmentGraphicTable: 
			{
				table:[],
				calendar: [],
				doctorOptions: [],
				monday: null
			},
			modalAppointment:
			{
				searchOptions:[{text: 'Phone', value: 1}, {text: 'Name', value: 2}, {text: 'Email', value: 3}],
				appointment: null,
				date: null,
				time: null,
				timeDisplay: null,
				doctor: null,
				doctorName: null,
				patient: null,
				email: null,
				phone: null,
				note: null,
				type: 'text',
				pattern: null,
				maxlength: 12,
				patientInput: null,
				patientNameInput: null,
				patientName: null,
				availableDoctorOptions: [],
				patientNameOptions: [],
				availableTimeOptions: [],
				patientNameOptionsDisplay: 'none',
				noteDisable: false,
				firstTreatment: {id: null, price: null, duration: null},
				treatment:
					[
						{elementID: 'at' + 0, treatment: {id: null, price: null, duration: null}}
					],
				next: 0,
				t_duration: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				add: true,
				cancel: true,
				paymentOptions: [],
				payment: null,
				subtotal: 0,
				total: 0,
				tax: 0,
				newtax: 0,
				amount: 0,
				amount2: 0,
				partialpayment: null,
				productOptions: [],
				products:
					[
						{product: {id: null, price: null, tax: null}, disable: false}
					],
				payoff:false,
				treatmentTax:[],
				editPayment: 
					[
						{payment:{method: null, paid: null}}
					]
			},
			appointment: //created an appointment
			{
				searchOptions:[{text: 'Phone', value: 1}, {text: 'Name', value: 2}, {text: 'Email', value: 3}],
				doctors: [],
				doctor: null,
				doctorName: null,
				searchOption: 1,
				placeholder: 'Search by phone',
				type: 'text',
				pattern: null,
				maxlength: 12,
				phone: null,
				step2: false,
				step3: false,
				step4: false,
				step5: false,
				treatmentOptions2: [],
				treatments: [],
				duration: 0,
				total: 0,
				timeOptions: [],
				timeDisplay: null,
				message1: null,
				message2: null,
				date: null,
				time: null,
				patient: null,
				patientName: null,
				patientInput: null,
				patientNameInput: null,
				patientNameOptions: [],
				email: null,
				note: null,
				patientNameOptionsDisplay: 'none',
				availableDoctorOptions: [],
				availableTimeOptions: [],
				treatmentOptions: [],
				firstTreatment: {id: null, price: null, duration: null},
				treatment:
					[
						{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}
					],
				next: 0,
				dismissSecs: 1,
				dismissCountDown: 0,
				subtotal: 0,
				tax: 0,
				total: 0,
				t_duration: null
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'created',
							sortable:true
						},
						{
							key: 'statusDisplay',
							label: 'Status',
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
			pending:
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'created',
							sortable: true
						},
						{
							key: 'statusDisplay',
							label: 'Status',
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
			unpaid:
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'created',
							sortable: true
						},
						{
							key: 'statusDisplay',
							label: 'Status',
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
			partial:
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'created',
							sortable: true
						},
						{
							key: 'statusDisplay',
							label: 'Status',
							sortable: true,
							thStyle: {width:'120px'}
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
			paid:
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
							key: 'patientName',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'created',
							sortable: true
						},
						{
							key: 'statusDisplay',
							label: 'Status',
							sortable: true,
							thStyle: {width:'110px'}
						},
						{
							key: 'detail',
							sortable: false,
							thStyle: {width:'70px'}
						},
						{
							key: 'invoice',
							sortable: false,
							thStyle: {width:'70px'}
						}
					],
				alert: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				pageOptions: [10, 25, 50, 100]
			},
			financial:
			{
				loaded: false,
				doctorOptions: [],
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
							//thStyle: {width:'130px'}
						},
						{
							key: 'time',
							label: 'Time',
							sortable: true,
							//thStyle: {width:'120px'}
						},
						{
							key: 'doctor',
							label: 'Doctor',
							sortable: true
						},
						{
							key: 'patient',
							label: 'Patient',
							sortable: true
						},
						{
							key: 'payment',
							sortable: true,
							//thStyle: {width:'110px'}
						},

					],
				alert: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				from: null,
				to: null,
				total: 0,
				pageOptions: [10, 25, 50, 100]
			},
			search:
			{
				doctor: null,
				date: null,
				patient: null,
				name: null,
				display: 'none',
				nameOptions: [],
				doctorOptions: [],
				format: null,
				message: null,
				resultDisplay: false,
				items:[],
				data:[],
				table: [],
				currentPage: 1,
				perPage: 10,
				totalRows: null,
				filter: null,
				hover: true,
				sortBy: 'date',
				sortDesc: true,
				currentDoctor: null,
				fields:fieldDefine,
					// [
					// 	{
					// 		key: 'date',
					// 		sortable: true,
					// 		thStyle: {width:'130px'}
					// 	},
					// 	{
					// 		key: 'timeDisplay',
					// 		label: 'Time',
					// 		sortable: true,
					// 		thStyle: {width:'120px'}
					// 	},
					// 	{
					// 		key: 'doctorName',
					// 		label: 'Doctor',
					// 		sortable: true
					// 	},
					// 	{
					// 		key: 'patientName',
					// 		label: 'Patient',
					// 		sortable: true
					// 	},
					// 	{
					// 		key: 'status',
					// 		sortable: true,
					// 		thStyle: {width:'110px'}
					// 	},
					// 	{
					// 		key: 'edit',
					// 		sortable: false,
					// 		thStyle: {width:'70px'}
					// 	}
					// ],
				alert: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				pageOptions: [10, 25, 50, 100]
			}
		},
		watch:
		{
			doctor: function(val, oldVal)
			{
				this.searchAvailableTime(val, 2);
			},
			practitioner: function(val, oldVal)
			{
				if(oldVal != null)
				loadmodalAppointment(val, this.appointmentGraphicTable.monday);
			},
			modalDoctor: function(val, oldVal)
			{
				if(oldVal != null)
				this.searchAvailableTime(val, 1, this.modalAppointment.appointment);
			},
			financialDoctor: function(val, oldVal)
			{
				loadApptList(6);
			},
			searchOption: function(val, oldVal)
			{
				if(val == 1)
				{
					this.appointment.type = 'text';
					this.appointment.maxlength = 12;
				}
				if(val == 2)
				{
					this.appointment.type = 'text';
					this.appointment.maxlength = null;
				}
				if(val == 3)
				{
					this.appointment.type = 'email';
					this.appointment.maxlength = null;
				}
				if(val != oldVal)
					this.appointment.patientInput = null;
			},
			modalSearchOption: function(val, oldVal)
			{
				if(val == 1)
				{
					this.modalAppointment.type = 'text';
					this.modalAppointment.maxlength = 12;
				}
				if(val == 2)
				{
					this.modalAppointment.type = 'text';
					this.modalAppointment.maxlength = null;
				}
				if(val == 3)
				{
					this.modalAppointment.type = 'email';
					this.modalAppointment.maxlength = null;
				}
				if(val != oldVal)
					this.modalAppointment.patientInput = null;
			}
		},
		computed:
		{
			Total: function() //create an appointment subtotal
			{
				let total = 0;
				if(this.appointment.firstTreatment.price != null)
				{
					total += parseFloat(this.appointment.firstTreatment.price);
				}
				for (var i = 0; i !=this.appointment.treatment.length; i++)
				{
					if(this.appointment.treatment[i].treatment.price != null)
					total += parseFloat(this.appointment.treatment[i].treatment.price);
				}
				this.appointment.total = total;
				return total.toFixed(2);
			},
			Total2: function() //modal add appointment subtotal
			{
				let total = 0;
				if(this.modalAppointment.firstTreatment.price != null)
				{
					total += parseFloat(this.modalAppointment.firstTreatment.price);
				}
				for (var i = 0; i !=this.modalAppointment.treatment.length; i++)
				{
					if(this.modalAppointment.treatment[i].treatment.price != null)
					total += parseFloat(this.modalAppointment.treatment[i].treatment.price);
				}
				this.modalAppointment.subtotal = total;
				return total.toFixed(2);
			},
			Duration: function()
			{
				let total = 0;
				if(this.appointment.firstTreatment.duration != null)
				{
					total += parseFloat(this.appointment.firstTreatment.duration);
				}
				for (var i = 0; i !=this.appointment.treatment.length; i++)
				{
					if(this.appointment.treatment[i].treatment.duration != null)
					total += parseFloat(this.appointment.treatment[i].treatment.duration);
				}
				this.appointment.t_duration = total;
				return total;
			},
			Duration2: function()
			{
				let total = 0;
				if(this.modalAppointment.firstTreatment.duration != null)
				{
					total += parseFloat(this.modalAppointment.firstTreatment.duration);
				}
				for (var i = 0; i !=this.modalAppointment.treatment.length; i++)
				{
					if(this.modalAppointment.treatment[i].treatment.duration != null)
					total += parseFloat(this.modalAppointment.treatment[i].treatment.duration);
				}
				this.modalAppointment.t_duration = total;
				return total;
			},
			MaxDuration: function()
			{
				let max = 0;
				let time = this.appointment.time;
				let index = null;
				if(time != null)
				{
					for (var i = 0; i != this.appointment.availableTimeOptions.length; i++)
					{
						if(time == this.appointment.availableTimeOptions[i].value)
						{
							if(i == this.appointment.availableTimeOptions.length - 1)
								max = 15;
							else
							{
								index = i;
								break;
							}
						}
					}
					if(index != null)
					{
						for (var i = (index + 1); i != this.appointment.availableTimeOptions.length; i++)
						{
							if((time + 1) == this.appointment.availableTimeOptions[i].value)
							{
								max += 15;
							}
							time++;
						}
						max += 15;
					}
				}
				return max;
			},
			Practitioner: function()
			{
				let result = '';
				for(var i = 0; i != this.appointmentGraphicTable.doctorOptions.length; i++)
				{
					if(this.appointmentGraphicTable.doctorOptions[i].value == this.practitioner)
					{
						result = this.appointmentGraphicTable.doctorOptions[i].text;
						break;
					}
				}
				return result;
			},
			Remaining: function()
			{
				let total = 0;
				if(this.modalAppointment.partialpayment != null)
				{
					for(var i = 0; i != this.modalAppointment.partialpayment.length; i++)
					{
						total += parseFloat(this.modalAppointment.partialpayment[i].paid);
					}
				}
				this.modalAppointment.amount = (this.modalAppointment.total - total).toFixed(2);
				return (this.modalAppointment.total - total).toFixed(2);
			},
			TotalRemaining: function() // full edit modal
			{
				let total = 0;
				if(this.modalAppointment.editPayment.length >= 1)
				{
					for(var i = 0; i != this.modalAppointment.editPayment.length; i++)
					{
						total += parseFloat(this.modalAppointment.editPayment[i].payment.paid);
					}
				}
				this.modalAppointment.amount2 = (this.modalAppointment.total - total).toFixed(2);
				return (this.modalAppointment.total - total).toFixed(2);
			},
			Subtotal: function()
			{
				let total = 0;
				if(this.modalAppointment.firstTreatment.price != null)
				{
					total += parseFloat(this.modalAppointment.firstTreatment.price);
				}
				for (var i = 0; i !=this.modalAppointment.treatment.length; i++)
				{
					if(this.modalAppointment.treatment[i].treatment.price != null)
						total += parseFloat(this.modalAppointment.treatment[i].treatment.price);
				}
				for(var i = 0; i!= this.modalAppointment.products.length; i++)
				{
					if(this.modalAppointment.products[i].product.price != null)
						total += parseFloat(this.modalAppointment.products[i].product.price);
				}
				this.modalAppointment.subtotal = total;
				return total.toFixed(2);
			},
			Tax: function()
			{
				let total = parseFloat(this.modalAppointment.tax);
				for(var i = 0; i!= this.modalAppointment.products.length; i++)
				{
					if(this.modalAppointment.products[i].product.tax != null)
						total += parseFloat(this.modalAppointment.products[i].product.tax);
				}
				this.modalAppointment.newtax = total;
				return total.toFixed(2);
			},
			Tax2: function()
			{
				let total = 0;
				if(this.modalAppointment.firstTreatment.price != null)
				{
					for (var i = 0; i != this.modalAppointment.treatmentTax.length; i++)
					{
						if(this.modalAppointment.treatmentTax[i].id == this.modalAppointment.firstTreatment.id)
							total += parseFloat(this.modalAppointment.treatmentTax[i].tax);
					}
				}
				for (var i = 0; i != this.modalAppointment.treatment.length; i++)
				{
					for (var k = 0; k != this.modalAppointment.treatmentTax.length; k++)
					{
						if(this.modalAppointment.treatment[i].treatment.id == this.modalAppointment.treatmentTax[k].id )
							total += parseFloat(this.modalAppointment.treatmentTax[k].tax);
					}
				}
				for(var i = 0; i != this.modalAppointment.products.length; i++)
				{
					if(this.modalAppointment.products[i].product.tax != null)
						total += parseFloat(this.modalAppointment.products[i].product.tax);
				}
				this.modalAppointment.newtax = total;
				return total.toFixed(2);
			},
			Total3: function() //edit payment total
			{
				let total = parseFloat(this.modalAppointment.subtotal) + parseFloat(this.modalAppointment.newtax)
				this.modalAppointment.total = total;
				return total.toFixed(2);
			}
		},
		methods:
		{
			showAlert(section)
			{
				if(section == 1)
				{
					this.modalAppointment.dismissCountDown = this.modalAppointment.dismissSecs;
				}
				if(section == 2)
				{
					this.appointment.dismissCountDown = this.appointment.dismissSecs;
				}				
			},
			reset()
			{
				this.appointment.step2 = this.appointment.step3 = this.appointment.step4 = this.appointment.step5 =false;
				this.appointment.patientName = this.appointment.patient = this.appointment.patientInput = this.appointment.phone = this.appointment.email = null;
				this.appointment.treatments = [];
				this.appointment.duration = this.appointment.total = 0;
				for (var i = 0; i <  this.appointment.treatmentOptions2.length; i++)
				{
					this.appointment.treatmentOptions2[i].selected = false;
				}
				for (var i = 0; i <  this.appointment.doctors.length; i++)
				{
					this.appointment.doctors[i].selected = false;
				}
			},
			searchPatient(section)
			{
				valid = false;
				name = null;
				type = null;
				switch(section)
				{
					case 1:
						if(this.modalAppointment.patientInput == '' || this.modalAppointment.patientInput == null)
						{
							this.modalAppointment.patientNameOptionsDisplay = 'none';
							this.modalAppointment.patient = null;
							this.modalAppointment.email = null;
							this.modalAppointment.phone = null;
							this.modalAppointment.patientName = null;
							this.modalAppointment.email = null;
						}
						else
						{
							value = this.modalAppointment.patientInput;
							if(this.modalSearchOption == 1)
							{
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
								else
									valid = true;
								type = 'phone';
							}
							if(this.modalSearchOption == 2)
								valid = true;
							if(this.modalSearchOption == 3)
							{
								type = 'email';
								valid = true;
							}
							name = this.modalAppointment.patientInput = value;
						}
						break;
					case 2:
						if(this.appointment.patientInput == '' || this.appointment.patientInput == null)
						{
							this.appointment.patientNameOptionsDisplay = 'none';
							this.appointment.patient = null;
							this.appointment.patientName = null;
							this.appointment.phone = null;
							this.appointment.email = null;
						}
						else
						{
							value = this.appointment.patientInput;
							if(this.searchOption == 1)
							{
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
								else
									valid = true;
								type = 'phone';
							}
							if(this.searchOption == 2)
								valid = true;
							if(this.searchOption == 3)
							{
								type = 'email';
								valid = true;
							}
							name = this.appointment.patientInput = value;
						}
						break;
				}
				if(valid)
				{
					axios.post('forms/form-search-patient.php?' + type,
					{
						value: name
					},
					config)
					.then(function (response)
					{
						if(response.data.success)
						{
							switch(section)
							{
								case 1:
									appointments.modalAppointment.patientNameOptions = response.data.patients;
									appointments.modalAppointment.patientNameOptionsDisplay = 'block';
									break;
								case 2:
									appointments.appointment.patientNameOptions = response.data.patients;
									appointments.appointment.patientNameOptionsDisplay = 'block';
									break;
							}
						}
						else
						{
							appointments.modalAppointment.patientNameOptionsDisplay = 'none';
							appointments.appointment.patientNameOptionsDisplay = 'none';
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			onFocus(section)
			{
				if(section == 1)
				{
					if(this.modalAppointment.patientInput == '' || this.modalAppointment.patientInput == null)
					{
						this.modalAppointment.patientNameOptionsDisplay = 'none';
					}
					else
					{
						this.modalAppointment.patientNameOptionsDisplay = 'block';
					}
				}
				if(section == 2)
				{
					if(this.appointment.patientInput == '' || this.appointment.patientInput == null)
					{
						this.appointment.patientNameOptionsDisplay = 'none';
					}
					else
					{
						this.appointment.patientNameOptionsDisplay = 'block';
					}
				}

				// if(section == 3)
				// {
				// 	if(this.search.name == '' || this.search.name == null)
				// 	{
				// 		this.search.display = 'none';
				// 	}
				// 	else
				// 	{
				// 		this.search.display = 'block';
				// 	}
				// }
			},
			selectPatient(item, section)
			{
				switch(section)
				{
					case 1:
						this.modalAppointment.patient = item.id;
						this.modalAppointment.patientName =  item.firstname + ' ' + item.lastname;
						this.modalAppointment.email = item.email;
						this.modalAppointment.phone = item.phone;
						this.modalAppointment.patientNameOptionsDisplay = 'none';
						break;
					case 2:
						this.appointment.patient = item.id;
						this.appointment.patientName =  item.firstname + ' ' + item.lastname;
						this.appointment.email = item.email;
						this.appointment.phone = item.phone;
						this.appointment.patientNameOptionsDisplay = 'none';
						break;
				}
			},
			searchByEmail(section)
			{
				valid = false;
				email = null;
				switch(section)
				{
					case 1:
						this.modalAppointment.patientNameInput = null;
						if(this.modalAppointment.email != null)
						{
							valid = true;
							email = this.modalAppointment.email;
						}
						break;
					case 2:
						this.appointment.patientNameInput = null;
						if(this.appointment.email != null)
						{
							valid = true;
							email = this.appointment.email;
						}
						break;
				}
				if(valid)
				{
					axios.post('forms/form-search-patient.php',
					{
						email: email
					},
					config)
					.then(function (response)
					{
						if(response.data.success)
						{
							switch(section)
							{
								case 1:
									appointments.modalAppointment.patient = response.data.patient;
									appointments.modalAppointment.patientNameInput = response.data.name;
									break;
								case 2:
									appointments.appointment.patient = response.data.patient;
									appointments.appointment.patientNameInput = response.data.name;
									break;
							}
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			loadModal(item, day, button)
			{
				this.$root.$emit('bv::hide::popover', item.elementID);
				this.modalAppointment.date = item.date;
				this.modalAppointment.doctor = item.doctor;
				this.modalAppointment.doctorName = item.doctorName;
				this.modalAppointment.patientName = item.patientName;
				this.modalAppointment.note = item.note;
				this.modalAppointment.appointment = item.appointment;
				this.modalAppointment.tax = item.tax;
				this.modalAppointment.total = item.total;
				if(item.treatment)
				{
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
				}
				if(item.product != null)
				{
					if(item.product.length >= 1)
					{
						for(var i = 0; i != item.product.length; i++)
						{
							Vue.set(this.modalAppointment.products, i, {product: item.product[i], disable:true});
						}
						Vue.set(this.modalAppointment.products, item.product.length, {product:{id: null, price: null, tax: null}, disable: false});
					}
				}
				if(item.payment)
				{

				}
				switch(item.statusDisplay)
				{
					case 'Available':
						this.modalAppointment.timeDisplay = day.time;
						this.modalAppointment.time = day.timeid;
						this.$root.$emit('bv::show::modal', 'modalAdd', button);
						break;
					case 'Booked':
						this.modalDoctor = item.doctor;
						this.searchAvailableDoctor(1);
						this.modalAppointment.time = item.time;
						this.searchAvailableTime(this.modalDoctor, 1, item.appointment);
						this.$root.$emit('bv::show::modal', 'modalEdit', button);
						break;
					case 'Unpaid':
						this.modalAppointment.timeDisplay = item.timeDisplay;
						this.$root.$emit('bv::show::modal', 'modalPayment', button);
						break;
					case 'Partially paid':
						this.modalAppointment.partialpayment = item.payment;
						this.modalAppointment.timeDisplay = item.timeDisplay;
						this.modalAppointment.cancel = false;
						this.$root.$emit('bv::show::modal', 'modalPayment', button);
						break;
					case 'Paid':
						this.modalAppointment.timeDisplay = item.timeDisplay;
						index = (item.product != null) ? item.product.length : 0;
						this.modalAppointment.products.splice(index, 1);
						this.$root.$emit('bv::show::modal', 'modalComplete', button);
						break;
				}
			},
			resetModal()
			{
				this.modalAppointment.appointment = this.modalAppointment.date = this.modalAppointment.time = this.modalAppointment.timeDisplay = null;
				this.modalAppointment.doctor = this.modalAppointment.doctorName = this.modalAppointment.patient = this.modalAppointment.email = null;
				this.modalAppointment.note = this.modalAppointment.patientNameInput = this.modalAppointment.patientName = this.modalAppointment.patientInput = null;
				this.modalAppointment.editPayment = [{payment: {method: null, paid: null}}];
				this.modalAppointment.availableDoctorOptions = this.modalAppointment.patientNameOptions = this.modalAppointment.availableTimeOptions = [];
				this.modalAppointment.patientNameOptionsDisplay = 'none';
				this.modalAppointment.noteDisable = false;
				this.modalAppointment.firstTreatment = {id: null, price: null, duration: null};
				this.modalAppointment.treatment = [{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}];
				this.modalAppointment.next = this.modalAppointment.subtotal = this.modalAppointment.tax = this.modalAppointment.total = 0;
				this.modalAppointment.t_duration = null;
				this.modalAppointment.add = this.modalAppointment.cancel = true;
				this.modalAppointment.amount = this.modalAppointment.amount2 = this.modalAppointment.newtax = 0;
				this.modalAppointment.payment = this.modalAppointment.partialpayment = null;
				this.modalAppointment.payoff = false;
				this.modalAppointment.products = [{product:{id: null, price: null, tax: null}, disable: false}];
				this.modalAppointment.editPayment = [{payment:{method: null, paid: null}}];
			},
			addAppointment(evt)
			{
				evt.preventDefault();
				axios.post('forms/form-update-appointment.php?create',
					{
						doctor: this.appointment.doctor,
						patient: this.appointment.patient,
						date: this.appointment.date,
						time: this.appointment.time,
						note: this.appointment.note,
						treatments: this.appointment.treatments,
						duration: this.appointment.duration
					},
					config)
				.then(function (response)
				{
					appointments.appointment.message2 = response.data.message;
					appointments.updateStatus();
					setTimeout(function(){
						appointments.appointment.message2 = null;
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
			addAppt(evt) //graphic table modal
			{
				evt.preventDefault();
				ignore = (this.tabIndex == 8) ? true : false;
				axios.post('forms/form-update-appointment.php?add',
					{
						doctor: this.modalAppointment.doctor,
						patient: this.modalAppointment.patient,
						date: this.modalAppointment.date,
						note: this.modalAppointment.note,
						time: this.modalAppointment.time,
						firsttreatment: this.modalAppointment.firstTreatment,
						treatments: this.modalAppointment.treatment,
						duration: this.modalAppointment.t_duration,
						ignore: ignore
					},
					config)
				.then(function (response)
				{
					appointments.message = response.data.message;
					if(response.data.success)
					{
						appointments.modalAppointment.alert = 'success';
						appointments.modalAppointment.add = false;
						appointments.updateStatus();
					}
					else
						appointments.modalAppointment.alert = 'danger';
					appointments.showAlert(1);
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			searchAvailableDoctor(section)
			{
				if(section == 1)
					date = this.modalAppointment.date;
				if(section == 2)
					date = this.appointment.date;
				axios.post('ajax/ajax-available-appointment.php?date',
				{
					date: date
				},config)
				.then(function (response)
				{
					if(response.data.success)
					{
						switch(section)
						{
							case 1:
								appointments.modalAppointment.availableDoctorOptions = response.data.doctors;
								if(appointments.modalDoctor != null)
									appointments.modalAppointment.availableDoctorOptions.splice(0, 1);
								break;
							case 2:
								appointments.appointment.availableDoctorOptions = response.data.doctors;
								break;
						}
					}
					else
					{
						appointments.modalAppointment.availableDoctorOptions = [];
						appointments.appointment.availableDoctorOptions = [];
						appointments.message = response.data.message;
						appointments.alert = 'danger';
						appointments.showAlert(value);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
				
				// if(section == 2)
				// {
				// 	this.doctor = null;
				// 	this.appointment.time = null;
				// 	if(this.appointment.date != null)
				// 	{
				// 		axios.post('ajax/ajax-available-appointment.php?date',
				// 		{
				// 			date: this.appointment.date
				// 		},config)
				// 		.then(function (response)
				// 		{
				// 			if(response.data.success)
				// 			{
				// 				appointments.appointment.doctorOptions = response.data.doctors;
				// 			}
				// 			else
				// 			{
				// 				appointments.appointment.doctorOptions = [];
				// 				appointments.appointment.message = response.data.message;
				// 				appointments.appointment.alert = 'danger';
				// 				appointments.showAlert(1);
				// 			}
				// 		})
				// 		.catch(function (error) {
				// 		console.log(error);
				// 		});
				// 	}	
				// }
			},
			searchAvailableTime(value, section, appt)
			{
				if(value != null && section == 1)
				{
					axios.post('ajax/ajax-available-appointment.php?time',
					{
						doctor: value,
						date: this.modalAppointment.date,
						appointment: appt
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.modalAppointment.availableTimeOptions = response.data.time;
						}
						else
						{
							appointments.modalAppointment.availableTimeOptions = [];
							appointments.modalAppointment.message = response.data.message;
							appointments.modalAppointment.alert = 'danger';
							appointments.showAlert(2);
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
				if(value != null && section == 2)
				{
					axios.post('ajax/ajax-available-appointment.php?time',
					{
						doctor: value,
						date: this.appointment.date
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.appointment.availableTimeOptions = response.data.time;
						}
						else
						{
							appointments.appointment.availableTimeOptions = [];
							appointments.appointment.message = response.data.message;
							appointments.appointment.alert = 'danger';
							appointments.showAlert(1);
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			moreTreatment(section)
			{
				if(section == 1)
				{
					if(this.appointment.next == 0)
					this.appointment.next++;
					else
					{
						this.appointment.treatment.push({elementID: 't' + this.appointment.next++, treatment: {id: null, price: null, duration: null}});
					}
				}
				if(section == 2)
				{
					if(this.modalAppointment.next == 0)
					this.modalAppointment.next++;
					else
					{
						this.modalAppointment.treatment.push({elementID: 'at' + this.modalAppointment.next++, treatment: {id: null, price: null, duration: null}});
					}
				}
				if(section == 3)
				{
					if(this.modalAppointment.next == 0)
					this.modalAppointment.next++;
					else
					{
						this.modalAppointment.treatment.push({elementID: 'at' + this.modalAppointment.next++, treatment: {id: null, price: null, duration: null}});
					}
				}
			},
			moreProduct()
			{
				this.modalAppointment.products.push({product: {id: null, price: null, tax: null}, disable:false});
			},
			morePayment()
			{
				this.modalAppointment.editPayment.push({payment:{method: null, paid: this.modalAppointment.amount2}});
			},
			
			updateTable(date)
			{
				loadmodalAppointment(this.practitioner, date);
			},
			searchInfo(item, day, button) //graphic table
			{
				if(item.status == 'Available')
				{
					this.modalAppointment.date = item.date;
					this.modalAppointment.displaytime = day.time;
					this.modalAppointment.time = day.timeid;
					this.modalAppointment.doctorName = item.doctor;
					this.modalAppointment.doctor = item.doctorID;
					this.$root.$emit('bv::show::modal', 'modalAdd', button)
				}
				if(item.status == 'Busy')
				{
					this.modalAppointment.appointment = item.appointment;
					this.modalAppointment.patientname = item.patient;
					this.modalAppointment.date = item.date;
					this.modalAppointment.time = item.appointmentTime;
					this.modalAppointment.note = item.note;
					this.modalAppointment.doctor = item.doctorID;
					this.modalAppointment.doctorName = item.doctor;
					this.modalAppointment.displaytime = item.appointmentTimeDisplay;
					this.modalDoctor = item.doctorID;
					this.searchAvailableDoctor(2);
					this.searchAvailableTime(this.modalDoctor, 2, item.appointment);
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					this.$root.$emit('bv::show::modal', 'modalEdit', button)
				}
				if(item.status == 'Unpaid' || item.status == 'Partially paid')
				{
					this.modalAppointment.appointment = item.appointment;
					this.modalAppointment.patientname = item.patient;
					this.modalAppointment.date = item.date;
					this.modalAppointment.time = item.appointmentTime;
					this.modalAppointment.note = item.note;
					this.modalAppointment.doctor = item.doctorID;
					this.modalAppointment.doctorName = item.doctor;
					this.modalAppointment.displaytime = item.appointmentTimeDisplay;
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.modalAppointment.tax = item.tax;
					this.modalAppointment.total = item.total;
					this.modalAppointment.partialpayment = item.payment;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					if(item.product != null)
					{
						if(item.product.length >= 1)
						{
							for(var i = 0; i != item.product.length; i++)
							{
								Vue.set(this.modalAppointment.products, i, {product: item.product[i], disable:true});
							}
							Vue.set(this.modalAppointment.products, item.product.length, {product:{id: null, price: null, tax: null}, disable: false});
						}
					}
					if(item.status == 'Partially paid')
					{
						this.modalAppointment.cancel = false;
					}
					this.$root.$emit('bv::show::modal', 'modalPayment', button)
				}
				if(item.status == 'Paid')
				{
					this.modalAppointment.appointment = item.appointment;
					this.modalAppointment.patientname = item.patient;
					this.modalAppointment.date = item.date;
					this.modalAppointment.time = item.appointmentTime;
					this.modalAppointment.note = item.note;
					this.modalAppointment.doctor = item.doctorID;
					this.modalAppointment.doctorName = item.doctor;
					this.modalAppointment.displaytime = item.appointmentTimeDisplay;
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.modalAppointment.tax = item.tax;
					this.modalAppointment.total = item.total;
					this.modalAppointment.payment = item.payment;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					if(item.product != null)
					{
						if(item.product.length >= 1)
						{
							for(var i = 0; i != item.product.length; i++)
							{
								Vue.set(this.modalAppointment.products, i, {product: item.product[i]});
							}
						}
					}
					this.$root.$emit('bv::show::modal', 'modalComplete', button)
				}
			},
			searchApptInfo(item, index, button) //datatable
			{
				console.log(item);
				if(item.status == 4) //paid
				{
					this.modalAppointment.appointment = item.id;
					this.modalAppointment.name = item.patientName;
					this.modalAppointment.patient = item.patient;
					this.modalAppointment.date = item.date;
					this.modalAppointment.time = item.time;
					this.modalAppointment.doctor = item.doctor;
					this.modalDoctor = item.doctor;
					this.modalAppointment.doctorName = item.doctorName;
					this.modalAppointment.displaytime = item.timeDisplay;
					this.searchAvailableDoctor(2);
					this.searchAvailableTime(this.modalDoctor, 2, item.id);
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.modalAppointment.tax = item.tax;
					this.modalAppointment.total = item.total;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					if(item.product != null)
					{
						if(item.product.length >= 1)
						{
							for(var i = 0; i != item.product.length; i++)
							{
								Vue.set(this.modalAppointment.products, i, {product: item.product[i], disable:false});
							}
							// Vue.set(this.modalAppointment.products, item.product.length, {product:{id: null, price: null, tax: null}, disable: false});
						}
					}
					if(item.payment.length != null)
					{
						for(var i = 0; i != item.payment.length; i++)
							{
								Vue.set(this.modalAppointment.editPayment, i, {payment:{method: item.payment[i].method, paid: item.payment[i].paid}});
							}
					}
					this.$root.$emit('bv::show::modal', 'modalFullEdit', button)
				}
				if(item.status == 1) //pending
				{
					
						this.modalAppointment.appointment = item.id;
						this.modalAppointment.patientname = item.patientName;
						this.modalAppointment.date = item.date;
						this.modalAppointment.time = item.time;
						this.modalAppointment.displaytime = item.timeDisplay;
						this.modalDoctor = item.doctor;
						this.searchAvailableDoctor(2);
						this.searchAvailableTime(this.modalDoctor, 2, item.id);
						this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
						if(item.treatment.length > 1)
						{
							for(var i = 1; i != item.treatment.length; i++)
							{
								Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
							}
						}
						this.$root.$emit('bv::show::modal', 'modalEdit', button)
				}	
				if(item.status == 2 || item.status == 3) //unpaid or partial paid
				{
					this.modalAppointment.appointment = item.id;
					this.modalAppointment.name = item.patientName;
					this.modalAppointment.patient = item.patient;
					this.modalAppointment.date = item.date;
					this.modalAppointment.time = item.time;
					this.modalAppointment.doctorName = item.doctorName;
					this.modalAppointment.displaytime = item.timeDisplay;
					this.modalAppointment.firstTreatment = {id: item.treatment[0].id, price: item.treatment[0].price, duration: item.treatment[0].duration};
					this.modalAppointment.tax = item.tax;
					this.modalAppointment.total = item.total;
					this.modalAppointment.payment = item.payment;
					if(item.treatment.length > 1)
					{
						for(var i = 1; i != item.treatment.length; i++)
						{
							Vue.set(this.modalAppointment.treatment, i - 1 , {elementID: 'at' + this.modalAppointment.next++, treatment: {id: item.treatment[i].id, price: item.treatment[i].price, duration: item.treatment[i].duration}});
						}
					}
					this.$root.$emit('bv::show::modal', 'modalPayment', button)
				}
			},
			cancelAppt()
			{
				axios.post('forms/form-update-appointment.php?cancel',
					{
						appointment: this.modalAppointment.appointment
					},
					config)
				.then(function (response)
				{
					if(response.data.success)
					{
						appointments.modalAppointment.message = response.data.message;
						appointments.modalAppointment.alert = 'success';
						appointments.modalAppointment.add = false;
						appointments.modalAppointment.cancel = false;
						appointments.modalAppointment.payoff = true;
						appointments.showAlert(2);
						appointments.updateStatus();
					}
					else
					{
						appointments.modalAppointment.message = response.data.message;
						appointments.modalAppointment.alert = 'danger';
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
						appointment: this.modalAppointment.appointment,
						date: this.modalAppointment.date,
						time: this.modalAppointment.time,
						note: this.modalAppointment.note,
						doctor: this.modalDoctor,
						firsttreatment: this.modalAppointment.firstTreatment,
						treatments: this.modalAppointment.treatment,
						duration: this.modalAppointment.t_duration
					},
					config)
				.then(function (response)
				{
					console.log(response.data);
					appointments.message = response.data.message;
					if(response.data.success)
					{
						appointments.alert = 'success';
						appointments.updateStatus();
						appointments.showAlert(2);
					}
					else
					{
						appointments.alert = 'danger';
					}
					appointments.showAlert(1);
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			addPayment(evt)
			{
				evt.preventDefault();
				if(this.modalAppointment.payment == null)
				{
					this.modalAppointment.message = 'Payment method incorrect';
					this.modalAppointment.alert = 'danger';
					this.showAlert(1);
				}
				else
				{
					axios.post('forms/form-update-payment.php?add',
						{
							appointment: this.modalAppointment.appointment,
							price: this.modalAppointment.subtotal,
							tax: this.modalAppointment.newtax,
							total: this.modalAppointment.total,
							method: this.modalAppointment.payment,
							amount: this.modalAppointment.amount,
							products: this.modalAppointment.products,
							note: this.modalAppointment.note
						},
						config)
					.then(function (response)
					{
						appointments.message = response.data.message;
						if(response.data.success)
						{
							appointments.modalAppointment.payoff = response.data.payoff;
							appointments.modalAppointment.cancel = false;
							for(var i = 0; i < appointments.modalAppointment.products.length ; i++)
							{
								if(response.data.payoff)
								{
									appointments.modalAppointment.products[i].disable = true;
								}
								else
								{
									if(appointments.modalAppointment.products[i].product.id != null)
										appointments.modalAppointment.products[i].disable = true;
								}		
							}
							appointments.modalAppointment.partialpayment = response.data.payment;
							
							appointments.alert = 'success';
							// appointments.modalAppointment.dismissCountDown = 3;
							appointments.updateStatus()
						}
						else
						{
							appointments.alert = 'danger';
						}
						appointments.showAlert(1);
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			onFiltered5 (filteredItems) //all
			{
				this.all.totalRows = filteredItems.length
				this.all.currentPage = 1
			},
			onFiltered1 (filteredItems) //pending
			{
				this.pending.totalRows = filteredItems.length
				this.pending.currentPage = 1
			},
			onFiltered2 (filteredItems) //unpaid
			{
				this.unpaid.totalRows = filteredItems.length
				this.unpaid.currentPage = 1
			},
			onFiltered3 (filteredItems) //partial paid
			{
				this.partial.totalRows = filteredItems.length
				this.partial.currentPage = 1
			},
			onFiltered4 (filteredItems) //paid
			{
				this.paid.totalRows = filteredItems.length
				this.paid.currentPage = 1
			},
			 onFiltered6 (filteredItems) //financial activity
			{
				this.financial.totalRows = filteredItems.length
				this.financial.currentPage = 1
			},
			onFiltered7 (filteredItems) //search
			{
				this.search.totalRows = filteredItems.length
				this.search.currentPage = 1
			},
			paymentFormatter(payment)
			{
				return payment.method + ' - $' + payment.paid;
			},
			buttonTextFormatter(status)
			{
				result = null;
				switch(status)
				{
					case 'Booked':
						result = 'Edit';
						break;
					case 'Unpaid':
						result = 'Pay';
						break;
					case 'Partially paid':
						result = 'Pay';
						break;
					case 'Paid':
						result = 'View';
						break;
					case 'Canceled':
						result = 'View';
						break;
				}
				return result;
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
						result = 'Unpaid';
						break;
					case 3:
						result = 'Partial paid';
						break;
					case 4:
						result = 'Paid';
						break;
				}
				return result;
			},
			loadAppointmentData(status)
			{
				if(status == 5) //all
				{
					if(this.all.loaded == false)
					{
						loadApptList(status);
						this.all.loaded = true;
					}
					else if(this.all.update == true)
					{
						loadApptList(status);
						this.all.update = false;
					}
				}
				if(status == 1) //pending
				{
					if(this.pending.loaded == false)
					{
						loadApptList(status);
						this.pending.loaded = true;
					}
					else if(this.pending.update == true)
					{
						loadApptList(status);
						this.pending.update = false;
					}
				}
				if(status == 2) //unpaid
				{
					if(this.unpaid.loaded == false)
					{
						loadApptList(status);
						this.unpaid.loaded = true;
					}
					else if(this.unpaid.update == true)
					{
						loadApptList(status);
						this.unpaid.update = false;
					}
				}
				if(status == 3) //partial
				{
					if(this.partial.loaded == false)
					{
						loadApptList(status);
						this.partial.loaded = true;
					}
					else if(this.partial.update == true)
					{
						loadApptList(status);
						this.partial.update = false;
					}
				}
				if(status == 4) //paid
				{
					if(this.paid.loaded == false)
					{
						loadApptList(status);
						this.paid.loaded = true;
					}
					else if(this.paid.update == true)
					{
						loadApptList(status);
						this.paid.update = false;
					}
				}
				if(status == 6) //financial status
				{
					if(this.financial.loaded == false)
					{
						loadApptList(status);
						this.financial.loaded = true;
					}
					else if(this.financial.update == true)
					{
						loadApptList(status);
						this.financial.update = false;
					}
				}
			},
			updateStatus()
			{
				loadmodalAppointment(this.practitioner, this.appointmentGraphicTable.monday);
				if(this.all.loaded == true)
				{
					this.all.update = true;
					if(this.tabIndex == 2)
						this.loadAppointmentData(5);
				}
				if(this.pending.loaded == true)
				{
					this.pending.update = true;
					if(this.tabIndex == 3)
						this.loadAppointmentData(1);
				}
				if(this.unpaid.loaded == true)
				{
					this.unpaid.update = true;
					if(this.tabIndex == 4)
						this.loadAppointmentData(2);
				}
				if(this.partial.loaded == true)
				{
					this.partial.update = true;
					if(this.tabIndex == 5)
						this.loadAppointmentData(3);
				}
				if(this.paid.loaded == true)
				{
					this.paid.update = true;
					if(this.tabIndex == 6)
						this.loadAppointmentData(4);
				}
				if(this.financial.loaded == true)
				{
					this.financial.update = true;
					if(this.tabIndex == 7)
						this.loadAppointmentData(6);
				}
				if(this.tabIndex == 8)
					this.searchAppointment();
			},
			UpdateFinancial()
			{
				if(this.financial.from != null && this.financial.to != null)
					loadApptList(6);
			},
			searchAppointment(evt)
			{
				if(evt)
					evt.preventDefault();
				axios.post('ajax/ajax-search-appointment.php',
					{
						doctor: this.search.doctor,
						patient: this.search.patient,
						date: this.search.date
					},
					config)
				.then(function (response)
				{
					console.log(response.data);
					appointments.search.message = response.data.message;
					appointments.search.resultDisplay = response.data.success;
					if(response.data.success)
					{
						appointments.search.format = response.data.format;
						appointments.search.items = response.data.appointments;
						appointments.search.table = response.data.appointmentable;
						appointments.search.currentDoctor = response.data.currentDoctor;
						if(response.data.displayDateCol)
						{
							appointments.search.fields = fieldDefine;
							appointments.search.sortBy = 'date';
							appointments.search.sortDesc = true;
						}
						else
						{
							appointments.search.fields = fieldDefine2;
							appointments.search.sortBy = 'timeDisplay';
							appointments.search.sortDesc = false;
						}
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			fullUpdateAppt(evt)
			{
				evt.preventDefault();
			},
			gotoStep2()
			{
				this.$root.$emit('bv::toggle::collapse', 'accordion2');
			},
			gotoStep3()
			{
				this.$root.$emit('bv::toggle::collapse', 'accordion3');
			},
			gotoStep4()
			{
				this.$root.$emit('bv::toggle::collapse', 'accordion4');
			},
			selectDoctor(item)
			{
				if(this.appointment.doctor != item.id)
				{
					this.appointment.doctor = item.id;
					this.appointment.doctorName = item.name;
					this.searchAvailableTimeSlot();
					this.appointment.time = this.appointment.timeDisplay = null;
					for (var i = 0; i <  this.appointment.doctors.length; i++)
					{
						this.appointment.doctors[i].selected = false;
					}
					item.selected = true;
					this.appointment.step3 = true;
					this.appointment.step5 = false;
				}
				this.$root.$emit('bv::toggle::collapse', 'accordion3');
			},
			selectTreatment(item)
			{
				item.selected = !item.selected;
				if(item.selected)
				{
					this.appointment.treatments.push({id: item.id, name: item.name, price: item.price, duration: item.duration, tax: item.tax});
					this.appointment.duration += item.duration;
					this.appointment.total += parseFloat(item.price);
				}
				else
				{
					index = this.appointment.treatments.findIndex(x => x.id === item.id);
					this.appointment.treatments.splice(index, 1);
					this.appointment.duration -= item.duration;
					this.appointment.total -= parseFloat(item.price);
				}
				this.appointment.time = null;
				if(this.appointment.treatments.length > 1)
					this.searchAvailableTimeSlot();
				this.appointment.step4 = this.appointment.treatments.length > 0 ? true : false;
				this.appointment.step5 = false;
			},
			searchAvailableTimeSlot()
			{
				if(this.appointment.date != null)
				{
					axios.post('ajax/ajax-available-time.php',
					{
						doctor: this.appointment.doctor,
						date: this.appointment.date,
						duration: this.appointment.duration
					},config)
					.then(function (response)
					{
						if(response.data.success)
						{
							appointments.appointment.timeOptions = response.data.timeOptions;
							appointments.appointment.message1 = null;
						}
						else
						{
							appointments.appointment.timeOptions = null;
							appointments.appointment.message1 = response.data.message;
						}
					})
					.catch(function (error) {
					console.log(error);
					});
				}
			},
			selectTime(item)
			{
				this.appointment.time = item.id;
				this.appointment.timeDisplay = item.time;
				for (var i = 0; i <  this.appointment.timeOptions.length; i++)
				{
					this.appointment.timeOptions[i].selected = false;
				}
				item.selected = true;
				this.appointment.step5 = true;
				this.$root.$emit('bv::toggle::collapse', 'accordion5');
			},
			showCalendar()
			{
				this.showCalendarText = (this.showCalendarText == 'Show calendar') ? 'Hide calendar' : 'Show calendar';
				this.calendarClass = (this.calendarClass == 'd-none') ? 'd-block' : 'd-none';
			}
		}
	});

}