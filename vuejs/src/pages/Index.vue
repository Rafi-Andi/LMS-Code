<script setup>
import { onMounted, ref } from "vue";
import NavbarUser from "../components/NavbarUser.vue";
import api from "../routes/axios";

const courseProgress = ref(null);
const fetchProgress = async () => {
  const ress = await api.get("/users/progress");
  courseProgress.value = ress.data.data.progress;
  fetchCourses();
};

const allCourses = ref(null);
const fetchCourses = async () => {
  const ress = await api.get("courses");
  allCourses.value = ress.data.data;
};

import { computed } from "vue";

const filteredCourses = computed(() => {
  if (!allCourses.value || !courseProgress.value) return [];

  const registeredCourseIds = courseProgress.value.map(
    (item) => item.course.id
  );

  return allCourses.value.filter(
    (course) => !registeredCourseIds.includes(course.id)
  );
});
onMounted(() => {
  fetchProgress();
});
</script>

<template>
  <div>
    <NavbarUser />
    <main class="py-5">
      <section class="my-courses">
        <div class="container">
          <h4 class="mb-3">My courses</h4>
          <div
            v-for="data in courseProgress"
            :key="data.id"
            class="courses d-flex flex-column gap-3"
          >
            <router-link
              :to="{
                name: 'DetailCourse',
                params: { slug: data?.course?.slug },
              }"
              class="card text-decoration-none bg-body-tertiary"
            >
              <div class="card-body">
                <h5 class="mb-2">{{ data?.course?.name }}</h5>
                <p class="text-muted mb-0">{{ data?.course?.description }}</p>
              </div>
            </router-link>
          </div>
        </div>
      </section>

      <section class="other-courses mt-4">
        <div class="container">
          <h4 class="mb-3">Other courses</h4>
          <div
            v-for="data in filteredCourses"
            :key="data.id"
            class="d-flex flex-column gap-3"
          >
            <router-link
              :to="{ name: 'DetailCourse', params: { slug: data?.slug } }"
              class="card text-decoration-none bg-body-tertiary"
            >
              <div class="card-body">
                <h5 class="mb-2">{{ data?.name }}</h5>
                <p class="text-muted mb-0">{{ data?.description }}</p>
              </div>
            </router-link>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>