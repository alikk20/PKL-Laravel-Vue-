// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Login.vue';
import Dashboard from '@/components/Dashboard.vue'; 
import RegisterStudent from '@/components/RegisterStudent.vue';
import RegisterTeacher from '@/components/RegisterTeacher.vue';
import Industry from '@/components/Industry.vue';
import Proses from '@/components/Proses.vue';
import Test from '@/components/Test.vue';

const routes = [
  { path: '/login', component: Login },
  { path: '/test', component: Test},
  { path: '/proses', component: Proses},
  { path: '/dashboard', component: Dashboard },
  { path: '/register-siswa', component: RegisterStudent },
  { path: '/register-guru', component: RegisterTeacher },
  { path: '/industri', component: Industry},
  { path: '/', redirect: '/login' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
