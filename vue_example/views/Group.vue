<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col">
        <form class="">
          <div class="form-group">
            <span class="badge badge-primary mb-2">
              {{group.id}}
            </span>
            <div>
                <span class="badge badge-info">
                    Group ID: {{group.group_id}}
                </span>
            </div>
          </div>
          <div class="form-group" v-if="!removed">
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
            />
          </div>
          <div class="form-group">
            <input class="form-control" v-model="group.name" placeholder="Name"/>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="3" v-model="group.description"></textarea>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="8" v-model="group.labels"></textarea>
          </div>
          <div role="toolbar" class="btn-toolbar">
            <button type="button" class="mr-2 btn btn-primary" @click="updateGroup">
              Update Group
            </button>
            <button type="button" class="btn btn-danger" @click="deleteGroup = true" v-if="!deleteGroup">
              DELETE
            </button>
            <button type="button" class="btn btn-success mr-2" @click="deleteGroup = false" v-if="deleteGroup">
              Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="setRemove" v-if="deleteGroup">
              DELETE GROUP
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
      groupRouteId: this.$route.params.groupRouteId,
      deleteGroup: false,
      error: false,
      removed: false,
      group: {
        id: '',
        name:'',
        group_id: '',
        description: '',
        sort_order: 0,
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
    removeGroup() {
      this.setLoading(true)
      fetch(`/api/videosgroup/remove/${this.groupRouteId}`, {
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

        this.setMsg('Group REMOVED. Redirecting to Home');

        this.remove = true;
        setTimeout(() => router.push('/'), 2000)
      })
    },
    getGroup() {
      this.setLoading(true)
      console.log('groupRouteId')
      console.log(this.groupRouteId)
      fetch(`/api/videosgroup/list/${this.groupRouteId}`, {
        method: 'GET',
        headers: {
            'Atn-Token': this.accessToken
        }
      })
      .then(async r => {
        let response = await r.json()
        console.log(response)
        if (response.error) {
          this.setLoading(false)
          this.setError(response)

          return
        }
        this.setLoading(false)
        this.setMsg('Group details received');
        this.group = response;
        console.log(this.group);
      })
    },
    updateGroup() {
      this.group.sort_order=parseInt(this.group.sort_order);
      this.setLoading(true)
      fetch('/api/videosgroup/update', {
        method: 'POST',
        headers: {
            'Atn-Token': this.accessToken
        },
          body: JSON.stringify(this.group)
      })
      .then(async r => {
        let response = await r.json()
        if (response.error) {
          this.setLoading(false)
          this.setError(response)
          return
        }
        this.setLoading(false)
        this.setMsg('Group updated');
        this.getGroup();
      })
    },
    setRemove() {
      this.removeGroup()
    }
  },
};

</script>