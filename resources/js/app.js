import Vue from "vue";
import VModal from 'vue-js-modal'
import 'vue-js-modal/dist/styles.css'
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

require('./bootstrap');

window.Vue = require('vue').default;

Vue.use(VueToast);
Vue.component('resource-component', require('./components/ResourceComponent.vue').default);
Vue.component('create-resource-component', require('./components/CreateResourceComponent.vue').default);
Vue.component('visitor-component', require('./components/VisitorComponent.vue').default);

Vue.use(VModal)

const app = new Vue({}).$mount("#app");
