<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, reactive, onMounted } from 'vue';
import Stagiaire from '@/Layouts/Stagiaire.vue';
import EmailSender from '@/Components/EmailSender.vue';

const props = defineProps(['auth', 'structures', 'users']);
const showModal = ref(false);
const memberInfosLoaded = ref(false);
const codeSuivi = ref('');
const demandeId = ref(null);
const step = ref(1);
const searchQuery = ref(''); // Pour la recherche de membres
const documentsRequired = ref([]); // Pour stocker les documents requis selon le type
const showMembersList = ref(false); // Pour contrôler l'affichage de la liste des membres

// Système de toast
const toasts = ref([]);
let toastCounter = 0;

// Variable pour le code de suivi à rechercher
const searchCode = ref('');

// Fonction pour ajouter un toast
const addToast = ({ type = 'info', title = '', message = '', duration = 5000 }) => {
  const id = toastCounter++;

  toasts.value.push({
    id,
    type,
    title,
    message,
    timeout: setTimeout(() => removeToast(id), duration)
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

// Fonction pour rechercher une demande par code de suivi
const searchByTrackingCode = () => {
  if (!searchCode.value.trim()) {
    addToast({
      type: 'error',
      title: 'Erreur',
      message: 'Veuillez saisir un code de suivi',
      duration: 5000
    });
    return;
  }

  window.location = route('demandes.search.get') + `?code_suivi=${searchCode.value.trim()}`;
};

// Structure pour stocker les documents soumis par membre
const memberDocuments = reactive({});
// Structure pour stocker les infos des membres
const memberInfos = reactive({});

// Fonction pour charger les informations des membres
const loadMemberInfos = () => {
  if (props.users && props.users.length > 0) {
    props.users.forEach(user => {
      memberInfos[user.id] = {
        nom: user.nom,
        prenom: user.prenom,
        email: user.email,
        telephone: user.telephone
      };
    });
    console.log('Informations des membres chargées:', memberInfos);
  } else {
    console.error('Aucun utilisateur disponible dans props.users');
  }
  memberInfosLoaded.value = true;
};

// Appeler loadMemberInfos immédiatement pour être sûr que les données sont chargées
loadMemberInfos();

// Fonction pour gérer la recherche
const handleSearchInput = () => {
  // Montrer la liste des résultats lors de la saisie
  showMembersList.value = true;
  
  // Journalisation pour le débogage
  console.log('Recherche en cours:', searchQuery.value);
  setTimeout(() => {
    console.log('Résultats trouvés:', filteredUsers.value.length);
  }, 100);
};

// Charger les informations des membres au montage du composant
onMounted(() => {
  loadMemberInfos();
  
  // Afficher les informations sur les utilisateurs disponibles
  console.log('Montage du composant. Utilisateurs disponibles:', props.users.length);
  console.log('Premier utilisateur:', props.users.length > 0 ? props.users[0] : 'Aucun');
  
  // Vérifier que les IDs sont des nombres
  if (props.users.length > 0) {
    props.users.forEach(user => {
      console.log(`Utilisateur ${user.id} (${typeof user.id}): ${user.nom} ${user.prenom}`);
    });
  }
});

const form = useForm({
  nom: props.auth.user.nom,
  prenom: props.auth.user.prenom,
  email: props.auth.user.email,
  telephone: props.auth.user.telephone,
  universite: '',
  filiere: '',
  niveau_etude: '',
  date_debut: '',
  date_fin: '',
  structure_id: '',
  nature: 'Individuel',
  type: 'Académique', // Maintenant indépendant de la nature
  lettre_cv_path: null,
  pieces_jointes: [], // Tableau pour stocker les pièces jointes multiples
  membres: [],
});

// Fonction pour gérer le téléchargement de fichier pour un membre
const handleMemberFile = (memberId, fileType, event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (!memberDocuments[memberId]) {
    memberDocuments[memberId] = {};
  }

  memberDocuments[memberId][fileType] = file;

  // Notifications pour les documents téléchargés
  const memberName = getUserInfo(memberId).nom + ' ' + getUserInfo(memberId).prenom;
  const documentType = fileType === 'lettre_cv_path'
    ? 'Lettre de recommandation'
    : (fileType === 'cv' ? 'CV' : 'Diplômes');

  addToast({
    type: 'success',
    title: 'Document téléchargé',
    message: `${documentType} de ${memberName} ajouté avec succès`,
    duration: 3000
  });

  console.log('Document ajouté pour membre', memberId, fileType, file.name);
};

// Obtenir les informations complètes de l'utilisateur directement à partir de props.users
const getUserInfo = (userId) => {
  // Vérifier que userId est défini
  if (!userId) {
    console.error('getUserInfo appelé avec un userId invalide:', userId);
    return { nom: '', prenom: '', email: '', telephone: '' };
  }
  
  // Convertir en nombre si c'est une chaîne
  const id = typeof userId === 'string' ? parseInt(userId) : userId;
  
  // Essayer de trouver l'utilisateur directement dans props.users
  const user = props.users.find(u => u.id === id);
  if (user) {
    console.log('Utilisateur trouvé dans props.users:', user);
    return {
      nom: user.nom,
      prenom: user.prenom,
      email: user.email,
      telephone: user.telephone
    };
  }
  
  console.error('Aucun utilisateur trouvé pour id:', id);
  return { nom: '', prenom: '', email: '', telephone: '' };
};

// Filtrer les utilisateurs selon la recherche
const filteredUsers = computed(() => {
  // Vérifier que nous avons des utilisateurs
  if (!props.users || props.users.length === 0) {
    console.log('Aucun utilisateur disponible');
    return [];
  }
  
  // Si le champ de recherche est vide ou ne contient que des espaces, afficher jusqu'à 10 utilisateurs
  if (!searchQuery.value || searchQuery.value.trim() === '') {
    return props.users.slice(0, 10);
  }
  
  // Nettoyer la requête et la convertir en minuscules
  const query = searchQuery.value.toLowerCase().trim();
  
  // Filtrer les utilisateurs dont le nom ou prénom contient la requête
  const results = props.users.filter(user => {
    // S'assurer que l'utilisateur a un nom et un prénom
    if (!user.nom || !user.prenom) return false;
    
    // Créer différentes variations du nom pour la recherche
    const nom = user.nom.toLowerCase();
    const prenom = user.prenom.toLowerCase();
    const fullName = `${nom} ${prenom}`;
    const reverseName = `${prenom} ${nom}`;
    
    // Vérifier si la requête correspond à l'une des variations
    return nom.includes(query) || 
           prenom.includes(query) || 
           fullName.includes(query) || 
           reverseName.includes(query);
  });
  
  console.log(`Recherche "${query}" : ${results.length} résultats trouvés`);
  return results;
});

// Surveiller le changement de nature pour réinitialiser les membres si on passe à Individuel
watch(() => form.nature, (newValue) => {
  if (newValue === 'Individuel') {
    form.membres = [];
  }
});

// Surveiller le changement de type pour définir les documents requis
watch(() => form.type, (newValue) => {
  if (newValue === 'Académique') {
    documentsRequired.value = ['Lettre de recommandation'];
  } else {
    documentsRequired.value = ['CV', 'Diplômes'];
  }
});

// Surveiller le changement des membres pour s'assurer d'avoir leurs infos
watch(() => form.membres, (newMembers) => {
  if (newMembers && newMembers.length > 0) {
    // S'assurer que nous avons les informations pour chaque membre
    newMembers.forEach(memberId => {
      if (!memberInfos[memberId]) {
        // Chercher les informations dans props.users
        const memberData = props.users.find(user => user.id === memberId);
        if (memberData) {
          memberInfos[memberId] = {
            nom: memberData.nom,
            prenom: memberData.prenom,
            email: memberData.email,
            telephone: memberData.telephone
          };
          console.log('Informations du membre chargées:', memberInfos[memberId]);
        }
      }
    });
  }
}, { deep: true });

// Validations
const isDateValid = computed(() => {
  if (!form.date_debut || !form.date_fin) return true;
  return new Date(form.date_fin) > new Date(form.date_debut);
});

const validateStep = () => {
  if (step.value === 1) {
    return form.nom && form.prenom && form.email && form.telephone &&
      // Validation supplémentaire pour le mode groupe
      (form.nature !== 'Groupe' || (form.membres && form.membres.length > 0));
  }
  if (step.value === 2) {
    return form.universite && form.filiere && form.niveau_etude &&
      form.date_debut && form.date_fin && form.structure_id && isDateValid.value;
  }
  if (step.value === 3) {
    // Pour l'étape 3, on avertit simplement mais on permet de continuer
    checkMemberDocuments();
    return true;
  }
  return true;
};

// Navigation
const nextStep = () => {
  if (!validateStep()) {
    let errorMessage = '';

    if (step.value === 1) {
      if (form.nature === 'Groupe' && (!form.membres || form.membres.length === 0)) {
        errorMessage = "Veuillez sélectionner au moins un membre pour le groupe";
      } else {
        errorMessage = "Veuillez remplir tous les champs obligatoires";
      }
    } else if (step.value === 2) {
      if (!form.universite) errorMessage = "Le champ Université est obligatoire";
      else if (!form.filiere) errorMessage = "Le champ Spécialité est obligatoire";
      else if (!form.niveau_etude) errorMessage = "Le champ Niveau d'étude est obligatoire";
      else if (!form.date_debut) errorMessage = "Le champ Date de début est obligatoire";
      else if (!form.date_fin) errorMessage = "Le champ Date de fin est obligatoire";
      else if (!form.structure_id) errorMessage = "Veuillez sélectionner une structure";
      else if (!isDateValid.value) errorMessage = "La date de fin doit être après la date de début";
    }

    addToast({
      type: 'error',
      title: 'Champs obligatoires',
      message: errorMessage
    });

    return;
  }

  // Si les étapes sont valides, on peut afficher un message de progression
  if (step.value === 1) {
    addToast({
      type: 'info',
      title: 'Informations validées',
      message: 'Passons aux détails du stage',
      duration: 3000
    });
  } else if (step.value === 2) {
    addToast({
      type: 'info',
      title: 'Détails validés',
      message: 'Veuillez maintenant fournir les documents nécessaires',
      duration: 3000
    });
  } else if (step.value === 3) {
    addToast({
      type: 'info',
      title: 'Documents enregistrés',
      message: 'Vérifiez les informations avant la soumission finale',
      duration: 3000
    });
  }

  step.value++;
};

const prevStep = () => {
  step.value--;
};

// Fonction pour vérifier si les membres ont soumis les documents requis à l'étape 3
const checkMemberDocuments = () => {
  if (form.nature !== 'Groupe' || form.membres.length === 0) return true;

  let allDocumentsSubmitted = true;
  const missingDocuments = [];

  form.membres.forEach(memberId => {
    const member = getUserInfo(memberId);
    if (!memberDocuments[memberId] || !Object.keys(memberDocuments[memberId]).length) {
      allDocumentsSubmitted = false;
      missingDocuments.push(`${member.nom} ${member.prenom}`);
    }
  });

  if (!allDocumentsSubmitted) {
    addToast({
      type: 'warning',
      title: 'Documents manquants',
      message: `Les membres suivants n'ont pas fourni de documents : <br>${missingDocuments.join(', ')}`,
      duration: 6000
    });
  }

  return allDocumentsSubmitted;
};

// Surveillons les changements des membres pour afficher des notifications
watch(() => form.membres, (newMembers, oldMembers) => {
  if (oldMembers && newMembers.length > oldMembers.length) {
    // Un membre a été ajouté
    const newMemberId = newMembers.find(id => !oldMembers.includes(id));
    if (newMemberId) {
      const member = getUserInfo(newMemberId);
      addToast({
        type: 'success',
        title: 'Membre ajouté',
        message: `${member.nom} ${member.prenom} a été ajouté au groupe`,
        duration: 3000
      });
    }
  } else if (oldMembers && newMembers.length < oldMembers.length) {
    // Un membre a été retiré
    const removedMemberId = oldMembers.find(id => !newMembers.includes(id));
    if (removedMemberId) {
      const member = getUserInfo(removedMemberId);
      addToast({
        type: 'info',
        title: 'Membre retiré',
        message: `${member.nom} ${member.prenom} a été retiré du groupe`,
        duration: 3000
      });

      // Nettoyer les documents du membre supprimé
      if (memberDocuments[removedMemberId]) {
        delete memberDocuments[removedMemberId];
      }
    }
  }
}, { deep: true });

// Soumission
const submitRequest = () => {
  // Vérifier la validité du formulaire avant soumission
  if (!validateForm()) {
    return;
  }

  // Informer l'utilisateur que la soumission est en cours
  addToast({
    type: 'info',
    title: 'Soumission en cours',
    message: 'Veuillez patienter pendant l\'envoi de votre demande...',
    duration: 10000
  });

  // Préparation des données pour inclure les documents des membres
  const formData = new FormData();

  // Ajouter les champs de base du formulaire
  Object.keys(form).forEach(key => {
    // Ignorer les données qui seront traitées spécialement
    if (key !== 'lettre_cv_path' && key !== 'membres') {
      formData.append(key, form[key]);
    }
  });

  // Ajouter la pièce jointe principale
  if (form.lettre_cv_path) {
    formData.append('lettre_cv_path', form.lettre_cv_path);
  }

  // Ajouter les membres
  if (form.membres && form.membres.length > 0) {
    form.membres.forEach((memberId, index) => {
      formData.append(`membres[${index}]`, memberId);

      // Ajouter les documents de ce membre
      if (memberDocuments[memberId]) {
        Object.keys(memberDocuments[memberId]).forEach(docType => {
          formData.append(`membre_documents[${memberId}][${docType}]`, memberDocuments[memberId][docType]);
        });
      }
    });
  }

  // Soumettre le formulaire avec les données préparées
  form.post(route('demande_stages.store'), {
    onSuccess: (response) => {
      console.log('Réponse complète:', response);

      // Tentative pour extraire le code de suivi d'où qu'il vienne
      let codeFound = false;

      // Vérification dans différents emplacements possibles
      if (response?.props?.flash?.code_suivi) {
        codeSuivi.value = response.props.flash.code_suivi;
        codeFound = true;
      } else if (response?.props?.code_suivi) {
        codeSuivi.value = response.props.code_suivi;
        codeFound = true;
      } else if (response?.props?.flash?.success?.code_suivi) {
        codeSuivi.value = response.props.flash.success.code_suivi;
        codeFound = true;
      }

      // Stocker l'ID de la demande pour l'envoi d'email
      demandeId.value = response.props?.demande_id || response.props?.flash?.demande_id;
      console.log("ID de la demande stocké pour l'envoi d'email:", demandeId.value);

      // Si un code a été trouvé, afficher un message de succès
      if (codeFound) {
        addToast({
          type: 'success',
          title: 'Demande soumise avec succès',
          message: `Votre code de suivi est : <span class="code-suivi">${codeSuivi.value}</span><br>Conservez-le précieusement pour suivre l'état de votre demande.`,
          duration: 8000
        });
      } else {
        // Sinon, montrer un message générique
        addToast({
          type: 'success',
          title: 'Demande soumise avec succès',
          message: `Vous pouvez consulter vos demandes dans la section <a href="${route('mes.demandes')}" class="toast-link">Mes Demandes</a>.`,
          duration: 8000
        });
      }

      showModal.value = false;
      form.reset();
      Object.keys(memberDocuments).forEach(key => delete memberDocuments[key]);
      step.value = 1;
    },
    onError: (errors) => {
      console.error("Erreurs:", errors);

      if (errors.message) {
        addToast({
          type: 'error',
          title: 'Erreur',
          message: errors.message
        });
      } else {
        // Message d'erreur détaillé
        let errorMessage = '';
        let hasErrors = false;

        for (const key in errors) {
          if (errors[key]) {
            errorMessage += `- ${errors[key]}<br>`;
            hasErrors = true;
          }
        }

        if (!hasErrors) {
          errorMessage = 'Une erreur inattendue est survenue. Veuillez réessayer.';
        }

        addToast({
          type: 'error',
          title: 'Erreur lors de la soumission',
          message: errorMessage
        });
      }
    },
  });
};

// Fonction pour gérer le téléchargement de fichier pour le demandeur principal
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  form.lettre_cv_path = file;

  // Notification pour le document téléchargé
  const documentType = form.type === 'Académique'
    ? 'Lettre de recommandation'
    : 'CV';

  addToast({
    type: 'success',
    title: 'Document téléchargé',
    message: `${documentType} ajouté avec succès`,
    duration: 3000
  });
};

// Fonction pour vérifier la validité du formulaire avant soumission
const validateForm = () => {
  // Vérification de base du formulaire
  if (!form.nom || !form.prenom || !form.email || !form.telephone ||
    !form.universite || !form.filiere || !form.niveau_etude ||
    !form.date_debut || !form.date_fin || !form.structure_id) {

    addToast({
      type: 'error',
      title: 'Formulaire incomplet',
      message: 'Veuillez remplir tous les champs obligatoires',
      duration: 5000
    });
    return false;
  }

  // Vérification des dates
  if (!isDateValid.value) {
    addToast({
      type: 'error',
      title: 'Dates invalides',
      message: 'La date de fin doit être après la date de début',
      duration: 5000
    });
    return false;
  }

  // Vérification des membres pour les demandes en groupe
  if (form.nature === 'Groupe' && (!form.membres || form.membres.length === 0)) {
    addToast({
      type: 'error',
      title: 'Membres manquants',
      message: 'Veuillez sélectionner au moins un membre pour le groupe',
      duration: 5000
    });
    return false;
  }

  // Vérification du document principal
  if (!form.lettre_cv_path) {
    addToast({
      type: 'warning',
      title: 'Document manquant',
      message: `Veuillez télécharger ${form.type === 'Académique' ? 'une lettre de recommandation' : 'votre CV'}`,
      duration: 5000
    });
    return false;
  }

  return true;
};

// Fonction pour vérifier si un membre est déjà dans la liste
const isMemberSelected = (userId) => {
  // Convertir userId en nombre si c'est une chaîne
  const id = typeof userId === 'string' ? parseInt(userId) : userId;
  
  // Vérifier si l'ID existe dans le tableau des membres, en tenant compte du type
  return form.membres.some(memberId => {
    const memId = typeof memberId === 'string' ? parseInt(memberId) : memberId;
    return memId === id;
  });
};
</script>
<template>
  <Head title="Tableau de bord" />
  <Stagiaire>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Tableau de bord</h2>
    </template>

    <!-- Recherche par code de suivi -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Rechercher une demande par code de suivi</h2>
            <div class="flex flex-wrap gap-4">
              <div class="flex-grow">
                <input 
                  type="text" 
                  v-model="searchCode"
                  placeholder="Entrez le code de suivi (ex: AB12CD34)" 
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <button 
                @click="searchByTrackingCode" 
                class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200 flex items-center gap-2"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Rechercher
              </button>
            </div>
            <p class="mt-3 text-sm text-gray-600">
              Vous pouvez retrouver rapidement une demande en saisissant son code de suivi unique.
              Cela vous permet de vérifier son statut même si vous n'êtes pas l'auteur de la demande.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulaire de demande de stage -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
          <h1 class="text-2xl font-bold mb-4">Bienvenue, {{ auth.user.nom }}</h1>
          <button @click="showModal = true" class="btn-primary">
            Soumettre une demande
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-container">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Soumettre une demande de stage</h2>
            <button @click="showModal = false" class="close-btn">&times;</button>
          </div>

          <div class="step-indicator">
            <div v-for="n in 4" :key="n" :class="{
              'step-active': n <= step,
              'step-inactive': n > step
            }">
              <div class="step-number">{{ n }}</div>
              <div class="step-label">
                <span v-if="n === 1">Informations</span>
                <span v-if="n === 2">Détails</span>
                <span v-if="n === 3">Documents</span>
                <span v-if="n === 4">Confirmation</span>
              </div>
            </div>
          </div>

          <!-- Étape 1 -->
          <div v-if="step === 1" class="step-content">
            <div class="form-section">
              <h3 class="section-title">Vos informations</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label class="required">Nom</label>
                  <input v-model="form.nom" type="text" class="form-input modified-field" :disabled="true"
                    title="Ce champ ne peut pas être modifié">
                  <span v-if="form.errors.nom" class="error-msg">{{ form.errors.nom }}</span>
                </div>
                <div class="form-group">
                  <label class="required">Prénom</label>
                  <input v-model="form.prenom" type="text" class="form-input modified-field" :disabled="true"
                    title="Ce champ ne peut pas être modifié">
                </div>
                <div class="form-group">
                  <label class="required">Email</label>
                  <input v-model="form.email" type="email" class="form-input">
                </div>
                <div class="form-group">
                  <label class="required">Téléphone</label>
                  <input v-model="form.telephone" type="tel" class="form-input">
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title">Type de demande</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Nature</label>
                  <select v-model="form.nature" class="form-input">
                    <option>Individuel</option>
                    <option>Groupe</option>
                  </select>
                </div>

                <!-- Type de demande (maintenant toujours visible, indépendant de la nature) -->
                <div class="form-group">
                  <label>Type de demande</label>
                  <select v-model="form.type" class="form-input">
                    <option>Académique</option>
                    <option>Professionnelle</option>
                  </select>
                </div>
              </div>

              <!-- Champ conditionnel pour les membres du groupe avec recherche -->
              <div v-if="form.nature === 'Groupe'" class="form-group mt-4">
                <label class="required">Membres du groupe</label>
                <div class="search-container">
                  <input v-model="searchQuery" type="text" placeholder="Rechercher des membres par nom ou prénom..."
                    class="form-input search-input mb-2" @focus="showMembersList = true" @input="handleSearchInput">
                  <span v-if="searchQuery.trim() !== ''" class="search-clear" @click="searchQuery = ''; handleSearchInput()">×</span>
                </div>
                
                <div class="members-select-container">
                  <div v-if="filteredUsers.length > 0">
                    <div v-for="user in filteredUsers" :key="user.id" class="member-option"
                      :class="{ 'selected': isMemberSelected(user.id), 'disabled': user.id === auth.user.id }">
                      <input type="checkbox" :value="user.id" v-model="form.membres" :disabled="user.id === auth.user.id"
                        :id="`member-${user.id}`">
                      <label :for="`member-${user.id}`" class="ml-2 member-label">
                        {{ user.nom }} {{ user.prenom }}
                        <span v-if="user.id === auth.user.id">(Vous)</span>
                      </label>
                    </div>
                  </div>
                  <p v-else-if="searchQuery && searchQuery.trim() !== ''" class="text-gray-500 mt-2 p-2">
                    Aucun membre trouvé pour "{{ searchQuery.trim() }}". Vérifiez l'orthographe ou essayez un autre nom.
                  </p>
                  <p v-else class="text-gray-500 mt-2 p-2">
                    Commencez à taper un nom pour rechercher des membres
                  </p>
                </div>
              </div>
            </div>

            <!-- Affichage des informations des membres sélectionnés -->
            <div v-if="form.nature === 'Groupe' && form.membres.length > 0" class="form-section">
              <h3 class="section-title">Informations des membres</h3>
              
              <div v-for="memberId in form.membres" :key="memberId" class="membre-info mb-4 p-3 border rounded">
                <div class="bg-blue-50 p-2 mb-2">
                  <div class="font-medium mb-2 text-blue-700">
                    {{ getUserInfo(memberId).nom || 'Nom non disponible' }} 
                    {{ getUserInfo(memberId).prenom || 'Prénom non disponible' }}
                  </div>
                  
                  <div v-if="!memberInfosLoaded" class="text-red-500 text-sm">
                    Chargement des informations en cours...
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label>Email</label>
                    <input type="text" :value="getUserInfo(memberId).email" class="form-input bg-gray-100 w-full" disabled readonly>
                  </div>
                  
                  <div>
                    <label>Téléphone</label>
                    <input type="text" :value="getUserInfo(memberId).telephone" class="form-input bg-gray-100 w-full" disabled readonly>
                  </div>
                </div>
              </div>

              <!-- Section de débogage - Étape 1 -->
              <!-- <div class="mt-4 p-2 bg-yellow-50 border border-yellow-200 rounded">
                <h4 class="font-medium text-yellow-800 mb-2">Informations de débogage:</h4>
                <div class="text-sm">
                  <p>Membres sélectionnés: {{ form.membres.join(', ') }}</p>
                  <p>Utilisateurs disponibles: {{ props.users.length }}</p>
                  <p>Info du premier utilisateur: {{ props.users.length > 0 ? JSON.stringify(props.users[0]) : 'Aucun' }}</p>
                  <p>Chargement des infos: {{ memberInfosLoaded ? 'Terminé' : 'En cours' }}</p>
                </div>
              </div> -->
            </div>
          </div>

          <!-- Étape 2 (organisée en 2 colonnes) -->
          <div v-if="step === 2" class="step-content">
            <!-- Première section (informations académiques) -->
            <div class="form-section">
              <h3 class="section-title">Informations </h3>
              <div class="form-grid">
                <div class="form-group">
                  <label class="required">Université</label>
                  <input v-model="form.universite" type="text" class="form-input">
                </div>
                <div class="form-group">
                  <label class="required">Specialité</label>
                  <input v-model="form.filiere" type="text" class="form-input">
                </div>
                <div class="form-group">
                  <label class="required">Niveau d'étude</label>
                  <select v-model="form.niveau_etude" class="form-input">
                    <option value="">-- Sélectionner --</option>
                    <option>Licence 1</option>
                    <option>Licence 2</option>
                    <option>Licence 3</option>
                    <option>Master 1</option>
                    <option>Master 2</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="required">Structure</label>
                  <select v-model="form.structure_id" class="form-input">
                    <option value="">-- Sélectionner --</option>
                    <option v-for="structure in structures" :key="structure.id" :value="structure.id">
                      {{ structure.libelle }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Deuxième section (détails du stage) -->
            <div class="form-section">
              <h3 class="section-title">Détails du stage</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label class="required">Date de début</label>
                  <input v-model="form.date_debut" type="date" class="form-input">
                </div>
                <div class="form-group">
                  <label class="required">Date de fin</label>
                  <input v-model="form.date_fin" type="date" class="form-input">
                  <span v-if="!isDateValid" class="error-msg">La date de fin doit être après la date de début</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Étape 3 (Téléchargement des pièces) -->
          <div v-if="step === 3" class="step-content">
            <div class="form-section">
              <h3 class="section-title">Pièces justificatives requises</h3>
              <div v-if="form.type === 'Académique'">
                <p class="mb-3">Pour une demande académique, veuillez fournir :</p>
                <div class="form-group">
                  <label class="required">Lettre de recommandation</label>
                  <input type="file" @change="handleFileUpload" class="form-input">
                </div>
              </div>

              <div v-else>
                <p class="mb-3">Pour une demande professionnelle, veuillez fournir :</p>
                <div class="form-group">
                  <label class="required">CV</label>
                  <input type="file" @change="handleFileUpload" class="form-input mb-2">
                  <span v-if="form.lettre_cv_path" class="file-name">
                    {{ form.lettre_cv_path.name }}
                  </span>
                </div>
                <div class="form-group">
                  <label class="required">Diplômes</label>
                  <input type="file" multiple class="form-input">
                </div>
              </div>
            </div>

            <!-- Documents pour les membres du groupe -->
            <div v-if="form.nature === 'Groupe' && form.membres.length > 0" class="form-section">
              <h3 class="section-title">Documents des membres du groupe</h3>
              <p class="mb-3">Chaque membre du groupe doit fournir les documents requis.</p>

              <div v-for="membreId in form.membres" :key="membreId" class="membre-documents mb-4 p-3 border rounded">
                <h4 class="font-medium mb-2">{{ getUserInfo(membreId).nom }} {{ getUserInfo(membreId).prenom }}</h4>

                <div v-if="form.type === 'Académique'">
                  <div class="form-group">
                    <label>Lettre de recommandation</label>
                    <div class="file-upload-container">
                      <input type="file" @change="e => handleMemberFile(membreId, 'lettre_cv_path', e)"
                        class="form-input">
                      <span v-if="memberDocuments[membreId]?.lettre_cv_path" class="file-name">
                        {{ memberDocuments[membreId].lettre_cv_path.name }}
                      </span>
                    </div>
                  </div>
                </div>

                <div v-else>
                  <div class="form-group">
                    <label>CV</label>
                    <div class="file-upload-container">
                      <input type="file" @change="e => handleMemberFile(membreId, 'cv', e)" class="form-input mb-2">
                      <span v-if="memberDocuments[membreId]?.cv" class="file-name">
                        {{ memberDocuments[membreId].cv.name }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Diplômes</label>
                    <div class="file-upload-container">
                      <input type="file" @change="e => handleMemberFile(membreId, 'diplomes', e)" class="form-input">
                      <span v-if="memberDocuments[membreId]?.diplomes" class="file-name">
                        {{ memberDocuments[membreId].diplomes.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Étape 4 (confirmation) -->
          <div v-if="step === 4" class="step-content">
            <!-- Informations principales de la demande -->
            <div class="confirmation-grid">
              <div class="confirmation-item">
                <h3>Détails du stage</h3>
                <p><strong>Université :</strong> {{ form.universite }}</p>
                <p><strong>Spécialité :</strong> {{ form.filiere }}</p>
                <p><strong>Niveau d'étude :</strong> {{ form.niveau_etude }}</p>
                <p><strong>Structure :</strong>
                  {{structures.find(s => s.id === form.structure_id)?.libelle}}
                </p>
                <p><strong>Période :</strong> {{ form.date_debut }} au {{ form.date_fin }}</p>
                <p><strong>Type de demande :</strong> {{ form.type }}</p>
                <p><strong>Nature :</strong> {{ form.nature }}</p>
              </div>
            </div>

            <!-- Informations des participants (demandeur principal et membres) -->
            <div class="form-section mt-4">
              <h3 class="section-title">Participants</h3>

              <!-- Demandeur principal -->
              <div class="confirmation-item mb-4">
                <h3 class="flex items-center">
                  {{ form.nom }} {{ form.prenom }}
                  <span class="tag-primary ml-2">Demandeur principal</span>
                </h3>
                <div class="participant-details">
                  <div class="participant-info">
                    <p><strong>Email :</strong> {{ form.email }}</p>
                    <p><strong>Téléphone :</strong> {{ form.telephone }}</p>
                  </div>
                  <div class="participant-documents">
                    <h4>Documents soumis</h4>
                    <p v-if="form.lettre_cv_path">
                      <strong>{{ form.type === 'Académique' ? 'Lettre de recommandation' : 'CV' }} :</strong>
                      {{ form.lettre_cv_path.name || 'Document téléchargé' }}
                    </p>
                    <p v-else class="text-yellow-600">Aucun document principal téléchargé</p>
                  </div>
                </div>
              </div>

              <!-- Membres du groupe -->
              <div v-if="form.nature === 'Groupe' && form.membres.length > 0">
                <div v-for="memberId in form.membres" :key="memberId" class="confirmation-item mb-4">
                  <h3>{{ getUserInfo(memberId).nom || 'Nom non disponible' }} {{ getUserInfo(memberId).prenom || 'Prénom non disponible' }}</h3>
                  <div class="participant-details">
                    <div class="participant-info">
                      <p><strong>Email :</strong> <span class="text-gray-700">{{ getUserInfo(memberId).email || 'Email non disponible' }}</span></p>
                      <p><strong>Téléphone :</strong> <span class="text-gray-700">{{ getUserInfo(memberId).telephone || 'Téléphone non disponible' }}</span></p>
                    </div>
                    <div class="participant-documents">
                      <h4>Documents soumis</h4>
                      <div v-if="memberDocuments[memberId]">
                        <!-- Documents selon le type de demande -->
                        <div v-if="form.type === 'Académique'">
                          <p v-if="memberDocuments[memberId]['lettre_cv_path']">
                            <strong>Lettre de recommandation :</strong>
                            {{ memberDocuments[memberId]['lettre_cv_path'].name }}
                          </p>
                          <p v-else class="text-yellow-600">Lettre de recommandation non fournie</p>
                        </div>
                        <div v-else>
                          <p v-if="memberDocuments[memberId]['cv']">
                            <strong>CV :</strong> {{ memberDocuments[memberId]['cv'].name }}
                          </p>
                          <p v-else class="text-yellow-600">CV non fourni</p>

                          <p v-if="memberDocuments[memberId]['diplomes']">
                            <strong>Diplômes :</strong> {{ memberDocuments[memberId]['diplomes'].name }}
                          </p>
                          <p v-else class="text-yellow-600">Diplômes non fournis</p>
                        </div>
                      </div>
                      <div v-else>
                        <p class="text-yellow-600">Aucun document soumis</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section de débogage - Étape 4 -->
              <!-- <div class="mt-4 p-2 bg-yellow-50 border border-yellow-200 rounded">
                <h4 class="font-medium text-yellow-800 mb-2">Informations de débogage:</h4>
                <div class="text-sm">
                  <p>Membres sélectionnés: {{ form.membres.join(', ') }}</p>
                  <p>Utilisateurs disponibles: {{ props.users.length }}</p>
                  <p>Exemple d'info pour le premier membre (si disponible): 
                    {{ form.membres.length > 0 ? JSON.stringify(getUserInfo(form.membres[0])) : 'Aucun membre' }}
                  </p>
                </div>
              </div> -->
            </div>
          </div>

          <div class="modal-footer">
            <button v-if="step > 1" @click="prevStep" class="btn-secondary">
              Précédent
            </button>
            <button v-if="step < 4" @click="nextStep" :disabled="!validateStep()" class="btn-primary">
              Suivant
            </button>
            <button v-if="step === 4" @click="submitRequest" :disabled="form.processing" class="btn-submit">
              <span v-if="form.processing">Envoi en cours...</span>
              <span v-else>Soumettre la demande</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Système de toast notifications -->
    <TransitionGroup tag="div" class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-md"
      enter-active-class="transition duration-300 ease-out" enter-from-class="transform translate-x-full opacity-0"
      enter-to-class="transform translate-x-0 opacity-100" leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform translate-x-0 opacity-100" leave-to-class="transform translate-x-full opacity-0">
      <div v-for="toast in toasts" :key="toast.id" :class="[
        'rounded-lg shadow-lg p-4 flex items-center border-l-4 min-w-80',
        toast.type === 'success' && 'bg-green-50 border-green-500 text-green-800',
        toast.type === 'error' && 'bg-red-50 border-red-500 text-red-800',
        toast.type === 'warning' && 'bg-yellow-50 border-yellow-500 text-yellow-800',
        toast.type === 'info' && 'bg-blue-50 border-blue-500 text-blue-800',
      ]">
        <div class="mr-3">
          <svg v-if="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-if="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <svg v-if="toast.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <svg v-if="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex-1">
          <div class="font-medium" v-html="toast.title"></div>
          <div class="text-sm opacity-90" v-html="toast.message"></div>
        </div>
        <button @click="removeToast(toast.id)" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </TransitionGroup>

    <!-- Ajouter le composant EmailSender après la soumission réussie de la demande -->
    <div v-if="codeSuivi && demandeId" class="fixed bottom-5 right-5 w-96 bg-white shadow-lg rounded-lg p-5 border border-gray-200 z-50">
      <h3 class="text-xl font-semibold mb-3">Confirmation de demande</h3>
      <p class="mb-2">Votre demande a été soumise avec succès.</p>
      <p class="mb-4">Code de suivi: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ codeSuivi }}</span></p>
      
      <EmailSender 
        :demande-id="demandeId" 
        :email="form.email"
        :has-group-members="form.nature === 'Groupe' && form.membres.length > 0"
        @email-sent="(data) => addToast({
          type: 'success',
          title: 'Email envoyé',
          message: 'Un email de confirmation a été envoyé avec succès.',
          duration: 5000
        })"
        @email-error="(error) => addToast({
          type: 'error',
          title: 'Erreur d\'envoi',
          message: `L'envoi de l'email a échoué: ${error}`,
          duration: 8000
        })"
      />
    </div>
  </Stagiaire>
</template>
<style scoped>
/* Styles de base */
.btn-primary {
  background-color: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 600;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #4b5563;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 600;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: #d1d5db;
}

.btn-submit {
  background-color: #10b981;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 600;
  transition: background-color 0.2s;
}

.btn-submit:hover {
  background-color: #059669;
}

.flash-message {
  padding: 0;
  border-radius: 0.5rem;
  margin-top: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.flash-message-content {
  display: flex;
  align-items: flex-start;
  padding: 1rem;
}

.flash-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  margin-right: 0.75rem;
  margin-top: 0.25rem;
}

.flash-message-text {
  flex-grow: 1;
}

.flash-close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  line-height: 1;
  color: currentColor;
  opacity: 0.7;
  cursor: pointer;
  padding: 0 0.5rem;
}

.flash-close-btn:hover {
  opacity: 1;
}

.flash-message.success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.flash-message.error {
  background-color: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.flash-message.warning {
  background-color: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.code-suivi {
  font-weight: bold;
  padding: 0.25rem 0.5rem;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 0.25rem;
  font-family: monospace;
  font-size: 1.1em;
}

.flash-link {
  color: inherit;
  text-decoration: underline;
  font-weight: 600;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 50;
}

.modal-container {
  width: 95%;
  max-width: 1200px;
  margin: 0 1rem;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  font-size: 1.05rem;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.close-btn {
  font-size: 1.5rem;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
}

.close-btn:hover {
  color: #4b5563;
}

/* Step indicator */
.step-indicator {
  display: flex;
  justify-content: space-between;
  padding: 1.5rem 1.5rem 0;
  position: relative;
}

.step-indicator::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 50px;
  right: 50px;
  height: 2px;
  background-color: #e5e7eb;
  z-index: 1;
}

.step-active,
.step-inactive {
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 2;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.step-active .step-number {
  background-color: #3b82f6;
  color: white;
}

.step-inactive .step-number {
  background-color: #e5e7eb;
  color: #9ca3af;
}

.step-label {
  font-size: 0.875rem;
  color: #9ca3af;
  font-weight: 500;
}

.step-active .step-label {
  color: #111827;
}

/* Form styles */
.step-content {
  padding: 1.5rem;
}

.form-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-title {
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.6rem;
  font-weight: 500;
  color: #374151;
}

.form-group label.required::after {
  content: ' *';
  color: #ef4444;
}

.form-input {
  width: 100%;
  padding: 0.6rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1.05rem;
  background-color: white;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.form-input::placeholder {
  color: #9ca3af;
}

.error-msg {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

/* Confirmation step */
.confirmation-grid {
  display: grid;
  gap: 1.5rem;
}

.confirmation-item {
  background-color: #f9fafb;
  padding: 1rem;
  border-radius: 0.375rem;
}

.confirmation-item h3 {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.confirmation-item p {
  margin-bottom: 0.5rem;
  color: #4b5563;
}

/* Footer */
.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
}

/* Responsive */
@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .step-indicator::before {
    left: 30px;
    right: 30px;
  }
}

/* Nouveaux styles pour la recherche et la sélection des membres */
.search-container {
  position: relative;
  margin-bottom: 0.5rem;
}

.search-input {
  padding-right: 2.5rem;
  font-size: 1.05rem;
  height: auto;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
}

.search-clear {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.5rem;
  color: #9ca3af;
  cursor: pointer;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.search-clear:hover {
  color: #6b7280;
  background-color: #f3f4f6;
}

/* Styles pour la liste des membres */
.members-select-container {
  max-height: 250px;
  overflow-y: auto;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0.75rem;
  margin-bottom: 1rem;
  background-color: white !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.member-option {
  padding: 0.75rem;
  display: flex;
  align-items: center;
  border-radius: 0.25rem;
  transition: background-color 0.2s;
  margin-bottom: 4px;
  background-color: #fafafa;
  font-size: 1.05rem;
}

.member-option:hover {
  background-color: #f0f0f0;
}

.member-option.selected {
  background-color: #e0f2fe;
  border-left: 3px solid #3b82f6;
}

/* Améliorer l'apparence des options de membres */
.member-label {
  display: inline-block;
  margin-bottom: 0;
  cursor: pointer;
  font-weight: 500;
}

.member-option input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

/* Renforcer la visibilité des champs des membres */
.membre-info {
  background-color: #f9fafb !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb !important;
  padding: 1rem !important;
}

.membre-info input {
  background-color: #f3f4f6 !important;
  color: #000000 !important;
  border-color: #d1d5db !important;
}

/* Styles pour les champs désactivés mais lisibles */
.form-input:disabled,
.form-input:readonly,
.form-input[readonly],
.bg-gray-100 {
  background-color: #f3f4f6 !important;
  cursor: not-allowed;
  opacity: 1 !important;
  color: #000000 !important;
  border-color: #d1d5db !important;
  font-weight: 500;
}

/* Renforcer la visibilité des inputs désactivés */
input:disabled, 
select:disabled, 
textarea:disabled {
  background-color: #f3f4f6 !important;
  color: #000000 !important;
  border-color: #d1d5db !important;
  opacity: 1 !important;
}

.text-gray-700 {
  color: #374151;
}

/* Styles pour les documents des membres */
.membre-documents {
  background-color: #f9fafb;
  border-color: #e5e7eb;
}

/* Ajustements pour les colonnes */
.col-span-2 {
  grid-column: span 2 / span 2;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.mb-3 {
  margin-bottom: 0.75rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

.p-3 {
  padding: 0.75rem;
}

.border {
  border-width: 1px;
}

.rounded {
  border-radius: 0.375rem;
}

.text-yellow-600 {
  color: #d97706;
}

/* Mettre à jour les styles responsifs */
@media (max-width: 768px) {
  .modal-container {
    width: 95%;
    margin: 0 0.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}

/* Styles pour l'étape 4 - Confirmation */
.participant-details {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: 0.5rem;
}

.participant-info,
.participant-documents {
  background-color: #f9fafb;
  padding: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
}

.participant-documents h4 {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #4b5563;
}

.tag-primary {
  background-color: #3b82f6;
  color: white;
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 9999px;
  font-weight: 500;
}

.status-pending {
  color: #f59e0b;
  font-size: 0.875rem;
  font-style: italic;
}

.doc-list {
  list-style: disc;
  padding-left: 1.25rem;
}

.mt-4 {
  margin-top: 1rem;
}

.flex {
  display: flex;
}

.items-center {
  align-items: center;
}

.ml-2 {
  margin-left: 0.5rem;
}

/* Ajustements responsifs pour l'affichage de confirmation */
@media (max-width: 768px) {
  .participant-details {
    grid-template-columns: 1fr;
  }
}

/* Styles pour l'upload de fichiers */
.file-upload-container {
  position: relative;
  width: 100%;
}

.file-name {
  display: block;
  margin-top: 0.5rem;
  padding: 0.375rem 0.75rem;
  background-color: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 0.375rem;
  color: #0369a1;
  font-size: 0.875rem;
}

/* Pour éviter que les inputs désactivés soient trop pâles */
.form-input:disabled {
  background-color: #f3f4f6;
  cursor: not-allowed;
  opacity: 0.8;
  color: #4b5563;
}

.membre-info {
  background-color: #f9fafb;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Styles pour le système de toast */
.toast-link {
  color: inherit;
  text-decoration: underline;
  font-weight: 600;
}

/* Ajustements pour s'assurer que le toast passe au-dessus de la modal */
.fixed {
  z-index: 100;
}
</style>
