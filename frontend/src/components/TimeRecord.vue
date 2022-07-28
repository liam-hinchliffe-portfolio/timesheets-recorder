<template>
  <div class="time-record">
    <error :error="errorMsg" />
    <div class="d-flex">
      <p v-if="timeRecord.project">
        <strong>Project</strong>: {{ timeRecord.project.name }}
      </p>
      <p v-if="timeRecord.project_stage">
        <strong>Project Stage</strong>: {{ timeRecord.project_stage.name }}
      </p>
      <p>
        <strong>Duration</strong>:
        {{ convertMinsToHrsMins(timeRecord.minutes) }}
      </p>
      <p v-if="timeRecord.notes">
        <strong>Notes: </strong>{{ timeRecord.notes }}
      </p>
    </div>
    <div v-if="deleteConfirmation">
      <button @click="deleteTimeRecord">Confirm Deletion</button
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
  name: "TimeRecord",
  props: {
    timeRecord: { type: Object, required: true },
  },
  setup(props, context) {
    const axios = inject("axios");
    const deleteConfirmation = ref(false);

    const errorMsg = ref(null);
    const convertMinsToHrsMins = (mins) => {
      let h = Math.floor(mins / 60);
      let m = mins % 60;
      return `${h}hr ${m}min`;
    };

    function deleteTimeRecord() {
      axios
        .delete(
          `https://oasisstudio.uk/timesheets/public/api/timeRecord/${props.timeRecord.id}/delete`,
          {
            withCredentials: true,
          }
        )
        .then(() => {
          deleteConfirmation.value = false;
          context.emit("deleted", props.timeRecord.id);
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not delete the time record";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    return {
      convertMinsToHrsMins,
      deleteTimeRecord,
      deleteConfirmation,
      errorMsg,
    };
  },
});
</script>

<style scoped>
.time-record {
  margin: 0 auto;
  width: 80%;
  background-color: white;
  padding: 2rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
  margin-top: 1rem;
  display: flex;
  flex-direction: column;
}
.d-flex {
  justify-content: space-between;
}
</style>
