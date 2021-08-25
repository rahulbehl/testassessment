<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\SendEmail;

class AuthController extends Controller
{
    use SendEmail;

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function jwt(User $user): string
    {
        return JWT::encode(
            [
                'iss' => config('constant.jwt.issuer'),
                'sub' => $user->id,
                'iat' => time(),
                'exp' => time() + 2880,
            ],
            config('constant.jwt.secret')
        );
    }

    public function authenticate(User $user): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [
                        'error' => $validator->errors(),
                    ],
                    'success' => false,
                    'status' => 422,
                ], 422);
            } else {
                $user = User::where('email', $this->request->input('email'))
                    ->orWhere('user_name', $this->request->input('email'))
                    ->first();

                if (!$user) {
                    throw new Exception('Please enter correct email or username.');
                }

                if ($user->status == '0') {
                    throw new Exception('You account is on hold.');
                }

                if (Hash::check($this->request->input('password'), $user->password)) {
                    return response()->json([
                        'data' => [
                            'message' => 'Logged in successfully.',
                            'token' => $this->jwt($user),
                            'user' => $user
                        ],
                        'success' => false,
                        'status' => 200,
                    ], 200);
                }

                throw new Exception('Please enter correct password.', 400);
            }
        } catch (Exception $e) {
            Log::error('Failed to login ' . $e->getFile() . ' Line  ' . $e->getLine() . '  errorMessage  ' . $e->getMessage());
            return response()->json([
                'data' => [
                    'error' => $e->getMessage(),
                ],
                'success' => false,
                'status' => 422,
            ], 422);
        }
    }

    public function registerAdmin(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->user_name = $request->user_name;
            $user->password = Hash::make($request->password);
            $user->role = '0';
            $user->status = '1';
            $user->registered_at = Carbon::now()->toDateTimeString();
            $user->created_at = Carbon::now()->toDateTimeString();
            $user->updated_at = Carbon::now()->toDateTimeString();

            if (!$user->save()) {
                throw new Exception('Something went wrong');
            }

            DB::commit();
            return response()->json([
                'data' => [
                    'message' => 'Admin Registered Successfully.',
                ],
                'success' => false,
                'status' => 200,
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to register admin' . $e->getFile() . ' Line  ' . $e->getLine() . '  errorMessage  ' . $e->getMessage());
            return response()->json([
                'data' => [
                    'error' => $e->getMessage(),
                ],
                'success' => false,
                'status' => 422,
            ], 422);
        }
    }

    public function registerUser(Request $request)
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [
                        'error' => $validator->errors(),
                    ],
                    'success' => false,
                    'status' => 422,
                ], 422);
            }

            DB::beginTransaction();
            $invitation = Invitation::where('id', $request->invitationId)->first();

            if ($invitation) {
                $invitation->status = '1';

                if (!$invitation->save()) {
                    throw new Exception('Something went wrong.');
                }
            }

            $user = new User();

            $user->name = null;
            $user->email = ($invitation) ? $invitation->email : null;
            $user->user_name = $request->username;
            $user->password = Hash::make($request->password);
            $user->role = '1';
            $user->status = '0';
            $user->registered_at = Carbon::now()->toDateTimeString();
            $user->created_at = Carbon::now()->toDateTimeString();
            $user->updated_at = Carbon::now()->toDateTimeString();

            if (!$user->save()) {
                throw new Exception('Something went wrong.');
            }

            $code = rand(100000, 999999);

            $activationCode = new ActivationCode();

            $activationCode->user_id = $user->id;
            $activationCode->code = $code;

            if (!$activationCode->save()) {
                throw new Exception('Something went wrong.');
            }

            $sendEmail = $this->sendEmail([
                'to' => $invitation->email,
                'subject' => config('constant.emails.subject.data_delete_notification'),
                'view' => 'activationcode',
                'code' => $code,
            ]);

            DB::commit();

            return response()->json([
                'data' => [
                    'message' => 'Check Your mail for code.',
                    'user' => $user
                ],
                'success' => false,
                'status' => 200,
            ], 200);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to to register user ' . $e->getFile() . ' Line  ' . $e->getLine() . '  errorMessage  ' . $e->getMessage());
            return response()->json([
                'data' => [
                    'error' => $e->getMessage(),
                ],
                'success' => false,
                'status' => 422,
            ], 422);
        }
    }

    public function activateUser(Request $request)
    {
        try {
            DB::beginTransaction();

            $activationCode = ActivationCode::where('user_id', $request->userId)->first();

            if (!$activationCode) {
                throw new Exception("Invalid code.");
            }

            if ($activationCode->code != $request->code) {
                throw new Exception("Invalid code.");
            }

            $user = User::where('id', $request->userId)->first();

            if (!$user) {
                throw new Exception("Invalid user.");
            }

            $user->status = '1';

            if (!$user->save()) {
                throw new Exception('Smething went wrong');
            }

            DB::commit();
            return response()->json([
                'data' => [
                    'message' => 'Your account activated successfully.',
                ],
                'success' => false,
                'status' => 200,
            ], 200);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to to activate user ' . $e->getFile() . ' Line  ' . $e->getLine() . '  errorMessage  ' . $e->getMessage());
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
