/**
 * app.js – Landing Page Dinâmica – Atendus
 *
 * Fluxo:
 *  1. Lê ?slug=xxx da URL (ou LP_CONFIG.defaultSlug).
 *  2. GET {apiBase}{endpoint}/{slug}  →  JSON da view v_landing_page_full.
 *  3. Renderiza cada seção com os dados.
 *  4. Inicia interações (navbar, mobile menu, FAQ, animações, formulário).
 *
 * Para testar sem backend, defina window.LP_DATA com o objeto JSON
 * antes de carregar este script. O fetch será ignorado.
 */
(function () {
  'use strict';

  /* ── SVG Icons predefinidos ──────────────────────────────── */
  const ICONS = {
    bot: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/></svg>`,
    clock: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
    lightning: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
    shield: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    messages: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,
    chart: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`,
    users: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
    globe: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,
    qr: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M21 14h-3v3h3v4h-4v-3h-3v3h-1v-4h3v-3h-3v-1h4v-3h4v4z"/></svg>`,
    settings: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/></svg>`,
    star: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
    heart: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`,
    target: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,
    lock: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,
    phone: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>`,
    check: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>`,
    bulb: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="6"/><path d="M12 6a6 6 0 1 0 0 12"/><path d="M12 18v4"/><line x1="8" y1="22" x2="16" y2="22"/></svg>`,
    user: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>`,
    arrow: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>`,
    chevron: `<svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>`,
    send: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>`,
  };

  function icon(name, size) {
    const s = size || 24;
    const svg = ICONS[name] || ICONS.star;
    return svg.replace(/width="24" height="24"/, `width="${s}" height="${s}"`);
  }

  /* ── Helpers ─────────────────────────────────────────────── */
  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];
  const safe = (v, fallback) => (v !== null && v !== undefined && v !== '' ? v : (fallback || ''));

  function el(tag, cls, html) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (html) e.innerHTML = html;
    return e;
  }

  /* ── Fetch ───────────────────────────────────────────────── */
  async function fetchData() {
    // Override para testes: defina window.LP_DATA antes de carregar o script
    if (window.LP_DATA) return window.LP_DATA;

    const cfg = window.LP_CONFIG || {};
    const base = cfg.apiBase || '';
    const ep = cfg.endpoint || '/api/lp';
    const slug = new URLSearchParams(window.location.search).get('slug')
      || cfg.defaultSlug
      || 'default';

    const res = await fetch(`${base}${ep}/${slug}`);
    if (!res.ok) throw new Error(`API respondeu ${res.status} para slug="${slug}"`);
    return res.json();
  }

  /* ── Loader ──────────────────────────────────────────────── */
  function hideLoader() {
    const l = $('#lp-loader');
    if (l) l.classList.add('fade-out');
  }

  /* ── Meta / SEO ──────────────────────────────────────────── */
  function renderMeta(d) {
    if (d.meta_title) document.title = d.meta_title;
    setMeta('description', d.meta_description);
    setMeta('og:title', d.meta_title, true);
    setMeta('og:description', d.meta_description, true);
    if (d.meta_og_image) setMeta('og:image', d.meta_og_image, true);
  }

  function setMeta(name, content, og) {
    if (!content) return;
    const attr = og ? 'property' : 'name';
    let tag = document.querySelector(`meta[${attr}="${name}"]`);
    if (!tag) { tag = document.createElement('meta'); tag.setAttribute(attr, name); document.head.appendChild(tag); }
    tag.setAttribute('content', content);
  }

  /* ── Hero ────────────────────────────────────────────────── */
  function renderHero(d) {
    const sec = $('#hero');
    if (!sec) return;

    if (d.hero_badge) {
      sec.querySelector('.hero-badge span').textContent = d.hero_badge;
    }

    const titleEl = sec.querySelector('.hero-title');
    if (titleEl) {
      titleEl.innerHTML = safe(d.hero_title, '')
        + (d.hero_title_gradient
          ? ` <span class="text-gradient">${d.hero_title_gradient}</span>`
          : '');
    }

    const descEl = sec.querySelector('.hero-description');
    if (descEl && d.hero_subtitle) descEl.textContent = d.hero_subtitle;

    // Botões
    const cta = sec.querySelector('.hero-cta');
    if (cta) {
      cta.innerHTML = '';
      if (d.hero_btn_primary_text) {
        cta.innerHTML += `<a href="${safe(d.hero_btn_primary_url,'#')}" class="btn btn-hero" target="_blank">
          ${d.hero_btn_primary_text} ${icon('arrow',20)}</a>`;
      }
      if (d.hero_btn_secondary_text) {
        cta.innerHTML += `<a href="${safe(d.hero_btn_secondary_url,'#')}" class="btn btn-hero-outline" target="_blank">
          ${d.hero_btn_secondary_text}</a>`;
      }
    }

    // Stats
    const stats = sec.querySelector('.hero-stats');
    if (stats && (d.hero_stat_1_value || d.hero_stat_2_value || d.hero_stat_3_value)) {
      const pairs = [
        [d.hero_stat_1_value, d.hero_stat_1_label],
        [d.hero_stat_2_value, d.hero_stat_2_label],
        [d.hero_stat_3_value, d.hero_stat_3_label],
      ].filter(([v]) => v);
      stats.innerHTML = pairs.map(([v, l]) =>
        `<div class="stat-item"><div class="stat-value">${v}</div><div class="stat-label">${safe(l,'')}</div></div>`
      ).join('');
    }

    // Imagem hero (opcional)
    if (d.hero_image_url) {
      const visual = sec.querySelector('.hero-visual');
      if (visual) {
        visual.innerHTML = `<img src="${d.hero_image_url}" alt="Hero" class="hero-image anim">`;
      }
    }
  }

  /* ── Como Funciona ───────────────────────────────────────── */
  function renderHowItWorks(d, steps) {
    const sec = $('#como-funciona');
    if (!sec) return;

    renderSectionHeader(sec, {
      badge: d.how_badge,
      title: d.how_title,
      gradient: d.how_title_gradient,
      subtitle: d.how_subtitle,
    });

    const grid = sec.querySelector('.steps-grid');
    if (!grid || !steps || !steps.length) return;

    grid.innerHTML = steps.map((s, i) => {
      const num = String(s.order_num || i + 1).padStart(2, '0');
      const stepIcons = ['user', 'qr', 'settings', 'arrow'];
      const ic = stepIcons[i] || 'check';
      return `<div class="step-card anim">
        <div class="step-number">${num}</div>
        <div class="step-icon">${icon(ic, 28)}</div>
        <h3 class="step-title">${safe(s.title,'')}</h3>
        <p class="step-description">${safe(s.description,'')}</p>
      </div>`;
    }).join('');
  }

  /* ── Recursos ────────────────────────────────────────────── */
  function renderFeatures(d, items) {
    const sec = $('#recursos');
    if (!sec) return;

    renderSectionHeader(sec, {
      badge: d.features_badge,
      title: d.features_title,
      gradient: d.features_title_gradient,
      subtitle: d.features_subtitle,
    });

    const grid = sec.querySelector('.features-grid');
    if (!grid || !items || !items.length) return;

    grid.innerHTML = items.map(f =>
      `<div class="feature-card anim">
        <div class="feature-icon">${icon(f.icon_name || 'star', 24)}</div>
        <h3 class="feature-title">${safe(f.title,'')}</h3>
        <p class="feature-description">${safe(f.description,'')}</p>
      </div>`
    ).join('');
  }

  /* ── Sobre ───────────────────────────────────────────────── */
  function renderAbout(d, values, diffs) {
    const sec = $('#sobre');
    if (!sec) return;

    const badge = sec.querySelector('.section-badge');
    if (badge && d.about_badge) badge.textContent = d.about_badge;

    const titleEl = sec.querySelector('.section-title');
    if (titleEl) {
      titleEl.innerHTML = safe(d.about_title, '')
        + (d.about_title_gradient
          ? ` <span class="text-gradient">${d.about_title_gradient}</span>` : '');
    }

    const descs = sec.querySelectorAll('.about-description');
    if (descs[0] && d.about_desc_1) descs[0].textContent = d.about_desc_1;
    if (descs[1] && d.about_desc_2) descs[1].textContent = d.about_desc_2;

    // Valores
    const valGrid = sec.querySelector('.values-grid');
    if (valGrid && values && values.length) {
      valGrid.innerHTML = values.map(v =>
        `<div class="value-item">
          <div class="value-icon">${icon(v.icon_name || 'target', 24)}</div>
          <h4 class="value-title">${safe(v.title,'')}</h4>
          <p class="value-desc">${safe(v.description,'')}</p>
        </div>`
      ).join('');
    }

    // Diferenciais
    const diffTitle = sec.querySelector('.stats-card-title');
    if (diffTitle && d.about_diff_title) diffTitle.textContent = d.about_diff_title;

    const diffGrid = sec.querySelector('.stats-grid');
    if (diffGrid && diffs && diffs.length) {
      diffGrid.innerHTML = diffs.map(df =>
        `<div class="about-stat-item">
          <div class="about-stat-value">${safe(df.icon,'')}</div>
          <div class="about-stat-label">${safe(df.label,'')}</div>
        </div>`
      ).join('');
    }
  }

  /* ── FAQ ─────────────────────────────────────────────────── */
  function renderFAQ(d, items) {
    const sec = $('#faq');
    if (!sec) return;

    renderSectionHeader(sec, {
      badge: d.faq_badge,
      title: d.faq_title,
      gradient: d.faq_title_gradient,
      subtitle: d.faq_subtitle,
    });

    const container = sec.querySelector('.faq-container');
    if (!container || !items || !items.length) return;

    container.innerHTML = items.map(f =>
      `<div class="faq-item anim">
        <button class="faq-question" aria-expanded="false">
          ${safe(f.question,'')}
          ${icon('chevron', 20)}
        </button>
        <div class="faq-answer" role="region">
          <p>${safe(f.answer,'')}</p>
        </div>
      </div>`
    ).join('');
  }

  /* ── CTA ─────────────────────────────────────────────────── */
  function renderCTA(d) {
    const sec = $('#cta');
    if (!sec) return;

    const badge = sec.querySelector('.cta-badge span');
    if (badge && d.cta_badge) badge.textContent = d.cta_badge;

    const titleEl = sec.querySelector('.cta-title');
    if (titleEl) {
      titleEl.innerHTML = safe(d.cta_title, '')
        + (d.cta_title_gradient
          ? ` <span class="text-gradient">${d.cta_title_gradient}</span>` : '');
    }

    const desc = sec.querySelector('.cta-description');
    if (desc && d.cta_description) desc.textContent = d.cta_description;

    const trust = sec.querySelector('.cta-trust');
    if (trust && d.cta_trust_text) trust.textContent = d.cta_trust_text;

    const btns = sec.querySelector('.cta-buttons');
    if (btns) {
      btns.innerHTML = '';
      if (d.cta_btn_primary_text) {
        btns.innerHTML += `<a href="${safe(d.cta_btn_primary_url,'#')}" class="btn btn-hero" target="_blank">
          ${d.cta_btn_primary_text} ${icon('arrow',20)}</a>`;
      }
      if (d.cta_btn_secondary_text) {
        btns.innerHTML += `<a href="${safe(d.cta_btn_secondary_url,'#')}" class="btn btn-hero-outline" target="_blank">
          ${d.cta_btn_secondary_text}</a>`;
      }
    }
  }

  /* ── Helper: section header ──────────────────────────────── */
  function renderSectionHeader(sec, { badge, title, gradient, subtitle }) {
    const hdr = sec.querySelector('.section-header');
    if (!hdr) return;
    const b = hdr.querySelector('.section-badge');
    if (b && badge) b.textContent = badge;
    const t = hdr.querySelector('.section-title');
    if (t) {
      t.innerHTML = safe(title, '')
        + (gradient ? ` <span class="text-gradient">${gradient}</span>` : '');
    }
    const s = hdr.querySelector('.section-description');
    if (s && subtitle) s.textContent = subtitle;
  }

  /* ── Render completo ─────────────────────────────────────── */
  function renderPage(data) {
    renderMeta(data);
    renderHero(data);
    renderHowItWorks(data, data.steps || []);
    renderFeatures(data, data.features_items || []);
    renderAbout(data, data.about_values || [], data.differentials || []);
    renderFAQ(data, data.faq_items || []);
    renderCTA(data);

    // Ano no footer
    const yr = $('#currentYear');
    if (yr) yr.textContent = new Date().getFullYear();
  }

  /* == INTERAÇÕES ============================================ */

  /* ── Navbar scroll ───────────────────────────────────────── */
  function initNavbar() {
    const nav = $('#navbar');
    if (!nav) return;
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ── Mobile menu ─────────────────────────────────────────── */
  function initMobileMenu() {
    const btn = $('#mobileMenuBtn');
    const menu = $('#mobileMenu');
    if (!btn || !menu) return;

    btn.addEventListener('click', () => {
      const open = menu.classList.toggle('open');
      btn.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open);
    });

    // Fecha ao clicar em link
    menu.querySelectorAll('a').forEach(a =>
      a.addEventListener('click', () => {
        menu.classList.remove('open');
        btn.classList.remove('open');
      })
    );
  }

  /* ── Theme toggle ────────────────────────────────────────── */
  function initThemeToggle() {
    // Padrão: light mode. Só escurece se o usuário tiver salvo 'dark'.
    const stored = localStorage.getItem('lp-theme');
    if (stored !== 'dark') document.body.classList.add('light-mode');

    $$('.theme-toggle-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        localStorage.setItem('lp-theme',
          document.body.classList.contains('light-mode') ? 'light' : 'dark');
      });
    });
  }

  /* ── FAQ accordion ───────────────────────────────────────── */
  function initFAQ() {
    document.addEventListener('click', e => {
      const btn = e.target.closest('.faq-question');
      if (!btn) return;
      const item = btn.closest('.faq-item');
      const isOpen = item.classList.contains('open');
      // Fecha todos
      $$('.faq-item.open').forEach(i => {
        i.classList.remove('open');
        i.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  }

  /* ── Scroll animations (IntersectionObserver) ────────────── */
  function initScrollAnimations() {
    if (!('IntersectionObserver' in window)) {
      $$('.anim').forEach(el => el.classList.add('visible'));
      return;
    }
    const obs = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('visible'), i * 80);
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });

    $$('.anim').forEach(el => obs.observe(el));
  }

  /* ── Formulário de contato ───────────────────────────────── */
  function initContactForm() {
    const form = $('#contactForm');
    const success = $('#contactSuccess');
    const newBtn = $('#newEmailBtn');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Enviando...';

      // Aguarda 1s (simulação) – substitua por fetch real se necessário
      await new Promise(r => setTimeout(r, 1000));

      form.style.display = 'none';
      if (success) success.style.display = 'block';
      submitBtn.disabled = false;
    });

    if (newBtn) {
      newBtn.addEventListener('click', () => {
        form.style.display = 'flex';
        if (success) success.style.display = 'none';
        form.reset();
      });
    }
  }

  /* ── Smooth scroll para âncoras ──────────────────────────── */
  function initSmoothScroll() {
    document.addEventListener('click', e => {
      const a = e.target.closest('a[href^="#"]');
      if (!a) return;
      const id = a.getAttribute('href').slice(1);
      const target = id ? document.getElementById(id) : null;
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  /* ── Ano footer ──────────────────────────────────────────── */
  function updateYear() {
    const el = document.getElementById('currentYear');
    if (el) el.textContent = new Date().getFullYear();
  }

  /* ── Boot ────────────────────────────────────────────────── */
  async function init() {
    try {
      const data = await fetchData();
      renderPage(data);
    } catch (err) {
      console.error('[LP] Erro ao carregar dados:', err);
      // Mantém o conteúdo placeholder do HTML se a API falhar
    } finally {
      hideLoader();
      // Inicia interações independente da API
      initNavbar();
      initMobileMenu();
      initThemeToggle();
      initFAQ();
      initSmoothScroll();
      updateYear();
      // Após render, observa os elementos animados
      requestAnimationFrame(() => initScrollAnimations());
    }
  }

  document.addEventListener('DOMContentLoaded', init);
})();
