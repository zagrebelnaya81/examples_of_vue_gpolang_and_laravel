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

// let temp = [
//     {
//         "id": "5fe340cf798ead6db07d2462",
//         "group_id": "4",
//         "name": "BinoX Series",
//         "description": "lorem ipsum dolor sit amiet posfnto apdmrp ,smkf s,mfmf, d,fmb ",
//         "sort_order": "100",
//         "labels": "binocular, binoculars, digital binoculars, night vision binoculars, binoculars with camera, hunting binoculars, camera binoculars, best hunting binoculars for the money, good hunting binoculars, binocular night vision, best night vision binoculars, digital camera binoculars, night binoculars, binoculars with night vision, binoculars with digital camera",
//         "videos": [{"id":"5fe340d0798ead6db07d2487","video_id":"30","name":"Turkey hunting at fields | ATN Shot Trak HD Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Z2CsmE9t5jk","channel_link":"Z2CsmE9t5jk","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2488","video_id":"31","name":"Texas turkeys & pigs hunting | ATN Shot Trak HD gun camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/MlpHu71WXuI","channel_link":"MlpHu71WXuI","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2489","video_id":"32","name":"Turkey hunting | ATN Shot Trak HD gun camera","description":"https://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fwvu62S4Q1k","channel_link":"Fwvu62S4Q1k","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248a","video_id":"33","name":"Deer walking | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/cHHOJA50huE","channel_link":"cHHOJA50huE","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248b","video_id":"34","name":"Pigs hunting with bow | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/DfH0Sjwmnoo","channel_link":"DfH0Sjwmnoo","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248c","video_id":"35","name":"Duck hunting (ring neck) | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/n8IhRRWlOeY","channel_link":"n8IhRRWlOeY","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248d","video_id":"36","name":"Red Head kill | Duck hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/0ZDNHD9ynGc","channel_link":"0ZDNHD9ynGc","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248e","video_id":"37","name":"Partridge Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/qnbSvkg30qQ","channel_link":"qnbSvkg30qQ","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248f","video_id":"38","name":"Bird Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fe28k0a55kg","channel_link":"Fe28k0a55kg","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2490","video_id":"39","name":"Turkeys | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/2c3RDuYmZFI","channel_link":"2c3RDuYmZFI","created":0,"videos_groups_assoc":null}]
//     },
//     {
//         "id": "5fe340cf798ead6db07d2463",
//         "group_id": "2",
//         "name": "X-Sight Series",
//         "description": "Turkey hunting at fields | ATN Shot Trak HD Gun Camera Turkey hunting at fields | ATN Shot Trak HD Gun Camera",
//         "sort_order": "200",
//         "labels": "rifle scopes, deer hunting, hog hunting, night vision rifle scope, best rifle scope, big game hunting, best hunting scope, night scope, boar hunting, hunting scope, predator hunting, hunting scopes, best ar scopes, pig hunting, varmint hunting, best scope, best scopes",
//         "videos": [{"id":"5fe340d0798ead6db07d2487","video_id":"30","name":"Turkey hunting at fields | ATN Shot Trak HD Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Z2CsmE9t5jk","channel_link":"Z2CsmE9t5jk","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2488","video_id":"31","name":"Texas turkeys & pigs hunting | ATN Shot Trak HD gun camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/MlpHu71WXuI","channel_link":"MlpHu71WXuI","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2489","video_id":"32","name":"Turkey hunting | ATN Shot Trak HD gun camera","description":"https://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fwvu62S4Q1k","channel_link":"Fwvu62S4Q1k","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248a","video_id":"33","name":"Deer walking | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/cHHOJA50huE","channel_link":"cHHOJA50huE","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248b","video_id":"34","name":"Pigs hunting with bow | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/DfH0Sjwmnoo","channel_link":"DfH0Sjwmnoo","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248c","video_id":"35","name":"Duck hunting (ring neck) | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/n8IhRRWlOeY","channel_link":"n8IhRRWlOeY","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248d","video_id":"36","name":"Red Head kill | Duck hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/0ZDNHD9ynGc","channel_link":"0ZDNHD9ynGc","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248e","video_id":"37","name":"Partridge Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/qnbSvkg30qQ","channel_link":"qnbSvkg30qQ","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248f","video_id":"38","name":"Bird Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fe28k0a55kg","channel_link":"Fe28k0a55kg","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2490","video_id":"39","name":"Turkeys | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/2c3RDuYmZFI","channel_link":"2c3RDuYmZFI","created":0,"videos_groups_assoc":null}]
//     },
//     {
//         "id": "5fe340cf798ead6db07d2464",
//         "group_id": "5",
//         "name": "Thermal",
//         "description": "Texas turkeys & pigs hunting | ATN Shot Trak HD gun camera",
//         "sort_order": "300",
//         "labels": "atn, atn thermal, hunting video, thermal scope, shooting, hunting gear, thermal imaging scopes, hunting stuff, thermal imaging scope, thermal rifle scope, thermal sights, thermal optics, thermal vision scopes, thermal hunting scope, thermal night vision",
//         "videos": [{"id":"5fe340d0798ead6db07d2487","video_id":"30","name":"Turkey hunting at fields | ATN Shot Trak HD Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Z2CsmE9t5jk","channel_link":"Z2CsmE9t5jk","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2488","video_id":"31","name":"Texas turkeys & pigs hunting | ATN Shot Trak HD gun camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/MlpHu71WXuI","channel_link":"MlpHu71WXuI","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2489","video_id":"32","name":"Turkey hunting | ATN Shot Trak HD gun camera","description":"https://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fwvu62S4Q1k","channel_link":"Fwvu62S4Q1k","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248a","video_id":"33","name":"Deer walking | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/cHHOJA50huE","channel_link":"cHHOJA50huE","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248b","video_id":"34","name":"Pigs hunting with bow | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/DfH0Sjwmnoo","channel_link":"DfH0Sjwmnoo","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248c","video_id":"35","name":"Duck hunting (ring neck) | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/n8IhRRWlOeY","channel_link":"n8IhRRWlOeY","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248d","video_id":"36","name":"Red Head kill | Duck hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/0ZDNHD9ynGc","channel_link":"0ZDNHD9ynGc","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248e","video_id":"37","name":"Partridge Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/qnbSvkg30qQ","channel_link":"qnbSvkg30qQ","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d248f","video_id":"38","name":"Bird Hunting | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/Fe28k0a55kg","channel_link":"Fe28k0a55kg","created":0,"videos_groups_assoc":null},{"id":"5fe340d0798ead6db07d2490","video_id":"39","name":"Turkeys | ATN Shot Trak HD Hunting Gun Camera","description":"http://www.atncorp.com/smart-hd-gun-camera","featured":"","origin_link":"https://youtu.be/2c3RDuYmZFI","channel_link":"2c3RDuYmZFI","created":0,"videos_groups_assoc":null}]
//     }
// ]
</script>
