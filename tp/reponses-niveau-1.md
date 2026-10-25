-- 4. Pourquoi R2 renvoie-t-elle 409 et pas 422 ?
-- La requête est syntaxiquement correcte et l'ID du plat existe, 
-- donc ce n'est pas une erreur 422 (Unprocessable Entity). Par contre, 
-- l'état actuel du plat (« épuisé ») entre en conflit direct avec la règle métier qui
--  exige que le plat du jour soit disponible. Le code 409 (Conflict) est précisément fait
--   pour indiquer un conflit avec l'état courant de la ressource.

-- 5. Que doit-il se passer si Bao marque comme épuisé le plat qui est déjà plat du jour ?
-- • Comportement proposé : Si un plat déjà défini comme plat du jour devient indisponible,
--  la ligne du jour dans la table plat_du_jour doit être automatiquement supprimée (ou le plat du jour repasse à null).
-- • Justification : Cela évite que les clients ne voient un plat épuisé mis en avant sur
--  l'application et tentent de passer des commandes que la cuisine ne pourra pas honorer,
--   ce qui évite les déceptions et les annulations logicielles inutiles.


### 1.7 Jeu de tests

curl -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Café","prix":1000,"id_categorie":3}'

HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Sat, 24 Oct 2026 07:02:47 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792825367.068540
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792825367.068540

{
  "message": "Données invalides.",
  "errors": {
    "nom": "Ce nom de plat existe déjà dans la base de données."
  }
}
POST http://localhost:8080/api/plats HTTP/1.1
Content-Type: application/json

{"nom":"Café","prix":1000,"id_categorie":3}
### `PUT /api/plats/4` `{"nom":"Romazava","prix":7500}` (on garde son propre nom) | 200 |

curl -X PUT http://localhost:8080/api/plats/4 \
     -H "Content-Type: application/json" \
     -d '{"nom":"Romazava","prix":7500}'



HTTP/1.1 200 OK
Host: localhost:8080
Date: Sat, 24 Oct 2026 07:03:57 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792825437.295430
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792825437.295430

{
  "id": 4,
  "nom": "Romazava",
  "prix": 7500,
  "id_categorie": 1,
  "description": null,
  "disponible": true
}



curl -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Thé","prix":0,"id_categorie":3}'


HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Sat, 24 Oct 2026 07:05:55 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792825555.650986
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792825555.650986

{
  "message": "Données invalides.",
  "errors": {
    "prix": "Le prix doit être un nombre supérieur à zéro."
  }
}

curl -X PUT http://localhost:8080/api/plats/7 \
     -H "Content-Type: application/json" \
     -d '{"disponible":"oui"}'


HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Sat, 24 Oct 2026 11:16:16 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792840576.883393
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792840576.883393

{
  "message": "Données invalides.",
  "errors": {
    "disponible": "Le champ doit être un booléen (vrai ou faux)."
  }
}

curl -X PUT http://localhost:8080/api/plats/7 \
     -H "Content-Type: application/json" \
     -d '{"disponible":"false"}'


HTTP/1.1 200 OK
Host: localhost:8080
Date: Sat, 24 Oct 2026 11:20:35 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792840835.810417
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792840835.810417

{
  "id": 7,
  "nom": "Mi sao",
  "prix": 6500,
  "id_categorie": 2,
  "description": null,
  "disponible": false
}


curl -X PUT http://localhost:8080/api/plats/7 \
     -H "Content-Type: application/json" \
     -d '{"id_plat":7}'


HTTP/1.1 409 Conflict
Host: localhost:8080
Date: Sun, 25 Oct 2026 04:12:03 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792901523.073375
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792901523.073375

{
  "message": "Le Mi sao est épuisé : il ne peut pas être plat du jour.",
  "errors": {}
}





HTTP/1.1 200 OK
Host: localhost:8080
Date: Sun, 25 Oct 2026 04:14:58 GMT
Connection: close
X-Powered-By: PHP/8.5.11
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1792901698.779755
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1792901698.779755

[
  {
    "id": 6,
    "nom": "Soupe chinoise",
    "prix": 6000,
    "id_categorie": 2,
    "description": null,
    "disponible": true,
    "categorie": "Soupe"
  }
]

