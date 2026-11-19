# Pedidos de reparação: gravação e backoffice

## Aplicar

1. Fazer backup da base de dados de dotbright2.
2. Executar no phpMyAdmin o CREATE TABLE repair_orders fornecido no chat. Não existem ficheiros .sql no projeto e o código não altera o esquema automaticamente.
3. Confirmar PHP 7.3+ com PDO MySQL e OpenSSL.
4. Configurar no ambiente do PHP a variável DOTBRIGHT_REPAIR_ACCESS_KEY com uma chave aleatória de 32 bytes em Base64. Para gerar uma chave no servidor: php -r "echo base64_encode(random_bytes(32)), PHP_EOL;". Guardar a chave fora do repositório e fazer cópia segura; não a enviar para logs nem substituir depois de haver pedidos cifrados. O processo PHP/FPM/Apache deve receber a variável; exportá-la apenas numa sessão SSH não é suficiente.
5. Publicar os ficheiros PHP alterados/criados. Testes e documentação não são necessários no servidor.
6. Abrir Produtos → Pedidos de reparação (admin/products/repair_orders.php). Requer a permissão existente Produtos, índice 8, com valor 1.
7. Usar Importar / continuar pedidos antigos para importar a tabela pedidos em lotes de até 500, sem apagar os originais. Repetir até o lote indicar zero registos analisados. O ID de origem é único e impede duplicação. A família atual do artigo tem de pertencer a REPARAÇÕES (85 ou descendentes); artigos eliminados ou movidos para famílias normais são ignorados. Não se recuperam dados que nunca foram guardados nem se atribui uma data/preço histórico a partir do catálogo atual.
8. Fazer um pedido controlado, verificar o detalhe e os valores, pesquisar e alterar o estado. Se o mailer falhar, o registo mantém-se e o BO indica o resultado da tentativa. Não repetir a submissão para tentar reenviar emails.

## Âmbito

Os novos pedidos passam a ser gravados exclusivamente em repair_orders, antes de qualquer tentativa de email. A tabela pedidos fica para consulta/importação dos registos antigos; não existe escrita dupla.

O registo contém o cliente, faturação, moradas e período, consentimentos com data UTC, identificação e nome da reparação, referência/EAN, peça e descrição, prazo em loja, garantia, preços/portes/IVA/total, estado, dados cifrados de acesso e resultado do envio para cada destinatário. Dados públicos do pedido ficam em details_json e os dados pesquisáveis em colunas próprias. Estes dados são uma fotografia do momento da submissão; o detalhe não recalcula preços nem consulta a peça/garantia atual do catálogo.

O formulário atual só fornece código e padrão; o modo de acesso é inferido desses campos. Sem ambos, fica “não fornecido”; não se inventa a escolha “código removido”. O código e padrão usam AES-256-GCM, ficam fora do JSON público e dos emails e só são revelados mediante POST com CSRF, confirmação e permissões de Produtos e Encomendas (8 e 9). Sem chave configurada, pedidos com código/padrão são rejeitados antes da gravação, sem guardar os segredos em claro. Sem dados de acesso, a ausência da chave não impede a gravação.

O número REP-ID identifica o pedido no BO e nos emails. Os estados são Recebido, Em análise, Recolha agendada, Concluído e Cancelado. A alteração exige CSRF, verifica concorrência pela versão e regista operador/data. “Concluído” é um estado manual; não cria uma ficha RMA nem executa pagamentos, recolhas ou alterações de stock.

Os estados de email significam não tentado, aceite pelo serviço de envio, falhou ou envio incerto; aceite não prova entrega. A integração de mailer não foi modificada. Mantêm-se os destinatários, as mensagens de erro, o popup curto e o regresso à homepage após OK.

## Validação

- php tests/repair_request.php
- php tests/repair_product_settings.php
- php tests/repair_product_settings.php --missing-table
- php tests/repair_orders.php (PDO SQLite e OpenSSL ativos)

O último teste usa SQLite em memória para validar gravação, fotografia dos valores, cifra/tamper/chave errada, campos obrigatórios, famílias, estados/concorrência, pesquisa/paginação, importação sem duplicados, permissões, CSRF e apresentação dos detalhes. Não envia emails nem liga à base de dados da loja.

O header e os quatro painéis públicos, incluindo os respetivos CSS/JS, não foram alterados. A única alteração no layout do backoffice é a ligação no menu Produtos e a nova área, com estilos limitados a .ro-admin.
