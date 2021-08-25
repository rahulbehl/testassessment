<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\SendEmail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    use SendEmail;

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $invitation = new Invitation();

            $invitation->email = $request->email;
            $invitation->status = '0';

            if (!$invitation->save()) {
                throw new Exception('Something went wrong while sending invitation.');
            }

            $sendEmail = $this->sendEmail([
                'to' => $request->email,
                'subject' => config('constant.emails.subject.data_delete_notification'),
                'view' => 'invitation',
                'signup_url' => 'https://front-end-url.com/i=' . $invitation->id . '&s=1',
            ]);

            DB::commit();
            return response()->json([
                'data' => [
                    'message' => 'Invitation sent.',
                ],
                'success' => false,
                'status' => 200,
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to to send invitation ' . $e->getFile() . ' Line  ' . $e->getLine() . '  errorMessage  ' . $e->getMessage());
            return response()->json([
                'data' => [
                    'error' => $e->getMessage(),
                ],
                'success' => false,
                'status' => 422,
            ], 422);
        }
    }
}
