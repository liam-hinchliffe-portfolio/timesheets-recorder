<template>
  <div>
    <error :error="errorMsg" />
    <div class="d-flex">
      <p>
        <strong>Name <span class="required">*</span></strong>
      </p>
      <input v-model="name" type="text" />
    </div>
    <button @click="save">Save</button>

    <div class="errors">
      <p class="error" v-for="(error, index) in errors" v-bind:key="index">
        {{ error[0] }}
      </p>
    </div>
  </div>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "NewDepartment",
  setup(props, context) {
    const axios = inject("axios");
    const name = ref(null);
    const errorMsg = ref(null);

    const errors = ref(null);

    function save() {
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/department/create`,
          {
            name: name.value,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          context.emit("created", response.data);
          errors.value = [];
        })
        .catch((error) => {
          console.warn(error);
          errors.value = error.response.data.errors;
          errorMsg.value = "Could not create department";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      save,
      name,
      errors,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.error {
  color: red;
}
</style>
