<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\AuthModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Auth extends BaseController
{
    public function index()
    {
        return view('Auth/index');
    }

    public function prosesLogin()
    {
        if ($this->request->getMethod() !== 'POST') {
            log_action('Auth', 'process', 'error', current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Reequest not allowed");
        }

        try {
            $rules = [
                'username' => [
                    'rules' => 'required|min_length[1]|alpha_numeric',
                    'errors' => [
                        'required' => 'Username is required',
                        'min_length' => 'Minimum character of username is {param}',
                        'alpha_numeric' => 'Username must only contain numbers and letters'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Password is required"
                    ]
                ]
            ];

            $validasi = Services::validation();
            $validasi->setRules($rules);

            if (!$validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $validasi->getErrors());

                log_action('Auth', 'process', 'error', current_url(), "Validation failed", '', json_encode([
                    'data' => $validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed $error_message");
            }

            $model = new AuthModel();
            $username = trim($this->request->getPost('username'));
            $password = trim($this->request->getPost('password'));

            $cari_user = $model->getUser($username);
            if (!$cari_user) {
                log_action('Auth', 'process', 'error', current_url(), "Account not available", '', json_encode([
                    'data' => $model->errors()
                ]));

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "User not available");
            }

            $verify = password_verify($password, $cari_user->user_password);
            if (!$verify) {
                log_action('Auth', 'process', 'error', current_url(), "Invalid password");

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Invalid password");
            }

            if ($cari_user->user_status === 0) {
                log_action('Auth', 'process', 'error', current_url(), "User is inactive");

                return pesan(ResponseInterface::HTTP_FORBIDDEN, "User is inactive");
            }

            // Update login data
            $updateData = [
                'last_login' => date('Y-m-d H:i:s'),
                'login_from' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()
            ];

            $update = $model->update($cari_user->user_id, $updateData);
            if (!$update) {
                log_action('Auth', 'process', 'error', current_url(), "Authorization failed", '', json_encode([
                    'data' => $model->errors()
                ]));

                return pesan(ResponseInterface::HTTP_FORBIDDEN, "Authorization failed");
            }

            // Set session
            session()->set([
                'logged'     => true,
                'user_name'  => $cari_user->user_name,
                'full_name'  => $cari_user->full_name,
                'user_image' => $cari_user->user_image
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Login successful',
                'redirect' => base_url('dashboard')
            ]);
        } catch (\Exception $e) {
            log_action('Auth', 'process', 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    public function logOut()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }
}
