<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - Gestione Date Esami</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0a0a0a] text-[#ededec] antialiased min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-[#161615] border border-[#27272a] rounded-2xl p-8 shadow-2xl">
        
        <!-- Intestazione del Login -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white flex items-center justify-center gap-2">
                <span class="text-[#f9322c]">●</span> Portale Esami
            </h1>
            <p class="text-sm text-[#706f6c] mt-2">Inserisci le tue credenziali per accedere</p>
        </div>

        <!-- Form di Login -->
        <form action="/login" method="POST" class="space-y-5">
            @csrf

            <!-- Campo Email -->
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#706f6c] mb-2">
                    Email
                </label>
                <input type="email" id="email" name="email" required placeholder="nome@esempio.it"
                       class="w-full bg-[#0a0a0a] border border-[#27272a] rounded-xl px-4 py-3 text-white placeholder-[#706f6c] focus:outline-none focus:border-[#f9322c] transition">
            </div>

            <!-- Campo Password -->
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#706f6c] mb-2">
                    Password
                </label>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                       class="w-full bg-[#0a0a0a] border border-[#27272a] rounded-xl px-4 py-3 text-white placeholder-[#706f6c] focus:outline-none focus:border-[#f9322c] transition">
            </div>

            <!-- Opzioni Aggiuntive -->
            <div class="flex items-center justify-between text-xs text-[#706f6c]">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#27272a] bg-[#0a0a0a] text-[#f9322c] focus:ring-0">
                    Ricordami
                </label>
                <a href="#" class="hover:text-white transition">Password dimenticata?</a>
            </div>

            <!-- Bottone di Invio -->
            <button type="submit" 
                    class="w-full bg-[#f9322c] hover:bg-[#e02b25] text-white font-medium py-3 rounded-xl transition duration-200 shadow-lg shadow-[#f9322c]/20">
                Accedi
            </button>
        </form>

    </div>

</body>
</html>