<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct(Marca $marca){
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);
        
        if($request->has('atributos_modelos')){

            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);

        }else{

            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){

            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){

            $marcaRepository->selectAtributos($request->atributos);
        
        }


        //--------------------------------------------------------

        
        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$marca = Marca::create($request->all());

        $request->validate($this->marca->rules(), $this->marca->feedback());
        //stateless
        

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca =  $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //associará marca com modelos, quando da exibição
        //um novo nó array será adicionado com todos os itens modelos associados
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        };
        return response()->json($marca, 200);
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
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Impossível atualizar. Recurso não existe'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = [];

            //percorrer todas as regras definidas no model
            foreach ($marca->rules() as $input => $regra) {
                
                //coletar apenas a regra aplicável aos parâmetros parciais da requisição
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
            
        }else{
            $request->validate($marca->rules(), $marca->feedback());
        }

        //função que se encarregará de apagar o arquivo imagem quando um novo for enviado
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        /*
            preencher o objeto $marca com os dados do request.
            fill() garantirá que os itens que não forem adicionados na requisição
            sejam preenchidos com os itens anteriores da request no objeto, permitindo
            que o método patch funcione corretamente
        */
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        /*
            o método save se encarrega de salvar as alterações, criando um novo
            registro caso o id seja diferente
        */
        $marca->save();

        /*
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        */

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$marca->delete();
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Impossível excluir. Recurso não existe'], 404);
        }

        //apagará as imagens do diretório
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso.'], 200);
    }
}
