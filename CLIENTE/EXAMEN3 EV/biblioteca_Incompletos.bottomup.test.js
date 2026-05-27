/**
 * @jest-environment jsdom
 *
 * Para ejecutar específicamente estas pruebas:
 * npm.cmd run test:integracion
 */

const fs = require("fs");
const path = require("path");

function cargarDOM() {
  const rutaHtml = path.join(__dirname, "index.html");
  const html = fs.readFileSync(rutaHtml, "utf8");
  const bodyMatch = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);

  document.body.innerHTML = bodyMatch ? bodyMatch[1] : html;
}

function cargarAplicacion() {
  jest.resetModules();
  cargarDOM();

  globalThis.BibliotecaVideojuegos = require("./js/biblioteca");

  require("./js/app");
}

function agregarJuego(datos = {}) {
  document.getElementById("inpTitulo").value =
    datos.titulo || "Hollow Knight";

  document.getElementById("inpEstudio").value =
    datos.estudio || "Team Cherry";

  document.getElementById("selPlataforma").value =
    datos.plataforma || "pc";

  document.getElementById("selPegi").value =
    String(datos.pegi || 12);

  document.getElementById("selEstado").value =
    datos.estado || "jugando";

  document.getElementById("inpPrecio").value =
    String(datos.precio === undefined ? 19.99 : datos.precio);

  document.getElementById("formJuego").dispatchEvent(
    new Event("submit", { bubbles: true, cancelable: true })
  );
}

// TODO 12:
// Implementa un bloque de pruebas de integración Bottom-Up utilizando Jest.
//
// 1. Alta de un videojuego.
//    Utiliza agregarJuego() para crear un videojuego válido.
//    Después comprueba:
//    - que se ha creado un elemento <article class="juego"> 
//    con data-titulo que contiene el título introducido;
//    - que el elemento <strong id="totalJuegos"> muestra "1";
//    - que el elemento <strong id="valorBiblioteca"> muestra
//      el importe correcto.
//
// 2. Marcar un videojuego como favorito mediante su botón interno.
//    Añade un juego con estado "jugando" y PEGI 12.
//    Localiza el elemento <button class="btn-favorito">
//    cuyo atributo data-accion sea "favorito" y simula su click.
//    Después comprueba:
//    - que el elemento <article class="juego"> tiene
//      data-favorito igual a "true";
//    - que la tarjeta contiene la clase CSS "favorito";
//    - que el elemento <strong id="totalFavoritos"> muestra "1".

describe("Integración Bottom-Up del gestor de biblioteca", () => {
  beforeEach(() => {
    cargarAplicacion();
  });

  test("añadir un juego crea su tarjeta y actualiza el resumen", () => {

  });

  test("pulsar el botón favorito modifica la tarjeta y el resumen", () => {

  });
});