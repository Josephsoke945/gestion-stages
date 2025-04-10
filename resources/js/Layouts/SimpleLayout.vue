<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const menuOpen = ref(false); // Pour gérer l'affichage du menu hamburger
</script>

<template>
  <div>
    <!-- Barre de navigation -->
    <nav class="bg-gray-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-3">
          <!-- Logo ou nom -->
          <div class="text-xl font-semibold">Mon Application</div>

          <!-- Menu hamburger sur mobile -->
          <div class="block sm:hidden">
            <button @click="menuOpen = !menuOpen" class="text-white">
              <span v-if="!menuOpen">☰</span>
              <span v-else>✖</span>
            </button>
          </div>

          <!-- Liens de navigation -->
          <div :class="{'block': menuOpen, 'hidden': !menuOpen}" class="sm:flex space-x-4">
            <Link href="/" class="hover:text-gray-300">Accueil</Link>
            <Link href="/structures" class="hover:text-gray-300">Structures</Link>
            <Link href="/agents" class="hover:text-gray-300">Agents</Link>
            <Link href="/stages" class="hover:text-gray-300">Stages</Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main>
      <slot />
    </main>
  </div>
</template>

<style scoped>
/* Style pour la navbar */
nav {
  background-color: #2d3748; /* Couleur de fond sombre */
}

nav a {
  text-decoration: none;
}

nav a:hover {
  text-decoration: underline;
}

/* Styles du menu hamburger */
.sm\:hidden {
  display: none;
}

@media (max-width: 640px) {
  .sm\:hidden {
    display: block;
  }

  .sm\:flex {
    display: none;
  }

  .sm\:flex.block {
    display: block;
  }
}
</style>
