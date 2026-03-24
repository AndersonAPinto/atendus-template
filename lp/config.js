/**
 * config.js – Configuração da LP Dinâmica
 *
 * Edite as variáveis abaixo antes de publicar.
 *
 * Como funciona:
 *  1. A página lê o ?slug=xxx da URL (ou usa LP_SLUG abaixo como padrão).
 *  2. Faz GET  {LP_API_BASE}/api/lp/{slug}
 *  3. Sua API faz:  SELECT * FROM v_landing_page_full
 *                   WHERE slug = $1 AND active = true;
 *     e retorna o resultado como JSON.
 *
 * Para testar sem API, defina window.LP_DATA com os dados direto (ver app.js).
 */

window.LP_CONFIG = {

    /**
     * URL base da sua API backend (sem barra final).
     * Ex: 'https://api.seusite.com.br'  ou  'http://localhost:3000'
     */
    apiBase: 'http://localhost:3000',

    /**
     * Slug padrão quando não há ?slug= na URL.
     * Útil para LPs em domínio próprio (ex: cliente1.seusite.com.br).
     */
    defaultSlug: 'atendus',

    /**
     * Endpoint chamado pela LP.  Não precisa alterar salvo customização.
     * A requisição final será:  GET {apiBase}{endpoint}/{slug}
     */
    endpoint: '/api/lp',

};
