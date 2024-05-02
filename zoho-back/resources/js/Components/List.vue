
<template>
  <div>
    <div class="list-container">
      <div class="deal" v-for="deal in deals" :key="deal.id">
        <div>
          <h3>{{ deal.name }}</h3>
          <p>Stage: {{ deal.stage }}</p>
          <p>Account: {{ deal.account_name.name }}</p>
          <p>Time: {{ deal.create_time }}</p>
        </div>
        <div class="editable" v-if="deal.editable">
          <button class="edit-btn" @click="handleEdit(deal.id)">Edit</button>
        </div>
      </div>
    </div>
  </div>
</template> 

<script>
 import axios from 'axios';
export default {
  data() {
    return {
      deals: [],
    };
  },
  mounted() {
    console.log('List component mounted.');
    this.getDeals();
  },
  methods: {
    handleEdit(id) {
      window.location.href = `/deal/${id}`;
    },
    getDeals() {
        // Make an HTTP GET request to fetch stages
        axios.get('/api/deals')
          .then(response => {
            // Update stages data with the response
            console.log('List component mounted.', response.data.list_deals);
            this.deals = response.data.list_deals;
          })
          .catch(error => {
            // Handle error response
            console.error(error);
          });
      },
  },
}
</script>

<style>
.list-container {
  display: grid;
  grid-template-columns: 32% 32% 32%;
  justify-content: center;
}
.deal {
  box-shadow: 7px -5px 38px -29px rgba(0, 0, 0, 0.75);
  margin: 5px;
  border-radius: 10px;
  display: flex;
  width: 30vw;
  height: 19vw;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-between;
}
.editable {
  padding-bottom: 15px;
  padding-right: 15px;
  display: flex;
  width: 98%;
  height: 15%;
  justify-content: flex-end;
}
.editable button {
  width: 16%;
  border-radius: 5px;
  background-color: #0000f382;
}
</style> 