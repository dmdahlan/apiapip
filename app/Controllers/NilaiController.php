<?php

namespace App\Controllers;

use App\Models\NilaiModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\RESTful\ResourceController;

class NilaiController extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';
    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $id = null;
        $user = $this->user();
        $data = [
            'messages'        => 'success',
            'data'            => $this->nilaiModel->getNilai($id, $user)->getResult()
        ];
        return $this->respond($data, 200);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $user = $this->user();
        $data = $this->nilaiModel->getNilai($id, $user)->getRow();
        if ($data) {
            $response = [
                'messages'          => 'success',
                'data'              => $data
            ];
            return $this->respond($response, 200);
        } else {
            return $this->failNotFound("Data tidak ditemukan untuk id $id");
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->validasi()) return $this->fail($this->validator->getErrors());
        $foto = $this->request->getFile('foto');
        $namaFoto = $foto->getRandomName();
        $foto->move('gambar', $namaFoto);
        $data = [
            'kelas'           => $this->request->getPost('kelas'),
            'pelajaran'       => $this->request->getPost('pelajaran'),
            'nilai'           => $this->request->getPost('nilai'),
            'foto_nilai'      => $namaFoto,
            'created_id'      => $this->user()
        ];
        $this->nilaiModel->insert($data);
        $response = [
            'messages'  => [
                'success'   => 'Nilai berhasil disimpan'
            ]
        ];
        return $this->respondCreated($response);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $user = $this->user();
        $data = $this->nilaiModel->where('created_id', $user)->find($id);
        if ($data) {
            if (!$this->validasi($id)) return $this->fail($this->validator->getErrors());
            $foto = $this->request->getFile('foto');
            if ($foto == null) {
                $namaFoto = $this->request->getPost('foto_lama');
            } else {
                $namaFoto = $foto->getRandomName();
                $foto->move('gambar', $namaFoto);
                unlink('gambar/' . $this->request->getPost('foto_lama'));
            }
            $data = [
                'kelas'         => $this->request->getPost('kelas'),
                'pelajaran'     => $this->request->getPost('pelajaran'),
                'nilai'         => $this->request->getPost('nilai'),
                'foto_nilai'    => $namaFoto,
                'updated_id'    => $this->user()
            ];
            $this->nilaiModel->update($id, $data);
            $response = [
                'messages'  => [
                    'success'   => 'Nilai berhasil diubah'
                ]
            ];
            return $this->respondUpdated($response);
        } else {
            return $this->failNotFound("Data tidak ditemukan untuk id $id");
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->nilaiModel->getNilai($id, $user)->getRow();
        if ($data) {
            if ($data->foto_nilai !== '') {
                unlink('gambar/' . $data->foto_nilai);
            }
            $this->nilaiModel->delete($id);
            $response = [
                'messages'  => [
                    'success'   => 'Nilai berhasil dihapus'
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound("Data tidak ditemukan untuk id $id");
        }
    }
    private function validasi($id = null)
    {
        return $this->validate([
            'kelas' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]",
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'pelajaran' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]",
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'nilai' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]",
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            // 'foto' => [
            //     'rules' => "uploaded[foto]",
            // ],
        ]);
    }
    private function user()
    {
        $key = getenv('TOKEN_SECRET');
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$header) return $this->failUnauthorized('Token required');
        $token = explode(' ', $header)[1];

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $response = [
                'id'    => $decoded->id,
                'email' => $decoded->email,
            ];
            return $response['id'];
        } catch (\Exception $th) {
            return $this->fail('Invalid Tokean');
        }
    }
}
