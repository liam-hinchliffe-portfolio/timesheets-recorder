<template>
  <div class="new-project-stage">
    <h5>New Project Stage</h5>
    <div class="d-flex">
      <error :error="errorMsg" />
      <div class="d-flex">
        <p>
          <strong>Name <span class="required">*</span></strong>
        </p>
        <input v-model="name" type="text" />
      </div>

      <div class="errors">
        <p class="error" v-for="(error, index) in errors" v-bind:key="index">
          {{ error[0] }}
        </p>
      </div>

      <button @click="create">Create</button>
    </div>
  </div>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "NewProjectStage",
  props: {
    project: { type: Object, required: true },
  },
  setup(props, context) {
    const axios = inject("axios");
    const name = ref(null);
    const errors = ref(null);
    const errorMsg = ref(null);
    function create() {
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/projectStage/create`,
          {
            name: name.value,
            project_id: props.project.id,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          context.emit("created", response.data);
          name.value = null;
          errors.value = null;
          errorMsg.value = null;
        })
        .catch((error) => {
          errors.value = error.response.data.errors;
          errorMsg.value = "could not create project stage";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      name,
      errors,
      create,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.error {
  color: red;
}
.new-project-stage {
  background-color: #f2f2ee;
  text-align: center;
}
h5 {
  margin: 0.75rem 0 0.5rem 0;
}
</style>
