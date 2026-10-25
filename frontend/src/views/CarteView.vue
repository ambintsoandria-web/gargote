<script setup>
import { onMounted, ref } from "vue";
import { api } from "../api";
import PlatList from "../components/PlatList.vue";
import PlatForm from "../components/PlatForm.vue";
import PlatDuJour from "../components/PlatDuJour.vue";

const plats = ref([]);
const categories = ref([]);
const platSelectionne = ref(null);
const message = ref(null);
const platDuJour = ref(null);

async function charger() {
  try {
    [plats.value, categories.value, platDuJour.value] = await Promise.all([
      api.plats.lister(),
      api.categories.lister(),
      api.platDuJour.lister(),
    ]);
  } catch (e) {
    message.value = { type: "erreur", texte: "Chargement impossible : " + e.message };
  }
}

async function basculerDispo(plat) {
  try {
    await api.plats.modifier(plat.id, { ...plat, disponible: !plat.disponible });
    await charger();
  } catch (e) {
    message.value = { type: "erreur", texte: e.message };
  }
}

async function surEnregistrement(plat) {
  message.value = { type: "succes", texte: `« ${plat.nom} » est enregistré.` };
  platSelectionne.value = null;
  await charger();
}

async function supprimer(plat) {
  if (!confirm(`Supprimer « ${plat.nom} » ?`)) return;
  try {
    await api.plats.supprimer(plat.id);
    await charger();
  } catch (e) {
    message.value = { type: "erreur", texte: e.message };
  }
}

function surChangePlatDuJour(plat) {
  platDuJour.value = plat;
  message.value = { type: "succes", texte: `Plat du jour : « ${plat?.nom ?? ""} »` };
}

onMounted(charger);
</script>

<template>
  <p v-if="message" class="alerte" :class="`alerte-${message.type}`">
    {{ message.texte }}
  </p>

  <PlatDuJour
    :plat="platDuJour"
    :modifiable="true"
    :plats="plats"
    @change="surChangePlatDuJour"
  />

  <div class="grille">
    <PlatList
      :plats="plats"
      @modifier="platSelectionne = $event"
      @supprimer="supprimer"
      @basculer="basculerDispo"
    />
    <PlatForm
      :plat="platSelectionne"
      :categories="categories"
      @enregistre="surEnregistrement"
      @annule="platSelectionne = null"
    />
  </div>
</template>
