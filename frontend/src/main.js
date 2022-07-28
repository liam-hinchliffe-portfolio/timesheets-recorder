import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from 'axios'
import VueAxios from 'vue-axios'
import 'v-calendar/dist/style.css';
import { SetupCalendar, Calendar, DatePicker } from 'v-calendar';


const app = createApp(App).use(store);
app.use(router);
app.use(VueAxios, axios);
app.use(SetupCalendar, {})
// Use the components
app.component('Calendar', Calendar)
app.component('DatePicker', DatePicker)
app.provide('axios', app.config.globalProperties.axios).mount("#app");
