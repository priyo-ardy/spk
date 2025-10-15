<?php

namespace App\Jobs;

use CodeIgniter\Email\Email;
use App\Models\AppSetup\JobQueue\QueueModel;


class SendEmailJob
{
    /**
     * Send email job based on the given job ID.
     *
     * @param string $jobId The ID of the job to be executed.
     *
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function execute(string $jobId)
    {
        $emailQueue = new QueueModel();
        $job = $emailQueue->where('id', $jobId)->first();

        if (!$job) {
            return false;
        }

        $email = new Email();
        $email->setFrom('no-reply@schlemmer.co.id', 'Schlemmer SPK Application');
        $email->setTo($job->to_email);
        $email->setSubject($job->subject);
        $email->setMessage($job->body);

        if ($email->send()) {
            $emailQueue->updateJobStatus($jobId, 'sent');
            return true;
        } else {
            $emailQueue->updateJobStatus($jobId, 'failed');
            $emailQueue->update($jobId, ['reason' => $email->printDebugger()]);
            log_message('error', 'Gagal mengirim email: ' . $email->printDebugger());
        }
    }
}
