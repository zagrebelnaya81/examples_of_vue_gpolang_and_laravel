<template>
  <div class="home">
    <div class="container">
      <VideoList v-if="VideoList" :videoslist="VideoList"/>
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
import VideoList from '@/components/ListVideos.vue'

export default {
  name: 'Home',
  data() {
    return {
      VideoList: false,
    }
  },
  components: {
    VideoList
  },
  computed: {
    ...mapState([
      'accessToken',
    ]),
  },
  mounted() {
    this.getVideos()
  },
  methods: {
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    getVideos() {
      this.setLoading(true)
      fetch('/api/videos/list', {
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
            this.setMsg('Groups, Videos obtained');

            this.VideoList = response;
          })
    }
  }
}
</script>
