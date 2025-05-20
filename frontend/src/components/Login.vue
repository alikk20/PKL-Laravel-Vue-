<script>
export default {
  data() {
    return {
      form: {
        id_number: '',
        password: ''
      },
      message: '',
      showRegisterModal: false,
      selectedRole: ''
    };
  },
  methods: {
    async login() {
      try {
        const res = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.form)
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Login gagal');

        // Pastikan token disimpan dengan benar
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        localStorage.setItem('role', data.role);

        if (!data.role) {
          // Jika belum di-approve, arahkan ke halaman proses
          this.$router.push('/proses');
          return;
        }

        this.message = 'Login berhasil!';
        this.$router.push('/dashboard');

      } catch (err) {
        this.message = 'Gagal login: ' + err.message;
      }
    },

    openRegisterModal() {
      this.showRegisterModal = true;
    },

    goToRegister() {
      if (this.selectedRole === 'guru') {
        this.$router.push('/register-guru');
      } else if (this.selectedRole === 'siswa') {
        this.$router.push('/register-siswa');
      } else {
        alert('Silakan pilih salah satu role terlebih dahulu.');
      }
      this.showRegisterModal = false;
    }
  },
  watch: {
    message(newVal) {
      if (newVal) {
        alert(newVal);
        this.message = '';
      }
    }
  }
};

</script>

<template>
    <div class="bg-linear-180 from-indigo-600 via-indigo-50 to-indigo-600 min-w-full h-screen flex justify-center items-center">
      <div class="bg-gray-100 px-10 py-10 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="mb-6 text-center font-bold text-2xl text-blue-600">LOGIN</h2>
  
        <form @submit.prevent="login" class="space-y-6">
          <div>
            <label class="block text-sm font-medium mb-1">Username</label>
            <input v-model="form.id_number" placeholder="Username" class="w-full border rounded px-3 py-2" />
          </div>
  
          <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" v-model="form.password" placeholder="Password" class="w-full border rounded px-3 py-2" required />
          </div>
  
          <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded" required>
            Login
          </button>
        </form>
        <p class="mt-4 text-center">
          <span class="text-blue-600 underline cursor-pointer" @click="openRegisterModal">
            Register akun
          </span>
        </p>

        <div v-if="showRegisterModal" class="fixed inset-0 flex justify-center items-center z-50">
          <div class="bg-white p-6 rounded shadow-md w-80">
            <h2 class="text-lg font-semibold mb-4">Daftar Sebagai</h2>
            <select v-model="selectedRole" class="border w-full px-3 py-2 rounded mb-4">
              <option disabled value="">-- Pilih Role --</option>
              <option value="guru">Guru</option>
              <option value="siswa">Siswa</option>
            </select>
            <div class="flex justify-end space-x-2">
              <button @click="goToRegister" class="bg-blue-500 text-white px-4 py-2 rounded">Lanjut</button>
              <button @click="showRegisterModal = false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  

  