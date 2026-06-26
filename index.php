<?php
require_once __DIR__ . '/includes/global/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpPoint | Sistema de Chamados</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-gray-800">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <div class="max-w-[1300px] mx-auto px-6">

            <div class="flex items-center justify-between h-20">

                <a href="#" class="text-3xl font-bold text-blue-700">
                    HelpPoint
                </a>

                <nav class="hidden md:flex items-center gap-8">

                    <a href="#inicio" class="hover:text-blue-700 transition">
                        Início
                    </a>

                    <a href="#funcionalidades" class="hover:text-blue-700 transition">
                        Funcionalidades
                    </a>

                    <a href="#beneficios" class="hover:text-blue-700 transition">
                        Benefícios
                    </a>

                  

                    <?php if (isLoggedIn()): ?>

                        <a href="pages/dashboard/index.php"
                            class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded transition">
                            Dashboard
                        </a>

                    <?php else: ?>

                        <a href="pages/login/index.php"
                            class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded transition">
                            Entrar
                        </a>

                    <?php endif; ?>

                </nav>

            </div>

        </div>
    </header>

    <!-- Hero -->
    <section id="inicio" class="bg-gradient-to-br from-white to-blue-50">

        <div class="max-w-[1300px] mx-auto px-6 min-h-[85vh] flex items-center">

            <div class="grid lg:grid-cols-2 gap-16 items-center w-full">

                <div>

                    <span class="uppercase tracking-[4px] text-blue-700 font-semibold">
                        Sistema de Gestão de Chamados
                    </span>

                    <h1 class="text-5xl lg:text-7xl font-bold mt-6 leading-tight">
                        O jeito mais simples de gerenciar chamados.
                    </h1>

                    <p class="mt-8 text-lg text-gray-600 leading-8 max-w-xl">
                        O <strong>HelpPoint</strong> centraliza todas as solicitações de suporte
                        em uma única plataforma, permitindo abrir, acompanhar e resolver chamados
                        com rapidez, organização e eficiência.
                    </p>

                    <div class="flex flex-wrap gap-5 mt-10">

                        <?php if (isLoggedIn()): ?>

                            <a href="pages/dashboard/index.php"
                                class="bg-blue-700 hover:bg-blue-800 text-white px-8 py-4 rounded transition">
                                Acessar Dashboard
                            </a>

                        <?php else: ?>

                            <a href="pages/login/index.php"
                                class="bg-blue-700 hover:bg-blue-800 text-white px-8 py-4 rounded transition">
                                Entrar
                            </a>

                        <?php endif; ?>

                        <a href="#funcionalidades"
                            class="border border-blue-700 text-blue-700 px-8 py-4 rounded hover:bg-blue-100 transition">
                            Conheça o sistema
                        </a>

                    </div>

                </div>

                <div>

                    <img
                        src="https://placehold.co/700x500/e5edff/1d4ed8?text=Dashboard+HelpPoint"
                        alt="Dashboard"
                        class="w-full border border-gray-200 shadow-xl rounded">

                </div>

            </div>

        </div>

    </section>

    <!-- Funcionalidades -->
    <section id="funcionalidades" class="py-24">

        <div class="max-w-[1300px] mx-auto px-6">

            <div class="text-center mb-16">

                <span class="text-blue-700 uppercase font-semibold tracking-widest">
                    Funcionalidades
                </span>

                <h2 class="text-4xl font-bold mt-3">
                    Tudo o que você precisa para gerenciar chamados.
                </h2>

            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Abertura de Chamados
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Crie solicitações rapidamente com categoria, prioridade e descrição detalhada.
                    </p>

                </div>

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Acompanhamento
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Consulte em tempo real o andamento dos chamados até sua conclusão.
                    </p>

                </div>

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Administração
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Controle usuários, departamentos, técnicos e permissões de acesso.
                    </p>

                </div>

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Histórico Completo
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Todas as alterações ficam registradas para consulta futura.
                    </p>

                </div>

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Controle por Status
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Visualize facilmente chamados pendentes, em andamento e finalizados.
                    </p>

                </div>

                <div class="border border-gray-200 p-8 rounded hover:shadow-lg transition">

                    <h3 class="text-xl font-semibold mb-4">
                        Interface Moderna
                    </h3>

                    <p class="text-gray-600 leading-7">
                        Design intuitivo para facilitar o uso em qualquer dispositivo.
                    </p>

                </div>

            </div>

        </div>

    </section>

    <!-- Benefícios -->
    <section id="beneficios" class="bg-gray-100 py-24">

        <div class="max-w-[1300px] mx-auto px-6">

            <div class="grid lg:grid-cols-2 gap-20 items-center">

                <div>

                    <img
                        src="https://placehold.co/650x450/f8fafc/2563eb?text=Painel+Administrativo"
                        class="w-full border border-gray-200 rounded shadow-lg"
                        alt="Painel">

                </div>

                <div>

                    <span class="text-blue-700 uppercase tracking-widest font-semibold">
                        Benefícios
                    </span>

                    <h2 class="text-4xl font-bold mt-4 mb-8">
                        Organize sua equipe de suporte com eficiência.
                    </h2>

                    <div class="space-y-6">

                        <div>
                            <h3 class="font-semibold text-xl mb-2">
                                Atendimento mais rápido
                            </h3>

                            <p class="text-gray-600">
                                Centralize todos os chamados em um único ambiente.
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-xl mb-2">
                                Maior organização
                            </h3>

                            <p class="text-gray-600">
                                Categorize chamados por prioridade, setor e responsável.
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-xl mb-2">
                                Acompanhamento completo
                            </h3>

                            <p class="text-gray-600">
                                Usuários e administradores acompanham todo o histórico do atendimento.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- CTA -->
    <section class="py-24">

        <div class="max-w-[1300px] mx-auto px-6">

            <div class="bg-blue-700 text-white rounded p-16 text-center">

                <h2 class="text-4xl font-bold">
                    Comece agora com o HelpPoint
                </h2>

                <p class="mt-6 text-blue-100 text-lg">
                    Gerencie chamados de maneira rápida, organizada e profissional.
                </p>

                <div class="mt-10">

                    <?php if (isLoggedIn()): ?>

                        <a href="pages/dashboard/index.php"
                            class="bg-white text-blue-700 px-8 py-4 rounded font-semibold hover:bg-gray-100 transition">
                            Ir para o Dashboard
                        </a>

                    <?php else: ?>

                        <a href="pages/login/index.php"
                            class="bg-white text-blue-700 px-8 py-4 rounded font-semibold hover:bg-gray-100 transition">
                            Entrar no Sistema
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>

    <!-- Footer -->
    <footer id="contato" class="border-t border-gray-200">

        <div class="max-w-[1300px] mx-auto px-6 py-10 flex flex-col md:flex-row justify-between items-center gap-4">

            <div>

                <h3 class="text-2xl font-bold text-blue-700">
                    HelpPoint
                </h3>

                <p class="text-gray-500 mt-2">
                    Sistema de Gerenciamento de Chamados.
                </p>

            </div>

            <p class="text-gray-500 text-center">
                © <?php echo date('Y'); ?> HelpPoint. Todos os direitos reservados.
            </p>

        </div>

    </footer>

</body>

</html>