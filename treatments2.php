<?php
$title = 'Treatment';
require('header.php');
?>
<section class="content">
	<div id="treatments">
		<template>
			<b-container>
				<b-row>
					<b-col sm="12">
						<div class="form-group">
							<b-col sm="12" offset-md="4" md="4">
								<b-form-group class="mb-0">
									<b-input-group>
										<b-form-input v-model="filter" placeholder="Search" ></b-form-input>
											<b-input-group-append>
												<b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
											</b-input-group-append>  
										</b-input-group>
							</b-form-group>
								</b-col>
						</div>
						<div class="form-group">
							<b-col sm="12">
							<b-table responsive :items.sync="items" :fields="fields" class="borderless"
								:current-page="currentPage" :per-page="perPage" :filter="filter" @filtered="onFiltered" 
								:sort-desc.sync="sortDesc" thead-class="hidden_header">
								<template slot="content" slot-scope="row">
									<div class="treatment-row">
										<b-col sm="12" md="3">
											<img :src="row.item.imagePath" style="width: 100%;">
										</b-col>
										<b-col sm="12" md="9">
											<h5>{{row.item.name}}</h5>
											<p class="treatment-description">Duration: {{row.item.duration_time}} min</p>
											<p class="treatment-description">Price: ${{row.item.price}}</p>
											<p class="treatment-description">Description: {{row.item.description}}</p>
										</b-col>
									</div>
								</template>
							</b-table>
							</b-col>
						</div>
						<div class="form-group text-center" v-if="totalRows > perPage">
							<b-col sm="12" offset-md="4" md="4">
								<b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0"/>
							</b-col>
						</div>
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
loadMenu('Treatments');
axios.post('ajax/ajax-treatment.php',config)
	.then(function (response)
	{
		if(response.data.success)
		{
			treatments.items = response.data.treatments;
		}
	})
	.catch(function (error) {
	console.log(error);
	});

var treatments = new Vue({
	el: '#treatments',
	data:
	{
		items: [],
		message: null,
		currentPage: 1,
		perPage: 10,
		totalRows: null,
		filter: null,
		sortDesc: false,
		fields:
			[
				{
					key: 'content',
					sortable: false
				}
			],
		pageOptions: [10, 25, 50, 100]
	},
	watch:
	{

	},
	methods:
	{
		onFiltered (filteredItems)
		{
			this.totalRows = filteredItems.length;
			this.currentPage = 1
		},
	}
});
</script>