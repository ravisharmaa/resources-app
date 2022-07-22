<template>
  <div class="detail-modal custom-modal">
    <h2>{{resource.properties.title}}</h2>
    <div class="custom-modal-content">
    <h3>{{resource.properties.description}}</h3>

    <p class="htm-tag" v-html="snippet"></p>
    <button class="btn btn-secondary btn-sm" @click="copy">
      Copy Snippet
    </button>
    </div>
    <button @click="$modal.hideAll()" class="action-close">X</button>
  </div>
</template>

<script>
import {marked} from 'marked';
export default {
  name: "ResourceDetailComponent",
  props: ['resource'],
  data() {
    return {
      snippet: marked(this.resource.properties.snippet)
    }
  },

  methods: {
    copy() {
      try {
        let tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        let tempString = this.resource.properties.snippet
        tempInput.value = tempString.replace('```','').replace('```','');
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert('Snippet added to clipboard')
      } catch (err) {
        console.log(err)
      }
    }
  }
}
</script>

<style scoped>

.detail-modal.custom-modal {
  position: relative;
}

.custom-modal-content {
  padding: 1rem;
}
.detail-modal.custom-modal h2 {
  text-align: center;
  padding: 0.5rem 1rem;
  background-color: #ccc;
  text-transform: uppercase;
  font-size: 1.3rem;
  font-weight: 700;
}

.custom-modal .htm-tag {
  border: 1px solid #ccc;
  padding: 1rem;
  box-shadow: 0 0 4px 0 #ccc;
  overflow-y: scroll;
  max-height: 130px;
}

.action-close {
  position: absolute;
  top: 0;
  right: 0;
  background-color: #5c636a;
  width: 40px;
  height: 40px;
  color: #fff;
  font-weight: 700;
}

</style>