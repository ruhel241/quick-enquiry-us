import routes from './Bits/routes';
const vueRouter = new window.QEUSPlugin.Router({
    routes: window.QEUSPlugin.applyFilters('qeus_global_routes', routes)
});

window.QEUSPlugin.Vue.prototype.$get  = window.QEUSPlugin.$get;
window.QEUSPlugin.Vue.prototype.$post = window.QEUSPlugin.$post;
window.QEUSPlugin.Vue.prototype.$put  = window.QEUSPlugin.$put;
window.QEUSPlugin.Vue.prototype.$del  = window.QEUSPlugin.$del;

window.QEUSPlugin.Vue.prototype.$bus = new window.QEUSPlugin.Vue();

document.addEventListener('DOMContentLoaded', () => {
    new window.QEUSPlugin.Vue({
        el: '#wp_quick_enquiry_us_settings_app',
        render: h => h(require('./Application').default),
        router: vueRouter,
        mounted() {
            // console.log('Vue App Mounted');
        }
    });
});
