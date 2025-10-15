<?php

use CodeIgniter\HTTP\ResponseInterface;

if (!function_exists('kirim_email')) {
    function kirim_email($to_email, $subject, $message)
    {
        $email = \Config\Services::email();

        $config = [
            'protocol'  => 'smtp',
            'SMTPHost'  => 'smtp.gmail.com',
            'SMTPPort'  => 587,
            'SMTPUser'  => 'schlemmerid.dev@gmail.com',
            'SMTPPass'  => 'qbzwvghjeqycxkxy',
            'charset'   => 'utf-8',
            'mailType'  => 'html',
            'newline'   => "\r\n"
        ];

        $email->initialize($config);
        $email->setFrom('schlemmerid.dev@gmail.com', 'Admin');
        $email->setTo($to_email);
        $email->setSubject($subject);
        $email->setMessage($message);


        if ($email->send()) {
            pesan(ResponseInterface::HTTP_OK, "Email sent successfully, please check your inbox or spam folder for the link.");
        } else {
            pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to send email");
        }
    }
}
