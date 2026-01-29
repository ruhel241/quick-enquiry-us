<template>
  <div class="qeus-main-navigation">
    <!-- {{ $getTypeModule('atcpu_module') }} -->
    <div class="logo">
      <h1>QuickView</h1>
    </div>
    <!-- <el-menu
        :router="true"
        mode="horizontal"
        class="qeus-navbar"
        :default-active="active"
        @select="handleSelect"
    >
      <el-menu-item
          v-for="item in visibleItems"
          :key="item.route"
          :index="item.route"
      >
        <i class="icon" v-html="item.icon"></i>
        {{item.title}}
      </el-menu-item>
    </el-menu> -->
  </div>
</template>

<script>
export default {
  name: 'Navigation',
  props: [''],
  data() {
    return {
      // active: localStorage.getItem('qeus_active_menu') || '/',
      // allMenuItems: [],
    };
  },
  computed: {
    // Computed property that reacts to module status changes
    // visibleItems() {
    //   return this.allMenuItems.filter(item => {
    //     if (item.key === 'dashboard' || item.key === 'all_modules' || item.key === 'license_module') {
    //       return true; // Always show these items
    //     }
    //     return this.$getTypeModule(item.key) === 'yes';
    //   });
    // }
  },
  methods: {
    defaultRoutes() {
      return [
        {
          route: '/',
          title: 'Dashboard',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="M19,1H5C2.239,1,0,3.239,0,6V14c0,2.761,2.239,5,5,5h6v2H7c-.553,0-1,.448-1,1s.447,1,1,1h10c.553,0,1-.448,1-1s-.447-1-1-1h-4v-2h6c2.761,0,5-2.239,5-5V6c0-2.761-2.239-5-5-5ZM6.802,15.359c-1.909-.449-3.404-2.058-3.729-3.992-.469-2.791,1.377-5.249,3.927-5.767v4.485c0,.53,.211,1.039,.586,1.414l3.169,3.169c-1.093,.724-2.482,1.036-3.952,.691Zm5.367-2.105l-2.875-2.875c-.188-.188-.293-.442-.293-.707V5.601c2.282,.463,4,2.48,4,4.899,0,1.019-.308,1.964-.832,2.754Zm7.832,1.746h-3c-.552,0-1-.448-1-1s.448-1,1-1h3c.552,0,1,.448,1,1s-.448,1-1,1Zm0-4h-3c-.552,0-1-.448-1-1s.448-1,1-1h3c.552,0,1,.448,1,1s-.448,1-1,1Zm0-4h-3c-.552,0-1-.448-1-1s.448-1,1-1h3c.552,0,1,.448,1,1s-.448,1-1,1Z"/></svg>',
          key: 'dashboard',
        },
        {
          route: '/all-modules',
          title: 'All Modules',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="m21.5,19c-.46,0-.886.133-1.256.35l-3.12-2.269,1.876-1.072V7.991l-1.876-1.072,3.12-2.269c.37.217.796.35,1.256.35,1.381,0,2.5-1.119,2.5-2.5s-1.119-2.5-2.5-2.5-2.5,1.119-2.5,2.5c0,.185.023.364.061.537l-3.841,2.794-3.22-1.84-3.22,1.84-3.841-2.794c.038-.173.061-.352.061-.537,0-1.381-1.119-2.5-2.5-2.5S0,1.119,0,2.5s1.119,2.5,2.5,2.5c.46,0,.886-.133,1.256-.35l3.12,2.269-1.876,1.072v8.018l1.876,1.072-3.12,2.269c-.37-.217-.796-.35-1.256-.35-1.381,0-2.5,1.119-2.5,2.5s1.119,2.5,2.5,2.5,2.5-1.119,2.5-2.5c0-.185-.023-.364-.061-.537l3.841-2.794,3.22,1.84,3.22-1.84,3.841,2.794c-.038.173-.061.352-.061.537,0,1.381,1.119,2.5,2.5,2.5s2.5-1.119,2.5-2.5-1.119-2.5-2.5-2.5ZM7,10.134l4,2.285v4.716l-4-2.286v-4.715Zm6,2.285l4-2.285v4.715l-4,2.286v-4.716Zm2.845-3.928l-3.845,2.196-3.845-2.196,3.845-2.197,3.845,2.197Z"/></svg>',
          key: 'all_modules',
        },
        {
          route: '/add-to-cart-popup',
          title: 'Add To Cart Popup',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Filled" viewBox="0 0 24 24" width="512" height="512"><path d="M18,12a5.993,5.993,0,0,1-5.191-9H4.242L4.2,2.648A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.821-2H17.657a5,5,0,0,0,4.921-4.113l.238-1.319A5.984,5.984,0,0,1,18,12Z"/><circle cx="7" cy="22" r="2"/><circle cx="17" cy="22" r="2"/><path d="M15,7h2V9a1,1,0,0,0,2,0V7h2a1,1,0,0,0,0-2H19V3a1,1,0,0,0-2,0V5H15a1,1,0,0,0,0,2Z"/></svg>',
          key: 'atcpu_module',
        },
        {
          route: '/quick-cart',
          title: 'Quick Cart',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="m22,9h-13c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2h13c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2Zm-13-2h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2ZM22,1h-4c-1.103,0-2,.897-2,2v2c0,1.103.897,2,2,2h4c1.103,0,2-.897,2-2v-2c0-1.103-.897-2-2-2Zm1,17h-15c-1.654,0-3-1.346-3-3V4C5,1.794,3.206,0,1,0,.448,0,0,.447,0,1s.448,1,1,1c1.103,0,2,.897,2,2v11c0,2.045,1.237,3.802,3,4.576v1.424c0,1.654,1.346,3,3,3s3-1.346,3-3v-1h5v1c0,1.654,1.346,3,3,3s3-1.346,3-3v-1c.552,0,1-.447,1-1s-.448-1-1-1Z"/></svg>',
          key: 'quick_cart_module',

        },
        {
          route: '/quick-view',
          title: 'Quick View',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="512" height="512"><path d="M23.271,9.419C21.72,6.893,18.192,2.655,12,2.655S2.28,6.893.729,9.419a4.908,4.908,0,0,0,0,5.162C2.28,17.107,5.808,21.345,12,21.345s9.72-4.238,11.271-6.764A4.908,4.908,0,0,0,23.271,9.419Zm-1.705,4.115C20.234,15.7,17.219,19.345,12,19.345S3.766,15.7,2.434,13.534a2.918,2.918,0,0,1,0-3.068C3.766,8.3,6.781,4.655,12,4.655s8.234,3.641,9.566,5.811A2.918,2.918,0,0,1,21.566,13.534Z"/><path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z"/></svg>',
          key: 'quick_view_module',

        },
        {
          route: '/direct-checkout',
          title: 'Direct Checkout',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="m9,22c0,1.105-.895,2-2,2s-2-.895-2-2,.895-2,2-2,2,.895,2,2Zm7.5-6.5c0,1.379,1.121,2.5,2.5,2.5s2.5-1.121,2.5-2.5-1.121-2.5-2.5-2.5-2.5,1.121-2.5,2.5Zm7.5,6.5v2h-10v-2c0-1.654,1.346-3,3-3h4c1.654,0,3,1.346,3,3Zm-16.222-5c-.507,0-.934-.38-.993-.883l-.131-1.117h7.897c.252-2.244,2.139-4,4.449-4,1.221,0,2.326.492,3.138,1.284l1.857-9.284H5.242l-.041-.351c-.178-1.51-1.459-2.649-2.979-2.649H0v2h2.222c.507,0,.934.38.993.883l1.584,13.468c.178,1.51,1.459,2.649,2.979,2.649h5.247c.507-.67,1.169-1.213,1.944-1.56-.07-.144-.139-.288-.193-.44h-6.998Z"/></svg>',
          key: 'direct_checkout_module',

        },
        {
          route: '/enquire-us',
          title: 'Enquiry US',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="512" height="512"><path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z"/><path d="M12,5a1,1,0,0,0-1,1v8a1,1,0,0,0,2,0V6A1,1,0,0,0,12,5Z"/><rect x="11" y="17" width="2" height="2" rx="1"/></svg>',
          key: 'enquire_us_module',

        },
        {
          route: '/quick-enquiry-us',
          title: 'Quick Enquiry Us',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="512" height="512"><path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z"/><path d="M12,5a1,1,0,0,0-1,1v8a1,1,0,0,0,2,0V6A1,1,0,0,0,12,5Z"/><rect x="11" y="17" width="2" height="2" rx="1"/></svg>',
          key: 'quick_lead_time_module',

        },
        {
          route: '/license',
          title: 'License',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="m19,17.5c0,.827-.673,1.5-1.5,1.5s-1.5-.673-1.5-1.5.673-1.5,1.5-1.5,1.5.673,1.5,1.5Zm-9,4.5c0-1.654-1.346-3-3-3h-.443c.682-1.687,1.443-3.738,1.443-4.5,0-1.381-1.119-2.45-2.5-2.45s-2.5,1.069-2.5,2.45c0,.762.761,2.813,1.443,4.5h-.443c-1.654,0-3,1.346-3,3-.553,0-1,.447-1,1s.447,1,1,1h9c.553,0,1-.447,1-1s-.447-1-1-1ZM24,5v14c0,2.757-2.243,5-5,5h-6.184c.112-.314.184-.648.184-1,0-.979-.472-1.85-1.199-2.397-.389-1.336-1.322-2.443-2.543-3.063.637-1.793.742-2.551.742-3.039,0-2.284-1.756-4.15-4-4.399v-5.101c0-2.757,2.243-5,5-5h8c2.757,0,5,2.243,5,5Zm-14,1c0,.553.447,1,1,1h8c.553,0,1-.447,1-1s-.447-1-1-1h-8c-.553,0-1,.447-1,1Zm7,5c.553,0,1-.447,1-1s-.447-1-1-1h-6c-.553,0-1,.447-1,1s.447,1,1,1h6Zm4,6.5c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5,1.57,3.5,3.5,3.5,3.5-1.57,3.5-3.5Z"/></svg>',
          key: 'license_module',

        },
      ];
    },
    
    // setMenus() {
    //   // Get all menu items with filters applied
    //   this.allMenuItems = this.applyFilters('qeus_top_menus', this.defaultRoutes() );
    // },

    // handleSelect(key) {
    //   this.active = key;
    //   localStorage.setItem('qeus_active_menu', this.active)
    // },
  },
  mounted() {
    // this.setMenus();
    // // Set active menu based on current route
    // this.active = this.$route.path;
  },
  watch: {
    // Keep active state in sync with route changes
    // '$route.path'(newPath) {
    //   this.active = newPath;
    // },
  }
};
</script>