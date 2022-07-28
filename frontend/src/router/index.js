import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/Login.vue";
import Home from "../views/Home.vue";
import Projects from "../views/Projects.vue";
import ViewProject from "../views/ViewProject.vue";
import Users from "../views/Users.vue";
import Clients from "../views/Clients.vue";
import Departments from "../views/Departments.vue";
import PageNotFound404 from "../views/PageNotFound404.vue";

const routes = [
  {
    path: "/",
    name: "Login",
    component: Login,
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/home",
    name: "Home",
    component: Home,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: "/projects",
    name: "Projects",
    component: Projects,
    meta: {
      requiresAuth: true,
      requiresAuthLevel2: true
    }
  },
  {
    path: "/project/:id/view",
    name: "ViewProject",
    component: ViewProject,
    props: true,
    meta: {
      requiresAuth: true,
      requiresAuthLevel2: true
    }
  },
  {
    path: "/users",
    name: "Users",
    component: Users,
    meta: {
      requiresAuth: true,
      requiresAuthLevel2: true
    }
  },
  {
    path: "/clients",
    name: "Clients",
    component: Clients,
    meta: {
      requiresAuth: true,
      requiresAuthLevel2: true
    }
  },
  {
    path: "/departments",
    name: "departments",
    component: Departments,
    meta: {
      requiresAuth: true,
      requiresAuthLevel2: true
    }
  },
  {
    path: "/:pathMatch(.*)*",
    name: "PageNotFound404",
    component: PageNotFound404,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Apply frontend middleware
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    try {
      const authUser = JSON.parse(window.localStorage._user);
      if (to.meta.requiresAuthLevel2) {
        (authUser && authUser.auth_level == 2) ? next() : next({ name: "Home" })
      } else {
        (window.localStorage._token && window.localStorage._token != null && window.localStorage._token != 'null') ? next() : next({ name: "Login" });
      }
    } catch (error) {
      next({ name: "Login" })
    }
  } else next();
})

export default router;
