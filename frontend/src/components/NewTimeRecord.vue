<template>
  <div class="new-time-record d-flex col">
    <error :error="errorMsg" />
    <div class="d-flex">
      <p>
        <strong>Project <span class="required">*</span></strong>
      </p>
      <select v-if="projects" v-model="selectedProject">
        <option value="0">-- Select Project --</option>
        <option
          v-for="(project, index) in projects"
          v-bind:key="index"
          :value="project.id"
        >
          {{ project.name }}
        </option>
      </select>
    </div>
    <div v-if="selectedProject && selectedProject != '0'" class="d-flex">
      <p>
        <strong>Project Stage <span class="required">*</span></strong>
      </p>
      <select v-model="selectedProjectStage">
        <option value="0">-- Select Project Stage --</option>
        <option
          v-for="(projectStage, index) in projects.find(
            (project) => project.id == selectedProject
          ).project_stages"
          v-bind:key="index"
          :value="projectStage.id"
        >
          {{ projectStage.name }}
        </option>
      </select>
    </div>

    <div class="d-flex">
      <p>
        <strong>Duration (minutes) <span class="required">*</span></strong>
      </p>
      <input v-model="duration" type="number" min="1" />
    </div>

    <div class="d-flex">
      <p><strong>Notes</strong></p>
      <textarea v-model="notes"></textarea>
    </div>

    <stopwatch />

    <div class="errors">
      <p class="error" v-for="(error, index) in errors" v-bind:key="index">
        {{ error[0] }}
      </p>
    </div>

    <button @click="save">Save</button>
  </div>
</template>
<script>
import { defineComponent, inject, onMounted, ref } from "vue";
import Stopwatch from "./Stopwatch.vue";
export default defineComponent({
  name: "NewTimeRecord",
  props: {
    date: { type: Date, required: false, default: new Date() },
  },
  components: {
    Stopwatch,
  },
  setup(props, context) {
    const axios = inject("axios");
    const projects = ref([]);
    const selectedProject = ref(null);
    const selectedProjectStage = ref(null);
    const notes = ref(null);
    const duration = ref(0);
    const errors = ref(null);
    const errorMsg = ref(null);

    function getProjects() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/projects`, {
          withCredentials: true,
        })
        .then((response) => (projects.value = response.data))
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read projects";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function save() {
      const date = props.date.toISOString().split("T")[0];
      axios
        .put(
          `https://oasisstudio.uk/timesheets/public/api/timeRecord/create`,
          {
            project: selectedProject.value,
            projectStage: selectedProjectStage.value,
            duration: duration.value,
            notes: notes.value,
            date,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          projects.value = response.data;
          context.emit("created", response.data);
          errors.value = null;
          window.location.reload();
        })
        .catch((error) => {
          errors.value = error.response.data.errors;
          errorMsg.value = "could not create new time record";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    onMounted(() => getProjects());

    return {
      getProjects,
      projects,
      selectedProject,
      selectedProjectStage,
      save,
      duration,
      notes,
      errors,
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
