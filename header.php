<!DOCTYPE html>
<?php
date_default_timezone_set('America/Toronto');
?>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<title><?=$title;?></title>
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="css/bootstrap-vue.css"/>
		<link href='css/style.css' rel='stylesheet'/>
		<link href='//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'/>
		<!-- <script src="js/bootstrap-vue-2.0.0.js"></script> -->
		<script src="js/vue-ajax-1.0.18.js"></script>
		<script src="js/vue-2.5.17.js"></script>
		<script src="js/polyfill.min.js"></script>
		<script src="js/bootstrap-vue.js"></script>
		<script src="//unpkg.com/axios/dist/axios.min.js"></script>
		<script src="js/axios.min.js"></script>
	</head>
		<body>
			<b-container id="menu-container">
				<template>
					<b-nav pills class="nav-container d-flex justify-content-between">
						<menu-item v-for="item in items" :item="item" :key="item.name"></menu-item>
					</b-nav pills>
				</template>
			</b-container>

<script type="text/x-template" id="menu-item-temp">
	<b-nav-item v-if="item.active == true" :to="item.url" active>{{item.name}}</b-nav-item>
	<b-nav-item v-else :to="item.url">{{item.name}}</b-nav-item>
</script>

<script type="text/x-template" id="form-footer-temp">
	<b-row>
		<b-col sm="4">
		</b-col>
		<b-col sm="6">
			<b-alert :show="item.dismissCountDown" dismissible :variant="item.alert" @dismissed="item.dismissCountDown=0">
				{{item.message}}
			</b-alert>
		</b-col>
		<b-col sm="2" class="text-right">
			<b-button type="submit" variant="primary" class="form-button">{{text}}</b-button>
		</b-col>
	</b-row>
</script>

<script type="text/x-template" id="search-row-temp">
	<b-row v-if="active" align-h="end">
		<b-col sm="4">
			<b-form-group class="mb-0">
				<b-input-group>
					<b-form-input v-model="item.filter" placeholder="Type to Search" ></b-form-input>
					<b-input-group-append>
						<b-btn :disabled="!item.filter" @click="item.filter = ''">Clear</b-btn>
					</b-input-group-append>  
				</b-input-group>
			</b-form-group>
		</b-col>
		<b-col sm="4">
			<b-form-group horizontal class="mb-0">
				<b-input-group>
				  <b-form-select v-model="active" :options="item.activeOptions">
				  </b-form-select>
				</b-input-group>
			</b-form-group>
		</b-col>
	</b-row>

	<b-row v-else align-h="center">
		<b-col sm="4">
			<b-form-group class="mb-0">
				<b-input-group>
					<b-form-input v-model="item.filter" placeholder="Type to Search" ></b-form-input>
					<b-input-group-append>
						<b-btn :disabled="!item.filter" @click="item.filter = ''">Clear</b-btn>
					</b-input-group-append>  
				</b-input-group>
			</b-form-group>
		</b-col>
	</b-row>
</script>

<script type="text/x-template" id="table-pagination-temp">
	<b-row v-if="item.totalRows > item.perPage">
		<b-col md="4">
		</b-col>
		<b-col md="4" class="my-1">
			<b-pagination :total-rows="item.totalRows" :per-page="item.perPage" v-model="item.currentPage" class="my-0" />
		</b-col>
		 <b-col md="4">
		</b-col>
	</b-row>
</script>

<script type="text/x-template" id="alert-box-temp">
	<b-col :sm="col" class="text-left">
		<b-alert :show="item.dismissCountDown" dismissible :variant="item.alert" @dismissed="item.dismissCountDown=0">
		{{item.message}}
		</b-alert>
	</b-col>
</script>

<script>
var config = {
	headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }
	};

Vue.component('menu-item', {
  props: ['item'],
  template: '#menu-item-temp'
});

Vue.component('form-footer', {
  props: ['item', 'text'],
  template: '#form-footer-temp'
});

Vue.component('search-row', {
  props: ['item', 'active'],
  template: '#search-row-temp'
});

Vue.component('table-pagination', {
  props: ['item'],
  template: '#table-pagination-temp'
});

Vue.component('alert-box', {
  props: ['item', 'col'],
  template: '#alert-box-temp'
});

var menu = new Vue({
	el: '#menu-container',
	data:
	{
		items:[],
	}
});

function loadMenu(currentPage)
{
	axios.post('forms/form-login.php?currentPage=' + currentPage, config)
			.then(function (response)
			{
				if(!response.data.success)
				{
					window.location = 'login.php';
				}
				else
				{
					menu.items = response.data.items;
					menu.items.push({name:'Logout', url: 'login.php', active: ''});
				}
			})
			.catch(function (error) {
			console.log(error);
			});
}

</script>

