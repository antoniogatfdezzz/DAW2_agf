<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decisiones - Karma</title>
    <style>
        html,body{height:100%;margin:0;font-family:Arial, Helvetica, sans-serif}
        body{
            background: url('img/bg.png') center/cover no-repeat fixed;
            display:flex;align-items:center;justify-content:center;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.8)
        }
        .site-title{position:fixed;top:12px;left:50%;transform:translateX(-50%);font-size:28px;padding:8px 14px;background:rgba(0,0,0,0.36);border-radius:8px;color:#fff;z-index:1000;box-shadow:0 4px 10px rgba(0,0,0,0.45);font-weight:700}
        .card{background:rgba(171, 171, 171, 0.55);padding:24px;border-radius:8px;max-width:920px;width:95%}
        h1{color:#1111; margin:0 0 8px}
        p.lead{margin:0 0 16px;color:#ddd}
        .actions{display:grid;grid-template-columns:repeat(5,1fr);gap:10px}
        .action{background:#222;border:2px solid rgba(255,255,255,0.06);padding:12px;border-radius:6px;cursor:pointer;text-align:center}
        .action.good{background:linear-gradient(180deg,#1a7a3b,#145b2c)}
        .action.bad{background:linear-gradient(180deg,#7a1a1a,#5b1414)}
        .action small{display:block;color:rgba(255,255,255,0.9);font-weight:700}
        .meta{display:flex;gap:12px;margin-top:14px;align-items:center}
        .btn{display:inline-block;background:#fff;color:#111;padding:8px 12px;border-radius:6px;text-decoration:none}
        .btn.secondary{background:transparent;color:#fff;border:1px solid rgba(255,255,255,0.15)}
        .foot{margin-top:12px;color:#ccc;font-size:14px}
        @media(max-width:700px){.actions{grid-template-columns:repeat(2,1fr)}}
    </style>
</head>
<body>
    <header class="site-title">La Perla Negra</header>
    <div class="card">
        <h1>Elige una acción</h1>
        <p class="lead">Cada acción cambia tu "Karma". Las buenas suman, las malas restan.</p>

        <div class="actions">
            <button class="action bad" data-name="matar" data-value="-100"><small>Matar</small><span>-100</span></button>
            <button class="action bad" data-name="robar" data-value="-50"><small>Robar</small><span>-50</span></button>
            <button class="action bad" data-name="abusar" data-value="-70"><small>Abusar</small><span>-70</span></button>
            <button class="action bad" data-name="quemar" data-value="-80"><small>Quemar</small><span>-80</span></button>
            <button class="action bad" data-name="embaucar" data-value="-60"><small>Embaucar</small><span>-60</span></button>

            <button class="action good" data-name="donar" data-value="50"><small>Donar</small><span>+50</span></button>
            <button class="action good" data-name="salvar" data-value="100"><small>Salvar</small><span>+100</span></button>
            <button class="action good" data-name="curar" data-value="40"><small>Curar</small><span>+40</span></button>
            <button class="action good" data-name="ayudar" data-value="30"><small>Ayudar</small><span>+30</span></button>
            <button class="action good" data-name="enseñar" data-value="20"><small>Enseñar</small><span>+20</span></button>
        </div>

        <div class="meta">
            <div id="status" class="foot" style="margin-left:8px">Karma actual: <span id="karmaValue">0</span></div>
        </div>
    </div>

    <script>
        function getCookie(name){
            const v = document.cookie.match('(^|;)\\s*'+name+'\\s*=\\s*([^;]+)');
            return v ? decodeURIComponent(v.pop()) : null;
        }
        function setCookie(name,value,days){
            let expires = '';
            if(typeof days === 'number'){
                const d = new Date(); d.setTime(d.getTime()+days*24*60*60*1000);
                expires = '; expires=' + d.toUTCString();
            }
            document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
        }
        function deleteCookie(name){ document.cookie = name+'=; Max-Age=0; path=/'; }

        function addAction(name, value){
            value = parseInt(value,10)||0;

            const deathRoll = Math.random();
            const died = deathRoll < 0.10; // 10%

            let karma = parseInt(getCookie('karma')) || 0;

            let acciones = [];
            const raw = getCookie('acciones');
            if(raw){
                try{ acciones = JSON.parse(raw); }catch(e){ acciones = []; }
            }

            if(died){
                const deathValue = -1000;
                karma += deathValue;
                setCookie('dead','1', 365);

                setCookie('karma', String(karma), 365);
                setCookie('acciones', JSON.stringify(acciones), 365);

                window.location.href = 'paginas/resultado.php';
                return;
            } else {
                karma += value;
                acciones.push({accion:name,valor:value,ts:Date.now()});
            }

            setCookie('karma', String(karma), 365);
            setCookie('acciones', JSON.stringify(acciones), 365);

            updateStatus();
        }

        function updateStatus(){
            const k = parseInt(getCookie('karma')) || 0;
            const el = document.getElementById('karmaValue');
            if(el) el.textContent = k;
        }

        document.querySelectorAll('.action').forEach(btn=>{
            btn.addEventListener('click', e=>{
                const name = btn.getAttribute('data-name');
                const value = btn.getAttribute('data-value');
                addAction(name,value);
            });
        });

        (function init(){
            updateStatus();
            if(getCookie('dead') === '1'){
                document.querySelectorAll('.action').forEach(b=>{ b.disabled = true; b.style.opacity = '0.6'; b.style.cursor = 'not-allowed'; });
                const lead = document.querySelector('.lead');
                if(lead) lead.textContent = 'Has muerto. No puedes realizar más acciones.';
            }
        })();

    </script>
</body>
</html>