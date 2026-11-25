# Integração dos pedidos RRE com a assistência RMA

## Análise e decisões

Foi analisada a árvore de código próprio de `rmas/`, incluindo autenticação partilhada com o BO, listagem/pesquisa, criação/alteração da ficha, estados/proveniências, clientes, linhas de intervenção, histórico, notificações, assinaturas/PDF e empréstimos. O export `dotbright_dev2.sql` contém estrutura, mas **não contém INSERTs nem registos**. Não permite determinar os estados/proveniências efetivamente configurados, analisar pedidos reais ou confirmar o erro através dos logs do servidor.

Na listagem, `settings.inc.php` carrega `funcoes.inc.php`. Este incluía incondicionalmente uma biblioteca privada de SMS num caminho calculado fora do projeto. Uma biblioteca ausente interrompia também um simples GET da lista com erro fatal. A dependência passou a ser carregada apenas ao enviar um SMS, com falha controlada quando indisponível. O caminho pode ser indicado por `DOTBRIGHT_NOS_SMS_LIBRARY`; mantêm-se as localizações históricas como alternativas. Não foi configurado nenhum serviço nem enviado qualquer SMS/email.

`repair_orders` usa InnoDB, JSON com fotografia do pedido e cifragem dos segredos. A ficha `rmas` usa MyISAM, campos diferentes, datas sem hora e campos de valores associados à intervenção técnica. A integração mantém as duas entidades e associa-as por IDs únicos nos dois sentidos. Não reutiliza o gravador legado, que pode criar clientes e enviar notificações.

O erro fatal identificado foi reproduzido por dependência ausente e a inclusão corrigida foi testada sem biblioteca SMS. A correção está no código local: o HTTP 500 do servidor só pode ser confirmado como resolvido depois da publicação e consulta dos logs se persistir.

## Funcionamento

- Na lista, «Enviar para RMA» abre o detalhe com o formulário de transferência. No detalhe, o operador escolhe um estado inicial e uma proveniência existentes/visíveis, e pode completar marca, modelo, IMEI e número de série. Não são inventados identificadores de equipamento nem IDs de estados.
- Criar/associar exige POST, CSRF, versão atual e permissões existentes de Produtos e Encomendas (8 e 9). A segunda submissão devolve a mesma RMA.
- A origem continua a ser REP-ID; a ficha técnica recebe o tracking habitual DB000001. Existem ligações nas duas direções. O BO aplica as suas permissões em cada destino.
- Transferir não significa que o equipamento já tenha chegado. Altera apenas «Estado reparação» para «Enviado para RMA». «Estado produto» conserva o valor indicado pelo operador.
- Os estados RRE e os estados técnicos RMA são independentes. Alterar uma ficha RMA não altera automaticamente o estado RRE, nem marca pagamentos/recolhas como executados. O operador atualiza os estados de acompanhamento no pedido.
- Pedidos terminados/cancelados e importações antigas incompletas não permitem nova transferência. Registos anteriores à migração ficam com «Não registado (anterior)» nos novos estados; novos pedidos começam em «Recebido» / «Aguarda recolha».
- Histórico separado regista operador, UTC, estados anteriores/novos e a associação. A lista permite filtrar os dois novos estados.
- Não existe envio automático de emails/SMS, criação de conta de cliente, alteração de stock ou faturação na transferência. Mailer, configuração da cifragem e WhatsApp continuam fora deste desenvolvimento.

## Mapeamento

| Pedido RRE | Ficha RMA |
| --- | --- |
| Nome, email e telefone | `cliente_nome`, `cliente_username`, `cliente_telefone1` |
| Nome/empresa e NIF de faturação | `cliente_empresa`, `cliente_nif` |
| Morada/CP de recolha | `cliente_morada`, `cliente_cp` |
| Referência e dados de equipamento revistos pelo operador | `m_referencia`, `m_marca`, `m_modelo`, `m_imei`, `m_nserie` |
| Reparação selecionada + informação adicional sobre a avaria | `m_avaria` |
| Ambas as moradas completas, localidades, observações, período, peça, prazo, garantia, estimativa e opção de acesso | `m_observacoes`, com identificação do pedido de origem |
| Estimativa da reparação com IVA, sem portes | `preorcamento`; não são criadas linhas de trabalho executado |
| Pagamento / total de trabalho executado | Não inferidos: `m_pago=0`, `m_valor=0`, `total=0` |
| Código/padrão cifrado | Permanece exclusivamente no pedido; não copiado para campos legados |

As colunas da morada/CP são alargadas e a tabela RMA passa para utf8mb4, para aceitar os limites/Unicode já permitidos pelo RRE. Mantém-se MyISAM e os IDs/registos existentes. A ficha RMA conserva marcas/modelos transferidos mesmo que não constem das listas auxiliares. Os campos transferidos são escapados ao apresentar na lista e no resumo RMA, sem modificar o texto guardado. Antes de gravar alterações no formulário legado, o operador deve completar os campos que esse formulário já exige (por exemplo, identificação do equipamento).

## Recuperação de falhas

O bloqueio InnoDB do pedido e o índice único `rmas.repair_order_id` evitam duplicados. Como MyISAM não participa no rollback, uma falha depois do INSERT pode deixar uma ficha ainda não ligada no pedido. Uma nova tentativa reutiliza essa ficha pelo ID da origem e completa a ligação, sem recriar a RMA. Uma ligação existente mas inconsistente é bloqueada para verificação. A estrutura não cria foreign keys para tabelas MyISAM.

## Instalação

Aplicar **uma vez** o SQL abaixo na mesma base do site, com cópia de segurança atual. O export disponibilizado é apenas estrutura e não substitui um backup dos dados. A conversão de charset reconstrói `rmas`: executar numa janela de manutenção, sem gravações concorrentes. A suspensão de SQL mode é apenas desta sessão de migração e permite preservar os defaults/datas zero do legado. Repõe-se o modo no fim; não se altera a configuração do servidor.

Não importar o dump completo: contém DROP TABLE. Não são necessárias alterações no header ou nos quatro painéis públicos. Sem migração, o BO continua a disponibilizar o fluxo anterior e apresenta um aviso; a transferência só fica disponível com a estrutura nova.

```sql
SET @rre_previous_sql_mode = @@SESSION.sql_mode;
SET SESSION sql_mode = '';

ALTER TABLE rmas
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY cliente_morada VARCHAR(1000) NULL,
  MODIFY cliente_cp VARCHAR(30) NULL,
  ADD repair_order_id BIGINT UNSIGNED NULL,
  ADD UNIQUE KEY uq_rmas_repair_order (repair_order_id);

ALTER TABLE repair_orders
  ADD repair_status VARCHAR(32) NOT NULL DEFAULT 'not_recorded',
  ADD product_status VARCHAR(32) NOT NULL DEFAULT 'not_recorded',
  ADD rma_id INT(9) NULL,
  ADD rma_sent_at DATETIME NULL,
  ADD rma_sent_by INT UNSIGNED NULL,
  ADD UNIQUE KEY uq_repair_orders_rma (rma_id),
  ADD KEY idx_repair_orders_workflow (repair_status, product_status, id);

CREATE TABLE IF NOT EXISTS repair_order_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  repair_order_id BIGINT UNSIGNED NOT NULL,
  event_type VARCHAR(32) NOT NULL,
  actor_id INT UNSIGNED NOT NULL,
  before_json MEDIUMTEXT NOT NULL,
  after_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_repair_order_events (repair_order_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET SESSION sql_mode = @rre_previous_sql_mode;
```

Se uma instrução falhar, parar e verificar a estrutura antes de repetir ALTERs; não são idempotentes. Repor o SQL mode da sessão mesmo se interrompida. Publicar todos os ficheiros de execução indicados no relatório; documentação e testes são locais.

## Validação

Testes offline: suites anteriores de pedidos, preços/portes, metadados e emails simulados; nova suite `tests/repair_order_workflow.php` com SQLite em memória, validação do contrato contra o export fornecido, estados independentes, filtros, versão concorrente, dupla submissão, recuperação de ficha órfã, preservação de valores/Unicode/segredos e renderização das vistas reais. Foram ainda testadas marcas/modelos ausentes das listas auxiliares e tentativas de injeção HTML no resumo legado. Lint de 474 ficheiros PHP próprios de `rmas/`, sem erros. Ensaio da interface real em Firefox headless a 1440 e 390 px: quatro separadores funcionais, um painel visível, campos de estados legíveis e sem overflow horizontal. Não se utilizam credenciais, pedidos reais ou notificações.

Depois de publicar/migrar: abrir a lista RMA, criar um pedido controlado, alterar e reabrir ambos os estados, transferir após escolher estado/proveniência, verificar os dados na ficha técnica e as ligações de retorno. Repetir a ação e confirmar que não aparece outra RMA. Confirmar que a estimativa não aparece como pagamento/trabalho realizado. A validação de MyISAM e das permissões reais do servidor depende deste ensaio no ambiente de desenvolvimento; os testes offline não o substituem.

Há outras áreas legadas que não foram reescritas: filtros/pesquisas montados por concatenação, ações antigas via GET, alterações de schema durante abertura de páginas e compatibilidade de consultas históricas com SQL modes mais restritos. O patch corrige a dependência fatal identificada e acrescenta o percurso pedido; não certifica uma auditoria de segurança completa de todas as dependências e endpoints antigos.
