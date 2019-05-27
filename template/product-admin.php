<section class="content">
	<div id="products">
		<template>
			<b-container>
				<b-row>
					<b-col>
						<b-tabs>
							<b-tab title="Products" active>
								<b-container>
									<b-row align-h="end" class="search-row">
										<b-col sm="12" md="6" lg="4">
											<b-input-group>
												<b-form-input v-model="product.filter" placeholder="Search" ></b-form-input>
												<b-input-group-append>
													<b-btn :disabled="!product.filter" @click="product.filter = ''">Clear</b-btn>
												</b-input-group-append>  
											</b-input-group>
										</b-col>
										<b-col sm="12" md="6" offset-lg="1" lg="3">
											<b-input-group>
											  <b-form-select v-model="showactive" :options="product.activeOptions">
											  </b-form-select>
											</b-input-group>
										</b-col>
									</b-row>
									<b-row>
										<b-table responsive :hover="product.hover" :items.sync="product.items" :fields="product.fields" :sort-by.sync="product.sortBy" 
											:current-page="product.currentPage" :per-page="product.perPage" :filter="product.filter" @filtered="onFiltered" 
											:sort-by.sync="product.sortBy" :sort-desc.sync="product.sortDesc">
											<!-- <template slot="detail" slot-scope="row">
												<b-button size="sm" @click.stop="row.toggleDetails" class="mr-1">
													{{ row.detailsShowing ? 'Hide' : 'Details'}} 
												</b-button>
											</template> -->
											<template slot="edit" slot-scope="row">
												<b-button size="sm" @click.stop="info(row.item, row.index, $event.target)" class="mr-1">
													Edit
												</b-button>
											</template>
											<!-- <template slot="row-details" slot-scope="row">
												<b-card>
													<b-row class="mb-2">
														<b-col sm="3" class="text-sm-right"><b>Treatment:</b></b-col>
														<b-col>{{row.item.name}}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="3" class="text-sm-right"><b>Description:</b></b-col>
														<b-col>{{ row.item.description ? row.item.description : 'Null'}}</b-col>
													</b-row>
													<b-row class="mb-2">
														<b-col sm="3" class="text-sm-right"><b>Image:</b></b-col>
														<b-col>
															{{row.item.imagePath ? '' : 'Null'}}
															<b-img v-if="row.item.imagePath" :src="row.item.imagePath" fluid :alt="row.item.name"/>
														</b-col>
													</b-row>
											  </b-card>
											</template> -->
										</b-table>
									</b-row>
									<table-pagination :item="product"></table-pagination>
									<b-modal id="modalInfo" @hide="resetModal" :title="product.modalInfo.title" size="lg">
										<b-form @submit="onSubmit" id="edit-product">
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-code">Code</label>
												</b-col>
												<b-col cols="12" sm="9" lg="3" class="sm-mobile-form-margin-bottom">
													<b-form-input v-model="product.code" id="edit-code"></b-form-input>
												</b-col>
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-name" class="col-sm-3">Name</label>
												</b-col>
												<b-col cols="12" sm="9" lg="5">
													<b-form-input v-model="product.name" id="edit-name" required></b-form-input>
												</b-col>
											</div>
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-tax">Taxable</label>
												</b-col>
												<b-col cols="12" sm="9" lg="3" class="sm-mobile-form-margin-bottom text-left">
													<b-form-checkbox v-model="product.tax" id="edit-tax"></b-form-input>
												</b-col>
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-price" class="col-sm-3">Price</label>
												</b-col>
												<b-col cols="12" sm="9" lg="5">
													<b-form-input v-model="product.price" id="edit-price" type="number" required></b-form-input>
												</b-col>
											</div>
											<div class="form-group">
												<b-col cols="12" sm="3" lg="2">
													<label for="edit-image">Image</label>
												</b-col>
												<b-col cols="12" sm="9" lg="10" class="text-left">
													<b-col cols="12" class="gallery-select" style="margin-bottom: 10px;" v-if="product.image_url">
														<img :src="product.image_url" class="library-image"/>
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
															<template v-for="(image, index) in addproduct.images">
																<b-col cols="12" md="6" lg="4" class="gallery-select" @click="selectImage(2, image.src)"
																	:class="addproduct.image_url == image.src ? 'selected' : ''">
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
												<label for="edit-description" >Description</label>
												</b-col>
												<b-col cols="12" sm="9" lg="10">
													<b-form-textarea id="edit-description" v-model="product.description" :rows="5"></b-form-textarea>
												</b-col>
											</div>
										</b-form>
										<div slot="modal-footer" class="w-100 mb-4 pb-4 mb-sm-0 pb-sm-0">
											<div class="form-group">
												<b-col sm="4" cols="6" order="1" order-sm="1">
													<b-button :variant="product.variant" @click="updateproduct">{{product.enable}}</b-button>
												</b-col>
												<b-col sm="5" cols="12" order="3" order-sm="2">
													<b-alert :show="product.dismissCountDown" dismissible :variant="alert" 
														@dismissed="product.dismissCountDown=0">
														{{message}}
													</b-alert>
												</b-col>
												<b-col sm="3"cols="6" order="2" order-sm="3" class="text-right">
													<b-button form="edit-product" type="submit" variant="primary">Save</b-button>
												</b-col>
											</div>
										</div>
									</b-modal>
								</b-container>
							</b-tab>
							<b-tab title="Add a product" >
								<b-form @submit="addProduct" class="text-center">
									<b-container>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="name">Name*</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="mobile-form-margin-bottom">
												<b-form-input v-model="addproduct.name" type="text" id="name" required></b-form-input>
											</b-col>
											<b-col cols="12" sm="2">
												<label for="code">Code</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4">
												<b-form-input v-model="addproduct.code" type="text" id="code"></b-form-input>
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="price" >Price*</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4">
												<b-form-input v-model="addproduct.price" type="text" id="price" required></b-form-input>
											</b-col>
											<b-col cols="12" sm="2">
												<label for="tax">Taxable</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="text-left">
												<b-form-checkbox v-model="addproduct.tax" id="tax"></b-form-input>
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label>Image</label>
											</b-col>
											<b-col cols="12" sm="10" lg="4" class="text-left">
												<b-btn variant="primary" v-on:click="browseLibrary">Browse library</b-btn>
												<b-col cols="12" class="gallery-select" style="margin-top: 10px;" v-if="addproduct.image_url">
													<img :src="addproduct.image_url" class="library-image"/>
													<button type="button" class="image-remove btn btn-sm btn-danger bs" @click="unselectImage(1)" title="Unselect image">
														<i class="fa fa-trash"></i>
													</button>
												</b-col>
											</b-col>
											<b-col cols="12" sm="2">
											</b-col>
											<b-col sm="4">
											</b-col>
										</b-row>
										<b-row align-h="center" class="search-row">
											<b-col cols="12" sm="2">
												<label for="description">Description</label>
											</b-col>
											<b-col cols="12" sm="10">
												<b-form-textarea id="description" v-model="addproduct.description" :rows="3"></b-form-textarea>
											</b-col>
										</b-row>
										<div class="form-group">
											<b-col>
												<b-button type="submit" variant="primary" class="form-button">Add</b-button>
											</b-col>
										</div>
										<div class="form-group">
											<b-col>
												<b-alert :show="addproduct.dismissCountDown" dismissible :variant="alert" 
													@dismissed="addproduct.dismissCountDown=0">
													{{message}}
												</b-alert>
											</b-col>
										</div>
									</b-container>
								</b-form>
								<b-modal id="modalLibrary" @hide="resetModal" title="Image Library" :hide-footer="true" size="lg">
									<div class="form-group library-container">
										<template v-for="(image, index) in addproduct.images">
											<b-col cols="12" md="6" lg="4" class="gallery-select" @click="selectImage(1, image.src)"
												:class="addproduct.image_url == image.src ? 'selected' : ''">
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
<script>
loadMenu('Products');
loadProduct(1);
loadImageLibrary();
function loadProduct(filter)
{
	axios.post('ajax/ajax-product.php',config)
		.then(function (response)
		{
			if(response.data.success)
			{
				products.product.items = response.data.products;
				products.product.data = response.data.products;
				products.filterData(filter);
			}
		})
		.catch(function (error) {
		console.log(error);
		});
}
function loadImageLibrary()
{
	axios.post('lib/image-browser.php?dir=product',config)
		.then(function (response)
		{
			products.addproduct.images = response.data.images;
		})
		.catch(function (error) {
		console.log(error);
		});
}
var products = new Vue({
	el: '#products',
	data: 
	{
		addproduct:
		{
			name: null,
			code: null,
			price: null,
			tax: null,
			description: null,
			message: null,
			alert: null,
			dismissSecs: 2,
			dismissCountDown: 0,
			image_url: null,
			images: []
		},
		product:
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
						thStyle: {width:'90px'}
					},
					{
						key: 'name',
						thStyle: {width:'300px'},
						sortable: true
					},
					{
						key: 'price', 
						sortable: true,
						thStyle: {width:'100px'}
					},
					{
						key: 'description',
						// tdClass: 'text-center',
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
					// {
					// 	key: 'detail',
					// 	label: '',
					// 	sortable: false,
					// 	thStyle: {width:'90px'}
					// },
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
			file: null,
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
				this.addproduct.dismissCountDown = this.addproduct.dismissSecs;
			}
			if(section == 2)
			{
				this.product.dismissCountDown = this.product.dismissSecs;
			}
			if(section == 3)
			{
				this.imageModel.dismissCountDown = this.imageModel.dismissSecs;
			}
		},
		reset()
		{
			this.addproduct.name = null;
			this.addproduct.code = null;
			this.addproduct.price = null;
			this.addproduct.description = null;
			this.addproduct.duration = null;
			this.addproduct.tax = null;
			this.addproduct.file = null;
			this.addproduct.image_url = null;
			this.addproduct.image_url = null;
			this.file = null;
			this.$refs.file.reset();
		},
		 onFiltered (filteredItems)
		{
			this.product.totalRows = filteredItems.length
			this.product.currentPage = 1
		},
		 filterData(value)
		{
			let temp = [];
			if(value == 'all')
			{
				this.product.items = this.product.data;
			}
			if(value == 1)
			{
				for(var i = 0; i != this.product.data.length; i++)
				{
					if(this.product.data[i].active == 1)
						temp.push(this.product.data[i])
				}
				this.product.items = temp;
			}
			if(value == 0)
			{
				for(var i = 0; i != this.product.data.length; i++)
				{
					if(this.product.data[i].active == 0)
						temp.push(this.product.data[i])
				}
				this.product.items = temp;
			}
		},
		info (item, index, button)
		{
			this.product.modalInfo.title = (item.code == null) ? item.name : item.code + ' ' + item.name;
			this.product.id = item.id;
			this.product.name = item.name;
			this.product.code = item.code;
			this.product.price = item.price;
			this.product.description = item.description;
			this.product.image_url = item.imagePath;
			this.product.file = null;
			this.product.tax = (item.tax == 1) ? true : false;
			this.product.enable = (item.active == 1) ? 'Disable' : 'Enable';
			this.product.variant = (item.active == 1) ? 'danger' : 'success';
			this.$root.$emit('bv::show::modal', 'modalInfo', button)
		},
		resetModal ()
		{
			this.product.modalInfo.title = this.product.name = this.product.code = this.product.price = this.product.description = this.product.duration = null;
			this.product.tax = this.product.enable = this.product.variant = this.product.image_url = null;
			this.product.dismissCountDown = 0;
			this.product.file = null;
			this.$refs.file2.reset();
		},
		onSubmit (evt) //update product
		{
			evt.preventDefault();
			axios.post('forms/form-update-product.php?update',
				{
					id: this.product.id,
					name: this.product.name,
					code: this.product.code,
					price: this.product.price,
					description: this.product.description,
					tax: this.product.tax,
					image_url: this.product.image_url
				},
				config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					products.message = response.data.message;
					products.alert = 'success';
					products.showAlert(2);
					loadProduct(products.showactive);
				}
				else
				{
					products.message = response.data.message;
					products.alert = 'danger';
					products.showAlert(2);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		updateproduct() //enable or disable product
		{
			axios.post('forms/form-update-product.php?' + this.product.enable,
				{
					id: this.product.id
				},
				config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					products.product.message = response.data.message;
					products.product.alert = 'success';
					products.showAlert(1);
					loadProduct(products.showactive);
				}
				else
				{
					products.product.message = response.data.message;
					products.product.alert = 'danger';
					products.showAlert(1);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
		},
		addProduct(evt)
		{
			evt.preventDefault();
			axios.post('forms/form-update-product.php?addproduct',
				{
					name: this.addproduct.name,
					code: this.addproduct.code,
					price: this.addproduct.price,
					description: this.addproduct.description,
					tax: this.addproduct.tax,
					image_url: this.addproduct.image_url
				},
				config)
			.then(function (response)
			{
				console.log(response.data);
				if(response.data.success)
				{
					products.message = response.data.message;
					products.alert = 'success';
					products.showAlert(1);
					loadProduct(products.showactive);
					setTimeout(function(){
						products.reset();
						}, 2000);
				}
				else
				{
					products.message = response.data.message;
					products.alert = 'danger';
					products.showAlert(1);
				}
			})
			.catch(function (error) {
			console.log(error);
			});
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
			let postUrl = 'lib/image-upload.php?dir=product';
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
					products.file = null;
					loadImageLibrary();
					products.$refs.file.reset();
					// products.$refs.file2.reset();
					products.alert = 'success';
					products.message = response.data.message;
					products.showAlert(value);
				}
				else
				{
					products.alert = 'danger';
					products.essage = response.data.message;
					products.showAlert(value);
				}
			})
			.catch(function (error){
			  console.log(error);
			});
		},
		selectImage(value, image)
		{
			if(value == 1)
				this.addproduct.image_url = image;
			if(value == 2)
				this.product.image_url = image;
		},
		deleteImage(value, image)
		{
			axios.post('lib/image-delete.php?dir=product',
			{
				image: image
			}
			,config)
			.then(function (response){
				if(response.data.success)
				{
					loadImageLibrary();
					if(image == products.addproduct.image_url)
						products.addproduct.image_url = null;
					if(image == products.product.image_url)
						products.product.image_url = null;
				}
				else
				{
					products.message = response.data.message;
					products.alert = 'danger';
					products.showAlert(value);
				}
			})
			.catch(function (error){
				console.log(error);
			});
		},
		unselectImage(value)
		{
			if(value == 1)
				this.addproduct.image_url = null;
			if(value == 2)
				this.product.image_url = null;
		},
		browseLibrary(button)
		{
			this.$root.$emit('bv::show::modal', 'modalLibrary', button)
		}
	}
});
</script>