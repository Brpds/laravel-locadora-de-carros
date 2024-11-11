<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    /*
        sobre a conferência do item nome, quando for passado para o unique,
        ele pode receber 3 parâmetros:

        1) tabela a consultar
        2) nome da coluna em que está procurando
        3) id do registro a ser desconsiderado na pesquisa
    */

    public function rules(){
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png',
            'numero_portas' => 'required|integer|digits_between:1,5', //1 2 3 4 5
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean' //aceitos: true, false, 1, 0, "1", "0"
        ];
    }

    public function marca(){
        //um modelo pertence a uma marca
        return $this->belongsTo('App\Models\Marca');
    }
}
