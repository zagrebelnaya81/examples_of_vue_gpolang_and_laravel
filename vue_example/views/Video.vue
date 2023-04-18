<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-5 col">
        <form class="" v-if="!removed && video">
          <div class="form-group">
            <span class="badge badge-primary mb-2">
              {{video.channel_link}}
            </span>
            <div>
                <span class="badge badge-info">
                    video ID: {{video.video_id}}
                </span>
            </div>
          </div>
          <div class="form-group">
            <a :href="video.origin_link" target="_blank" type="button" >{{video.origin_link}}</a>
          </div>
          <div class="form-group">
            <youtube-iframe
                class="border border-primary"
                :video-id="video.channel_link"
                player-width="100%"
                player-height="300vw"
                :no-cookie="true"
            ></youtube-iframe>
          </div>
          
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1" v-model="feat" @click="setFeat">
              <label class="form-check-label" for="inlineCheckbox1">Featured</label>
            </div>
          </div>
          <div class="form-group">
            <input class="form-control form-control-lg" type="text" placeholder="Title" v-model="video.name" required min="3">
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="3" v-model="video.description"></textarea>
          </div>


          <div class="form-group groups border pl-3 pt-3" v-if="groups">
                  <div class="form-row align-items-center mb-2" v-for="group in groups" :key="group.id">
                    <div class="col-auto">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" :id="group.id" :value="group" v-model="group.assoc.checked" :name="group.id">
                        <!--                  @change="ChangeGroup($event, group.assoc.group_id, group.assoc.sort_order)"-->
                        <label class="form-check-label" :for="group.id">{{group.name}}</label>
                      </div>
                    </div>
                    <div class="col-auto">
                        <input
                          name="name"
                          type="number"
                          class="form-control form-control-sm"
                          v-model="group.assoc.sort_order"
                        />
                    </div>
                  </div>
          </div>

          <div role="toolbar" class="btn-toolbar">
            <button type="button" class="mr-2 btn btn-primary" @click="updatevideo">
              Update video
            </button>
            <button type="button" class="btn btn-danger" @click="deletevideo = true" v-if="!deletevideo">
              DELETE
            </button>
            <button type="button" class="btn btn-success mr-2" @click="deletevideo = false" v-if="deletevideo">
              Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="setRemove" v-if="deletevideo">
              DELETE video
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style src="@/assets/less/form.less" lang="less"></style>
<style lang="less" scoped>
  .groups {
    height: auto;
    min-height: 100px;
    overflow: hidden;
    overflow-y: scroll;
    background: white;
    border-radius: 0.25em;
    color: black;
    [type=number] {
      max-width: 5em;
    }
  }
  .flip-list-move {
    transition: transform 0.5s;
  }
  .no-move {
    transition: transform 0s;
  }
  .ghost {
    opacity: 0.5;
    background: #c8ebfb;
  }
  .list-group {
    min-height: 20px;
    width:100%;
  }
  .list-group-item {
    cursor: move;
  }
  .list-group-item i {
    cursor: pointer;
  }
</style>
<script>
import router from '../router';
import { mapState, mapMutations } from "vuex";
// import { VueDraggableNext } from 'vue-draggable-next'
export default {
  order: 3,
  // components: {
  //   draggable: VueDraggableNext
  // },
  data() {
    return {
      videoRouteId: this.$route.params.videoRouteId,
      deletevideo: false,
      error: false,
      removed: false,
      feat: false,
      groups: false,
      video: false,
      groups_selected : []
    };
  },
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
  mounted() {
    this.getvideo();
  },
  methods: {

    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),
    removevideo() {
      this.setLoading(true)

      fetch(`/api/videos/remove/${this.videoRouteId}`, {
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

        this.setMsg('Video REMOVED. Redirecting to Home');

        this.remove = true;
        setTimeout(() => router.push('/'), 2000)
      })
    },
    getvideo() {
      this.setLoading(true)

      fetch(`/api/videos/getcurrentvideo/${this.videoRouteId}`, {
        method: 'GET',
        headers: {
            'Atn-Token': this.accessToken
        }
      })
      .then(async r => {
        console.log(r)
        let response = await r.json()
        if (response && response.error) {
          this.setLoading(false)
          this.setError(response)
          return
        }
        this.setLoading(false)
        this.setMsg('Video details received');

        this.video = response;
        console.log(this.video);

        if(this.video.featured =="1"){
          this.feat = true;
        } else {
          this.feat = false;
        }
        // this.feat = 'featured' in this.video && false
        if (!this.video.videos_groups_assoc){
          this.video.videos_groups_assoc =[];
        }
  
        this.getGroups();
      })
    },
    updatevideo() {
      let video = { ...this.video }
     
      if (this.feat) {
        video.featured = "1";
      } else {
        video.featured = "0";
      }


     
      this.setLoading(true)
      fetch('/api/videos/update', {
        method: 'POST',
        headers: {
            'Atn-Token': this.accessToken
        },
          body: JSON.stringify(video)
      })
      .then(async r => {
        let response = await r.json()

        if (response.error) {
          this.setLoading(false)
          this.setError(response)
          return
        }
        this.groups_selected = []
        
        //add groups
      
        for(var i=0; i<this.groups.length; i++) {
          if (this.groups[i].assoc.checked == true) {     
   
              this.groups[i].sort_order = parseInt(this.groups[i].assoc.sort_order) 
              this.groups_selected.push(this.groups[i])           
          }
        }
      

        this.addVideoGroup(this.groups_selected);
              
        this.setLoading(false)
        this.setMsg('Video has been updated');
   
        router.push({ name: 'Video', params: { videoRouteId: video.video_id } })
      })
    },
 
    addVideoGroup(groups){
          console.log(groups)
          fetch(`/api/video/addgrouptovideo`, {
            method: 'POST',
            async:false,
            headers: {
              'Atn-Token': this.accessToken
            },
            body: JSON.stringify({
              "video_id": parseInt(this.video.video_id),
              "videos_groups_assoc": groups
              
            }),
          })
              .then(async r => {
                let response = await r.json()
                if (response.error) {
                  this.setLoading(false)
                  this.setError(response)

                  return
                }
              })        
    },

    setRemove() {
      this.removevideo()
    },
    setFeat() {
      this.feat =!this.feat;
      console.log(this.feat);
    },
    DeleteAllGroups(){
      let video = { ...this.video }
      fetch(`/api/video/deleteallgroupsfromvideo/`+video.video_id, {
        method: 'GET',
        headers: {
          'Atn-Token': this.accessToken
        },
        // body: JSON.stringify({
        //   "video_id": parseInt(video.video_id)
        // }),
      })
          .then(async r => {
            let response = await r.json()
            if (response.error) {
              this.setLoading(false)
              this.setError(response)
              return
            }
          })
    },
    ChangeGroup(e, group_id, sort_order){
      let video = { ...this.video }
      if(e.target.checked == false){
        console.log(JSON.stringify({
          "video_id": parseInt(video.video_id),
          "videos_groups_assoc": [{
            group_id: parseInt(group_id),
            sort_order: parseInt(sort_order),
          }]
        }));

        //remove old group
        fetch(`/api/video/deletegroupfromvideo`, {
          method: 'POST',
          headers: {
            'Atn-Token': this.accessToken
          },
          body: JSON.stringify({
            "video_id": parseInt(video.video_id),
            "videos_groups_assoc": [{
              group_id: parseInt(group_id),
              sort_order: parseInt(sort_order)
            }]
          }),
        })
            .then(async r => {
              let response = await r.json()
              if (response.error) {
                this.setLoading(false)
                this.setError(response)

                return
              }
            })
      }
    },
    getGroups() {
      this.setLoading(true)

      fetch(`/api/videosgroup/list`, {
        method: 'GET',
        async:false,
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

        this.groups = response;
        this.groups.map(gr => {
          let isFinded = this.video.videos_groups_assoc.find(a => a.group_id == gr.group_id);
          gr.assoc = {
            checked: false,
            group_id: parseInt(gr.group_id),
            sort_order: parseInt(gr.sort_order)
          };
          if (!isFinded) return;
          gr.assoc.checked = true;
          gr.assoc.sort_order = parseInt(isFinded.sort_order);
        });
      })
    },
    log(event) {
      console.log(event)
    },
  },
};

</script>