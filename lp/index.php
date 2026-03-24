<?php
// ============================================================
// index.php – Landing Page Dinâmica – Atendus
// ============================================================
// Configuração da conexão com o MySQL.
// Prefira variáveis de ambiente em produção:
//   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
// ============================================================

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3307');
define('DB_NAME', getenv('DB_NAME') ?: 'atendus');
define('DB_USER', getenv('DB_USER') ?: 'atendus');
define('DB_PASS', getenv('DB_PASS') ?: 'atendus123');

// Slug vem da URL (?slug=meu-slug) ou usa o padrão abaixo
$slug = preg_replace('/[^a-z0-9\-_]/', '', strtolower($_GET['slug'] ?? 'atendus'));

$lp    = null;
$error = null;

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare(
        'SELECT * FROM v_landing_page_full WHERE slug = :slug AND active = true LIMIT 1'
    );
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();

    if ($row) {
        // As colunas JSON da view chegam como string – precisamos decodificar
        foreach (['steps', 'features_items', 'about_values', 'differentials', 'faq_items'] as $col) {
            if (isset($row[$col]) && is_string($row[$col])) {
                $row[$col] = json_decode($row[$col], true) ?: [];
            }
        }
        $lp = $row;
    }

} catch (PDOException $e) {
    // Em produção, logue o erro sem expor detalhes ao usuário
    error_log('[LP] DB error: ' . $e->getMessage());
    $error = 'Não foi possível carregar a página. Tente novamente em instantes.';
}

// Prepara valores para o HTML (SEO server-side + injeção de dados no JS)
$metaTitle = htmlspecialchars($lp['meta_title']       ?? 'Atendus',  ENT_QUOTES, 'UTF-8');
$metaDesc  = htmlspecialchars($lp['meta_description'] ?? '',          ENT_QUOTES, 'UTF-8');
$metaImg   = htmlspecialchars($lp['meta_og_image']    ?? '',          ENT_QUOTES, 'UTF-8');

// JSON injetado inline – o app.js lê window.LP_DATA e pula o fetch
$lpJson = $lp
    ? json_encode($lp, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
    : 'null';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $metaTitle ?></title>
  <?php if ($metaDesc): ?>
  <meta name="description" content="<?= $metaDesc ?>">
  <?php endif; ?>

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:locale"      content="pt_BR">
  <meta property="og:site_name"   content="Atendus">
  <meta property="og:title"       content="<?= $metaTitle ?>">
  <?php if ($metaDesc): ?>
  <meta property="og:description" content="<?= $metaDesc ?>">
  <?php endif; ?>
  <?php if ($metaImg): ?>
  <meta property="og:image"       content="<?= $metaImg ?>">
  <meta name="twitter:card"       content="summary_large_image">
  <meta name="twitter:image"      content="<?= $metaImg ?>">
  <?php endif; ?>

  <link rel="stylesheet" href="styles.css">

  <!-- Dados do banco injetados antes do app.js: o fetch é pulado automaticamente -->
  <script>window.LP_DATA = <?= $lpJson ?>;</script>
</head>

<body>

  <?php if ($error): ?>
  <!-- Mensagem de erro visível apenas se o banco falhar -->
  <div style="position:fixed;top:0;left:0;right:0;background:#ef4444;color:#fff;text-align:center;padding:12px;z-index:9999;font-family:sans-serif;font-size:.9rem;">
    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <!-- ── Loader ──────────────────────────────────────────────── -->
  <div id="lp-loader" aria-hidden="true">
    <div class="loader-ring"></div>
  </div>


  <!-- ══════════════════════════════════════════════════════════
       NAVBAR  (estático – igual ao Atendus)
  ══════════════════════════════════════════════════════════ -->
  <nav class="navbar" id="navbar">
    <div class="container">
      <div class="navbar-content">

        <!-- Logo -->
        <a href="#" class="logo">
          <div class="logo-icon"><span>A</span></div>
          <span class="logo-text">Atendus</span>
        </a>

        <!-- Links desktop -->
        <div class="nav-links-desktop">
          <a href="#como-funciona">Como Funciona</a>
          <a href="#recursos">Recursos</a>
          <a href="#precos">Preços</a>
          <a href="#sobre">Sobre</a>
          <a href="#contato">Contato</a>
        </div>

        <!-- Ações desktop -->
        <div class="navbar-actions-desktop">
          <div class="theme-toggle-wrapper">
            <button class="theme-toggle-btn" id="themeToggle" aria-label="Alternar tema">
              <svg class="theme-icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
              </svg>
              <svg class="theme-icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
            </button>
          </div>
          <a href="https://app.atendus.com.br/login" class="btn btn-ghost" target="_blank">Entrar</a>
          <a href="https://app.atendus.com.br/register" class="btn btn-primary" target="_blank">Começar Grátis</a>
        </div>

        <!-- Botão menu mobile -->
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu" aria-expanded="false">
          <svg class="menu-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
          <svg class="close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>

      <!-- Menu mobile -->
      <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-content">
          <a href="#como-funciona" class="mobile-nav-link">Como Funciona</a>
          <a href="#recursos" class="mobile-nav-link">Recursos</a>
          <a href="#precos" class="mobile-nav-link">Preços</a>
          <a href="#sobre" class="mobile-nav-link">Sobre</a>
          <a href="#contato" class="mobile-nav-link">Contato</a>
          <div class="mobile-menu-actions">
            <div class="mobile-theme-toggle">
              <span>Tema</span>
              <button class="theme-toggle-btn" id="themeToggleMobile" aria-label="Alternar tema">
                <svg class="theme-icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                </svg>
                <svg class="theme-icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
              </button>
            </div>
            <a href="https://app.atendus.com.br/login" class="btn btn-ghost btn-full">Entrar</a>
            <a href="https://app.atendus.com.br/register" class="btn btn-primary btn-full">Começar Grátis</a>
          </div>
        </div>
      </div>
    </div>
  </nav>


  <main class="main-content">

    <!-- ══════════════════════════════════════════════════════
         HERO  (conteúdo preenchido pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section class="hero-section" id="hero">
      <div class="hero-bg-effects">
        <div class="hero-bg-gradient"></div>
        <div class="hero-bg-glow-1"></div>
        <div class="hero-bg-glow-2"></div>
      </div>

      <div class="container">
        <div class="hero-content">

          <!-- Texto -->
          <div class="hero-text">
            <div class="hero-badge anim">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
              </svg>
              <span><!-- badge --></span>
            </div>

            <h1 class="hero-title anim"><!-- título --></h1>
            <p class="hero-description anim"><!-- subtítulo --></p>

            <div class="hero-cta anim"><!-- botões --></div>
            <div class="hero-stats anim"><!-- stats --></div>
          </div>

          <!-- Visual (phone mockup estático decorativo) -->
          <div class="hero-visual anim">
            <div class="phone-mockup">
              <div class="phone-inner">
                <div class="phone-header">
                  <div class="phone-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/>
                    </svg>
                  </div>
                  <div>
                    <div class="phone-header-title">Atendus Bot</div>
                    <div class="phone-header-subtitle">Online</div>
                  </div>
                </div>
                <div class="phone-chat">
                  <div class="chat-message chat-message-right">
                    <div class="chat-bubble chat-bubble-right">Olá, gostaria de saber os preços</div>
                  </div>
                  <div class="chat-message chat-message-left">
                    <div class="chat-bubble chat-bubble-left">Olá! 👋 Claro! Nossos planos começam em R$ 197/mês. Posso te enviar mais detalhes?</div>
                  </div>
                  <div class="chat-message chat-message-right">
                    <div class="chat-bubble chat-bubble-right">Sim, por favor!</div>
                  </div>
                  <div class="chat-message chat-message-left">
                    <div class="chat-bubble chat-bubble-left">Perfeito! Vou te enviar nosso catálogo completo agora mesmo 📋</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="floating-element floating-1">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              </svg>
            </div>
            <div class="floating-element floating-2">
              <div class="floating-status">
                <div class="status-dot"></div>
                <span>Respondendo...</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         COMO FUNCIONA  (cards preenchidos pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section id="como-funciona" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-badge"><!-- badge --></span>
          <h2 class="section-title"><!-- título --></h2>
          <p class="section-description"><!-- subtítulo --></p>
        </div>
        <div class="steps-grid"><!-- preenchido pelo app.js --></div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         RECURSOS  (cards preenchidos pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section id="recursos" class="section section-alt">
      <div class="section-bg-gradient"></div>
      <div class="container">
        <div class="section-header">
          <span class="section-badge"><!-- badge --></span>
          <h2 class="section-title"><!-- título --></h2>
          <p class="section-description"><!-- subtítulo --></p>
        </div>
        <div class="features-grid"><!-- preenchido pelo app.js --></div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         PREÇOS  (estático – igual ao Atendus)
    ══════════════════════════════════════════════════════ -->
    <section id="precos" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">Preços</span>
          <h2 class="section-title">Planos que <span class="text-gradient">cabem no seu bolso</span></h2>
          <p class="section-description">Comece imediatamente e escale conforme sua necessidade. Cancele quando quiser.</p>
        </div>

        <div class="pricing-grid">

          <!-- Starter -->
          <div class="pricing-card anim">
            <div class="pricing-header">
              <h3 class="pricing-name">Starter</h3>
              <p class="pricing-description">O essencial para começar a automatizar</p>
            </div>
            <div class="pricing-price">
              <span class="price-currency">R$</span>
              <span class="price-value">197</span>
              <span class="price-period">/mês</span>
            </div>
            <ul class="pricing-features">
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>1 número de WhatsApp</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>1 usuário</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>2.000.000 tokens/mês</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>IA conversacional avançada</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Suporte por email</span></li>
            </ul>
            <a href="https://app.atendus.com.br/?plan=starter" class="btn btn-outline btn-full" target="_blank">Começar Agora</a>
          </div>

          <!-- Professional (popular) -->
          <div class="pricing-card pricing-card-popular anim">
            <div class="pricing-badge">Mais Popular</div>
            <div class="pricing-header">
              <h3 class="pricing-name">Professional</h3>
              <p class="pricing-description">Perfeito para escalar sua equipe e vendas</p>
            </div>
            <div class="pricing-price">
              <span class="price-currency">R$</span>
              <span class="price-value">497</span>
              <span class="price-period">/mês</span>
            </div>
            <ul class="pricing-features">
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>3 números de WhatsApp</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>3 usuários</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>6.000.000 tokens/mês</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>IA conversacional avançada</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Suporte por WhatsApp e Email</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Múltiplos atendentes</span></li>
            </ul>
            <a href="https://app.atendus.com.br/?plan=professional" class="btn btn-primary btn-full" target="_blank">Escolher Plano</a>
          </div>

          <!-- Enterprise -->
          <div class="pricing-card anim">
            <div class="pricing-header">
              <h3 class="pricing-name">Enterprise</h3>
              <p class="pricing-description">Potência máxima para grandes operações</p>
            </div>
            <div class="pricing-price">
              <span class="price-currency">R$</span>
              <span class="price-value">997</span>
              <span class="price-period">/mês</span>
            </div>
            <ul class="pricing-features">
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>10 números de WhatsApp</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>10 usuários</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>10.000.000 tokens/mês</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>IA conversacional avançada</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Suporte por WhatsApp e Email</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Múltiplos atendentes</span></li>
              <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>SLA garantido</span></li>
            </ul>
            <a href="https://app.atendus.com.br/?plan=enterprise" class="btn btn-outline btn-full" target="_blank">Escolher Plano</a>
          </div>

        </div>

        <!-- Custom CTA -->
        <div class="pricing-custom-cta">
          <div class="custom-cta-wrapper">
            <div class="custom-cta-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>
            <div class="custom-cta-text">
              <h3>Precisa de um plano maior ou personalizado?</h3>
              <p>Entre em contato com um de nossos especialistas e descubra como podemos criar uma solução sob medida para sua empresa.</p>
            </div>
          </div>
          <a href="https://wa.me/5551936181848?text=Ol%C3%A1!%20Gostaria%20de%20saber%20mais%20sobre%20os%20planos%20personalizados%20da%20Atendus." class="btn-custom-cta" target="_blank">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
            Falar com Especialista
          </a>
        </div>

      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         SOBRE  (conteúdo preenchido pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section id="sobre" class="section section-alt">
      <div class="section-bg-gradient"></div>
      <div class="container">
        <div class="about-content">

          <div class="about-text">
            <span class="section-badge"><!-- badge --></span>
            <h2 class="section-title"><!-- título --></h2>
            <p class="about-description"><!-- desc 1 --></p>
            <p class="about-description"><!-- desc 2 --></p>
            <div class="values-grid"><!-- preenchido pelo app.js --></div>
          </div>

          <div class="about-stats-card">
            <h3 class="stats-card-title"><!-- título diferenciais --></h3>
            <div class="stats-grid"><!-- preenchido pelo app.js --></div>
          </div>

        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         FAQ  (itens preenchidos pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section id="faq" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-badge"><!-- badge --></span>
          <h2 class="section-title"><!-- título --></h2>
          <p class="section-description"><!-- subtítulo --></p>
        </div>
        <div class="faq-container"><!-- preenchido pelo app.js --></div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         CONTATO  (estático – igual ao Atendus)
    ══════════════════════════════════════════════════════ -->
    <section id="contato" class="section section-alt">
      <div class="section-bg-gradient"></div>
      <div class="container">
        <div class="section-header">
          <span class="section-badge">Contato</span>
          <h2 class="section-title">Fale <span class="text-gradient">Conosco</span></h2>
          <p class="section-description">Tem alguma dúvida? Nossa equipe está pronta para ajudar você.</p>
        </div>

        <div class="contact-grid">
          <div class="contact-info">
            <h3 class="contact-info-title">Entre em Contato</h3>

            <div class="contact-items">
              <div class="contact-item anim">
                <div class="contact-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                </div>
                <div>
                  <p class="contact-label">Email</p>
                  <p class="contact-value">contato@atendus.com.br</p>
                </div>
              </div>

              <div class="contact-item anim">
                <div class="contact-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                </div>
                <div>
                  <p class="contact-label">WhatsApp</p>
                  <p class="contact-value">+55 51 93618-1848</p>
                </div>
              </div>

              <div class="contact-item anim">
                <div class="contact-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                  </svg>
                </div>
                <div>
                  <p class="contact-label">Endereço</p>
                  <p class="contact-value">Porto Alegre, RS – Brasil</p>
                </div>
              </div>
            </div>

            <div class="contact-testimonial">
              <p class="testimonial-text">
                "A Atendus revolucionou nosso atendimento. Reduzimos o tempo de resposta e aumentamos nossas vendas consideravelmente."
              </p>
              <div class="testimonial-author">
                <div class="testimonial-avatar"><span>L</span></div>
                <div>
                  <p class="testimonial-name">Lucélia Barros</p>
                  <p class="testimonial-role">CEO, TechStore</p>
                </div>
              </div>
            </div>
          </div>

          <div class="contact-form-wrapper">
            <form class="contact-form" id="contactForm">
              <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" class="form-input" placeholder="Seu nome" required>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-input" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Telefone</label>
                  <input type="tel" class="form-input" placeholder="(11) 99999-9999">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Mensagem</label>
                <textarea class="form-textarea" rows="5" placeholder="Como podemos ajudar?" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-full btn-lg">
                Enviar Mensagem
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
              </button>
            </form>

            <div id="contactSuccess" class="contact-success" style="display:none">
              <div class="contact-success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
              </div>
              <h3 class="contact-success-title">Mensagem Recebida!</h3>
              <p class="contact-success-message">
                Agradecemos seu contato. Se preferir um atendimento mais ágil, envie também uma mensagem pelo WhatsApp.
              </p>
              <div class="contact-success-actions">
                <a href="https://wa.me/5551936181848" target="_blank" class="btn btn-whatsapp btn-lg">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                  </svg>
                  Enviar mensagem no WhatsApp
                </a>
                <button type="button" id="newEmailBtn" class="btn btn-ghost btn-full">Enviar novo email</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════
         CTA FINAL  (conteúdo preenchido pelo app.js)
    ══════════════════════════════════════════════════════ -->
    <section class="cta-section" id="cta">
      <div class="cta-bg"></div>
      <div class="cta-bg-glow"></div>
      <div class="container">
        <div class="cta-content">
          <div class="cta-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 2 7 12 12 22 7 12 2"/><polygon points="2 17 12 22 22 17"/><polygon points="2 12 12 17 22 12"/>
            </svg>
            <span><!-- badge --></span>
          </div>
          <h2 class="cta-title"><!-- título --></h2>
          <p class="cta-description"><!-- descrição --></p>
          <div class="cta-buttons"><!-- botões --></div>
          <p class="cta-trust"><!-- trust text --></p>
        </div>
      </div>
    </section>

  </main>


  <!-- ══════════════════════════════════════════════════════════
       FOOTER  (estático – igual ao Atendus)
  ══════════════════════════════════════════════════════════ -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">

        <div class="footer-brand">
          <a href="#" class="logo">
            <div class="logo-icon"><span>A</span></div>
            <span class="logo-text">Atendus</span>
          </a>
          <p class="footer-description">
            Transformando o atendimento ao cliente com inteligência artificial.
            Automatize seu WhatsApp e nunca perca uma oportunidade de venda.
          </p>
          <div class="footer-social">
            <a href="https://www.instagram.com/atendusai/" class="social-link" aria-label="Instagram" target="_blank">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </a>
            <a href="https://www.facebook.com/atendus/" class="social-link" aria-label="Facebook" target="_blank">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="footer-links">
          <div>
            <h4 class="footer-link-title">Produto</h4>
            <ul class="footer-link-list">
              <li><a href="#recursos">Recursos</a></li>
              <li><a href="#precos">Preços</a></li>
              <li><a href="#como-funciona">Como Funciona</a></li>
            </ul>
          </div>
          <div>
            <h4 class="footer-link-title">Empresa</h4>
            <ul class="footer-link-list">
              <li><a href="#sobre">Sobre Nós</a></li>
              <li><a href="https://atendus.com.br/#" target="_blank">Blog</a></li>
              <li><a href="#contato">Contato</a></li>
            </ul>
          </div>
          <div>
            <h4 class="footer-link-title">Legal</h4>
            <ul class="footer-link-list">
              <li><a href="https://atendus.com.br/termos-de-uso/" target="_blank">Termos de Uso</a></li>
              <li><a href="https://atendus.com.br/privacidade/" target="_blank">Privacidade</a></li>
              <li><a href="https://atendus.com.br/cookies/" target="_blank">Cookies</a></li>
              <li><a href="https://atendus.com.br/lgpd/" target="_blank">LGPD</a></li>
            </ul>
          </div>
        </div>

      </div>

      <div class="footer-bottom">
        <p>© <span id="currentYear"></span> Atendus. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>


  <!-- app.js lê window.LP_DATA (já injetado acima) e pula o fetch -->
  <script src="app.js"></script>
</body>
</html>
