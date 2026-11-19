# Fase 2 — configuração das reparações

## Aplicação

1. Fazer uma cópia de segurança da base de dados de dotbright2.
2. No phpMyAdmin, selecionar a base de dados usada por esta cópia de desenvolvimento (ver DB_name na configuração do projeto).
3. No separador SQL, colar e executar a migração da fase 2 fornecida diretamente no chat. As migrações são partilhadas no chat, sem criar ficheiros .sql no projeto.
4. Confirmar que existe a tabela repair_product_settings. O SQL apenas cria esta tabela; não atualiza produtos, famílias, preços ou pedidos existentes e pode ser executado novamente.
5. Publicar os ficheiros PHP alterados/criados, mantendo as pastas. Os ficheiros de testes e este documento não são necessários no servidor.
6. Abrir Produtos → Catálogo → REPARAÇÕES → marca/modelo → ficha → separador REPARAÇÃO. Definir prazo, tipo/texto/ícone da peça e garantia em meses; guardar a ficha.
7. Confirmar os valores na ficha pública e no resumo do pedido. Validar os emails com um pedido de teste controlado.

A migração não foi executada por esta implementação. Não existe alteração automática do esquema ao abrir páginas.

## Âmbito e valores atuais

A restrição é feita no servidor pela família REPARAÇÕES (ID 85 nesta instalação) e seus descendentes. Não basta o nome ou referência do artigo. Os produtos normais não recebem campos nem guardam/leem estas definições.

Os novos campos usam uma tabela separada, por ID de produto, para não se misturarem com os campos gerais/importadores. Ao duplicar uma ficha através do formulário do backoffice, as definições apresentadas são guardadas no novo ID. Ao mudar a família de um artigo para reparações, guardar e reabrir a ficha para aparecer o separador. Ao sair da família de reparações, configurações antigas ficam inativas.

Sem configuração: prazo sob consulta, identificação da peça pelo título e regra de garantia anterior. Garantia vazia mantém essa regra; zero apresenta 0 meses. Os tipos e prazos personalizados exigem texto. Ícones limitados aos existentes, sem HTML livre.

O prazo configurado é de reparação em loja e não de recolha/transporte RRE. É usado na ficha e nos emails; a garantia é partilhada pela ficha, resumo e emails. A confirmação de disponibilidade no WhatsApp usa o nome da peça configurado. Não há notificação automática WhatsApp nesta fase.

O header, os CSS/JS, os campos e a navegação dos quatro painéis não foram alterados. Os valores surgem nos locais existentes.

## Verificação local

Executar com PHP (short_open_tag ativado para os ficheiros legados):

- php tests/repair_request.php
- php tests/repair_product_settings.php
- php tests/repair_product_settings.php --missing-table

Os testes usam dependências em memória, sem escrever numa base de dados nem enviar emails. A validação real no backoffice requer aplicar a migração e publicar os ficheiros.
