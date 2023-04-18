<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col">
        <form class="">
          <div class="form-group">
          </div>
          <div class="form-group">
            <label class="form-label">
                Sort Order
            </label>
            <input
              name="name"
              required
              min="100"
              step="100"
              type="number"
              class="form-control"
              v-model="group.sort_order"
              placeholder="Sort_order"
            />
          </div>
          <div class="form-group">
            <input class="form-control" v-model="group.name" placeholder="Name"/>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="3" v-model="group.description" placeholder="Description"></textarea>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="8" v-model="group.labels" placeholder="Labels"></textarea>
          </div>
          <div role="toolbar" class="btn-toolbar">
            <button type="button" class="mr-2 btn btn-primary" @click="addGroup">
              Add Group
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style src="@/assets/less/form.less" lang="less"></style>

<script>
import router from '../router';
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      error: false,
      removed: false,
      group: {
        id: '',
        group_id: '',
        name: '',
        description: '',
        sort_order: '',
        labels: '',
        videos: ''
      }
    };
  },
  computed: {
    ...mapState([
      'accessToken',
    ])
  },
  mounted() {
    this.getGroup();
  },
  methods: {
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    addGroup() {
      this.setLoading(true)
      let group = {
        sort_order: parseInt(this.group.sort_order),
        description: this.group.description,
        labels: this.group.labels,
        name:this.group.name,
      }
      fetch(`/api/videosgroup/add`, {
        method: 'POST',
        headers: {
            'Atn-Token': this.accessToken
        },
        body: JSON.stringify(group)
      })
      .then(async r => {
        let response = await r.json()
        if (response.error) {
          this.setLoading(false)
          this.setError(response)

          return
        }

        this.setMsg('Group ADDED. Redirecting to Home');

        this.remove = true;
        setTimeout(() => router.push('/videosgroup/list'), 2000)
      })
    },
  },
};

</script>