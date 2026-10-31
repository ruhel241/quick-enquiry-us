import routes from './Bits/routes';
const vueRouter = new window.QPEFWPlugin.Router({
    routes: window.QPEFWPlugin.applyFilters('qpefw_global_routes', routes)
});

window.QPEFWPlugin.Vue.prototype.$get  = window.QPEFWPlugin.$get;
window.QPEFWPlugin.Vue.prototype.$post = window.QPEFWPlugin.$post;
window.QPEFWPlugin.Vue.prototype.$put  = window.QPEFWPlugin.$put;
window.QPEFWPlugin.Vue.prototype.$del  = window.QPEFWPlugin.$del;

window.QPEFWPlugin.Vue.prototype.$bus = new window.QPEFWPlugin.Vue();

document.addEventListener('DOMContentLoaded', () => {
    new window.QPEFWPlugin.Vue({
        el: '#wp_qpefw_settings_app',
        render: h => h(require('./Application').default),
        router: vueRouter,
        mounted() {
            // console.log('Vue App Mounted');
        }
    });
});
