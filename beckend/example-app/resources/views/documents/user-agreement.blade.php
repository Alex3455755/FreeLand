<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 28mm 22mm 26mm 26mm;
        }

        * {
            font-family: "DejaVu Sans", sans-serif;
        }

        body {
            color: #1a1a1a;
            font-size: 11pt;
            line-height: 1.55;
        }

        .doc-header {
            text-align: center;
            border-bottom: 2px solid #2b3a55;
            padding-bottom: 14px;
            margin-bottom: 26px;
        }

        .brand {
            font-size: 13pt;
            letter-spacing: 3px;
            color: #2b3a55;
            font-weight: bold;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 8.5pt;
            letter-spacing: 1px;
            color: #6b7280;
            margin-top: 2px;
        }

        h1.title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 6px 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title-rule {
            width: 70px;
            border: none;
            border-top: 2px solid #c9a227;
            margin: 10px auto 0 auto;
        }

        h2.section {
            font-size: 11.5pt;
            font-weight: bold;
            color: #2b3a55;
            margin: 20px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        p.clause {
            margin: 0 0 7px 0;
            text-align: justify;
        }

        .clause-num {
            font-weight: bold;
            color: #2b3a55;
        }

        .doc-footer {
            margin-top: 34px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            font-size: 9pt;
            color: #6b7280;
        }

        .doc-footer .pub-date {
            font-weight: bold;
            color: #1a1a1a;
        }

        .page-foot {
            position: fixed;
            bottom: -16mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="page-foot">FreeLand — Пользовательское соглашение</div>

    <div class="doc-header">
        <div class="brand">FreeLand</div>
        <div class="brand-sub">Платформа для фрилансеров и заказчиков</div>
        <h1 class="title">Пользовательское соглашение</h1>
        <hr class="title-rule">
    </div>

    @foreach ($sections as $section)
        <h2 class="section">{{ $section['number'] }}. {{ $section['title'] }}</h2>
        @foreach ($section['clauses'] as $clause)
            <p class="clause"><span class="clause-num">{{ $clause['num'] }}</span> {{ $clause['text'] }}</p>
        @endforeach
    @endforeach

    <div class="doc-footer">
        <p>Настоящий документ является официальной публичной офертой платформы FreeLand.</p>
        <p class="pub-date">Дата публикации: {{ $publishedYear }}</p>
    </div>
</body>
</html>
