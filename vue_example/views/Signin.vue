<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col">
        <form @submit.prevent="signinSubmit">
          <div class="form-group">
            <input
              placeholder="User name"
              name="name"
              type="email"
              class="form-control"
              v-model="email"
            />
          </div>
          <div class="form-group">
            <input
              placeholder="Password"
              name="password"
              type="password"
              class="form-control"
              v-model="password"
            />
          </div>
          <div role="toolbar" class="btn-toolbar">
            <button
              type="submit"
              class="mr-2 btn btn-primary"
            >
              Signin
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style src="@/assets/less/form.less" lang="less"></style>

<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {
    ...mapActions(["doSignin"]),
    signinSubmit() {
      this.doSignin({
        email: this.email,
        password: this.password,
      });
    }
  },
};
</script>