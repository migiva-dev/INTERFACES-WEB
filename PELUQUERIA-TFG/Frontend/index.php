<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barbería - Reservas</title>

<link rel="stylesheet" href="assets/css/styles.css">
<script defer src="assets/js/app.js"></script>
</head>

<body>

<header class="nav">
<h1>✂️ Barbería</h1>

<nav>
<a href="#">Servicios</a>
<a href="#">Reservar</a>
<a href="#">Mis citas</a>
<a href="#">Login</a>
</nav>
</header>


<section class="container">

<h2>Servicios</h2>

<div class="services">

<div class="service-card">
<h3>Corte clásico</h3>
<p>Corte tradicional con tijera o máquina.</p>
<span>30 min - 12€</span>
<button onclick="selectService('Corte clásico')">Reservar</button>
</div>

<div class="service-card">
<h3>Degradado</h3>
<p>Fade profesional con acabado perfecto.</p>
<span>45 min - 15€</span>
<button onclick="selectService('Degradado')">Reservar</button>
</div>

<div class="service-card">
<h3>Corte + Barba</h3>
<p>Servicio completo de barbería.</p>
<span>60 min - 22€</span>
<button onclick="selectService('Corte + Barba')">Reservar</button>
</div>

</div>


</section>


<section class="container reserva">

<h2>Reservar cita</h2>

<label>Servicio</label>
<input id="serviceInput" readonly>

<label>Fecha</label>
<input type="date">

<h3>Horas disponibles</h3>

<div class="slots">
<button>10:00</button>
<button>10:30</button>
<button>11:00</button>
<button>11:30</button>
<button>12:00</button>
<button>12:30</button>
<button>13:00</button>
<button>16:00</button>
<button>16:30</button>
<button>17:00</button>
<button>17:30</button>
<button>18:00</button>
</div>

<button class="confirm">Confirmar reserva</button>

</section>


<footer>

<p>Barbería TFG DAW - Sistema de reservas online</p>

</footer>

</body>
</html>