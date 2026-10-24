## 0.2 Lire le schéma relationnel

- categorie(<u>id</u>,libelle);

- plat(<u>id</u>,nom,prix,#id_categorie);

- commande(<u>id</u>, date_commande,numero_table);

- commande_ligne(<u>id</u>,#ic_commande,#id_plat,quantite,prix_unitaire)


## 0.3 Interroger l’API à la main
```bash
# a) liste des soupes
curl http://localhost:8080/api/plats?categorie=2
```

- code http : 200
- reponse : Content : [
                        {
                            "id": 7,
                            "nom": "Mi sao",
                            "prix": 6500,
                            "id_categorie": 2,
                            "categorie": "Soupe"
                        },
                        {
                            "id": 6,
                            "nom": "Soupe chinoise",
                            "...
                        }

#### b) création d'un plat valide
curl -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Mofo gasy","prix":300,"id_categorie":1}'

- code : 201
- reponse : {
  "id": 14,
  "nom": "Mofo gasy",
  "prix": 300,
  "id_categorie": 1
}


#### c) création d'un plat invalide
curl -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"","prix":"cher"}'
- code : 422
- response : {
  "message": "Données invalides.",
  "errors": {
    "nom": "Le nom du plat est obligatoire.",
    "prix": "Le prix doit être un nombre entier (en Ariary).",
    "id_categorie": "Choisissez une catégorie."
  }
}

#### d) suppression d'un plat présent dans une commande
curl -X DELETE http://localhost:8080/api/plats/2

- code : 409 conflict
- response : {
  "message": "Ce plat figure déjà dans des commandes, il ne peut pas être supprimé.",
  "errors": {}
}

#### e) une commande qui n'existe pas
curl http://localhost:8080/api/commandes/999
- code : 404 
- response : {
  "message": "Commande 999 introuvable.",
  "errors": {}
}

 5. Pour la requête **b**, quel code obtenez-vous ? Pourquoi pas 200 ?
on obtient le code : 201 et pas 200 car le code 201 signifie created alors que 200 n'evoye aucune reponse

 6. Pour la requête **d**, qui bloque la suppression : PHP ou PostgreSQL ? Montrez la ligne de code qui transforme ce blocage en réponse 409.
return $this->erreur('Ce plat figure déjà dans des commandes, il ne peut pas être supprimé.', 409);

c'est postgresql qui bloque car on a une foreign key dans la commande à plat (id_plat)

 7. Créez une commande avec `curl` (table 6, deux *Romazava* et un *Café*). Écrivez la commande `curl` complète.

```bash
curl -X POST http://localhost:8080/api/commandes \
     -H "Content-Type: application/json" \
     -d '{"date_commande":"2026-11-01","numero_table":1}'
```

## 0.4 Suivre une requête de bout en bout

| # | Étape | Fichier | Fonction / ligne |
|---|-------|---------|------------------|
| 1 | Le formulaire est soumis | `components/PlatForm.vue` | `enregistrer()`|
| 2 | La requête HTTP est construite | | `requete()` |
| 3 | Vite transmet `/api/...` au port 8080 |`api.js` | `export const api`|
| 4 | CodeIgniter choisit le contrôleur |`app/Config/Routes.php`|`$routes->get('plats', 'Plats::index');` |
| 5 | Le contrôleur lit le JSON | `Controllers/Api/Plats.php`|`donneesJons()` |
| 6 | Les règles de validation sont vérifiées | `Models/PlatModel.php`| `protected $validationRules = `|
| 7 | La ligne est insérée |`Models/Plats.php` |`create()` |
| 8 | Le formulaire prévient l’écran parent |`components/Panier.vue` | `emit(…)` |
| 9 | L’écran recharge la liste | `views/CarteView.vue` | `async function charger()` |

8. Où est le code métier de la création d’une commande ? Pourquoi n’est-il pas dans le contrôleur `Commandes.php` ?
- Le code métier del a création d'une commande est dans : CommandeService car le controlleur lui ne sert qu'à envoyer des données one ne met jamai des logiques métiers dans le controlleur;


## 0.5 Repérer les notions de Vue

| Notion | À quoi ça sert | Votre exemple |
|---|---|---|
| `ref()` | une variable dont le changement met l’écran à jour |`const plats = ref([])` |
| `computed()` | une valeur calculée à partir d’autres, recalculée automatiquement |`const nombre = computed(() => nombre * 3)`|
| `v-for` | répéter un élément pour chaque élément d’une liste |`v-for="onglet in onglets"` |
| `v-if` / `v-else` | afficher un élément seulement si une condition est vraie |`<p v-if="message" class="alerte alerte-erreur">{{ message }}</p>` |
| `v-model` | lier un champ de formulaire à une variable, dans les deux sens |`<input v-model.trim="form.nom" type="text" placeholder="Ex. : Mofo gasy" />` permet de lier une variable à un formulaire comme input |
| `defineProps` | recevoir des données du composant parent |`defineProps({` permet à une composante de d'avoir des parametres pour que la composante mere peuvent leur transmettre des données via les parametres |
| `defineEmits` / `emit` | envoyer un événement au composant parent |`const emit = defineEmits(['plus', 'moins'])` permet de renvoyer vers la composante mere des actions   |
| `onMounted` | exécuter du code quand le composant apparaît |`onMounted(charger)` permet d'executer la fonction charger des que la composante apparait à l'ecran |

9. `Panier.vue` ne modifie jamais la quantité lui-même : il émet `plus` et `moins`. Pourquoi ce choix ? Qui modifie réellement la quantité ?

- Panier.vue ne modifie jamais la quantitée car il y a déjà une composante mere qui le fait généralement on le fait dans la View car la view appelle la composante

10. Dans `PlatForm.vue`, à quoi sert le `watch` ? Que se passerait-il sans lui quand on clique sur **Modifier** d’un autre plat ?

- watch(() =>) permet de : surveiller une variable si elle change alors toute la page se met à jour sans se recharger   

## 0.6 Premières modifications

Deux petites modifications pour vérifier que tout est en place.

**a) Le nom de la gargote.** Remplacez « Chez Bao, Analakely » par le nom de votre choix. Un seul fichier à modifier.
<span class="enseigne-lieu">Chez Rakoto, Analamahitsy</span>

**b) Une nouvelle catégorie, sans toucher au code.** Ajoutez la catégorie *Goûter* directement en SQL, puis rechargez l’interface.

11. La catégorie apparaît dans la liste déroulante du formulaire sans que vous ayez écrit de code. Expliquez pourquoi, en citant la route et le composant concernés.
Dans PlatForm.vue on a :
<option v-for="c in categories" :key="c.id" :value="c.id">{{ c.libelle }}</option>
et dans 

<PlatForm :plat="platSelectionne" :categories="categories" @enregistre="surEnregistrement"
@annule="platSelectionne = null" />


