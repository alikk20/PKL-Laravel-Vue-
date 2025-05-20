<script>
export default {
  data() {
    return {
      internships: [],
      showModal: false,
      selectedInternship: null,
      currentUserId: null,
    };
  },
  async mounted() {
    this.fetchInternships();

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
    async fetchInternships() {
      try {
        const res = await fetch('http://localhost:8000/api/internships', {
          headers: {
            Accept: 'application/json',
          }
        });
        const data = await res.json();
        this.internships = data;
      } catch (error) {
        console.error('Gagal fetch data internship:', error);
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
              v-for="(intern, index) in internships"
              :key="intern.id"
              class="hover:bg-blue-50 cursor-pointer"
              @click="openModal(intern)"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
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
                    class="w-10 h-10 rounded-full bg-gray-300 border-2 border-gray-800 text-black flex items-center justify-center font-semibold text-sm"
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
      </div>
    </main>
  </div>
  <div v-if="showModal" class="fixed inset-0 flex items-center justify-center backdrop-brightness-50 bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">Edit Data PKL</h2>

      <label class="block mb-2">
        Guru:
        <input
          v-model="selectedInternship.guru_id"
          :readonly="selectedInternship.student.id !== currentUserId"
          type="number"
          class="w-full border rounded px-2 py-1"
        />
      </label>

      <label class="block mb-2">
        Industri:
        <input
          v-model="selectedInternship.industri_id"
          :readonly="selectedInternship.student.id !== currentUserId"
          type="number"
          class="w-full border rounded px-2 py-1"
        />
      </label>

      <label class="block mb-2">
        Tanggal Mulai:
        <input
          v-model="selectedInternship.mulai"
          :readonly="selectedInternship.student.id !== currentUserId"
          type="date"
          class="w-full border rounded px-2 py-1"
        />
      </label>

      <label class="block mb-2">
        Tanggal Selesai:
        <input
          v-model="selectedInternship.selesai"
          :readonly="selectedInternship.student.id !== currentUserId"
          type="date"
          class="w-full border rounded px-2 py-1"
        />
      </label>

      <div class="mt-4 flex justify-end space-x-2">
        <button class="bg-gray-400 text-white px-4 py-2 rounded" @click="showModal = false">
          Batal
        </button>
        <button
          v-if="selectedInternship.student.id === currentUserId"
          class="bg-blue-600 text-white px-4 py-2 rounded"
          @click="updateInternship"
        >
          Simpan
        </button>
      </div>
    </div>
  </div>
</template>
