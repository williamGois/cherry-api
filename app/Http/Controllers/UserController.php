<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Helpers\JwtGenerator;
use Illuminate\Support\Facades\Hash;

use Exception;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Estrutura de dados para um usuário",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Identificador único do usuário"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Endereço de email do usuário"
 *     ),
 * )
 */


/**
 * @OA\Info(
 *     title="Economizza Api",
 *     version="1.0.0",
 *     description="Documentação da API",
 *     @OA\Contact(
 *         email="goiswilliam194@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */


/**
 * @OA\Tag(
 *     name="Users",
 *     description="Operações relacionadas a usuários"
 * )
 */
class UserController extends BaseController
{

    public function ini()
    {
        $frase = "Se a vida lhe der limões, faça uma limonada! 🍋😄";
        return response()->json(['mensagem' => $frase]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Lista todos os usuários",
     *     @OA\Response(
     *         response=200,
     *         description="Usuários listados com sucesso"
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Atualiza um usuário",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao atualizar o usuário.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/social/upsert",
     *     tags={"Users"},
     *     summary="Cria ou atualiza um usuário com base em dados de autenticação social",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="id", type="string")
     *                 ),
     *                 @OA\Property(property="session", type="object",
     *                     @OA\Property(property="access_token", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário criado ou atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function upsertFromSocial(Request $request)
    {
        $userData = $request['data']['user'];
        $sessionData = $request['data']['session'];

        $user = User::firstOrNew(['email' => $userData['email']]);

        $user->fill([
            'email' => $userData['email'],
            'social_id' => $userData['id'],
            'avatar' => $userData['avatar'],
            'name' => $userData['name'],
            'token' => $sessionData['access_token'],
        ]);

        $user->save(); // Salva o usuário (funciona tanto para criar quanto para atualizar)

        $token = JwtGenerator::generateToken($user->uuid);

        return response()->json(['user' => $user, 'token' => $token]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Busca um usuário pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        return response()->json($user);
    }


    public function showAll()
    {
        $user = User::all();

        if (!$user) {
            return response()->json(['error' => 'Não tem usuario'], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Cria um novo usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na solicitação"
     *     )
     * )
     */
    public function store(Request $request)
    {

        $validator = $this->getValidationFactory()->make($request->json()->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('email')) {
                return response()->json(['error' => 'O e-mail já está em uso.'], 409);
            }

            return response()->json(['error' => $errors->first()], 400);
        }

        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            $token = JwtGenerator::generateToken($user->id); // Gera o token JWT para o usuário

            return response()->json(['user' => $user, 'token' => $token], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao cadastrar usuário: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/login",
     *     tags={"Users"},
     *     summary="Efetua login de um usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request['email'])->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $token = JwtGenerator::generateToken($user->id); // Gera um token JWT

        return response()->json(['user' => $user, 'token' => $token]);
    }
}
