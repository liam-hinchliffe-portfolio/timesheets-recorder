<template>
  <header class="d-flex">
    <error :error="errorMsg" />
    <a href="/timesheets/client/home">
      <img
        src="https://oasisstudio.uk/v2/wp-content/uploads/2021/07/logo_oasis_studio_vhr.png"
        alt=""
      />
    </a>

    <div class="admin-nav d-flex" v-if="authUser.auth_level == 2">
      <a href="/timesheets/client/home">
        <button :class="{ selected: selectedTab == 'home' }">Home</button>
      </a>
      <a href="/timesheets/client/projects">
        <button :class="{ selected: selectedTab == 'projects' }">
          Projects
        </button>
      </a>
      <a href="/timesheets/client/users">
        <button :class="{ selected: selectedTab == 'users' }">Users</button>
      </a>
      <a href="/timesheets/client/clients">
        <button :class="{ selected: selectedTab == 'clients' }">Clients</button>
      </a>
      <a href="/timesheets/client/departments">
        <button :class="{ selected: selectedTab == 'departments' }">
          Departments
        </button>
      </a>
    </div>
    <div class="d-flex">
      <p>Hello, {{ authUser.first_name }} {{ authUser.last_name }}</p>
      <img
        class="logout-btn"
        @click="logout"
        src="@/assets/logout.svg"
        alt=""
      />
    </div>
  </header>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  name: "HeaderNav",
  props: {
    selectedTab: { type: String, required: false },
  },
  components: {
    Error,
  },
  setup() {
    const axios = inject("axios");
    const authUser = JSON.parse(window.localStorage._user);
    const errorMsg = ref(null);

    function logout() {
      axios
        .get("https://oasisstudio.uk/timesheets/public/api/logout", {
          withCredentials: true,
        })
        .then(() => {
          window.localStorage._token = null;
          window.localStorage._user = null;
          window.location.href = "/timesheets/client/home";
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not log you out";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      authUser,
      logout,
      errorMsg,
    };
  },
});
</script>

<style scoped>
header {
  background-color: white;
  position: sticky;
  top: 0;
  padding: 1rem 2rem;
  justify-content: space-between !important;
  z-index: 2;
  margin-bottom: 1rem;
  box-shadow: #707070 0 0.1rem 0.5rem 0rem;
}
img {
  height: 2rem;
}
.logout-btn {
  margin-left: 1rem;
  max-height: 1.5rem;
  cursor: pointer;
}
button {
  color: #707070;
  background-color: unset;
  opacity: 0.6;
  border: unset;
}
button.selected {
  color: black;
  opacity: 1;
  font-weight: bold;
}
</style>
