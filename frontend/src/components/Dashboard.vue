<script>
export default {
  data() {
    return {
      internships: [],
      teachers: [],
      industries: [],
      searchQuery: this.$route.query.search || '',
      filteredInternships: [],
      showModal: false,
      selectedInternship: null,
      currentUserId: null,
      currentPage: 1,
      itemsPerPage: 5,
    };
  },
  computed: {
    paginatedInternships() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredInternships.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredInternships.length / this.itemsPerPage);
    }
  },
  async mounted() {
    this.fetchInternships();
    this.fetchTeachers();
    this.fetchIndustries();

    try {
      const res = await fetch('http://localhost:8000/api/profile', {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      const user = await res.json();
      this.currentUserId = user.id;
    } catch (err) {
      console.error('Gagal mengambil data user:', err);
    }
  },
  methods: {
    async fetchTeachers() {
      try {
        const res = await fetch('http://localhost:8000/api/teachers', {
          headers: {
            Accept: 'application/json',
          },
        });
        this.teachers = await res.json();
      } catch (error) {
        console.error('Gagal fetch data guru:', error);
      }
    },
    async fetchIndustries() {
      try {
        const res = await fetch('http://localhost:8000/api/industry', {
          headers: {
            Accept: 'application/json',
          },
        });
        this.industries = await res.json();
      } catch (error) {
        console.error('Gagal fetch data industri:', error);
      }
    },
    async fetchInternships() {
      try {
        const res = await fetch('http://localhost:8000/api/internships', {
          headers: {
            Accept: 'application/json',
          }
        });
        const data = await res.json();
        this.internships = data;
        this.filterInternships();
      } catch (error) {
        console.error('Gagal fetch data internship:', error);
      }
    },
    filterInternships() {
      if (this.searchQuery) {
        const keyword = this.searchQuery.toLowerCase();
        this.filteredInternships = this.internships.filter(item => {
          const namaSiswa = item.student?.nama?.toLowerCase() || '';
          const genderSiswa = item.student?.gender?.toLowerCase() || '';
          const namaGuru = item.teacher?.nama?.toLowerCase() || '';
          const namaIndustri = item.industry?.nama?.toLowerCase() || '';
          const tanggalMulai = item.mulai?.toLowerCase() || '';

          return (
            namaSiswa.includes(keyword) ||
            genderSiswa.includes(keyword) ||
            namaGuru.includes(keyword) ||
            namaIndustri.includes(keyword) ||
            tanggalMulai.includes(keyword)
          );
        });
      } else {
        this.filteredInternships = this.internships;
      }
    },
    formatPeriode(mulai, selesai) {
      if (!mulai || !selesai) return '-';
      return `${this.formatDate(mulai)} - ${this.formatDate(selesai)}`;
    },
    formatDate(dateStr) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateStr).toLocaleDateString('id-ID', options);
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map((word) => word[0].toUpperCase())
        .join('')
        .slice(0, 2); // Maksimal 2 huruf
    },
    handleImageError(event) {
      event.target.style.display = 'none';
    },
    openModal(intern) {
      this.selectedInternship = { ...intern };
      this.showModal = true;
    },
    async updateInternship() {
      try {
        const res = await fetch(`http://localhost:8000/api/internships/${this.selectedInternship.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
          body: JSON.stringify({
            guru_id: this.selectedInternship.guru_id,
            industri_id: this.selectedInternship.industri_id,
            mulai: this.selectedInternship.mulai,
            selesai: this.selectedInternship.selesai,
          }),
        });

        const data = await res.json();
        if (res.ok) {
          alert('Data berhasil diperbarui');
          this.fetchInternships();
          this.showModal = false;
        } else {
          alert(data.message || 'Gagal memperbarui data');
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan saat mengupdate data');
      }
    },
    async deleteInternship() {
      const konfirmasi = confirm("Apakah kamu yakin ingin menghapus data ini?");
      if (!konfirmasi) return;

      try {
        const res = await fetch(`http://localhost:8000/api/internships/${this.selectedInternship.id}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });

        if (res.ok) {
          alert('Data berhasil dihapus');
          this.fetchInternships();
          this.showModal = false; 
        } else {
          const data = await res.json();
          alert(data.message || 'Gagal menghapus data');
        }
      } catch (err) {
        console.error('Terjadi kesalahan saat menghapus:', err);
        alert('Terjadi kesalahan saat menghapus data');
      }
    }
  },
  watch: {
    '$route.query.search'(newVal) {
      this.searchQuery = newVal || '';
      this.filterInternships();
    }
  }
}
</script>

<template>
  <div class="flex min-h-screen bg-gradient-to-b from-blue-200 via-white to-indigo-200 text-gray-800">
    <SideBar />
    <main class="flex-1 p-8">
      <Info />

      <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto text-left">
          <thead class="bg-blue-300 text-gray-800 font-semibold">
            <tr>
              <th class="px-4 py-3">No</th>
              <th class="px-4 py-3">Profil</th>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Gender</th>
              <th class="px-4 py-3">IDUKA</th>
              <th class="px-4 py-3">Guru Pembimbing</th>
              <th class="px-4 py-3">Periode</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(intern, index) in paginatedInternships"
              :key="intern.id"
              class="hover:bg-blue-50 cursor-pointer border-y-2 border-gray-200"
              @click="openModal(intern)"
            >
              <td class="px-4 py-2">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
              <td class="px-4 py-2">
                <div class="flex items-center space-x-2">
                  <!-- Jika ada gambar profil -->
                  <img
                    v-if="intern.student?.image"
                    :src="`http://localhost:8000/storage/${intern.student.image}`"
                    @error="handleImageError"
                    alt="Foto Profil"
                    class="w-13 h-13 rounded-full object-cover border-2 border-gray-800"
                  />
                  <!-- Jika tidak ada gambar profil, tampilkan inisial -->
                  <div
                    v-else
                    class="w-13 h-13 rounded-full bg-gray-300 border-2 border-gray-800 text-black flex items-center justify-center font-semibold text-sm"
                  >
                    {{ getInitials(intern.student?.nama) }}
                  </div>
                </div>
              </td>
              <td class="px-4 py-2">{{ intern.student.nama }}</td>
              <td class="px-4 py-2">{{ intern.student.nis }}</td>
              <td class="px-4 py-2">{{ intern.student.gender }}</td>
              <td class="px-4 py-2">{{ intern.industry.nama }}</td>
              <td class="px-4 py-2">{{ intern.teacher.nama }}</td>
              <td class="px-4 py-2">{{ formatPeriode(intern.mulai, intern.selesai) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="flex justify-center items-center p-4">
          <button
            class="px-3 py-1 bg-blue-500 text-white rounded disabled:opacity-50"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Prev
          </button>

          <span class="px-6">Page {{ currentPage }} of {{ totalPages }}</span>

          <button
            class="px-3 py-1 bg-blue-500 text-white rounded disabled:opacity-50"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
      </div>
    </main>
  </div>
  <div v-if="showModal" class="fixed inset-0 flex items-center justify-center backdrop-brightness-50 bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md relative">
      <button class="absolute px-2 py-3 top-2 right-3 text-gray-500 hover:text-black text-2xl font-bold focus:outline-none" @click="showModal = false" aria-label="Tutup">
        &times;
      </button>
        <h2 class="text-lg font-bold mb-4">Edit Data PKL</h2>
          <label class="block mb-2">
            Guru:
            <select v-model="selectedInternship.guru_id" :disabled="selectedInternship.student.id !== currentUserId" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': selectedInternship.student.id !== currentUserId }">
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                {{ teacher.nama }}
              </option>
            </select>
          </label>
          <label class="block mb-2">
            Industri:
            <select v-model="selectedInternship.industri_id" :disabled="selectedInternship.student.id !== currentUserId" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': selectedInternship.student.id !== currentUserId }">
              <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                {{ industry.nama }}
              </option>
            </select>
          </label>
          <label class="block mb-2">
            Tanggal Mulai:
            <input v-model="selectedInternship.mulai" :readonly="selectedInternship.student.id !== currentUserId" type="date" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': selectedInternship.student.id !== currentUserId }"/>
          </label>
          <label class="block mb-2">
            Tanggal Selesai:
            <input v-model="selectedInternship.selesai" :readonly="selectedInternship.student.id !== currentUserId" type="date" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': selectedInternship.student.id !== currentUserId }"/>
          </label>
      <div class="mt-4 flex justify-between space-x-2">
        <button v-if="selectedInternship.student.id === currentUserId" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" @click="deleteInternship">
          Hapus
        </button>
        <button v-if="selectedInternship.student.id === currentUserId" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" @click="updateInternship">
          Simpan
        </button>
      </div>
    </div>
  </div>
</template>
