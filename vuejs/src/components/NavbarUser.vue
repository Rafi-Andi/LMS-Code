<script setup>
import router from "../routes";
import api from "../routes/axios";
const fullname = localStorage.getItem("full_name");
const token = localStorage.getItem("token");

if (!token) {
  router.push({ name: "Login" });
}

const handleLogout = async () => {
  try {
    const ress = await api.post("/logout");
    localStorage.removeItem("token");
    localStorage.removeItem("full_name");
    router.push({ name: "Login" });
  } catch (error) {
    alert(error.response.data.message);
  }
};
</script>

<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
      <router-link :to="{ name: 'Index' }" class="navbar-brand"
        >LMS Code</router-link
      >
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
            <a class="nav-link" href="#">{{ fullname }}</a>
          </li>
          <li class="nav-item">
            <p @click="handleLogout()" style="cursor: pointer" class="nav-link">
              Logout
            </p>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>