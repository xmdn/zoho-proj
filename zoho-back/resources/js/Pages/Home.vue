<template>
    <div>
      <div v-if="isLoggedIn">
        <List />
      </div>
      <div class="unloged" v-else>
        <h1>Please login to continue</h1>
      </div>
    </div>
  </template>
  

  <script>
  import axios from 'axios';
  import { computed, renderList } from 'vue'
  import { useStore } from 'vuex'
  import { mapActions, mapGetters } from 'vuex'

  import List from '../Components/List.vue'

  export default {
    components: {
      List
    },
    computed: {
    ...mapGetters(['isLoggedIn']),
  },
  methods: {
    ...mapActions(['logout']),
  },
    setup() {
      const store = useStore()
      console.log('Access Token:', store.state.accessToken);
      const isLoggedIn = computed(() => store.state.accessToken !== null)
      const user = computed(() => store.state.user)
  
      const handleLogout = async () => {
        await store.dispatch('logout')
      }

      const handleLogin = async () => {
        await store.dispatch('login')
      }
      return { isLoggedIn, user, handleLogout, handleLogin }
      
    },
  }
  </script>
<style>
.unloged {
  display: flex;
    justify-content: center;
}
</style>