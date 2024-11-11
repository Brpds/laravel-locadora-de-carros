<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarroController extends Controller
{
    public function __construct(Carro $carro){
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);
        
        if($request->has('atributos_modelo')){

            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;
            
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);

        }else{

            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')){

            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){

            $carroRepository->selectAtributos($request->atributos);
        
        }


        //--------------------------------------------------------

        
        return response()->json($carroRepository->getResultado(), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());

        $carro =  $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        if($carro === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        };
        return response()->json($carro, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Impossível atualizar. Recurso não existe'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = [];

            //percorrer todas as regras definidas no model
            foreach ($carro->rules() as $input => $regra) {
                
                //coletar apenas a regra aplicável aos parâmetros parciais da requisição
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
            
        }else{
            $request->validate($carro->rules());
        }

        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Impossível excluir. Recurso não existe'], 404);
        }

        $carro->delete();
        return response()->json(['msg' => 'O carro foi removido com sucesso.'], 200);
    }
}
