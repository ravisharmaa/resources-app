<template>
  <modal name="create-resource" transition="pop-out" :width="modalWidth" :focus-trap="true" :height="500">
    <div class="detail-modal custom-modal">
      <h2 class="mb-0">Create Resource</h2>
      <div class="custom-modal-content">
        <ul v-if="errors.length" class="alert alert-danger list-unstyled">
          <li v-for="err in errors" v-text="err"></li>
        </ul>
        <form action="#" @submit.prevent="submitForm">
          <div class="form-group">
            <label for="">Resource:</label>
            <input type="radio" :value="resourceTypes.htmlResource" v-model="selectedResourceType"> HTML
            <input type="radio" :value="resourceTypes.pdfResource" v-model="selectedResourceType"> PDF
            <input type="radio" :value="resourceTypes.linkResource" v-model="selectedResourceType"> Link
          </div>

          <div v-if="this.selectedResourceType === resourceTypes.htmlResource">
            <div class="form-group col-8">
              <label for="">Title</label>
              <input type="text" class="form-control" v-model="formData.html.title">
            </div>
            <div class="form-group col-8">
              <label for="">Description</label>
              <textarea class="form-control" v-model="formData.html.description"></textarea>
            </div>
            <div class="form-group col-8">
              <label for="">Snippet(Supports Markdown)</label>
              <textarea class="form-control" :value="formData.html.snippet" @input="update"></textarea>
            </div>
          </div>

          <div v-if="this.selectedResourceType === resourceTypes.linkResource">
            <div class="form-group col-8">
              <label for="">Title</label>
              <input type="text" class="form-control needs-validation" v-model="formData.link.title">
            </div>
            <div class="form-group col-8">
              <label for="">Link</label>
              <input type="text" class="form-control" v-model="formData.link.link"></input>
            </div>
            <div class="form-group col-8 mt-3">
              <label for="">Open in new page:</label>
              <input class="align-middle" type="checkbox" id="checkbox" v-model="formData.link.shouldShowOnNewPage">
              <label for="checkbox">{{ formData.link.shouldShowOnNewPage ? "Yes" : "No" }}</label>
            </div>
          </div>

          <div v-if="this.selectedResourceType === resourceTypes.pdfResource">
            <div class="form-group col-8">
              <label for="">Title</label>
              <input type="text" class="form-control" v-model="formData.pdf.title">
            </div>

            <div class="form-group col-8 mt-3">
              <label for="">File</label>
              <input type="file" accept="application/pdf" @change="uploadPdf">
            </div>
          </div>

          <div class="form-group mt-3">
            <button class="btn btn-sm btn-primary">Create</button>
            <button class="btn btn-sm btn-danger" @click.prevent="handleCancel">Cancel</button>
          </div>

        </form>
      </div>
    </div>
  </modal>
</template>
<script>
const MODAL_WIDTH = 500

import resourceTypes from "../const";
import {marked} from 'marked';

export default {
  name: 'CreateResourceComponent',
  data() {
    return {
      modalWidth: MODAL_WIDTH,
      resourceTypes: resourceTypes,
      selectedResourceType: "",
      errors: [],
      formData: {
        html: {
          title: "",
          description: "",
          snippet: ""
        },
        link: {
          title: "",
          link: "",
          shouldShowOnNewPage: true
        },

        pdf: {
          title: "",
          file: ""
        }
      }
    }
  },
  watch: {
    selectedResourceType: function (value) {
      this.errors = []
    }
  },
  created() {
    this.modalWidth = window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    this.selectedResourceType = resourceTypes.htmlResource
  },
  methods: {
    submitForm() {
      let postData = {};
      let formData = null;
      if (this.selectedResourceType.toLowerCase() === 'pdf') {
        formData = new FormData();
        formData.append('type', 'pdf');
        formData.append('properties[title]', this.formData.pdf.title);
        formData.append('properties[file]', this.formData.pdf.file)
      } else {
        postData = {
          type: this.selectedResourceType.toLowerCase(),
          properties: this.formData[this.selectedResourceType.toLowerCase()]
        }
      }
      axios.post('/api/admin/resources',
          this.selectedResourceType.toLowerCase() === 'pdf' ? formData : postData)
          .then((response) => {
            this.$emit('created', response.data)
            Vue.$toast.success('Resource Created', {
              position: 'bottom'
            })
            this.$modal.hide('create-resource');
            this.resetForm();
            this.selectedResourceType = resourceTypes.htmlResource;

          }).catch((error) => {
        if (error.response.status === 422) {
          this.errors = error.response.data
        }
      })

    },

    resetForm() {

      let lowerCaseSelected = this.selectedResourceType.toLowerCase();
      this.formData[lowerCaseSelected].title = "";
      if (lowerCaseSelected === 'html') {
        this.formData[lowerCaseSelected].description = "";
        this.formData[lowerCaseSelected].snippet = "";
      }

      if (lowerCaseSelected === 'link') {
        this.formData[lowerCaseSelected].link = "";
      }

      if (lowerCaseSelected === 'pdf') {
        this.formData[this.selectedResourceType].file = "";
      }


    },

    handleCancel() {
      this.resetForm()
      this.$modal.hide('create-resource')
    },

    uploadPdf(event) {
      this.formData.pdf.file = event.target.files[0]
    },

    update: _.debounce(function (e) {
      this.formData.html.snippet = e.target.value;
    }, 300)

  },
  computed: {
    compiledMarkdown: function () {
      return marked(this.formData.html.snippet, {sanitize: true});
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

.custom-modal-content ul.alert {
  padding: 0.5rem;
}

.detail-modal.custom-modal h2 {
  text-align: center;
  padding: 0.5rem 1rem;
  background-color: #ccc;
  text-transform: uppercase;
  font-size: 1.3rem;
  font-weight: 700;
}


.htm-tag {
  border: 1px solid #ccc;
  padding: 1rem;
  box-shadow: 0 0 4px 0 #ccc;
  overflow-y: scroll;
  max-height: 130px;
}

</style>