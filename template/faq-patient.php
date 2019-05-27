<section class="content">
	<div id="faq">
		<template>
			<b-container>
				<b-row>
					<b-col sm="12">
						<div role="tablist">
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-4 variant="light" class="text-left">How to make an appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-4" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS from the top menu bar and click "Book an appointment" tab.<br/>
										2. Follow the steps.<br/>
										3. Click BOOK button.
										
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-7 variant="light" class="text-left">How to cancel an appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-7" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS and click "My appointments" tab<br/>
										2. Find the appointment form the table and click EDIT button in the last column. A pop up window will appear.<br/>
										3. Click CANCEL button.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-8 variant="light" class="text-left">How to view appointment details?</b-button>
								</b-card-header>
								<b-collapse id="accordion-8" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS and click "My appointments" tab.<br/>
										2. Find the appointment form the table and click EDIT/VIEW button in the last column. A pop up window will appear.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-9 variant="light" class="text-left">How to change the time of the appointment?</b-button>
								</b-card-header>
								<b-collapse id="accordion-9" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS and click "My appointments" tab.<br/>
										2. Find the appointment form the table and click EDIT button in the last column. A pop up window will appear.<br/>
										3. Change time and click SAVE.
									</b-card-body>
								</b-collapse>
							</b-card>
							
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-12 variant="light" class="text-left">How to search for appointments?</b-button>
								</b-card-header>
								<b-collapse id="accordion-12" role="tabpanel">
									<b-card-body>
										1. Click APPOINTMENTS from the top menu bar and click on "My appointments" tab.<br/>
										2. Type information in search field.
									</b-card-body>
								</b-collapse>
							</b-card>
							<b-card no-body class="mb-3">
								<b-card-header header-tag="header" class="p-1" role="tab">
									<b-button block v-b-toggle.accordion-19 variant="light" class="text-left">How to connect my account to Wavetoget?</b-button>
								</b-card-header>
								<b-collapse id="accordion-19" role="tabpanel">
									<b-card-body>
										1. Click SETTINGS from the top menu bar and click on "Wavetoget" tab.<br/>
										2. If you use the same email, click "Link with the same email" button. If you use a different eamil 
										on Wavetoget, enter either email or card number.<br/>
										3. Click LINK button.
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