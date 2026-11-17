<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
$key_linguas=array(
	'key_arraste' => array
						(
							'pt' => 'Arraste os seus ficheiros para a zona de upload',
							'en' => 'Drag your files to upload zone'
						),
	'key_vertarefas' => array
						(
							'pt' => 'VER TAREFAS',
							'en' => 'SEE TASKS'
						),
	'key_mensagens' => array
						(
							'pt' => 'MENSAGENS',
							'en' => 'MESSAGES'
						),
	'key_todasmensagens' => array
						(
							'pt' => 'VER TODAS AS MENSAGENS',
							'en' => 'SEE ALL MESSAGES'
						),
	'key_ativa' => array
						(
							'pt' => 'Ativa',
							'en' => 'Active'
						),
	'key_pesquisavel' => array
						(
							'pt' => 'Pesquisável',
							'en' => 'Searchable'
						),
	'key_ocultar_titulo' => array
						(
							'pt' => 'Ocultar Título',
							'en' => 'Hide title'
						),
	'key_template' => array
						(
							'pt' => 'Template',
							'en' => 'Template'
						),
	'key_criado' => array
						(
							'pt' => 'Criado',
							'en' => 'Created'
						),
	'key_ordem' => array
						(
							'pt' => 'Ordem',
							'en' => 'Order'
						),
	'key_titulo' => array
						(
							'pt' => 'Título',
							'en' => 'Title'
						),
	'key_familia' => array
						(
							'pt' => 'Família',
							'en' => 'Family'
						),
	'key_meta_title' => array
						(
							'pt' => 'Meta Title',
							'en' => 'Meta Title'
						),
	'key_meta_description' => array
						(
							'pt' => 'Meta Description',
							'en' => 'Meta Description'
						),
	'key_pagina_keywords' => array
						(
							'pt' => 'Página Keywords',
							'en' => 'Page Keywords'
						),
	'key_page_enter' => array
						(
							'pt' => 'Page Enter',
							'en' => 'Page Enter'
						),
	'key_imagem_pequena' => array
						(
							'pt' => 'Imagem pequena',
							'en' => 'Small Image'
						),
	'key_nova' => array
						(
							'pt' => 'Nova',
							'en' => 'New'
						),
	'key_video' => array
						(
							'pt' => 'Vídeo',
							'en' => 'Video'
						),
	'key_ou' => array
						(
							'pt' => 'ou',
							'en' => 'or'
						),
	'key_script' => array
						(
							'pt' => 'Script',
							'en' => 'Script'
						),
	'key_resumo' => array
						(
							'pt' => 'Resumo',
							'en' => 'Resume'
						),
	'key_maximo' => array
						(
							'pt' => 'Máximo',
							'en' => 'Max'
						),
	'key_caracteres' => array
						(
							'pt' => 'Caracteres',
							'en' => 'Character'
						),
	'key_categoria' => array
						(
							'pt' => 'Categoria',
							'en' => 'Category'
						),
	'key_noticias' => array
						(
							'pt' => 'Notícias',
							'en' => 'News'
						),
	'key_comentarios' => array
						(
							'pt' => 'Comentários',
							'en' => 'Comments'
						),
	'key_apagar' => array
						(
							'pt' => 'Apagar',
							'en' => 'Delete'
						),
	'key_alterar' => array
						(
							'pt' => 'Alterar',
							'en' => 'Change'
						),
	'key_valida' => array
						(
							'pt' => 'Válida',
							'en' => 'Valid'
						),
	'key_de' => array
						(
							'pt' => 'de',
							'en' => 'from'
						),
	'key_em' => array
						(
							'pt' => 'Em',
							'en' => 'In'
						),
	'key_destaque' => array
						(
							'pt' => 'Destaque',
							'en' => 'Highlight'
						),
	'key_no' => array
						(
							'pt' => 'No',
							'en' => 'On'
						),
	'key_topo' => array
						(
							'pt' => 'Topo',
							'en' => 'Top'
						),
	'key_permanente' => array
						(
							'pt' => 'Permanente',
							'en' => 'Permanent'
						),
	'key_ate' => array
						(
							'pt' => 'até',
							'en' => 'to'
						),
	'key_gravar_como_draft' => array
						(
							'pt' => 'Gravar como draft',
							'en' => 'Save as draft'
						),
	'key_nova_pagina' => array
						(
							'pt' => 'Nova Página',
							'en' => 'New Page'
						),
	'key_nova_pagina_privada' => array
						(
							'pt' => 'Nova Página Privada',
							'en' => 'New Private Page'
						),
	'key_backup_das_paginas' => array
						(
							'pt' => 'Backup das Páginas',
							'en' => 'Pages Backup'
						),
	'key_ver_ficheiros' => array
						(
							'pt' => 'Ver ficheiros',
							'en' => 'View Files'
						),
	'key_imprimir_paginas' => array
						(
							'pt' => 'Imprimir Páginas',
							'en' => 'Print Pages'
						),
	'key_exportar_paginas' => array
						(
							'pt' => 'Exportar Páginas',
							'en' => 'Export Pages'
						),
	'key_conversoes' => array
						(
							'pt' => 'CONVERSÕES',
							'en' => 'CONVERSIONS'
						),
	'key_converter_em_conteudo_publico' => array
						(
							'pt' => 'Converter em conteúdo público',
							'en' => 'Convert to public content'
						),
	'key_converter_em_noticia' => array
						(
							'pt' => 'Converter em Notícia',
							'en' => 'Convert to News'
						),
	'key_seo_optimization' => array
						(
							'pt' => 'SEO OPTIMIZATION',
							'en' => 'SEO OPTIMIZATION'
						),
	'key_mostrar_esconder_seo_campos' => array
						(
							'pt' => 'Mostrar/Esconder SEO Campos',
							'en' => 'Show/Hide SEO Fields'
						),
	'key_mostrar_efeitos' => array
						(
							'pt' => 'MOSTRAR EFEITOS',
							'en' => 'SHOW EFECTS'
						),
	'key_mostrar_esconder_efeitos' => array
						(
							'pt' => 'Mostrar/Esconder Efeitos',
							'en' => 'Show/Hide Effects'
						),
	'key_gravar_alteracoes' => array
						(
							'pt' => 'Gravar alterações',
							'en' => 'Save Changes'
						),
	'key_pre_visualizar_conteudo' => array
						(
							'pt' => 'Pré-visualizar conteúdo',
							'en' => 'Preview content'
						),
	'key_pesquisar' => array
						(
							'pt' => 'Pesquisar',
							'en' => 'Search'
						),
	'key_submeter_pesquisa' => array
						(
							'pt' => 'Submeter Pesquisa',
							'en' => 'Submit Search'
						),
	'key_com_os_selecionados' => array
						(
							'pt' => 'Com os selecionados',
							'en' => 'With the selected'
						),
	'key_reordenar' => array
						(
							'pt' => 'Reordenar',
							'en' => 'Reorder'
						),
	'key_mover_para_a_familia' => array
						(
							'pt' => 'Mover para a família',
							'en' => 'Move to family'
						),
	'key_visivel_on' => array
						(
							'pt' => 'Visível - (ON)',
							'en' => 'Visible - (ON)'
						),
	'key_visivel_off' => array
						(
							'pt' => 'Visível - (OFF)',
							'en' => 'Visible - (OFF)'
						),
	'key_protegido_on' => array
						(
							'pt' => 'Protegido - (ON)',
							'en' => 'Protected - (ON)'
						),
	'key_protegido_off' => array
						(
							'pt' => 'Protegido - (OFF)',
							'en' => 'Protected - (OFF)'
						),
	'key_pesquisa_on' => array
						(
							'pt' => 'Pesquisa - (ON)',
							'en' => 'Search - (ON)'
						),
	'key_pesquisa_off' => array
						(
							'pt' => 'Pesquisa - (OFF)',
							'en' => 'Search - (OFF)'
						),
	'key_destaque_on' => array
						(
							'pt' => 'Destaque - (ON)',
							'en' => 'Highlight - (ON)'
						),
	'key_destaque_off' => array
						(
							'pt' => 'Destaque - (OFF)',
							'en' => 'Highlight - (OFF)'
						),
	'key_aplicar_accao' => array
						(
							'pt' => 'Aplicar ação',
							'en' => 'Apply action'
						),
	'key_duplicar_pagina' => array
						(
							'pt' => 'Duplicar Página',
							'en' => 'Duplicate Page'
						),
	'key_apagar_pagina' => array
						(
							'pt' => 'Apagar Página',
							'en' => 'Delete Page'
						),
	'key_veja_tambem_esta_pagina_em' => array
						(
							'pt' => 'Veja também esta página em',
							'en' => 'See also this page'
						),
	'key_ou_compare_esta_pagina_com_a_versao_em' => array
						(
							'pt' => 'Ou compare esta página com a versão em',
							'en' => 'Or compare this page with version'
						),
	'key_homepage' => array
						(
							'pt' => 'Homepage',
							'en' => 'Homepage'
						),
	'key_converter' => array
						(
							'pt' => 'Converter',
							'en' => 'Convert'
						),
	'key_editar_pagina' => array
						(
							'pt' => 'Editar Página',
							'en' => 'Edit Page'
						),
	'key_pagina_invisivel' => array
						(
							'pt' => 'Página Invisível',
							'en' => 'Page Invisible'
						),
	'key_gravar_o_novo_registo' => array
						(
							'pt' => 'Gravar o Novo Registo',
							'en' => 'Save New Record'
						),
	'key_permissoes' => array
						(
							'pt' => 'Permissões',
							'en' => 'Permitions'
						),
	'key_bem_vindo' => array
						(
							'pt' => 'Bem Vindo',
							'en' => 'Welcome'
						),
	'key_ver_estatiticas' => array
						(
							'pt' => 'Ver Estatísticas',
							'en' => 'View Stats'
						),
	'key_editar_linguas' => array
						(
							'pt' => 'Editar Línguas',
							'en' => 'Edit Language'
						),
	'key_mensagens_internas' => array
						(
							'pt' => 'Mensagens Internas',
							'en' => 'Internal Messaging'
						),
	'key_logout' => array
						(
							'pt' => 'Logout',
							'en' => 'Logout'
						),
	'key_lista_de_utilizadores' => array
						(
							'pt' => 'Lista de Utilizadores',
							'en' => 'Users List'
						),
	'key_lingua' => array
						(
							'pt' => 'Língua',
							'en' => 'Language'
						),
	'key_adicionar_nova' => array
						(
							'pt' => 'Adicionar Nova',
							'en' => 'Add New'
						),
	'key_nao_tem_mensagens' => array
						(
							'pt' => 'Não tem mensagens',
							'en' => 'You have no messages'
						),
	'key_ver_todas_as_mensagens' => array
						(
							'pt' => 'VER TODAS AS MENSAGENS',
							'en' => 'SEE ALL MESSAGES'
						),
	'key_o_ficheiro_foi_aceite_com_sucesso' => array
						(
							'pt' => 'O ficheiro foi aceite com sucesso',
							'en' => 'The file was successfully accepted'
						),
	'key_erro_o_ficheiro_invalido' => array
						(
							'pt' => 'ERRO!!!! o ficheiro é inválido',
							'en' => 'ERROR!!!! the file is invalid'
						),
	'key_todos_ficheiro_foram_inseridos_actualizados_com_sucesso' => array
						(
							'pt' => 'Todos os ficheiro foram inseridos/actualizados com sucesso',
							'en' => 'All files were inserted/updated successfully'
						),
	'key_erro_a_enviar' => array
						(
							'pt' => 'Erro a enviar',
							'en' => 'Error sending'
						),
	'key_upload_foi_cancelado' => array
						(
							'pt' => 'O envio  foi Cancelado',
							'en' => 'The upload was canceled'
						),
	'key_deseja_converter_conteudos_automaticamente' => array
						(
							'pt' => 'Deseja converter os conteúdos automaticamente',
							'en' => 'Want to convert the contents automatically'
						),
	'key_ver_tarefas' => array
						(
							'pt' => 'VER TAREFAS',
							'en' => 'VIEW TASKS'
						),
	'key_criar_novo_banner' => array
						(
							'pt' => 'Criar Novo Banner',
							'en' => 'Create New Banner'
						),
	'key_inserir_elemento_em_banner' => array
						(
							'pt' => 'Inserir elemento em banner',
							'en' => 'Insert element banner'
						),
	'key_inserir' => array
						(
							'pt' => 'Inserir',
							'en' => 'Insert'
						),
	'key_grupo' => array
						(
							'pt' => 'Grupo',
							'en' => 'Group'
						),
	'key_nome' => array
						(
							'pt' => 'Nome',
							'en' => 'Name'
						),
	'key_url' => array
						(
							'pt' => 'Url',
							'en' => 'Url'
						),
	'key_imagem_swf' => array
						(
							'pt' => 'Imagem ou SWF',
							'en' => 'Image or SWF'
						),
	'key_descricao' => array
						(
							'pt' => 'Descrição',
							'en' => 'Description'
						),
	'key_configuracoes' => array
						(
							'pt' => 'Configurações',
							'en' => 'Settings'
						),
	'key_valido' => array
						(
							'pt' => 'Válido',
							'en' => 'Valid'
						),
	'key_ativo' => array
						(
							'pt' => 'Ativo',
							'en' => 'Active'
						),
	'key_banner_permanente' => array
						(
							'pt' => 'Banner Permanente',
							'en' => 'Permanent Banner'
						),
	'key_peso' => array
						(
							'pt' => 'Peso',
							'en' => 'Weight'
						),
	'key_n_impressoes' => array
						(
							'pt' => 'N. impressões',
							'en' => 'N. prints'
						),
	'key_total_de_impressoes' => array
						(
							'pt' => 'Total de impressões',
							'en' => 'Total prints'
						),
	'key_n_cliques' => array
						(
							'pt' => 'N. cliques',
							'en' => 'N. clicks'
						),
	'key_total_de_cliques' => array
						(
							'pt' => 'Total de cliques',
							'en' => 'Total of clicks'
						),
	'key_cancelar' => array
						(
							'pt' => 'Cancelar',
							'en' => 'Cancel'
						),
	'key_efeito' => array
						(
							'pt' => 'Efeito',
							'en' => 'Efect'
						),
	'key_dimensoes' => array
						(
							'pt' => 'Dimensões',
							'en' => 'Dimensions'
						),
	'key_largura' => array
						(
							'pt' => 'Largura',
							'en' => 'Width'
						),
	'key_altura' => array
						(
							'pt' => 'Altura',
							'en' => 'Height'
						),
	'key_or' => array
						(
							'pt' => 'ou',
							'en' => 'or'
						),
	'key_cor_de_fundo' => array
						(
							'pt' => 'Cor de fundo',
							'en' => 'Background Color'
						),
	'key_criar_novo_grupo' => array
						(
							'pt' => 'Criar Novo Grupo',
							'en' => 'Create New Group'
						),
	'key_inserir_novo_elemento' => array
						(
							'pt' => 'Inserir Novo Elemento',
							'en' => 'Insert New Element'
						),
	'key_escolher' => array
						(
							'pt' => 'Escolher',
							'en' => 'Choose'
						),
	'key_menu_visivel' => array
						(
							'pt' => 'Menu Visível',
							'en' => 'Visible Menu'
						),
	'key_menu_pai' => array
						(
							'pt' => 'Menu Pai',
							'en' => 'Parent Menu'
						),
	'key_descreva_o_link' => array
						(
							'pt' => 'Descreva o link',
							'en' => 'Describe the link'
						),
	'key_nome_que_deve_aparecer_no_site' => array
						(
							'pt' => 'Nome que deve aparecer no site',
							'en' => 'Name that should appear on the site'
						),
	'key_procurar' => array
						(
							'pt' => 'Procurar',
							'en' => 'Search'
						),
	'key_usar_imagem' => array
						(
							'pt' => 'Usar Imagem',
							'en' => 'Use Image'
						),
	'key_usar_icon' => array
						(
							'pt' => 'Usar Icon',
							'en' => 'Use Icon'
						),
	'key_nenhuma' => array
						(
							'pt' => 'Nenhuma',
							'en' => 'None'
						),
	'key_criar_novo_link_de_menu' => array
						(
							'pt' => 'Criar Novo Link de Menu',
							'en' => 'Create New Menu Link'
						),
	'key_criar_novo_sub_link_de_menu' => array
						(
							'pt' => 'Criar Novo Sub-link de Menu',
							'en' => 'Create New Sub-Menu link'
						),
	'key_gravar_novo_registo' => array
						(
							'pt' => 'Gravar Novo Registo',
							'en' => 'Save New Record'
						),
	'key_ver_mais_paginas' => array
						(
							'pt' => 'Ver mais páginas',
							'en' => 'See more pages'
						),
	'key_com_os_seleccionados' => array
						(
							'pt' => 'Com os selecionados',
							'en' => 'With the selected'
						),
	'key_tornar_visivel' => array
						(
							'pt' => 'Tornar visível',
							'en' => 'Become visible'
						),
	'key_tornar_invisivel' => array
						(
							'pt' => 'Tornar invisível',
							'en' => 'Become invisible'
						),
	'key_aplicar_acao' => array
						(
							'pt' => 'Aplicar Ação',
							'en' => 'Apply Action'
						),
	'key_nome_da_template' => array
						(
							'pt' => 'Nome da Template',
							'en' => 'Name of Template'
						),
	'key_resolucao' => array
						(
							'pt' => 'Resolução',
							'en' => 'Resolution'
						),
	'key_margem_no_topo' => array
						(
							'pt' => 'Margem no Topo',
							'en' => 'Margin on Top'
						),
	'key_em_pixels' => array
						(
							'pt' => 'em pixels',
							'en' => 'in pixels'
						),
	'key_alinhado_a_esquerda' => array
						(
							'pt' => 'Alinhado à Esquerda',
							'en' => 'Aligned to the left'
						),
	'key_alinhado_ao_centro' => array
						(
							'pt' => 'Alinhado ao Centro',
							'en' => 'Aligned to the Center'
						),
	'key_conteudos_dentro_do_site' => array
						(
							'pt' => 'Conteúdos Dentro do Site',
							'en' => 'Within Site Content'
						),
	'key_alinhados_a_esquerda' => array
						(
							'pt' => 'Alinhados à Esquerda',
							'en' => 'Aligned Left'
						),
	'key_alinhados_ao_centro' => array
						(
							'pt' => 'Alinhados ao Centro',
							'en' => 'Aligned to the Center'
						),
	'key_aplicar_uma_cor_de_fundo' => array
						(
							'pt' => 'Aplicar uma Cor de Fundo',
							'en' => 'Apply a Background Color'
						),
	'key_sim' => array
						(
							'pt' => 'SIM',
							'en' => 'YES'
						),
	'key_efeito_pretendido' => array
						(
							'pt' => 'Efeito Pretendido',
							'en' => 'Intended Effect'
						),
	'key_efeito_imagem' => array
						(
							'pt' => 'Repetir Imagem',
							'en' => 'Image Repeat'
						),
	'key_repetir_imagem_na_horizontal' => array
						(
							'pt' => 'Repetir Imagem na Horizontal',
							'en' => 'Image Repeat on Horizontal'
						),
	'key_repetir_imagem_na_vertical' => array
						(
							'pt' => 'Repetir Imagem na Vertical',
							'en' => 'Image Repeat Vertically'
						),
	'key_imagem_alinhada_ao_centro_topo' => array
						(
							'pt' => 'Imagem Alinhada ao Centro/Topo',
							'en' => 'Image Aligned Center/Top'
						),
	'key_activar_template' => array
						(
							'pt' => 'Ativar Template',
							'en' => 'Enable Template'
						),
	'key_topo_generico' => array
						(
							'pt' => 'Topo Genérico',
							'en' => 'Top Generic'
						),
	'key_rodape_generico' => array
						(
							'pt' => 'Rodapé Genérico',
							'en' => 'Footer Generic'
						),
	'key_criar_novo_template' => array
						(
							'pt' => 'Criar Nova Template',
							'en' => 'Create New Template'
						),
	'key_duplicar_ambiente' => array
						(
							'pt' => 'Duplicar Ambiente',
							'en' => 'Duplicate Environment'
						),
	'key_apagar_ambiente' => array
						(
							'pt' => 'Apagar Ambiente',
							'en' => 'Delete Environment'
						),
	'key_mover_para_o_grupo' => array
						(
							'pt' => 'Mover para o Grupo',
							'en' => 'Move to Group'
						),
	'key_exportar_listagem' => array
						(
							'pt' => 'Exportar Listagem',
							'en' => 'Export Listing'
						),
	'key_username' => array
						(
							'pt' => 'Username',
							'en' => 'Username'
						),
	'key_password' => array
						(
							'pt' => 'Password',
							'en' => 'Password'
						),
	'key_se_colocar_uma_nova_pass' => array
						(
							'pt' => 'se colocar uma nova password a antiga será eliminada',
							'en' => 'if you enter a new password, the old one will be eliminated'
						),
	'key_nif' => array
						(
							'pt' => 'NIF',
							'en' => 'NIF'
						),
	'key_criar_novo_cliente' => array
						(
							'pt' => 'Criar Novo Cliente',
							'en' => 'Create New Customer'
						),
	'key_editar_email' => array
						(
							'pt' => 'Editar Email',
							'en' => 'Edit Email'
						),
	'key_apagar_email' => array
						(
							'pt' => 'Apagar Email',
							'en' => 'Delete Email'
						),
	'key_criar_novo_email' => array
						(
							'pt' => 'Criar Novo Email',
							'en' => 'Create New Email'
						),
	'key_email' => array
						(
							'pt' => 'Email',
							'en' => 'Email'
						),
	'key_nova_noticia' => array
						(
							'pt' => 'Nova Notícia',
							'en' => 'New Article'
						),
	'key_novo_evento' => array
						(
							'pt' => 'Novo Evento',
							'en' => 'New Event'
						),
	'key_duplicar_este_registo' => array
						(
							'pt' => 'Duplicar este Registo',
							'en' => 'Duplicate'
						),
	'key_sem_comportamento' => array
						(
							'pt' => 'Sem Comportamento',
							'en' => 'Without Behavior'
						),
	'key_aleatorio_ao_abrir_a_pagina' => array
						(
							'pt' => 'Aleatório ao abrir a página',
							'en' => 'Random opening page'
						),
	'key_a_rolar_automaticamente' => array
						(
							'pt' => 'A rolar automaticamente',
							'en' => 'Automatic rolling'
						),
	'key_fadein_slide_descritivo_topo' => array
						(
							'pt' => 'FadeIn, slide descritivo topo',
							'en' => 'Fade-in, slide descriptive top'
						),
	'key_efeito_3d' => array
						(
							'pt' => 'Efeito 3D',
							'en' => 'Effect 3D'
						),
	'key_galeria' => array
						(
							'pt' => 'Galeria',
							'en' => 'Gallery'
						),
	'key_slide_pro' => array
						(
							'pt' => 'Slide pro',
							'en' => 'Slide pro'
						),
	'key_estilo_portfolio_com_zoom' => array
						(
							'pt' => 'Estilo portfolio com zoom',
							'en' => 'Portfolio with zoom'
						),
	'key_estilo_portfolio_sem_zoom' => array
						(
							'pt' => 'Estilo portfólio sem zoom',
							'en' => 'Portfolio without zoom'
						),
	'key_fadein_selecaono_topo' => array
						(
							'pt' => 'FadeIn, Seleção no topo',
							'en' => 'Fade-in, Selection on top'
						),
	'key_slide_noticias' => array
						(
							'pt' => 'Slide Notícias',
							'en' => 'Slide News'
						),
	'key_ticker' => array
						(
							'pt' => 'Ticker',
							'en' => 'Ticker'
						),
	'key_calendario' => array
						(
							'pt' => 'Calendário',
							'en' => 'Calendar'
						),
	'key_fadein_slide_descritivo_direita' => array
						(
							'pt' => 'FadeIn, slide descritivo direita',
							'en' => 'Fade-in, descriptive slide right'
						),
	'key_fadein_com_moldura' => array
						(
							'pt' => 'FadeIn, com moldura',
							'en' => 'Fade-in, with frame'
						),
	'key_fadeIn_selecao_por_baixo' => array
						(
							'pt' => 'FadeIn, seleção por baixo',
							'en' => 'Fade-in, selection under'
						),
	'key_anuncios_inseridos' => array
						(
							'pt' => 'anúncios inseridos',
							'en' => 'ads inserted'
						),
	'key_imagens_ficheiros' => array
						(
							'pt' => 'IMAGENS/FICHEIROS',
							'en' => 'IMAGES/FILES'
						),
	'key_caracteristicas' => array
						(
							'pt' => 'CARACTERÍSTICAS',
							'en' => 'FEATURES'
						),
	'key_referencia' => array
						(
							'pt' => 'Referência',
							'en' => 'Reference'
						),
	'key_pvp' => array
						(
							'pt' => 'PVP',
							'en' => 'Price (Gross)'
						),
	'key_preco_promocao' => array
						(
							'pt' => 'Preço Promoção',
							'en' => 'Price Promotion'
						),
	'key_ativar_promocao' => array
						(
							'pt' => 'Ativar Promoção',
							'en' => 'Enable Promotion'
						),
	'key_preco_outlet' => array
						(
							'pt' => 'Preço Outlet',
							'en' => 'Price Outlet'
						),
	'key_ativar_novidade' => array
						(
							'pt' => 'Ativar Novidade',
							'en' => 'Enable New Product'
						),
	'key_palavras_alternativas_para_a_pesquisa' => array
						(
							'pt' => 'Palavras alternativas para a pesquisa',
							'en' => 'Alternative words for search'
						),
	'key_entrega_em' => array
						(
							'pt' => 'Entrega em',
							'en' => 'Delivery'
						),
	'key_sem_stock' => array
						(
							'pt' => 'Sem Stock',
							'en' => 'No Stock'
						),
	'key_dois_a_cinco_dias' => array
						(
							'pt' => '2 a 5 dias',
							'en' => '2 to 5 days'
						),
	'key_medidas' => array
						(
							'pt' => 'Medidas',
							'en' => 'Dimensions'
						),
	'key_qtd' => array
						(
							'pt' => 'qtd',
							'en' => 'qtt'
						),
	'key_imediato' => array
						(
							'pt' => 'Imediato',
							'en' => 'Immediate'
						),
	'key_stock_disponivel_brevemente' => array
						(
							'pt' => 'Stock Disponível Brevemente',
							'en' => 'Stock Available Soon'
						),
	'key_disponivel_por_encomenda' => array
						(
							'pt' => 'Disponível por encomenda',
							'en' => 'Available to order'
						),
	'key_24_48_horas_disponivel' => array
						(
							'pt' => '24/48 horas disponível',
							'en' => '24/48 hours available'
						),
	'key_stock_esgotado' => array
						(
							'pt' => 'Stock Esgotado',
							'en' => 'Out of Stock'
						),
	'key_disponibilidade_imediata' => array
						(
							'pt' => 'Disponibilidade Imediata',
							'en' => 'Immediate Availability'
						),
	'key_activar_carrinho' => array
						(
							'pt' => 'Ativar Carrinho',
							'en' => 'Activate Cart'
						),
	'key_activar_produto' => array
						(
							'pt' => 'Ativar Produto',
							'en' => 'Activate Product'
						),
	'tem_prod_destaque_homepage' => array
						(
							'pt' => 'Colpocar o produto destacado na homepage',
							'en' => 'Put featured product on homepage '
						),
	'key_apagar_este_registo' => array
						(
							'pt' => 'Apagar este Registo',
							'en' => 'Delete this Register'
						),
	'key_pagina_visivel' => array
						(
							'pt' => 'Página Visível',
							'en' => 'Page Visible'
						),
	'key_categorias' => array
						(
							'pt' => 'Categorias',
							'en' => 'Categories'
						),
	'key_produtos' => array
						(
							'pt' => 'Produtos',
							'en' => 'Products'
						),
	'key_imagens' => array
						(
							'pt' => 'Imagens',
							'en' => 'Images'
						),
	'key_caracteriticas' => array
						(
							'pt' => 'Características',
							'en' => 'Features'
						),
	'key_tornar_categoria_visivel' => array
						(
							'pt' => 'Tornar categoria visível',
							'en' => 'Make category visible'
						),
	'key_imagem' => array
						(
							'pt' => 'Imagem',
							'en' => 'Image'
						),
	'key_categoria_de_downloads' => array
						(
							'pt' => 'Os Produtos desta categoria são downloads',
							'en' => 'Products in this category are downloads'
						),
	'key_inicio' => array
						(
							'pt' => 'Inicio',
							'en' => 'Beginning'
						),
	'key_pesquisar_por_nome' => array
						(
							'pt' => 'Pesquisar por nome',
							'en' => 'Search by name'
						),
	'key_por_ref' => array
						(
							'pt' => 'por Referencia.',
							'en' => 'by Reference'
						),
	'key_preco_custo' => array
						(
							'pt' => 'Preço Custo',
							'en' => 'Price (Net)'
						),
	'key_em_stock' => array
						(
							'pt' => 'Em Stock',
							'en' => 'In Stock'
						),
	'key_ver_em_forma_de_arvore' => array
						(
							'pt' => 'Ver em Forma de Árvore',
							'en' => 'View in Tree Mode'
						),
	'key_ver_em_lista' => array
						(
							'pt' => 'Ver em Lista',
							'en' => 'View by List Mode'
						),
	'key_criar_novo_produto' => array
						(
							'pt' => 'Criar Novo Produto',
							'en' => 'Create New Product'
						),
	'key_stock' => array
						(
							'pt' => 'Stock',
							'en' => 'Stock'
						),
	'key_estado_do_produto' => array
						(
							'pt' => 'Estado do Produto',
							'en' => 'Product Status'
						),
	'key_ativar_edicao_na_pagina' => array
						(
							'pt' => 'Ativar Edição na Página',
							'en' => 'Enable Editing Page'
						),
	'key_download' => array
						(
							'pt' => 'É um Download',
							'en' => 'Download'
						),
	'key_trailer' => array
						(
							'pt' => 'Trailer',
							'en' => 'Trailer'
						),
	'key_permite_encomendas_quando_stock_esta_a_zero' => array
						(
							'pt' => 'Permite encomendas quando não existe stock',
							'en' => 'Allows orders when there is no stock'
						),
	'key_pais' => array
						(
							'pt' => 'País',
							'en' => 'Country'
						),
	'key_abr_linguas' => array
						(
							'pt' => 'Abr. - Língua',
							'en' => 'Abbr. - Language'
						),
	'key_valor_moeda' => array
						(
							'pt' => 'Valor Moeda',
							'en' => 'Currency value'
						),
	'key_atualiza_moeda' => array
						(
							'pt' => 'Atualiza Moeda',
							'en' => 'Currency Update'
						),
	'key_automatico' => array
						(
							'pt' => 'Automático',
							'en' => 'Automatic'
						),
	'key_manual' => array
						(
							'pt' => 'Manual',
							'en' => 'Manual'
						),
	'key_nota' => array
						(
							'pt' => 'Nota',
							'en' => 'Nota'
						),
	'key_valores_com_ponto' => array
						(
							'pt' => 'Valores com ponto',
							'en' => 'Values with point'
						),
	'key_aceita_ate_4_casas_decimais' => array
						(
							'pt' => 'Aceita até 4 casas decimais',
							'en' => 'Supports up to 4 decimal places'
						),
	'key_moeda' => array
						(
							'pt' => 'Moeda',
							'en' => 'Currency'
						),
	'key_se_for_selecionado' => array
						(
							'pt' => 'Se for selecionado "Update Automático", os seguintes campos vão ser atualizados',
							'en' => 'If you select "Automatic Update", the following fields will be updated'
						),
	'key_update_automatico' => array
						(
							'pt' => 'Update Automático',
							'en' => 'Automatic Update'
						),
	'key_ultimo_update' => array
	(
		'pt' => 'Último update',
		'en' => 'Last update'
	),
	'key_valor_da_moeda' => array
						(
							'pt' => 'Valor da Moeda',
							'en' => 'Currency Value'
						),
	'key_intervalo_actualizacao_moeda' => array
						(
							'pt' => 'Intervalo',
							'en' => 'Interval'
						),
	'key_simbolo_da_moeda' => array
						(
							'pt' => 'Simbolo da Moeda',
							'en' => 'Currency Symbol'
						),
	'key_configurar_nova_moeda' => array
						(
							'pt' => 'Configurar Nova Moeda',
							'en' => 'Set New Currency'
						),
	'key_utilizador_inexistente' => array
						(
							'pt' => 'Utilizador errado ou inexistente!',
							'en' => 'User nonexistent or wrong!'
						),
	'key_password_errada' => array
						(
							'pt' => 'Password Errada!',
							'en' => 'Wrong Passord!'
						),
	'key_aparecer_em' => array
						(
							'pt' => 'Aparecer em',
							'en' => 'Displayed in'
						),
	'key_criar_nova' => array
						(
							'pt' => 'Criar Nova',
							'en' => 'Create New'
						),
	'key_atualizar_nomes' => array
						(
							'pt' => 'Atualizar Nomes',
							'en' => 'Update Names'
						),
	'key_slide_noticias_login' => array
						(
							'pt' => 'Slide Notícias Login',
							'en' => 'Slide News Login'
						),
	'key_novo_blog' => array
						(
							'pt' => 'Novo Blog Post',
							'en' => 'New Blog Post'
						),
	'key_paginas_publicas' => array
						(
							'pt' => 'Páginas Públicas',
							'en' => 'Public Pages'
						),
	'key_paginas_privadas' => array
						(
							'pt' => 'Páginas Privadas',
							'en' => 'Private Pages'
						),
	'key_eventos' => array
						(
							'pt' => 'Eventos',
							'en' => 'Events'
						),
	'key_blog' => array
						(
							'pt' => 'Blog',
							'en' => 'Blog'
						),
	'key_listar' => array
						(
							'pt' => 'Listar',
							'en' => 'List'
						),
	'key_txt_title' => array
						(
							'pt' => '
								A tag "title" em combinação com a "description" e meta tag "keyword" serve para influenciar os resultados.
								Todos os motores de busca como Google, Yahoo e AltaVista mostram a Tag "title". E com esta tag "title" pode influenciar seus resultados de pesquisa dos motores de busca.
								Tente sempre chegar a um título relevante e atraente para todas as páginas do seu site.
								O título é a frase, que se vê em primeiro num resultado de pesquisa.
								O título nao deve ter mais que 100 caracteres.
								Deve encarar o título como uma mensagem curta comercial que tem para atrair o visitante.
							',
							'en' => '
								The tag "title" in combination with the "description" meta tag and "keyword" is used to influence the results.
								All search engines like Google, Yahoo and AltaVista show the tag "title". And with this tag "title" it can influence your search results of search engines.
								Always try to get a title relevant and attractive to all pages of your site.
								The title is the phrase that one sees first as a search result.
								The title should not be longer than 100 characters.
								You must face the title as a commercial short message that has to attract the visitor.
							'
						),
	'key_txt_description' => array
						(
							'pt' => '
								A meta tag "Description" serve para explicar ao seu visitante em apenas 2 ou 3 frases o que eles podem esperar desta página?
								Quase todos os principais motores de busca como Yahoo, Google, Fast e Altavista irão utilizar a tag "Description".
								Esta descrição pode influenciar o utilizador a clicar logo é extremamente importante que seja uma descrição bem percetível.
								Não use mais de 200 caracteres.
							',
							'en' => '
								The meta tag "Description" serves to explain to the visitor in just 2 or 3 sentences what they can expect from this page?
								Almost all major search engines like Yahoo, Google, Altavista and Fast will use the tag "Description".
								This description can influence the user to click so it is extremely important to be a good description noticeable.
								Do not use more than 200 characters.
							'
						),
	'key_txt_processos' => array
						(
							'pt' => '
								Mais de 80% de todos os processos on-line começam por uma consulta de pesquisa.
								O utilizador acede a um motor de busca e preenche determinadas palavras-chave que correspondem ao que pretende encontrar.
								É por isso que é tao importante colocar palavras-chave relevantes e corretas em cada página do site.
								Não adicione muitas palavras, a maioria dos motores de busca irá indexar apenas as primeiras 20 palavras.
								Certifique-se que as 10 palavras-chave mais importantes veem primeiro.
							',
							'en' => '
								Over 80% of all online processes begin with a search query.
								The user accesses a search engine and meets certain keywords that match what you want to find.
								That is why it is so important to put relevant keywords and correct in every page of the site.
								Do not add too many words, most search engines will index only the first 20 words.
								Make sure that the 10 most important keywords see first.
							'
						),
	'key_ultimas_15_encomndas' => array
						(
							'pt' => 'Últimas 15 encomendas',
							'en' => 'Last 15 orders'
						),
	'key_club' => array
						(
							'pt' => 'Club',
							'en' => 'Club'
						),
	'key_nova_pagina_club' => array
						(
							'pt' => 'Nova Página Club',
							'en' => 'New Club Page'
						),
	'key_box_alterar' => array
						(
							'pt' => 'ALTERAR',
							'en' => 'CHANGE'
						),
	'key_box_registo_alterado_corretamente' => array
						(
							'pt' => 'REGISTO ALTERADO CORRETAMENTE',
							'en' => 'REGISTRATION CHANGED CORRECTLY'
						),
	'key_box_registo_foi_alterado' => array
						(
							'pt' => 'O seu registo foi alterado',
							'en' => 'The record was changed'
						),
	'key_box_inserir' => array
						(
							'pt' => 'INSERIR',
							'en' => 'INSERT'
						),
	'key_box_registo_inserido_corretamente' => array
						(
							'pt' => 'REGISTO INSERIDO CORRETAMENTE',
							'en' => 'RECORD INSERTED CORRECTLY'
						),
	'key_box_registo_foi_inserido' => array
						(
							'pt' => 'O seu registo foi inserido',
							'en' => 'Your registration was inserted'
						),
	'key_box_apagar' => array
						(
							'pt' => 'APAGAR',
							'en' => 'DELETE'
						),
	'key_box_gerar' => array
						(
							'pt' => 'GERAR',
							'en' => 'GENERATE'
						),
	'key_box_registo_apagado_corretamente' => array
						(
							'pt' => 'REGISTO APAGADO CORRETAMENTE',
							'en' => 'RECORD DELETED CORRECTLY'
						),
	'key_box_gerar_ficheiro_traducoes' => array
						(
							'pt' => 'FICHEIRO TRADUÇÕES GERADO CORRETAMENTE',
							'en' => 'TRANSLATION FILE CORRECTLY GENERATED'
						),
	'key_box_registo_foi_eliminado' => array
						(
							'pt' => 'O seu registo foi eliminado',
							'en' => 'The record was removed'
						),
	'key_box_ficheiro_gerado' => array
						(
							'pt' => 'O ficheiro de traduções foi gerado',
							'en' => 'The translation file was generated'
						),
	'key_ver_comentarios' => array
						(
							'pt' => 'Ver Comentários',
							'en' => 'See Comments'
						),
	'key_nova_categoria' => array
						(
							'pt' => 'Nova Categoria',
							'en' => 'New Category'
						),
	'key_box_erro_inserir' => array
						(
							'pt' => 'ERRO AO INSERIR',
							'en' => 'ERROR INSERT'
						),
	'key_box_erro' => array
						(
							'pt' => 'ERRO',
							'en' => 'ERROR'
						),
	'key_box_prob_removido' => array
						(
							'pt' => 'Devido a problemas técnicos, Este registo não foi inserido.',
							'en' => 'Due to technical issues, this record was not inserted.'
						),
	'key_box_erro_eliminar' => array
						(
							'pt' => 'ERRO AO ELIMINAR',
							'en' => 'ERROR DELETING'
						),
	'key_box_erro_alterar' => array
						(
							'pt' => 'ERRO AO ALTERAR',
							'en' => 'ERROR CHANGING'
						),
	'key_box_prob_alterado' => array
						(
							'pt' => 'Devido a problemas técnicos, Este registo não foi alterado.',
							'en' => 'Due to technical issues, this record has not changed.'
						),
	'key_box_operacao' => array
						(
							'pt' => 'Operação Ilegal',
							'en' => 'Illegal Operation'
						),
	'key_marca' => array
						(
							'pt' => 'Marca',
							'en' => 'Brand'
						),
	'key_editar_grupo' => array
						(
							'pt' => 'Editar Grupo',
							'en' => 'Edit Group'
						),
	'key_rmas_estado' => array
						(
							'pt' => 'Estado',
							'en' => 'Status'
						),
	'key_rmas_estado0' => array
						(
							'pt' => 'Aguarda diagnóstico',
							'en' => 'Awaiting diagnostic'
						),
	'key_rmas_estado1' => array
						(
							'pt' => 'Aguarda aceitação',
							'en' => 'Awaiting acceptance'
						),
	'key_rmas_estado2' => array
						(
							'pt' => 'Aguarda reparação',
							'en' => 'Awaiting repair'
						),
	'key_rmas_estado3' => array
						(
							'pt' => 'Aguarda peça',
							'en' => 'Awaiting part'
						),
	'key_rmas_estado4' => array
						(
							'pt' => 'Pronto para entrega',
							'en' => 'Ready to deliver'
						),
	'key_rmas_estado5' => array
						(
							'pt' => 'Concluído',
							'en' => 'Completed'
						),
	'key_rmas_estado6' => array
						(
							'pt' => 'Recusado',
							'en' => 'Refused'
						),
	'key_rmas_estado7' => array
						(
							'pt' => 'Aguarda peça para despiste',
							'en' => 'Awaiting part for screening'
						),
	'key_rmas_tipoequip' => array
						(
							'pt' => 'Tipo Equipamento',
							'en' => 'Equipment Type'
						),
	'key_criar_novo_rma' => array
						(
							'pt' => 'Criar Novo RMA',
							'en' => 'Create a new RMA'
						),
	'key_dados_cliente' => array
						(
							'pt' => 'Dados Cliente',
							'en' => 'Client Data'
						),
	'key_nif' => array
						(
							'pt' => 'NIF',
							'en' => 'NIF'
						),
	'key_nome' => array
						(
							'pt' => 'Nome',
							'en' => 'Name'
						),
	'key_apelido' => array
						(
							'pt' => 'Apelido',
							'en' => 'Surname'
						),
	'key_morada' => array
						(
							'pt' => 'Morada',
							'en' => 'Address'
						),
	'key_codigo_postal' => array
						(
							'pt' => 'Código Postal',
							'en' => 'Postal Code'
						),
	'key_telefone' => array
						(
							'pt' => 'Telefone',
							'en' => 'Phone'
						),
	'key_empresa' => array
						(
							'pt' => 'Empresa',
							'en' => 'Company'
						),
	'key_dados_rma' => array
						(
							'pt' => 'Dados RMA',
							'en' => 'RMA Data'
						),
	'key_referencia' => array
						(
							'pt' => 'Referência',
							'en' => 'Reference'
						),
	'key_marca' => array
						(
							'pt' => 'Marca',
							'en' => 'Brand'
						),
	'key_modelo' => array
						(
							'pt' => 'Modelo',
							'en' => 'Model'
						),
	'key_tipo_equip' => array
						(
							'pt' => 'Tipo Equip.',
							'en' => 'Equip. Type'
						),
	'key_nr_serie' => array
						(
							'pt' => 'Nr. Serie',
							'en' => 'Serial Nb.'
						),
	'key_avaria' => array
						(
							'pt' => 'Avaria',
							'en' => 'Malfunction'
						),
	'key_estado_equip' => array
						(
							'pt' => 'Estado Equipamento',
							'en' => 'Equipment State'
						),
	'key_acessorios' => array
						(
							'pt' => 'Acessórios',
							'en' => 'Accessories'
						),
	'key_relatorio_reparacao' => array
						(
							'pt' => 'RELATORIO REPARAÇÃO',
							'en' => 'REPAIR REPORT'
						),
	'key_cliente' => array
						(
							'pt' => 'CLIENTE',
							'en' => 'CLIENT'
						),
	'key_todos' => array
						(
							'pt' => 'Todos',
							'en' => 'All'
						),
	'key_tecnico' => array
						(
							'pt' => 'Técnico',
							'en' => 'Tecnico'
						),
	'key_observacoes' => array
						(
							'pt' => 'Observações',
							'en' => 'Comments'
						),

	'key_relatorio_intervencao' => array
						(
							'pt' => 'Relatório de Intervenção',
							'en' => 'Intervention Report'
						),
	'key_reparacao_prevista' => array
						(
							'pt' => 'Relatório de Intervenção',
							'en' => 'Intervention Report'
						),
	'key_imprimir_recepcao' => array
						(
							'pt' => 'Imprime Folha Recepção',
							'en' => 'Print Reception Sheet'
						),
	'key_imprimir_orcamento' => array
						(
							'pt' => 'Imprime Folha Orçamento',
							'en' => 'Print Estimate Sheet'
						),
	'key_imprimir_reparacao' => array
						(
							'pt' => 'Imprime Relatório Final',
							'en' => 'Print Final Report'
						),
	'key_sem_encomendas' => array
						(
							'pt' => 'Não foram encontradas encomendas',
							'en' => 'No orders were found'
						),
	'key_filtros' => array
						(
							'pt' => 'Filtros',
							'en' => 'Filters'
						),
	'key_criar_novo_filtro' => array
						(
							'pt' => 'Criar Novo Filtro',
							'en' => 'Create New Filter'
						),
	'key_criar_categoria' => array
				(
					'pt' => 'Criar nova Categoria',
					'en' => 'Create new Category'
				),
				'key_voucher_create' => array
						(
							'pt' => 'Criar novo cupão',
							'en' => 'Create new voucher'
						),
	'key_voucher_back_listvouchers' => array
						(
							'pt' => 'Voltar para a lista de cupões',
							'en' => 'Back to list of vouchers'
						),
	'key_voucher_code' => array
						(
							'pt' => 'Código do cupão',
							'en' => 'Voucher Code'
						),
	'key_voucher_title' => array
						(
							'pt' => 'Título do cupão',
							'en' => 'Voucher Title'
						),
	'key_voucher_warning' => array
						(
							'pt' => 'Nota: O código do cupão será gerado automaticamente',
							'en' => 'Note: The voucher code will be generated automatically'
						),
	'key_voucher_type' => array
						(
							'pt' => 'Tipo de cupão',
							'en' => 'Voucher type'
						),
	'key_voucher_orderdiscount' => array
						(
							'pt' => 'Desconto na encomenda(%)',
							'en' => 'Order Discout(%)'
						),
	'key_voucher_date_start' => array
						(
							'pt' => 'Inicio do cupão',
							'en' => 'Voucher Start'
						),
	'key_voucher_date_end' => array
						(
							'pt' => 'Fim do cupão',
							'en' => 'Voucher Ends'
						),
	'key_voucher_date_end' => array
						(
							'pt' => 'Fim do cupão',
							'en' => 'Voucher Ends'
						),
	'key_voucher_productoffer' => array
						(
							'pt' => 'Oferta de produto',
							'en' => 'Product Offer'
						),
	'key_voucher_orderdiscount' => array
						(
							'pt' => 'Desconto na encomenda',
							'en' => 'Order discount'
						),
	'key_voucher_partnerdiscount' => array
						(
							'pt' => 'Desconto Parceiro',
							'en' => 'Partner discount'
						),
	'key_voucher_usage' => array
						(
							'pt' => 'Uso do cupão',
							'en' => 'Voucher Usage'
						),
	'key_voucher_unlimited' => array
						(
							'pt' => 'Ilimitado',
							'en' => 'Unlimited'
						),
	'key_voucher_onetimeonly' => array
						(
							'pt' => 'Apenas uma vez',
							'en' => 'One Time Only'
						),
	'key_voucher_product' => array
						(
							'pt' => 'Produto',
							'en' => 'Product'
						),
	'key_voucher_productclearbox' => array
						(
							'pt' => 'Clique aqui para limpar a caixa',
							'en' => 'Click here to empty the box'
						),
	'key_voucher_productsearch' => array
						(
							'pt' => 'Pesquisar',
							'en' => 'Search'
						),
	'key_voucher_productadd' => array
						(
							'pt' => 'Adicionar',
							'en' => 'Add'
						),
	'key_voucher_codelist' => array
						(
							'pt' => 'Codigo',
							'en' => 'Code'
						),
	'key_voucher_state' => array
						(
							'pt' => 'Estado',
							'en' => 'State'
						),
	'key_voucher_no_vouchers' => array
						(
							'pt' => 'Não existem cupões.',
							'en' => 'There aren\'t no vouchers'
						),
	'key_voucher_inactive' => array
						(
							'pt' => 'Inactivo',
							'en' => 'Inactive'
						),
	'key_voucher_inactivealreadyused' => array
						(
							'pt' => 'Inactivo - Já foi utilizado',
							'en' => 'Inactive - Already used'
						),
	'key_voucher_active' => array
						(
							'pt' => 'Activo',
							'en' => 'Active'
						),
	'key_voucher_partner' => array
						(
							'pt' => 'Parceiro',
							'en' => 'Partner'
						),
	'key_voucher_partnerprofit' => array
						(
							'pt' => 'Lucro atribuido ao parceiro(%)',
							'en' => 'Profit given to the partner(%)'
						),
	'key_definicoes_plataforma' => array
						(
							'pt' => 'Definições da Plataforma',
							'en' => 'Platform Settings'
						),
	'key_exportar_produtos_1' => array
						(
							'pt' => 'Exportar Produtos sem descrição',
							'en' => 'Export Products with no description'
						),
	'key_apagar_draft' => array
						(
							'pt' => 'Apagar Draft',
							'en' => 'Delete Draft'
						),
	'key_gravar_draft' => array
						(
							'pt' => 'Gravar Draft',
							'en' => 'Save Draft'
						),
	'key_pre_visualizar_draf' => array
						(
							'pt' => 'Pré-visualizar draft',
							'en' => 'Preview draft'
						),
	'key_sobrepor_conteudo_principal' => array
						(
							'pt' => 'Sobrepor conteúdo principal',
							'en' => 'Overlay page'
						),
	'key_voltar_a_pagina' => array
						(
							'pt' => 'Voltar a página',
							'en' => 'Back to page'
						),
	'key_apagar_este_draft' => array
						(
							'pt' => 'Apagar este Draft',
							'en' => 'Delete Draft'
						),
	'key_paypal_canceled_reversal' => array
						(
							'pt' => 'Canceled_Reversal - A reversão foi cancelada. Por exemplo, você ganhou uma disputa com o cliente, e os fundos para a transação que foi revertido foi devolvido a si.',
							'en' => 'Canceled_Reversal - A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you.'
						),
	'key_paypal_completed' => array
						(
							'pt' => 'Concluído - O pagamento foi concluído e o montante foi adicionado com êxito à sua conta.',
							'en' => 'Completed - The payment has been completed, and the funds have been added successfully to your account balance.'
						),
	'key_paypal_created' => array
						(
							'pt' => 'Criado - Um Pagamento German ELV foi feito através do Express Checkout',
							'en' => 'Created - A German ELV payment is made using Express Checkout'
						),
	'key_paypal_denied' => array
						(
							'pt' => 'Negado - O Pagamento foi negado. Isto apenas acontence se o pagamento foi previamente pendente por causa de uma das razões listadas no campo pending_reason ou no campo Fraud_Management_Filters_x.',
							'en' => 'Denied - The payment was denied. This happens only if the payment was previously pending because of one of the reasons listed for the pending_reason variable or the Fraud_Management_Filters_x variable.'
						),
	'key_paypal_expired' => array
						(
							'pt' => 'Expirado - Esta autorização expirou e não pode ser capturada.',
							'en' => 'Expired - This authorization has expired and cannot be captured.'
						),
	'key_paypal_failed' => array
						(
							'pt' => 'Falhou - O pagamento falhou. Isto apenas acontece se o pagamento foi feito da conta bancária do cliente.',
							'en' => 'Failed - The payment has failed. This happens only if the payment was made from your customer\'s bank account.'
						),
	'key_paypal_pending' => array
						(
							'pt' => 'Pendente - O pagamento está pendente. veja pending_reason para mais Informação.',
							'en' => 'Failed - The payment is pending. See pending_reason for more information.'
						),
	'key_paypal_refunded' => array
						(
							'pt' => 'Devolvido - Você devolveu o pagamento.',
							'en' => 'Refunded - You refunded the payment.'
						),
	'key_paypal_reversed' => array
						(
							'pt' => 'Invertida - Um pagamento foi invertido devido a uma cobrança retroativa ou outro tipo de inversão. Os montantes foram removidos do saldo da sua conta e foram devolvidos ao cliente. A razão pela inversão está especificada no campo ReasonCode..',
							'en' => 'Reversed - A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element..'
						),
	'key_paypal_processed' => array
						(
							'pt' => 'Processada - Um pagamento foi aceite.',
							'en' => 'Processed - A payment has been accepted.'
						),
	'key_paypal_voided' => array
						(
							'pt' => 'Anulado - Esta autorização foi anulada.',
							'en' => 'Voided - This authorization has been voided.'
						),
	'key_paypal_default' => array
						(
							'pt' => 'Não houve confirmação da paypal em que o pagamento tenha sido efetuado.',
							'en' => 'There was no confirmation from paypal that the payment has been made.'
						),
	'key_unicre_success' => array
						(
							'pt' => 'Sucesso - O pagamento foi concluído e o montante foi adicionado com êxito à sua conta.',
							'en' => 'Successs - The payment has been completed, and the funds have been added successfully to your account balance.'
						),
	'key_unicre_cancel' => array
						(
							'pt' => 'O pagamento foi cancelado.',
							'en' => 'Payment was Canceled.'
						),
	'key_unicre_denied' => array
						(
							'pt' => 'Negado - O Pagamento foi negado.',
							'en' => 'Denied - The payment was denied.'
						),
	'key_unicre_failed' => array
						(
							'pt' => 'Falhou - O pagamento falhou.',
							'en' => 'Failed - The payment has failed.'
						),
	'key_unicre_failedfraud' => array
						(
							'pt' => 'Falhou - O pagamento falhou devido a suspeita de fraude.',
							'en' => 'Failed - The payment has failed due to suspected fraud .'
						),
	'key_unicre_failedtechnical' => array
						(
							'pt' => 'Falhou - O pagamento falhou devido a um problema técnico.',
							'en' => 'Failed - The payment has failed due a technical failure.'
						),
	'key_unicre_default' => array
						(
							'pt' => 'Não houve confirmação da unicre em que o pagamento tenha sido efetuado.',
							'en' => 'There was no confirmation from unicre that the payment has been made.'
						),
	'key_apagar_este_draft' => array
						(
							'pt' => 'Apagar este Draft',
							'en' => 'Apagar este Draft'
						),
	'key_pesquisa' => array
						(
							'pt' => 'Pesquisa',
							'en' => 'Pesquisa'
						),
	'key_ativar_legendas' => array
						(
							'pt' => 'Ativar Legendas',
							'en' => 'Ativar Legendas'
						),
	'key_converter_em_conteudo_privado' => array
						(
							'pt' => 'Converter em conteúdo privado',
							'en' => 'Converter em conteúdo privado'
						),
	'key_ver_imagem' => array
						(
							'pt' => 'Ver Imagem',
							'en' => 'Ver Imagem'
						),
	'key_duplicar' => array
						(
							'pt' => 'Duplicar',
							'en' => 'Duplicate'
						),

	'key_configurar' => array
						(
							'pt' => 'Configurar',
							'en' => 'Set Up'
						),
	'key_editar_campos' => array
						(
							'pt' => 'Configurar Campos',
							'en' => 'Set Up Fields'
						),
	'key_listar_registos' => array
						(
							'pt' => 'Listar Registos',
							'en' => 'List Records'
						),
	'key_apagar_formulario' => array
						(
							'pt' => 'Apagar Formulário',
							'en' => 'Delete Form'
						),
	'key_mmostrar_popup' => array
						(
							'pt' => 'Mostrar em Popup',
							'en' => 'Show as Popup'
						),
	'key_nome_formulario' => array
						(
							'pt' => 'Nome do Formulário',
							'en' => 'Form Name'
						),
	'key_formulario_email' => array
						(
							'pt' => 'endereço de email que recebe os dados submetidos (pode indicar mais do que um endereço de correio electrónico separados por ponto e vírgula (;) e sem espaços entre eles)',
							'en' => 'email address that receives the submitted data (if you wish to send more emails, no spaces insert comma (;))'
						),
	'key_formulario_campos_mostrar' => array
						(
							'pt' => 'campos a apresentar na listagem (separados por ponto e vírgula (;))',
							'en' => 'fields to show in the list (separated by semicolons (;))'
						),
	'key_formulario_resposta_positiva' => array
						(
							'pt' => 'mensagem de resposta positiva',
							'en' => 'positive response message'
						),
	'key_formulario_resposta_negativa' => array
						(
							'pt' => 'mensagem de resposta negativa',
							'en' => 'negative response message'
						),
	'key_formulario_assunto' => array
						(
							'pt' => 'assunto',
							'en' => 'subject'
						),
	'key_formulario_corpo_mensagem' => array
						(
							'pt' => 'corpo da mensagem',
							'en' => 'body of the message'
						),
	'key_ajuda' => array
						(
							'pt' => 'Ajuda',
							'en' => 'Help'
						),
	'key_formulario_tag_nome' => array
						(
							'pt' => 'Para apresentar o nome do formulário colocar <strong>[nome]</strong>',
							'en' => 'To display the name of the form put <strong>[nome]</strong>'
						),
	'key_codigo' => array
						(
							'pt' => 'Código',
							'en' => 'code'
						),
	'key_campo' => array
						(
							'pt' => 'campo',
							'en' => 'field'
						),
	'key_formulario_layout' => array
						(
							'pt' => 'Personalizar disposição de Formulário',
							'en' => 'Customize form layout'
						),
	'key_submeter' => array
						(
							'pt' => 'Submeter',
							'en' => 'Submit'
						),
	'key_pre_visualizar' => array
						(
							'pt' => 'Pré-Visualizar',
							'en' => 'Preview'
						),
	'key_new_field' => array
						(
							'pt' => 'criar novo campo',
							'en' => 'create new field'
						),
	'key_new_form' => array
						(
							'pt' => 'criar novo formulário',
							'en' => 'create new form'
						),
	'key_configure_fields' => array
						(
							'pt' => 'configurar campos',
							'en' => 'set up fields'
						),
	'key_view_info' => array
						(
							'pt' => 'ver informação',
							'en' => 'view info'
						),
	'key_norte' => array
						(
							'pt' => 'Norte',
							'en' => 'North'
						),
	'key_leste' => array
						(
							'pt' => 'Leste',
							'en' => 'East'
						),
	'key_sul' => array
						(
							'pt' => 'Sul',
							'en' => 'South'
						),
	'key_oeste' => array
						(
							'pt' => 'Oeste',
							'en' => 'West'
						),
	'key_tiponegocio_comprar' => array
						(
							'pt' => 'Comprar',
							'en' => 'Buy'
						),
	'key_tiponegocio_arrendar' => array
						(
							'pt' => 'Arrendar',
							'en' => 'Rent'
						),
	'key_tiponegocio_nova' => array
						(
							'pt' => 'Nova construção',
							'en' => 'New construction'
						),
	'key_tiponegocio_arrendar_buy' => array
						(
							'pt' => 'Arrendamento com opção de compra',
							'en' => 'Rent with option to buy'
						)


//key_apagar_pagina


);
