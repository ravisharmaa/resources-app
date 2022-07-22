<template>
  <div>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card mt-2">
            <div class="card-header">
              Resources
            </div>
            <div class="card-body">
              <table class="table table-striped table-responsive table-hover">
                <thead>
                <tr>
                  <th>Resource</th>
                  <th>&nbsp;</th>
                  <th>Detail</th>

                </tr>
                </thead>
                <tbody>
                <tr v-for="resource in resources">
                  <td v-text="resource.name.toUpperCase()"></td>
                  <td>&nbsp;</td>
                  <td v-if="resource.name === 'pdf'">
                    <a href="" @click.prevent="download(resource)" class="">Download</a>
                  </td>
                  <td v-if="resource.name === 'html'">
                    <button class="btn btn-primary btn-sm" @click="viewDetail(resource)">View Details</button>
                  </td>
                  <td v-if="resource.name === 'link'">
                    <a :href="resource.properties.link" :target="resource.properties.shouldShowOnNewPage ? '_blank':''">Link</a>
                  </td>
                </tr>
                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ResourceDetailComponent from "./ResourceDetailComponent";


export default {
  name: "VisitorComponent",
  props: ['resources'],
  methods: {
    download(resource) {
      axios.get('/storage/pdf_resources/' + resource.properties.path, {responseType: 'arraybuffer'})
          .then(response => {
            let blob = new Blob([response.data], {type: 'application/pdf'})
            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = resource.properties.title + ".pdf";
            link.click()
          })
    },

    viewDetail(resource) {
      this.$modal.show(ResourceDetailComponent, {
        'resource': resource
      })

    }
  }
}
</script>

<style scoped>

</style>