<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ensenanza->tema }} - {{ $libro->titulo }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .wrapper {
            max-width: 620px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 30px 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
        .content {
            padding: 30px 25px;
        }
        .teaching-card {
            background-color: #f1f5f9;
            border-left: 4px solid #4f46e5;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 25px;
        }
        .teaching-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .lang-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .lang-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .lang-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }
        .teaching-text {
            font-size: 16px;
            color: #334155;
            margin: 0;
        }
        .action-box {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 25px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <span class="badge">Lección Diaria #{{ $ensenanza->orden }}</span>
            <h1>{{ $libro->titulo }}</h1>
            <p>por {{ $libro->autor }}</p>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $usuario->nombre ?: 'Lector' }}</strong>,</p>
            <p>Aquí tienes tu enseñanza programada de hoy para reflexionar y aplicar en tu día a día:</p>

            <div class="teaching-card">
                <h2 class="teaching-title">{{ $ensenanza->tema }}</h2>

                @foreach($textosPorIdioma as $item)
                    <div class="lang-section">
                        <span class="lang-badge">{{ $item['idioma']->nombre }} ({{ strtoupper($item['idioma']->codigo) }})</span>
                        <p class="teaching-text">{{ $item['texto'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="action-box">
                <a href="{{ config('app.url') }}" class="btn">Continuar Leyendo en BooksMentor</a>
            </div>
        </div>

        <div class="footer">
            <p>Recibes este correo porque estás suscrito al libro <em>{{ $libro->titulo }}</em> en BooksMentor.</p>
            <p>&copy; {{ date('Y') }} BooksMentor — Sabiduría diaria en tus idiomas favoritos.</p>
        </div>
    </div>
</body>
</html>