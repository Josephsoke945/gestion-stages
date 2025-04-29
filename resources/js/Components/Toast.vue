<template>
  <TransitionGroup 
    tag="div" 
    class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-md"
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform translate-x-full opacity-0"
    enter-to-class="transform translate-x-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform translate-x-0 opacity-100"
    leave-to-class="transform translate-x-full opacity-0"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="[
        'rounded-lg shadow-lg p-4 flex items-center border-l-4 min-w-80',
        toast.type === 'success' && 'bg-green-50 border-green-500 text-green-800',
        toast.type === 'error' && 'bg-red-50 border-red-500 text-red-800',
        toast.type === 'warning' && 'bg-yellow-50 border-yellow-500 text-yellow-800',
        toast.type === 'info' && 'bg-blue-50 border-blue-500 text-blue-800',
      ]"
    >
      <div class="mr-3">
        <svg v-if="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <svg v-if="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <svg v-if="toast.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <svg v-if="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="flex-1">
        <div class="font-medium">{{ toast.title }}</div>
        <div class="text-sm opacity-90">{{ toast.message }}</div>
      </div>
      <button 
        @click="removeToast(toast.id)" 
        class="ml-4 text-gray-400 hover:text-gray-600 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </TransitionGroup>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let counter = 0;

// Fonction pour ajouter un toast
const addToast = (toast) => {
  const id = counter++;
  toasts.value.push({
    id,
    ...toast,
    timeout: setTimeout(() => removeToast(id), 5000) // Disparaît après 5 secondes
  });
};

// Fonction pour retirer un toast
const removeToast = (id) => {
  const index = toasts.value.findIndex(toast => toast.id === id);
  if (index !== -1) {
    clearTimeout(toasts.value[index].timeout);
    toasts.value.splice(index, 1);
  }
};

// Exposer la méthode pour l'utiliser globalement
defineExpose({ addToast, removeToast });
</script>