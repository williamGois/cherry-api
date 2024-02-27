<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class UpdateUserRequest extends Request
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize()
    {
        // Implemente a lógica de autorização conforme necessário
        return true;
    }

    /**
     * Regras de validação para a atualização do usuário.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email,' . $this->user,
        ];
    }
}
