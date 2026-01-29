<template>
  <div class="qeus-app-wrapper">
    <el-row>
      <el-col :span="24">
        <div class="qeus-header"></div>
        <div class="qeus-body">
          <router-view :key="$route.fullPath"></router-view>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script type="text/babel">
import Clipboard from 'clipboard';
import Navigation from './Layouts/Navigation.vue';
// import Submenu from './Layouts/SubMenu.vue';

export default {
  name: 'application',
  components: {
    Navigation,
    // Submenu
  },
  data() {
    return {
      // settings: {
      //   atcpu_settings: {},
      //   quickcart_settings: {}
      // },
      // fetching: false,
      // saving: false
    }
  },
  methods: {
    // getSettings(){
    //   this.fetching = true;
    //   this.$get({
    //     action: "qeus_admin_ajax",
    //     route: "get_settings",
    //     nonce: window.QuickEnquiryUsAdminVars.nonce,
    //   })
    //       .then((response) => {
    //         this.settings = response.data.settings;
    //       })
    //       .fail((error) => {
    //         console.log(error);
    //         this.$handleError(error);
    //       })
    //       .always(() => {
    //         this.fetching = false;
    //       });
    // },
    // saveSettings() {
    //   this.saving = true;
    //   this.$post({
    //     action: "qeus_admin_ajax",
    //     route: "save_settings",
    //     settings: this.settings,
    //     nonce: window.QuickEnquiryUsAdminVars.nonce,
    //   })
    //       .then((response) => {
    //         this.getSettings();
    //         this.$handleSuccess(response.data.message);
    //       })
    //       .fail((error) => {
    //         this.$handleError(error.message);
    //       })
    //       .always(() => {
    //         this.saving = false;
    //       });
    // },
    // updateSettings(section, updatedData) {
    //   this.settings = {
    //     ...this.settings,
    //     [section]: {
    //       ...this.settings[section],
    //       ...updatedData
    //     }
    //   };
    // }
  },
  mounted() {
    // this.getSettings();
    var clipboard = new Clipboard('.copy');
    clipboard.on('success', (e) => {
      this.$message({
        message: 'Copied to Clipboard!',
        type: 'success',
        offset: 50
      });
    });
  }
}
</script>
