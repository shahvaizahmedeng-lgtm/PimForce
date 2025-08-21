<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PimForce</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <style>
            :root {
                --brand:#7c3aed;
                --brand-600:#6d28d9;
                --brand-200:#ddd6fe;
                --text:#0f0f12;
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                color: var(--text);
                background: linear-gradient(180deg, var(--brand-200) 0%, #ffffff 45%);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .container { width: 100%; max-width: 1100px; margin: 0 auto; padding: 0 20px; }
            header { padding: 20px 0; }
            .nav { display:flex; align-items:center; justify-content: space-between; }
            .brand { font-weight: 800; font-size: 22px; letter-spacing: 0.2px; }
            .brand span { color: var(--brand); }
            .btn { display:inline-block; padding: 12px 18px; border-radius: 10px; text-decoration: none; font-weight: 600; }
            .btn-primary { background: var(--brand); color: #fff; box-shadow: 0 6px 16px rgba(124,58,237,0.35); }
            .btn-primary:hover { background: var(--brand-600); }
            .btn-outline { border: 1px solid #d1d5db; color:#111827; background:#fff; }
            main { flex:1; display:flex; align-items:flex-start; justify-content:center; }
            .hero { text-align:center; padding: 60px 0 40px; }
            .hero-logo { width: 120px; height: 120px; margin: 0 auto 10px; display:flex; align-items:center; justify-content:center; border-radius: 999px; background: #ffffff; box-shadow: 0 2px 20px rgba(0,0,0,0.05); }
            .hero h1 { font-size: clamp(34px, 5vw, 54px); margin: 10px 0 6px; }
            .hero h1 strong { font-weight: 800; }
            .hero h2 { font-size: clamp(28px, 4.5vw, 44px); color: var(--brand); margin: 0 0 18px; font-weight: 800; }
            .hero p { max-width: 720px; margin: 0 auto 28px; color: #4b5563; line-height: 1.6; }
            .actions { display:flex; gap: 14px; justify-content:center; flex-wrap: wrap; }
        </style>
    </head>
    <body>
        <header>
            <div class="container nav">
                <div class="brand">Pim<span>Force</span></div>
                <div>
                    <a class="btn btn-primary" href="{{ route('login') }}">Get Started</a>
                </div>
            </div>
        </header>
        <main>
            <section class="container hero">
                <div class="hero-logo">
                    <img src="/favicon.svg" alt="Logo" width="56" height="56">
                </div>
                <h1><strong>Integrate KatanaPIM</strong></h1>
                <h2>without CHAOS</h2>
                <p>
                    We are KatanaPIM specialists and ensure onboarding and integrations are seamless.
                    We go beyond where other PIM systems stop.
                </p>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('login') }}">Get Started</a>
                    <a class="btn btn-outline" href="#about">Getting acquainted</a>
                </div>
            </section>
        </main>
    </body>
</html>

