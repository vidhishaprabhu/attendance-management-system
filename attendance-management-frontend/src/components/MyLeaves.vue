<template>
<div>
  <div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6 text-center">List of Leaves</h1>
    <div v-if="leaves.length===0">Leaves list not found</div>
    <table v-else class="w-full border border-collapse mt-4 shadow-md">
      <thead>
        <tr class="bg-gray">
          <th class="border px-5 py-4">From Date</th>
          <th class="border px-5 py-4">Upto Date</th>
          <th class="border px-5 py-4">Reason</th>
          <th class="border px-5 py-4">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(leave,index) in leaves" :key="index">
          <td class="border px-5 py-4">{{leave.from_date}}</td>
          <td class="border px-5 py-4">{{leave.upto_date}}</td>
          <td class="border px-5 py-4">{{leave.reason}}</td>
          <td class="border px-4 py-2">
            <span :class="statusClass(leave.status)">
              {{ leave.status }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</div>
</template>

<script>
import axios from '../axios';
export default {
  name: 'MyLeaves',
  data() {
    return {
      leaves: [],     
    }
  },
  mounted() {
    this.fetchLeaves();
  },
  methods: {
    async fetchLeaves() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('leaves', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.leaves = response.data;
      } catch (error) {
        alert('Error in fetching the leaves ', error);
      } 
    },
    statusClass(status) {
      return {
        'text-green-600 font-semibold': status === 'Approved',
        'text-yellow-600 font-semibold': status === 'Pending',
        'text-red-600 font-semibold': status === 'Cancelled',
      };
    },

  }

}
</script>
