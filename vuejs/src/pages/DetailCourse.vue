<script setup >
import { loadRouteLocation, useRoute } from "vue-router";
import NavbarUser from "../components/NavbarUser.vue";
import { computed, onMounted, ref, useTemplateRef } from "vue";
import api from "../routes/axios";
const route = useRoute();
const slug = route.params.slug;
const statuses = ref([]);

const dataCourse = ref([]);
const fetchCourse = async () => {
  const ress = await api.get(`/courses/${slug}`);
  dataCourse.value = ress.data.data;
};

const courseProgress = ref([]);
const fetchProgress = async () => {
  const ress = await api.get("/users/progress");
  courseProgress.value = ress.data.data.progress;
  console.log(firstIncompleted.value);
};

const isRegistered = computed(() => {
  const checkCourse = courseProgress.value.some(
    (p) => p.course.slug === dataCourse?.value?.slug
  );

  return checkCourse;
});
const handleRegisterCourse = async () => {
  try {
    const ress = await api.post(`courses/${slug}/register`);
    alert(ress.data.message);
    window.location.reload();
  } catch (error) {
    alert(error.response.data.message);
  }
};

const marked = ref(false);
const lessonsStatus = computed(() => {
  marked.value = false;
  if (!dataCourse.value && !courseProgress.value) return;
  const userCourse = courseProgress.value.find((c) => c.course.slug === slug);
  const completedLessonIds = userCourse?.completed_lessons?.map((c) => c.id);

  const statuses = {};

  dataCourse.value.sets.forEach((set) => {
    set.lessons.forEach((lesson) => {
      const id = lesson.id;

      if (completedLessonIds?.includes(id)) {
        statuses[id] = "Completed";
      } else if (!marked.value) {
        statuses[id] = "Current";
        marked.value = true;
      } else {
        statuses[id] = "Locked";
      }
    });
  });

  return statuses;
});

const progressPrecent = computed(() => {
  if (!dataCourse.value) return;
  let countLessons = 0;
  dataCourse.value.sets?.forEach((set) => {
    countLessons += set.lessons.length;
  });

  let countLessonCompleted = 0;

  courseProgress.value.forEach((progres) => {
    if (progres?.course?.slug == dataCourse.value?.slug) {
      countLessonCompleted = progres.completed_lessons.length;
    }
  });

  return Math.floor((countLessonCompleted / countLessons) * 100);
});

const firstIncompleted = computed(() => {
  let id = null;
  let marked = false;

  dataCourse.value?.sets?.forEach((set) => {
    if (
      !set.lessons.every(
        (lesson) => lessonsStatus.value[lesson.id] === "Completed"
      ) &&
      !marked
    ) {
      id = set.id;
      marked = true;
    }
  });

  return id;
});

const full_name = localStorage.getItem("full_name");

onMounted(() => {
  fetchCourse();
  fetchProgress();
});
</script>

<template>
  <div>
    <NavbarUser />
    <main class="py-5">
      <section>
        <div class="container">
          <div
            class="d-flex align-items-center justify-content-between mb-3"
            v-if="!isRegistered"
          >
            <div>
              <h3 class="mb-0">{{ dataCourse?.name }}</h3>
              <p class="mb-5">{{ dataCourse?.description }}</p>
            </div>
            <p @click="handleRegisterCourse" class="btn btn-primary">
              Register to this course
            </p>
          </div>

          <div v-if="isRegistered">
            <h3 class="mb-3">{{ dataCourse?.name }}</h3>
            <div
              class="progress mb-5"
              role="progressbar"
              aria-label="Example with label"
              aria-valuenow="25"
              aria-valuemin="0"
              aria-valuemax="100"
            >
              <div
                class="progress-bar"
                :style="{ width: progressPrecent + '%' }"
              >
                {{ progressPrecent }}%
              </div>
            </div>
          </div>

          <div class="mb-4" v-if="!isRegistered">
            <h4 class="mb-3">Outline</h4>
            <div class="row">
              <div
                class="col-md-12"
                v-for="(data, index) in dataCourse?.sets"
                :key="data?.id"
              >
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="mb-0">{{ index + 1 }}. {{ data?.name }}</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-4" v-if="isRegistered">
            <div v-for="(set, index) in dataCourse?.sets" :key="index">
              <h4 class="mb-3">{{ set?.name }}</h4>
              <div class="row">
                <div
                  class="col-md-12"
                  v-for="lesson in set?.lessons"
                  :key="lesson.id"
                >
                  <router-link
                    :to="
                      lessonsStatus[lesson?.id] === 'Locked'
                        ? route.fullPath
                        : {
                            name: 'Lesson',
                            params: {
                              course_slug: dataCourse?.slug,
                              lesson_id: lesson?.id,
                            },
                          }
                    "
                    class="card mb-3 text-decoration-none bg-body-tertiary"
                  >
                    <div class="card-body d-flex justify-content-between">
                      <div>
                        <small class="text-muted">Lesson</small>
                        <h5 class="mt-2">{{ lesson?.name }}</h5>
                      </div>
                      <div>
                        <div
                          v-if="lessonsStatus[lesson?.id] === 'Completed'"
                          class="badge border border-primary text-primary"
                        >
                          {{ lessonsStatus[lesson?.id] }}
                        </div>
                        <div
                          v-if="lessonsStatus[lesson?.id] === 'Current'"
                          class="badge border"
                        >
                          {{ lessonsStatus[lesson?.id] }}
                        </div>
                        <div
                          v-if="lessonsStatus[lesson?.id] === 'Locked'"
                          class="badge opacity-50"
                        >
                          {{ lessonsStatus[lesson?.id] }}
                        </div>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
              <div class="text-center mb-4" v-if="firstIncompleted === set?.id">
                <p class="mb-2"><b>Too easy?</b></p>
                <router-link
                  :to="{
                    name: 'Jump',
                    params: { course_slug: dataCourse?.slug, set_id: set?.id },
                  }"
                  class="btn btn-outline-primary"
                  >Jump Here</router-link
                >
              </div>
            </div>
          </div>
          <div v-if="progressPrecent >= 100">
            <h4 class="mb-3">Certificate</h4>
            <div class="row">
              <div class="col-md-12">
                <div class="card mb-3 text-decoration-none bg-body-tertiary">
                  <div
                    class="card-body text-center d-flex flex-column gap-5 p-4"
                  >
                    <h5>Course Certificate</h5>

                    <div class="text-center d-flex flex-column gap-2">
                      <p class="mb-0 text-muted">
                        <small>This is to certify that</small>
                      </p>
                      <h1 class="mb-0 fw-bold">{{ full_name.toUpperCase() }}</h1>
                      <p class="mb-0 text-muted">
                        <small
                          >has successfully completed the course by
                          demonstrating <br />
                          theorical and practical understanding to</small
                        >
                      </p>
                      <h3 class="fw-normal">{{ dataCourse?.name }}</h3>
                    </div>

                    <h6 class="mb-0">LMS Code</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>