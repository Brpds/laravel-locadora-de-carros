<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- início card buscas -->                
                <card-component titulo="Busca de Marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">

                                <input-container-component
                                    titulo="ID"
                                    id="inputId"
                                    id-help="idHelp"
                                    texto-ajuda="Opcional. Informe o ID da marca."
                                >
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-container-component>
                    
                            </div>
                            <div class="col mb-3">
                                <input-container-component id="inputNome" titulo="Nome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o Nome da marca.">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da Marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Buscar</button>
                    </template>
                </card-component>
                <!-- fim card buscas -->

                <!--início card marcas-->
                <card-component titulo="Lista de Marcas">
                    <template v-slot:conteudo>
                        <table-component
                         :dados="marcas.data"
                         :visualizar="{ visivel:true, dataToggle:'modal', dataTarget:'#modalMarcaVisualizar'}"
                         :atualizar="{ visivel:true, dataToggle:'modal', dataTarget:'#modalMarcaAtualizar'}"
                         :remover="{ visivel:true, dataToggle:'modal', dataTarget:'#modalMarcaRemover'}"
                         :titulos="{
                            //caso deseje-se mais ou menos colunas, basta adicionar/remover aqui os títulos correspondentes às colunas no db
                            id: {titulo: 'ID', tipo: 'text'},
                            nome: {titulo: 'Nome', tipo: 'text'},
                            imagem: {titulo: 'Imagem', tipo: 'imagem'},
                            created_at: {titulo: 'Criado em:', tipo: 'data'},
                         }">
                         <!--['id', 'nome', 'imagem', 'created_at']-->
                            
                        </table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row">

                            <div class="col-10">
                                <pagination-component>
                                    <!--:class="l.active" ficará responsável por aplicar a classe active no item li-->
                                    <li v-for="l, key in marcas.links" :key="key" 
                                        :class="l.active ? 'page-item active' : 'page-item '" 
                                        @click="paginacao(l)"
                                    >
                                        <!--v-html="item" ficará reponsável por intepretar caracteres html, enquanto preenche o elemento com o texto recebido-->
                                        <a class="page-link" v-html="l.label"></a></li>
                                </pagination-component>
                            </div>

                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>

                        </div>
                    </template>
                </card-component>
                <!-- fim card marcas -->
            </div>
        </div>

        <!--Início modal inclusão de marca-->
        <modal-component id="modalMarca" titulo="Adicionar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca." v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
            <div class="form-group">
                <input-container-component id="novoNome" titulo="Nome" id-help="novoNomeHelp" texto-ajuda="Informe o Nome da marca:">
                    <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da Marca" v-model="nomeMarca">
                </input-container-component>
            </div>
            <div class="form-group">
                <input-container-component id="novoImagem" titulo="Imagem" id-help="novoImagemHelp" texto-ajuda="Adicione uma imagem">
                    <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Adicione Imagem" @change="carregarImagem($event)">
                </input-container-component>
            </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!--final do modal de inclusão de marca-->

        <!--Início do modal de visualização de marca-->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:alertas>

            </template>

            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>

                <input-container-component titulo="Criado em:">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>

        </modal-component>
        <!--final do modal de visualização de marca-->

        <!--Início do modal de remoção de marca-->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso!" :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'sucesso'"
                >

                </alert-component>
                <alert-component tipo="danger" titulo="Erro na transação." :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'erro'"
                >

                </alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>

        </modal-component>
        <!--final do modal de remoção de marca-->

        <!--Início modal Atualizar de marca-->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso!" :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'sucesso'"
                ></alert-component>

                <alert-component tipo="danger" titulo="Erro na transação." :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'erro'"
                ></alert-component>
            </template>

            <template v-slot:conteudo>
            <div class="form-group">
                <input-container-component id="atualizarNome" titulo="Nome" id-help="atualizarNomeHelp" texto-ajuda="Informe o Nome da marca:">
                    <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da Marca" v-model="$store.state.item.nome">
                </input-container-component>
            </div>

            <div class="form-group">
                <input-container-component id="atualizarImagem" titulo="Imagem" id-help="atualizarImagemHelp" texto-ajuda="Adicione uma imagem">
                    <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Adicione Imagem" @change="carregarImagem($event)">
                </input-container-component>
            </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
            <!--final do modal de Atualizar de marca-->
        
    </div>
</template>
<script>
import axios from 'axios';

    export default {
        data(){
            return {
                urlBase:'http://localhost:8000/api/v1/marca',
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca:'',
                arquivoImagem: [],
                transacaoStatus:'',
                transacaoDetalhes:{},
                marcas:{ data: [] },
                busca:{
                    id: '',
                    nome: '',
                }
            }
        },

        methods:{
            atualizar(){

                // console.log('nome atualizado: ', this.$store.state.item.nome)
                // console.log('imagem: ', this.arquivoImagem)
                // console.log('verbo http', 'patch')

                let formData = new FormData();

                formData.append('_method', 'patch')
                formData.append('nome', this.$store.state.item.nome)

                if(this.arquivoImagem[0]){
                    formData.append('imagem', this.arquivoImagem[0])
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                let config = {
                    headers:{
                        //por ter um envio de file tipo imagem, requer o content-type sinalizando que é um multipart/form-data
                        'Content-type': 'multipart/form-data',
                    }
                }

                axios.post(url, formData, config)
                    .then(response => {
                        //console.log('Atualizado', response)
                        //limpar o campo de seleção de arquivo
                        atualizarImagem.value = '';
                        this.carregarLista()
                        this.$store.state.transacao.status = 'sucesso';
                        this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso!'
                    })
                    .catch(errors => {
                        //console.log('Erro! ', errors.response)
                        this.$store.state.transacao.status = 'erro';
                        this.$store.state.transacao.mensagem = errors.response.data.message;
                        this.$store.state.transacao.dados = errors.response.data.errors;
                    })
            },
            remover(){
                let confirmacao = confirm('Tem certeza que deseja remover o registro?');

                if(!confirmacao){
                    return false;
                } 

                let url = this.urlBase + '/' + this.$store.state.item.id;
                
                console.log(this.$store.state.transacao)
                let formData = new FormData();
                /*
                    adicionar o verbo http 'delete' no append é necessário
                    para que o sistema entenda que, mesmo que o axios faça a req
                    via post, o objetivo é fazer uma operação de delete.
                */
                formData.append('_method', 'delete')


               axios.post(url, formData)
                    .then(response => {
                        //console.log("Registro removido com sucesso", response)
                        this.$store.state.transacao.status = 'sucesso';
                        this.$store.state.transacao.mensagem = response.data.msg;
                        this.carregarLista()
                    })
                    .catch(errors => {
                        this.$store.state.transacao.status = 'erro';
                        this.$store.state.transacao.mensagem = errors.response.data.erro;
                        console.log('Erro na remoção.', errors.response)
                    })
                    
            },
            pesquisar(){
                //console.log(this.busca);

                let filtro = '';
                for(let chave in this.busca){
                    //console.log(chave, this.busca[chave])

                    //função que popula o filtro com os parâmetros passados.
                    //caso não haja parâmetro em algum dos itens passados, o item será ignorado
                    if(this.busca[chave]){
                        if(filtro != ''){
                            filtro += ';'
                        }
                        filtro += chave + ':like:' + this.busca[chave]
    
                    }

                }
                //caso o filtro esteja preenchido, será adicionado à url
                if(filtro != ''){
                    //define urlPaginacao para page=1 para que a aplicação não se perca se a requisição for feita em pagina diferente de 1
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro=' + filtro;
                }else{
                    //se o filtro não estiver preenchido, o valor será vazio
                    this.urlFiltro = '';
                }
                //chamar o método carregarLista aqui formará uma url dinâmica com o filtro passado
                this.carregarLista();
            },
            paginacao(l){
                if(l.url){
                    //this.urlBase = l.url; //ajustando a url com o parâmetro de página
                    this.urlPaginacao = l.url.split('?')[1] //faz o split da url para capturar o parâmetro de paginação
                    this.carregarLista(); //requisitando os dados para a API
                }
                
            },
            carregarLista(){

                /*
                    variável url que armazenará os parâmetros de paginação e a url
                    base para que não haja conflito com a paginação do laravel quando
                    novos parâmetros de pesquisa forem passados
                */
                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro;

                //console.log(url)

                axios.get(url)
                    .then(response => {
                        this.marcas = response.data;
                        //console.log(this.marcas)
                    })
                    .catch(errors =>{
                        console.log(errors)
                    });
            },
            carregarImagem(e){
                this.arquivoImagem = e.target.files
            },
            salvar(){
                //console.log(this.nomeMarca, this.arquivoImagem[0]);

                let formData = new FormData();

                formData.append('nome', this.nomeMarca);
                formData.append('imagem', this.arquivoImagem[0]);

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                };


                //url, conteúdo, config
                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado';
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro: ' + response.data.id
                        };
                        this.carregarLista()
                        //console.log(response);
                    })
                    .catch(errors => {
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }

                        //console.log(errors.response.data.message)
                    })
            }
        },
        //mounted é responsável por executar métodos quando o componente é carregado
        mounted(){
            this.carregarLista();
        }
    }
</script>