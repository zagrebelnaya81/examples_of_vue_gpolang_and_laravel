<template>
  <div class="home">
    <div class="container">
      <groups-video v-if="groupsVideoList" :groupsvideo="groupsVideoList"/>
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
import GroupsVideo from '@/components/GroupsVideo.vue'

export default {
  name: 'Home',
  data() {
    return {
      groupsVideoList: false,
      groupsList: false,
      k:0
    }
  },
  components: {
    GroupsVideo
  },
  computed: {
    ...mapState([
      'accessToken',
    ])
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
      var list_of_groups=[]
      this.setLoading(true)
      fetch('/api/videos/getvideobygroupsids', {
        method: 'GET',
        headers: {
            'Atn-Token': this.accessToken
        }
      })
      .then(async r => {
        let response = await r.json()

        //get groups information
        response.map(function(value) {
          list_of_groups.push(value._id);
        });


        if (response.error) {
          this.setLoading(false)
          this.setError(response)
          console.log(response)
          return
        }
        this.setLoading(false)
        this.setMsg('Groups, Videos obtained');
        this.groupsVideoList = response;
        console.log(this.groupsVideoList)
      })

      //groups
       fetch('/api/videosgroup/list', {
          method: 'GET',
          headers: {
            'Atn-Token': this.accessToken
          }
        })
            .then(async r => {
              let responseGroup = await r.json()
console.log(responseGroup)
              if (responseGroup.error) {
                this.setLoading(false)
                this.setError(responseGroup)
                return
              }
              this.setLoading(false)
              this.setMsg('Groups obtained');

              this.groupsList = responseGroup;
            })
    }
  }
}
</script>
