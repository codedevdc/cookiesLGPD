=== LGPD Cookie Consent ===
Contributors: codedev
Tags: lgpd, cookies, consent, privacidade, gdpr, cookie banner, cookie notice, compliance
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin moderno e completo para conformidade com a LGPD. Banner personalizável, gerenciamento granular de cookies, logs de consentimento e integrações nativas.

== Description ==

O **LGPD Cookie Consent** é a solução mais completa para adequar seu site WordPress à Lei Geral de Proteção de Dados (LGPD). Com um design moderno e configuração simples, você coloca seu banner no ar em minutos — sem precisar editar código.

= Recursos gratuitos =

* Banner de cookies com avatar flutuante e barra inferior
* Tema claro e cores personalizáveis
* Categoria de cookies necessários (obrigatórios)
* Integração nativa com Google Analytics 4 (GA4)
* Registro de consentimentos com retenção de até 30 dias
* Proteção automática de scripts de rastreamento até o consentimento
* Shortcodes para botão de revogação e link de política de privacidade
* Painel de dashboard com estatísticas de consentimento
* 100% compatível com WordPress Multisite

= Recursos PRO =

* **Aparência avançada:** gradiente de cores, cor secundária, tema escuro e automático, presets de paleta
* **Posicionamento:** barra superior e modal central (além do avatar e barra inferior)
* **Avatar:** ícones adicionais (smile, cookie, lock) e animação de pulso
* **Logo personalizada** no banner
* **Integrações:** Google Tag Manager, Facebook Pixel, Hotjar
* **Scripts personalizados** por categoria de cookie
* **Categorias personalizadas** de cookies
* **Logs avançados:** exportação CSV e retenção ilimitada
* **Relatórios gráficos** de consentimento
* **White-label:** remover branding do plugin

= Por que o LGPD Cookie Consent? =

* **Seguro:** nonce obrigatório na API REST, IPs anonimizados nos logs, sanitização completa de inputs
* **Leve:** zero dependências externas de JavaScript
* **Flexível:** shortcodes, filtros WordPress e configurações avançadas de rede (proxy reverso, CloudFlare)
* **Privacidade por padrão:** scripts de rastreamento só são carregados após consentimento explícito

== Installation ==

1. Faça upload da pasta `lgpd-cookie-consent` para o diretório `/wp-content/plugins/`
2. Ative o plugin em **Plugins → Plugins instalados** no painel WordPress
3. Acesse **LGPD Cookies → Configurações** para personalizar o banner
4. (Opcional) Vá em **LGPD Cookies → Aparência** para ajustar cores, posição e ícone
5. Publique — o banner será exibido automaticamente para visitantes sem consentimento registrado

== Frequently Asked Questions ==

= O plugin é compatível com a LGPD brasileira? =

Sim. O plugin implementa consentimento granular por categoria, registro de logs com data/hora e IP anonimizado, opção de revogação de consentimento a qualquer momento, e bloqueio de scripts de rastreamento até o consentimento — todos requisitos da LGPD.

= Preciso de conhecimento técnico para configurar? =

Não. Toda a configuração é feita pelo painel do WordPress, sem precisar editar código.

= O plugin bloqueia o Google Analytics automaticamente? =

Sim, com a opção "Bloquear scripts de rastreamento até consentimento" ativada, o GA4 e outras integrações só são carregadas após o visitante aceitar os cookies.

= Posso personalizar as categorias de cookies? =

Na versão gratuita, as categorias são pré-definidas (Necessários, Análise, Marketing, Preferências). No plano PRO você pode criar e editar categorias personalizadas.

= Os logs de consentimento ficam no meu servidor? =

Sim. Todos os registros são armazenados no banco de dados do seu próprio WordPress. Nenhum dado é enviado a servidores externos (exceto a integração opcional com o Freemius para licenciamento).

= O site usa CloudFlare ou proxy reverso. Como configuro? =

Vá em **LGPD Cookies → Configurações → Rede avançada** e ative a opção "O site usa proxy reverso". Leia o aviso exibido antes de ativar.

= Como o visitante revoga o consentimento? =

Use o shortcode `[lgpd_withdraw_btn]` em qualquer página (ex.: na Política de Privacidade). Ele exibe um botão que reabre o modal de preferências.

== Screenshots ==

1. Banner do avatar flutuante com popover de ações
2. Modal de preferências de cookies
3. Painel de configurações gerais
4. Painel de aparência com seletor de cores
5. Dashboard com estatísticas de consentimento
6. Tela de logs de consentimento

== Changelog ==

= 1.1.1 =
* Segurança: nonce obrigatório no endpoint REST `/consent` — impede envio de logs por bots externos
* Segurança: proteção contra IP spoofing via `X-Forwarded-For`
* Novo: opção de proxy reverso confiável no painel (CloudFlare, nginx, etc.) com aviso de risco
* Correção: logs de consentimento não eram contabilizados quando as configurações eram salvas pela primeira vez
* Correção: botão de upgrade PRO exibia barra de carregamento infinita (`has_paid_plans` corrigido)
* Correção: versão free agora exibe apenas categorias obrigatórias (necessários) para visitantes
* Remoção: bypass de ambiente de desenvolvimento que forçava PRO em localhost

= 1.1.0 =
* Redesign completo do modal de preferências
* Correção crítica: reset CSS global removia padding de todos os elementos do banner
* Correção crítica: erro de referência circular no objeto `i18n` do JavaScript travava o script inteiro
* Integração com Freemius para licenciamento PRO
* Gate de features PRO via `LGPD_CC_Pro::is_active()`
* IP anonimizado nos logs de consentimento (último octeto removido)

= 1.0.0 =
* Lançamento inicial

== Upgrade Notice ==

= 1.1.1 =
Atualização de segurança recomendada. Corrige vulnerabilidade no endpoint REST de consentimento e proteção contra spoofing de IP nos logs.
