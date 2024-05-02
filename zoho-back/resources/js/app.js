import { createApp, h, reactive } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createStore } from 'vuex'
import axios from 'axios'
import * as yup from 'yup';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    
    const store = createStore({
      state: {
        user: null,
        isLoggedIn: false,
        accessToken: accessToken,
      },
      mutations: {
        setUser(state, user) {
          state.user = user
        },
        setAccessToken(state, accessToken) {
          state.accessToken = accessToken
          state.isLoggedIn = true
        },
        logout(state) {
          state.user = null
          state.accessToken = null
          state.isLoggedIn = false
        },
      },
      actions: {
        async login({ commit }) {
          try {
            window.location.href = '/oauth';
            // const response = await axios.get('api/login')
            // const { user, token } = response.data
            // commit('setUser', user)
            // commit('setToken', token)
            return true
          } catch (error) {
            console.error('Login error:', error)
            return false
          }
        },
        async logout({ commit }) {
          try {
            await axios.post('api/logout');
            location.reload();
            // commit('logout')
          } catch (error) {
            console.error('Logout error:', error)
          }
        },
      },
      getters: {
        isLoggedIn: state => state.isLoggedIn,
      },
    })

    // Call parseAccessToken method when the route changes and the URI matches /oauth/callback
    const handleRouteChange = () => {
      if (window.location.pathname.includes('/oauth/callback')) {
        console.log('Valldf');
        store.dispatch('parseAccessToken');
      }
    };

    // Listen for route changes and trigger handleRouteChange
    window.addEventListener('popstate', handleRouteChange);

    
    createApp({ render: () => h(App, props)})
      .use(plugin)
      .use(store)
      .mount(el)

  },
})
