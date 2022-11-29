<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProfileController extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';
    public function __construct()
    {
        $this->authModel = new AuthModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
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
            return $this->respond($response);
        } catch (\Exception $th) {
            return $this->fail('Invalid Tokean');
        }
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
        //
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
        if (!$this->validasi($id = null)) return $this->fail($this->validator->getErrors());
        $user = $this->authModel->where('id', $this->user())->first();
        $password = password_verify($this->request->getPost('pass_lama'), $user['password_hash']);
        if (!$password) {
            return $this->fail('Password lama salah');
        }
        $data = [
            'password_hash'     => password_hash($this->request->getPost('pass_confirm'), PASSWORD_BCRYPT),
        ];
        $this->authModel->update($this->user(), $data);
        $response = [
            'messages'  => [
                'success'   => 'Password berhasil diubah'
            ]
        ];
        return $this->respondUpdated($response);
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
    private function validasi()
    {
        return $this->validate([
            'pass_lama' => [
                'rules' => 'required',
                'errors' => [
                    'required'      => 'password lama tidak boleh kosong',
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                ]
            ],
            'pass_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'matches'       => 'password tidak sama',
                ]
            ],
        ]);
    }
}
