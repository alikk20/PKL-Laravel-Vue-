<script>
export default {
  data() {
    return {
      internships: [],
    };
  },
  mounted() {
    this.fetchInternships();
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
            <tr v-for="(intern, index) in internships" :key="intern.id" class="hover:bg-blue-50">
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
              <td class="px-4 py-2"> {{ formatPeriode(intern.mulai, intern.selesai) }} </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>