<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Models\AppSetup\JobQueue\QueueModel;
use App\Jobs\SendEmailJob;
use CodeIgniter\CLI\CLI;

class ProcessEmailQueue extends BaseCommand
{
    protected $group = 'queue';
    protected $name = 'queue:process';
    protected $description = "Proses queue email yang pending";

    public function run(array $params)
    {
        $emailModel = new QueueModel();
        $pendingJobs = $emailModel->getPendingJobs();

        foreach ($pendingJobs as $job) {
            $sendEmailJob = new SendEmailJob();
            $sendEmailJob->execute($job->id);
            CLI::write("Memproses job ID: {$job->id}\n");
        }

        CLI::write("Proses email queue selesai.\n");
    }
}
