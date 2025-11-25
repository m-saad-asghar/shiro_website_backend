<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

trait GeneralTrait
{
    public function apiResponse($data = null, bool $status = true, $error = null, $statusCode = 200)
    {
        return response([
            'data' => $data,
            'status' => $status,
            'error' => $error,
            'statusCode' => $statusCode
        ], $statusCode);
    }

    public function unAuthorizeResponse()
    {
        return $this->apiResponse(null, false, 'Unauthorized', 401);
    }

    public function notFoundResponse($message)
    {
        return $this->apiResponse(null, false, $message, 404);
    }

    public function requiredField($message)
    {
        return $this->apiResponse(null, false, $message, 400);
    }

    public function forbiddenResponse()
    {
        return $this->apiResponse(null, false, 'Forbidden', 403);
    }

    public function handleException(\Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $modelName = class_basename($e->getModel());
            return $this->notFoundResponse("Element ($modelName) not found");
        } elseif ($e instanceof ValidationException) {
            $errors = $e->validator->errors();
            return $this->requiredField($errors->first());
        } elseif ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } else {
            return $this->apiResponse(null, false, 'An unexpected error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function send_email($templateName, $email1, $subj, $order)
    {
        try {
            // Improved method with debugging
            $result = Mail::send($templateName, $order, function ($message) use ($email1, $subj) {
                $message->to($email1)
                       ->subject($subj)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            // Log the success of the process
            \Log::info("Email sent successfully to: {$email1} with subject: {$subj}");
            
            return true;
        } catch (\Symfony\Component\Mailer\Exception\TransportException $exception) {
            \Log::error('Mail Transport Error: ' . $exception->getMessage());
            \Log::error('Mail Config: ' . json_encode([
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
            ]));
            return false;
        } catch (\Exception $e) {
            \Log::error('Mail General Error: ' . $e->getMessage());
            \Log::error('Template: ' . $templateName);
            \Log::error('Recipient: ' . $email1);
            return false;
        }
    }

    // Alternative simple email method
    public function send_simple_email($to, $subject, $message)
    {
        try {
            Mail::raw($message, function ($mail) use ($to, $subject) {
                $mail->to($to)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            // Log the success of the process
            \Log::info("Simple email sent to: {$to}");
            return true;
        } catch (\Exception $exception) {
            // Log the error of the process
            \Log::error('Simple Mail Error: ' . $exception->getMessage());
            return false;
        }
    }
}
