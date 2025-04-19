<script setup>
import {  Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const menuOpen = ref(true)
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white w-64 flex-shrink-0 flex flex-col">
      <div class="p-4 text-2xl font-bold border-b border-gray-700">
        📘 Gestion Stages
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <Link href="/dashboard" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700"
          :class="{ 'bg-gray-700': $page.url === '/dashboard' }">
        <span>🏠</span><span>Accueil</span>
        </Link>
        <Link href="/users" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700"
          :class="{ 'bg-gray-700': $page.url.startsWith('/users') }">
        <span>👤</span><span>Utilisateurs</span>
        </Link>
        <Link href="/structures" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700"
          :class="{ 'bg-gray-700': $page.url.startsWith('/structures') }">
        <span>🏢</span><span>Structures</span>
        </Link>
        <Link href="/agents" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700"
          :class="{ 'bg-gray-700': $page.url.startsWith('/agents') }">
        <span>🧑‍💼</span><span>Agents</span>
        </Link>
        <Link href="/stagiaires" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700"
          :class="{ 'bg-gray-700': $page.url.startsWith('/stagiaires') }">
        <span>🎓</span><span>Stagiaires</span>
        </Link>
      </nav>
      <div class="p-4 border-t border-gray-700 text-sm text-gray-300">
        &copy; {{ new Date().getFullYear() }} - Gestions stages
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow p-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">Bienvenue</h1>
        <button @click="menuOpen = !menuOpen" class="sm:hidden text-gray-600">
          <span v-if="!menuOpen">☰</span>
          <span v-else>✖</span>
        </button>
      </header>

      <main class="flex-1 p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Ajout de transitions si besoin */
a {
  transition: background-color 0.3s ease;
}
</style>
