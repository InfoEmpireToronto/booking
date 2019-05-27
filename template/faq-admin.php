<section class="content">
	<div id="faq">
		<template>
			<b-container>
				<b-row>
					<b-col sm="12">
						<div role="tablist">
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-1 variant="light" class="text-left">How to add a treatment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-1" visible role="tabpanel">
									<b-card-body>
										1. Click TREATMENTS from the top menu bar and click on "Add a treatment" tab.<br/>
										2. Fill in the form.<br/>
										3. Click ADD to activate the treatment.<br/>
										Note that fields marked with an asterisk (*) are required.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block  v-b-toggle.accordion-2 variant="light" class="text-left">How to add a doctor? </b-button>
								</b-card-header>
								<b-collapse id="accordion-2" role="tabpanel">
									<b-card-body>
										1. Click USERS from the top menu bar and click on "Add a doctor" tab.<br/>
										2. Fill in the form.<br/>
										3. Click ADD to activate the doctor.<br/>
										Note that fields marked with an asterisk (*) are required.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-3 variant="light" class="text-left">How to activate/deactivate a doctor?</b-button>
								</b-card-header>
								<b-collapse id="accordion-3" role="tabpanel">
									<b-card-body>
										1. Click USERS from the top menu bar and "Doctors" tab.<br/>
										2. Click EDIT button in the last column.<br/>
										3. Click  ACTIVATE/DEACTIVATE button.<br/>
										You may deactivate the doctor in case of vacation or short-term absence.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-4 variant="light" class="text-left">How to create an appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-4" role="tabpanel">
									<b-card-body>
										To make an appointment, choose one of the options bellow. <br/>
										1. Click APPOINTMENTS from the top menu bar and click "Create an appointment" tab, follow the steps.<br/>
										2. Click APPOINTMENTS from the top menu bar and click "View/Edit" tab. Select a doctor from the dropdown options. Select date from calendar on the right side or
											arrows on the top. Click a timeslot table cell, a pop up window will show up. Fill in the form to add an appointment.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-5 variant="light" class="text-left">How to add a new patient?</b-button>
								</b-card-header>
								<b-collapse id="accordion-5" role="tabpanel">
									<b-card-body>
										1. Click PATIENTS from the top menu bar and click "Add a patient" tab.<br/>
										2. Follow the steps and fill in the form.<br/>
										3. Click ADD button.<br/>
										Note that fields marked with an asterisk (*) are required.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-6 variant="light" class="text-left">How to find an existing patient?</b-button>
								</b-card-header>
								<b-collapse id="accordion-6" role="tabpanel">
									<b-card-body>
										1. Click PATIENTS from the top menu bar and click "Patients" tab.<br/>
										2. Use search input to filter the table.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-7 variant="light" class="text-left">How to delete an appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-7" role="tabpanel">
									<b-card-body>
										To delete an appointment, choose one of the options bellow.<br/>
										1. Click APPOINTMENTS and click "View/Edit" tab, find the appointment form the table and click on it. A pop up window will appear. 
											Click DELETE button.<br/>
										2. Click APPOINTMENTS and click either "All" tab, find the appointment form the table and click EDIT button. 
											A pop up window will appear. Click DELETE button.<br/>
										 Note that deleted appointments cannot be restored!
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-8 variant="light" class="text-left">How to view appointment details?</b-button>
								</b-card-header>
								<b-collapse id="accordion-8" role="tabpanel">
									<b-card-body>
										To view appointment details, choose one of the options bellow.<br/>
										1. Click APPOINTMENTS and click "View/Edit" tab. Either move cusor to a timeslot table cell and small information window will appear.
										Or find the appointment form the table and click on it. A pop up window will appear. <br/>
										2. Click APPOINTMENTS and click "All" tab. Find the appointment and click the button in the last column. A pop up window will appear.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-9 variant="light" class="text-left">How to change the time of the appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-9" role="tabpanel">
									<b-card-body>
										To change the time of the appointment, choose one of the options bellow.<br/>
										1. Click APPOINTMENTS and click "View/Edit" tab. Find the appointment form the table and click on it. A pop up window will appear. 
										Change time and click SAVE.<br/>
										2. Click APPOINTMENTS and click "All" tab. Find the appointment and click the button in the last column. A pop up window will appear. 
										Change time and click SAVE.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-10 variant="light" class="text-left">How to add a product?</b-button>
								</b-card-header>
								<b-collapse id="accordion-10" role="tabpanel">
									<b-card-body>
										1. Click PRODUCTS from the top menu bar and click on "Add a product" tab.<br/>
										2. Fill in the form.
										3. Click ADD to activate the product.<br/>
										Note that fields marked with an asterisk (*) are required.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-11 variant="light" class="text-left">How to change doctor availability?</b-button>
								</b-card-header>
								<b-collapse id="accordion-11" role="tabpanel">
									<b-card-body>
										1. Click USERS from the top menu bar and click on "Doctors" tab.<br/>
										2. Click EDIT button in AVAILABILITY column.<br/>
										3. Change availability and click SAVE.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-12 variant="light" class="text-left">How to search for appointments?</b-button>
								</b-card-header>
								<b-collapse id="accordion-12" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS from the top menu bar and click on "All" tab.<br/>
										2. Type information in search field.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-13 variant="light" class="text-left">How to pay for an appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-13" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS from the top menu bar and click on "Unpaid" or "Partial paid" tab.<br/>
										2. Click PAY button in the last coulmn.<br/>
										3. Select payment method and enter amount.<br/>
										4. Click SUBMIT.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-14 variant="light" class="text-left">How to print out appointment invoice?</b-button>
								</b-card-header>
								<b-collapse id="accordion-14" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS from the top menu bar and click on "Paid" tab.<br/>
										2. Click VIEW button in INVOICE column. It will bring you to invoice page.<br/>
										3. Print the page out.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-15 variant="light" class="text-left">How to connect to Wavetoget?</b-button>
								</b-card-header>
								<b-collapse id="accordion-15" role="tabpanel">
									<b-card-body>
										1. Click SETTINGS from the top menu bar and click on "Store setting" tab.<br/>
										2. Enter your API key and click SAVE.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-16 variant="light" class="text-left">How to change doctor's default availability?</b-button>
								</b-card-header>
								<b-collapse id="accordion-16" role="tabpanel">
									<b-card-body>
										1. Click SETTINGS from the top menu bar and click on "Store setting" tab.<br/>
										2. Change default start time and default end time.<br/>
										3. Click SAVE.<br/>
										Changes will apply to USERS/"Add a doctor".
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-17 variant="light" class="text-left">How to change notification email content?</b-button>
								</b-card-header>
								<b-collapse id="accordion-17" role="tabpanel">
									<b-card-body>
										1. Click SETTINGS from the top menu bar and click on "Notification" tab.<br/>
										2. Edit content and Click SAVE.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-18 variant="light" class="text-left">How to reset password?</b-button>
								</b-card-header>
								<b-collapse id="accordion-18" role="tabpanel">
									<b-card-body>
										1. Click SETTINGS from the top menu bar and click on "Reset password" tab.<br/>
										2. Enter current password and new password.<br/>
										3. Click UPDATE.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-19 variant="light" class="text-left">How to connect a patient to Wavetoget?</b-button>
								</b-card-header>
								<b-collapse id="accordion-19" role="tabpanel">
									<b-card-body>
										1. Click PATIENTS from the top menu bar and click on "Patients" tab.<br/>
										2. Click EDIT button in the last column. A pop up window will appear.<br/>
										3. Click "Wavetoget" tab. If the patient uses the same email, click "Link with the same email" button. If the patient uses a different eamil 
										on Wavetoget, enter either email or card number.<br/>
										4. click LINK button.
									</b-card-body>
								</b-collapse>
							</b-card>
						</div>
					</b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<script>
loadMenu('FAQ');

var faqs = new Vue({
	el: '#faq',
	data:
	{
		
	}
});
</script>