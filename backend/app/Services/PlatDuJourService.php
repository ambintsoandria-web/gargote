<?php

namespace App\Services;

use App\Models\PlatDuJourModel;
use App\Models\PlatModel;

class PlatDuJourService
{
    private PlatDuJourModel $platsDuJour;
    private PlatModel $plats;

    public function __construct()
    {
        $this->platsDuJour = model(PlatDuJourModel::class);
        $this->plats = model(PlatModel::class);
    }

    public function getAujourdhui(): ?array
    {
        return $this->platsDuJour->getPlatDuJour(date('Y-m-d'));
    }

    public function enregistrerAujourdhui(int $idPlat): void
    {
        $plat = $this->plats->find($idPlat);
        if (!$plat) {
            throw new \InvalidArgumentException("Plat introuvable.", 422);
        }
        if (isset($plat['disponible']) && !$plat['disponible']) {
            $nom = $plat['nom'] ?? 'Le plat';
            throw new \RuntimeException(
                "Le plat « $nom » est épuisé : il ne peut pas être plat du jour.",
                409
            );
        }
        $this->platsDuJour->updatePlatDuJour($idPlat);
    }

    public function getHistorique7Jours(int $nombre): array
    {
        return $this->platsDuJour
            ->select('plat_du_jour.date_jour, plat.*, categorie.libelle AS categorie')
            ->join('plat', 'plat.id = plat_du_jour.id_plat')
            ->join('categorie', 'plat.id_categorie = categorie.id')
            ->orderBy('plat_du_jour.date_jour', 'DESC')
            ->limit($nombre)
            ->findAll();
    }
}