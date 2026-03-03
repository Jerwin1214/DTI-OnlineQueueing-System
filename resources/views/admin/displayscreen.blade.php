@extends('layouts.admin')

@section('title', 'Display Screen - Smart TV')

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
    height: 100vh;
    width: 100vw;
    overflow: hidden;
    background: #0f172a;
    font-family: Arial, Helvetica, sans-serif;
}

#displayScreenContainer {
    display: flex;
    height: 100%;
    width: 100%;
    gap: 15px;
    padding: 15px;
    box-sizing: border-box;
}

/* LEFT PANEL (VIDEO + CLOCK) */
#videoPanel {
    flex: 3;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

#videoPlayer {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 15px;
}

#dateTimePanel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    color: #1e3a8a;
    padding: 20px;
    border-radius: 15px;
}

#txtClock {
    font-size: 60px;
    font-weight: 900;
}

#txtDate {
    font-size: 30px;
    font-weight: 600;
}

.logo-img {
    height: 100px;
}

/* RIGHT PANEL (COUNTERS) */
#countersPanel {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 25px;
}

#txtTopNowServing {
    font-size: 50px;
    color: white;
    font-weight: 900;
    text-align: center;
}

.counterBox {
    background: #1e40af;
    padding: 25px;
    border-radius: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.counterLabel {
    color: white;
    font-size: 32px;
    font-weight: bold;
}

.counterNumber {
    color: #facc15;
    font-size: 55px;
    font-weight: 900;
}
</style>

<div id="displayScreenContainer">

    <!-- LEFT SIDE -->
    <div id="videoPanel">

        <video id="videoPlayer" autoplay loop muted playsinline>
            <source src="{{ asset('storage/VIDEOFORQUEUING.mp4') }}" type="video/mp4">
        </video>

        <div id="dateTimePanel">

            <img src="{{ asset('storage/logoDTI.png') }}" class="logo-img">

            <div style="text-align:center;">
                <div id="txtClock"></div>
                <div id="txtDate"></div>
            </div>

            <img src="{{ asset('storage/bagongpilipinas2.png') }}" class="logo-img">

        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div id="countersPanel">

        <div id="txtTopNowServing">NOW SERVING</div>

        @foreach($selectedCounters as $i)
        <div class="counterBox">
            <div class="counterLabel">Counter {{ $i }}</div>
            <div id="txtServingNumber{{ $i }}" class="counterNumber">C000</div>
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

// 🔥 CLOCK FUNCTION
function updateClock() {
    const now = new Date();

    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    const time = now.toLocaleTimeString('en-US');
    const date = now.toLocaleDateString('en-US', options);

    document.getElementById('txtClock').innerText = time;
    document.getElementById('txtDate').innerText = date;
}

setInterval(updateClock, 1000);
updateClock();


// 🔥 PLAY SOUND
function playSound() {
    const sound = document.getElementById('nextSound');
    sound.currentTime = 0;
    sound.play().catch(()=>{});
}


// 🔥 FETCH COUNTERS
function fetchCounters() {

    fetch("{{ route('admin.getCounters') }}", { cache: "no-store" })
    .then(response => response.json())
    .then(data => {

        @foreach($selectedCounters as $i)

        const counterId = {{ $i }};
        const element = document.getElementById('txtServingNumber{{ $i }}');

        if (!element) return;

        let newTicket = "C000";

        if (data[counterId] && data[counterId].ticket) {
            newTicket = data[counterId].ticket;
        }

        if (previousTickets[counterId] !== newTicket) {

            if (previousTickets[counterId] !== undefined) {
                playSound();
            }

            previousTickets[counterId] = newTicket;
        }

        element.innerText = newTicket;

        @endforeach

    })
    .catch(error => {
        console.log("Fetch error:", error);
    });
}

// 🔥 AUTO REFRESH EVERY 2 SECONDS
setInterval(fetchCounters, 2000);
fetchCounters();

</script>
@endsection
