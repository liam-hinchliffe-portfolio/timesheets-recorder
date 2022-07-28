<template>
  <div>
    <error :error="errorMsg" />
    <header-nav selectedTab="projects" />
    <modal v-if="newProjectModal" @close="newProjectModal = false">
      <new-project @created="displayNewProject" />
    </modal>
    <div class="d-flex">
      <h3>Projects</h3>
      <button @click="newProjectModal = true">New +</button>
    </div>
    <div class="projects-container d-flex">
      <project
        @deleted="deleteProject"
        v-for="(project, index) in projects"
        v-bind:key="index"
        :project="project"
      />
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import { inject, onMounted, ref } from "@vue/runtime-core";
import HeaderNav from "../components/HeaderNav.vue";
import Modal from "../components/Modal.vue";
import NewProject from "../components/NewProject.vue";
import Project from "../components/Project.vue";
import Error from "../components/Error.vue";
export default {
  components: { HeaderNav, NewProject, Modal, Project, Error },
  name: "Projects",
  setup() {
    const axios = inject("axios");
    const projects = ref([]);
    const newProjectModal = ref(false);

    const errorMsg = ref(null);

    function getProjects() {
      axios
        .get(`https://oasisstudio.uk/timesheets/public/api/projects`, {
          withCredentials: true,
        })
        .then((response) => {
          projects.value = response.data;
          errorMsg.value = null;
        })
        .catch((error) => {
          console.warn(error);
          errorMsg.value = "could not read the projects";
          setTimeout(() => (errorMsg.value = null), 3000);
        });
    }

    function displayNewProject(project) {
      projects.value.push(project);
      newProjectModal.value = false;
    }

    function deleteProject(projectId) {
      projects.value = projects.value.filter(
        (project) => project.id != projectId
      );
    }

    onMounted(() => getProjects());

    return {
      projects,
      newProjectModal,
      displayNewProject,
      deleteProject,
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