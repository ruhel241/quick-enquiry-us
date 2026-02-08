<template>
  <div class="qeus-main-navigation">
    <div class="logo">
      <h1>Quick Enquiry Us</h1>
    </div>
    <el-menu
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
    </el-menu>
  </div>
</template>

<script>
export default {
  name: 'Navigation',
  props: [''],
  data() {
    return {
      active: localStorage.getItem('qeus_active_menu') || '/',
      allMenuItems: [],
    };
  },
  computed: {
    // Computed property that reacts to module status changes
    visibleItems() {
      return this.allMenuItems.filter(item => {
        return item.key;
        if (item.key === 'dashboard' || item.key === 'license_module') {
          return true; // Always show these items
        }
        // return this.$getTypeModule(item.key) === 'yes';
      });
    }
  },
  methods: {
    defaultRoutes() {
      return [
        {
          route: '/',
          title: 'Quick Enquiry Us',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="512" height="512"><path d="M23.271,9.419C21.72,6.893,18.192,2.655,12,2.655S2.28,6.893.729,9.419a4.908,4.908,0,0,0,0,5.162C2.28,17.107,5.808,21.345,12,21.345s9.72-4.238,11.271-6.764A4.908,4.908,0,0,0,23.271,9.419Zm-1.705,4.115C20.234,15.7,17.219,19.345,12,19.345S3.766,15.7,2.434,13.534a2.918,2.918,0,0,1,0-3.068C3.766,8.3,6.781,4.655,12,4.655s8.234,3.641,9.566,5.811A2.918,2.918,0,0,1,21.566,13.534Z"/><path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z"/></svg>',
          key: 'dashboard',
        },
        {
          route: '/license-management',
          title: 'License',
          icon: '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"><path d="m19,17.5c0,.827-.673,1.5-1.5,1.5s-1.5-.673-1.5-1.5.673-1.5,1.5-1.5,1.5.673,1.5,1.5Zm-9,4.5c0-1.654-1.346-3-3-3h-.443c.682-1.687,1.443-3.738,1.443-4.5,0-1.381-1.119-2.45-2.5-2.45s-2.5,1.069-2.5,2.45c0,.762.761,2.813,1.443,4.5h-.443c-1.654,0-3,1.346-3,3-.553,0-1,.447-1,1s.447,1,1,1h9c.553,0,1-.447,1-1s-.447-1-1-1ZM24,5v14c0,2.757-2.243,5-5,5h-6.184c.112-.314.184-.648.184-1,0-.979-.472-1.85-1.199-2.397-.389-1.336-1.322-2.443-2.543-3.063.637-1.793.742-2.551.742-3.039,0-2.284-1.756-4.15-4-4.399v-5.101c0-2.757,2.243-5,5-5h8c2.757,0,5,2.243,5,5Zm-14,1c0,.553.447,1,1,1h8c.553,0,1-.447,1-1s-.447-1-1-1h-8c-.553,0-1,.447-1,1Zm7,5c.553,0,1-.447,1-1s-.447-1-1-1h-6c-.553,0-1,.447-1,1s.447,1,1,1h6Zm4,6.5c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5,1.57,3.5,3.5,3.5,3.5-1.57,3.5-3.5Z"/></svg>',
          key: 'license_module',

        },
        {
          route: '/documentation',
          title: 'Documentation',
          icon: '<svg viewBox="0 -0.5 17 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="si-glyph si-glyph-document"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <title>1083</title> <defs> </defs> <g stroke="none" stroke-width="1" fill-rule="evenodd"> <g transform="translate(2.000000, 0.000000)"> <path d="M0,0 L0,16 L13,16 L13,4.02441406 L7,4 L7,0.062 L0,0 Z M11,13 L2,13 L2,12 L11,12 L11,13 L11,13 Z M11,11 L2,11 L2,10 L11,10 L11,11 L11,11 Z M11,7 L11,8 L2,8 L2,7 L11,7 L11,7 Z" class="si-glyph-fill"> </path> <path d="M8,0 L8,2.844 L12.752,2.844 L8,0 Z" class="si-glyph-fill"> </path> </g> </g> </g></svg>',
          key: 'documentation',
        },
        {
          route: '/support',
          title: 'Support',
          icon: '<svg viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1"  fill-rule="evenodd"><g id="support" transform="translate(42.666667, 42.666667)"><path d="M379.734355,174.506667 C373.121022,106.666667 333.014355,-2.13162821e-14 209.067688,-2.13162821e-14 C85.1210217,-2.13162821e-14 45.014355,106.666667 38.4010217,174.506667 C15.2012632,183.311569 -0.101643453,205.585799 0.000508304259,230.4 L0.000508304259,260.266667 C0.000508304259,293.256475 26.7445463,320 59.734355,320 C92.7241638,320 119.467688,293.256475 119.467688,260.266667 L119.467688,230.4 C119.360431,206.121456 104.619564,184.304973 82.134355,175.146667 C86.4010217,135.893333 107.307688,42.6666667 209.067688,42.6666667 C310.827688,42.6666667 331.521022,135.893333 335.787688,175.146667 C313.347976,184.324806 298.68156,206.155851 298.667688,230.4 L298.667688,260.266667 C298.760356,283.199651 311.928618,304.070103 332.587688,314.026667 C323.627688,330.88 300.801022,353.706667 244.694355,360.533333 C233.478863,343.50282 211.780225,336.789048 192.906491,344.509658 C174.032757,352.230268 163.260418,372.226826 167.196286,392.235189 C171.132153,412.243552 188.675885,426.666667 209.067688,426.666667 C225.181549,426.577424 239.870491,417.417465 247.041022,402.986667 C338.561022,392.533333 367.787688,345.386667 376.961022,317.653333 C401.778455,309.61433 418.468885,286.351502 418.134355,260.266667 L418.134355,230.4 C418.23702,205.585799 402.934114,183.311569 379.734355,174.506667 Z M76.8010217,260.266667 C76.8010217,269.692326 69.1600148,277.333333 59.734355,277.333333 C50.3086953,277.333333 42.6676884,269.692326 42.6676884,260.266667 L42.6676884,230.4 C42.6676884,224.302667 45.9205765,218.668499 51.2010216,215.619833 C56.4814667,212.571166 62.9872434,212.571166 68.2676885,215.619833 C73.5481336,218.668499 76.8010217,224.302667 76.8010217,230.4 L76.8010217,260.266667 Z M341.334355,230.4 C341.334355,220.97434 348.975362,213.333333 358.401022,213.333333 C367.826681,213.333333 375.467688,220.97434 375.467688,230.4 L375.467688,260.266667 C375.467688,269.692326 367.826681,277.333333 358.401022,277.333333 C348.975362,277.333333 341.334355,269.692326 341.334355,260.266667 L341.334355,230.4 Z"></path></g></g></svg>',
          key: 'support',
        },
      ];
    },
    
    setMenus() {
      // Get all menu items with filters applied
      this.allMenuItems = this.applyFilters('qeus_top_menus', this.defaultRoutes() );
    },

    handleSelect(key) {
      this.active = key;
      localStorage.setItem('qeus_active_menu', this.active)
    },
  },
  mounted() {
    this.setMenus();
    // Set active menu based on current route
    this.active = this.$route.path;
  },
  watch: {
    // Keep active state in sync with route changes
    '$route.path'(newPath) {
      this.active = newPath;
    },
  }
};
</script>