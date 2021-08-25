<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
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

            $user = User::find(Auth::id());

            $user->name = $request->name;

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = storage_path('/app/images');
                $image->move($destinationPath, $name);

                $user->avatar = url('/public/images/' . $name);
            }

            if (!$user->save()) {
                throw new Exception('Something went wrong');
            }

            DB::commit();
            return response()->json([
                'data' => [
                    'message' => 'Profile updated successfully.',
                    'user' => Auth::user()
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
