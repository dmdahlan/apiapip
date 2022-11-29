<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\RESTful\ResourceController;

class Home extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';
    public function index()
    {
        $data = [
            'messages'        => 'success',
            'data'            => 'Home'
        ];
        return $this->respond($data, 200);
    }
}
