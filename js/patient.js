function load()
{
	Vue.component('add-patient', {
	  props: ['item'],
	  template: '#add-patient-temp'
	});

	Vue.component('modal-edit-form', {
	  props: ['item'],
	  template: '#modal-editform-temp'
	});

	Vue.component('treatment', {
	  props: ['item', 'options'],
	  template: '#treatment-temp'
	});

	axios.post('ajax/ajax-patient-init.php',config)
	            .then(function (response)
	            {
	                if(response.data.success)
	                {
	                	patients.addpatient.countryOptions = response.data.countries;
	                	patients.addpatient.provinceOptions = response.data.provinces;
	                	patients.patient.countryOptions = response.data.countries;
	                	patients.patient.provinceOptions = response.data.provinces;
	                	patients.appointment.treatmentOptions = response.data.treatments;
	                	patients.patient.items = response.data.patients;
	            		patients.patient.data = response.data.patients;
	            		patients.wavetoget = response.data.wavetoget;
	                }
	            })
	            .catch(function (error) {
	            console.log(error);
	            });

	// axios.post('ajax/ajax-treatment-option.php',config)
	//             .then(function (response)
	//             {
	//                 if(response.data.success)
	//                 {
	//                 	patients.appointment.treatmentOptions = response.data.treatments;
	//                 }
	//             })
	//             .catch(function (error) {
	//             console.log(error);
	//             });

	// loadPatient();

	function loadPatient()
	{
		axios.post('ajax/ajax-patient.php',config)
	        .then(function (response)
	        {
	            if(response.data.success)
	            {
	            	patients.patient.items = response.data.patients;
	            	patients.patient.data = response.data.patients;
	            }
	        })
	        .catch(function (error) {
	        console.log(error);
	        });
	}

	var patients = new Vue({
		el: '#patients',
		data:
		{
			importOption: 1,
			addpatient:
			{
				createOptions: [{text: 'Import information from Wavetoget', value: 1}, {text: 'Do not import from Wavetoget', value: 2}],
				importOptions: [{text: 'Email', value: 1}, {text: 'Card number', value: 2}],
				createOption: 1,
				step2: false,
				step3: false,
				stepTitle: '2.',
				placeholder: 'Email',
				display: 'none',
				firstname: null,
				lastname: null,
				birthday: null,
				gender: null,
				marital_status: 1,
				phone: null,
				address: null,
				city: null,
				province: null,
				country: null,
				postalcode: null,
				email: null,
				card: null,
				cardholder: null,
				email_notification: true,
				sms_notification: true,
				provinceOptions: [],
				countryOptions: [],
				genderOptions:[{text: 'Male', value: 0}, {text: 'Female', value: 1}],
				maritalOptions: [{text: 'Unassigned', value: 1}, {text: 'Married', value: 2}, {text: 'Single', value: 3}],
				message: null,
				message2: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				alert: null
			},
			patient:
			{
				items: [],
				data: [],
				currentPage: 1,
				perPage: 10,
				totalRows: null,
				filter: null,
				hover: true,
				sortBy: null,
				modalInfo: { title: '', content: '' },
				fields:
					[
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
						{
							key: 'email',
							sortable: true
						},
						{
							key: 'birthday',
							label: 'Date of birth',
							sortable: true
						},
						{
							key: 'phone',
							sortable: true
						},
						{
							key: 'detail',
							label: '',
							sortable: false,
							thStyle: {width:'95px'}
						},
						{
							key: 'edit',
							sortable: false,
							thStyle: {width:'50px'}
						}
					],
				id: null,
				firstname: null,
				lastname: null,
				email: null,
				birthday: null,
				phone: null,
				gender: null,
				marital_status: null,
				address: null,
				city: null,
				province: null,
				country: null,
				postalcode: null,
				email_notification: null,
				sms_notification: null,
				provinceOptions: [],
				countryOptions: [],
				genderOptions:[{text: 'Male', value: 0}, {text: 'Female', value: 1}],
				maritalOptions: [{text: 'Unassigned', value: 1}, {text: 'Married', value: 2}, {text: 'Single', value: 3}],
				message: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				alert: null,
				pageOptions: [10, 25, 50, 100],
				cardholder: null,
				w2gDollar: null,
				w2gPoint: null,
				w2gCard: null,
				w2gEmail: null
			},
			appointment:
			{
				date: null,
				appointment: [],
				treatmentOptions: [],
				doctorOptions:[],
				firstTreatment: {id: null, price: null, duration: null},
				treatment:
					[
						{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}
					],
				next: 0,
				time: null,
				timeOptions:[],
				message: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				alert: null,
				total: null,
				t_duration: null
			},
			patientWavetoget:
			{
				email: null,
				message: null,
				dismissSecs: 2,
				dismissCountDown: 0,
				alert: null,
			},
			doctor: null,
			wavetoget: null,
			tabIndex: 0
		},
		watch:
		{
			doctor: function(val, oldVal)
			{
				this.searchAvailableTime(val);
			},
			importOption: function(val, oldVal)
			{
				if(val == 1)
				{
					this.placeholder = 'Email';
				}
				if(val == 2)
				{
					this.placeholder = 'Card number';
				}
			}
		},
		computed:
		{
			Total: function()
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
				return total;
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
			MaxDuration: function()
			{
				let max = 0;
				let time = this.appointment.time;
				let index = null;
				if(time != null)
				{
					for (var i = 0; i != this.appointment.timeOptions.length; i++)
					{
						if(time == this.appointment.timeOptions[i].value)
						{
							if(i == this.appointment.timeOptions.length - 1)
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
						for (var i = (index + 1); i != this.appointment.timeOptions.length; i++)
						{
							if((time + 1) == this.appointment.timeOptions[i].value)
							{
								max += 15;
							}
							time++;
						}
						max += 15;
					}
				}
				return max;
			}
		},
		methods:
		{
			showAlert(section)
			{
				if(section == 1)
				{
					this.addpatient.dismissCountDown = this.addpatient.dismissSecs;
				}
				if(section == 2)
				{
					this.patient.dismissCountDown = this.patient.dismissSecs;
				}
				if(section == 3)
				{
					this.appointment.dismissCountDown = this.appointment.dismissSecs;
				}
				if(section == 4)
				{
					this.patientWavetoget.dismissCountDown = this.patientWavetoget.dismissSecs;
				}
			},
			reset()
			{
				this.addpatient.firstname = this.addpatient.lastname = this.addpatient.email = this.addpatient.card = this.addpatient.birthday = null;
				this.addpatient.marital_status = 1;
				this.addpatient.email_notification = this.addpatient.sms_notification = true;
				this.addpatient.address = this.addpatient.city = this.addpatient.province = this.addpatient.country = null;
				this.addpatient.postalcode = this.addpatient.phone = this.addpatient.gender = this.addpatient.message = null;
				this.addpatient.message2 = this.addpatient.cardholder = null;
				this.addpatient.step2 = this.addpatient.step3 = false;
				this.addpatient.display = 'none';
				this.$root.$emit('bv::toggle::collapse', 'accordion1');
			},
			resetAppt()
			{
				this.appointment.date = this.doctor = this.appointment.time = null;
				this.appointment.treatment = [{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}];
			    this.appointment.firstTreatment = {id: null, price: null, duration: null};
			    this.appointment.doctorOptions = [];
	    		this.appointment.timeOptions = [];
	    		this.appointment.next = 0;
			},
			searchEmail()
			{
				if(this.addpatient.email != null)
				{
					axios.post('forms/form-search-cardholder.php',
					{
						email: this.addpatient.email
					},
					config)
		            .then(function (response)
		            {
		                if(response.data.success)
		                {
		                	patients.addpatient.firstname = response.data.firstname;
		                	patients.addpatient.lastname = response.data.lastname;
		                	patients.addpatient.birthday = response.data.birthday;
		                	patients.addpatient.phone = response.data.phone;
		                	patients.addpatient.address = response.data.address;
		                	patients.addpatient.city = response.data.city;
		                	patients.addpatient.province = response.data.province;
		                	patients.addpatient.country = response.data.country;
		                	patients.addpatient.postalcode = response.data.postalcode;
		                	patients.addpatient.cardholder = response.data.cardholder;
		                }
		                else
		                {
		                	patients.addpatient.firstname = null;
							patients.addpatient.lastname = null;
							patients.addpatient.card = null;
							patients.addpatient.birthday = null;
							patients.addpatient.marital_status = 1;
							patients.addpatient.address = null;
							patients.addpatient.city = null;
							patients.addpatient.province = null;
							patients.addpatient.country = null;
							patients.addpatient.postalcode = null;
							patients.addpatient.phone = null;
		                }
		            })
		            .catch(function (error) {
		            console.log(error);
		            });
				}
			},
			searchCard()
			{
				axios.post('forms/form-search-cardholder.php',
				{
					card: this.addpatient.card
				},
				config)
	            .then(function (response)
	            {
	                if(response.data.success)
	                {
	                	patients.addpatient.firstname = response.data.firstname;
	                	patients.addpatient.lastname = response.data.lastname;
	                	patients.addpatient.birthday = response.data.birthday;
	                	patients.addpatient.phone = response.data.phone;
	                	patients.addpatient.address = response.data.address;
	                	patients.addpatient.city = response.data.city;
	                	patients.addpatient.province = response.data.province;
	                	patients.addpatient.country = response.data.country;
	                	patients.addpatient.postalcode = response.data.postalcode;
	                	patients.addpatient.cardholder = response.data.cardholder;
	                	patients.addpatient.email = response.data.email;
	                }
	            })
	            .catch(function (error) {
	            console.log(error);
	            });
			},
			addPatient(evt)
			{
				evt.preventDefault();
				axios.post('forms/form-update-patient.php?add',
				{
					firstname: this.addpatient.firstname,
					lastname: this.addpatient.lastname,
					email: this.addpatient.email,
					phone: this.addpatient.phone,
					birthday: this.addpatient.birthday,
					gender: this.addpatient.gender,
					marital_status: this.addpatient.marital_status,
					address: this.addpatient.address,
					city: this.addpatient.city,
					province: this.addpatient.province,
					country: this.addpatient.country,
					postalcode: this.addpatient.postalcode,
					wavetoget: this.addpatient.cardholder,
					email_notification: this.addpatient.email_notification,
					sms_notification: this.addpatient.sms_notification
				}
				,config)
	            .then(function (response)
	            {
	            	console.log(response);
	            	if(response.data.success)
	            	{
	            		patients.addpatient.alert = 'success';
	            		patients.addpatient.message = response.data.message;
	            		patients.showAlert(1);
	            		loadPatient();
	            		setTimeout(function(){
				            patients.reset();
				            }, 2000);
	            	}
	            	else
	            	{
	            		patients.addpatient.alert = 'danger';
	            		patients.addpatient.message = response.data.message;
	            		patients.showAlert(1);
	            	}
	            })
	            .catch(function (error) {
	            console.log(error);
	            });
			},
			info (item, index, button)
			{
			    this.patient.modalInfo.title = item.firstname + ' ' + item.lastname;
			    this.patient.id = item.id;
			    this.patient.firstname = item.firstname;
			    this.patient.lastname = item.lastname;
			    this.patient.email = item.email;
			    this.patientWavetoget.email = item.email;
			    this.patient.phone = item.phone;
			    this.patient.birthday = item.birthday;
			    this.patient.gender = item.gender;
			    this.patient.marital_status = item.marital_status;
			    this.patient.address = item.address;
			    this.patient.city = item.city;
			    this.patient.province = item.province;
			    this.patient.country = item.country;
			    this.patient.postalcode = item.postalcode;
			    this.patient.cardholder = item.wavetoget;
			    this.patient.email_notification = item.email_notification ? true : false;
			    this.patient.sms_notification = item.sms_notification ? true : false;
			    this.$root.$emit('bv::show::modal', 'modalInfo', button)
	    	},
	    	resetModal ()
			{
	    		this.patient.modalInfo.title = this.patient.firstname = this.patient.lastname = this.patient.birthday = this.patient.doctor = this.patient.gender = null;
	    		this.patient.message = this.patient.email = this.patient.description = this.patient.id = this.patient.marital_status = this.patient.address = null;
	    		this.patient.city = this.patient.province = this.patient.country = this.patient.postalcode = this.patientWavetoget.email = null;
	    		this.patient.notification = null;
	    		this.appointment.date = this.doctor = this.appointment.time = this.patient.cardholder = this.patient.w2gPoint = this.patient.w2gDollar = null;
	    		this.appointment.doctorOptions = [];
	    		this.appointment.timeOptions = [];
	    		this.appointment.next = 0;
			    this.appointment.treatment = [{elementID: 't' + 0, treatment: {id: null, price: null, duration: null}}];
			    this.appointment.firstTreatment = {id: null, price: null, duration: null};
	    		this.patient.dismissCountDown = this.tabIndex = 0;
		    },
		    onFiltered (filteredItems)
		    {
		    	this.patient.totalRows = filteredItems.length
		        this.patient.currentPage = 1
		    },
		    updatePatient (evt)
		    {
		    	evt.preventDefault();
		    	axios.post('forms/form-update-patient.php?update',
	        		{
	        			id: this.patient.id,
	        			firstname: this.patient.firstname,
	        			lastname: this.patient.lastname,
	        			email: this.patient.email,
	        			birthday: this.patient.birthday,
	        			gender: this.patient.gender,
	        			marital_status: this.patient.marital_status,
	        			address: this.patient.address,
	        			city: this.patient.city,
	        			province: this.patient.province,
	        			country: this.patient.country,
	        			postalcode: this.patient.postalcode,
	        			phone: this.patient.phone,
	        			code: this.patient.code,
	        			email_notification: this.patient.email_notification,
	        			sms_notification: this.patient.sms_notification
	        		},
	        		config)
	            .then(function (response)
	            {
	                if(response.data.success)
	                {
	                	patients.patient.message = response.data.message;
	                	patients.patient.alert = 'success';
	                	patients.patientWavetoget.email = patients.patient.email;
	                	patients.showAlert(2);
	                	loadPatient();
	                }
	                else
	                {
	                	patients.patient.message = response.data.message;
	                	patients.patient.alert = 'danger';
	                	patients.showAlert(2);
	                }
	            })
	            .catch(function (error) {
	            console.log(error);
	            });
		    },
		    moreTreatment()
		    {
		    	if(this.appointment.next == 0)
		    		this.appointment.next++;
		    	else
		    	{
		    		this.appointment.treatment.push({elementID: 't' + this.appointment.next++, treatment: {id: null, price: null, duration: null}});
		    	}
		    },
		    searchAvailableDoctor()
		    {
		    	this.doctor = null;
		    	this.appointment.time = null;
		    	if(this.appointment.date != null)
		    	{
		    		axios.post('ajax/ajax-available-appointment.php?date',
		    		{
		    			date: this.appointment.date
		    		},config)
		            .then(function (response)
		            {
		                if(response.data.success)
		                {
		                	patients.appointment.doctorOptions = response.data.doctors;
		                }
		                else
		                {
		                	patients.appointment.doctorOptions = [];
		                	patients.appointment.message = response.data.message;
		                	patients.appointment.alert = 'danger';
		                	patients.showAlert(3);
		                }
		            })
		            .catch(function (error) {
		            console.log(error);
		            });
		    	}	
		    },
		    searchAvailableDoctor2()
		    {		    	
		    },
		    searchAvailableTime(value)
		    {
		    	if(value != null)
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
		                	patients.appointment.timeOptions = response.data.time;
		                }
		                else
		                {
		                	patients.appointment.timeOptions = [];
		                	patients.appointment.message = response.data.message;
		                	patients.appointment.alert = 'danger';
		                	patients.showAlert(3);
		                }
		            })
		            .catch(function (error) {
		            console.log(error);
		            });
		    	}
		    },
		    addAppointment(evt)
		    {
		    	evt.preventDefault();
		    	axios.post('forms/form-update-appointment.php?add',
	        		{
	        			doctor: this.doctor,
	        			patient: this.patient.id,
	        			date: this.appointment.date,
	        			time: this.appointment.time,
	        			firsttreatment: this.appointment.firstTreatment,
	        			treatments: this.appointment.treatment,
	        			duration: this.appointment.t_duration
	        		},
	        		config)
	            .then(function (response)
	            {
	                if(response.data.success)
	                {
	                	patients.appointment.message = response.data.message;
	                	patients.appointment.alert = 'success';
	                	patients.showAlert(3);
	                	// loadPatient();
	                	setTimeout(function(){
				            patients.resetAppt();
				            }, 2000);
	                }
	                else
	                {
	                	patients.appointment.message = response.data.message;
	                	patients.appointment.alert = 'danger';
	                	patients.showAlert(3);
	                }
	            })
	            .catch(function (error) {
	            console.log(error);
	            });
		    },
		    // updateAppointment(item)
		    // {
		    // 	console.log(item);
		    // },
		    date(value)
		    {
		    	return value;
		    },
		    addressFormatter(item)
		    {
		    	let address = '';
		    	if(item.address != null)
		    		address = item.address;
		    	if(item.city != null)
		    		address += ', ' + item.city;
		    	if(item.province_code != null)
		    		address += ', ' + item.province_code;
		    	if(item.country_code)
		    		address += ', ' + item.country_code;
		    	if(item.postalcode)
		    		address += ', ' + item.postalcode;
		    	return (address == '') ? 'Null' : address;
		    },
		    loadCardholderInfo(cardholder)
		    {
		    	if(this.patient.w2gPoint == null && cardholder != null)
		    	{
		    		axios.post('forms/form-cardholder.php?getinfo',
		    		{
		    			cardholder: this.patient.cardholder
		    		}
		    		,config)
		            .then(function (response)
		            {
		            	console.log(response.data);
		                if(response.data.success)
		                {
		                	patients.patient.w2gPoint = response.data.info.points;
		                	patients.patient.w2gDollar = response.data.info.dollars;
		                }
		            })
		            .catch(function (error) {
		            console.log(error);
		            });
			    }	    		
		    },
		    linkCardholder(evt)
		    {
		    	evt.preventDefault();
		    	if(this.patient.w2gEmail == null && this.patient.w2gCard ==  null)
		    	{
		    		this.patientWavetoget.message = 'Invalid input';
		    		this.patientWavetoget.alert = 'danger';
	                this.showAlert(4);
		    	}
		    	else
		    	{
		    		axios.post('forms/form-cardholder.php?link',
		    		{
		    			email: this.patient.w2gEmail,
		    			card: this.patient.w2gCard,
		    			patient: this.patient.id
		    		}
		    		,config)
		            .then(function (response)
		            {
		                if(response.data.success)
						{
							patients.patient.w2gPoint = response.data.points;
							patients.patient.w2gDollar = response.data.dollars;
							patients.patientWavetoget.message = response.data.message;
							patients.patientWavetoget.alert = 'success';
							patients.showAlert(4);
							patients.patient.cardholder = response.data.cardholder;
							loadPatient();
						}
						else
						{
							patients.patientWavetoget.message = response.data.message;
							patients.patientWavetoget.alert = 'danger';
							patients.showAlert(4);
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
					email: this.patientWavetoget.email,
					patient: this.patient.id
				}
				,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						patients.patient.w2gPoint = response.data.points;
						patients.patient.w2gDollar = response.data.dollars;
						patients.patient.cardholder = response.data.cardholder;
						patients.patientWavetoget.message = response.data.message;
						patients.patientWavetoget.alert = 'success';
						patients.showAlert(4);
						loadPatient();
					}
					else
					{
						patients.patientWavetoget.message = response.data.message;
						patients.patientWavetoget.alert = 'danger';
						patients.showAlert(4);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
		    UnlinkCardholder()
			{
				axios.post('forms/form-cardholder.php?unlink',
				{
					patient: this.patient.id
				}
				,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						patients.patientWavetoget.message = response.data.message;
						patients.patientWavetoget.alert = 'success';
						patients.showAlert(4);
						patients.patient.cardholder = null;
						loadPatient();
					}
					else
					{
						patients.patientWavetoget.message = response.data.message;
						patients.patientWavetoget.alert = 'danger';
						patients.showAlert(4);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			},
			phoneFormatter(section)
			{
				if(section == 1)
					value = this.patient.phone;
				if(section == 2)
					value = this.addpatient.phone;
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
				if(section == 1)
					this.patient.phone = value;
				if(section == 2)
					this.addpatient.phone = value;
			},
			gotoStep2()
			{
				if(this.addpatient.createOption == 1)
				{
					this.addpatient.step2 = true;
					this.addpatient.step3 = false;
					this.addpatient.stepTitle = '3.';
					this.addpatient.display = 'block';
					this.$root.$emit('bv::toggle::collapse', 'accordion2');
				}
				if(this.addpatient.createOption == 2)
				{
					this.addpatient.step2 = false;
					this.addpatient.step3 = true;
					this.addpatient.display = 'none';
					this.addpatient.stepTitle = '2.';
					this.$root.$emit('bv::toggle::collapse', 'accordion3');
				}
			},
			gotoStep3()
			{
				if(this.addpatient.card == null || this.addpatient.card == '')
				{
					this.addpatient.message2 = 'Invalid input';
					setTimeout(function(){
						registration.addpatient.message2 = null;
						}, 2500);
				}
				else
				{
					if(this.importOption == 1)
						value = 'email';
					if(this.importOption == 2)
						value = 'card';
					let formData = new FormData();
					formData.append(value, this.addpatient.card);
					axios.post('forms/form-search-cardholder.php',
					formData
					,config)
					.then(function (response)
					{
						if(response.data.success)
						{
							patients.addpatient.firstname = response.data.firstname;
							patients.addpatient.lastname = response.data.lastname;
							patients.addpatient.email = response.data.email;
							patients.addpatient.birthday = response.data.birthday;
							patients.addpatient.phone = response.data.phone;
							patients.addpatient.address = response.data.address;
							patients.addpatient.city = response.data.city;
							patients.addpatient.province = response.data.province;
							patients.addpatient.country = response.data.country;
							patients.addpatient.postalcode = response.data.postalcode;
							patients.addpatient.cardholder = response.data.cardholder;
							patients.addpatient.step3 = true;
							patients.phoneFormatter(2);
							patients.$root.$emit('bv::toggle::collapse', 'accordion3');
						}
						else
						{
							patients.addpatient.message2 = 'No result found';
							setTimeout(function(){
								patients.addpatient.message2 = null;
							}, 2000);
						}
					})
					.catch(function (error) {
					console.log(error);
					});
					}
			}
		}
	});
}