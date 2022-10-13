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
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];
        if (!$this->validasi()) return $this->fail($this->validator->getErrors());
        $this->authModel->insert($data);
        $response = [
            'status'    => 200,
            'error'     => null,
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
            'email' => $this->request->getPost('email'),
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        return $this->respond($token);
    }
    private function validasi()
    {
        return $this->validate([
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email,id,{id}]',
            ],
            'username' => [
                'rules' => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
            ],
            'password' => [
                'rules' => 'required',
            ],
            'pass_confirm' => [
                'rules' => 'required|matches[password]',
            ],
        ]);
    }
}
