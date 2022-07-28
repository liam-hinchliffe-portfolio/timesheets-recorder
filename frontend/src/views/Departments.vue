<template>
  <div>
    <error :error="errorMsg" />
    <header-nav selectedTab="departments" />
    <div class="d-flex">
      <h3>Departments</h3>
      <button @click="newDepartmentModal = true">New +</button>
    </div>
    <div class="departments-container d-flex">
      <department
        @deleted="deleteDepartment"
        v-for="(department, index) in departments"
        v-bind:key="index"
        :department="department"
        :users="users"
        :clients="clients"
      />
    </div>

    <modal v-if="newDepartmentModal" @close="newDepartmentModal = false">
      <new-department @created="displayNewDepartment" />
    </modal>
  </div>
</template>

<script>
// @ is an alias to /src
import { inject, onMounted, ref } from "@vue/runtime-core";
import HeaderNav from "../components/HeaderNav.vue";
import Modal from "../components/Modal.vue";
import NewDepartment from "../components/NewDepartment.vue";
import Department from "../components/Department.vue";
import Error from "@/components/Error.vue";

export default {
  components: { HeaderNav, Department, Modal, NewDepartment, Error },
  name: "Departments",
  setup() {
    const axios = inject("axios");
    const newDepartmentModal = ref(false);
    const errorMsg = ref(null);

    const departments = ref();
    const clients = ref();
    const users = ref();

    function getDepartments() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/departments`, {
          withCredentials: true,
        })
        .then((response) => {
          departments.value = response.data;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the departments";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function getClients() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/clients`, {
          withCredentials: true,
        })
        .then((response) => {
          clients.value = response.data;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the clients";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

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

    function displayNewDepartment(client) {
      departments.value.push(client);
      newDepartmentModal.value = false;
    }

    function deleteDepartment(clientId) {
      departments.value = departments.value.filter(
        (client) => client.id != clientId
      );
    }

    onMounted(() => {
      getDepartments();
      getUsers();
      getClients();
    });

    return {
      departments,
      users,
      clients,
      newDepartmentModal,
      displayNewDepartment,
      deleteDepartment,
      errorMsg,
    };
  },
};
</script>


<style scoped>
h3 {
  margin-right: 1rem;
}
</style>