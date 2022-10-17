<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';
    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $data = [
            'email'         => $this->request->getPost('email'),
            'username'      => $this->request->getPost('username'),
            'nis'           => $this->request->getPost('nis'),
            'level'         => $this->request->getPost('level'),
            'password_hash' => password_hash($this->request->getPost('pass_confirm'), PASSWORD_BCRYPT),
        ];
        if (!$this->validasi()) return $this->fail($this->validator->getErrors());
        $this->authModel->insert($data);
        $response = [
            'status'    => 200,
            'messages'  => [
                'success'   => 'user berhasil disimpan'
            ]
        ];
        return $this->respondCreated($response);
    }
    public function login()
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        $user = $this->authModel->where('email', $this->request->getVar('email'))->first();
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        if (!$user) {
            return $this->failNotFound('Email tidak terdaftar');
        }
        if ($user['active'] == 0) {
            return $this->failNotFound('User belum aktif');
        }
        $password = password_verify($this->request->getPost('password'), $user['password_hash']);
        if (!$password) {
            return $this->fail('Password salah');
        }

        $key = getenv('TOKEN_SECRET');
        $payload = [
            'iss'   => 'apiapip',
            'aud'   => 'logintoken',
            'iat'   => time(),
            'exp'   => time() + (60 * 60),
            'id'    => $user['id'],
            'email' => $user['email'],
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        return $this->respond($token);
    }
    private function validasi()
    {
        return $this->validate([
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email,id,{id}]',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'is_unique'     => '{field} sudah ada',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'username' => [
                'rules' => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'is_unique'     => '{field} sudah ada',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'nis' => [
                'rules' => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.nis,id,{id}]',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
                    'is_unique'     => '{field} sudah ada',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'level' => [
                'rules' => 'required',
                'errors' => [
                    'required'      => '{field} tidak boleh kosong',
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
