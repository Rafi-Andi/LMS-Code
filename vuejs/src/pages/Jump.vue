<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import api from "../routes/axios";
import router from "../routes";

const route = useRoute();

const course_slug = route.params.course_slug;
const set_id = route.params.set_id;
const currentOrderLesson = ref(0);
const currentOrderContent = ref(0);

const dataCourse = ref({});

const fetchCourse = async () => {
  const ress = await api.get(`/courses/${course_slug}`);
  dataCourse.value = ress.data.data;
  console.log(dataLesson.value);
};

const dataSet = computed(() => {
  if (!dataCourse.value) return;

  let findSet = null;

  dataCourse.value.sets?.forEach((set) => {
    if (set.id == set_id) {
      findSet = set;
    }
  });

  return findSet;
});

const dataLesson = computed(() => {
  if (!dataSet.value) return;

  let findLesson = null;

  dataSet.value.lessons?.forEach((lesson) => {
    if (lesson.order === currentOrderLesson.value) {
      findLesson = lesson;
    }
  });
  return findLesson;
});

const currentContent = computed(() => {
  if (!dataLesson.value) return;

  let findContent = null;

  dataLesson.value.contents.forEach((content) => {
    if (content.order === currentOrderContent.value) {
      findContent = content;
    }
  });
  return findContent;
});

const answerInput = ref(null);
function checkAnswered() {
  if (!answerInput.value.is_correct) {
    alert("Answer Wrong!");
  } else {
    incrementOrder();
    answerInput.value = null;
  }
}

const handleCompleted = async () => {
  try {
    await api.put(`lessons/${dataLesson.value?.id}/complete`);

    if (!isFinishLesson.value) {
      currentOrderLesson.value++;
      currentOrderContent.value = 0;
    } else {
      alert("Course Completed!");

      router.back();
    }
  } catch (error) {
    console.log(error);
    alert(error.response?.data?.message);
  }
};
function incrementOrder() {
  if (!isFinishContent.value) {
    currentOrderContent.value++;
  } else {
    handleCompleted();
  }
}

const isFinishContent = computed(() => {
  if (!currentContent.value) return;
  const indexContent = dataLesson.value.contents.indexOf(currentContent.value);

  if (dataLesson.value.contents.length - 1 === indexContent) {
    return true;
  } else {
    return false;
  }
});

const isFinishLesson = computed(() => {
  if (!dataSet.value) return false;

  const indexLesson = dataSet.value.lessons.findIndex(
    (l) => l.order === currentOrderLesson.value
  );

  return indexLesson === dataSet.value.lessons.length - 1;
});

const completedPercent = computed(() => {
  if (!currentContent.value) return;
  const indexContent = dataLesson.value.contents.indexOf(currentContent.value);

  const percent = (indexContent / dataLesson.value.contents.length) * 100;

  return Math.floor(percent);
});
onMounted(() => {
  fetchCourse();
});
</script>


<template>
  <main class="py-5">
    <section>
      <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h4 class="mb-0">{{ dataLesson?.name }}</h4>
        </div>
        <div
          class="progress mb-5"
          role="progressbar"
          aria-label="Example with label"
          aria-valuenow="16"
          aria-valuemin="0"
          aria-valuemax="100"
        >
          <div
            class="progress-bar"
            :style="{ width: completedPercent + '%' }"
          ></div>
        </div>

        <div v-if="currentContent?.type === 'quiz'">
          <div class="mb-4">
            <p class="mb-4">{{ currentContent?.content }}</p>

            <div>
              <div
                class="my-2"
                v-for="(option, index) in currentContent?.options"
                :key="index"
              >
                <input
                  v-model="answerInput"
                  :value="option"
                  type="radio"
                  :id="option?.id"
                  name="answer"
                  class="input-choice"
                />
                <label :for="option?.id" class="card">
                  <div class="card-body">{{ option?.option_text }}</div>
                </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <p @click="checkAnswered" class="btn btn-primary w-100 mt-2">
                Check
              </p>
            </div>
          </div>
        </div>
        <div v-if="currentContent?.type === 'learn'">
          <div class="container">
            <div class="mb-4">
              <p class="mb-4">{{ currentContent?.content }}</p>
            </div>

            <div class="row">
              <div class="col-md-12">
                <p @click="incrementOrder" class="btn btn-primary w-100 mt-2">
                  Continue
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>