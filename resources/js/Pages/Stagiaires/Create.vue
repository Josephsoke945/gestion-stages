<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Ajouter un Stagiaire</h1>
  
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
            <input v-model="form.nom" type="text" id="nom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.nom" class="text-red-500 text-sm">{{ form.errors.nom }}</span>
          </div>
  
          <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
            <input v-model="form.prenom" type="text" id="prenom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.prenom" class="text-red-500 text-sm">{{ form.errors.prenom }}</span>
          </div>
  
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
          </div>
  
          <div>
            <label for="date_naissance" class="block text-sm font-medium text-gray-700">Date de naissance</label>
            <input v-model="form.date_naissance" type="date" id="date_naissance" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.date_naissance" class="text-red-500 text-sm">{{ form.errors.date_naissance }}</span>
          </div>
  
          <div>
            <label for="niveau_etude" class="block text-sm font-medium text-gray-700">Niveau d'étude</label>
            <input v-model="form.niveau_etude" type="text" id="niveau_etude" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.niveau_etude" class="text-red-500 text-sm">{{ form.errors.niveau_etude }}</span>
          </div>
  
          <div>
            <label for="universite" class="block text-sm font-medium text-gray-700">Université</label>
            <input v-model="form.universite" type="text" id="universite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.universite" class="text-red-500 text-sm">{{ form.errors.universite }}</span>
          </div>
  
          <div>
            <label for="filiere" class="block text-sm font-medium text-gray-700">Filière</label>
            <input v-model="form.filiere" type="text" id="filiere" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.filiere" class="text-red-500 text-sm">{{ form.errors.filiere }}</span>
          </div>
  
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input v-model="form.password" type="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
          </div>
        </div>
  
        <div class="mt-6">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Enregistrer
          </button>
        </div>
      </form>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3'
  
  const form = useForm({
    nom: '',
    prenom: '',
    email: '',
    date_naissance: '',
    niveau_etude: '',
    universite: '',
    filiere: '',
    password: '',
  })
  
  function submit() {
    form.post(route('stagiaires.store'))
  }
  </script>
  