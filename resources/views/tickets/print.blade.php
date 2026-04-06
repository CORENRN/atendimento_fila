<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            width: 300px;
            margin: 0;
            padding: 10px;
        }
        h1 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 10px;
        }
        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0;
        }
        .line {
            margin: 4px 0;
            font-weight: bold;
        }
        .center {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-top: 30px;
        }
        .date-large {
            font-size: 7px; 
            margin-top: 10px;
            display: block;
        }
        .small-text {
            font-size: 14px;
            font-weight: normal;
        }
    </style>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</head>
<body>

    <h1>TICKET DE ATENDIMENTO</h1>
    <hr>

    <div class="line">Número: {{ $ticket->id }}</div>
    <div class="line">Tipo: {{ strtoupper($ticket->type) }}</div>
    <div class="line">Data: {{ now()->format('d/m/Y H:i') }}</div>
    <div class="line">Fila: {{ strtoupper($ticket->stage) }}</div>

    <hr>

    <div class="center">
        AGUARDE SER CHAMADO
    </div>

</body>
</html>
