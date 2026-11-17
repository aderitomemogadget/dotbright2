<html>
<!-- 1º Passo: Importar o ficheiro do Widget -->
<script src="wepay.widget.min.js"></script>
<!-- 2º Passo: Criar um elemento para incluir o Widget -->
<div id="widget_mbway"></div>
<!-- 3º Passo: Fazer a chamada à função do Widget -->
<script type="text/javascript">



/*

id - identificador do elemento HTML criado no segundo passo.
amount - montante total da compra a ser realizada. O valor deste campo é um número
natural, ou seja, numa compra com o valor de 2,99€ o valor a passar ao parâmetro será
de 299.
description - descrição da operação que fica associada à transacção.
link - URL para o endpoint disponibilizado pelo servidor dos pré-requisitos. O servidor
exporta um endpoint, para realizar uma compra, com o URI /financial_operation. Ou seja,
neste caso valor a passar ao parâmetro terá de ser
'http://url/para/servidor/financial_operation'.
callback - função de callback que tem o elemento XMLHttpRequest criado para fazer a
transacção como parâmetro.

*/


BuildMBWayWidget('widget_mbway', 100, 'Widget Demo Payment',
'https://www.on4tech.pt/sucesso.php', function(xhttp) {
alert((JSON.parse(xhttp.response).status === '200') ? 'Sucesso.' : 'Erro.')
})
</script>

</html>