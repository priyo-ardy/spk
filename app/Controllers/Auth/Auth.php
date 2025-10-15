<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\AuthModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Services\EmailQueueService;

class Auth extends BaseController
{
    protected $authModel;
    protected $kirimEmail;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->kirimEmail = new EmailQueueService();
    }

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

                // log_action('Auth', 'process', 'error', current_url(), "Validation failed", '', json_encode([
                //     'data' => $validasi->getErrors()
                // ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed $error_message");
            }

            $model = new AuthModel();
            $username = trim($this->request->getPost('username'));
            $password = trim($this->request->getPost('password'));

            $cari_user = $model->getUser($username);
            if (!$cari_user) {
                // log_action('Auth', 'process', 'error', current_url(), "Account not available", '', json_encode([
                //     'data' => $model->errors()
                // ]));

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "User not available");
            }

            $verify = password_verify($password, $cari_user->user_password);
            if (!$verify) {
                // log_action('Auth', 'process', 'error', current_url(), "Invalid password");

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Invalid password");
            }

            if ($cari_user->user_status === 0) {
                // log_action('Auth', 'process', 'error', current_url(), "User is inactive");

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
                // log_action('Auth', 'process', 'error', current_url(), "Authorization failed", '', json_encode([
                //     'data' => $model->errors()
                // ]));

                return pesan(ResponseInterface::HTTP_FORBIDDEN, "Authorization failed");
            }

            // Set session
            session()->set([
                'logged'        => true,
                'user_name'     => $cari_user->user_name,
                'full_name'     => $cari_user->full_name,
                'level'         => $cari_user->user_level,
                'user_image'    => $cari_user->user_image
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Login successful',
                'redirect' => base_url('dashboard')
            ]);
        } catch (\Exception $e) {
            // log_action('Auth', 'process', 'error', current_url(), "Unexpected error occured", '', json_encode([
            //     'message' => $e->getMessage(),
            //     'file' => $e->getFile(),
            //     'line' => $e->getLine(),
            //     'trace' => $e->getTraceAsString()
            // ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    public function logOut()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }

    function forgotPassword()
    {
        return view('Auth/forgot');
    }

    function resetPassword()
    {
        if ($this->request->getMethod() !== 'POST') {
            log_action('Auth', 'reset', 'error', current_url(), "Request method not allowed");
        }

        $rules = [
            'user_email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => "Email is required",
                    'valid_email' => "Email is not valid"
                ]
            ]
        ];

        $validasi = Services::validation();
        $validasi->setRules($rules);

        if (!$validasi->withRequest($this->request)->run()) {
            $error_message = implode("<br>", $validasi->getErrors());
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed $error_message");
        }

        $email = $this->request->getPost('user_email');
        $encrypt_email = email_hash($email);
        $get_user_data = $this->authModel->where('email_hash ', $encrypt_email)->first();

        if (!$get_user_data) {
            return pesan(ResponseInterface::HTTP_NOT_FOUND, "User not found");
        }

        $token = enkripsi($get_user_data->user_id);
        $name = $get_user_data->full_name;
        $email_address = dekripsi($get_user_data->user_email);

        $emailQueue = new EmailQueueService();

        $data = [
            'token' => $token,
            'name' => $name,
            'date' => date('Y-m-d H:i:s'),
        ];

        $toEmail = $email_address;
        $subject = "Password Recovery";
        $body = view('Auth/reset_password', $data);

        $emailQueue->queueEmail($toEmail, $subject, $body);

        return pesan(ResponseInterface::HTTP_OK, "Email sent successfully, please check your inbox or spam folder for the link.");
    }

    function recoverPassword($token)
    {
        return view('Auth/reset', ['token' => $token]);
    }

    function changePassword()
    {
        if ($this->request->getMethod() !== 'POST') {
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Request method not allowed");
        }

        try {
            $password = $this->request->getPost('new_password');
            $token = $this->request->getPost('data_token');
            $user_id = dekripsi($token);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Token is required"
                    ]
                ],
                'new_password' => [
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
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed $error_message");
            }

            #make validation rules for new password, the minimum password length is 8 characters, must have at least one uppercase, one number, one lowercase, and one special character
            $pswd = $this->request->getPost('new_password');
            $pswd_length = strlen($pswd);
            $pswd_upper = preg_match('@[A-Z]@', $pswd);
            $pswd_lower = preg_match('@[a-z]@', $pswd);
            $pswd_number = preg_match('@[0-9]@', $pswd);
            $pswd_special = preg_match('@[^\w]@', $pswd);

            if ($pswd_length < 8) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Password must be at least 8 characters long");
            }

            if (!$pswd_upper) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Password must contain at least one uppercase letter");
            }

            if (!$pswd_lower) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Password must contain at least one lowercase letter");
            }

            if (!$pswd_number) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Password must contain at least one number");
            }

            if (!$pswd_special) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Password must contain at least and one special character");
            }

            $update = $this->authModel->update($user_id, ['user_password' => $password_hash]);
            if (!$update) {
                throw new \Exception("Failed to update password");
            }

            return pesan(ResponseInterface::HTTP_OK, "Password updated successfully");
        } catch (\Exception $e) {
            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
            log_message('error', "Unexpected error occured " . $e->getMessage());
        }
    }
}
