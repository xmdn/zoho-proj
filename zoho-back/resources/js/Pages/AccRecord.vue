<template>
  <div>
    <form class="form-acc-container" @submit.prevent="submitForm">
      <div class="group-container">
        <div>
          <h1>Create Account Record</h1>
        </div>
        <div>
          <label for="accountName">Account Name:</label>
          <div class="group-inputs">
            <input type="text" v-model="accountName" @input="validateAccountName" />
            <span v-if="!isAccountNameValid" class="error-message">Account Name is required</span>
          </div>
          
        </div>
        <div>
          <label for="accountWebsite">Account Website:</label>
          <div class="group-inputs">
            <input type="text" v-model="accountWebsite" @input="validateAccountWebsite" />
            <span v-if="!isAccountWebsiteValid" class="error-message">{{ accountWebsiteError }}</span>
          </div>
          
        </div>
        <div>
          <label for="accountPhone">Account Phone:</label>
          <div class="group-inputs">
            <input type="text" v-model="accountPhone" @input="validateAccountPhone" />
            <span v-if="!isAccountPhoneValid" class="error-message">{{ accountPhoneError }}</span>
          </div>
          
        </div>
        <div>
          <button class="create-btn" type="submit" :disabled="!isFormValid">Create Account</button>
        </div>
      </div>
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </form>
    
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AccRecord',
  data() {
    return {
      accountName: '',
      accountWebsite: '',
      accountPhone: '',
      isAccountNameValid: false,
      isAccountWebsiteRequired: false,
      isAccountWebsiteValid: false,
      accountWebsiteError: '',
      isAccountPhoneRequired: false,
      isAccountPhoneValid: false,
      accountPhoneError: '',
      successMessage: '',
      errorMessage: '',
    };
  },
  computed: {
    isFormValid() {
      return this.isAccountNameValid && this.isAccountWebsiteValid && this.isAccountPhoneValid;
    },
  },
  methods: {
    validateAccountName() {
      this.isAccountNameValid = !!this.accountName.trim();
    },
    validateAccountWebsite() {
      this.isAccountWebsiteRequired = !!this.accountWebsite.trim();
      if (this.isAccountWebsiteRequired) {
        const pattern = /^https:\/\/(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        console.log(pattern.test(this.accountWebsite));
        this.isAccountWebsiteValid = pattern.test(this.accountWebsite);
        this.accountWebsiteError = this.isAccountWebsiteValid ? '' : "Account Website should be 'https://example.com'";
      } else {
        console.log('required')
        this.isAccountWebsiteValid = false;
        this.accountWebsiteError = 'Account Website is required';
      }
    },
    validateAccountPhone() {
      this.isAccountPhoneRequired = !!this.accountPhone.trim();
      if (this.isAccountPhoneRequired) {
        this.isAccountPhoneValid = /^\d+$/.test(this.accountPhone); // Check if phone is a valid number
        this.accountPhoneError = this.isAccountPhoneValid ? '' : "Account Phone should be number";
      } else {
        this.isAccountPhoneValid = false;
        this.accountPhoneError = 'Account Phone is required';
      }
    },
    async submitForm() {
      if (this.isFormValid) {
        try {
          await axios.post('/api/account', {
            accountName: this.accountName,
            accountWebsite: this.accountWebsite,
            accountPhone: this.accountPhone,
          });
          this.successMessage = 'Account created successfully!';
          this.resetForm();
        } catch (error) {
          this.errorMessage = 'Failed to create account';
        }
      } else {
        this.errorMessage = 'Please fill in all required fields correctly';
      }
    },
    resetForm() {
      this.accountName = '';
      this.accountWebsite = '';
      this.accountPhone = '';
      this.isAccountNameValid = false;
      this.isAccountWebsiteRequired = false;
      this.isAccountWebsiteValid = false;
      this.accountWebsiteError = '';
      this.isAccountPhoneRequired = false;
      this.isAccountPhoneValid = false;
      this.accountPhoneError = '';
      this.errorMessage = '';
    },
  },
};
</script>

<style>
.group-inputs {
  display: flex;
  flex-direction: column;
}
.error-message {
  color: red;
}
.form-acc-container {
  height: 70vw;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
.group-container {
  display: flex;
  background-color: aliceblue;
  box-shadow: 7px -5px 38px -29px rgba(0, 0, 0, 0.75);
  border-radius: 10px;
  height: 40vw;
  width: 60vw;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;
}
.group-container div {
  display: flex;
  width: 60%;
  justify-content: space-around;
}
.create-btn {
  color: white;
  background-color: #394dd2;
  border-radius: 15px;
  width: 40%;
}
</style>
