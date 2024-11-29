<template>
<div>
    <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col" v-for="t, key in titulos" :key="key">{{ t.titulo }}</th>
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
    props:['dados', 'titulos'],
    computed:{
      dadosFiltrados(){

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