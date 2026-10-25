<?php

namespace App\Models;

use CodeIgniter\Model;

class PlatModel extends Model
{
    protected $table = 'plat';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected array $casts = [
        'id' => 'int',
        'prix' => 'int',
        'id_categorie' => 'int',
        'disponible' => 'boolean'
    ];

    protected $allowedFields = ['nom', 'prix', 'id_categorie', 'description', 'disponible'];

    protected $beforeInsert = ['normaliserDisponibilite'];
    protected $beforeUpdate = ['normaliserDisponibilite'];

    protected function normaliserDisponibilite(array $event): array
    {
        // PostgreSQL attend un booléen, même si le client envoie 0 ou 1.
        if (
            isset($event['data']['disponible'])
            && in_array($event['data']['disponible'], [0, 1, '0', '1'], true)
        ) {
            $event['data']['disponible'] = (bool) $event['data']['disponible'];
        }

        return $event;
    }

    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'nom' => 'required|string|is_unique[plat.nom,id,{id}]|min_length[2]|max_length[100]',
        'prix' => 'required|integer|is_natural_no_zero|less_than_equal_to[100000]',
        'id_categorie' => 'required|integer',
        'description' => 'permit_empty',
        'disponible' => 'permit_empty|est_booleen'
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom du plat est obligatoire.',
            'is_unique' => 'Ce nom de plat existe déjà dans la base de données.',
            'min_length' => 'Le nom du plat doit contenir au moins 2 caractères.',
            'max_length' => 'Le nom ne doit pas dépasser 100 caractères.',
        ],
        'prix' => [
            'required' => 'Le prix du plat est obligatoire.',
            'integer' => 'Le prix doit être un nombre entier (en Ariary).',
            'is_natural_no_zero' => 'Le prix doit être un nombre supérieur à zéro.',
            'less_than_equal_to' => 'Le prix ne peut pas dépasser 100 000 Ariary.',
        ],
        'id_categorie' => [
            'required' => 'Veuillez associer ce plat à une catégorie.',
            'integer' => 'La catégorie choisie n’est pas valide.',
        ],
    ];

    public function listerAvecCategorie(?int $idCategorie = null, ?bool $dispo = null): array
    {
        $this->select('plat.*, categorie.libelle AS categorie')
            ->join('categorie', 'categorie.id = plat.id_categorie')
            ->orderBy('categorie.libelle')
            ->orderBy('plat.nom');

        if ($idCategorie !== null) {
            $this->where('plat.id_categorie', $idCategorie);
        }
        if ($dispo !== null) {
            $this->where('plat.disponible', $dispo);
        }

        return $this->findAll();
    }
}
