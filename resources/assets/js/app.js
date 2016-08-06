var Vue = require('vue');

Vue.use(require('vue-resource'));

import Nodes from './components/nodes.vue';
import ServerForm from './components/server-form.vue';

new Vue({
	el: 'body',

	components: { Nodes, ServerForm }
});
