<?php

namespace App\Controllers\AppSetup\Queue;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EmailQueueService;
use Config\Services;

class EmailController extends BaseController
{
    protected $emailQueueService;

    public function __construct()
    {
        $this->emailQueueService = new EmailQueueService();
    }

    public function sendResetPasswordRequest() {
        
    }
}
