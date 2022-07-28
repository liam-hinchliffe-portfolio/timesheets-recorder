<template>
  <div class="home">
    <error :error="errorMsg" />
    <header-nav selectedTab="home" />
    <h3 class="d-flex">Select Date</h3>
    <p class="d-flex">Select the date which you wish to view timesheets for</p>
    <div class="d-flex">
      <DatePicker v-model="selectedDate" :attributes="calanderAttrs" />
    </div>
    <new-time-record :date="selectedDate" @created="addNewTimeRecord" />
    <div
      v-if="timeRecords && timeRecords.length"
      class="time-records-container"
    >
      <p>
        <strong>Total Daily Hours</strong>:
        {{ convertMinsToHrsMins(totalDailyHours) }}
      </p>
      <time-record
        v-for="(timeRecord, index) in timeRecords"
        v-bind:key="index"
        :timeRecord="timeRecord"
        @deleted="deleteTimeRecord"
      />
    </div>
    <p class="d-flex" v-else>
      <strong>There are no timesheets for this date</strong>
    </p>
  </div>
</template>

<script>
import { computed, inject, onMounted, ref, watch } from "@vue/runtime-core";
// @ is an alias to /src
import HeaderNav from "../components/HeaderNav.vue";
import TimeRecord from "../components/TimeRecord.vue";
import NewTimeRecord from "../components/NewTimeRecord.vue";
import Error from "../components/Error.vue";
export default {
  components: { HeaderNav, TimeRecord, NewTimeRecord, Error },
  name: "Home",
  setup() {
    const axios = inject("axios");
    const user = JSON.parse(window.localStorage._user);

    const errorMsg = ref(null);

    const selectedDate = ref(new Date());
    const totalRecordedTime = ref(0);
    const calanderAttrs = [
      {
        key: "today",
        highlight: true,
      },
    ];
    const timeRecords = ref([]);

    function getTimeRecordsByDate(date) {
      axios
        .get(
          `https://oasisstudio.uk/timesheets/public/api/timeRecords/date/${date}`,
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          timeRecords.value = response.data;
          totalRecordedTime.value = 0;
          timeRecords.value.forEach((timeRecord) => {
            totalRecordedTime.value += timeRecord.minutes;
          });
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "Could not read time records";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function addNewTimeRecord(timeRecord) {
      timeRecords.value.push(timeRecord);
    }

    function deleteTimeRecord(id) {
      timeRecords.value = timeRecords.value.filter(
        (timeRecord) => timeRecord.id != id
      );
    }

    watch(selectedDate, () => {
      console.log(selectedDate.value)
      const selectedDateString = selectedDate.value.toISOString().split("T")[0];
      getTimeRecordsByDate(selectedDateString);
    });

    onMounted(() => {
      const selectedDateString = selectedDate.value.toISOString().split("T")[0];
      getTimeRecordsByDate(selectedDateString);
    });

    const convertMinsToHrsMins = (mins) => {
      let h = Math.floor(mins / 60);
      let m = mins % 60;
      return `${h}hr ${m}min`;
    };

    const totalDailyHours = computed(() => {
      let minutes = 0;
      if (timeRecords.value) {
        timeRecords.value.forEach((tr) => (minutes += tr.minutes));
        return minutes;
      }
      return 0;
    });

    return {
      user,
      selectedDate,
      calanderAttrs,
      timeRecords,
      totalRecordedTime,
      addNewTimeRecord,
      deleteTimeRecord,
      errorMsg,
      totalDailyHours,
      convertMinsToHrsMins,
    };
  },
};
</script>


<style scoped>
.time-records-container {
  width: 70%;
  margin: 1rem auto 0 auto;
}
</style>