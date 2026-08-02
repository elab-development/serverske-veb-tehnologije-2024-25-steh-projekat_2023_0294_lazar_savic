<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    // Generisanje tokena za zaboravljenu lozinku

    public function sendResetToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Korisnik sa navedenim email-om ne postoji.',
                'greske' => $validator->errors()
            ], 422);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        return response()->json([
            'status' => true,
            'poruka' => 'Reset token je uspešno kreiran.',
            'reset_token' => $token
        ], 200);
    }

    // Metoda resetovanja lozinke na osnovu dodeljenog reset tokena

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Validacija nije prošla.',
                'greske' => $validator->errors()
            ], 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'status' => false,
                'poruka' => 'Neispravan ili nevažeći reset token.'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'poruka' => 'Lozinka je uspešno izmenjena.'
        ], 200);
    }
}
