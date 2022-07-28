<template>
  <div class="department">
    <error :error="errorMsg" />
    <p><strong>Name: </strong>{{ department.name }}</p>
    <p><strong>Users: </strong>{{ department.users.length }}</p>
    <p><strong>Client: </strong>{{ department.clients.length }}</p>
    <div>
      <button v-if="users" @click="assignUserModal = true">Users</button>
      <button v-if="clients" @click="assignClientModal = true">Clients</button>
      <div v-if="deleteConfirmation">
        <button @click="deleteDepartment">Confirm Deletion</button
        ><button @click="deleteConfirmation = false">Cancel</button>
      </div>
      <button v-else @click="deleteConfirmation = true">Delete</button>
    </div>
    <modal v-if="assignUserModal && users" @close="assignUserModal = false">
      <div v-for="(user, index) in users" v-bind:key="index" class="d-flex">
        <p>{{ user.first_name }} {{ user.last_name }} ({{ user.email }})</p>
        <input type="checkbox" :value="user.id" v-model="assignedUsers" />
      </div>
      <button @click="assignUsers">Save</button>
    </modal>

    <modal v-if="assignClientModal && users" @close="assignClientModal = false">
      <div v-for="(client, index) in clients" v-bind:key="index" class="d-flex">
        <p>{{ client.name }} ({{ client.reference }})</p>
        <input type="checkbox" :value="client.id" v-model="assignedClients" />
      </div>
      <button @click="assignClients">Save</button>
    </modal>
  </div>
</template>
<script>
import { defineComponent, inject, onMounted, ref } from "vue";
import Error from "./Error.vue";
import Modal from "./Modal.vue";
export default defineComponent({
  name: "Department",
  props: {
    department: { type: Object, required: true },
    users: { type: Array, required: false },
    clients: { type: Array, required: false },
  },
  components: {
    Error,
    Modal,
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);
    const errorMsg = ref(null);

    const assignUserModal = ref(false);
    const assignedUsers = ref([]);
    const assignClientModal = ref(false);
    const assignedClients = ref([]);

    function deleteDepartment() {
      axios
        .delete(
          `https://oasisstudio.uk/timesheets/public/api/department/${props.department.id}/delete`,
          {
            withCredentials: true,
          }
        )
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.department.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the department";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function assignUsers() {
      axios
        .post(
          `https://oasisstudio.uk/timesheets/public/api/department/${props.department.id}/addUsers`,
          {
            users: assignedUsers.value,
          },
          {
            withCredentials: true,
          }
        )
        .then(() => {
          assignUserModal.value = false;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not add user to department";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function assignClients() {
      axios
        .post(
          `https://oasisstudio.uk/timesheets/public/api/department/${props.department.id}/addClients`,
          {
            clients: assignedClients.value,
          },
          {
            withCredentials: true,
          }
        )
        .then(() => {
          assignClientModal.value = false;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not add client to department";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    onMounted(() => {
      props.department.users.forEach((user) =>
        assignedUsers.value.push(user.id)
      );
      props.department.clients.forEach((client) => {
        assignedClients.value.push(client.id);
      });
    });

    return {
      deleteDepartment,
      deleteConfirmation,
      errorMsg,
      assignUsers,
      assignClients,
      assignUserModal,
      assignClientModal,
      assignedUsers,
      assignedClients,
    };
  },
});
</script>

<style scoped>
.department {
  padding: 0.5rem;
  background-color: white;
  margin-left: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
}

.department:first-child {
  margin-left: unset;
}

button {
  margin-right: 0.5rem;
}

button:last-child {
  margin-right: unset;
}

p {
  margin: 0;
  margin-bottom: 0.5rem;
}
</style>
