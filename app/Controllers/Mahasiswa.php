<?php

namespace App\Controllers;

use App\Models\MahasiswaModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Mahasiswa extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';
    public function __construct()
    {
        $this->mahasiswaModel = new MahasiswaModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'messages'        => 'success',
            'data_maasiswa'   => $this->mahasiswaModel->findall()
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
        //
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
        $foto = $this->request->getFile('path_foto');
        $namaFoto = $foto->getRandomName();
        $foto->move('gambar', $namaFoto);
        $data = [
            'nim'           => $this->request->getPost('nim'),
            'nama'          => $this->request->getPost('nama'),
            'path_foto'     => $namaFoto,
        ];
        if (!$this->validasi()) return $this->fail($this->validator->getErrors());
        $this->mahasiswaModel->insert($data);
        $response = [
            'messages'  => [
                'success'   => 'Mahasiswa berhasil disimpan'
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
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
    private function validasi()
    {
        return $this->validate([
            'nim' => [
                'rules' => 'required|alpha_numeric_punct|is_unique[mahasiswa.nim,id,{id}]',
            ],
            'nama' => [
                'rules' => 'required|alpha_numeric_punct',
            ],
            // 'path_foto' => [
            //     'rules' => 'required',
            // ],
        ]);
    }
}
