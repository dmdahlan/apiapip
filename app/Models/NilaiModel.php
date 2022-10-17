<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table            = 'nilai_pelajaran';
    protected $primaryKey       = 'id_nilai';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['kelas', 'pelajaran', 'nilai', 'foto_nilai', 'created_id', 'updated_id', 'deleted_id'];

    function getNilai($id = null, $user = null)
    {
        $builder = $this->db->table('nilai_pelajaran')
            ->select('id_nilai,email,username,kelas,pelajaran,nilai,foto_nilai')
            ->join('users', 'id=created_id')
            ->where('nilai_pelajaran.deleted_at', null);
        if (isset($id)) {
            $query = $builder->where('id_nilai', $id);
        }
        if (isset($user)) {
            $query = $builder->where('nilai_pelajaran.created_id', $user);
        }
        $query = $builder->get();
        return $query;
    }
}
