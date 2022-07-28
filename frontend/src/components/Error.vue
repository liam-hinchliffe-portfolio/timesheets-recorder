<template>
  <p v-if="error" class="fixed-error">Error: {{ error }}</p>
</template>
<script>
import { defineComponent } from "vue";
export default defineComponent({
  name: "Error",
  props: {
    error: { type: String, required: true },
  },
});
</script>

<style scoped>
.fixed-error {
  position: fixed;
  top: 1rem;
  right: 1rem;
  background-color: red;
  padding: 1rem;
  color: white;
  z-index: 10;
}
</style>
