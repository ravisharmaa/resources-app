<template>
  <div class="detail-modal custom-modal">
    <h2>Update Resource</h2>
    <div class="custom-modal-content">
      <ul v-if="errors.length" class="alert alert-danger list-unstyled">
        <li v-for="err in errors" v-text="err"></li>
      </ul>
      <form action="#" @submit.prevent="submitForm">
        <div v-if="resource.name === resourceTypes.htmlResource.toLowerCase()">
          <div class="form-group col-8">
            <label for="">Title</label>
            <input type="text" class="form-control" v-model="formData.html.title">
          </div>

            <div class="form-group col-8">
              <label for="">Description</label>
              <textarea class="form-control" v-model="formData.html.description"></textarea>
            </div>

            <div class="form-group col-8">
              <label for="">Snippet</label>
              <textarea class="form-control" v-model="formData.html.snippet"></textarea>
            </div>
        </div>

        <div v-if="resource.name === resourceTypes.pdfResource.toLowerCase()">
          <div class="form-group col-8">
            <label for="">Title</label>
            <input type="text" class="form-control" v-model="formData.pdf.title">
          </div>

            <div class="form-group col-8 mt-2">
              <label for="">File</label>
              <input type="file"  @change="uploadPdf">
              <br>
              <span class="help-text">*The pdf file shall not be updated if you dont wish to provide one.</span>
            </div>

        </div>

        <div v-if="resource.name === resourceTypes.linkResource.toLowerCase()">
          <div class="form-group col-8">
            <label for="">Title</label>
            <input type="text" class="form-control" v-model="formData.link.title">

            <div class="form-group col-8">
              <label for="">Link</label>
              <input type="text" class="form-control" v-model="formData.link.link"></input>
            </div>

            <div class="form-group col-8 mt-3">
              <label for="">Open in new page:</label>
              <input type="checkbox" id="checkbox" v-model="formData.link.shouldShowOnNewPage">
              <label for="checkbox">{{ formData.link.shouldShowOnNewPage ? "Yes" : "No" }}</label>
            </div>
          </div>
        </div>


        <div class="form-group mt-5">
          <button class="btn btn-sm btn-primary">Update</button>
          <button class="btn btn-sm btn-danger" @click.prevent="hideModal">Cancel</button>
        </div>
      </form>
    </div>

  </div>
</template>

<script>
import resourceTypes from "../const";
export default {
  name: "UpdateResourceComponent",
  props: ['resource'],
  data() {
    return {
      resourceTypes,
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

  methods: {
    hideModal() {
      this.$modal.hideAll()
    },

    uploadPdf(event) {
      this.formData.pdf.file = event.target.files[0]
    },

    submitForm() {
      let postData = {};
      let formData = null;
      if (this.resource.name === "pdf") {
        formData = new FormData();
        formData.append('type', 'pdf');
        formData.append('_method', 'put');
        formData.append('properties[title]', this.formData.pdf.title);
        formData.append('properties[file]', this.formData.pdf.file)
      } else {
        postData = {
          type: this.resource.name,
          "_method": 'put',
          properties: this.formData[this.resource.name]
        }
      }
      axios.post('/api/admin/resources/'+ this.resource.id,
          this.resource.name === "pdf" ? formData : postData
      ).then((response) => {
        Vue.$toast.success('Resource Updated', {
          // override the global option
          position: 'bottom'
        })
        this.$modal.hideAll();
      }).catch((err) => {
        this.errors = err.response.data
      })
    },
  },
  mounted() {
    if (this.resource.name === "html") {
      this.formData[this.resource.name].title = this.resource.properties.title;
      this.formData[this.resource.name].description = this.resource.properties.description;
      this.formData[this.resource.name].snippet = this.resource.properties.snippet;
    }

    if (this.resource.name === "link") {
      this.formData[this.resource.name].title = this.resource.properties.title;
      this.formData[this.resource.name].link = this.resource.properties.link;
      this.formData[this.resource.name].shouldShowOnNewPage = this.resource.properties.shouldShowOnNewPage;
    }

    if (this.resource.name === "pdf") {
      this.formData[this.resource.name].title = this.resource.properties.title;
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

.custom-modal-content ul.alert {
  padding: 0.5rem;
}

</style>