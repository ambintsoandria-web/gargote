<script setup>
import { computed } from 'vue'
import { ariary, parCategorie } from '../utils/format'

const props = defineProps({
  plats: { type: Array, required: true },
})

const emit = defineEmits(['modifier', 'supprimer', 'basculer'])

const groupes = computed(() => parCategorie(props.plats))
</script>

<template>
  <div class="ardoise">
    <p v-if="plats.length === 0" class="vide">
      Aucun plat pour l’instant. Ajoutez-en un avec le formulaire.
    </p>

    <section v-for="(liste, categorie) in groupes" :key="categorie" class="ardoise-groupe">
      <h3 class="ardoise-titre">{{ categorie }}</h3>
      <ul class="ardoise-liste">
        <li v-for="plat in liste" :key="plat.id" class="ardoise-ligne" :class="{ epuise: !plat.disponible }">
          <span class="plat-nom">{{ plat.nom }}</span>
          <span class="plat-description">{{ plat.description }}</span>
          <span class="plat-pointilles" aria-hidden="true"></span>
          <span class="plat-prix">{{ ariary(plat.prix) }}</span>
          <span v-if="!plat.disponible" class="badge-epuise">Épuisé</span>
          <span class="plat-actions">
            <button class="lien" @click="emit('modifier', plat)">Modifier</button>
            <button class="lien lien-danger" @click="emit('supprimer', plat)">Supprimer</button>
            <button class="lien" @click="emit('basculer', plat)">
              {{ plat.disponible ? 'Épuisé' : 'Remettre' }}
            </button>
          </span>
        </li>
      </ul>
    </section>
  </div>
</template>