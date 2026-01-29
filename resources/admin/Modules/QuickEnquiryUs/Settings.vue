<template>
  <div class="qeus-settings-wrapper" v-loading="fetching" element-loading-spinner="el-icon-loading">
    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Enquiry US Shop Page Button Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Shop Page</span>
        <el-switch
            v-model="settings.shop_page_enable"
            active-text="Enable Shop Page"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('shop_page_enable', value)">
        </el-switch>
        <el-tooltip content="enable the shop page" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div v-if="settings.shop_page_enable === 'yes'">
          <div class="setting-row">
            <span class="setting-label">In-Stock Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.in_stock_shop_button_text"
                placeholder="Enquiry Us {title}"
                @change="(value) => changeHandler('in_stock_shop_button_text', value)"
            />
            <el-tooltip content="This text is used for the in-stock button on the shop page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Out-of-Stock Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.out_of_stock_shop_button_text"
                placeholder="Email when stock available {title}"
                @change="(value) => changeHandler('out_of_stock_shop_button_text', value)"
            />
            <el-tooltip content="This text is used for the out-of-stock button on the shop page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Backorder Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.backorder_shop_button_text"
                placeholder="Pre-order Enquiry {title}"
                @change="(value) => changeHandler('backorder_shop_button_text', value)"
            />
            <el-tooltip content="This text is used for the backorder button on the shop page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
      </div>
    </div>

    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Enquiry US Single Page Button Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Single Page</span>
        <el-switch
            v-model="settings.single_page_enable"
            active-text="Enable Single Page"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('single_page_enable', value)">
        </el-switch>
        <el-tooltip content="enable the single page" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div v-if="settings.single_page_enable === 'yes'">
          <div class="setting-row">
            <span class="setting-label">In-Stock Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.in_stock_single_button_text"
                @change="(value) => changeHandler('in_stock_single_button_text', value)"
            />
            <el-tooltip content="This text is used for the in-stock button on the single page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Out-of-Stock Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.out_of_stock_single_button_text"
                @change="(value) => changeHandler('out_of_stock_single_button_text', value)"
            />
            <el-tooltip content="This text is used for the out-of-stock button on the single page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Backorder Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.backorder_single_button_text"
                @change="(value) => changeHandler('backorder_single_button_text', value)"
            />
            <el-tooltip content="This text is used for the backorder button on the single page. You can use {title} to automatically display the product name." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
      </div>
    </div>

    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Enquiry US Form Heading</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">In-Stock Form Heading</span>
        <el-input
            class="setting-input"
            v-model="settings.in_stock_form_heading"
            @change="(value) => changeHandler('in_stock_form_heading', value)"
        />
        <el-tooltip content="This text appears as the form heading when the product is in-stock. Use {title} to automatically show the product name." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">Out-of-Stock Form Heading</span>
        <el-input
            class="setting-input"
            v-model="settings.out_of_stock_form_heading"
            @change="(value) => changeHandler('out_of_stock_form_heading', value)"
        />
        <el-tooltip content="This text appears as the form heading when the product is out-of-stock. Use {title} to automatically show the product name." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">Backorder Form Heading</span>
        <el-input
            class="setting-input"
            v-model="settings.backorder_form_heading"
            @change="(value) => changeHandler('backorder_form_heading', value)"
        />
        <el-tooltip content="This text appears as the form heading when the product is backordered. Use {title} to automatically show the product name." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>

    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Enquiry US Form Tabs Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Enquiry From Tab</span>
        <el-switch
            v-model="settings.enquiry_from_tab_enable"
            active-text="Enable Enquiry From Tab"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('enquiry_from_tab_enable', value)">
        </el-switch>
        <el-tooltip content="enable the enquiry from Tab" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">WhatsApp Tab</span>
        <el-switch
            v-model="settings.whatsapp_tab_enable"
            active-text="Enable WhatsApp Tab"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('whatsapp_tab_enable', value)">
        </el-switch>
        <el-tooltip content="enable the WhatsApp Tab" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">SMS Tab</span>
        <el-switch
            v-model="settings.sms_tab_enable"
            active-text="Enable SMS Tab"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('sms_tab_enable', value)">
        </el-switch>
        <el-tooltip content="enable the SMS Tab" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>

    <div class="qeus-settings-content" v-if="settings.enquiry_from_tab_enable === 'yes'">
      <div class="setting-title">
        <h3>Enquiry US Form Configure Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Email Send</span>
        <el-switch
            v-model="settings.email_enable"
            active-text="Enable Email"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('email_enable', value)">
        </el-switch>
        <el-tooltip content="Enable this to send enquiry form submissions to the selected email address." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>

      <div v-if="settings.email_enable === 'yes'">
        <div class="setting-row">
          <span class="setting-label">Admin Email <span style="color: red">*</span> </span>
          <el-input
              class="setting-input"
              v-model="settings.admin_email"
              @change="(value) => changeHandler('admin_email', value)"
            />
          <el-tooltip content="Enter the email address where enquiry form submissions will be sent." placement="top">
            <i class="el-icon-question"></i>
          </el-tooltip>
        </div>
      </div>

      <div class="setting-row">
        <span class="setting-label">Tab Name Change</span>
        <el-input
            class="setting-input"
            v-model="settings.enquiry_tab_text"
            @change="(value) => changeHandler('enquiry_tab_text', value)"
        />
        <el-tooltip content="Change the text displayed on the enquiry tab." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">Submit Button Text</span>
        <el-input
            class="setting-input"
            v-model="settings.enquiry_submit_button_text"
            @change="(value) => changeHandler('enquiry_submit_button_text', value)"
        />
        <el-tooltip content="Change the text displayed on the enquiry form submit button." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>

    <div class="qeus-settings-content" v-if="settings.whatsapp_tab_enable === 'yes'">
      <div class="setting-title">
        <h3>WhatsApp Configure Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Title Change</span>
        <el-input
            class="setting-input"
            v-model="settings.whatsapp_title_text"
            @change="(value) => changeHandler('whatsapp_title_text', value)"
        />
        <el-tooltip content="Change the title text displayed in the WhatsApp section" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">Tab Name Change</span>
        <el-input
            class="setting-input"
            v-model="settings.whatsapp_tab_text"
            @change="(value) => changeHandler('whatsapp_tab_text', value)"
        />
        <el-tooltip content="Change the text displayed on the WhatsApp tab." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row-repeter-fields">
        <el-button size="mini" type="primary" @click="addNew" class="repeter-add-btn">
          Add WhatsApp Number <i class="el-icon-circle-plus-outline"></i>
        </el-button>
        <div
          class="setting-input-repeter"
          v-for="(item, index) in whatsappNumbers"
          :key="index"
        >
          <el-input
            class="setting-input"
            placeholder="Receiver Name"
            v-model="item.receiver_name"
            @input="emitWhatsappNumbers"
          />
          <el-input
            class="setting-input"
            type="number"
            placeholder="WhatsApp Number"
            v-model="item.receiver_number"
            @input="emitWhatsappNumbers"
          />
          <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            class="delete-btn"
            :disabled="whatsappNumbers.length === 1"
            @click="removeLink(index)"
          />
          <el-tooltip content="Change the text in stock button text" placement="top">
            <i class="el-icon-question"></i>
          </el-tooltip>
        </div>
      </div>
      <div class="setting-row">
        <span class="setting-label">Button Text Change</span>
        <el-input
            class="setting-input"
            v-model="settings.whatsapp_button_text"
            @change="(value) => changeHandler('whatsapp_button_text', value)"
        />
        <el-tooltip content="Change the text in stock button text" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">WhatsApp Message</span>
        <el-input
            class="setting-input"
            type="textarea"
            :rows="3"
            v-model="settings.whatsapp_message"
            placeholder="Enter Your WhatsApp Message Here. You can use {receiver_name}, {title}, {url} as a placeholder."
            @change="(value) => changeHandler('whatsapp_message', value)"
        />
        <el-tooltip content="Change the text in stock button text" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>

    <div class="qeus-settings-content" v-if="settings.sms_tab_enable === 'yes'">
      <div class="setting-title">
        <h3>SMS Configure Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">SMS Send</span>
        <el-switch
            v-model="settings.sms_enable"
            active-text="Enable SMS"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('sms_enable', value)">
        </el-switch>
        <el-tooltip content="Enable this option to send enquiry form submissions via SMS." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">Tab Name Change</span>
        <el-input
            class="setting-input"
            v-model="settings.sms_tab_text"
            @change="(value) => changeHandler('sms_tab_text', value)"
        />
        <el-tooltip content="Change the text displayed on the sms tab." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>

      <div v-if="settings.sms_enable == 'yes'">
          <div class="setting-row">
            <span class="setting-label">Twilio Account SID <span style="color: red">*</span></span>
            <el-input
                class="setting-input"
                v-model="settings.twilio_account_sid"
                placeholder="Enter Your Twilio Account SID"
                @change="(value) => changeHandler('twilio_account_sid', value)"
            />
            <el-tooltip content="Enter your Twilio Account SID. This is required to send SMS messages via Twilio" placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Twilio Auth Token <span style="color: red">*</span></span>
            <el-input
                class="setting-input"
                v-model="settings.twilio_auth_token"
                placeholder="Enter Your Twilio Auth Token"
                @change="(value) => changeHandler('twilio_auth_token', value)"
            />
            <el-tooltip content="Enter your Twilio Auth Token. This is required to send SMS messages via Twilio" placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Twilio Phone Number <span style="color: red">*</span></span>
            <el-input
                class="setting-input"
                v-model="settings.twilio_phone_number"
                placeholder="Enter Your Twilio Phone Number"
                @change="(value) => changeHandler('twilio_phone_number', value)"
            />
            <el-tooltip content="Enter your Twilio Phone Number. This is required to send SMS messages via Twilio" placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
          <div class="setting-row">
            <span class="setting-label">Submit Button Text</span>
            <el-input
                class="setting-input"
                v-model="settings.sms_submit_button_text"
                @change="(value) => changeHandler('sms_submit_button_text', value)"
            />
            <el-tooltip content="Change the text displayed on the submit button." placement="top">
              <i class="el-icon-question"></i>
            </el-tooltip>
          </div>
      </div>
    </div>
    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>ShortCode Enquiry Us Button Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Enquiry Us Button Text</span>
        <el-input
            class="setting-input"
            v-model="settings.shortcode_enquiry_us_button_text"
            @change="(value) => changeHandler('shortcode_enquiry_us_button_text', value)"
        />
        <el-tooltip content="Change the text displayed on the shortcode button." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">ShortCode General Form</span>
        <el-input
            class="setting-input"
            v-model="shortcodeGeneralForm"
            readonly
        />
        <el-tooltip effect="dark"
          content="Click To Copy"
          title="Click To Copy"
          placement="top">
          <code
            class="copy"
            :data-clipboard-text="getCopyText(shortcodeGeneralForm)">
            <i class="el-icon-document-copy"></i>
          </code>
        </el-tooltip>
        <el-tooltip content="Copy this shortcode to display the enquiry button for general form." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">ShortCode Product Id</span>
        <el-input
            class="setting-input"
            v-model='shortcodeProductId'
            readonly
        />
        <el-tooltip effect="dark"
          content="Click To Copy"
          title="Click To Copy"
          placement="top">
          <code
            class="copy"
            :data-clipboard-text="getCopyText(shortcodeProductId)">
            <i class="el-icon-document-copy"></i>
          </code>
        </el-tooltip>
        <el-tooltip content="Copy this shortcode to display the enquiry button for a specific product." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row">
        <span class="setting-label">ShortCode Product Slug</span>
        <el-input
            class="setting-input"
            v-model='shortcodeProductSlug'
            readonly
        />
        <el-tooltip effect="dark"
          content="Click To Copy"
          title="Click To Copy"
          placement="top">
          <code
            class="copy"
            :data-clipboard-text="getCopyText(shortcodeProductSlug)">
            <i class="el-icon-document-copy"></i>
          </code>
        </el-tooltip>
        <el-tooltip content="Copy this shortcode to display the enquiry button for a specific product." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>   
    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Cart Page Enquiry Us Button Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Cart Page</span>
        <el-switch
            v-model="settings.cart_page_enquiry_us_btn_enable"
            active-text="Enable Cart Page"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('cart_page_enquiry_us_btn_enable', value)">
        </el-switch>
        <el-tooltip content="enable the cart page" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row" v-if="settings.cart_page_enquiry_us_btn_enable == 'yes'">
        <span class="setting-label">Enquiry Us Button Text</span>
        <el-input
            class="setting-input"
            v-model="settings.cart_page_enquiry_us_button_text"
            @change="(value) => changeHandler('cart_page_enquiry_us_button_text', value)"
        />
        <el-tooltip content="Change the text displayed on the cart button." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div> 
    <div class="qeus-settings-content">
      <div class="setting-title">
        <h3>Order Summary Enquiry Us Button Settings</h3>
      </div>
      <div class="setting-row">
        <span class="setting-label">Order Summary</span>
        <el-switch
            v-model="settings.order_summary_enquiry_us_btn_enable"
            active-text="Enable Order Summary"
            active-value="yes"
            inactive-value="no"
            @change="(value) => changeHandler('order_summary_enquiry_us_btn_enable', value)">
        </el-switch>
        <el-tooltip content="enable the order summary" placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
      <div class="setting-row" v-if="settings.order_summary_enquiry_us_btn_enable == 'yes'">
        <span class="setting-label">Enquiry Us Button Text</span>
        <el-input
            class="setting-input"
            v-model="settings.order_summary_enquiry_us_button_text"
            @change="(value) => changeHandler('order_summary_enquiry_us_button_text', value)"
        />
        <el-tooltip content="Change the text displayed on the cart button." placement="top">
          <i class="el-icon-question"></i>
        </el-tooltip>
      </div>
    </div>   
  </div>
</template>

<script type="text/babel">
  export default {
  name: 'settings',
  props: {
    settings: {
      type: Object,
      required: true
    },
    fetching: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      whatsappNumbers: [
        {
          receiver_name: '',
          receiver_number: ''
        }
      ],
      shortcodeGeneralForm: '[quick_enquiry_us_button]',
      shortcodeProductId: '[quick_enquiry_us_button product_id="23"]',
      shortcodeProductSlug: '[quick_enquiry_us_button product_slug="beanie-with-logo"]'
    }
  },
  watch: {
    // Handle async-loaded settings (WordPress / API)
    'settings.whatsapp_numbers': {
      immediate: true,
      deep: true,
      handler(newVal) {
        if (Array.isArray(newVal) && newVal.length) {
          this.whatsappNumbers = JSON.parse(JSON.stringify(newVal))
        } else {
          this.whatsappNumbers = [
            {
              receiver_name: '',
              receiver_number: ''
            }
          ]
        }
      }
    }
  },
  methods: {
    changeHandler(field, value) {
      this.$emit('update', field, value)
    },

    emitWhatsappNumbers() {
      this.$emit('update', 'whatsapp_numbers', this.whatsappNumbers)
    },

    addNew() {
      if (this.whatsappNumbers.length >= 5) {
        return // stop adding more than 5
      }

      this.whatsappNumbers.push({
        receiver_name: '',
        receiver_number: ''
      })
      this.emitWhatsappNumbers()
    },

    removeLink(index) {
      this.whatsappNumbers.splice(index, 1)
      this.emitWhatsappNumbers()
    }
  }
}
</script>