<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe.',
            'nome.min' => 'Mínimo de 3 caracteres',
            'imagem.mimes' => 'O arquivo deve ser uma imagem no formato PNG'
        ];
    }

    public function modelos (){
        //uma marca possui vários modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
