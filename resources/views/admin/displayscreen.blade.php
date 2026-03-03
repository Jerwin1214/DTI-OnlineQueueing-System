@extends('layouts.admin')

@section('title', 'Display Screen - Google')

@php
    $hideSidebar = true;
    $hideTopbar = true;
    $selectedCounters = request()->query('counters', range(1,5));
@endphp

@section('content')
<style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    overflow: hidden;
    background-color: #1f2937;
}

#displayScreenContainer {
    display: flex;
    height: 100%;
    width: 100%;
    gap: 1rem;
    padding: 0.5rem;
    box-sizing: border-box;
}

#videoPanel {
    flex: 3;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
}

#videoPlayer {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.5rem;
}

#dateTimePanel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    color: #1e40af;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
}

#countersPanel {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

#txtTopNowServing {
    font-size: 2.5rem;
    color: white;
    font-weight: bold;
    text-align: right;
}

.counterBox {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #1e3a8a;
    padding: 1rem 2rem;
    border-radius: 0.75rem;
    width: 100%;
}

.counterLabel {
    color: white;
    font-size: 1.5rem;
}

.counterNumber {
    color: #facc15;
    font-size: 2rem;
    font-weight: bold;
}

#btnFullscreen {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    font-size: 2rem;
    color: white;
    background: transparent;
    border: none;
    cursor: pointer;
}
</style>

<div id="displayScreenContainer">

    <!-- VIDEO -->
    <div id="videoPanel">

        <video id="videoPlayer"
               autoplay
               loop
               muted
               playsinline>

            <source src="{{ asset('storage/VIDEOFORQUEUING.mp4') }}" type="video/mp4">
        </video>

        <button id="btnFullscreen">⛶</button>

        <div id="dateTimePanel">
            <div id="txtClock"></div>
            <div id="txtDate"></div>
        </div>

    </div>

    <!-- COUNTERS -->
    <div id="countersPanel">
        <h1 id="txtTopNowServing">NOW SERVING</h1>

        @foreach($selectedCounters as $i)
        <div class="counterBox">
            <span class="counterLabel">Counter {{ $i }}:</span>
            <span id="txtServingNumber{{ $i }}" class="counterNumber">C000</span>
        </div>
        @endforeach
    </div>

</div>

<audio id="nextSound" preload="auto">
    <source src="{{ asset('storage/doorbell-223669.mp3') }}" type="audio/mpeg">
</audio>

@endsection

@section('scripts')
<script>

let previousTickets = {};

// CLOCK
function updateClock() {
    const now = new Date();
    const hours = now.getHours() % 12 || 12;
    const minutes = now.getMinutes().toString().padStart(2,'0');
    const seconds = now.getSeconds().toString().padStart(2,'0');
    const ampm = now.getHours() >= 12 ? 'PM' : 'AM';

    document.getElementById('txtClock').innerText =
        `${hours}:${minutes}:${seconds} ${ampm}`;

    document.getElementById('txtDate').innerText =
        now.toDateString();
}

setInterval(updateClock, 1000);
updateClock();


// FULLSCREEN
document.getElementById('btnFullscreen')
.addEventListener('click', () => {

    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
});


// PLAY SOUND
function playSound() {
    const sound = document.getElementById('nextSound');
    sound.currentTime = 0;
    sound.play().catch(() => {});
}


// FETCH COUNTERS WITH SOUND DETECTION
function fetchCounters() {

    fetch("{{ route('admin.getCounters') }}")
        .then(res => res.json())
        .then(data => {

            @foreach($selectedCounters as $i)

            const counterId = {{ $i }};
            const el = document.getElementById('txtServingNumber{{ $i }}');

            if (!el) return;

            let newTicket = 'C000';

            if (data[counterId] && data[counterId].ticket) {
                newTicket = data[counterId].ticket;
            }

            // 🔥 Play sound only if ticket changed
            if (previousTickets[counterId] !== newTicket) {

                // Don't play on first load
                if (previousTickets[counterId] !== undefined) {
                    playSound();
                }

                previousTickets[counterId] = newTicket;
            }

            el.innerText = newTicket;

            @endforeach

        })
        .catch(err => console.log("Display fetch error:", err));
}

setInterval(fetchCounters, 2000);
fetchCounters();

</script>
@endsection
