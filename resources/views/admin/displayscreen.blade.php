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
    background-color: #ffffff;
    color: #1e40af;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-family: sans-serif;
}

#countersPanel {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
    padding-top: 0.5rem;
    width: 100%;
}

#txtTopNowServing {
    font-size: 2.5rem;
    color: white;
    font-weight: bold;
    margin: 0;
    text-align: right;
}

.counterBox {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1e3a8a;
    padding: 1rem 2rem;
    border-radius: 0.75rem;
    width: 100%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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
    cursor: pointer;
    z-index: 50;
    background: transparent;
    border: none;
}
</style>

<div id="displayScreenContainer">

    <!-- VIDEO -->
    <div id="videoPanel">

        <!-- ✅ FIXED VIDEO FOR RENDER -->
        <video id="videoPlayer"
               autoplay
               loop
               muted
               playsinline
               preload="auto">

            <source src="{{ asset('storage/VIDEOFORQUEUING.mp4') }}"
                    type="video/mp4">

            Your browser does not support the video tag.
        </video>

        <button id="btnFullscreen">⛶</button>

        <!-- DATE / TIME / LOGOS -->
        <div id="dateTimePanel">

            <img src="{{ asset('storage/logoDTI.png') }}"
                 class="h-24 object-contain"
                 alt="Logo Left">

            <div class="text-center">
                <div id="txtClock" class="text-5xl font-bold"></div>
                <div id="txtDate" class="text-2xl mt-1"></div>
            </div>

            <img src="{{ asset('storage/bagongpilipinas2.png') }}"
                 class="h-24 object-contain"
                 alt="Logo Right">

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

<!-- AUDIO -->
<audio id="nextSound" preload="auto">
    <source src="{{ asset('storage/doorbell-223669.mp3') }}"
            type="audio/mpeg">
</audio>

@endsection

@section('scripts')
<script>

// CLOCK
function updateClock() {
    const now = new Date();
    const hours = now.getHours() % 12 || 12;
    const minutes = now.getMinutes().toString().padStart(2,'0');
    const seconds = now.getSeconds().toString().padStart(2,'0');
    const ampm = now.getHours() >= 12 ? 'PM' : 'AM';

    document.getElementById('txtClock').textContent =
        `${hours}:${minutes}:${seconds} ${ampm}`;

    document.getElementById('txtDate').textContent =
        now.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
}
setInterval(updateClock, 1000);
updateClock();


// FULLSCREEN
document.getElementById('btnFullscreen').addEventListener('click', () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
});


// FETCH COUNTERS
function fetchCounters() {
    fetch("{{ route('admin.getCounters') }}", {
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(res => res.json())
    .then(data => {

        @foreach($selectedCounters as $i)
        const el{{ $i }} = document.getElementById('txtServingNumber{{ $i }}');

        if (el{{ $i }} && data[{{ $i }}]) {

            let newTicket = 'C000';

            if (data[{{ $i }}].ticket !== null) {
                const ticketNum = parseInt(data[{{ $i }}].ticket);
                if (!isNaN(ticketNum)) {
                    newTicket = 'C' + ticketNum.toString().padStart(3,'0');
                }
            }

            el{{ $i }}.textContent = newTicket;
        }
        @endforeach

    })
    .catch(err => console.error("Counter fetch error:", err));
}

setInterval(fetchCounters, 2000);
fetchCounters();

</script>
@endsection
