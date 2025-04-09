<template>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="form bg-white p-4 rounded shadow-sm" style="min-width: 350px;">
      <h3 class="text-center mb-4">Register</h3>
      <input type="text" v-model="name" class="form-control mb-3" placeholder="Enter your Name" required>
      <input type="email" v-model="email" class="form-control mb-3" placeholder="Enter your Email" required>
      <input type="password" v-model="password" class="form-control mb-3" placeholder="Enter your Password" required>
      <button @click="register" class="btn btn-primary w-100">Register</button>
    </div>
  </div>
</template>

<script>
import axios from '../axios';
export default{
  name:'Register',
  data(){
    return{
      name:'',
      email:'',
      password:'',
    }
  },
  methods:{
    async register(){
      try{
        const response= await axios.post('/register',{
        name:this.name,
        email:this.email,
        password:this.password,
        })
        localStorage.setItem('token',response.data.token);
        alert("User Registered Successfully");
        this.$router.push('/login');
      }
      catch(error){
        console.error("Error during registration ",error);
      }
    }
  }
  
}
</script>