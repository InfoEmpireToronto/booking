<?php
require('header.php');
?>
<section class="content">
	<div id="locations">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Locations" active>
								<b-container>
									<search-row :item="location" :active="showactive"></search-row>
									<b-table responsive :hover="location.hover" :items.sync="location.items" :fields="location.fields" :sort-by.sync="location.sortBy" 
										:current-page="location.currentPage" :per-page="location.perPage" :filter="location.filter" @filtered="onFiltered" 
										:sort-by.sync="location.sortBy" :sort-desc.sync="location.sortDesc">
										<template slot="address" slot-scope="data">
											{{data.item.address}}, {{data.item.city}}, {{data.item.province_name}}, {{data.item.postalcode}}
										</template>
										<template slot="edit" slot-scope="row">
											<b-button size="sm" @click.stop="info(row.item, row.index, $event.target)" class="mr-1">
          										Edit
        									</b-button>
										</template>
									</b-table>
									<table-pagination :item="location"></table-pagination>
    								<b-modal id="modalInfo" @hide="resetModal" :title="location.modalInfo.title" >
        									<b-form @submit="onSubmit" id="edit-location">
        										<modal-edit-form :item="location"></modal-edit-form>
      										</b-form>
      										<div slot="modal-footer" class="w-100">
									       		<b-row>
									       			<b-col sm="4">
									       				<b-button :variant="location.variant" @click="updateLocation">{{location.activate}}</b-button>
									       			</b-col>
        											<alert-box :item="location"></alert-box>
         											<b-col sm="3"class="text-right">
         												<b-button form="edit-location" type="submit" variant="primary">Save</b-button>
									       			</b-col>
       											</b-row>
									       </div>
    								</b-modal>
								</b-container>
						  	</b-tab>
						  	<b-tab title="Add location" >
								<b-form @submit="addLocation" class="text-center">
									<add-location :item="addlocation"></add-location>
								</b-form>
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
<script type="text/x-template" id="add-location-temp">
<b-container>
	<b-row align-h="center">
		<b-col sm="2">
			<label for="name">Name</label>
		</b-col>
		<b-col sm="4">
			<b-form-input v-model="item.name" type="text" id="name" required></b-form-input>
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
			<label for="address">Address</label>
		</b-col>
		<b-col sm="4">
			<b-form-input v-model="item.address" type="text" id="address" required></b-form-input>
		</b-col>
		<b-col sm="2">
			<label for="city">City</label>
		</b-col>
		<b-col sm="4">
			<b-form-input v-model="item.city" type="text" id="city" required></b-form-input>
		</b-col>
	</b-row>
	<b-row align-h="center">
		<b-col sm="2">
			<label for="province">Province</label>
		</b-col>
		<b-col sm="4">
			<b-form-select v-model="item.province" :options="item.provinceOptions" id="province" required/>
		</b-col>
		<b-col sm="2">
			<label for="country">Country</label>
		</b-col>
		<b-col sm="4">
			<b-form-select v-model="item.country" :options="item.countryOptions" id="country" required/>
		</b-col>
	</b-row>
	<b-row align-h="center">
		<b-col sm="2">
			<label for="postalcode">Postal code</label>
		</b-col>
		<b-col sm="4">
			<b-form-input v-model="item.postalcode" type="text" id="postalcode" required></b-form-input>
		</b-col>
		<b-col sm="2">
			<label for="email">Email</label>
		</b-col>
		<b-col sm="4">
			<b-form-input v-model="item.email" type="text" id="email"></b-form-input>
		</b-col>
	</b-row>
	<form-footer :item="item"></form-footer>
</b-container>
</script>

<script type="text/x-template" id="modal-editform-temp">
<div>
    <b-row>
    	<label for="edit-name" class="col-sm-3">Name</label>
    	<b-col sm="9">
    		<b-form-input v-model="item.name" id="edit-name" required></b-form-input>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-phone" class="col-sm-3">Phone</label>
    	<b-col sm="9">
    		<b-form-input v-model="item.phone" id="edit-phone"></b-form-input>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-email" class="col-sm-3">Email</label>
    	<b-col sm="9">
    		<b-form-input v-model="item.email" id="edit-email"></b-form-input>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-address" class="col-sm-3">Address</label>
    	<b-col sm="9">
    		<b-form-input v-model="item.address" id="edit-address" required></b-form-input>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-city" class="col-sm-3">City</label>
    	<b-col sm="9">
    		<b-form-input id="edit-city" v-model="item.city" required></b-form-input>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-province" class="col-sm-3">Province</label>
    	<b-col sm="9">
    		<b-form-select v-model="item.province" :options="item.provinceOptions" id="edit-province" required/>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-country" class="col-sm-3">Country</label>
    	<b-col sm="9">
    		<b-form-select v-model="item.country" :options="item.countryOptions" id="edit-country" required/>
    	</b-col>
    </b-row>
    <b-row>
    	<label for="edit-postalcode" class="col-sm-3">Postal code</label>
    	<b-col sm="9">
    		<b-form-input id="edit-postalcode" v-model="item.postalcode" required></b-form-input>
    	</b-col>
    </b-row>
</div>
</script>

<script>
loadMenu('Locations');

Vue.component('add-location', {
  props: ['item'],
  template: '#add-location-temp'
});

Vue.component('modal-edit-form', {
  props: ['item'],
  template: '#modal-editform-temp'
});

axios.post('ajax/ajax-country-province.php',config)
            .then(function (response)
            {
                if(response.data.success)
                {
                	locations.addlocation.countryOptions = response.data.countries;
                	locations.addlocation.provinceOptions = response.data.provinces;
                	locations.location.countryOptions = response.data.countries;
                	locations.location.provinceOptions = response.data.provinces;
                }
            })
            .catch(function (error) {
            console.log(error);
            });

loadLocation(1);

function loadLocation(filter)
{
	axios.post('ajax/ajax-location.php',config)
        .then(function (response)
        {
            if(response.data.success)
            {
            	locations.location.items = response.data.locations;
            	locations.location.data = response.data.locations;
            	locations.filterData(filter);
            }
        })
        .catch(function (error) {
        console.log(error);
        });
}

var locations = new Vue({
	el: '#locations',
	data:
	{
		addlocation:
		{
			name: null,
			phone: null,
			address: null,
			city: null,
			province: null,
			provinceOptions: [],
			country: null,
			countryOptions: [],
			postalcode: null,
			email: null,
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null
		},
		location:
		{
			items: [],
			data: [],
			currentPage: 1,
			perPage: 10,
			totalRows: null,
			filter: null,
			hover: true,
			sortBy: 'active',
			sortDesc: true,
			modalInfo: { title: '', content: '' },
			fields:
				[
					{
						key: 'name',
						sortable: true
					},
					{
						key: 'phone',
						sortable: true
					},
					{
						key: 'address',
						sortable: true
					},
					{
						key: 'country_name',
						label: 'Country',
						sortable: true
					},
					{
						key: 'active',
						label: 'Status',
						sortable: true,
						formatter: (value) => { return (value == 1) ? 'Active' : 'Inactive' }
					},
					{
						key: 'edit',
						sortable: false
					}
				],
			id: null,
			name: null,
			phone: null,
			address: null,
			province: null,
			country: null,
			postalcode: null,
			email: null,
			active: null,
			activate: null,
			provinceOptions: [],
			countryOptions: [],
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null,
			activeOptions: [{value: 'all', text:'All'}, {value: '1', text: 'Active'}, {value: '0', text: 'Inactive'}]
		},
		showactive: 1
	},
	watch:
	{
		showactive: function(val, oldVal)
		{
			this.filterData(val);
		}
	},
	methods:
	{
		showAlert(section)
		{
			if(section == 1)
			{
				this.addlocation.dismissCountDown = this.addlocation.dismissSecs;
			}
			if(section == 2)
			{
				this.location.dismissCountDown = this.location.dismissSecs;
			}
		},
		reset()
		{
			this.addlocation.name = null;
			this.addlocation.phone = null;
			this.addlocation.address = null;
			this.addlocation.city = null;
			this.addlocation.province = null;
			this.addlocation.country = null;
			this.addlocation.postalcode = null;
			this.addlocation.email = null;
		},
		addLocation(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-location.php?addlocation',
			{
				name: this.addlocation.name,
				phone: this.addlocation.phone,
				address: this.addlocation.address,
				city: this.addlocation.city,
				province: this.addlocation.province,
				country: this.addlocation.country,
				postalcode: this.addlocation.postalcode,
				email: this.addlocation.email
			}
			,config)
            .then(function (response)
            {
            	if(response.data.success)
            	{
            		locations.addlocation.alert = 'success';
            		locations.addlocation.message = response.data.message;
            		locations.showAlert(1);
            		loadLocation(locations.showactive);
            		setTimeout(function(){
			            locations.reset();
			            }, 2000);
            	}
            	else
            	{
            		locations.addlocation.alert = 'danger';
            		locations.addlocation.message = response.data.message;
            		locations.showAlert(1);
            	}
            })
            .catch(function (error) {
            console.log(error);
            });
		},
		filterData(value)
		{
			let temp = [];
			if(value == 'all')
			{
				this.location.items = this.location.data;
			}
			if(value == 1)
			{
				for(var i = 0; i != this.location.data.length; i++)
				{
					if(this.location.data[i].active == 1)
						temp.push(this.location.data[i])
				}
				this.location.items = temp;
			}
			if(value == 0)
			{
				for(var i = 0; i != this.location.data.length; i++)
				{
					if(this.location.data[i].active == 0)
						temp.push(this.location.data[i])
				}
				this.location.items = temp;
			}
		},
		info (item, index, button)
		{
		    this.location.modalInfo.title = item.name;
		    this.location.id = item.id;
		    this.location.name = item.name;
		    this.location.phone = item.phone;
		    this.location.email = item.email;
		    this.location.address = item.address;
		    this.location.city = item.city;
		    this.location.province = item.province;
		    this.location.country = item.country;
		    this.location.postalcode = item.postalcode;
		    this.location.activate = (item.active == 1) ? 'Deactivate' : 'Activate';
		    this.location.variant = (item.active == 1) ? 'danger' : 'success';
		    this.$root.$emit('bv::show::modal', 'modalInfo', button)
    	},
    	resetModal () {
    		this.location.modalInfo.title = this.location.firstname = this.location.lastname = this.location.location = this.location.doctor = this.location.user = '';
    		this.location.message = this.location.email = this.location.description = this.location.id = '';
    		this.location.dismissCountDown = 0;
	    },
	    onFiltered (filteredItems)
	    {
	    	this.location.totalRows = filteredItems.length
	        this.location.currentPage = 1
	    },
	    onSubmit (evt)
	    {
	    	evt.preventDefault();
	    	axios.post('forms/form-update-location.php?update',
        		{
        			id: this.location.id,
        			name: this.location.name,
        			address: this.location.address,
        			city: this.location.city,
        			province: this.location.province,
        			country: this.location.country,
        			postalcode: this.location.postalcode,
        			phone: this.location.phone,
        			email: this.location.email
        		},
        		config)
            .then(function (response)
            {
                if(response.data.success)
                {
                	locations.location.message = response.data.message;
                	locations.location.alert = 'success';
                	locations.showAlert(2);
                	loadLocation(locations.showactive);
                }
                else
                {
                	locations.location.message = response.data.message;
                	locations.location.alert = 'danger';
                	locations.showAlert(2);
                }
            })
            .catch(function (error) {
            console.log(error);
            });
        },
        updateLocation()
        {
        	axios.post('forms/form-update-location.php?' + this.location.activate,
        		{
        			id: this.location.id
        		},
        		config)
            .then(function (response)
            {
                if(response.data.success)
                {
                	locations.location.message = response.data.message;
                	locations.location.alert = 'success';
                	locations.showAlert(2);
                	loadLocation(locations.showactive);
                }
                else
                {
                	locations.location.message = response.data.message;
                	locations.location.alert = 'danger';
                	locations.showAlert(2);
                }
            })
            .catch(function (error) {
            console.log(error);
            });
        }
	}
});
</script>
<!-- 									<b-row align-h="end">
										<b-col sm="4">
											<b-form-group class="mb-0">
									        	<b-input-group>
									            	<b-form-input v-model="location.filter" placeholder="Type to Search" ></b-form-input>
										            <b-input-group-append>
										            	<b-btn :disabled="!location.filter" @click="location.filter = ''">Clear</b-btn>
										            </b-input-group-append>  
									            </b-input-group>
	        								</b-form-group>
	        							</b-col>
	        							<b-col sm="4">
	        								<b-form-group horizontal class="mb-0">
									            <b-input-group>
									              <b-form-select v-model="showactive" :options="location.activeOptions">
									              </b-form-select>
									            </b-input-group>
									        </b-form-group>
	        							</b-col>
									</b-row> -->

									<!-- <b-row v-if="location.totalRows > location.perPage">
										<b-col md="4">
										</b-col>
	      								<b-col md="8" class="my-1">
	        								<b-pagination :total-rows="location.totalRows" :per-page="location.perPage" v-model="location.currentPage" class="my-0" />
	      								</b-col>
    								</b-row> -->