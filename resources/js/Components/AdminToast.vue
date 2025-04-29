<template>
  <div class="toast-container fixed top-4 right-4 z-50 w-80 space-y-3">
    <transition-group name="toast" tag="div">
      <div 
        v-for="(toast, index) in toasts" 
        :key="toast.id" 
        class="toast-item rounded-lg shadow-lg overflow-hidden border-l-4 flex items-start p-4"
        :class="getToastClasses(toast.type)"
      >
        <!-- Icône selon le type -->
        <div class="flex-shrink-0 mr-3 mt-0.5">
          <svg v-if="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <svg v-else-if="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <svg v-else-if="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <svg v-else-if="toast.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>

        <!-- Contenu -->
        <div class="flex-1">
          <div class="font-medium" :class="getTitleClasses(toast.type)">{{ toast.title }}</div>
          <p class="text-sm opacity-90">{{ toast.message }}</p>
        </div>

        <!-- Bouton de fermeture -->
        <button 
          @click="removeToast(index)" 
          class="ml-3 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const toasts = ref([]);
let toastIdCounter = 0;
let flashCheckInterval = null;

// Fonction pour ajouter un toast
function addToast({ type = 'info', title = '', message = '', duration = 5000 }) {
  const id = toastIdCounter++;
  toasts.value.push({ id, type, title, message });

  // Auto-suppression après la durée spécifiée
  if (duration > 0) {
    setTimeout(() => {
      const index = toasts.value.findIndex(t => t.id === id);
      if (index !== -1) {
        removeToast(index);
      }
    }, duration);
  }

  return id;
}

// Fonction pour supprimer un toast
function removeToast(index) {
  toasts.value.splice(index, 1);
}

// Classes de style selon le type de toast
function getToastClasses(type) {
  switch (type) {
    case 'success':
      return 'bg-green-50 border-green-500';
    case 'error':
      return 'bg-red-50 border-red-500';
    case 'warning':
      return 'bg-yellow-50 border-yellow-500';
    case 'info':
    default:
      return 'bg-blue-50 border-blue-500';
  }
}

// Classes pour le titre selon le type
function getTitleClasses(type) {
  switch (type) {
    case 'success':
      return 'text-green-800';
    case 'error':
      return 'text-red-800';
    case 'warning':
      return 'text-yellow-800';
    case 'info':
    default:
      return 'text-blue-800';
  }
}

// Vérifier les messages flash depuis Inertia
function checkFlashMessages() {
  const { flash } = usePage().props;
  
  if (flash) {
    if (flash.success) {
      addToast({
        type: 'success',
        title: 'Succès',
        message: flash.success
      });
    }
    
    if (flash.error) {
      addToast({
        type: 'error',
        title: 'Erreur',
        message: flash.error
      });
    }
    
    if (flash.warning) {
      addToast({
        type: 'warning',
        title: 'Attention',
        message: flash.warning
      });
    }
    
    if (flash.info) {
      addToast({
        type: 'info',
        title: 'Information',
        message: flash.info
      });
    }
  }
}

// Hook de cycle de vie pour surveiller les changements de props
onMounted(() => {
  // Vérifier les messages flash au chargement initial
  checkFlashMessages();
  
  // Configurer un intervalle pour vérifier régulièrement les nouveaux messages flash
  flashCheckInterval = setInterval(checkFlashMessages, 500);
});

onUnmounted(() => {
  // Nettoyer l'intervalle lors du démontage du composant
  if (flashCheckInterval) {
    clearInterval(flashCheckInterval);
  }
});

// Exposer les méthodes
defineExpose({
  addToast,
  removeToast
});
</script>

<style scoped>
/* Animation pour les toasts */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  transform: translateX(100%);
  opacity: 0;
}
.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style> 