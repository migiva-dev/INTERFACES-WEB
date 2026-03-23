## 1. Introducción

En el contexto del **desarrollo web con HTML5 y JavaScript**, este ejercicio crea una página mínima para **validar un DNI español** desde un campo de entrada. Practicarás:

- **Estructura HTML5** correcta y clara (semántica básica).
- **Validación de formularios en el cliente** con JavaScript (evento `blur`).
- Separación del código en un **script externo simple** (hospedado en tu propio GitHub Pages) sin usar librerías de terceros.

**Contexto de uso:** páginas donde se requiere verificar el formato del DNI antes de enviar datos a un servidor (ej.: formularios de alta). Se respeta la restricción de no usar librerías externas “adicionales”: el script externo será **tuyo**, dentro del mismo repositorio, referenciado por **URL externa** (la de tu GitHub Pages).

---

## 2. Desarrollo detallado y preciso

### Definiciones clave
- **DNI**: 8 dígitos seguidos de **una letra** de control.  
  La letra se calcula como `número % 23`, mapeando el resto con la cadena **`TRWAGMYFPDXBNJZSQVHLCKE`**.
- **Criterios**:  
  1) Debe tener **8 dígitos** (0–9).  
  2) Debe **terminar en una letra** (A–Z), que coincida con la letra calculada.

### Estructura base del documento
Usaremos el esqueleto proporcionado e insertaremos el `<input class="dni" type="text">`, un contenedor para el mensaje y el enlace al script externo.

### Algoritmo de validación (paso a paso)
1. Estructura HTML5 mínima y **campo** `<input class="dni" type="text">`.  
2. Contenedor para resultado (p. ej., `<p id="resultado">`).  
3. **Script externo propio** (alojado en tu GitHub Pages) que exporta una función `validarDNI()`.  
4. Enlace al script por **URL absoluta** a tu GitHub Pages (cumple “URL externa” pero sin librerías de terceros).  
5. Añadir `addEventListener('blur', ...)` para validar cuando el usuario sale del campo.

### Código (ejemplos reales)

#### 2.1. HTML — `validar_dni.html`

```html
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <title>Validar DNI</title>
    <style>
      body { background:#f4f4f9; font-family: Arial, sans-serif; margin:0; }
      header { background:#333; color:#fff; padding:10px 20px; text-align:center; }
      main { padding:20px; max-width:720px; margin:0 auto; }
      label { display:block; margin-bottom:8px; font-weight:bold; }
      input.dni { padding:8px 10px; font-size:1rem; width:220px; }
      .ok { color:#0f7b0f; }
      .error { color:#b00020; }
    </style>
  </head>
  <body>
    <header>
      <h1>Validación de DNI</h1>
    </header>
    <main>
      <label for="dni">Introduce tu DNI (8 dígitos + letra):</label>
      <input class="dni" id="dni" name="dni" type="text" placeholder="12345678Z" />
      <p id="resultado" aria-live="polite"></p>
    </main>

    <!-- Script externo PROPIO: alojado en tu GitHub Pages -->
    <script src="https://TU_USUARIO.github.io/TU_REPO/js/dni-validator.js"></script>

    <!-- Enlazamos la validación al evento blur sin librerías adicionales -->
    <script>
      const input = document.querySelector('.dni');
      const salida = document.getElementById('resultado');

      input.addEventListener('blur', () => {
        const valor = input.value;
        const esValido = window.validarDNI(valor); // función expuesta por tu script externo

        if (esValido) {
          salida.textContent = 'DNI válido ✔';
          salida.className = 'ok';
        } else {
          salida.textContent = 'DNI no válido ✖ (formato: 8 dígitos + letra correcta)';
          salida.className = 'error';
        }
      });
    </script>
  </body>
</html>
```

#### 2.2. JS externo — `js/dni-validator.js`

```js
// js/dni-validator.js (sin librerías externas)
(function () {
  const LETRAS = "TRWAGMYFPDXBNJZSQVHLCKE";

  function validarDNI(valor) {
    if (!valor) return false;
    const limpio = valor.toString().trim().toUpperCase().replace(/\s+/g, "");
    const match = limpio.match(/^(\d{8})([A-Z])$/);
    if (!match) return false;

    const numero = parseInt(match[1], 10);
    const letra = match[2];
    const letraEsperada = LETRAS[numero % 23];
    return letra === letraEsperada;
  }

  // Exponer como función global simple
  window.validarDNI = validarDNI;
})();
```

---

## 3. Aplicación práctica

**Cómo se aplica**
- Usuario escribe `12345678Z` en el input.
- Al salir del campo (evento **`blur`**), se invoca `validarDNI(valor)`.
- Si el patrón y la letra son correctos, muestra **“DNI válido ✔”**; si no, **mensaje de error**.

**Ejemplo de prueba rápida**
- Válido: `00000000T` (0 % 23 = 0 → **T**).  
- Inválido: `00000000A` (letra no coincide).

**Errores comunes y cómo evitarlos**
- **Poner NIE o formatos distintos** (no pedido): limítate a DNI estándar del enunciado.  
- **Olvidar publicar el JS en Pages**: si el `src` da 404, revisa rama/ubicación.  
- **Esquema de URL mal** (`http` vs `https`): usa la **URL exacta** de tu GitHub Pages.  
- **No usar `blur`**: el profe pide validar al perder el foco; no lo sustituyas por `input` o `change`.  
- **No limpiar espacios / no forzar mayúsculas**: normaliza con `trim()` y `toUpperCase()`.

---

## 4. Conclusión

Has implementado una **validación de DNI en cliente** con **HTML5 + JS** cumpliendo:  
- Estructura clara y semántica mínima.  
- **Evento `blur`** para validar sin librerías externas.  
- **Script externo propio** enlazado por URL (GitHub Pages), respetando la restricción.  
Esto enlaza con la unidad sobre **formularios, eventos del DOM, expresiones regulares y despliegue estático**.

---
