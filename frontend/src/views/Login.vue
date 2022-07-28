<template>
  <div class="home">
    <error :error="errorMsg" />
    <header>
      <img :src="require('@/assets/logo_oasis_studio_vhr.png')" />
    </header>
    <h2>Timesheets</h2>
    <div class="body">
      <label for="email">Email <span class="required">*</span></label>
      <input v-model="email" type="text" name="email" required />
      <label for="password">Password <span class="required">*</span></label>
      <input v-model="password" type="password" name="password" required />
      <p class="error" v-if="invalid">The credentials entered are invalid</p>
      <button @click="login">Login</button>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import { inject, ref } from "@vue/runtime-core";
import Error from "../components/Error.vue";

export default {
  name: "Login",
  components: { Error },
  setup() {
    const axios = inject("axios");

    const email = ref(null);
    const password = ref(null);
    const invalid = ref(null);
    const errorMsg = ref(null);

    function login() {
      axios
        .post(
          "https://oasisstudio.uk/timesheets/public/api/login",
          {
            email: email.value,
            password: password.value,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          // Store the user data in local storage for quick access and frontend authentication
          if (response && response.data && response.data.token)
            window.localStorage._token = response.data.token;

          if (response && response.data && response.data.user)
            window.localStorage._user = JSON.stringify(response.data.user);

          window.location.href = "/timesheets/client/home";

          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          invalid.value = true;
          errorMsg.value = "could not login";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      login,
      email,
      password,
      invalid,
      errorMsg,
    };
  },
};
</script>


<style scoped>
.error {
  font-size: 0.75rem;
  color: red;
  margin: 0;
  margin-bottom: 1rem;
}

h2 {
  text-align: center;
}

img {
  max-width: 20rem;
}

header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 2rem 0;
}

button {
  margin-top: 1rem;
}
</style>