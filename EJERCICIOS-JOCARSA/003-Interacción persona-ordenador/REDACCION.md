## 1. Introducción

En el contexto del diseño web y la interacción persona-ordenador, la animación es un recurso clave para dar vida a interfaces y mejorar la experiencia de usuario. Un movimiento fluido puede simular procesos, representar dinámicas reales o, como en este caso, recrear un escenario lúdico inspirado en el **fútbol**.

En este ejercicio he combinado el deporte con la tecnología, representando a un **jugador de fútbol en movimiento automático dentro de un campo**, utilizando únicamente **Canvas de HTML5** y **JavaScript puro**.  
La finalidad es comprender cómo programar un bucle de animación, controlar posiciones en el lienzo y dar contexto visual mediante el dibujo de formas, sombras y detalles (como el balón).

---

## 2. Desarrollo detallado y preciso

He creado un documento HTML que incluye un `<canvas>` de 512x512 píxeles, sobre el cual dibujo un campo y un jugador que avanza hacia la derecha de manera continua.

### Pasos técnicos:

**1. Lienzo y contexto 2D**

```js
const lienzo = document.getElementById("lienzo");
const ctx = lienzo.getContext("2d");
```

- `getElementById`: obtiene el canvas.
- `getContext("2d")`: devuelve el contexto para poder dibujar.

**2. Campo de fútbol**

- Rectángulo con degradado verde como césped.
- Líneas reglamentarias: perímetro, línea media y círculo central.
- Métodos usados: `fillRect`, `strokeRect`, `moveTo`, `lineTo`, `arc`.

**3. Jugador**

- Círculo rojo que representa al jugador.
- Sombra elíptica debajo (`globalAlpha` para transparencia).
- Balón pequeño delante del jugador.

**4. Estado inicial y movimiento**

```js
let x = 50, y = 256;
const velocidad = 2;

if (x - 20 > lienzo.width) {
  x = -20;
}
```

- Estado inicial en (x=50, y=256).
- La posición x aumenta en cada frame (velocidad=2).
- Cuando desaparece por la derecha, reaparece por la izquierda usando el radio del círculo.

**5. Bucle de animación**

```js
function bucle() {
  ctx.clearRect(0, 0, lienzo.width, lienzo.height);
  dibujarCampo();
  dibujarJugador(x, y, "red");

  x += velocidad;
  if (x - 20 > lienzo.width) x = -20;

  setTimeout(bucle, 20);
}

bucle();
```

- `clearRect`: limpia el lienzo antes de redibujar.
- `dibujarCampo`: pinta el campo en cada frame.
- `dibujarJugador`: coloca al jugador en su posición actual.
- `setTimeout`: ejecuta la función cada 20 ms (~50 FPS).

### Verificación técnica

✔ El movimiento es fluido.  
✔ No aparecen "fantasmas" gracias a `clearRect`.  
✔ Se considera el radio del jugador al reaparecer.  
✔ Se restaura `globalAlpha` tras la sombra.

---

## 3. Aplicación práctica con ejemplo claro

Aquí muestro el documento HTML completo con la animación funcional:

```html
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fútbol en Canvas</title>
    <style>
      body { display:flex; min-height:100vh; align-items:center; justify-content:center; margin:0; background:#0b1020; font-family:sans-serif; }
      .wrap { text-align:center; color:#e8eef9; }
      canvas { background:#0a7f2e; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.4); }
      p { opacity:.8; }
    </style>
  </head>
  <body>
    <div class="wrap">
      <canvas id="lienzo" width="512" height="512"></canvas>
      <p>Un jugador se mueve automáticamente. (Canvas + JavaScript puro)</p>
    </div>

    <script>
      const lienzo = document.getElementById("lienzo");
      const ctx = lienzo.getContext("2d");

      function dibujarCampo() {
        const w = lienzo.width, h = lienzo.height;
        const grad = ctx.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, "#0a7f2e");
        grad.addColorStop(1, "#096d27");
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, w, h);

        ctx.strokeStyle = "#ffffff";
        ctx.lineWidth = 3;
        ctx.strokeRect(10, 10, w - 20, h - 20);
        ctx.beginPath(); ctx.moveTo(w/2, 10); ctx.lineTo(w/2, h-10); ctx.stroke();
        ctx.beginPath(); ctx.arc(w/2, h/2, 40, 0, Math.PI * 2); ctx.stroke();
      }

      function dibujarJugador(x, y, color = "red") {
        ctx.globalAlpha = 0.15;
        ctx.beginPath();
        ctx.ellipse(x, y + 18, 24, 10, 0, 0, Math.PI * 2);
        ctx.fillStyle = "#000"; ctx.fill();
        ctx.globalAlpha = 1;

        ctx.beginPath();
        ctx.arc(x, y, 20, 0, Math.PI * 2);
        ctx.fillStyle = color; ctx.fill();

        ctx.beginPath();
        ctx.arc(x + 26, y + 4, 6, 0, Math.PI * 2);
        ctx.fillStyle = "#f5f5f5"; ctx.fill();
        ctx.beginPath();
        ctx.arc(x + 26, y + 4, 6, 0, Math.PI * 2);
        ctx.strokeStyle = "#222"; ctx.lineWidth = 1; ctx.stroke();
      }

      let x = 50, y = 256;
      const velocidad = 2;

      function bucle() {
        ctx.clearRect(0, 0, lienzo.width, lienzo.height);
        dibujarCampo();
        dibujarJugador(x, y, "red");

        x += velocidad;
        if (x - 20 > lienzo.width) x = -20;

        setTimeout(bucle, 20);
      }

      bucle();
    </script>
  </body>
</html>
```

### Posibles errores comunes y cómo evitarlos:

- **Olvidar `clearRect`**: deja rastros del jugador → limpiar siempre cada frame.
- **No considerar el radio**: el círculo se corta → usar `x - radio`.
- **`globalAlpha` mal gestionado**: si no se restaura, todo sale transparente.
- **Velocidad ligada al FPS**: en máquinas rápidas/capadas varía → solución: `requestAnimationFrame` + delta de tiempo.

---

## 4. Conclusión breve

Con esta actividad he aprendido a combinar HTML5 Canvas y JavaScript para crear animaciones simples pero efectivas. Entendí:

- Cómo controlar el estado de un objeto en movimiento.
- Cómo estructurar un bucle de actualización.
- La importancia de limpiar cada frame para evitar residuos visuales.

Al enmarcarlo en un campo de fútbol, la animación se vuelve más atractiva y contextualizada, reforzando conceptos de Interacción Persona-Ordenador mediante retroalimentación visual inmediata y coherente.

Además, exploré:

- La diferencia entre usar `setTimeout` y `requestAnimationFrame`.
- La aplicación de sombras y degradados para mejorar la percepción visual.
- La idea de extender el ejercicio hacia escenarios con múltiples jugadores o control por teclado.