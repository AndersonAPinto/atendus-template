-- =============================================================
-- SCHEMA: Landing Pages Dinâmicas – Atendus
-- =============================================================
-- Execute este script no seu banco MySQL para criar as
-- tabelas necessárias.  Uma view (v_landing_page_full) agrega
-- tudo em uma única query:
--   SELECT * FROM v_landing_page_full WHERE slug = 'meu-slug' AND active = 1;
-- =============================================================


-- -------------------------------------------------------------
-- 1. TABELA PRINCIPAL
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS landing_pages (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    slug    VARCHAR(100) UNIQUE NOT NULL,   -- identificador na URL  (?slug=meu-slug)
    active  TINYINT(1) DEFAULT 1,

    -- Meta / SEO
    meta_title          TEXT,
    meta_description    TEXT,
    meta_og_image       TEXT,               -- URL da imagem Open Graph

    -- ── Hero ─────────────────────────────────────────────────
    hero_badge              TEXT,           -- ex: "Automatize seu atendimento"
    hero_title              TEXT,           -- ex: "Transforme seu WhatsApp em um"
    hero_title_gradient     TEXT,           -- parte em degradê – ex: "Assistente Inteligente"
    hero_subtitle           TEXT,
    hero_btn_primary_text   TEXT,
    hero_btn_primary_url    TEXT,
    hero_btn_secondary_text TEXT,
    hero_btn_secondary_url  TEXT,
    hero_stat_1_value       TEXT,           -- ex: "24/7"
    hero_stat_1_label       TEXT,           -- ex: "Disponibilidade"
    hero_stat_2_value       TEXT,
    hero_stat_2_label       TEXT,
    hero_stat_3_value       TEXT,
    hero_stat_3_label       TEXT,
    hero_image_url          TEXT,           -- URL de imagem opcional no hero

    -- ── Como Funciona ─────────────────────────────────────────
    how_badge           VARCHAR(100) DEFAULT 'Como Funciona',
    how_title           TEXT,               -- ex: "Configure em"
    how_title_gradient  TEXT,               -- ex: "4 Passos Simples"
    how_subtitle        TEXT,

    -- ── Recursos ──────────────────────────────────────────────
    features_badge              VARCHAR(100) DEFAULT 'Recursos',
    features_title              TEXT,
    features_title_gradient     TEXT,
    features_subtitle           TEXT,

    -- ── Sobre ─────────────────────────────────────────────────
    about_badge             VARCHAR(100) DEFAULT 'Quem Somos',
    about_title             TEXT,
    about_title_gradient    TEXT,
    about_desc_1            TEXT,
    about_desc_2            TEXT,
    about_diff_title        VARCHAR(100) DEFAULT 'Nossos Diferenciais',

    -- ── FAQ ───────────────────────────────────────────────────
    faq_badge           VARCHAR(100) DEFAULT 'FAQ',
    faq_title           TEXT,
    faq_title_gradient  TEXT,
    faq_subtitle        TEXT,

    -- ── CTA Final ─────────────────────────────────────────────
    cta_badge               TEXT,
    cta_title               TEXT,
    cta_title_gradient      TEXT,
    cta_description         TEXT,
    cta_trust_text          TEXT,           -- ex: "✓ Sem fidelidade  ✓ Cancele quando quiser"
    cta_btn_primary_text    TEXT,
    cta_btn_primary_url     TEXT,
    cta_btn_secondary_text  TEXT,
    cta_btn_secondary_url   TEXT,

    created_at  DATETIME DEFAULT NOW(),
    updated_at  DATETIME DEFAULT NOW() ON UPDATE NOW()
);


-- -------------------------------------------------------------
-- 2. PASSOS  –  seção "Como Funciona"
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lp_steps (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lp_id       INT NOT NULL,
    order_num   SMALLINT NOT NULL DEFAULT 1,
    title       TEXT NOT NULL,
    description TEXT,
    UNIQUE KEY uq_steps (lp_id, order_num),
    CONSTRAINT fk_steps_lp FOREIGN KEY (lp_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);


-- -------------------------------------------------------------
-- 3. RECURSOS / FEATURES
--    icon_name: nome do ícone predefinido no app.js
--    Opções: bot | clock | lightning | shield | messages |
--            chart | users | globe | qr | settings | star |
--            heart | target | lock | phone | check
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lp_features (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lp_id       INT NOT NULL,
    order_num   SMALLINT NOT NULL DEFAULT 1,
    icon_name   VARCHAR(50) DEFAULT 'star',
    title       TEXT NOT NULL,
    description TEXT,
    UNIQUE KEY uq_features (lp_id, order_num),
    CONSTRAINT fk_features_lp FOREIGN KEY (lp_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);


-- -------------------------------------------------------------
-- 5. VALORES DA EMPRESA  –  seção "Sobre"
--    icon_name: target | heart | bulb  (ou outros do mapa)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lp_about_values (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lp_id       INT NOT NULL,
    order_num   SMALLINT NOT NULL DEFAULT 1,
    icon_name   VARCHAR(50) DEFAULT 'target',
    title       TEXT NOT NULL,
    description TEXT,
    UNIQUE KEY uq_about_values (lp_id, order_num),
    CONSTRAINT fk_about_values_lp FOREIGN KEY (lp_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);


-- -------------------------------------------------------------
-- 6. DIFERENCIAIS  –  seção "Sobre"
--    icon: emoji ou texto curto  (ex: "⚡", "🔒", "🚀", "🤝")
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lp_differentials (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lp_id       INT NOT NULL,
    order_num   SMALLINT NOT NULL DEFAULT 1,
    icon        TEXT NOT NULL,
    label       TEXT NOT NULL,
    UNIQUE KEY uq_differentials (lp_id, order_num),
    CONSTRAINT fk_differentials_lp FOREIGN KEY (lp_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);


-- -------------------------------------------------------------
-- 7. FAQ
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lp_faq_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lp_id       INT NOT NULL,
    order_num   SMALLINT NOT NULL DEFAULT 1,
    question    TEXT NOT NULL,
    answer      TEXT NOT NULL,
    UNIQUE KEY uq_faq (lp_id, order_num),
    CONSTRAINT fk_faq_lp FOREIGN KEY (lp_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);


-- -------------------------------------------------------------
-- 8. VIEW AGREGADA  –  retorna tudo em uma única query
--    Uso: SELECT * FROM v_landing_page_full
--         WHERE slug = 'meu-slug' AND active = 1;
-- -------------------------------------------------------------
CREATE OR REPLACE VIEW v_landing_page_full AS
SELECT
    lp.*,

    COALESCE(
        (SELECT JSON_ARRAYAGG(j) FROM (
            SELECT JSON_OBJECT(
                'id', s.id,
                'order_num', s.order_num,
                'title', s.title,
                'description', s.description
            ) AS j
            FROM lp_steps s WHERE s.lp_id = lp.id ORDER BY s.order_num
        ) AS _steps),
        JSON_ARRAY()
    ) AS steps,

    COALESCE(
        (SELECT JSON_ARRAYAGG(j) FROM (
            SELECT JSON_OBJECT(
                'id', f.id,
                'order_num', f.order_num,
                'icon_name', f.icon_name,
                'title', f.title,
                'description', f.description
            ) AS j
            FROM lp_features f WHERE f.lp_id = lp.id ORDER BY f.order_num
        ) AS _features),
        JSON_ARRAY()
    ) AS features_items,

    COALESCE(
        (SELECT JSON_ARRAYAGG(j) FROM (
            SELECT JSON_OBJECT(
                'id', av.id,
                'order_num', av.order_num,
                'icon_name', av.icon_name,
                'title', av.title,
                'description', av.description
            ) AS j
            FROM lp_about_values av WHERE av.lp_id = lp.id ORDER BY av.order_num
        ) AS _about_values),
        JSON_ARRAY()
    ) AS about_values,

    COALESCE(
        (SELECT JSON_ARRAYAGG(j) FROM (
            SELECT JSON_OBJECT(
                'id', d.id,
                'order_num', d.order_num,
                'icon', d.icon,
                'label', d.label
            ) AS j
            FROM lp_differentials d WHERE d.lp_id = lp.id ORDER BY d.order_num
        ) AS _differentials),
        JSON_ARRAY()
    ) AS differentials,

    COALESCE(
        (SELECT JSON_ARRAYAGG(j) FROM (
            SELECT JSON_OBJECT(
                'id', fq.id,
                'order_num', fq.order_num,
                'question', fq.question,
                'answer', fq.answer
            ) AS j
            FROM lp_faq_items fq WHERE fq.lp_id = lp.id ORDER BY fq.order_num
        ) AS _faq),
        JSON_ARRAY()
    ) AS faq_items

FROM landing_pages lp;


-- =============================================================
-- EXEMPLO DE INSERT (adapte aos seus dados)
-- =============================================================
/*
INSERT INTO landing_pages (
    slug,
    meta_title, meta_description,
    hero_badge, hero_title, hero_title_gradient, hero_subtitle,
    hero_btn_primary_text, hero_btn_primary_url,
    hero_btn_secondary_text, hero_btn_secondary_url,
    hero_stat_1_value, hero_stat_1_label,
    hero_stat_2_value, hero_stat_2_label,
    hero_stat_3_value, hero_stat_3_label,
    how_title, how_title_gradient, how_subtitle,
    features_title, features_title_gradient, features_subtitle,
    about_title, about_title_gradient,
    about_desc_1, about_desc_2,
    faq_title, faq_title_gradient, faq_subtitle,
    cta_badge, cta_title, cta_title_gradient,
    cta_description, cta_trust_text,
    cta_btn_primary_text, cta_btn_primary_url,
    cta_btn_secondary_text, cta_btn_secondary_url
) VALUES (
    'atendus',
    'Atendus – Chatbot com IA para WhatsApp', 'Automatize seu atendimento 24/7.',
    'Automatize seu atendimento',
    'Transforme seu WhatsApp em um', 'Assistente Inteligente',
    'Conecte seu WhatsApp em segundos e deixe a inteligência artificial atender seus clientes 24/7.',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Saiba Mais', 'https://wa.me/5551936181848',
    '24/7', 'Disponibilidade',
    '100%', 'Automatizado',
    'Zero', 'Fila de Espera',
    'Configure em', '4 Passos Simples',
    'Não precisa de conhecimento técnico. Em poucos minutos seu atendimento estará automatizado.',
    'Tudo que você precisa para', 'escalar seu atendimento',
    'Recursos poderosos projetados para transformar a experiência do seu cliente.',
    'Democratizando o', 'atendimento inteligente',
    'A Atendus nasceu com uma missão clara: tornar a tecnologia de IA acessível para todos os negócios.',
    'Acreditamos que todo negócio merece oferecer atendimento de excelência 24 horas por dia.',
    'Dúvidas', 'Frequentes',
    'Encontre respostas para as perguntas mais comuns.',
    'Comece Agora', 'Pronto para transformar seu', 'atendimento?',
    'Junte-se a empresas que já automatizaram seu WhatsApp. Comece gratuitamente hoje.',
    '✓ Configuração rápida   ✓ Sem fidelidade   ✓ Cancele quando quiser',
    'Experimente Grátis', 'https://app.atendus.com.br/register',
    'Saiba Mais', 'https://wa.me/5551936181848'
);

-- Pegar o id da LP que acabamos de criar
-- (substitua 1 pelo id retornado acima)

INSERT INTO lp_steps (lp_id, order_num, title, description) VALUES
    (1, 1, 'Crie sua Conta', 'Cadastre-se em menos de 1 minuto. Rápido e sem complicações.'),
    (1, 2, 'Escaneie o QR Code', 'Conecte seu WhatsApp escaneando o QR Code.'),
    (1, 3, 'Configure o Bot', 'Defina como seu assistente deve atender e quais informações fornecer.'),
    (1, 4, 'Pronto para Usar', 'Seu assistente já está ativo! Responderá automaticamente todas as mensagens.');

INSERT INTO lp_features (lp_id, order_num, icon_name, title, description) VALUES
    (1, 1, 'bot',       'IA Conversacional Avançada', 'Respostas naturais e contextuais que seus clientes nem perceberão que é um bot.'),
    (1, 2, 'clock',     'Atendimento 24/7', 'Nunca perca uma venda. Seu assistente trabalha sem pausas, feriados ou férias.'),
    (1, 3, 'lightning', 'Respostas Instantâneas', 'Tempo de resposta em segundos, aumentando a satisfação e conversão.'),
    (1, 4, 'shield',    'Segurança Total', 'Seus dados e conversas protegidos com criptografia de ponta a ponta.'),
    (1, 5, 'messages',  'Multi-Conversas', 'Atenda milhares de clientes simultaneamente sem perder qualidade.'),
    (1, 6, 'chart',     'Relatórios Detalhados', 'Acompanhe métricas como volume de atendimentos e satisfação.'),
    (1, 7, 'users',     'Transferência Humana', 'Quando necessário, transfira a conversa para um atendente real.'),
    (1, 8, 'globe',     'Múltiplos Idiomas', 'Atenda clientes em português, inglês, espanhol e outros idiomas.');

INSERT INTO lp_about_values (lp_id, order_num, icon_name, title, description) VALUES
    (1, 1, 'target', 'Missão',  'Automatizar com inteligência'),
    (1, 2, 'heart',  'Valores', 'Cliente em primeiro lugar'),
    (1, 3, 'bulb',   'Visão',   'Inovação acessível a todos');

INSERT INTO lp_differentials (lp_id, order_num, icon, label) VALUES
    (1, 1, '⚡', 'Setup Rápido'),
    (1, 2, '🔒', 'Segurança Total'),
    (1, 3, '🚀', 'Alta Disponibilidade'),
    (1, 4, '🤝', 'Suporte Dedicado');

INSERT INTO lp_faq_items (lp_id, order_num, question, answer) VALUES
    (1, 1, 'Preciso ter conhecimento técnico para usar?',
     'Não! A Atendus foi desenvolvida para ser extremamente simples e intuitiva. Todo o processo é guiado passo a passo.'),
    (1, 2, 'Existe fidelidade ou contrato?',
     'Para consumidores (B2C), os planos são flexíveis, sem fidelidade. Para empresas (B2B), há fidelidade mínima de 12 meses.'),
    (1, 3, 'O agente responde de forma natural?',
     'Sim! Nossa IA utiliza modelos de linguagem avançados (LLMs) treinados para conversar de forma natural e contextual.'),
    (1, 4, 'E se o cliente precisar falar com um humano?',
     'O sistema permite a transferência para atendimento humano a qualquer momento, preservando todo o histórico da conversa.'),
    (1, 5, 'Meus dados estão seguros?',
     'Absolutamente. Atuamos em conformidade com a LGPD, com criptografia SSL/TLS e AES-256 e servidores no Brasil.'),
    (1, 6, 'Posso cancelar quando quiser?',
     'Sim! Consumidores (B2C) podem cancelar a qualquer momento pelo painel, sem multas.');
*/
