<template>
  <div class="client">
    <error :error="errorMsg" />
    <p><strong>Name: </strong>{{ client.name }}</p>
    <p><strong>Reference: </strong>{{ client.reference }}</p>
    <div v-if="deleteConfirmation">
      <button @click="deleteClient">Confirm Deletion</button
      ><button @click="deleteConfirmation = false">Cancel</button>
    </div>
    <button v-else @click="deleteConfirmation = true">Delete</button>
  </div>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  name: "Client",
  props: {
    client: { type: Object, required: true },
  },
  components: {
    Error,
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);
    const errorMsg = ref(null);

    function deleteClient() {
      axios
        .delete(`https://oasisstudio.uk/timesheets/public/api/client/${props.client.id}/delete`, {
          withCredentials: true,
        })
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.client.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the client";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      deleteClient,
      deleteConfirmation,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.client {
  padding: 0.5rem;
  background-color: white;
  margin-left: 1rem;
  margin-bottom: 1rem;
  display: flex;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.client p {
  margin: unset;
  margin-bottom: 0.5rem;
}

.client:first-child {
  margin-left: unset;
}

</style>
