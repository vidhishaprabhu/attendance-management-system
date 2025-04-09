<template>
  <div class="max-w-md mx-auto mt-10 bg-white shadow-lg p-6 rounded-lg">
    <h2 class="text-2xl font-semibold mb-4 text-center">Apply for Leave</h2>

    <div class="mb-4">
      <label class="block text-gray-700">From Date:</label>
      <input type="date" v-model="form.from_date" class="w-full border px-3 py-2 rounded" />
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Upto Date:</label>
      <input type="date" v-model="form.upto_date" class="w-full border px-3 py-2 rounded" />
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Reason:</label>
      <textarea v-model="form.reason" class="w-full border px-3 py-2 rounded" rows="3"></textarea>
    </div>

    <button
      @click="applyLeave"
      class="btn btn-primary hover:bg-blue-700 text-white px-4 py-2 rounded w-full"
    >
      Submit Leave Request
    </button>

    <p v-if="message" class="mt-4 text-green-600">{{ message }}</p>
    <p v-if="error" class="mt-4 text-red-600">{{ error }}</p>
  </div>
</template>

<script>
import axios from '../axios'

export default {
  name: "ApplyLeave",
  data() {
    return {
      form: {
        from_date: '',
        upto_date: '',
        reason: '',
      },
      message: '',
      error: '',
    };
  },
  methods: {
    async applyLeave() {
      this.message = '';
      this.error = '';

      if (!this.form.from_date || !this.form.upto_date || !this.form.reason) {
        this.error = 'All fields are required.';
        return;
      }

      try {
        const token = localStorage.getItem('token');
        const response = await axios.post('apply', this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        this.message = response.data.message;
        this.form = { from_date: '', upto_date: '', reason: '' };
      } catch (err) {
        alert('Something went wrong ',this.error);
      }
    },
  },
};
</script>

