<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $ticket->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: 80mm auto; margin: 0 !important; }
            body { margin: 0 !important; padding: 0 !important; background: white !important; width: 80mm; }
            .print-hidden { display: none !important; }
            .print-only { display: block !important; position: absolute; left: 6mm; width: 45mm !important; text-align: center; color: black !important; }
            .bg-black { background-color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        .print-only { display: none; }
        @keyframes pulse-slow { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(0.95); } }
        .animate-pulse-slow { animation: pulse-slow 2s infinite ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen overflow-hidden">
    <div class="print-hidden text-center px-6">
        <div class="mb-6 flex justify-center">
            <div class="relative">
                <div class="w-20 h-20 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 animate-pulse-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Imprimindo sua Senha</h1>
        <p class="text-slate-500 mt-3 text-lg">Retire seu ticket na impressora.</p>
        <div class="mt-8 inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium border border-blue-100">Aguarde...</div>
    </div>
    <div class="print-only" style="font-family: Arial, sans-serif;">
        <div style="border-bottom: 1px solid black; padding-bottom: 2px; margin-bottom: 5px;"><h2 style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin: 0;">SENHA</h2></div>
        <div style="margin-bottom: 5px;"><h1 style="font-size: 38px; font-weight: 900; margin: 0; line-height: 1;">#{{ $ticket->id }}</h1></div>
        <div class="bg-black" style="padding: 3px 0; margin-bottom: 5px;"><h2 style="font-size: 12px; font-weight: bold; color: white !important; text-transform: uppercase; margin: 0;">{{ $ticket->type }}</h2></div>
        <div style="border-top: 1px dashed black; padding-top: 5px; font-size: 8px;"><p style="margin: 0;">{{ date('d/m/Y H:i:s') }}</p><div style="margin-top: 8px;">*******</div></div>
    </div>
    <script>
        window.onload = function() {
            setTimeout(() => { window.print(); }, 500);
            setTimeout(() => { window.location.href = "{{ route('ticket.take') }}"; }, 3000);
        };
    </script>
</body>
</html>