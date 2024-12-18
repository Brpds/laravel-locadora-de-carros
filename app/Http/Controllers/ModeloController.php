<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        
        if($request->has('atributos_marca')){

            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);

        }else{

            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')){

            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){

            $modeloRepository->selectAtributos($request->atributos);
        
        }

        
        return response()->json($modeloRepository->getResultado(), 200);




        //-------------------------------------------------------
        
        // $modelos = [];

        // if($request->has('atributos_marca')){
        //     $atributos_marca = $request->atributos_marca;
        //     $modelos = $this->modelo->with('marca:id,'.$atributos_marca);
        // }else{
        //     $modelos = $this->modelo->with('marca');
        // }

        // if($request->has('filtro')){

        //     $filtros = explode(';', $request->filtro);
        //     foreach ($filtros as $key => $condicao) {

        //         $c = explode(':', $condicao);

        //         $modelos = $modelos->where($c[0], $c[1], $c[2]);

        //         //dd(explode(':', $request->filtro));
        //     }
        // }

        // if($request->has('atributos')){
        //     $atributos = $request->atributos;

        //     /*
        //         marca_id, o atributo fk, deve ser encaminhado como parâmetro para
        //         que seja possível fazer o relacionamento com a tabela marca, através
        //         do with();
        //     */
        //     //with() permite receber parâmetros com nomes de colunas específicas da tabela
        //     //a codificação abaixo foi refatorada para implementar uma nova lógica de fluxo
        //     //$modelos = $this->modelo->selectRaw($atributos)->with('marca:id,'.$atributos_marca)->get();

        //     $modelos = $modelos->selectRaw($atributos)->get();

        
        //     //id,nome,imagem
        // }
        // else{

        //     //a codificação abaixo foi refatorada para implementar uma nova lógica de fluxo
        //     //$modelos = $this->modelo->with('marca')->get();

        //     $modelos = $modelos->get();
        //     //all() cria um objeto de consulta + get() = collection
        //     //get() permite modificar a consulta e depois retornar a collection
        // }


        // return response()->json($modelos, 200);
        //return response()->json($this->modelo->with('marca')->get(), 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        //stateless

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo =  $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //mostrará o modelo relacionado à marca pelo with()
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        };
        return response()->json($modelo, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Impossível atualizar. Recurso não existe'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = [];

            //percorrer todas as regras definidas no model
            foreach ($modelo->rules() as $input => $regra) {
                
                //coletar apenas a regra aplicável aos parâmetros parciais da requisição
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
            
        }else{
            $request->validate($modelo->rules());
        }

        //função que se encarregará de apagar o arquivo imagem quando um novo for enviado
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        /*
        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        */

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Impossível excluir. Recurso não existe'], 404);
        }

        //apagará as imagens do diretório
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removido com sucesso.'], 200);
    }
}
