<template>
  <div class="project">
    <error :error="errorMsg" />
    <p><strong>Name:</strong> {{ project.name }}</p>
    <p><strong>Reference:</strong> {{ project.reference }}</p>
    <a :href="`/timesheets/client/project/${project.id}/view`">
      <button>View</button>
    </a>
    <div v-if="deleteConfirmation">
      <button @click="deleteProject">Confirm Deletion</button
      ><button @click="deleteConfirmation = false">Cancel</button>
    </div>
    <button v-else @click="deleteConfirmation = true">Delete</button>
  </div>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "Project",
  props: {
    project: { type: Object, required: true },
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);
    const errorMsg = ref(null);

    function deleteProject() {
      axios
        .delete(
          `https://oasisstudio.uk/timesheets/public/api/project/${props.project.id}/delete`,
          {
            withCredentials: true,
          }
        )
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.project.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the project";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return { deleteProject, deleteConfirmation, errorMsg };
  },
});
</script>

<style scoped>
.project {
  padding: 1rem;
  background-color: white;
  margin-right: 1rem;
  margin-top: 1rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
}

a {
  color: unset;
}
</style>
