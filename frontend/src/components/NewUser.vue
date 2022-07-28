<template>
  <div>
    <error :error="errorMsg" />
    <div class="d-flex">
      <p>
        <strong>First Name <span class="required">*</span></strong>
      </p>
      <input v-model="first_name" type="text" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Last Name <span class="required">*</span></strong>
      </p>
      <input v-model="last_name" type="text" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Email <span class="required">*</span></strong>
      </p>
      <input v-model="email" type="text" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Password <span class="required">*</span></strong>
      </p>
      <input v-model="password" type="password" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Confirm Password <span class="required">*</span></strong>
      </p>
      <input v-model="confirmPassword" type="password" />
    </div>
    <div class="d-flex">
      <p><strong>Wage</strong></p>
      <input v-model="wage" type="number" min="1" />
    </div>
    <div class="d-flex">
      <p>
        <strong>Authorisation Level <span class="required">*</span></strong>
      </p>
      <select v-model="auth_level">
        <option value="1">Employee</option>
        <option value="2">Admin</option>
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
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "NewUser",
  setup(props, context) {
    const axios = inject("axios");
    const first_name = ref(null);
    const last_name = ref(null);
    const email = ref([]);
    const password = ref(null);
    const confirmPassword = ref(null);
    const wage = ref(null);
    const auth_level = ref(null);

    const errors = ref(null);
    const errorMsg = ref(null);

    function save() {
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/user/create`,
          {
            first_name: first_name.value,
            last_name: last_name.value,
            email: email.value,
            password: password.value,
            confirmPassword: confirmPassword.value,
            wage: wage.value,
            auth_level: auth_level.value,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          context.emit("created", response.data);
          errors.value = [];
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errors.value = error.response.data.errors;
          errorMsg.value = "could not create user";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      save,
      first_name,
      last_name,
      email,
      password,
      confirmPassword,
      wage,
      auth_level,
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
