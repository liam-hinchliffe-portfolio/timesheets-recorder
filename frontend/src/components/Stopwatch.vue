<template>
  <div class="stopwatch">
    <p>
      Time: {{ convertMinsToHrsMins(elapsedTime) }} (minutes: {{ elapsedTime }})
    </p>
    <div class="d-flex">
      <button v-if="!started" @click="start">Start</button>
      <button v-else @click="pause">Pause</button>
      <button @click="reset">Reset</button>
    </div>
  </div>
</template>
<script>
import { defineComponent, ref } from "vue";
export default defineComponent({
  name: "Stopwatch",
  setup() {
    const elapsedTime = ref(0);
    const elapsedTimeInterval = ref(null);
    const started = ref(false);

    const convertMinsToHrsMins = (mins) => {
      let h = Math.floor(mins / 60);
      let m = mins % 60;
      return `${h}hr ${m}min`;
    };

    function start() {
      started.value = true;
      elapsedTimeInterval.value = setInterval(
        () => (elapsedTime.value += 1),
        60000
      );
    }

    function reset() {
      clearInterval(elapsedTimeInterval.value);
      elapsedTime.value = 0;
      started.value = false;
    }

    function pause() {
      clearInterval(elapsedTimeInterval.value);
      started.value = false;
    }

    return {
      elapsedTime,
      start,
      reset,
      pause,
      convertMinsToHrsMins,
      started,
    };
  },
});
</script>

<style>
.stopwatch {
  padding: 0.5rem 1rem;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  border: 1px solid slategrey;
  margin: 1rem 0;
}

button:first-child {
  margin-right: 1rem;
}
</style>
