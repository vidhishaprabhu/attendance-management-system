<template>
<div>
  <div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6 text-center">List of Leaves</h1>
    <div class="flex flex-wrap gap-4 mb-6 items-center justify-center">
      <div>
        <label class="block mb-1 font-semibold">Status</label>
        <select v-model="filters.status" class="border p-2 rounded w-48">
          <option value="">All</option>
          <option value="Pending">Pending</option>
          <option value="Approved">Approved</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
      <br>
      <div>
        <label class="block mb-1 font-semibold">From Date</label>
        <input type="date" v-model="filters.from_date" class="border p-2 rounded" />
      </div>
      <br>
      <div>
        <label class="block mb-1 font-semibold">Upto Date</label>
        <input type="date" v-model="filters.upto_date" class="border p-2 rounded" />
      </div>
      <br>
      <div class="flex items-end gap-2">
        <button @click="applyFilter" class="btn btn-primary text-white px-4 py-2 rounded">Filter</button>
      </div>
      <br>
      <div class="flex items-end gap-2">
        <button @click="resetLeaves" class="btn btn-primary text-white px-4 py-2 rounded">Clear</button>
      </div>
    </div>
    <div v-if="loading">Wait Loading....</div>
    <div v-else-if="leaves.length===0">Leaves list not found</div>
   <div class="container mt-4 text-center">
  <div class="table-responsive d-flex justify-content-center">
    <table class="table table-bordered table-hover shadow w-auto">
      <thead class="table-secondary">
        <tr>
          <th>From Date</th>
          <th>Upto Date</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(leave,index) in leaves" :key="index">
          <td>{{ leave.from_date }}</td>
          <td>{{ leave.upto_date }}</td>
          <td>{{ leave.reason }}</td>
          <td>
            <span :class="badgeClass(leave.status)">
              {{ leave.status }}
            </span>
          </td>
          <td>
            <button 
              class="btn btn-danger btn-sm" 
              v-if="leave.status === 'Pending' || leave.status === 'Approve'" 
              @click="cancelLeave(leave.id)">
              Cancel
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">Download Monthly Leave Report in PDF</h5>
        <div class="row">
          <div class="col-md-5">
            <input type="month" class="form-control" v-model="selectedMonth" />
          </div>
          <div class="col-md-4">
            <button class="btn btn-outline-primary" @click="downloadPdfReport">Download PDF</button>
          </div>
        </div>
      </div>
    </div>

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
      loading: true,
      filters: {
        status: '',
        from_date: '',
        upto_date: ''
      }
    }
  },
  mounted() {
    this.fetchLeaves();
  },
  methods: {
    async downloadPdfReport() {
      if (!this.selectedMonth) {
        alert("Please select a month.");
        return;
      }
      const [year, month] = this.selectedMonth.split("-");
      try {
        const response = await axios.post(
          '/leave/export-pdf', {}, {
            params: {
              month,
              year
            },
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
            responseType: 'blob',
          }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `leave_${month}_${year}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error("PDF download failed", error);
      }
    },
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
      } finally {
        this.loading = false;
      }
    },
    async applyFilter() {
      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        const params = {
          status: this.filters.status,
          from_date: this.filters.from_date,
          upto_date: this.filters.upto_date
        };
        const response = await axios.get('/leaves/filter', {
          headers: {
            Authorization: `Bearer ${token}`
          },
          params,
        });
        this.leaves = response.data;

      } catch (error) {
        alert("Error in filtering the data");
      } finally {
        this.loading = false;
      }
    },
    resetLeaves() {
      this.filters.status = '';
      this.filters.from_date = '';
      this.filters.upto_date = '';
      this.fetchLeaves();
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
    badgeClass(status) {
      return {
        'bg-success bg-opacity-25 text-success px-3 py-1 rounded-pill small fw-medium': status === 'Approved',
        'bg-warning bg-opacity-25 text-warning px-3 py-1 rounded-pill small fw-semibold': status === 'Pending',
        'bg-secondary bg-opacity-25 text-dark px-3 py-1 rounded-pill small fw-semibold': status === 'Cancelled',
      };
    },

  }

}
</script>
