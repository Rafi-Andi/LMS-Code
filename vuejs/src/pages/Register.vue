<script setup>
import { ref } from "vue";
import api from "../routes/axios";
import router from "../routes";

const formRegister = ref({
  full_name: "",
  username: "",
  password: "",
});

const handleRegister = async () => {
  try {
    const ress = await api.post("register", formRegister.value);
    console.log(ress);
    alert(ress.data.message);
    localStorage.setItem("token", ress.data.data.token);
    localStorage.setItem("full_name", ress.data.data.full_name);
    router.push({ name: "Index" });
  } catch (error) {
    console.log(error);
    alert(error.response.data.message);
  }
};
</script>

<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container">
        <p class="navbar-brand">LMS Code</p>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <router-link :to="{ name: 'Login' }" class="nav-link"
                >Login</router-link
              >
            </li>
            <li class="nav-item">
              <router-link :to="{ name: 'Register' }" class="nav-link"
                >Register</router-link
              >
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="py-5">
      <section>
        <div class="container">
          <h3 class="mb-3 text-center">Register</h3>

          <div class="row justify-content-center">
            <div class="col-md-7">
              <div class="card mb-4">
                <div class="card-body">
                  <form @submit.prevent="handleRegister">
                    <div class="form-group mb-2">
                      <label for="full_name">Full Name</label>
                      <input
                        v-model="formRegister.full_name"
                        type="text"
                        name="full_name"
                        id="full_name"
                        class="form-control"
                        autofocus
                      />
                    </div>
                    <div class="form-group mb-2">
                      <label for="username">Username</label>
                      <input
                        v-model="formRegister.username"
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                      />
                    </div>
                    <div class="form-group mb-2">
                      <label for="password">Password</label>
                      <input
                        v-model="formRegister.password"
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                      />
                    </div>
                    <div class="mt-3">
                      <button type="submit" class="btn btn-primary w-100">
                        Register
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <p class="text-center">
                You have an account?
                <router-link :to="{ name: 'Login' }">Login</router-link>
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>