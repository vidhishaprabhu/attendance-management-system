<template>
<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <div class="form bg-white p-4 rounded shadow-sm" style="min-width: 350px;">
    <h3 class="text-center mb-4">Login</h3>
    <input type="email" v-model="email" class="form-control mb-3" placeholder="Enter your Email" required>
    <input type="password" v-model="password" class="form-control mb-3" placeholder="Enter your Password" required>
    <button @click="login" class="btn btn-primary w-100">Login</button>
  </div>
</div>
</template>

<script>
import axios from '../axios'; 
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
    }
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password
        });
        localStorage.setItem('token', response.data.token);
        alert("User logged in successfully");
        this.$router.push('/dashboard');
      } 
      catch (error) {
        console.error("Error during login", error);
      }
    }
  }

}
</script>
