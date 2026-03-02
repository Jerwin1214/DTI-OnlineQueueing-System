<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Counter Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 10px;
        }

        .card {
            width: 100%;
            max-width: 380px;
            background: #1e1e1e;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            color: #fff;
        }

        .counter-title {
            font-size: 16px;
            font-weight: 600;
            color: #ffd700;
            margin-bottom: 15px;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .username {
            font-size: 16px;
            color: #00e676;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .buttons button {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
        }

        .btn-next { background: #2979ff; color: #fff; }
        .btn-done { background: #d32f2f; color: #fff; }

        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .stat-box {
            flex: 1;
            background: #2c2c2c;
            padding: 15px 10px;
            border-radius: 10px;
        }

        .stat-box h3 {
            font-size: 14px;
            color: #ccc;
        }

        .stat-box h2 {
            font-size: 22px;
            margin-top: 8px;
        }

        .logout button {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: none;
            border-radius: 10px;
            background: #555;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="counter-title">
        DTIR2 CAGAYAN ONLINE QUEUEING SYSTEM
    </div>

    <h1>
        Counter {{ $counterId }}
        <br>
        <span class="username">
            {{ $fullName ?? $user->user_id }}
        </span>
    </h1>

    <div class="buttons">
        <button class="btn-next" onclick="nextTicket()">NEXT</button>
        <button class="btn-done" onclick="completeTicket()">DONE</button>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>SERVING</h3>
            <h2 id="serving">---</h2>
        </div>

        <div class="stat-box">
            <h3>WAITING</h3>
            <h2 id="waiting">0</h2>
        </div>

        <div class="stat-box">
            <h3>DONE</h3>
            <h2 id="done">---</h2>
        </div>
    </div>

    <div class="logout">
        <form method="POST" action="{{ route('counter.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

</div>

<script>

    // 🔥 IMPORTANT FOR LARAVEL SESSION
    axios.defaults.withCredentials = true;

    // CSRF TOKEN
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
        document.querySelector('meta[name="csrf-token"]').content;

    function nextTicket() {
        axios.post("{{ route('counter.serveTicket') }}")
            .then(() => {
                loadStats();
            })
            .catch(error => {
                alert(error.response?.data?.message ?? 'No waiting tickets.');
            });
    }

    function completeTicket() {
        axios.post("{{ route('counter.completeTicket') }}")
            .then(() => loadStats())
            .catch(error => {
                alert(error.response?.data?.message ?? 'No serving ticket.');
            });
    }

    function loadStats() {
        axios.get("{{ route('counter.status') }}")
            .then(response => {

                document.getElementById('serving').innerText =
                    response.data.serving ?? '---';

                document.getElementById('waiting').innerText =
                    response.data.waiting ?? 0;

                document.getElementById('done').innerText =
                    response.data.last_done ?? '---';
            })
            .catch(err => console.log(err));
    }

    // Auto refresh every 3 seconds
    setInterval(loadStats, 3000);
    loadStats();

</script>

</body>
</html>
