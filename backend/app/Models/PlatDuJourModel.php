<?php

namespace App\Models;

use CodeIgniter\Model;

class PlatDuJourModel extends Model
{
    protected $table = 'plat_du_jour';
    protected $primaryKey = 'date_jour';
    protected $returnType = 'array';
    protected $allowedFields = ['date_jour', 'id_plat'];
    protected $useTimestamps = false;

    public function getPlatDuJour($date)
    {
        return $this->select('plat_du_jour.date_jour, plat.*, categorie.libelle AS categorie')
            ->join('plat', 'plat.id = plat_du_jour.id_plat')
            ->join('categorie', 'plat.id_categorie = categorie.id')
            ->where('plat_du_jour.date_jour', $date)
            ->first();
    }

    public function updatePlatDuJour(int $id_plat): void
    {
        $sql = "INSERT INTO plat_du_jour (date_jour, id_plat) VALUES (CURRENT_DATE, ?)
                ON CONFLICT (date_jour) DO UPDATE SET id_plat = EXCLUDED.id_plat";
        $this->db->query($sql, [$id_plat]);
    }
}