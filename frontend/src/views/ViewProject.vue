<template>
  <div>
    <error :error="errorMsg" />
    <header-nav selectedTab="projects" />
    <div class="d-flex column">
      <h3>{{ project.name }} [ref: {{ project.reference }}]</h3>
      <div class="project-stages-container d-flex column">
        <h3>Project Stages</h3>
        <div class="d-flex">
          <project-stage
            v-for="(projectStage, index) in project.project_stages"
            v-bind:key="index"
            :projectStage="projectStage"
            @deleted="deleteProjectStage"
          />
        </div>
        <new-project-stage :project="project" @created="createProjectStage" />
      </div>

      <div class="analytics-container d-flex column">
        <h3>Analytics</h3>
        <p class="m-0">
          <strong>Total Time</strong>:
          {{ convertMinsToHrsMins(totalTimeSpent) }}
        </p>
        <p class="m-0">
          <strong>Total Cost</strong>: {{ formatter.format(totalCost) }}
        </p>
        <p>
          <strong>Add Margin (%)</strong>:
          <input type="number" v-model="marginPercentage" />
          <span> = {{ formatter.format(costWithMargin) }}</span>
        </p>
        <h3>Calculate Total Profit</h3>
        <input
          min="1"
          placeholder="Paid / quotes amount..."
          type="number"
          v-model="paidAmount"
        />
        <p
          v-if="paidAmount"
          :style="{ color: paidAmount - totalCost <= 0 ? 'red' : 'green' }"
        >
          Earned Profit Margin:
          {{ formatter.format(((paidAmount - totalCost) / paidAmount) * 100) }}%
        </p>
        <p
          style="margin-top: 0"
          v-if="paidAmount"
          :style="{ color: paidAmount - totalCost <= 0 ? 'red' : 'green' }"
        >
          Earned Profit: {{ formatter.format(paidAmount - totalCost) }}
        </p>
        <h4>Users ({{ userAnalytics.length }})</h4>
        <div class="users d-flex">
          <div
            class="user d-flex column"
            v-for="(user, index) in userAnalytics"
            v-bind:key="index"
          >
            <p>
              <strong>User</strong>: {{ user.first_name }} {{ user.last_name }}
            </p>
            <p>
              <strong>Time Spent</strong>:
              {{ convertMinsToHrsMins(user.minutes) }}
            </p>
            <p v-if="user.cost">
              <strong>Wage Cost</strong>: {{ formatter.format(user.cost) }}
            </p>
          </div>
        </div>

        <h4>Project Stages ({{ stageAnalytics.length }})</h4>
        <div
          class="project-stage d-flex column"
          v-for="(projectStage, index) in stageAnalytics"
          v-bind:key="index"
        >
          <p><strong>Name</strong>: {{ projectStage.name }}</p>
          <p>
            <strong>Time Spent</strong>:
            {{ convertMinsToHrsMins(projectStage.minutes) }}
          </p>
          <p v-if="projectStage.cost">
            <strong>Wage Cost</strong>:
            {{ formatter.format(projectStage.cost) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import { computed, inject, onMounted, ref } from "@vue/runtime-core";
import HeaderNav from "../components/HeaderNav.vue";
import ProjectStage from "../components/ProjectStage.vue";
import NewProjectStage from "../components/NewProjectStage.vue";
import Error from "../components/Error.vue";
export default {
  components: { HeaderNav, ProjectStage, NewProjectStage, Error },
  name: "ViewProject",
  props: {
    id: { type: Number, required: true },
  },
  setup(props) {
    const axios = inject("axios");
    const project = ref([]);
    const analytics = ref();

    const totalTimeSpent = ref(0);
    const totalCost = ref(0);
    const userAnalytics = ref([]);
    const stageAnalytics = ref([]);

    const marginPercentage = ref(0);
    const paidAmount = ref(0);

    const errorMsg = ref(null);

    function getProject() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/project/${props.id}`, {
          withCredentials: true,
        })
        .then((response) => (project.value = response.data))
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the project";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function getAnalytics() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/project/${props.id}/analytics`, {
          withCredentials: true,
        })
        .then((response) => {
          analytics.value = response.data;
          calculateAnalytics();
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the analytics";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    const formatter = new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "GBP",
    });

    function calculateAnalytics() {
      analytics.value.time_records.forEach((timeRecord) => {
        totalTimeSpent.value += timeRecord.minutes;

        if (!userAnalytics.value.find((user) => user.id == timeRecord.user.id))
          userAnalytics.value.push(timeRecord.user);

        const user = userAnalytics.value.find(
          (user) => user.id == timeRecord.user.id
        );

        if (!user.minutes) user.minutes = 0;
        user.minutes += timeRecord.minutes;

        if (user.wage) {
          if (!user.cost) user.cost = 0;
          const timeRecordCost = (timeRecord.minutes / 60) * user.wage;
          totalCost.value += timeRecordCost;
          user.cost += timeRecordCost;
        }

        if (timeRecord.project_stage) {
          if (
            !stageAnalytics.value.find(
              (stage) => stage.id == timeRecord.project_stage.id
            )
          ) {
            timeRecord.project_stage.cost = 0;
            timeRecord.project_stage.minutes = 0;
            stageAnalytics.value.push(timeRecord.project_stage);
          }

          const stage = stageAnalytics.value.find(
            (stage) => stage.id == timeRecord.project_stage.id
          );
          stage.minutes += timeRecord.minutes;
          stage.cost += (timeRecord.minutes / 60) * user.wage;
        }
      });
    }

    function createProjectStage(projectStage) {
      project.value.project_stages.push(projectStage);
    }

    function deleteProjectStage(projectStageId) {
      project.value.project_stages = project.value.project_stages.filter(
        (projectStage) => projectStage.id != projectStageId
      );
    }

    const convertMinsToHrsMins = (mins) => {
      let h = Math.floor(mins / 60);
      let m = mins % 60;
      return `${h}hr ${m}min`;
    };

    const costWithMargin = computed(
      () => totalCost.value * (1 + marginPercentage.value / 100)
    );

    onMounted(() => {
      getProject();
      getAnalytics();
    });

    return {
      project,
      getProject,
      createProjectStage,
      deleteProjectStage,
      analytics,
      totalTimeSpent,
      userAnalytics,
      convertMinsToHrsMins,
      totalCost,
      formatter,
      stageAnalytics,
      marginPercentage,
      paidAmount,
      costWithMargin,
      errorMsg,
    };
  },
};
</script>


<style scoped>
.d-flex.column {
  flex-direction: column;
}
h5 {
  margin-bottom: 0;
}
h3 {
  margin-top: 0;
}
.user,
.project-stage {
  background-color: #f2f2ee;
  padding: 0.5rem;
  box-shadow: 0.1rem 0.1rem 0.2rem 0rem black;
  margin-right: 1rem;
  margin-bottom: 1rem;
}
.project-stage p {
  margin: unset;
  margin-bottom: 0.5rem;
}
.project-stage p:last-child {
  margin-bottom: unset;
}
.user:last-child {
  margin-right: unset;
}
.user p {
  margin: 0;
  margin-bottom: 0.5rem;
}
.user p:last-child {
  margin-bottom: unset;
}
.analytics-container,
.project-stages-container {
  background-color: white;
  width: 80vw;
  margin-bottom: 2rem;
  padding: 1.5rem;
}
</style>