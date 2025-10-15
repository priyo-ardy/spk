<?php

namespace App\Services;

use App\Models\AppSetup\JobQueue\QueueModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class EmailQueueService
{
    protected $queueModel;
    protected $email;

    public function __construct()
    {
        $this->queueModel = new QueueModel();
        $this->email = Services::email();
    }

    /**
     * Tambah email ke queue
     */
    public function queueEmail(string $toEmail, string $subject, string $body)
    {
        $emailData = [
            'id' => generate_uuid(),
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
        ];

        return $this->queueModel->insert($emailData);
    }
}
