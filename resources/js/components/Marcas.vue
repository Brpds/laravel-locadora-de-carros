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
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID">
                                </input-container-component>
                    
                            </div>
                            <div class="col mb-3">
                                <input-container-component id="inputNome" titulo="Nome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o Nome da marca.">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da Marca">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right">Buscar</button>
                    </template>
                </card-component>
                <!-- fim card buscas -->

                <!--início card marcas-->
                <card-component titulo="Lista de Marcas">
                    <template v-slot:conteudo>
                        <table-component
                         :dados="marcas"
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
                        <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                    </template>
                </card-component>
                <!-- fim card marcas -->
            </div>
        </div>
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
                {{ nomeMarca }}
            </div>
            <div class="form-group">
                <input-container-component id="novoImagem" titulo="Imagem" id-help="novoImagemHelp" texto-ajuda="Adicione uma imagem">
                    <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Adicione Imagem" @change="carregarImagem($event)">
                </input-container-component>
                {{ arquivoImagem }}
            </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>

        
    </div>
</template>
<script>
import axios from 'axios';

    export default {
        computed:{
            token(){

                let token = document.cookie.split(';').find(index => {
                    return index.includes('token=');
                });

                token = token.split('=')[1];
                token = 'Bearer ' + token;
    
                return token;
            }
        },
        data(){
            return {
                urlBase:'http://localhost:8000/api/v1/marca',
                nomeMarca:'',
                arquivoImagem: [],
                transacaoStatus:'',
                transacaoDetalhes:{},
                marcas:[]
            }
        },

        methods:{
            carregarLista(){

                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                };
                axios.get(this.urlBase, config)
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
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                };


                //url, conteúdo, config
                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado';
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro: ' + response.data.id
                        };
                        console.log(response);
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