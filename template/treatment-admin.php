<section class="content">
	<div id="treatments">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Treatments" active>
								<b-container>
									<b-row align-h="end" class="search-row">
										<b-col sm="12" md="6" lg="4">
											<b-input-group>
												<b-form-input v-model="treatment.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!treatment.filter" @click="treatment.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											</b-input-group>
										</b-col>
										<b-col sm="12" md="4" offset-lg="1" lg="3">
											<b-input-group>
											  <b-form-select v-model="showactive" :options="treatment.activeOptions">
											  </b-form-select>
											</b-input-group>
										</b-col>
									</b-row>
									<b-row>
										<b-table responsive :hover="treatment.hover" :items.sync="treatment.items" :fields="treatment.fields" 
											:sort-by.sync="treatment.sortBy" :current-page="treatment.currentPage" :per-page="treatment.perPage" 
											:filter="treatment.filter" @filtered="onFiltered" :sort-by.sync="treatment.sortBy" :sort-desc.sync="treatment.sortDesc">
											<template slot="detail" slot-scope="row">
												<b-button size="sm" @click.stop="row.toggleDetails" class="mr-1">
													{{ row.detailsShowing ? 'Hide' : 'Details'}} 
												</b-button>
											</template>
											<template slot="edit" slot-scope="row">
												<b-button size="sm" @click.stop="info(row.item, row.index, $event.target)" class="mr-1">
													Edit
												</b-button>
											</template>
											<template slot="row-details" slot-scope="row">
												<b-card>
													<b-row class="mb-2">
														<b-col sm="12"><b>Treatment : </b>{{row.item.name}}</b-col>
														<b-col></b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Description : </b>{{ row.item.description ? row.item.description : 'Null'}}</b-col>
														<b-col></b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="12"><b>Image : </b>{{row.item.imagePath ? '' : 'Null'}}<b-img v-if="row.item.imagePath" :src="row.item.imagePath" fluid :alt="row.item.name"/>
														</b-col>
													</b-row>
											  </b-card>
											</template>
										</b-table>
									</b-row>
									<table-pagination :item="treatment"></table-pagination>
									<b-modal id="modalInfo" @hide="resetModal" :title="treatment.modalInfo.title" size="lg">
										<b-form @submit="onSubmit" id="edit-treatment" class="text-center">
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-code">Code</label>
												</b-col>
												<b-col cols="12" sm="9" lg="3" class="sm-mobile-form-margin-bottom">
													<b-form-input v-model="treatment.code" id="edit-code"></b-form-input>
												</b-col>
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-name">Name</label>
												</b-col>
												<b-col cols="12" sm="9" lg="5">
													<b-form-input v-model="treatment.name" id="edit-name" required>
													</b-form-input>
												</b-col>
											</div>
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-price">Price</label>
												</b-col>
												<b-col cols="12" sm="9" lg="3" class="sm-mobile-form-margin-bottom">
													<b-form-input v-model="treatment.price" id="edit-price" type="number" required></b-form-input>
												</b-col>
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-duration">Duration</label>
												</b-col>
												<b-col cols="12" sm="9" lg="2">
													<b-form-select v-model="treatment.duration" :options="treatment.durationOptions" id="edit-duration"/>
												</b-col>
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-tax">Taxable</label>
												</b-col>
												<b-col cols="12" sm="9" lg="1" class="text-left">
													<b-form-checkbox v-model="treatment.tax" id="edit-tax"></b-form-input>
												</b-col>
											</div>
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-image">Image</label>
												</b-col>
												<b-col cols="12" sm="9" lg="10" class="text-left">
													<b-col cols="12" class="gallery-select" style="margin-bottom: 10px;" v-if="treatment.image_url">
														<img :src="treatment.image_url" class="library-image"/>
														<button type="button" class="image-remove btn btn-sm btn-danger bs" @click="unselectImage(2)" title="Unselect image">
															<i class="fa fa-trash"></i>
														</button>
													</b-col>
													<!-- <b-btn variant="primary" v-on:click="browseLibrary"></b-btn> -->
													<b-button v-b-toggle.collapselibrary variant="primary">Browse library</b-button>
												</b-col>
												<b-col cols="12">
													<b-collapse id="collapselibrary" class="mt-2">
														<div class="form-group library-container">
															<template v-for="(image, index) in addtreatment.images">
																<b-col cols="12" md="6" lg="4" class="gallery-select" @click="selectImage(2, image.src)"
																	:class="addtreatment.image_url == image.src ? 'selected' : ''">
																	<img :src="image.src" class="library-image"/>
																	<small>{{image.size.width}} x {{image.size.height}}</small>
																	<button type="button"  @click.stop="deleteImage(2, image.src)" class="image-remove btn btn-sm btn-danger bs" title="Delete image">
																		<i class="fa fa-trash"></i>
																	</button>
																</b-col>
															</template>
														</div>
														<div class="form-group upload-row">
															<b-col cols="12" offset-lg="2" lg="8">
																<b-input-group>
																	<b-form-file v-model="file" ref="file2" @change="handleFileUpload(2)" 
																		accept="image/jpeg, image/png" placeholder="jpg / png">
																	</b-form-file>
																	<b-input-group-append>
																		<b-btn variant="success" v-on:click="submitFile(2)" :disabled="!file">Upload</b-btn>
																	</b-input-group-append>
																</b-input-group>
															</b-col>
														</div>
														<hr>
													</b-collapse>
												</b-col>
											</div>
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-description">Description</label>
												</b-col>
												<b-col cols="12" sm="9" lg="10">
													<b-form-textarea id="edit-description" v-model="treatment.description" :rows="5"></b-form-textarea>
												</b-col>
											</div>
										</b-form>
										<div slot="modal-footer" class="w-100 mb-4 pb-4 mb-sm-0 pb-sm-0">
											<div class="form-group">
												<b-col sm="4" cols="6" order="1" order-sm="1">
													<b-button :variant="treatment.variant" @click="updateTreatment">{{treatment.enable}}</b-button>
												</b-col>
												<b-col sm="5" cols="12" order="3" order-sm="2">
													<b-alert :show="treatment.dismissCountDown" dismissible :variant="alert" 
														@dismissed="treatment.dismissCountDown=0">
														{{message}}
													</b-alert>
												</b-col>
												<b-col sm="3" cols="6" order="2" order-sm="3" class="text-right">
													<b-button form="edit-treatment" type="submit" variant="primary">Save</b-button>
												</b-col>
											</div>
										</div>
									</b-modal>
								</b-container>
							</b-tab>
							<b-tab title="Add a treatment" >
								<b-form @submit="addTreatment" class="text-center">
									<b-container>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="name">Name*</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="mobile-form-margin-bottom">
												<b-form-input v-model="addtreatment.name" type="text" id="name" required></b-form-input>
											</b-col>
											<b-col cols="12" sm="2">
												<label for="code">Code</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4">
												<b-form-input v-model="addtreatment.code" type="text" id="code"></b-form-input>
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="price" >Price*</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="mobile-form-margin-bottom">
												<b-form-input v-model="addtreatment.price" type="text" id="price" required></b-form-input>
											</b-col>
											<b-col cols="12" sm="2">
												<label for="duration">Duration*</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4">
												<b-form-select v-model="addtreatment.duration" :options="addtreatment.durationOptions" id="duration" required/>
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="tax">Taxable</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="text-left">
												<b-form-checkbox v-model="addtreatment.tax" id="tax"></b-form-input>
											</b-col>
											<b-col cols="12" sm="2">
												<label for="image">Image</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="text-left">
												<b-btn variant="primary" v-on:click="browseLibrary">Browse library</b-btn>
												<b-col cols="12" class="gallery-select" style="margin-top: 10px;" v-if="addtreatment.image_url">
													<img :src="addtreatment.image_url" class="library-image"/>
													<button type="button" class="image-remove btn btn-sm btn-danger bs" @click="unselectImage(1)" title="Unselect image">
														<i class="fa fa-trash"></i>
													</button>
												</b-col>
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col sm="2">
												<label for="description">Description</label>
											</b-col>
											<b-col sm="10">
												<b-form-textarea id="description" v-model="addtreatment.description" :rows="4"></b-form-textarea>
											</b-col>
										</b-row>
										<div class="form-group">
											<b-col>
												<b-button type="submit" variant="primary" class="form-button">Add</b-button>
											</b-col>
										</div>
										<div class="form-group">
											<b-col cols="12" offset-lg="3" lg="6">
												<b-alert :show="addtreatment.dismissCountDown" dismissible :variant="addtreatment.alert" 
													@dismissed="addtreatment.dismissCountDown=0">
													{{addtreatment.message}}
												</b-alert>
											</b-col>
										</div>
									</b-container>
								</b-form>
								<b-modal id="modalLibrary" @hide="resetModal" title="Image Library" :hide-footer="true" size="lg">
									<div class="form-group library-container">
										<template v-for="(image, index) in addtreatment.images">
											<b-col cols="12" md="6" lg="4" class="gallery-select" @click="selectImage(1, image.src)"
												:class="addtreatment.image_url == image.src ? 'selected' : ''">
												<img :src="image.src" class="library-image"/>
												<small>{{image.size.width}} x {{image.size.height}}</small>
												<button type="button"  @click.stop="deleteImage(1, image.src)" class="image-remove btn btn-sm btn-danger bs" title="Delete image">
													<i class="fa fa-trash"></i>
												</button>
											</b-col>
										</template>
									</div>
									<hr>
									<div class="form-group">
										<b-col cols="12" offset-lg="2" lg="8">
											<b-input-group>
											<b-form-file id="image" v-model="file" ref="file" @change="handleFileUpload(1)" 
												accept="image/jpeg, image/png" placeholder="jpg / png">
											</b-form-file>
											<b-input-group-append>
												<b-btn variant="success" v-on:click="submitFile(3)" :disabled="!file">Upload</b-btn>
											</b-input-group-append>
										</b-input-group>
										</b-col>
									</div>
									<div class="form-group">
										<b-col cols="12" offset-lg="2" lg="8">
											<b-alert :show="imageModel.dismissCountDown" dismissible :variant="alert" 
												@dismissed="imageModel.dismissCountDown=0">
												{{message}}
											</b-alert>
										</b-col>
									</div>
								</b-modal>
							</b-tab>
						</b-tabs>
					<b-col>
				</b-row>
			</b-container>
		</template>
	</div>
</section>
<script type="text/x-template" id="add-treatment-temp">
	<b-container>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="name">Name</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.name" type="text" id="name" required></b-form-input>
			</b-col>
			<b-col sm="2">
				<label for="code">Code</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.code" type="text" id="code"></b-form-input>
			</b-col>
		</b-row>
		<b-row align-h="center">
			<b-col sm="2">
				<label for="price" >Price</label>
			</b-col>
			<b-col sm="4">
				<b-form-input v-model="item.price" type="text" id="price" required></b-form-input>
			</b-col>
			<b-col sm="2">
				<label for="duration">Duration</label>
			</b-col>
			<b-col sm="4">
				<b-form-select v-model="item.duration" :options="item.durationOptions" id="duration"/>
			</b-col>	
		</b-row>
	</b-container>
</script>
<script>
loadMenu('Treatments');
axios.post('ajax/ajax-duration.php',config)
			.then(function (response)
			{
				if(response.data.success)
				{
					treatments.addtreatment.durationOptions = response.data.durations;
					treatments.treatment.durationOptions = response.data.durations;
				}
			})
			.catch(function (error) {
			console.log(error);
			});
loadTreatment(1);
loadImageLibrary();
function loadTreatment(filter)
{
	axios.post('ajax/ajax-treatment.php',config)
		.then(function (response)
		{
			if(response.data.success)
			{
				treatments.treatment.items = response.data.treatments;
				treatments.treatment.data = response.data.treatments;
				treatments.filterData(filter);
			}
		})
		.catch(function (error) {
		console.log(error);
		});
}
function loadImageLibrary()
{
	axios.post('lib/image-browser.php?dir=treatment',config)
		.then(function (response)
		{
			treatments.addtreatment.images = response.data.images;
		})
		.catch(function (error) {
		console.log(error);
		});
}
Vue.component('add-treatment', {
  props: ['item'],
  template: '#add-treatment-temp'
});
var treatments = new Vue({
	el: '#treatments',
	data:
	{
		addtreatment:
		{
			name: null,
			code: null,
			price: null,
			description: null,
			duration: null,
			tax: null,
			image_url: null,
			durationOptions: [],
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null,
			images: []
		},
		treatment:
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
						key: 'code',
						sortable: true,
						thStyle: {width:'95px'}
					},
					{
						key: 'name',
						sortable: true
					},
					{
						key: 'price', 
						sortable: true,
						thStyle: {width:'100px'}
					},
					{
						key: 'duration_time',
						label: 'Duration(min)',
						thStyle: {width:'150px'},
						tdClass: 'text-center',
						sortable: true
					}, 
					{
						key: 'tax',
						label: 'Taxable',
						sortable: true,
						thStyle: {width:'100px'},
						formatter: (value) => { return (value == 1) ? 'Yes' : 'No' }
					},
					{
						key: 'active',
						label: 'Status',
						thStyle: {width:'100px'},
						sortable: true,
						formatter: (value) => { return (value == 1) ? 'Enabled' : 'Disabled' }
					},
					{
						key: 'detail',
						label: '',
						sortable: false,
						thStyle: {width:'90px'}
					},
					{
						key: 'edit',
						sortable: false,
						thStyle: {width:'50px'}
					}
				],
			id: null,
			name: null,
			code: null,
			price: null,
			description: null,
			image_url: null,
			durationOptions: [],
			message: null,
			active: true,
			duration: null,
			enable: null,
			variant: null,
			activeOptions: [{value: 'all', text:'All'}, {value: '1', text: 'Active'}, {value: '0', text: 'Inactive'}],
			alert: null,
			dismissSecs: 2,
			dismissCountDown: 0
		},
		imageModel:
		{
			message: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			alert: null
		},
		showactive: 1,
		file: null,
		message: null,
		alert: null
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
				this.addtreatment.dismissCountDown = this.addtreatment.dismissSecs;
			}
			if(section == 2)
			{
				this.treatment.dismissCountDown = this.treatment.dismissSecs;
			}
			if(section == 3)
			{
				this.imageModel.dismissCountDown = this.imageModel.dismissSecs;
			}
		},
		reset()
		{
			this.addtreatment.name = null;
			this.addtreatment.code = null;
			this.addtreatment.price = null;
			this.addtreatment.description = null;
			this.addtreatment.duration = null;
			this.addtreatment.tax = null;
			this.addtreatment.image_url = null;
			this.file = null;
			this.$refs.file.reset();
		},
		addTreatment(evt)
		{
			evt.preventDefault();
			if(this.addtreatment.duration == null)
			{
				treatments.addtreatment.message = 'Please select duration';
				treatments.addtreatment.alert = 'danger';
				treatments.showAlert(1);
			}
			else
			{
				axios.post('forms/form-update-treatment.php?add',
				{
					name: this.addtreatment.name,
					code: this.addtreatment.code,
					price: this.addtreatment.price,
					description: this.addtreatment.description,
					duration: this.addtreatment.duration,
					tax: this.addtreatment.tax,
					image_url: this.addtreatment.image_url,
				}
				,config)
				.then(function (response)
				{
					if(response.data.success)
					{
						treatments.addtreatment.alert = 'success';
						treatments.addtreatment.message = response.data.message;
						treatments.showAlert(1);
						loadTreatment(treatments.showactive);
						setTimeout(function(){
							treatments.reset();
							}, 2000);
					}
					else
					{
						treatments.addtreatment.alert = 'danger';
						treatments.addtreatment.message = response.data.message;
						treatments.showAlert(1);
					}
				})
				.catch(function (error) {
				console.log(error);
				});
			}
			
		},
		handleFileUpload(value)
		{
			if(value == 1)
				this.file = this.$refs.file.files;
			if(value == 2)
				this.file = this.$refs.file2.files;
		},
		submitFile(value)
		{
			// Initialize the form data
			let formData = new FormData();
			let postUrl = 'lib/image-upload.php?dir=treatment';
			// Add the form data we need to submit
				formData.append('file', this.file);
				// postUrl += '?dir=treatment&add';

			// Make the request to the POST /single-file URL
			axios.post( postUrl,
				formData,
				{
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			  }
			).then(function (response){
				if(response.data.success)
				{
					treatments.file = null;
					loadImageLibrary();
					treatments.$refs.file.reset();
					treatments.$refs.file2.reset();
					treatments.alert = 'success';
					treatments.message = response.data.message;
					treatments.showAlert(value);
				}
				else
				{
					treatments.alert = 'danger';
					treatments.essage = response.data.message;
					treatments.showAlert(value);
				}
			})
			.catch(function (error){
			  console.log(error);
			});
		},
		selectImage(value, image)
		{
			if(value == 1)
				this.addtreatment.image_url = image;
			if(value == 2)
				this.treatment.image_url = image;
		},
		deleteImage(value, image)
		{
			axios.post('lib/image-delete.php?dir=treatment',
			{
				image: image
			}
			,config)
			.then(function (response){
				if(response.data.success)
				{
					loadImageLibrary();
					if(image == treatments.addtreatment.image_url)
						treatments.addtreatment.image_url = null;
					if(image == treatments.treatment.image_url)
						treatments.treatment.image_url = null;
				}
				else
				{
					treatments.message = response.data.message;
					treatments.alert = 'danger';
					treatments.showAlert(value);
				}
			})
			.catch(function (error){
				console.log(error);
			});
		},
		unselectImage(value)
		{
			if(value == 1)
				this.addtreatment.image_url = null;
			if(value == 2)
				this.treatment.image_url = null;
		},
		filterData(value)
		{
			let temp = [];
			if(value == 'all')
			{
				this.treatment.items = this.treatment.data;
			}
			if(value == 1)
			{
				for(var i = 0; i != this.treatment.data.length; i++)
				{
					if(this.treatment.data[i].active == 1)
						temp.push(this.treatment.data[i])
				}
				this.treatment.items = temp;
			}
			if(value == 0)
			{
				for(var i = 0; i != this.treatment.data.length; i++)
				{
					if(this.treatment.data[i].active == 0)
						temp.push(this.treatment.data[i])
				}
				this.treatment.items = temp;
			}
		},
		info (item, index, button)
		{
			this.treatment.modalInfo.title = (item.code == null) ? item.name : item.code + ' ' + item.name;
			this.treatment.id = item.id;
			this.treatment.name = item.name;
			this.treatment.code = item.code;
			this.treatment.price = item.price;
			this.treatment.description = item.description;
			this.treatment.duration = item.duration;
			this.treatment.image_url = item.imagePath;
			this.treatment.tax = (item.tax == 1) ? true : false;
			this.treatment.enable = (item.active == 1) ? 'Disable' : 'Enable';
			this.treatment.variant = (item.active == 1) ? 'danger' : 'success';
			this.$root.$emit('bv::show::modal', 'modalInfo', button)
		},
		resetModal()
		{
			this.file = null;
			this.$refs.file2.reset();
			this.treatment.modalInfo.title = this.treatment.name = this.treatment.code = this.treatment.price = this.treatment.description = this.treatment.duration = null;
			this.treatment.tax = this.treatment.enable = this.treatment.variant = this.treatment.image_url = null;
			this.treatment.dismissCountDown = 0;
		},
		onFiltered (filteredItems)
		{
			this.treatment.totalRows = filteredItems.length
			this.treatment.currentPage = 1
		},
		onSubmit (evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-treatment.php?update',
				{
					id: this.treatment.id,
					name: this.treatment.name,
					code: this.treatment.code,
					price: this.treatment.price,
					duration: this.treatment.duration,
					description: this.treatment.description,
					tax: this.treatment.tax,
					image_url: this.treatment.image_url
				},
				config)
			.then(function (response)
			{
				treatments.message = response.data.message;
				if(response.data.success)
				{
					loadTreatment(treatments.showactive);
					treatments.alert = 'success';
				}
				else
				{
					treatments.alert = 'danger';
				}
				treatments.showAlert(2);
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		updateTreatment()
		{
			axios.post('forms/form-update-treatment.php?' + this.treatment.enable,
				{
					id: this.treatment.id
				},
				config)
			.then(function (response)
			{
				treatments.message = response.data.message;
				if(response.data.success)
				{
					loadTreatment(treatments.showactive);
					treatments.alert = 'success';
				}
				else
				{
					treatments.alert = 'danger';
				}
				treatments.showAlert(2);
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		browseLibrary(button)
		{
			this.$root.$emit('bv::show::modal', 'modalLibrary', button)
		}
	}
});
</script>
