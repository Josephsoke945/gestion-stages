<template>
    <nav class="bg-gray-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-3">
          <!-- Logo ou nom -->
          <div class="text-xl font-semibold">Mon Application</div>
  
          <!-- Liens de navigation -->
          <div class="space-x-4">
            <router-link to="/" class="hover:text-gray-300">Accueil</router-link>
            <router-link to="/structures" class="hover:text-gray-300">Structures</router-link>
            <router-link to="/agents" class="hover:text-gray-300">Agents</router-link>
            <router-link to="/stages" class="hover:text-gray-300">Stages</router-link>
          </div>
        </div>
      </div>
    </nav>
  </template>
  
  <script setup>
  // Pas de logique ici, juste la structure du layout
  </script>
  
  <style scoped>
  /* Style global pour la navbar */
  nav {
    background-color: #2d3748; /* Couleur de fond sombre */
  }
  
  nav a {
    text-decoration: none;
  }
  
  nav a:hover {
    text-decoration: underline;
  }
  </style>
  