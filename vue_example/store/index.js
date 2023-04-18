import { createStore } from 'vuex'
import router from '../router';

let errTimeout;
let msgTimeout;

export default createStore({
  state: {
    loading: false,
    accessToken: null,
    hasError: false,
    msg: false
  },
  mutations: {
    setLoading: (state, loading) => loading ? state.loading = true : state.loading = false,
    setError: (state, error) => {
      console.log(state)
      console.log(error)
      state.hasError = false
      errTimeout && clearTimeout(errTimeout);
      errTimeout = setTimeout(() => state.hasError = error ? {error: error.error} : false, 100);
      if (error.status && error.status == 401) {
        localStorage.removeItem('accessToken');
        state.accessToken = null;
        router.push('/signin');
      }
    },
    setMsg: (state, msg) => {
      state.msg = false
      msgTimeout && clearTimeout(msgTimeout);
      msgTimeout = setTimeout(() => state.msg = msg ? msg : false, 100);
    },
    updateAccessToken: (state, accessToken) => {
      state.accessToken = accessToken;
    },
    signout: (state) => {
      state.accessToken = null;
    }
  },
  actions: {
    doSignin({ commit }, signinData) {
      commit('setLoading', true);
      
      fetch('/auth/signin', {
        method: 'POST',
        body: JSON.stringify(signinData)
      })
      .then(async r => {
        let response = await r.json()

        if (response.error || !response.token) {
          commit('setLoading', false);
          commit('setError', response.error);
          commit('updateAccessToken', null);
          
          return
        }

        localStorage.setItem('accessToken', response.token);
        commit('setLoading', false);
        commit('setMsg', 'Signed In successeful');
        commit('updateAccessToken', response.token);

        router.push('/');
      })
    },
    fetchAccessToken({ commit }) {
      commit('updateAccessToken', localStorage.getItem('accessToken'));
    },
    signout({ commit }) {
      localStorage.removeItem('accessToken');
      commit('signout');
      router.push('/signin');
    }
  },
  modules: {
  }
})
