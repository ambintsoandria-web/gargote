<script setup>
import { computed, ref } from "vue";
import { api, ApiError } from "../api";

const props = defineProps({
  plat: { type: Object, default: null },
  modifiable: { type: Boolean, default: false },
  plats: { type: Array, default: () => [] },
});

const emit = defineEmits(["choisir", "change"]);

const selection = ref("");
const erreur = ref("");
const enCours = ref(false);
const historique = ref([]);
const ouvert = ref(false);
const nombre = ref(7);

const dispos = computed(() => props.plats.filter((p) => p.disponible));

async function choisir() {
  if (!selection.value) return;
  enCours.value = true;
  erreur.value = "";
  try {
    const nouveau = await api.platDuJour.definir(selection.value);
    emit("change", nouveau);
    selection.value = "";
  } catch (e) {
    erreur.value = e instanceof ApiError ? e.message : "Serveur injoignable.";
  } finally {
    enCours.value = false;
  }
}

function clicPlat() {
  if (!props.modifiable && props.plat) emit("choisir", props.plat);
}

async function chargerHistorique() {
  const n = Number(nombre.value);
  if (!n || n < 1) {
    erreur.value = "Nombre de jours invalide.";
    return;
  }
  erreur.value = "";
  try {
    historique.value = await api.platDuJour.historique(n);
    ouvert.value = true;
  } catch (e) {
    erreur.value = e.message;
  }
}
</script>

<template>
  <section class="plat-du-jour">
    <h2>Plat du jour</h2>

    <p v-if="erreur" class="alerte alerte-erreur">{{ erreur }}</p>

    <div v-if="!plat" class="vide">
      <p>Aucun plat du jour défini.</p>
      <p v-if="modifiable">Choisissez-en un ci-dessous.</p>
    </div>

    <div v-else class="carte" :class="{ cliquable: !modifiable }" @click="clicPlat">
      <strong>{{ plat.nom }}</strong>
      <p v-if="plat.description">{{ plat.description }}</p>
      <p>{{ plat.prix }} Ar</p>
      <p v-if="plat.categorie">Catégorie : {{ plat.categorie }}</p>
    </div>

    <div v-if="modifiable" class="choix">
      <select v-model="selection">
        <option value="" disabled>Choisir un plat…</option>
        <option v-for="p in dispos" :key="p.id" :value="p.id">{{ p.nom }}</option>
      </select>
      <button :disabled="!selection || enCours" @click="choisir">Choisir</button>
    </div>

    <div class="histo-bar">
      <input v-model.number="nombre" type="number" min="1" max="30" />
      <button @click="chargerHistorique">Voir les derniers jours</button>
    </div>

    <ul v-if="ouvert && historique.length">
      <li v-for="h in historique" :key="h.date_jour">
        {{ h.date_jour }} — {{ h.nom ?? "aucun" }}
      </li>
    </ul>
    <p v-else-if="ouvert" class="vide">Aucun historique.</p>
  </section>
</template>
