<template>
  <div class="page-wrapper mt-2">
    <div class="card">
      <div class="card-header">
        <button class="btn btn-sm btn-primary" @click="showModal">Create Resource</button>
      </div>
      <create-resource-component @created="updateResources"/>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
          <tr>
            <th scope="col">Resource</th>
            <th scope="col">Action</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="resource in appResources">
            <td v-text="resource.name"></td>
            <td>
              <a href="#" @click.prevent="showEditModal(resource)" class="btn btn-secondary btn-sm">Edit</a>
              <button @click.prevent="remove(resource)" class="btn btn-danger btn-sm">Delete</button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import CreateResourceComponent from "./CreateResourceComponent";
import UpdateResourceComponent from "./UpdateResourceComponent";

export default {
  props: ['resources'],
  name:'ResourceComponent',
  components: {
    CreateResourceComponent,
    UpdateResourceComponent
  },
  data() {
    return {
      appResources: this.resources
    }
  },
  methods: {
    showModal() {
      this.$modal.show('create-resource')
    },

    updateResources(params) {
      this.appResources = params
    },

    remove(resource) {
      if (confirm("Do you really want to delete?")) {
        axios.delete('/api/admin/resources/'+ resource.id).then((response) => {
          this.appResources = response.data
          Vue.$toast.success('Resource Deleted', {
            // override the global option
            position: 'bottom'
          })
        }).catch(err => {
          console.log(err)
        })
      }
    },

    showEditModal(resource) {
      this.$modal.show(UpdateResourceComponent, {
        'resource': resource
      }, {
        width: 800,
        height: 550
      })
    }
  }
}
</script>

