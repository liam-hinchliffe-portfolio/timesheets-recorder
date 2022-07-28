<template>
  <div>
    <error :error="errorMsg" />
    <div class="d-flex">
      <p>
        <strong>Name <span class="required">*</span></strong>
      </p>
      <input v-model="name" type="text" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Reference <span class="required">*</span></strong>
      </p>
      <input v-model="reference" type="text" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Department <span class="required">*</span></strong>
      </p>
      <select v-model="department">
        <option
          v-for="(department, index) in departments"
          v-bind:key="index"
          :value="department.id"
        >
          {{ department.name }}
        </option>
      </select>
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
import { defineComponent, inject, onMounted, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "NewClient",
  setup(props, context) {
    const axios = inject("axios");
    const name = ref(null);
    const reference = ref(null);
    const departments = ref(null);
    const department = ref(null);

    const errorMsg = ref(null);

    const errors = ref(null);

    function save() {
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/client/create`,
          {
            name: name.value,
            reference: reference.value,
            department_id: department.value,
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
          errorMsg.value = "Could not create client";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function getDepartments() {
      axios
        .get(
          `https://oasisstudio.uk/timesheets/public/api/departments`,
          {
            withCredentials: true,
          }
        )
        .then((response) => (departments.value = response.data))
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the departments";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    onMounted(() => getDepartments());

    return {
      save,
      name,
      reference,
      department,
      errors,
      getDepartments,
      departments,
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
