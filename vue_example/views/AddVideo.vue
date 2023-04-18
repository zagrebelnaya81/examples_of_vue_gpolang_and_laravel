<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-5 col">
        <form class="">
          <div class="form-group">
            <input type="text" v-model="video.origin_link" placeholder="Origin_link" @blur="GetCannelLink" :originlink="link_origin"/>
          </div>
          <Link :link="link_channel" @link='onLink'/>
          <div v-if="link_channel" class="form-group">
            <youtube-iframe
                class="border border-primary"
                :video-id="link_channel"
                player-width="100%"
                player-height="300vw"
                :no-cookie="true"
            ></youtube-iframe>
          </div>
          
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1" v-model="feat"   @click="setFeat">
              <label class="form-check-label" for="inlineCheckbox1">Featured</label>
            </div>
          </div>
          <div class="form-group">
            <input class="form-control form-control-lg" type="text" placeholder="Title" v-model="video.name" required min="3">
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="3" v-model="video.description" placeholder="Description"></textarea>
          </div>
          <div class="form-group groups border pl-3 pt-3" v-if="groups">
                  <div class="form-row align-items-center mb-2" v-for="group in groups" :key="group.id">
                    <div class="col-auto">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" :id="group.id" :value="group" v-model="group.assoc.checked" :name="group.id">
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
            <button type="button" class="mr-2 btn btn-primary" @click="addvideo">
              Add video
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
import Link from '@/components/Link.vue'
// import { VueDraggableNext } from 'vue-draggable-next'
export default {
  order: 3,
  data() {
    return {
      error: false,
      removed: false,
      feat: false,
      groups: false,
      link_channel:"",
      link_origin:"",
      groups_selected : [],
      video: {
        channel_link:"",
        origin_link: '',
        id: '',
        name: '',
        featured: '',
        description:"",
        videos_groups_assoc: [],
      },
    };
  },
  components: {
    Link,
    // draggable: VueDraggableNext
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
    this.getGroups();
  },
  methods: {

    ...mapMutations([
      "setLoading",
      "setError",
      "setMsg"
    ]),

    GetCannelLink(){
      // this.video.channel_link = this.video.origin_link;
      // this.link_channel = this.video.origin_link;
      // this.link_origin = this.video.origin_link;

      if(this.video.origin_link && this.video.origin_link.indexOf('&')!=-1) {
        console.log(this.video.origin_link.indexOf('&'))
        var pos = this.video.origin_link.indexOf('?v=');
        pos = pos+3;
        var pos1 = this.video.origin_link.indexOf('&');
        var pos2 = this.video.origin_link.length-pos1;
        console.log(this.video.origin_link.length);
        this.video.channel_link = this.video.origin_link.slice(pos, -pos2);
        this.link_channel = this.video.origin_link.slice(pos, -pos2);
      } else if (this.video.origin_link.indexOf('?v=')==-1){
        var pos5 = this.video.origin_link.indexOf('.be');
        pos5 = pos5+4;
        this.video.channel_link = this.video.origin_link.slice(pos5);
        this.link_channel = this.video.origin_link.slice(pos5);
        console.log(this.link_channel)
      } else {
        var pos4 = this.video.origin_link.indexOf('?v=');
        pos4 = pos4+3;
        this.video.channel_link = this.video.origin_link.slice(pos4);
        this.link_channel = this.video.origin_link.slice(pos4);
      }
    },

    onLink(data){
      console.log('child component said subject', data);
      this.link_channel = data;
    },
    setFeat() {
      this.feat =!this.feat;
      console.log(this.feat);
    },

    addvideo() {
      let video = { ...this.video }

      if( this.feat ==true){
        video.featured = "1";
      } else {
        video.featured = "0";
      }
      this.setLoading(true)

      fetch(`/api/videos/add`, {
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
        
        //add groups
        this.groups_selected = []
      
      
        for(var i=0; i<this.groups.length; i++) {
          if (this.groups[i].assoc.checked == true) {     
   
              this.groups[i].sort_order = parseInt(this.groups[i].assoc.sort_order)
              this.groups_selected.push(this.groups[i])           
          }
        }
      

        this.addVideoGroup(this.groups_selected, response.video_id);

        this.setMsg('Video ADDED. Redirecting to Home');

        this.remove = true;
        setTimeout(() => router.push('/'), 2000)
      })
    },
    addVideoGroup(groups, video_id){
          console.log(groups)
          fetch(`/api/video/addgrouptovideo`, {
            method: 'POST',
            async:false,
            headers: {
              'Atn-Token': this.accessToken
            },
            body: JSON.stringify({
              "video_id": parseInt(video_id),
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
    getGroups() {
      this.setLoading(true)

      fetch(`/api/videosgroup/list`, {
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

        this.groups = response;
        this.groups.map(gr => {
          let isFinded = this.video.videos_groups_assoc.find(a => a.group_id == gr.group_id);
          gr.assoc = {
            checked: false,
            group_id: gr.group_id,
            sort_order: 1000
          };
          if (!isFinded) return;
          gr.assoc.checked = true;
          console.log(isFinded.sort_order)
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