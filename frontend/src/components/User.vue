<template>
  <div class="user">
    <error :error="errorMsg" />
    <p><strong>Name: </strong>{{ user.first_name }} {{ user.last_name }}</p>
    <p><strong>Email: </strong>{{ user.email }}</p>
    <div v-if="deleteConfirmation">
      <button @click="deleteUser">Confirm Deletion</button
      ><button @click="deleteConfirmation = false">Cancel</button>
    </div>
    <button v-else @click="deleteConfirmation = true">Delete</button>
  </div>
</template>
<script>
import { defineComponent, inject, ref } from "vue";
import Error from "./Error.vue";
export default defineComponent({
  components: { Error },
  name: "User",
  props: {
    user: { type: Object, required: true },
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);

    const errorMsg = ref(null);

    function deleteUser() {
      axios
        .delete(`https://oasisstudio.uk/timesheets/public/api/user/${props.user.id}/delete`, {
          withCredentials: true,
        })
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.user.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the user";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      deleteUser,
      deleteConfirmation,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.user {
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

.user p {
  margin: unset;
  margin-bottom: 0.5rem;
}

.user:first-child {
  margin-left: unset;
}
</style>
