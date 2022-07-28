<template>
  <div>
    <error :error="errorMsg" />
    <header-nav selectedTab="users" />
    <div class="d-flex">
      <h3>Users</h3>
      <button @click="newUserModal = true">New +</button>
    </div>
    <div class="users-container d-flex">
      <user
        @deleted="deleteUser"
        v-for="(user, index) in users"
        v-bind:key="index"
        :user="user"
      />
    </div>

    <modal v-if="newUserModal" @close="newUserModal = false">
      <new-user @created="displayNewUser" />
    </modal>
  </div>
</template>

<script>
// @ is an alias to /src
import { inject, onMounted, ref } from "@vue/runtime-core";
import HeaderNav from "../components/HeaderNav.vue";
import Modal from "../components/Modal.vue";
import User from "../components/User.vue";
import NewUser from "../components/NewUser.vue";
import Error from "../components/Error.vue";
export default {
  components: { HeaderNav, User, Modal, NewUser, Error },
  name: "Users",
  setup() {
    const axios = inject("axios");
    const users = ref([]);
    const newUserModal = ref(false);

    const errorMsg = ref(null);

    function getUsers() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/users`, {
          withCredentials: true,
        })
        .then((response) => {
          users.value = response.data.users;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the users";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function displayNewUser(user) {
      users.value.push(user);
      newUserModal.value = false;
    }

    function deleteUser(userId) {
      users.value = users.value.filter((user) => user.id != userId);
    }

    onMounted(() => getUsers());

    return { users, newUserModal, displayNewUser, deleteUser, errorMsg };
  },
};
</script>


<style scoped>
h3 {
  margin-right: 1rem;
}
</style>