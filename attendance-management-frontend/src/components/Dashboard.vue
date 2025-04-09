<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Attendance Dashboard</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="markAttendance">
              <div class="mb-3">
                <label class="form-label">Date:</label>
                <input type="date" class="form-control" required v-model="date" />
              </div>
              <div class="mb-3">
                <label class="form-label">Status:</label>
                <select class="form-select" v-model="status" required>
                  <option value="true">Present</option>
                  <option value="false">Absent</option>
                </select>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-success">Mark Attendance</button>
              </div>
            </form>
          </div>
        </div>
        <div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">Download Monthly Attendance Report in Excel</h5>
    <div class="row">
      <div class="col-md-5">
        <input type="month" class="form-control" v-model="selectedMonth" />
      </div>
      <div class="col-md-4">
        <button class="btn btn-outline-primary" @click="downloadReport">Download Excel</button>
      </div>
    </div>
  </div>
  <div class="card-body">
    <h5 class="card-title">Download Monthly Attendance Report in PDF</h5>
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

        <div class="mt-4">
          <h4 class="text-center">Attendance History</h4>
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(record, index) in attendances" :key="index">
                <td>{{ record.date }}</td>
                <td>{{ record.status ? 'Present' : 'Absent' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '../axios';

export default {
  name: 'Dashboard',
  data() {
    return {
      user_id: '',
      date: '',
      status: '',
      attendances: [],
      selectedMonth: '',
    };
  },
  created() {
    this.fetchUser().then(() => {
      this.fetchAttendances();
    });
  },
  methods: {
  async downloadReport() {
    if (!this.selectedMonth) {
      alert("Please select a month.");
      return;
    }
    const [year, month] = this.selectedMonth.split("-");
    try {
      const response = await axios.get('/export-attendance', {
        params: { month, year },
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
        responseType: 'blob', 
      });

      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `attendance_${month}_${year}.xlsx`);
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      console.error("Download failed", error);
    }
  },
  async downloadPdfReport() {
  if (!this.selectedMonth) {
    alert("Please select a month.");
    return;
  }
  const [year, month] = this.selectedMonth.split("-");
  try {
    const response = await axios.post(
  '/attendance/export-pdf',
  {}, // POST body is empty
  {
    params: { month, year},
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
    responseType: 'blob',
  }
);

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `attendance_${month}_${year}.pdf`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error("PDF download failed", error);
  }
},
    async fetchUser() {
      try {
        const res = await axios.get('/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.user_id = res.data.id || res.data.user.id; 
      } catch (error) {
        console.error('Error fetching user info', error);
        alert('Unable to fetch user information. Please login again.');
      }
    },

    async fetchAttendances() {
      try {
        const res = await axios.get('/attendances', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.attendances = res.data;
      } catch (error) {
        console.error('Error fetching attendances', error);
      }
    },

    async markAttendance() {
      const alreadyMarked = this.attendances.some(
        record => record.date === this.date && record.user_id === this.user_id
      );

      if (alreadyMarked) {
        alert("You have already marked attendance for this date.");
        return;
      }

      try {
        const response = await axios.post('/attendances', {
          user_id: this.user_id,
          date: this.date,
          status: this.status === 'true',
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });

        alert('Attendance marked successfully.');
        this.date = '';
        this.status = '';
        this.fetchAttendances();
      } catch (error) {
        if (error.response && error.response.status === 409) {
          alert(error.response.data.message);
        } else {
          console.error('Error in marking attendance', error);
        }
      }
    }
  }
};
</script>

<style scoped>
.table td, .table th {
  vertical-align: middle;
  text-align: center;
}
</style>
