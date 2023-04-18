<template>
  <div class="home">
    <div class="container">
      <ListGroup v-if="ListGroup" :listgroup="ListGroup"/>
    </div>
  </div>
</template>

<style lang="less" scoped>
.container {
  display: flex;
  flex-direction: column;
}
</style>

<script>
import { mapMutations, mapState } from "vuex";
import ListGroup from '@/components/ListGroups.vue'

export default {
  name: 'Home',
  data() {
    return {
      ListGroup: false,
    }
  },
  components: {
    ListGroup
  },
  computed: {
    ...mapState([
      'accessToken',
    ])
  },
  mounted() {
    this.getGroups()
  },
  methods: {
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    getGroups() {
      this.setLoading(true)
      fetch('/api/videosgroup/list', {
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
            this.setMsg('Groups obtained');
            this.ListGroup = response;
          })
    }
  }
}
</script>
