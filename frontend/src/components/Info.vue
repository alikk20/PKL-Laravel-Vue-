<script>
export default {
  data() {
    return {
      namaUser: '',
      idNumber: '',
      role: '',
      image: '',
      pageTitle: '',
      showDropdown: false,
      showProfileModal: false,
      showIndustriModal: false,
      showPKLModal: false,
      sudahDaftarPKL: false,
      form: {
        nama: '',
        nis: '',
        email: '',
        alamat: '',
        kontak: '',
        old_password: '',
        new_password: '',
        confirm_password: ''
      },
      industriform: {
        nama: '',
        bidang_usaha: '',
        alamat: '',
        kontak: '',
        email: ''
      },
      pklForm: {
        industri_id: '',
        guru_id: '',
        mulai: '',
        selesai: ''
      },
      
      industries: [],
      teachers: []  
    };
  },

  created() {
    const role = localStorage.getItem('role');
    const userData = localStorage.getItem('user');

    if (role && userData) {
      const user = JSON.parse(userData);
      this.namaUser = user.nama;
      this.role = role;
      this.image = user.image 
        ? `http://localhost:8000/storage/${user.image}` 
        : '';

      if (role === 'student') {
        this.idNumber = user.nis;
        this.cekStatusPKL();
      } else if (role === 'teacher') {
        this.idNumber = user.nip;
      }
    } else {
      this.$router.push('/login');
    }

    this.setPageTitle();
    this.fetchIndustriDanGuru();
  },
  watch: {
    '$route'(to) {
      this.setPageTitle();
    }
  },
  methods: {

    setPageTitle() {
      if (this.$route.path === '/dashboard') {
        this.pageTitle = 'Data Siswa PKL';
      } else if (this.$route.path === '/industri') {
        this.pageTitle = 'Data Industri';
      }
    },

    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },

    async cekStatusPKL() {
      const token = localStorage.getItem('token');
      if (!token) return;

      try {
        const res = await fetch('http://localhost:8000/api/internship/cek', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        if (!res.ok) throw new Error('Gagal cek status PKL');

        const data = await res.json();
        this.sudahDaftarPKL = data.sudah_daftar; // contoh: { sudah_daftar: true }

      } catch (err) {
        console.error('Error cek status PKL:', err);
      }
    },

    async openProfileModal() {
      this.showProfileModal = true;
      this.showDropdown = false;

      const token = localStorage.getItem('token');
      if (!token) {
        alert('Token tidak ditemukan! Harap login terlebih dahulu.');
        this.$router.push('/login');
        return;
      }

      try {
        const response = await fetch('http://localhost:8000/api/profile', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        if (!response.ok) {
          throw new Error('Gagal mengambil data profil.');
        }

        const data = await response.json();

        this.form = {
          nama: data.nama || '',
          nis: data.nis || '',
          email: data.email || '',
          alamat: data.alamat || '',
          kontak: data.kontak || ''
        };
      } catch (error) {
        alert('Terjadi kesalahan: ' + error.message);
      }
    },

    closeProfileModal() {
      this.showProfileModal = false;
    },

    async submitIndustri() {
      const token = localStorage.getItem('token');
      if (!token) {
        alert('Token tidak ditemukan! Harap login terlebih dahulu.');
        this.$router.push('/login');
        return;
      }

      try {
        const res = await fetch('http://localhost:8000/api/industry', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.industriform)
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan industri');

        alert('Industri berhasil didaftarkan!');
        this.showIndustriModal = false;

        window.location.reload(); 

        // Kosongkan form setelah submit
        this.industriform = {
          nama: '',
          bidang_usaha: '',
          alamat: '',
          kontak: '',
          email: ''
        };

      } catch (err) {
        alert('Gagal simpan industri: ' + err.message);
      }
    },

    handleDaftarClick() {
      if (this.pageTitle === 'Data Industri') {
        this.showIndustriModal = true;
      } else if (this.pageTitle === 'Data Siswa PKL') {
        if (this.sudahDaftarPKL) {
          alert('Anda sudah mendaftar PKL.');
        } else {
          this.showPKLModal = true;
        }
      }
    },

    async submitPKL() {
      const token = localStorage.getItem('token');
      if (!token) {
        alert('Token tidak ditemukan! Harap login terlebih dahulu.');
        this.$router.push('/login');
        return;
      }

      try {
        const token = localStorage.getItem('token'); // misal disimpan di sini
          console.log('Token:', token);

          const res = await fetch('http://localhost:8000/api/internships', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify(this.pklForm)
          });


        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal Mendaftar PKL');

        alert('PKL berhasil didaftarkan!');
        
        this.showPKLModal = false;

        this.pklForm = {
          industri_id: '',
          guru_id: '',
          mulai: '',
          selesai: ''
        };

        window.location.reload();

      } catch (err) {
        alert('Error: ' + err.message);
      }
    },

    async fetchIndustriDanGuru() {
      try {
        const token = localStorage.getItem('token');
        const [industriRes, guruRes] = await Promise.all([
          fetch('http://localhost:8000/api/industry', {
            headers: { 'Authorization': `Bearer ${token}` }
          }),
          fetch('http://localhost:8000/api/teachers', {
            headers: { 'Authorization': `Bearer ${token}` }
          })
        ]);

        const industriData = await industriRes.json();
        const guruData = await guruRes.json();

        this.industries = industriData || [];
        this.teachers = guruData || [];

      } catch (err) {
        console.error('Gagal fetch data industri/guru:', err);
      }
    },

    async fetchInternships() {
      try {
        const response = await fetch('http://localhost:8000/api/internships');
        const data = await response.json();
        this.internships = data;
      } catch (error) {
        console.error('Error fetching internships:', error);
      }
    },
    getInitials(name) {
      if (!name) return '';
      const parts = name.trim().split(' ');
      if (parts.length === 1) return parts[0][0].toUpperCase();
      return (parts[0][0] + parts[1][0]).toUpperCase();
    },
    handleImageError() {
      this.image = '';
    },

    async updateProfile() {

      if (this.form.old_password || this.form.new_password || this.form.confirm_password) {
        if (!this.form.old_password || !this.form.new_password || !this.form.confirm_password) {
          alert("Isi semua kolom password untuk mengubah password.");
          return;
        }

        if (this.form.new_password !== this.form.confirm_password) {
          alert("Password baru dan konfirmasi tidak cocok.");
          return;
        }
      }

      const token = localStorage.getItem('token');
      if (!token) {
        alert('Token tidak ditemukan! Harap login terlebih dahulu.');
        this.$router.push('/login');
        return;
      }

      try {
        const res = await fetch('http://localhost:8000/api/profile', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.form)
        });

        if (!res.ok) {
          const data = await res.json();
          throw new Error(data.message || 'Gagal update profil');
        }

        const data = await res.json();
        alert(data.message);
        this.closeProfileModal();

        const updatedUser = JSON.parse(localStorage.getItem('user'));
        updatedUser.nis = this.form.nis;
        localStorage.setItem('user', JSON.stringify(updatedUser));

      } catch (err) {
        alert('Error: ' + err.message);
      }
    },

    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      localStorage.removeItem('user');
      this.$router.push('/login');
    },
  }

};
</script>

<template>
  <!-- NAVBAR & PROFILE -->
  <div class="flex justify-between items-center pb-4 px-4 relative">
    <h1 class="text-2xl font-bold text-gray-800">Hi, {{ namaUser }} 👋🏼 - {{ idNumber }}</h1>
    <div class="relative">
      <div @click="toggleDropdown">
        <!-- Tampilkan gambar jika ada -->
        <img
          v-if="image"
          :src="image"
          @error="handleImageError"
          alt="Foto Profil"
          class="w-12 h-12 rounded-full object-cover border-2 border-gray-800 cursor-pointer"
        />

        <!-- Tampilkan inisial jika tidak ada gambar -->
        <div
          v-else
          class="w-12 h-12 rounded-full border-2 border-gray-800 bg-gray-300 text-black flex items-center justify-center cursor-pointer font-semibold text-lg"
        >
          {{ getInitials(namaUser) }}
        </div>
      </div>
      <div v-if="showDropdown" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-md z-50">
        <ul class="py-2">
          <button v-if="role === 'student'" @click="openProfileModal" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            Profile
          </button>
          <li>
            <button @click="logout" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
              Logout
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!-- END NAVBAR & PROFILE -->

  <!-- JUDUL TABLE & DAFTAR -->
  <div class="pb-4 flex justify-between items-center px-8 py-4">
    <h1 class="text-xl font-bold text-gray-800">
        {{ pageTitle }}
    </h1>
      <button
        v-if="role === 'student'"
        @click="handleDaftarClick"
        class="bg-blue-500 hover:bg-blue-600 text-xl font-bold text-white px-10 py-3 rounded-lg">
        Daftar
      </button>
  </div>
  <!-- MODAL DAFTAR PKL -->
  <div v-if="showPKLModal && pageTitle === 'Data Siswa PKL'" class="fixed inset-0 z-50 backdrop-brightness-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-2xl relative">
      <button @click="showPKLModal = false" class="absolute top-3 right-4 text-gray-500 hover:text-black text-xl font-bold">×</button>
      <h2 class="text-lg font-bold mb-4">Tambah Data PKL</h2>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Industri</label>
          <select v-model="pklForm.industri_id" class="w-full border px-3 py-2 rounded" required>
            <option disabled value="">Pilih Industri</option>
            <option v-for="industri in industries" :key="industri.id" :value="industri.id">
              {{ industri.nama }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium">Guru Pembimbing</label>
          <select v-model="pklForm.guru_id" class="w-full border px-3 py-2 rounded" required>
            <option disabled value="">Pilih Guru</option>
            <option v-for="guru in teachers" :key="guru.id" :value="guru.id">
              {{ guru.nama }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium">Tanggal Mulai</label>
          <input type="date" v-model="pklForm.mulai" class="w-full border px-3 py-2 rounded" required />
        </div>

        <div>
          <label class="block text-sm font-medium">Tanggal Selesai</label>
          <input type="date" v-model="pklForm.selesai" class="w-full border px-3 py-2 rounded" required />
        </div>
      </div>

      <div class="mt-6 text-right">
        <button @click="submitPKL" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
          Simpan
        </button>
      </div>
    </div>
  </div>  
  <!-- END MODAL DAFTAR PKL -->
  <!-- Modal Daftar Industri -->
  <div v-if="showIndustriModal && pageTitle === 'Data Industri'" class="fixed inset-0 z-50 backdrop-brightness-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-2xl relative">
      <button @click="showIndustriModal = false" class="absolute top-3 right-4 text-gray-500 hover:text-black text-xl font-bold">×</button>
      <h2 class="text-lg font-bold mb-4">Daftar Industri</h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Nama Industri</label>
          <input type="text" v-model="industriform.nama" class="w-full border px-3 py-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Bidang Usaha</label>
          <input type="text" v-model="industriform.bidang_usaha" class="w-full border px-3 py-2 rounded" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Email</label>
          <input type="email" v-model="industriform.email" class="w-full border px-3 py-2 rounded" required />
          <p class="pt-1 text-xs text-gray-500">Harus mengandung simbol '@'</p>
        </div>
        <div>
          <label class="block text-sm font-medium">Kontak</label>
          <input type="text" v-model="industriform.kontak" class="w-full border px-3 py-2 rounded" required />
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium">Alamat</label>
          <input type="text" v-model="industriform.alamat" class="w-full border px-3 py-3 rounded" required />
        </div>
      </div>
      <div class="mt-6 text-right">
        <button @click="submitIndustri" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
          Simpan
        </button>
      </div>
    </div>
  </div>
  <!-- END JUDUL TABLE & DAFTAR -->

  <!-- MODAL PROFILE -->
  <div v-if="showProfileModal" class="fixed inset-0 backdrop-brightness-50 z-50 flex items-center justify-center">
    <div class="bg-white w-200 rounded-lg shadow-lg p-8 relative">
      <button @click="closeProfileModal" class="absolute top-2 right-3 text-gray-500 hover:text-black text-xl font-bold">×</button>
      <h2 class="text-xl font-bold mb-4">Profile</h2>
      <div class="flex gap-6">
        <!-- Kolom Kiri -->
        <div class="w-1/2 space-y-4">
          <div>
            <label class="block text-sm font-medium">Name</label>
            <input type="text" v-model="form.nama" class="w-full border px-3 py-2 rounded bg-gray-100" readonly />
          </div>

          <div>
            <label class="block text-sm font-medium">NIS</label>
            <input type="text" v-model="form.nis" class="w-full border px-3 py-2 rounded bg-gray-100" readonly />
          </div>

          <div>
            <label class="block text-sm font-medium">Email Address</label>
            <input type="email" v-model="form.email" class="w-full border px-3 py-2 rounded" />
          </div>

          <div>
            <label class="block text-sm font-medium">Alamat</label>
            <input type="text" v-model="form.alamat" class="w-full border px-3 py-2 rounded" />
          </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="w-1/2 space-y-4">
          <div>
            <label class="block text-sm font-medium">Kontak</label>
            <input type="text" v-model="form.kontak" class="w-full border px-3 py-2 rounded" />
          </div>

          <div>
            <label class="block text-sm font-medium">Old Password</label>
            <input type="password" v-model="form.old_password" class="w-full border px-3 py-2 rounded" />
          </div>

          <div>
            <label class="block text-sm font-medium">New Password</label>
            <input type="password" v-model="form.new_password" class="w-full border px-3 py-2 rounded" />
          </div>

          <div>
            <label class="block text-sm font-medium">Confirm Password</label>
            <input type="password" v-model="form.confirm_password" class="w-full border px-3 py-2 rounded" />
          </div>
        </div>
      </div>

      <!-- Tombol Update -->
      <div class="mt-6 text-right">
        <button @click="updateProfile" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
          Update Profile
        </button>
      </div>

    </div>
  </div>
  <!-- END MODAL PROFILE -->

  
</template>
