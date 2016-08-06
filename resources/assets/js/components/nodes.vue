<template>
	<h2>Node visibility</h2>

	<p class="help-block">Click on a container type to show/hide this type of node on the map. Coins listed are average earnings per node.</p>

	<input class="form-control" v-model="query" placeholder="Search">

	<div class="text-center">
		<span class="btn btn-default" @click="hideAll()" v-show="hiddenLayers.length < list.length">
            <i class="fa fa-eye-slash"></i>
            Hide all
        </span>
        <span class="btn btn-default" @click="showAll()" v-show="hiddenLayers.length > 0">
            <i class="fa fa-eye"></i>
            Show all
        </span>
	</div>

    <div id='sidebar-nodes'>
	    <div :class="{ 'item-entry': true, 'disabled': hiddenLayers.indexOf(container.id) >= 0 }" :data-key="container.id" :data-price="container.earnings" v-for="container in filteredList" @click="toggle(container.id)">
	        <img :src="container.thumbnail" class='node-icon'>
	        <span class='node-title'>{{ container.name }}</span>
	        <span class='node-title node-earnings'>{{ currency(container.earnings) }}</span>
	    </div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				query: '',
				hiddenLayers: [],
				sortType: 0
			}
		},

		computed: {
			filteredList() {
                var vm = this;

                var earningSort = function (a, b) { return b.earnings - a.earnings; };
                var nameSort = function (a, b) { return a.name.localeCompare(b.name); }

                return this.list.filter(function (container) {
                	return container.name.toLowerCase().indexOf(vm.query.toLowerCase()) >= 0;
                }).sort(this.query.length > 0 ? nameSort : earningSort);
			}
		},

		methods: {
			currency(coin) {
			    var gold = Math.floor(coin / 10000);
			    var silver = Math.floor( ( coin - gold * 10000) / 100);
			    var copper = coin % 100;

			    if (gold > 0) {
			        return gold + "g " + silver + "s " + copper + "c";
			    } else if (silver > 0) {
			        return silver + "s " + copper + "c";
			    } else {
			        return copper + "c";
			    }
			},

			toggle(cid) {
				if (this.hiddenLayers.indexOf(cid) >= 0) {
					this.hiddenLayers.$remove(cid);
				} else {
					this.hiddenLayers.push(cid);
				}
			},

			hideAll() {
				this.hiddenLayers = this.list.map(function (container) { return container.id; });
			},

			showAll() {
				this.hiddenLayers = [];
			}
		},

		watch: {
			hiddenLayers(newVal, oldVal) {
				this.list.forEach(function (container) {
					if (newVal.indexOf(container.id) >= 0) {
						hideLayer(container.id);
					} else {
						showLayer(container.id);
					}
				});

				localStorage.setItem("hiddenNodes", JSON.stringify(newVal));
			},
		},

		ready() {
			this.$http.get('/api/containers').then(function (response) {
				this.list = response.data;
			});

			if (localStorage.getItem("hiddenLayers") !== null) {
				// From historical code
				var localHiddenLayers = JSON.parse(localStorage.hiddenLayers || "{}");
				this.hiddenLayers = Object.keys(localHiddenLayers);
				localStorage.removeItem("hiddenLayers")
			} else {
				// More recently
				var recentHiddenLayers = JSON.parse(localStorage.hiddenNodes || "[]");
				this.hiddenLayers = recentHiddenLayers;
			}

			this.hiddenLayers.forEach(function (layer) {
				hideLayer(layer);
			});
		}
	};
</script>
