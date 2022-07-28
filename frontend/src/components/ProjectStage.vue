<template>
  <div class="project-stage">
    <error :error="errorMsg" />
    <p>{{ projectStage.name }}</p>
    <div v-if="deleteConfirmation">
      <button @click="deleteProjectStage">Confirm Deletion</button
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
  name: "ProjectStage",
  props: {
    projectStage: { type: Object, required: false },
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);

    const errorMsg = ref(null);

    function deleteProjectStage() {
      axios
        .delete(
          `https://oasisstudio.uk/timesheets/public/api/projectStage/${props.projectStage.id}/delete`,
          {
            withCredentials: true,
          }
        )
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.projectStage.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the project stage";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      deleteProjectStage,
      deleteConfirmation,
      errorMsg
    };
  },
});
</script>

<style scoped>
.project-stage {
  padding: 0.5rem;
  background-color: white;
  margin-left: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
}

.project-stage:first-child {
  margin-left: unset;
}
</style>
