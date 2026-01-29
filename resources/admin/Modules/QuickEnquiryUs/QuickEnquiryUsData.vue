<template>
  <div class="qeus-settings-wrapper" v-loading="loading" element-loading-spinner="el-icon-loading">
    <div class="qeus-settings-content">
      <template>
        <div class="qeus-header">
          <el-input
            placeholder="Search"
            v-model="search"
            @change="searchHandler"
            clearable>
            <el-button slot="append" icon="el-icon-search"></el-button>
          </el-input>
        </div>
        <!-- {{ tableData }} -->
        <table-selections
          @reloadData="getQuickEnquiryUs"
          :selections="multipleSelection"
        />
          <el-table
            ref="multipleTable"
            :data="tableData"
            style="width: 100%"
            border
            @selection-change="handleSelectionChange">
            <el-table-column
              type="selection">
            </el-table-column>
            <el-table-column
              label="Name"
              width="150">
              <template slot-scope="scope">{{ scope.row.name }}</template>
            </el-table-column>
            <el-table-column
              label="Email"
              width="180">
              <template slot-scope="scope">{{ scope.row.email }}</template>
            </el-table-column>
            <el-table-column
              label="Phone Number"
              width="150">
              <template slot-scope="scope">{{ scope.row.phone_number }}</template>
            </el-table-column>
            <el-table-column
              label="Enquiry"
              width="250"
              >
              <template slot-scope="scope">{{ textTrim(scope.row.enquiry) }}</template>
            </el-table-column>
            <el-table-column 
              label="Product Title"
              width="150">
              <template slot-scope="scope">
                 <span v-html="getProductHtml(scope.row)"></span>
              </template>
            </el-table-column>
            <el-table-column 
              label="Type"
              width="150">
              <template slot-scope="scope">{{ textTrim(scope.row.type) }}</template>
            </el-table-column>
            <el-table-column 
              label="Status"
              width="150">
              <template slot-scope="scope">{{ textTrim(scope.row.status) }}</template>
            </el-table-column>
             <el-table-column 
              label="subscribe"
              width="150">
              <template slot-scope="scope">{{ textTrim(scope.row.subscribe) }}</template>
            </el-table-column>
            <el-table-column
              label="Date"
              width="150">
              <template slot-scope="scope">
                {{
                  new Date(scope.row.created_at).toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                  })
                }}
              </template>
            </el-table-column>
            <el-table-column
              label="Actions"
              width="150">
              <template slot-scope="scope">
                <el-button class="qeus-btn" type="primary" icon="el-icon-view" circle @click="confirmView(scope.row.id)"></el-button>
                <el-button 
                  class="qeus-btn"
                  type="danger" 
                  icon="el-icon-delete" 
                  @click="handleDeleteModal(scope.row.id)"
                  circle>
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        <table-selections
            @reloadData="getQuickEnquiryUs"
            :selections="multipleSelection"
        />
        <div class="qeus_pagination_wrap">
            <el-pagination
                :hide-on-single-page="false"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page.sync="paginate.current_page"
                :page-sizes="[5, 10, 20, 50, 100]"
                :page-size="paginate.per_page"
                layout="total, sizes, prev, pager, next"
                :total="paginate.total">
            </el-pagination>
        </div>
        <!-- Confirmation Dialog -->
        <el-dialog
          :visible.sync="showDisableDialog"
          width="40%"
          :close-on-click-modal="true"
          :close-on-press-escape="true"
          :show-close="true"
          custom-class="qeus-module-disable-dialog"
          center>
          <div class="qeus-module-dialog-content">
            <div class="dialog-text">
              <h3>Quick Enquiry US Details</h3>
              <el-row :gutter="14" class="detail-row">
                <template v-if="detail.name">
                  <el-col :span="8" class="label">Name:</el-col>
                  <el-col :span="16" class="value">{{ detail.name }}</el-col>
                </template>
               
                <template v-if="detail.email">
                  <el-col :span="8" class="label">Email:</el-col>
                  <el-col :span="16" class="value">{{ detail.email }}</el-col>
                </template>

                <template v-if="detail.enquiry">
                  <el-col :span="8" class="label">Enquiry:</el-col>
                  <el-col :span="16" class="value">{{ detail.enquiry }}</el-col>
                </template>

                <template v-if="detail.phone_number">
                  <el-col :span="8" class="label">Phone Number:</el-col>
                  <el-col :span="16" class="value">{{ detail.phone_number }}</el-col>
                </template>

                <template v-if="detail.type">
                  <el-col :span="8" class="label">Type:</el-col>
                  <el-col :span="16" class="value">{{ detail.type }}</el-col>
                </template>

                <template v-if="detail.status">
                  <el-col :span="8" class="label">Status:</el-col>
                  <el-col :span="16" class="value">{{ detail.status }}</el-col>
                </template>

                <template v-if="detail.subscribe">
                  <el-col :span="8" class="label">Subscribe:</el-col>
                  <el-col :span="16" class="value">{{ detail.subscribe }}</el-col>
                </template>

                <template v-if="hasProduct(detail)">
                  <el-col :span="8" class="label">Product Title:</el-col>
                  <el-col :span="16" class="value">
                    <span v-html="getProductHtml(detail)"></span>
                  </el-col>
                </template>

              </el-row>
            </div>
          </div>
        </el-dialog>
      </template>
    </div>
  </div>
</template>

<script type="text/babel">
// import { Loading } from 'element-ui';
import TableSelections from './_TableSelectionHandler.vue';
  export default {
    name: 'quick-enquiry-us-data',
    props: ['settings', 'fetching'],
    components: {
      TableSelections,
    },
    data() {
      return {
        //fetching: false,
        loading: false,
        search: "",
        paginate: {
          total: 0,
          current_page: +(localStorage.getItem('quickEnquiryUsDetailsCurrentPage') || 1),
          last_page: 1,
          per_page: +(localStorage.getItem('quickEnquiryUsPerPage') || 10)
        },
        tableData: [],
        multipleSelection: [],
        showDisableDialog: false,
        detail: {
          name: ''
        },
        adminUrl: window.QuickEnquiryUsAdminVars.admin_url
      }
    },
    methods: {
      hasProduct(row) {
        if (!row || !row.settings) return false;

        try {
          const obj = JSON.parse(row.settings);
          return !!obj.title && !!obj.product_url;
        } catch (e) {
          return false;
        }
      },

      getProductHtml(row) {
        if (!row || !row.settings) return '';
        try {
          const obj = JSON.parse(row.settings);
          return `
            <a href="${obj.product_url}"
              target="_blank"
              class="qeus-product-title">
              ${this.textTrim(obj.title)}
            </a>`;
        } catch (e) {
          return '';
        }
      },

      textTrim(val) {
        if (!val) return '';
        const words = val.split(' ');
        return words.slice(0, 10).join(' ') + (words.length > 10 ? '...' : '');
      },
      
      handleSizeChange(val) {
          localStorage.setItem('quickEnquiryUsPerPage', val);
          this.paginate.per_page = val;
          this.getQuickEnquiryUs();
      },
      handleCurrentChange(val) {
          localStorage.setItem('quickEnquiryUsDetailsCurrentPage', val);
          this.paginate.current_page = val
          this.getQuickEnquiryUs();
      },
      confirmView(id) {
        this.showDisableDialog = true;
        // this.loading = true;
        this.$get({
          action: "qeus_admin_ajax",
          route: "get_quick_enquiry_us_detail_view",
          detail_id: id,
          nonce: window.QuickEnquiryUsAdminVars.nonce,
        })
            .then((response) => {
              this.detail = response.data.detail;
            })
            .fail((error) => {
              this.$handleError(error);
            })
            .always(() => {
              // setTimeout(() => { this.loading = false; }, 500);
            });
      },
      cancelDisable() {
        this.showDisableDialog = false;
      },
      confirmDisable() {
        this.showDisableDialog = false;
      },
      handleSelectionChange(val) {
        this.multipleSelection = val;
      },

      searchHandler(val) {
        this.search = val;
        this.getQuickEnquiryUs();
      },

      getQuickEnquiryUs() {
        this.loading = true;
        this.$get({
          action: "qeus_admin_ajax",
          route: "get_quick_enquiry_us_details",
          search: this.search,
          current_page: this.paginate.current_page,
          per_page: this.paginate.per_page,
          nonce: window.QuickEnquiryUsAdminVars.nonce,
        })
            .then((response) => {
              this.tableData = response.data.data.details;
              // console.log(this.tableData);
              this.paginate.total = response.data.data.total;
              this.paginate.current_page = response.data.data.current_page;
              this.paginate.last_page = response.data.data.last_page;
            })
            .fail((error) => {
              this.$handleError(error);
            })
            .always(() => {
              setTimeout(() => { this.loading = false; }, 500);
            });
      },

      handleDeleteModal(id) {
        this.$confirm("Do you want delete this enquire us permanently", "Warning", {
          confirmButtonText: "Delete",
          cancelButtonText: "Cancel",
          type: "warning",
        })
          .then(() => {
            this.performAction("delete", id);
          })
          .catch(() => {
            this.$message({
              type: "info",
              offset: 90,
              showClose: true,
              message: "Delete canceled",
            });
          });
      },
      performAction(type, id) {
        this.$post({
          action: "qeus_admin_ajax",
          route: "maybe_delete_details",
          details_ids: [id],
          action_type: type,
          nonce: window.QuickEnquiryUsAdminVars.nonce,
        })
          .then((response) => {
            this.$notify({
              title: "Sucess",
              message: response.data.message,
              type: "success",
              offset: 40,
            });
            this.getQuickEnquiryUs();
          })
          .fail((error) => {
            this.$handleError(error);
          })
          .always(() => {});
      },
    },
    mounted() {
      this.getQuickEnquiryUs();
    }
  }
</script>