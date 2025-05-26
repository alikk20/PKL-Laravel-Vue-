<script>
export default {
  data() {
    return {
      industries: [],
      selectedIndustry: null,
      showEditModal: false,
      currentUserId: null,
      searchQuery: this.$route.query.search || '',
      apiUrl: 'http://localhost:8000/api/industry',
      currentPage: 1,
      itemsPerPage: 5,
    };
  },
  computed: {
    filteredIndustries() {
      if (!this.searchQuery) return this.industries;

      const keyword = this.searchQuery.toLowerCase();
      return this.industries.filter(item => {
      const namaIndustry = item.nama?.toLowerCase() || '';
      const bidang_usahaIndustry = item.bidang_usaha?.toLowerCase() || '';
      const alamatIndustry = item.alamat?.toLowerCase() || '';
        return (
          namaIndustry.includes(keyword) ||
          bidang_usahaIndustry.includes(keyword) ||
          alamatIndustry.includes(keyword)
        );
      });
    },
    paginatedIndustries() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredIndustries.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredIndustries.length / this.itemsPerPage);
    }
  },
  mounted() {
    this.fetchProfile().then(() => {
      this.fetchIndustries();
    });
  },
  methods: {
    async fetchIndustries() {
      try {
        const res = await fetch(this.apiUrl, {
          headers: {
            Accept: 'application/json',
          }
        });
        const data = await res.json();
        this.industries = data;
      } catch (error) {
        console.error('Gagal mengambil data industri:', error);
      }
    },

    async fetchProfile() {
      const token = localStorage.getItem('token');
      try {
        const res = await fetch('http://localhost:8000/api/profile', {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
          },
        });
        const data = await res.json();
        this.currentUserId = data.id;
      } catch (error) {
        console.error('Gagal mengambil profil pengguna:', error);
      }
    },

    openEditModal(industry) {
      this.selectedIndustry = { ...industry }; // clone supaya tidak langsung edit di tabel
      this.showEditModal = true;
    },

    closeEditModal() {
      this.showEditModal = false;
      this.selectedIndustry = null;
    },

    async updateIndustry() {
      if (!this.selectedIndustry || !this.selectedIndustry.id) return;
      const token = localStorage.getItem('token');

      try {
        const res = await fetch(`${this.apiUrl}/${this.selectedIndustry.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(this.selectedIndustry)
        });

        const data = await res.json();

        if (!res.ok) throw data;

        this.closeEditModal();
        await this.fetchIndustries(); // refresh data
        alert('Data berhasil diperbarui.');
      } catch (error) {
        console.error('Gagal update industri:', error);
        alert(error.message || 'Terjadi kesalahan saat update.');
      }
    },

    async deleteIndustry() {
      if (!this.selectedIndustry || !this.selectedIndustry.id) return;
      const token = localStorage.getItem('token');
      
      const konfirmasi = confirm('Yakin ingin menghapus industri ini?');
      if (!konfirmasi) return;

      try {
        const res = await fetch(`${this.apiUrl}/${this.selectedIndustry.id}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
          }
        });

        const data = await res.json();

        if (!res.ok) throw data;

        this.closeEditModal();
        await this.fetchIndustries();
        alert('Data berhasil dihapus.');
      } catch (error) {
        console.error('Gagal menghapus industri:', error);
        alert(error.message || 'Terjadi kesalahan saat menghapus.');
      }
    },

  },
  watch: {
    '$route.query.search'(val) {
      this.searchQuery = val || '';
    },
    currentPage(val) {
      if (val > this.totalPages) {
        this.currentPage = this.totalPages || 1;
      }
    }
  },
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
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Bidang Usaha</th>
              <th class="px-4 py-3">Alamat</th>
              <th class="px-4 py-3">Kontak</th>
              <th class="px-4 py-3">Email</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(industry, index) in paginatedIndustries" :key="industry.id" class="hover:bg-blue-50 border-y-2 border-gray-200" @click="openEditModal(industry)">
              <td class="px-4 py-2">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
              <td class="px-4 py-2">{{ industry.nama }}</td>
              <td class="px-4 py-2">{{ industry.bidang_usaha }}</td>
              <td class="px-4 py-2">{{ industry.alamat }}</td>
              <td class="px-4 py-2">{{ industry.kontak }}</td>
              <td class="px-4 py-2">{{ industry.email }}</td>
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
  <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-brightness-50">  
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md relative">
      <h2 class="text-xl font-semibold mb-4">Edit Industri</h2>
      <div class="space-y-4">
        <input v-model="selectedIndustry.nama" :readonly="!isOwner" placeholder="Nama" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': !isOwner }"/>
        <input v-model="selectedIndustry.bidang_usaha" :readonly="!isOwner" placeholder="Bidang Usaha" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': !isOwner }"/>
        <input v-model="selectedIndustry.alamat" :readonly="!isOwner" placeholder="Alamat" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': !isOwner }"/>
        <input v-model="selectedIndustry.kontak" :readonly="!isOwner" placeholder="Kontak" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': !isOwner }"/>
        <input v-model="selectedIndustry.email" :readonly="!isOwner" placeholder="Email" class="w-full border px-3 py-2 rounded" :class="{ 'bg-gray-100': !isOwner }"/>
      </div>
      <button @click="closeEditModal" class="absolute top-3 right-4 text-gray-500 hover:text-black text-xl font-bold">×</button>

      <div class="flex justify-between mt-4" v-if="isOwner">
        <button @click="deleteIndustry" class="px-4 py-2 bg-red-500 text-white rounded">Hapus</button>
        <button @click="updateIndustry" class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
      </div>
    </div>
  </div>

</template>

