<template>
  <div>
    <div class="modal-container" @click="close"></div>
    <div class="modal">
      <div class="close" @click="close">X</div>
      <slot></slot>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
export default defineComponent({
  name: "Modal",
  setup(props, context) {
    function close() {
      context.emit("close");
    }
    return {
      close,
    };
  },
});
</script>
<style>
.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 1rem;
  background-color: white;
  box-shadow: 0.1rem 0.1rem 0rem 1px #aaa;
  border: 1px solid black;
  max-height: 80vh;
  overflow-y: auto;
  z-index: 10;
}

.modal-container {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: rgba(245, 245, 245, .75);
  z-index: 10;
}

.close {
  position: absolute;
  top: 0.15rem;
  right: 0.5rem;
  cursor: pointer;
  text-align: center;
}
</style>
