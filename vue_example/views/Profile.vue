<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col">
        <form class="">
          <div class="form-group">
            <!-- <span class="badge badge-primary">
              {{profile.id}}
            </span> -->
            <div>
                <label class="form-label">
                  {{profile.email}}
                </label>
            </div>
          </div>
          <div class="form-group">
            <input
              placeholder="User name"
              name="name"
              required
              min="3"
              type="text"
              id="formBasicEmail"
              class="form-control"
              v-model="profile.name"
            />
          </div>
          <div class="form-group">
            <input
              placeholder="New password"
              name="password"
              type="password"
              id="formBasicEmail"
              class="form-control"
              v-model="profile.password"
              @change="error = false"
            />
            <span class="badge badge-danger" v-if="error">
              {{error}}
            </span>
          </div>
          <div role="toolbar" class="btn-toolbar">
            <button type="button" class="mr-2 btn btn-primary" @click="updateProfile">
              Update Profile
            </button>
            <button type="button" class="btn btn-outline-light" @click="signout">
              Signout
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style src="@/assets/less/form.less" lang="less"></style>

<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      error: false,
      profile: {
        email: '',
        id: '',
        name: '',
        password: ''
      }
    };
  },
  computed: {
    ...mapState([
      'accessToken',
    ])
  },
  mounted() {
    this.getProfile();
  },
  methods: {
    ...mapActions([
      "signout",
    ]),
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    getProfile() {
      this.setLoading(true)
      fetch('/auth/profile', {
        method: 'GET',
        headers: {
            'Atn-Token': this.accessToken
        }
      })
      .then(async r => {
        let response = await r.json()
        if (response.error) {
          this.setLoading(false)
          this.setError(response)
          return
        }
        this.setLoading(false)
        this.setMsg('Profile obtained');
        this.profile = response;
      })
    },
    updateProfile() {
      let user = {
        id: this.profile.id,
        email: this.profile.email,
        name: this.profile.name
      }
      let pass = this.profile.password
      if (pass && !!pass.length) {
        if (pass.length < 4) return this.error = 'password longer than 3';
        user.password = pass
      }

      this.setLoading(true)
      fetch('/auth/profile', {
        method: 'PUT',
        headers: {
            'Atn-Token': this.accessToken
        },
          body: JSON.stringify(user)
      })
      .then(async r => {
        let response = await r.json()
        if (response.error) {
          this.setLoading(false)
          this.setError(response)
          return
        }
        this.setLoading(false)
        this.setMsg('Profile updated');
        this.getProfile();
      })
    }
  },
};
</script>