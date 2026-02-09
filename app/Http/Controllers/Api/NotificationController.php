<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WebsiteNotificationMail;

class NotificationController extends Controller
{
    public function sendEmail(Request $request)
    {
        // OPTIONAL: light validation for a few common fields (won't block extra fields)
        $request->validate([
            'email' => 'nullable|email',
            'name'  => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        // Get ALL frontend data (exclude file objects / huge stuff if any)
        $payload = $request->except(['attachments', 'files']);

        // Extra safety: prevent massive payload from breaking email
        // (you can tune this)
        $payloadJsonSize = strlen(json_encode($payload));
        if ($payloadJsonSize > 200000) { // ~200KB
            return response()->json([
                'success' => false,
                'message' => 'Payload too large',
            ], 413);
        }

        try {
            $to = 'marketing@shiroestate.ae';

            Mail::to($to)->send(new WebsiteNotificationMail($payload));

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email sending failed',
            ], 500);
        }
    }
}