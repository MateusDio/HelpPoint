<?php
require_once __DIR__ . '/includes/global/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpPoint — Suporte e chamados sem caos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1d4ed8; --primary-dark: #1e3a8a; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }

        /* Gradient hero */
        .hero-gradient {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(29,78,216,0.15), transparent),
                        radial-gradient(ellipse 60% 40% at 80% 50%, rgba(139,92,246,0.10), transparent),
                        radial-gradient(ellipse 60% 40% at 20% 80%, rgba(6,182,212,0.10), transparent),
                        #fff;
        }

        /* Blob flutuante */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .35;
            pointer-events: none;
            z-index: 0;
        }
        @keyframes floatBlob {
            0%,100% { transform: translate(0,0) scale(1); }
            50%     { transform: translate(40px, -30px) scale(1.1); }
        }
        .blob-a { background:#3b82f6; width:380px; height:380px; top:-80px; right:-60px; animation: floatBlob 14s ease-in-out infinite; }
        .blob-b { background:#8b5cf6; width:320px; height:320px; bottom:-100px; left:-40px; animation: floatBlob 18s ease-in-out infinite reverse; }

        /* Fade-up */
        @keyframes fadeUp { from { opacity:0; transform: translateY(20px);} to { opacity:1; transform:translateY(0);} }
        .fade-up { animation: fadeUp .8s ease both; }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }

        /* Gradient text */
        .grad-text {
            background: linear-gradient(135deg, #1d4ed8, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Mock dashboard card */
        .mock-dash {
            background: linear-gradient(180deg, #fff, #f8fafc);
            border: 1px solid #e5e7eb;
            box-shadow: 0 30px 60px -20px rgba(15,23,42,0.25), 0 12px 25px -8px rgba(29,78,216,0.15);
        }

        /* Pulse dot */
        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
            70% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
            100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
        }
        .pulse-dot { animation: pulseDot 2s infinite; }

        /* Marquee */
        .marquee { overflow: hidden; mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent); }
        .marquee-track { display:flex; gap:3rem; animation: scroll 35s linear infinite; width: max-content; }
        @keyframes scroll { from{ transform: translateX(0);} to { transform: translateX(-50%);} }

        /* Card hover */
        .feature-card { transition: all .25s; }
        .feature-card:hover { transform: translateY(-4px); border-color: #1d4ed8; box-shadow: 0 12px 30px -10px rgba(29,78,216,.18); }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased">

<!-- ====== HEADER ====== -->
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-[1300px] mx-auto px-6 h-16 flex items-center justify-between">
        <a href="#" class="flex items-center gap-2 font-bold text-xl text-slate-900">
            <span class="w-8 h-8 rounded-lg bg-blue-700 text-white grid place-items-center"><i class="bi bi-headset"></i></span>
            HelpPoint
        </a>
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
            <a href="#funcionalidades" class="hover:text-slate-900">Funcionalidades</a>
            <a href="#como-funciona" class="hover:text-slate-900">Como funciona</a>
            <a href="#depoimentos" class="hover:text-slate-900">Depoimentos</a>
            <a href="#faq" class="hover:text-slate-900">FAQ</a>
        </nav>
        <div class="flex items-center gap-3">
            <?php if (isLoggedIn()): ?>
                <a href="pages/<?= isAdmin() ? 'admin/dashboard.php' : 'dashboard/index.php' ?>"
                   class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Ir para o Painel <i class="bi bi-arrow-right"></i>
                </a>
            <?php else: ?>
                <a href="pages/login/index.php" class="hidden sm:inline text-sm font-medium text-slate-700 hover:text-slate-900">Entrar</a>
                <a href="pages/login/index.php?modo=registro"
                   class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Começar grátis <i class="bi bi-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- ====== HERO ====== -->
<section id="inicio" class="hero-gradient relative overflow-hidden">
    <div class="blob blob-a"></div>
    <div class="blob blob-b"></div>

    <div class="max-w-[1300px] mx-auto px-6 pt-20 pb-24 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <span class="fade-up inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-green-500 pulse-dot"></span>
                Novo · Sistema de chamados HelpPoint v1.0
            </span>
            <h1 class="fade-up delay-1 text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mt-6 leading-[1.05]">
                Suporte sem caos.<br>
                <span class="grad-text">Chamados sob controle.</span>
            </h1>
            <p class="fade-up delay-2 text-lg md:text-xl text-slate-600 mt-6 max-w-2xl mx-auto leading-relaxed">
                O HelpPoint centraliza solicitações, equipamentos e respostas em um único lugar.
                Sua equipe abre. A TI resolve. Você acompanha.
            </p>
            <div class="fade-up delay-3 flex flex-wrap justify-center gap-3 mt-9">
                <?php if (isLoggedIn()): ?>
                    <a href="pages/<?= isAdmin() ? 'admin/dashboard.php' : 'dashboard/index.php' ?>"
                       class="bg-slate-900 hover:bg-slate-800 text-white px-7 py-3.5 rounded-lg font-semibold transition flex items-center gap-2">
                        Acessar Painel <i class="bi bi-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="pages/login/index.php?modo=registro"
                       class="bg-slate-900 hover:bg-slate-800 text-white px-7 py-3.5 rounded-lg font-semibold transition flex items-center gap-2">
                        Criar conta grátis <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#como-funciona"
                       class="bg-white border border-slate-300 hover:border-slate-900 text-slate-900 px-7 py-3.5 rounded-lg font-semibold transition flex items-center gap-2">
                        <i class="bi bi-play-circle"></i> Como funciona
                    </a>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500 mt-4">Sem cartão de crédito · Setup em 2 minutos</p>
        </div>

        <!-- Mock dashboard -->
        <div class="fade-up delay-3 max-w-5xl mx-auto mt-16 relative">
            <div class="mock-dash rounded-2xl overflow-hidden">
                <!-- Topbar mock -->
                <div class="flex items-center gap-2 px-4 h-9 bg-slate-100 border-b border-slate-200">
                    <div class="flex gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                    </div>
                    <div class="mx-auto text-xs text-slate-500 font-mono">helppoint.com / dashboard</div>
                </div>
                <!-- Stats -->
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-4 gap-3 mb-6">
                        <div class="border border-slate-200 rounded-xl p-3">
                            <div class="text-xs text-slate-500">Abertos</div>
                            <div class="text-2xl font-bold mt-1">24</div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-3">
                            <div class="text-xs text-slate-500">Em andamento</div>
                            <div class="text-2xl font-bold mt-1">12</div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-3">
                            <div class="text-xs text-slate-500">Concluídos</div>
                            <div class="text-2xl font-bold mt-1 text-green-600">147</div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-3">
                            <div class="text-xs text-slate-500">Tempo médio</div>
                            <div class="text-2xl font-bold mt-1">2h</div>
                        </div>
                    </div>
                    <!-- Chamados mock -->
                    <div class="grid md:grid-cols-3 gap-3">
                        <?php
                        $mockCards = [
                            ['#1247', 'Hardware',  'Aberto',       'bg-blue-50',  'text-blue-700',  'border-l-blue-500'],
                            ['#1246', 'Rede',      'Em Andamento', 'bg-yellow-50','text-yellow-700','border-l-yellow-500'],
                            ['#1245', 'Software',  'Concluído',    'bg-green-50', 'text-green-700', 'border-l-green-500'],
                        ];
                        foreach ($mockCards as $m): ?>
                        <div class="border border-slate-200 border-l-4 <?= $m[5] ?> rounded-lg p-3 text-left">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm"><?= $m[0] ?></span>
                                <span class="text-xs <?= $m[3] ?> <?= $m[4] ?> px-2 py-0.5 rounded-full font-semibold"><?= $m[2] ?></span>
                            </div>
                            <div class="text-xs text-slate-500"><i class="bi bi-tag-fill"></i> <?= $m[1] ?></div>
                            <div class="text-xs text-slate-500 mt-1"><i class="bi bi-geo-alt"></i> Sala 12 · 2º andar</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== SOCIAL PROOF MARQUEE ====== -->
<section class="py-12 border-y border-slate-200 bg-slate-50">
    <p class="text-center text-xs uppercase tracking-widest text-slate-500 mb-6 font-semibold">Equipes que já usam o HelpPoint</p>
    <div class="marquee">
        <div class="marquee-track">
            <?php
            $logos = ['Nimbus', 'Acme Co.', 'TechBR', 'Vertex', 'Polaris', 'Atlas', 'Lumio', 'Helix', 'Nimbus', 'Acme Co.', 'TechBR', 'Vertex', 'Polaris', 'Atlas', 'Lumio', 'Helix'];
            foreach ($logos as $l): ?>
                <span class="text-2xl font-bold text-slate-400 flex items-center gap-2"><i class="bi bi-hexagon-fill text-slate-300"></i> <?= $l ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== FUNCIONALIDADES ====== -->
<section id="funcionalidades" class="py-24">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-blue-700 text-sm font-bold uppercase tracking-widest">Funcionalidades</span>
            <h2 class="text-4xl md:text-5xl font-bold mt-3 tracking-tight">Tudo para gerenciar chamados, em um só lugar.</h2>
            <p class="text-slate-600 mt-4 text-lg">Do primeiro click ao "concluído". Sem planilhas, sem grupos de WhatsApp.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php
            $features = [
                ['bi-ticket-perforated','Abertura em 3 passos','Categoria, equipamento e descrição. Pronto. Wizard guiado, sem campos demais.','bg-blue-100 text-blue-700'],
                ['bi-arrow-repeat','Status em tempo real','Aberto → Em andamento → Concluído. Cliente acompanha sem ligar pra TI.','bg-yellow-100 text-yellow-700'],
                ['bi-pc-display','Equipamentos mapeados','Patrimônio, número de série, tipo e histórico. Cada chamado ligado ao ativo.','bg-purple-100 text-purple-700'],
                ['bi-grid','Painel admin completo','Cards por chamado, filtro por status, ação rápida de mudança de status.','bg-pink-100 text-pink-700'],
                ['bi-people','Multiusuário','Cada cliente vê só os próprios chamados. Admin vê tudo. Permissão por role.','bg-green-100 text-green-700'],
                ['bi-shield-check','Seguro por padrão','Senhas com hash, prepared statements, sessão protegida. Você só foca no atendimento.','bg-cyan-100 text-cyan-700'],
            ];
            foreach ($features as $f): ?>
            <div class="feature-card bg-white border border-slate-200 rounded-2xl p-7">
                <div class="w-12 h-12 rounded-xl <?= $f[3] ?> grid place-items-center text-xl mb-5"><i class="bi <?= $f[0] ?>"></i></div>
                <h3 class="text-lg font-bold mb-2"><?= $f[1] ?></h3>
                <p class="text-slate-600 leading-relaxed"><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== COMO FUNCIONA ====== -->
<section id="como-funciona" class="py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-blue-700 text-sm font-bold uppercase tracking-widest">Como funciona</span>
            <h2 class="text-4xl md:text-5xl font-bold mt-3 tracking-tight">Três passos. Sem mistério.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8 relative">
            <?php
            $passos = [
                ['1','Cliente abre','Escolhe categoria nos cards, marca o equipamento, descreve o problema. Pronto.','bi-cursor-fill'],
                ['2','TI recebe','Chamado entra no painel admin. Filtre por status, abra, mude pra "Em andamento".','bi-bell-fill'],
                ['3','Resolve e fecha','Atualiza o status pra "Concluído". Cliente vê na hora. Sem follow-up por email.','bi-check2-circle'],
            ];
            foreach ($passos as $p): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-7 relative">
                <div class="absolute -top-4 -left-4 w-12 h-12 rounded-full bg-slate-900 text-white grid place-items-center font-bold text-lg shadow-lg"><?= $p[0] ?></div>
                <i class="bi <?= $p[3] ?> text-3xl text-blue-700 mb-4 block"></i>
                <h3 class="text-xl font-bold mb-2"><?= $p[1] ?></h3>
                <p class="text-slate-600 leading-relaxed"><?= $p[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== STATS ====== -->
<section class="py-24">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <?php
            $stats = [
                ['10k+', 'Chamados resolvidos'],
                ['98%',  'Satisfação dos usuários'],
                ['2h',   'Tempo médio de resposta'],
                ['24/7', 'Disponibilidade do sistema'],
            ];
            foreach ($stats as $s): ?>
            <div>
                <div class="text-5xl md:text-6xl font-extrabold grad-text"><?= $s[0] ?></div>
                <div class="text-slate-600 mt-2 text-sm font-medium"><?= $s[1] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== DEPOIMENTOS ====== -->
<section id="depoimentos" class="py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-blue-700 text-sm font-bold uppercase tracking-widest">Depoimentos</span>
            <h2 class="text-4xl md:text-5xl font-bold mt-3 tracking-tight">Equipes felizes. TI sem dor de cabeça.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <?php
            $deps = [
                ['Ana M.',     'Coordenadora · Nimbus', '"Reduziu nosso tempo de resposta em mais da metade. A equipe inteira adotou em uma semana."'],
                ['Carlos R.',  'Analista TI · Acme',     '"Finalmente sei o status de cada chamado em tempo real. Acabou o caos do email."'],
                ['Beatriz F.', 'Diretora · Vertex',      '"Interface limpa, sem firula. Funciona, e isso é o que importa."'],
                ['Diego S.',   'Técnico · TechBR',       '"O painel admin é muito intuitivo. Os cards de chamado deixam tudo visual."'],
                ['Eduarda P.', 'Gerente · Atlas',        '"Visibilidade total dos equipamentos. Hoje sei o histórico de cada máquina."'],
                ['Hugo N.',    'CEO · Lumio',            '"Melhor ferramenta de chamados que já usamos. Simples e direto."'],
            ];
            foreach ($deps as $d):
                $i = strtoupper(mb_substr($d[0], 0, 1));
            ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <div class="flex text-yellow-400 mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-slate-700 leading-relaxed mb-5"><?= htmlspecialchars($d[2]) ?></p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 text-white grid place-items-center font-bold"><?= $i ?></div>
                    <div>
                        <div class="font-semibold text-sm"><?= htmlspecialchars($d[0]) ?></div>
                        <div class="text-xs text-slate-500"><?= htmlspecialchars($d[1]) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== FAQ ====== -->
<section id="faq" class="py-24">
    <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-blue-700 text-sm font-bold uppercase tracking-widest">FAQ</span>
            <h2 class="text-4xl md:text-5xl font-bold mt-3 tracking-tight">Perguntas frequentes.</h2>
        </div>
        <div class="space-y-3">
            <?php
            $faqs = [
                ['O HelpPoint é gratuito?', 'Sim, totalmente gratuito. Você só precisa cadastrar sua conta e começar a usar.'],
                ['Preciso instalar alguma coisa?', 'Não. É um sistema web — basta acessar pelo navegador. Funciona em qualquer dispositivo.'],
                ['Como funciona o controle de equipamentos?', 'Cada equipamento tem patrimônio, número de série e tipo. Ao abrir um chamado, o cliente pode vincular o ativo, criando um histórico completo de manutenções.'],
                ['Posso ter mais de um admin?', 'Sim. Qualquer usuário pode ser promovido a admin, e admins têm acesso completo a chamados, equipamentos, categorias e tipos.'],
                ['Quem vê o quê?', 'Clientes veem apenas os próprios chamados. Admins veem tudo, podem mudar status, editar e excluir.'],
            ];
            foreach ($faqs as $i => $q): ?>
            <details class="group border border-slate-200 rounded-xl bg-white p-5 cursor-pointer">
                <summary class="flex items-center justify-between font-semibold list-none">
                    <span><?= htmlspecialchars($q[0]) ?></span>
                    <i class="bi bi-plus-lg text-slate-400 group-open:rotate-45 transition-transform"></i>
                </summary>
                <p class="text-slate-600 mt-3 leading-relaxed"><?= htmlspecialchars($q[1]) ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== CTA FINAL ====== -->
<section class="py-24">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-12 md:p-20 text-center text-white">
            <div class="blob blob-a" style="opacity:.25"></div>
            <div class="blob blob-b" style="opacity:.25"></div>
            <div class="relative z-10">
                <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">Pronto pra acabar com o caos?</h2>
                <p class="text-slate-300 mt-4 text-lg max-w-xl mx-auto">Crie sua conta agora e abra o primeiro chamado em menos de 2 minutos.</p>
                <div class="flex flex-wrap justify-center gap-3 mt-9">
                    <?php if (isLoggedIn()): ?>
                        <a href="pages/<?= isAdmin() ? 'admin/dashboard.php' : 'dashboard/index.php' ?>"
                           class="bg-white text-slate-900 hover:bg-slate-100 px-7 py-3.5 rounded-lg font-semibold transition flex items-center gap-2">
                            Ir para o painel <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="pages/login/index.php?modo=registro"
                           class="bg-white text-slate-900 hover:bg-slate-100 px-7 py-3.5 rounded-lg font-semibold transition flex items-center gap-2">
                            Criar conta grátis <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="pages/login/index.php"
                           class="border border-white/30 hover:bg-white/10 text-white px-7 py-3.5 rounded-lg font-semibold transition">
                            Já tenho conta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== FOOTER ====== -->
<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-[1300px] mx-auto px-6 py-12">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <div class="md:col-span-2">
                <a href="#" class="flex items-center gap-2 font-bold text-xl text-slate-900">
                    <span class="w-8 h-8 rounded-lg bg-blue-700 text-white grid place-items-center"><i class="bi bi-headset"></i></span>
                    HelpPoint
                </a>
                <p class="text-slate-600 mt-3 max-w-sm">Sistema de gerenciamento de chamados para equipes que valorizam organização e velocidade.</p>
            </div>
            <div>
                <h4 class="font-semibold text-sm mb-3">Produto</h4>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li><a href="#funcionalidades" class="hover:text-slate-900">Funcionalidades</a></li>
                    <li><a href="#como-funciona" class="hover:text-slate-900">Como funciona</a></li>
                    <li><a href="#faq" class="hover:text-slate-900">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-sm mb-3">Acesso</h4>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li><a href="pages/login/index.php" class="hover:text-slate-900">Entrar</a></li>
                    <li><a href="pages/login/index.php?modo=registro" class="hover:text-slate-900">Criar conta</a></li>
                </ul>
            </div>
        </div>
        <div class="pt-6 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-3 text-sm text-slate-500">
            <p>© <?= date('Y') ?> HelpPoint · Todos os direitos reservados.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-slate-900"><i class="bi bi-github text-lg"></i></a>
                <a href="#" class="hover:text-slate-900"><i class="bi bi-linkedin text-lg"></i></a>
                <a href="#" class="hover:text-slate-900"><i class="bi bi-twitter-x text-lg"></i></a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
