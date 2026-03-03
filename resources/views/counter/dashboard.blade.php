<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Counter Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 15px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 22px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            color: #fff;
        }

        .counter-title {
            font-size: 14px;
            font-weight: 600;
            color: #facc15;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .username {
            font-size: 15px;
            color: #22c55e;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }

        .buttons button {
            flex: 1;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn-next {
            background: #2563eb;
            color: #fff;
        }

        .btn-next:hover {
            background: #1d4ed8;
        }

        .btn-done {
            background: #dc2626;
            color: #fff;
        }

        .btn-done:hover {
            background: #b91c1c;
        }

        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }

        .stat-box {
            flex: 1;
            background: rgba(255,255,255,0.08);
            padding: 14px 8px;
            border-radius: 12px;
        }

        .stat-box h3 {
            font-size: 12px;
            color: #cbd5e1;
        }

        .stat-box h2 {
            font-size: 20px;
            margin-top: 6px;
        }

        .logout button {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 12px;
            background: #334155;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
        }

        .logout button:hover {
            background: #475569;
        }

        /* ================= MODAL ================= */

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-content {
            background: white;
            color: #111;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            width: 90%;
            max-width: 320px;
            animation: fadeIn 0.2s ease;
        }

        .modal-content h3 {
            margin-bottom: 10px;
        }

        .modal-content button {
            margin-top: 15px;
            padding: 8px 18px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            .card {
                padding: 18px;
            }

            h1 {
                font-size: 18px;
            }

            .stat-box h2 {
                font-size: 18px;
            }
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

<!-- SUCCESS MODAL -->
<div class="modal" id="successModal">
    <div class="modal-content">
        <h3 id="modalMessage">Success</h3>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<script>

axios.defaults.withCredentials = true;

axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').content;

const STATUS_URL = "{{ route('counter.status') }}";
const NEXT_URL = "{{ route('counter.serveTicket') }}";
const DONE_URL = "{{ route('counter.completeTicket') }}";

function showModal(message) {
    document.getElementById('modalMessage').innerText = message;
    document.getElementById('successModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('successModal').style.display = 'none';
}

async function loadStats() {
    try {
        const response = await axios.get(STATUS_URL);

        document.getElementById('serving').innerText =
            response.data.serving ?? '---';

        document.getElementById('waiting').innerText =
            response.data.waiting ?? 0;

        document.getElementById('done').innerText =
            response.data.last_done ?? '---';

    } catch (error) {
        console.log(error);
    }
}

async function nextTicket() {
    try {
        const res = await axios.post(NEXT_URL);
        loadStats();
        showModal(res.data.message || "Ticket called successfully!");
    } catch (error) {
        showModal(error.response?.data?.message || "No waiting tickets.");
    }
}

async function completeTicket() {
    try {
        const res = await axios.post(DONE_URL);
        loadStats();
        showModal(res.data.message || "Ticket completed successfully!");
    } catch (error) {
        showModal(error.response?.data?.message || "No serving ticket.");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    loadStats();
    setInterval(loadStats, 3000);
});

</script>

</body>
</html>
