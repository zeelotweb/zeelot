<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') — ZeelotWeb</title>

        <link rel="icon" href="/favicon.png" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

        <style>
            :root {
                --bg: #0a0f16;
                --surface: rgba(255, 255, 255, 0.03);
                --border: rgba(255, 255, 255, 0.1);
                --text: #ffffff;
                --text-muted: #94a3b8;
                --accent: #3577ca;
                --accent-soft: rgba(53, 119, 202, 0.1);
                --accent-border: rgba(53, 119, 202, 0.3);
            }

            * { box-sizing: border-box; }

            html, body {
                margin: 0;
                height: 100%;
                background: var(--bg);
                color: var(--text);
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            }

            body {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                position: relative;
                overflow: hidden;
                text-align: center;
            }

            .grid-bg {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(to right, rgba(255,255,255,0.04) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,255,255,0.04) 1px, transparent 1px);
                background-size: 40px 40px;
                mask-image: radial-gradient(ellipse 60% 50% at 50% 40%, black 40%, transparent 100%);
                pointer-events: none;
            }

            .logo {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 3rem;
                text-decoration: none;
                color: var(--text);
                font-weight: 700;
                font-size: 1.05rem;
            }

            .logo img { height: 2rem; width: 2rem; border-radius: 0.375rem; }

            .code {
                position: relative;
                font-family: 'Instrument Sans', ui-sans-serif, sans-serif;
                font-weight: 700;
                font-size: clamp(4rem, 12vw, 7rem);
                line-height: 1;
                color: var(--accent);
                letter-spacing: -0.02em;
            }

            .message {
                position: relative;
                font-size: 1.5rem;
                font-weight: 600;
                margin-top: 0.75rem;
            }

            .detail {
                position: relative;
                color: var(--text-muted);
                font-size: 1rem;
                max-width: 28rem;
                margin: 0.75rem auto 0;
                line-height: 1.6;
            }

            .actions {
                position: relative;
                display: flex;
                gap: 0.75rem;
                margin-top: 2rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.7rem 1.4rem;
                border-radius: 0.65rem;
                font-size: 0.9rem;
                font-weight: 600;
                text-decoration: none;
                transition: transform 0.15s, background 0.15s;
            }

            .btn-primary {
                background: var(--accent);
                color: white;
            }
            .btn-primary:hover { background: #4586d4; }

            .btn-secondary {
                background: var(--surface);
                color: var(--text);
                border: 1px solid var(--border);
            }
            .btn-secondary:hover { background: rgba(255,255,255,0.06); }
        </style>
    </head>
    <body>
        <div class="grid-bg"></div>

        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('7.jpg') }}" alt="">
            ZeelotWeb
        </a>

        <div class="code">@yield('code')</div>
        <div class="message">@yield('message')</div>
        <div class="detail">@yield('detail', "Something didn't go as planned. Let's get you back on track.")</div>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
            <a href="mailto:hello@zeelotweb.com" class="btn btn-secondary">Contact us</a>
        </div>
    </body>
</html>
