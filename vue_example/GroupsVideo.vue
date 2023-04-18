<template>
  <div>
    <div v-for="(group, ind) in groupsvideo" :key="group.id">
      <div class="card-transparent border rounded border-secondary text-white mb-3 group">
        <nav class="navbar navbar-expand-lg bg-secondary">
          <span class="navbar-brand">{{group.groupid[0].name}}</span>
          <button class="mr-2 btn btn-primary" @click="gotoGroup(group._id)">edit</button>
        </nav>
        <div class="card-body description">
          <p class="font-weight-normal">{{group.groupid[0].description}}</p>
          <span class="badge badge-secondary mr-2 mb-1">{{group.groupid[0].labels}}
          </span>
        </div>
        <div class="videos" :key="componentKey">
              <div class="item" v-for="(video, index) in  group.videos" :key="video.id" :id="group.id" :style="`background-image: url(http://img.youtube.com/vi/${video.channel_link}/mqdefault.jpg)`">
                <div class="title" @click="gotoVideo(video.video_id)">{{video.name}}</div>
                <svg class="strelka-left-2" viewBox="0 0 9 14" @click="move(index,index-1, ind)" v-if="index!=0">
                  <path class="svg-strelka" d="M6.660,8.922 L6.660,8.922 L2.350,13.408 L0.503,11.486 L4.813,7.000 L0.503,2.515 L2.350,0.592 L8.507,7.000 L6.660,8.922 Z" ></path>
                </svg>

                <!-- Стрелка вправо -->
                <svg class="strelka-right-2" viewBox="0 0 9 14" @click="move(index,index+1, ind)" v-if="index!=(group.videos.length-1)">
                  <path class="svg-strelka" d="M6.660,8.922 L6.660,8.922 L2.350,13.408 L0.503,11.486 L4.813,7.000 L0.503,2.515 L2.350,0.592 L8.507,7.000 L6.660,8.922 Z" ></path>
                </svg>
              </div>
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
  flex-direction: row;
  position: relative;
  //cursor: pointer;
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
    cursor: pointer;
    text-shadow: 2px 1px 6px black, 2px 1px 6px black;
  }
}

.strelka-left-2,
.strelka-right-2,
.strelka-top-2,
.strelka-bottom-2 {
  //margin: 20px 8px;
  //width: 80px;
  //height: 80px;
  margin-top: 131px;
  width: 21px;
}
.strelka-left-2 path,
.strelka-right-2 path,
.strelka-top-2 path,
.strelka-bottom-2 path {
  fill: #BFE2FF;
  stroke-width: .6;
  stroke: #337AB7;
  transition: fill 0.5s ease-out;
  cursor: pointer;
}
.strelka-left-2 {
  transform: rotate(180deg);
}
//.strelka-left-2 {
//  //margin-left: 314px;
//}
.strelka-right-2 {
  margin-left: 17px;
}
.strelka-top-2 {
  transform: rotate(270deg);
}
.strelka-bottom-2 {
  transform: rotate(90deg);
}
.strelka-left-2 path:hover,
.strelka-right-2 path:hover,
.strelka-top-2 path:hover,
.strelka-bottom-2 path:hover {
  fill: #337AB7;
}
</style>

<script>
import router from '../router';
// import { VueDraggableNext } from 'vue-draggable-next'
import { mapState, mapMutations } from "vuex";
Array.prototype.move = function(from, to, ind, accesstoken) {
  this.map(function(arr) {
    arr.old_sort_order = arr.sort_order;
  })

  this.splice(to, 0, this.splice(from, 1)[0]);

  this.map(function(arr, index) {
    arr.sort_order = index;
    //update videos one by one
    // let video = { ...arr }
  
    // fetch('/api/video/deletegroupfromvideo', {
    //   method: 'POST',
    //   async:false,
    //   headers: {
    //     'Atn-Token': accesstoken
    //   },
    //   body: JSON.stringify({
    //     "video_id": parseInt(video.video_id),
    //     "videos_groups_assoc": [{
    //       group_id: parseInt(ind),
    //       sort_order: parseInt(arr.old_sort_order)
    //     }]
    //   }),
    // })
    //     .then(async r => {
    //       let response = await r.json()
    //       if (response.error) {
    //         console.log(response.error)
    //       }
    //     })
  });

//revert array for right sort
  this.reverse();
  this.map(function(arr, index) {
    console.log('add groups')
    arr.sort_order = index;
    console.log(arr)
    //update videos one by one
    let video = { ...arr }
    console.log("video")   
    console.log(video)    
      
    fetch('/api/video/addgrouptovideoforsortvideo', {
      method: 'POST',
      headers: {
        'Atn-Token': accesstoken
      },
      body: JSON.stringify({
        "video_id": parseInt(video.video_id),
        "videos_groups_assoc_for_delete": [{
          group_id: parseInt(ind),
          sort_order: parseInt(arr.sort_order),
          old_sort_order: parseInt(arr.old_sort_order)
        }]
      }),
    })
        .then(async r => {
          let response = await r.json()
          if (response.error) {
              console.log(response.error)
          }
        })
  });
  console.log(this)
  this.reverse();
  return this;
};
export default {
  name: "GroupsVideo",
  props: ["groupsvideo"],
  order: 100,
  computed: {
    ...mapState([
      'accessToken',
    ]),
    dragOptions() {
      return {
        animation: 0,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      };
    }
  },
  data() {
    return {
      componentKey: 0,
    };
  },
  methods: {
    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    move(from, to, ind) {
      console.log(from);
      console.log(ind)
     
      console.log(this.groupsvideo[ind].videos);
      this.groupsvideo[ind].videos.move(from, to, this.groupsvideo[ind].groupid[0].group_id, this.accessToken);
    },
    gotoVideo(id) {
      router.push({ name: 'Video', params: { videoRouteId: id } })
    },

    gotoGroup(id) {
      router.push({ name: 'Group', params: { groupRouteId: id } })
    },
  
    forceRerender() {
      this.componentKey += 1;
    }
  }
};
</script>