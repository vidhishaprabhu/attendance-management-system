<template>
<div>
  <div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6 text-center">List of Leaves</h1>
    <div v-if="loading">Wait Loading....</div>
    <div v-else-if="leaves.length===0">Leaves list not found</div>
    <table v-else class="w-full border border-collapse mt-4 shadow-md">
      <thead>
        <tr class="bg-gray">
          <th class="border px-5 py-4">From Date</th>
          <th class="border px-5 py-4">Upto Date</th>
          <th class="border px-5 py-4">Reason</th>
          <th class="border px-5 py-4">Status</th>
          <th class="border px-5 py-4">Action</th>
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
          <td class="border px-4 py-2">
            <span :class="statusClass(leave.status)">
              <button class="btn btn-danger text-white px-3 py-1 rounded" v-if="leave.status==='Pending' || leave.status==='Approve'" @click="cancelLeave(leave.id)"> Cancel
              </button>
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
      loading:true,
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
      finally{
        this.loading=false;
      }
    },
    async cancelLeave(id) {
      try {
        const token = localStorage.getItem('token');
        await axios.put(`/leaves/cancel/${id}`, null, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.fetchLeaves();
      } catch (error) {
        alert("Error in cancelling the leave");
        console.error(error);
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
