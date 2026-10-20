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

# 5. Pour la requête **b**, quel code obtenez-vous ? Pourquoi pas 200 ?
on obtient le code : 201 et pas 200 car le code 201 signifie created alors que 200 n'evoye aucune reponse

# 6. Pour la requête **d**, qui bloque la suppression : PHP ou PostgreSQL ? Montrez la ligne de code qui transforme ce blocage en réponse 409.
return $this->erreur('Ce plat figure déjà dans des commandes, il ne peut pas être supprimé.', 409);

c'est postgresql qui bloque car on a une foreign key dans la commande à plat (id_plat)

# 7. Créez une commande avec `curl` (table 6, deux *Romazava* et un *Café*). Écrivez la commande `curl` complète.

```bash
curl -X POST http://localhost:8080/api/commandes \
     -H "Content-Type: application/json" \
     -d '{"date_commande":"2026-11-01","numero_table":1}'
```
