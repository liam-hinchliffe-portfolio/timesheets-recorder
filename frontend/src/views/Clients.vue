<template>
  <div>
    <error :error="errorMsg" />
    <header-nav selectedTab="clients" />
    <div class="d-flex">
      <h3>Clients</h3>
      <button @click="newClientModal = true">New +</button>
    </div>
    <div class="clients-container d-flex">
      <client
        @deleted="deleteClient"
        v-for="(client, index) in clients"
        v-bind:key="index"
        :client="client"
      />
    </div>

    <modal v-if="newClientModal" @close="newClientModal = false">
      <new-client @created="displayNewClient" />
    </modal>
  </div>
</template>

<script>
// @ is an alias to /src
import { inject, onMounted, ref } from "@vue/runtime-core";
import HeaderNav from "../components/HeaderNav.vue";
import Modal from "../components/Modal.vue";
import Client from "../components/Client.vue";
import NewClient from "../components/NewClient.vue";
import Error from "@/components/Error.vue";

export default {
  components: { HeaderNav, Client, Modal, NewClient, Error },
  name: "Clients",
  setup() {
    const axios = inject("axios");
    const clients = ref([]);
    const newClientModal = ref(false);
    const errorMsg = ref(null);

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

    function displayNewClient(client) {
      clients.value.push(client);
      newClientModal.value = false;
    }

    function deleteClient(clientId) {
      clients.value = clients.value.filter((client) => client.id != clientId);
    }

    onMounted(() => getClients());

    return {
      clients,
      newClientModal,
      displayNewClient,
      deleteClient,
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