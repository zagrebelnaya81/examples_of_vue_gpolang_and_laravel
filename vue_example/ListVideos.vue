<template>
  <div>
      <div class="card-transparent border rounded border-secondary text-white mb-3 group">
        <div class="videos">
          <div class="item" v-for="video in videoslist" :key="video.id" @click="gotoVideo(video.video_id)" :style="`background-image: url(http://img.youtube.com/vi/${video.channel_link}/mqdefault.jpg)`">
            <div class="title">{{video.name}}</div>
          </div>
        </div>
      </div>
  </div>
</template>

<style lang="less" scoped>
.group {
    .videos {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
    }
}
.item {
  display: flex;
  flex-direction: column;
  position: relative;
  cursor: pointer;
  margin-right: 0.5em;
  margin-bottom: 0.5em;
  width: calc(33% - 1em);
  height: 210.22px;
  background-size: cover;
  .title {
    position: absolute;
    font-size: 1.2em;
    width: 90%;
    padding: 0.5em;
    text-shadow: 2px 1px 6px black, 2px 1px 6px black;
  }
}
</style>

<script>
import router from '../router';

export default {
  name: "ListVideos",
  props: ["videoslist"],
  methods: {
    gotoVideo(id) {
      router.push({ name: 'Video', params: { videoRouteId: id } })
    }
  }
};
</script>