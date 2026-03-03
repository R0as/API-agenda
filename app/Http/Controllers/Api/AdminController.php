<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="List all users",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     summary="Create a new user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string", enum={"admin", "user"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'user'])]
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{user}",
     *     summary="Get a specific user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/users/{user}",
     *     summary="Update an existing user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string", enum={"admin", "user"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['sometimes', Rule::in(['admin', 'user'])]
        ]);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/{user}",
     *     summary="Delete a user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted"),
     *     @OA\Response(response=400, description="Cannot delete yourself")
     * )
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Não pode excluir a si mesmo'], 400);
        }
        $user->delete();
        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}
