<template>
  <form class="form-single-container" @submit.prevent="submitForm">
    <div class="group-container">
      <div>
        <label for="dealName">Deal Name:</label>
        <div class="group-inputs">
          <input type="text" id="dealName" v-model="dealName" required />
          <span v-if="!isDealNameValid" class="error-message">Deal Name is required</span>
        </div>
      </div>
      <div>
        <label for="dealStage">Deal Stage:</label>
        <div class="group-inputs">
          <select id="dealStage" v-model="dealStage" required>
            <option value="" disabled>Select Stage</option>
            <option v-for="stage in stages" :key="stage" :value="stage">{{ stage }}</option>
          </select> 
          <span v-if="!isDealStageValid" class="error-message">Deal Stage is required</span>
        </div>
        
      </div>
      <div>
        <label for="dealAcc">Deal Accounts:</label>
        <div class="group-inputs">
          <select id="dealAcc" v-model="dealAcc" required>
            <option value="" disabled>Select Acc</option>
            <option v-for="acc in accs" :key="acc" :value="acc.id">{{ acc.name }}</option>
          </select>
          <span v-if="!isDealAccValid" class="error-message">Deal Account is required</span>
        </div>
        
      </div>
      <div class="group-btns">
        <button class="back-btn" @click="handleBack()">Back</button>
        <button class="create-btn" type="submit">Create Deal</button>
      </div>
      
    </div>
    <div v-if="showSuccessMessage" class="success-message">Deal created successfully!</div>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      dealName: '',
      dealStage: '',
      dealAcc: '',
      stages: [],
      accs: [],
      showSuccessMessage: false,
    };
  },
  mounted() {
    this.getStages();
    this.getAccs();
  },
  computed: {
    isDealNameValid() {
      return !!this.dealName.trim();
    },
    isDealStageValid() {
      return !!this.dealStage;
    },
    isDealAccValid() {
      return !!this.dealAcc;
    },
    isFormValid() {
      return this.isDealNameValid && this.isDealStageValid && this.isDealAccValid;
    },
  },
  methods: {
    submitForm() {
      if (!this.isFormValid) {
        return; 
      }
      const formData = {
        dealName: this.dealName,
        dealStage: this.dealStage,
        dealAccount: this.dealAcc,
      };

      // Make an HTTP POST request to your Laravel backend API
      axios.post('/api/deal', formData)
        .then(response => {
          // Handle success response
          console.log(response.data);
          this.showSuccessMessage = true;
          setTimeout(() => {
            window.location.reload();
          }, 2000);
          // Display success message to the user
        })
        .catch(error => {
          // Handle error response
          console.error(error);
          // Display error message to the user
        });
    },
    getStages() {
      // Make an HTTP GET request to fetch stages
      axios.get('/api/stages')
        .then(response => {
          // Update stages data with the response
          this.stages = response.data.stages;
        })
        .catch(error => {
          // Handle error response
          console.error(error);
        });
    },
    getAccs() {
      // Make an HTTP GET request to fetch stages
      axios.get('/api/accounts')
        .then(response => {
          // Update stages data with the response
          this.accs = response.data.accounts;
        })
        .catch(error => {
          // Handle error response
          console.error(error);
        });
    },
    handleBack() {
      // Use Vue Router to navigate back
      window.location.href = '/';
    },
    
  },
};
</script>
<style>
.success-message {
  color: green;
  margin-top: 10px;
}
.group-inputs {
  display: flex;
  flex-direction: column;
}
.form-single-container {
    padding-top: 5%;
    height: 70vw;
    display: flex;
    justify-content: flex-start;
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
    background-color: #39cdd2;
    border-radius: 15px;
    /* height: 8%; */
    width: 40%;
}
.back-btn {
    background-color: #888888;
    border-radius: 15px;
    /* height: 8%; */
    width: 15%;
}
.group-btns {
    height: 8%;
}
</style>