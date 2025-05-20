<template>
    <div class="p-6">
      <h1 class="text-xl font-bold mb-4">Profil</h1>
      <div v-if="error" class="text-red-600">{{ error }}</div>
      <div v-else-if="profile">
        <p><strong>Nama:</strong> {{ profile.nama }}</p>
        <p><strong>Email:</strong> {{ profile.email }}</p>
        <p><strong>Alamat:</strong> {{ profile.alamat }}</p>
        <p><strong>Kontak:</strong> {{ profile.kontak }}</p>
      </div>
      <div v-else>
        <p>Memuat data profil...</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        profile: null,
        error: ''
      };
    },
    async created() {
      try {
        const token = localStorage.getItem('token');
  
        const res = await fetch('http://localhost:8000/api/profile', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
  
        if (!res.ok) {
          const errData = await res.json();
          throw new Error(errData.message || 'Gagal fetch data');
        }
  
        const data = await res.json();
        this.profile = data;
      } catch (err) {
        this.error = err.message;
      }
    }
  };
  </script>
  