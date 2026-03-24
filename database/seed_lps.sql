-- =============================================================
-- SEED: Landing Pages – PetShop e Clínica Médica
-- =============================================================

-- -------------------------------------------------------------
-- 1. PETSHOP
-- -------------------------------------------------------------
INSERT INTO landing_pages (
    slug, active,
    meta_title, meta_description,
    hero_badge, hero_title, hero_title_gradient, hero_subtitle,
    hero_btn_primary_text, hero_btn_primary_url,
    hero_btn_secondary_text, hero_btn_secondary_url,
    hero_stat_1_value, hero_stat_1_label,
    hero_stat_2_value, hero_stat_2_label,
    hero_stat_3_value, hero_stat_3_label,
    how_badge, how_title, how_title_gradient, how_subtitle,
    features_badge, features_title, features_title_gradient, features_subtitle,
    about_badge, about_title, about_title_gradient,
    about_desc_1, about_desc_2, about_diff_title,
    faq_badge, faq_title, faq_title_gradient, faq_subtitle,
    cta_badge, cta_title, cta_title_gradient,
    cta_description, cta_trust_text,
    cta_btn_primary_text, cta_btn_primary_url,
    cta_btn_secondary_text, cta_btn_secondary_url
) VALUES (
    'petshop', 1,
    'Atendus para PetShops – Automatize o Atendimento do seu Pet Shop',
    'Agende banho, tosa e consultas veterinárias pelo WhatsApp 24h por dia com inteligência artificial.',
    'Automatize seu PetShop',
    'Seu PetShop atendendo', 'Automaticamente no WhatsApp',
    'Agende banho, tosa e consultas veterinárias sem esforço. Nosso assistente inteligente cuida dos seus clientes 24h por dia, 7 dias por semana.',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Falar com Especialista', 'https://wa.me/5551936181848',
    '24/7', 'Disponível',
    '3min', 'Tempo Médio de Resposta',
    'Zero', 'Chamadas Perdidas',
    'Como Funciona', 'Configure em', '4 Passos Simples',
    'Sem conhecimento técnico. Em minutos seu PetShop já está automatizando agendamentos e dúvidas pelo WhatsApp.',
    'Recursos', 'Tudo que seu PetShop', 'precisa para crescer',
    'Ferramentas pensadas para petshops que querem atender mais sem contratar mais.',
    'Quem Somos', 'Tecnologia feita para', 'quem ama animais',
    'Desenvolvemos soluções de atendimento inteligente especialmente para PetShops, entendendo as necessidades únicas do setor pet.',
    'Acreditamos que donos de pets merecem atendimento ágil e carinhoso — e seu negócio merece crescer sem sobrecarregar sua equipe.',
    'Nossos Diferenciais',
    'FAQ', 'Dúvidas', 'Frequentes',
    'Tire suas principais dúvidas sobre como o Atendus funciona para PetShops.',
    'Comece Agora', 'Seu PetShop pronto para', 'atender 24h por dia',
    'Junte-se a PetShops que já automatizaram seus agendamentos e nunca mais perderam um cliente por falta de resposta.',
    '✓ Configuração rápida   ✓ Sem fidelidade   ✓ Cancele quando quiser',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Falar com Especialista', 'https://wa.me/5551936181848'
);

SET @petshop_id = LAST_INSERT_ID();

INSERT INTO lp_steps (lp_id, order_num, title, description) VALUES
    (@petshop_id, 1, 'Crie sua Conta', 'Cadastre-se em menos de 1 minuto sem precisar de cartão de crédito.'),
    (@petshop_id, 2, 'Conecte seu WhatsApp', 'Escaneie o QR Code e vincule o número do seu PetShop instantaneamente.'),
    (@petshop_id, 3, 'Configure os Serviços', 'Informe os serviços, horários e preços — o assistente aprende tudo automaticamente.'),
    (@petshop_id, 4, 'Receba Agendamentos', 'Seu assistente já começa a agendar banho, tosa, consultas e responder dúvidas.');

INSERT INTO lp_features (lp_id, order_num, icon_name, title, description) VALUES
    (@petshop_id, 1, 'bot',       'Agendamento Automático',      'Clientes agendam banho, tosa e consultas diretamente pelo WhatsApp sem precisar ligar.'),
    (@petshop_id, 2, 'clock',     'Atendimento 24/7',             'Seu PetShop nunca fecha para o WhatsApp. Responde de madrugada, fins de semana e feriados.'),
    (@petshop_id, 3, 'messages',  'Confirmação e Lembretes',      'Envio automático de confirmação de agendamento e lembretes para reduzir faltas.'),
    (@petshop_id, 4, 'lightning', 'Respostas Instantâneas',       'Dúvidas sobre preços, horários e serviços respondidas em segundos.'),
    (@petshop_id, 5, 'users',     'Transferência para Atendente', 'Quando necessário, redireciona a conversa para um humano com todo o histórico preservado.'),
    (@petshop_id, 6, 'chart',     'Relatórios de Agendamentos',   'Visualize volume de atendimentos, serviços mais solicitados e horários de pico.'),
    (@petshop_id, 7, 'shield',    'Dados Protegidos',             'Informações dos clientes e pets armazenadas com segurança, em conformidade com a LGPD.'),
    (@petshop_id, 8, 'star',      'Avaliação Pós-Atendimento',    'Colete feedback dos tutores automaticamente após cada serviço realizado.');

INSERT INTO lp_about_values (lp_id, order_num, icon_name, title, description) VALUES
    (@petshop_id, 1, 'target', 'Missão',  'Simplificar a gestão e o atendimento de PetShops'),
    (@petshop_id, 2, 'heart',  'Valores', 'Amor aos animais e respeito aos tutores'),
    (@petshop_id, 3, 'bulb',   'Visão',   'Ser o assistente digital preferido do setor pet');

INSERT INTO lp_differentials (lp_id, order_num, icon, label) VALUES
    (@petshop_id, 1, '🐾', 'Foco no Setor Pet'),
    (@petshop_id, 2, '📅', 'Agendamento Inteligente'),
    (@petshop_id, 3, '🔔', 'Lembretes Automáticos'),
    (@petshop_id, 4, '🤝', 'Suporte Dedicado');

INSERT INTO lp_faq_items (lp_id, order_num, question, answer) VALUES
    (@petshop_id, 1, 'O assistente consegue agendar banho e tosa automaticamente?',
     'Sim! O assistente verifica disponibilidade, confirma o agendamento e envia lembretes automaticamente para o tutor.'),
    (@petshop_id, 2, 'Preciso de sistema de gestão integrado?',
     'Não é obrigatório. O assistente funciona de forma independente, mas também pode ser integrado com sistemas de gestão via API.'),
    (@petshop_id, 3, 'E se o cliente tiver uma emergência veterinária?',
     'O assistente identifica urgências e redireciona imediatamente para um atendente humano ou informa o contato de emergência cadastrado.'),
    (@petshop_id, 4, 'Posso personalizar as respostas com o nome do meu PetShop?',
     'Sim! Todo o conteúdo é personalizado com o nome, serviços, preços e tom de voz da sua empresa.'),
    (@petshop_id, 5, 'Funciona para mais de um número de WhatsApp?',
     'Sim, planos empresariais suportam múltiplos números, ideal para redes de PetShops com várias unidades.'),
    (@petshop_id, 6, 'Existe período de teste gratuito?',
     'Sim! Oferecemos um período de teste gratuito para você ver o assistente funcionando antes de assinar qualquer plano.');


-- -------------------------------------------------------------
-- 2. CLÍNICA MÉDICA
-- -------------------------------------------------------------
INSERT INTO landing_pages (
    slug, active,
    meta_title, meta_description,
    hero_badge, hero_title, hero_title_gradient, hero_subtitle,
    hero_btn_primary_text, hero_btn_primary_url,
    hero_btn_secondary_text, hero_btn_secondary_url,
    hero_stat_1_value, hero_stat_1_label,
    hero_stat_2_value, hero_stat_2_label,
    hero_stat_3_value, hero_stat_3_label,
    how_badge, how_title, how_title_gradient, how_subtitle,
    features_badge, features_title, features_title_gradient, features_subtitle,
    about_badge, about_title, about_title_gradient,
    about_desc_1, about_desc_2, about_diff_title,
    faq_badge, faq_title, faq_title_gradient, faq_subtitle,
    cta_badge, cta_title, cta_title_gradient,
    cta_description, cta_trust_text,
    cta_btn_primary_text, cta_btn_primary_url,
    cta_btn_secondary_text, cta_btn_secondary_url
) VALUES (
    'clinica-medica', 1,
    'Atendus para Clínicas Médicas – Automatize o Agendamento de Consultas',
    'Agende consultas, envie lembretes e faça triagem de pacientes pelo WhatsApp com inteligência artificial.',
    'Modernize sua Clínica',
    'Sua Clínica agendando consultas', 'com Inteligência Artificial',
    'Reduza filas telefônicas, automatize confirmações e ofereça uma experiência moderna aos seus pacientes pelo WhatsApp.',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Falar com Especialista', 'https://wa.me/5551936181848',
    '24/7', 'Agendamentos',
    '60%', 'Menos Faltas',
    'Zero', 'Fila Telefônica',
    'Como Funciona', 'Automatize em', '4 Etapas Simples',
    'Sem complicação técnica. Em poucos minutos sua clínica começa a receber e confirmar agendamentos automaticamente.',
    'Recursos', 'Tudo que sua Clínica', 'precisa para atender melhor',
    'Soluções pensadas para clínicas que querem mais eficiência sem abrir mão do cuidado com o paciente.',
    'Quem Somos', 'Tecnologia a serviço da', 'saúde dos seus pacientes',
    'Desenvolvemos soluções de atendimento inteligente para clínicas médicas, com foco em humanização e eficiência operacional.',
    'Acreditamos que pacientes merecem ser atendidos com agilidade e atenção — e que sua equipe merece focar no que realmente importa: a saúde das pessoas.',
    'Nossos Diferenciais',
    'FAQ', 'Dúvidas', 'Frequentes',
    'Tire suas principais dúvidas sobre o uso do Atendus em clínicas médicas.',
    'Comece Agora', 'Transforme o atendimento', 'da sua Clínica hoje',
    'Junte-se a clínicas que já automatizaram agendamentos e reduziram em até 60% as faltas e ligações desnecessárias.',
    '✓ Configuração rápida   ✓ Sem fidelidade   ✓ Cancele quando quiser',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Falar com Especialista', 'https://wa.me/5551936181848'
);

SET @clinica_id = LAST_INSERT_ID();

INSERT INTO lp_steps (lp_id, order_num, title, description) VALUES
    (@clinica_id, 1, 'Crie sua Conta', 'Cadastro simples em menos de 1 minuto, sem necessidade de cartão de crédito.'),
    (@clinica_id, 2, 'Conecte o WhatsApp da Clínica', 'Escaneie o QR Code e vincule o número oficial da sua clínica.'),
    (@clinica_id, 3, 'Configure Especialidades e Horários', 'Informe médicos, especialidades, horários disponíveis e convênios aceitos.'),
    (@clinica_id, 4, 'Comece a Agendar', 'Pacientes já podem agendar, confirmar e cancelar consultas diretamente pelo WhatsApp.');

INSERT INTO lp_features (lp_id, order_num, icon_name, title, description) VALUES
    (@clinica_id, 1, 'bot',       'Agendamento Inteligente',        'Pacientes agendam consultas por especialidade, médico e horário direto no WhatsApp, sem ligação.'),
    (@clinica_id, 2, 'clock',     'Disponível 24 horas',            'Receba agendamentos a qualquer hora, inclusive fora do horário comercial.'),
    (@clinica_id, 3, 'messages',  'Confirmação e Lembretes',        'Lembretes automáticos reduzem faltas e cancelamentos de última hora.'),
    (@clinica_id, 4, 'shield',    'Triagem Inicial',                'O assistente coleta sintomas e informações básicas antes da consulta, otimizando o tempo do médico.'),
    (@clinica_id, 5, 'users',     'Transferência para Recepção',    'Casos complexos são redirecionados para a recepcionista com todo o histórico da conversa.'),
    (@clinica_id, 6, 'chart',     'Relatórios de Ocupação',         'Visualize taxa de ocupação da agenda, especialidades mais demandadas e horários de pico.'),
    (@clinica_id, 7, 'lock',      'LGPD e Segurança de Dados',      'Dados dos pacientes protegidos com criptografia e em total conformidade com a LGPD.'),
    (@clinica_id, 8, 'star',      'Pesquisa de Satisfação',         'Envio automático de pesquisa de satisfação após cada consulta para melhorar continuamente.');

INSERT INTO lp_about_values (lp_id, order_num, icon_name, title, description) VALUES
    (@clinica_id, 1, 'target', 'Missão',  'Tornar o acesso à saúde mais ágil e humanizado'),
    (@clinica_id, 2, 'heart',  'Valores', 'Cuidado, ética e respeito ao paciente'),
    (@clinica_id, 3, 'bulb',   'Visão',   'Ser o assistente digital referência para clínicas médicas');

INSERT INTO lp_differentials (lp_id, order_num, icon, label) VALUES
    (@clinica_id, 1, '🏥', 'Foco em Saúde'),
    (@clinica_id, 2, '📋', 'Triagem Automatizada'),
    (@clinica_id, 3, '🔔', 'Lembretes de Consulta'),
    (@clinica_id, 4, '🔒', 'LGPD Compliant');

INSERT INTO lp_faq_items (lp_id, order_num, question, answer) VALUES
    (@clinica_id, 1, 'O assistente consegue verificar disponibilidade de médicos em tempo real?',
     'Sim! Integrando com seu sistema de agenda, o assistente exibe horários disponíveis por especialidade e médico em tempo real.'),
    (@clinica_id, 2, 'Como funciona a triagem automática?',
     'O assistente coleta informações como sintomas, urgência e histórico básico antes da consulta, disponibilizando o resumo para o médico.'),
    (@clinica_id, 3, 'O sistema é compatível com a LGPD?',
     'Sim. Todos os dados de pacientes são tratados em conformidade com a Lei Geral de Proteção de Dados, com criptografia e controle de acesso.'),
    (@clinica_id, 4, 'Funciona com planos de saúde e convênios?',
     'Sim! O assistente pode ser configurado para informar quais convênios são aceitos e orientar o paciente sobre a documentação necessária.'),
    (@clinica_id, 5, 'E em caso de emergência médica?',
     'O assistente identifica situações de urgência e redireciona imediatamente para a recepção ou orienta o paciente a ligar para o SAMU (192).'),
    (@clinica_id, 6, 'Posso usar em clínicas com múltiplas especialidades?',
     'Sim! O sistema suporta múltiplas especialidades, médicos e até múltiplas unidades em um único painel de controle.');
