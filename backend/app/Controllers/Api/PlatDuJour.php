<?php

namespace App\Controllers\Api;

use App\Services\PlatDuJourService;

class PlatDuJour extends ApiController
{
    private PlatDuJourService $platDuJourService;

    public function __construct()
    {
        $this->platDuJourService = new PlatDuJourService();
    }

    public function index()
    {
        return $this->respond($this->platDuJourService->getAujourdhui(), 200);
    }

    public function update($id = null)
    {
        if ($id === null || !is_numeric($id)) {
            return $this->erreur("Le champ id_plat est obligatoire.", 422);
        }

        try {
            $this->platDuJourService->enregistrerAujourdhui((int) $id);
            return $this->respond($this->platDuJourService->getAujourdhui(), 200);
        } catch (\InvalidArgumentException $e) {
            return $this->erreur($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->erreur($e->getMessage(), 409);
        }
    }

    public function historique()
    {
        $nombre = (int) ($this->request->getGet('jours') ?? 7);
        return $this->respond(
            $this->platDuJourService->getHistorique7Jours($nombre),
            200
        );
    }
}