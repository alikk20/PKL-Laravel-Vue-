<script>
const baseUrl = import.meta.env.VITE_API_BASE_URL;
export default {
  data() {
    return {
      form: {
        nama: '',
        id_number: '',
        gender: '',
        alamat: '',
        kontak: '',
        password: ''
      },
      message: ''
    };
  },
  methods: {
    async register() {
      try {
        const res = await fetch(`${baseUrl}/register-siswa`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.form)
        });

        const data = await res.json();

        if (!res.ok) throw new Error(data.message || 'Gagal');

        this.message = 'Registrasi berhasil!';
        this.$router.push('/login');
      } catch (err) {
        this.message = 'Gagal registrasi: ' + err.message;
      }
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
}
</script>

<template>
  <div class="bg-linear-180 from-indigo-600 via-indigo-50 to-indigo-600 min-w-full h-screen flex justify-center items-center">
    <div class="bg-gray-100 px-10 py-10 rounded-lg shadow-lg w-full max-w-4xl">
      <h2 class="mb-6 text-center font-bold text-2xl text-blue-600">REGISTER SISWA</h2>

      <form @submit.prevent="register" class="grid grid-cols-2 gap-6">
        <!-- KIRI -->
        <div class="space-y-4">

          <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input v-model="form.nama" placeholder="Nama" class="w-full border rounded px-3 py-2" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">NIS</label>
            <input v-model="form.id_number" placeholder="NIS" class="w-full border rounded px-3 py-2" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" v-model="form.password" placeholder="Password" class="w-full border rounded px-3 py-2" />
          </div>
        </div>

        <!-- KANAN -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
            <select v-model="form.gender" class="w-full border rounded px-3 py-2">
              <option disabled value="">Pilih Jenis Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Alamat</label>
            <input v-model="form.alamat" placeholder="Alamat" class="w-full border rounded px-3 py-2" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Kontak</label>
            <input v-model="form.kontak" placeholder="Kontak" class="w-full border rounded px-3 py-2" />
          </div>

        </div>

        <div class="col-span-2">
          <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
            Daftar
          </button>
        </div>
      </form>
      <p class="mt-4 text-center">
        <router-link to="/login" class="text-blue-500 hover:text-blue-600 underline underline-offset-2">Login</router-link>
      </p>
    </div>
  </div>
</template>



