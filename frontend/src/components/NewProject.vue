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
        <strong>Client <span class="required">*</span></strong>
      </p>
      <select v-model="selectedClient">
        <option
          v-for="(client, index) in clients"
          v-bind:key="index"
          :value="client.id"
        >
          {{ client.name }}
        </option>
      </select>
    </div>

    <button @click="save">Save</button>
  </div>
</template>
<script>
import { defineComponent, inject, onMounted, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "NewProject",
  setup(props, context) {
    const axios = inject("axios");
    const name = ref(null);
    const reference = ref(null);
    const clients = ref([]);
    const selectedClient = ref(null);
    const errorMsg = ref(null);

    function getClients() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/clients`, {
          withCredentials: true,
        })
        .then((response) => (clients.value = response.data))
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the clients";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function save() {
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/project/create`,
          {
            name: name.value,
            reference: reference.value,
            client_id: selectedClient.value,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => context.emit("created", response.data))
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not create project";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    onMounted(() => getClients());

    return {
      save,
      name,
      reference,
      getClients,
      clients,
      selectedClient,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.new-time-record {
  margin: 0 auto;
  width: 50%;
  background-color: white;
  padding: 2rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
  margin-top: 1rem;
}
.d-flex.col {
  flex-direction: column;
}
strong {
  margin-right: 1rem;
}
.error {
  color: red;
}
</style>
