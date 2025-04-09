import { createRouter, createWebHistory } from 'vue-router';
import Register from './components/Register.vue';
import Login from './components/Login.vue';
import Dashboard from './components/Dashboard.vue';
import ApplyLeave from './components/ApplyLeave.vue';

const routes = [
  { path: "/register", component: Register },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true } },
  { path: "/applyleave",component:ApplyLeave, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("token");
  if (to.meta.requiresAuth && !isAuthenticated) {
    alert("You need to log in first!");
    return next("/login"); 
  }

  if (to.meta.role && to.meta.role !== userRole) {
    alert("You do not have permission to access this page.");
    return next("/dashboard"); 
  }
  next();
});

export default router;
