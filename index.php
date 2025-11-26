<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marque sua Consulta - Clínica Premium</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Raleway:wght@300;400;600&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#564549',
                        secondary: '#767964',
                        background: '#e9eae2',
                        highlight: '#d4af37',
                    },
                    fontFamily: {
                        serif: ['"DM Serif Display"', 'serif'],
                        sans: ['"Raleway"', 'sans-serif'],
                        display: ['"Simplifica"', 'sans-serif'], // Fallback to sans if not found
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Font Fallback / Placeholder for Simplifica if needed */
        /* @font-face { font-family: 'Simplifica'; src: url('path/to/simplifica.woff2'); } */

        body {
            background-color: #e9eae2;
            color: #564549;
        }

        .slide-enter {
            opacity: 0;
            transform: translateY(20px);
        }

        .slide-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }

        .slide-exit {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-exit-active {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease-in, transform 0.5s ease-in;
        }

        /* Progress Bar Transition */
        #progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>

<body class="font-sans antialiased h-screen flex flex-col overflow-hidden">

    <!-- Progress Bar -->
    <div class="w-full h-2 bg-gray-200 fixed top-0 left-0 z-50">
        <div id="progress-bar" class="h-full bg-highlight w-0"></div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow flex items-center justify-center p-6 relative">

        <!-- Welcome Screen (Step 0) -->
        <div id="step-0" class="step-container text-center max-w-2xl w-full">
            <h1 class="font-serif text-5xl md:text-6xl text-primary mb-6">Marque sua Consulta</h1>
            <p class="text-xl md:text-2xl text-secondary mb-10 font-light">Preencha os Dados Abaixo para lhe Conhecermos
                Melhor.</p>
            <button onclick="nextStep()"
                class="bg-primary text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-secondary transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Começar
            </button>
        </div>

        <!-- Question Template (Hidden by default) -->
        <form id="quiz-form" class="w-full max-w-2xl hidden" onsubmit="return false;">

            <!-- Step 1: Nome -->
            <div id="step-1" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Qual seu nome completo?</label>
                <input type="text" name="nome"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="Digite sua resposta aqui..." required>
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 2: Como quer ser chamado -->
            <div id="step-2" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Como você gosta de ser
                    chamada(o)?</label>
                <input type="text" name="como_quer_ser_chamado"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="Digite sua resposta aqui...">
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 3: Whatsapp -->
            <div id="step-3" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Coloque aqui seu Whatsapp</label>
                <input type="tel" name="telefone"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="(00) 00000-0000" required>
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 4: Idade -->
            <div id="step-4" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Qual sua idade?</label>
                <input type="number" name="idade"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="Digite sua idade..." required>
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 5: Local da Dor -->
            <div id="step-5" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Onde é sua dor?</label>
                <input type="text" name="local_dor"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="Descreva o local..." required>
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 6: Tempo da Dor -->
            <div id="step-6" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">Há quanto tempo?</label>
                <input type="text" name="tempo_dor"
                    class="w-full bg-transparent border-b-2 border-secondary text-2xl py-2 focus:outline-none focus:border-highlight transition-colors placeholder-gray-400"
                    placeholder="Ex: 2 meses, 1 ano..." required>
                <div class="mt-8 flex justify-end">
                    <button onclick="nextStep()"
                        class="bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">OK
                        ✓</button>
                </div>
            </div>

            <!-- Step 7: Interesse (Final) -->
            <div id="step-7" class="step-container hidden">
                <label class="block font-serif text-3xl md:text-4xl text-primary mb-6">O valor da consulta é R$ 500,00.
                    Você tem interesse em marcar a sua?</label>
                <div class="flex flex-col space-y-4">
                    <button type="button" onclick="submitForm('Sim')"
                        class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white py-4 rounded-lg text-xl transition-all duration-300">Sim,
                        tenho interesse</button>
                    <button type="button" onclick="submitForm('Talvez')"
                        class="w-full border-2 border-secondary text-secondary hover:bg-secondary hover:text-white py-4 rounded-lg text-xl transition-all duration-300">Gostaria
                        de saber mais</button>
                </div>
            </div>

        </form>

        <!-- Loading State -->
        <div id="loading-screen" class="hidden text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-highlight border-solid mx-auto mb-4">
            </div>
            <p class="text-xl font-serif text-primary">Analisando suas respostas...</p>
        </div>

        <!-- Success Message -->
        <div id="success-screen" class="hidden text-center max-w-2xl">
            <h2 class="font-serif text-4xl text-primary mb-4">Obrigado!</h2>
            <p class="text-xl text-secondary">Recebemos suas informações. Nossa equipe entrará em contato em breve.</p>
        </div>

    </main>

    <footer class="fixed bottom-0 w-full p-4 text-center text-secondary text-sm opacity-50">
        <p>&copy; 2024 Clínica Premium. Todos os direitos reservados.</p>
    </footer>

    <script src="assets/js/quiz.js"></script>
</body>

</html>