@extends('layouts.admin')

@section('title', 'Display Screen - Google')

@php
$hideSidebar = true;
$hideTopbar = true;
$selectedCounters = request()->query('counters', range(1,5));

if(!is_array($selectedCounters)){
    $selectedCounters = explode(',', $selectedCounters);
}
@endphp

@section('content')

<style>

html, body{
margin:0;
padding:0;
height:100%;
width:100%;
overflow:hidden;
background:#1f2937;
font-family:Arial, Helvetica, sans-serif;
}

/* MAIN SCREEN */

#displayScreenContainer{
display:flex;
height:100%;
width:100%;
gap:1rem;
padding:0.5rem;
box-sizing:border-box;
}

/* VIDEO SIDE */

#videoPanel{
flex:3;
position:relative;
height:100%;
}

#videoPlayer{
width:100%;
height:100%;
object-fit:cover;
border-radius:10px;
}

/* FULLSCREEN BUTTON */

#btnFullscreen{
position:absolute;
top:10px;
left:10px;
font-size:2rem;
color:white;
background:transparent;
border:none;
cursor:pointer;
}

/* RIGHT PANEL */

#countersPanel{
flex:1;
display:flex;
flex-direction:column;
height:100%;
gap:1rem;
}

/* LOGO AREA */

#logoPanel{
display:flex;
justify-content:space-between;
align-items:center;
}

.logo-img{
height:90px;
object-fit:contain;
}

/* NOW SERVING TITLE */

#txtTopNowServing{
font-size:2.5rem;
color:white;
font-weight:bold;
text-align:center;
}

/* COUNTERS */

#counterList{
flex:1;
display:flex;
flex-direction:column;
gap:1rem;
}

.counterBox{
display:flex;
justify-content:space-between;
align-items:center;
background:#1e3a8a;
padding:1rem 2rem;
border-radius:0.75rem;
}

.counterLabel{
color:white;
font-size:1.5rem;
}

.counterNumber{
color:#facc15;
font-size:2rem;
font-weight:bold;
}

/* DATE TIME PANEL */

#dateTimePanel{
background:white;
border-radius:0.75rem;
padding:1rem;
text-align:center;
margin-top:auto;
}

#txtClock{
font-size:3.5rem;
font-weight:800;
color:#1e40af;
}

#txtDate{
font-size:1.8rem;
font-weight:600;
color:#1e40af;
}

</style>

<div id="displayScreenContainer">

<!-- VIDEO PANEL -->
<div id="videoPanel">

<video id="videoPlayer" autoplay loop muted playsinline>
<source src="{{ asset('storage/VIDEOFORQUEUING.mp4') }}" type="video/mp4">
</video>

<button id="btnFullscreen">⛶</button>

</div>

<!-- RIGHT PANEL -->
<div id="countersPanel">

<!-- LOGOS -->
<div id="logoPanel">
<img src="{{ asset('storage/logoDTI.png') }}" class="logo-img">
<img src="{{ asset('storage/bagongpilipinas2.png') }}" class="logo-img">
</div>

<h1 id="txtTopNowServing">NOW SERVING</h1>

<div id="counterList">

@foreach($selectedCounters as $i)

<div class="counterBox">

<span class="counterLabel">Counter {{ $i }}</span>

<span id="txtServingNumber{{ $i }}" class="counterNumber">C000</span>

</div>

@endforeach

</div>

<!-- CLOCK -->
<div id="dateTimePanel">

<div id="txtClock"></div>
<div id="txtDate"></div>

</div>

</div>

</div>

<audio id="nextSound" preload="auto">
<source src="{{ asset('storage/doorbell-223669.mp3') }}" type="audio/mpeg">
</audio>

@endsection


@section('scripts')

<script>

/* COUNTER DATA */

let selectedCounters = @json($selectedCounters);
let previousTickets = {};
let firstLoad = true;

/* AUDIO SETUP */

const sound = document.getElementById('nextSound');

document.addEventListener('click', function unlockAudio(){

sound.play().then(()=>{
sound.pause();
sound.currentTime = 0;
document.removeEventListener('click', unlockAudio);
}).catch(()=>{});

});

/* CLOCK */

function updateClock(){

const now = new Date();

const hours = now.getHours()%12 || 12;
const minutes = now.getMinutes().toString().padStart(2,'0');
const seconds = now.getSeconds().toString().padStart(2,'0');

const ampm = now.getHours()>=12 ? 'PM':'AM';

document.getElementById('txtClock').innerText =
`${hours}:${minutes}:${seconds} ${ampm}`;

document.getElementById('txtDate').innerText =
now.toDateString();

}

setInterval(updateClock,1000);
updateClock();

/* FULLSCREEN */

document.getElementById('btnFullscreen')
.addEventListener('click',function(){

if(!document.fullscreenElement){
document.documentElement.requestFullscreen();
}else{
document.exitFullscreen();
}

});

/* PLAY SOUND */

function playSound(){

sound.pause();
sound.currentTime = 0;

sound.play().catch(()=>{});

}

/* FETCH COUNTERS */

function fetchCounters(){

fetch("{{ route('admin.getCounters') }}",{cache:"no-store"})

.then(res=>res.json())

.then(data=>{

selectedCounters.forEach(function(counterId){

let el = document.getElementById('txtServingNumber'+counterId);

if(!el) return;

let newTicket = 'C000';

if(data[counterId] && data[counterId].ticket){
newTicket = data[counterId].ticket;
}

if(previousTickets[counterId] !== undefined
&& previousTickets[counterId] !== newTicket
&& !firstLoad){
playSound();
}

previousTickets[counterId] = newTicket;

el.innerText = newTicket;

});

firstLoad = false;

})

.catch(err=>console.log("Display fetch error:",err));

}

setInterval(fetchCounters,2000);
fetchCounters();

</script>

@endsection
