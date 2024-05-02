<template>
    <form class="form-container" @submit.prevent="submitForm">
        <div class="group-container">
            <div>
                <label for="dealName">Deal Name:</label>
                <input type="text" id="dealName" v-model="dealName" required />
            </div>
            <div>
                <label for="dealStage">Deal Stage:</label>
                <select id="dealStage" v-model="dealStage" required>
                    <option value="" disabled>Select Stage</option>
                    <option v-for="stage in stagesArr" :key="stage" :value="stage" :selected="stage === dealStage">{{ stage }}</option>
                </select>
            </div>
            <div>
                <label for="dealAcc">Deal Accounts:</label>
                <select id="dealAcc" v-model="dealAccId" required>
                    <option value="" disabled>Select Acc</option>
                    <option v-for="acc in accs" :key="acc" :value="acc.id" :selected="acc === dealAccId">{{ acc.name }}</option>
                </select>
            </div>
            <div class="group-btns">
                <button class="back-btn" @click="handleBack()">Back</button>
                <button class="create-btn" type="submit">Change Deal</button>
            </div>
            
        </div>
        <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </form>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    props: {
        deal: {
            type: Object,
            required: true
        },
        accounts: {
            type: Array,
            required: true
        },
        stages: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            dealName: this.deal.name || '', // Initialize with deal name if available
            dealStageText: this.deal.stage || '', // Initialize with deal stage text if available
            dealStage: this.deal.stage, // Initialize select with empty value
            dealAccId: this.deal.account_name.id, // Initialize with empty value
            dealAccChosen: '',
            stagesArr: this.stages.original.stages,
            accs: this.accounts.original.accounts,
            successMessage: ''
        };
    },
    mounted() {
        console.log('Deal:', this.deal);
        console.log('Accounts:', this.accounts.original.accounts);
        console.log('Stgs:', this.stages.original.stages);
    },
    methods: {
      submitForm() {
        const formData = {
          dealName: this.dealName,
          dealStage: this.dealStage,
          dealAccount: this.dealAccId,
        };
        const dealId = this.deal.id;
        // Make an HTTP POST request to your Laravel backend API
        axios.put(`/api/deal/${dealId}`, formData)
          .then(response => {
            // Handle success response
            console.log(response.data);
            this.successMessage = 'Deal updated successfully!';
            // Display success message to the user
          })
          .catch(error => {
            // Handle error response
            console.error(error);
            // Display error message to the user
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
.form-container {
    height: 70vw;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-direction: column;
    margin-top: 5%;
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