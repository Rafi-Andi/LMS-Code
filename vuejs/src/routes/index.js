import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import DashboardLayout from '../layout/DashboardLayout.vue'
import Index from '../pages/Index.vue'
import DetailCourse from '../pages/DetailCourse.vue'
import Lesson from '../pages/Lesson.vue'
import Jump from '../pages/Jump.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            component: Login,
            name: 'Login'
        },
        {
            path: '/register',
            component: Register,
            name: 'Register'
        },
        {
            path: '/',
            component: Index,
            name: 'Index'
        },
        {
            path: '/course/:slug',
            component: DetailCourse,
            name: 'DetailCourse',
        },
        {
            path: '/:course_slug/lessons/:lesson_id',
            component: Lesson,
            name: 'Lesson'
        },
        {
            path: '/:course_slug/lessons/jump/:set_id',
            component: Jump,
            name: 'Jump'
        }
    ]
})

export default router