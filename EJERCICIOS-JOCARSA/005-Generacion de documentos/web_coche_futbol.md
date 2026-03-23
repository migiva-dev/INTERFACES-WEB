## 1. Introducción

En el contexto del desarrollo web con **HTML5**, este ejercicio consiste en crear una página estática para **Jose Vicente**, aficionado a los coches y al fútbol. La página mostrará **noticias de automóviles** y una lista de **partidos de fútbol** a los que podría asistir.  
El objetivo es practicar la **estructura semántica básica**, el uso de **listas desordenadas** y la correcta **cerradura de etiquetas**, manteniendo un estilo interno mínimo sin librerías externas.

---

## 2. Desarrollo detallado y preciso

Se parte de un **esqueleto HTML5** proporcionado: `<!doctype html>`, elementos `<html>`, `<head>`, `<body>`, un `<header>` con el título del sitio y un `<main>` con el comentario "Aquí irá el contenido específico".  
Sobre esa base se añaden dos secciones dentro de `<main>`:

1. Un **encabezado secundario** (`<h2>`) con el texto **"Noticias de Automóviles"**, seguido de una **lista desordenada** (`<ul>`) con **tres noticias**, cada una dentro de un `<li>` que contiene un `<article>` con **título (`<h3>`)** y **párrafo (`<p>`)** breves.
2. Un segundo **encabezado secundario** (`<h2>`) con el texto **"Proximos Partidos de Fútbol"** (tal cual, sin tilde en “Proximos”, para respetar el enunciado), seguido de otra **lista desordenada** con **tres partidos**. Cada partido incluye **equipo local**, **equipo visitante** y **fecha** en el `<h3>`, además de un **párrafo** breve.

**Restricciones cumplidas:** no se usan librerías externas, el código es HTML5 y **todas las etiquetas** se cierran correctamente.

---

## 3. Aplicación práctica con ejemplo claro

A continuación, el archivo **web_coche_futbol.html** completo que cumple todos los requisitos:

```html
<!doctype html>
<html>
  <head>
    <title>Web de Jose Vicente</title>
    <style>
      body { background: #f4f4f9; font-family: Arial, sans-serif; }
      header { background: #333; color: white; padding: 10px 20px; text-align: center; }
      main { padding: 20px; }
    </style>
  </head>
  <body>
    <header>
      <h1>Web de Jose Vicente</h1>
    </header>
    <main>
      <!-- Aquí irá el contenido específico -->

      <h2>Noticias de Automóviles</h2>
      <ul>
        <li>
          <article>
            <h3>Nuevo compacto eléctrico con mayor autonomía</h3>
            <p>El último modelo urbano ofrece hasta 500 km por carga y asistentes avanzados para ciudad.</p>
          </article>
        </li>
        <li>
          <article>
            <h3>Actualización de software reduce consumo en autopista</h3>
            <p>Una mejora OTA optimiza la gestión del motor y suaviza la aceleración en viajes largos.</p>
          </article>
        </li>
        <li>
          <article>
            <h3>SUV híbrido ligero consigue la máxima calificación en seguridad</h3>
            <p>El modelo destaca por sus sistemas ADAS y una estructura reforzada de alta resistencia.</p>
          </article>
        </li>
      </ul>

      <h2>Proximos Partidos de Fútbol</h2>
      <ul>
        <li>
          <article>
            <h3>Valencia CF vs. Real Betis — 2025-10-25</h3>
            <p>Partido en Mestalla con gran ambiente; recomendable llegar con antelación.</p>
          </article>
        </li>
        <li>
          <article>
            <h3>Levante UD vs. CD Tenerife — 2025-11-02</h3>
            <p>Encuentro clave por los puestos altos; acceso cómodo en transporte público.</p>
          </article>
        </li>
        <li>
          <article>
            <h3>Villarreal CF vs. Valencia CF — 2025-11-09</h3>
            <p>Derbi regional de alta intensidad; ideal para asistir con amigos.</p>
          </article>
        </li>
      </ul>
    </main>
  </body>
</html>
```

**Errores comunes y cómo evitarlos**
- Colocar `<li>` fuera de `<ul>` → Los elementos de lista **siempre** dentro de una lista.
- Saltar niveles de encabezado (p. ej., de `<h1>` a `<h4>`) → Mantener jerarquía: `h1` (título) → `h2` (secciones) → `h3` (ítems).
- Olvidar cerrar etiquetas (`</li>`, `</article>`) → Revisar cuidadosamente el cierre de todos los elementos.
- Cambiar literal del enunciado → Mantener **"Noticias de Automóviles"** y **"Proximos Partidos de Fútbol"** exactamente como se solicita.

---

## 4. Conclusión breve

Se ha construido una página **HTML5** limpia y semántica que organiza noticias y eventos deportivos en dos bloques claros. El ejercicio refuerza el uso correcto de **encabezados**, **listas** y **estructuras de contenido**, cumpliendo las restricciones establecidas y sirviendo como base para futuras mejoras del sitio de Jose Vicente.
