<template>
  <div class="home">
    <div class="container">
      <VideoFeature v-if="VideoFeature" :videosfeature="VideoFeature"/>
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
import VideoFeature from '@/components/FeatureVideos.vue'

export default {
  name: 'Feature',
  data() {
    return {
      VideoFeature: false,
    }
  },
  components: {
    VideoFeature
  },
  computed: {
    ...mapState([
      'accessToken',
    ]),
  },
  mounted() {
    this.getVideosFeature()
  },
  methods: {
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    getVideosFeature() {
      this.setLoading(true)
      fetch('/api/videos/featured', {
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

            this.VideoFeature = response;
          })
    }
  }
}
</script>
