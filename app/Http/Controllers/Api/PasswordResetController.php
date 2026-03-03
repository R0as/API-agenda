<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryCode;
use OpenApi\Annotations as OA;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/password/email",
     *     summary="Send password recovery code to email",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Code sent successfully"),
     *     @OA\Response(response=500, description="Error sending email")
     * )
     */
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        // Envio de e-mail disparado
        try {
            Mail::to($request->email)->send(new PasswordRecoveryCode($code));
            Log::info("E-mail de recuperação enviado para {$request->email}");
        } catch (\Exception $e) {
            Log::error("Erro ao enviar e-mail para {$request->email}: " . $e->getMessage());
            return response()->json(['error' => 'Não foi possível enviar o e-mail no momento. Tente novamente mais tarde.'], 500);
        }

        return response()->json(['message' => 'Se o e-mail existir, um código foi enviado. (Verifique os logs)']);
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Reset user password using recovery code",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "code", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password successfully reset"),
     *     @OA\Response(response=400, description="Invalid or expired code")
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->code, $record->token)) {
            return response()->json(['error' => 'Código inválido ou expirado.'], 400);
        }

        // Atualiza a senha
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Remove o código
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
