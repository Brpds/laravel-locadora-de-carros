<template>
<div>
    <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col" v-for="t, key in titulos" :key="key">{{ t.titulo }}</th>
            <!--o v-if abaixo é responsável por exibir a coluna caso qualquer das variáveis seja true-->
            <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel"></th>
          </tr>
        </thead>
        <tbody>

          <tr v-for="obj, chave in dadosFiltrados" :key="chave">
            <td v-for="valor, chaveValor in obj" :key="chaveValor">
              <span v-if="titulos[chaveValor].tipo == 'text'">{{ valor }}</span>
              <span v-if="titulos[chaveValor].tipo == 'data'">{{ '...' + valor }}</span>
              <span v-if="titulos[chaveValor].tipo == 'imagem'">
                <img :src="'/storage/'+valor" width="30" height="30" />
              </span>
            </td>

            <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
              <!--botão com data-toggle e data-target para o modal de visualização-->
              <button
                v-if="visualizar" 
                class="btn btn-outline-primary btn-sm" 
                :data-toggle="visualizar.dataToggle" 
                :data-target="visualizar.dataTarget"
                @click="setStore(obj)"
              >
                  Visualizar
              </button>
              <button 
                v-if="atualizar.visivel" 
                class="btn btn-outline-primary btn-sm"
                :data-toggle="atualizar.dataToggle"  
                :data-target="atualizar.dataTarget"
                @click="setStore(obj)"
              >
                Atualizar
              </button>

              <button 
                v-if="remover" 
                class="btn btn-outline-danger btn-sm"
                @click="setStore(obj)"
                :data-toggle="remover.dataToggle"
                :data-target="remover.dataTarget"
              >
                Remover
              </button>
            </td>
          </tr>

          <!--
          <tr v-for="obj in dados" :key="obj.id">
            <td v-if="titulos.includes(chave)" v-for="valor, chave in obj" :key="chave">
				<span v-if="chave == 'imagem'">
					<img :src="'/storage/'+valor" width="30" height="30" />
				</span>
				<span v-else>{{ valor }}</span>
            </td>

            <th scope="row">{{ marca.id }}</th>
            <td>{{ marca.nome }}</td>
            <td><img :src="'/storage/'+ marca.imagem" width="30px" height="30px"/></td>

          </tr>
        -->
        </tbody>
    </table>
</div>
</template>

<script>
export default {
    props:['dados', 'titulos', 'visualizar', 'atualizar', 'remover'],
    methods:{
        setStore(obj){
            //reiniciará os status da transação para que os dados de sucesso/erro sejam definidos como vazios
            this.$store.state.transacao.status = ''
            //reiniciará as mensagens da transação para que os dados de sucesso/erro sejam definidos como vazios
            this.$store.state.transacao.mensagem = ''
            //reiniciará a mensagem de dados da transação para que os dados de sucesso/erro sejam definidos como vazios
            this.$store.state.transacao.dados = ''
            //armazenará no state item global o objeto passado por parâmetro
            this.$store.state.item = obj
            //console.log(obj);
        }
    },
    computed:{
      dadosFiltrados(){

        //console.log(this.dados)

        let campos = Object.keys(this.titulos)
        let dadosFiltrados = [];

        this.dados.map((item, chave) => {
          //console.log(chave, item)

          let itemFiltrado = {}

          campos.forEach(campo => {
            //console.log(campo)
            itemFiltrado[campo] = item[campo] //utilizar a sintaxe de array para atribuir valores a objetos

            //console.log(chave, item, campo)
          })
          dadosFiltrados.push(itemFiltrado)
        })
        //console.log(campos)
        //console.log()

        //console.log(dadosFiltrados)

        return dadosFiltrados
      }
    }
}
</script>