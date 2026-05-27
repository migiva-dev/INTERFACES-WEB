// TODO 3:
// Documenta la introducción general de este archivo utilizando JSDoc.
// Debes explicar brevemente que tipo de lógica contiene e incluir las etiquetas:
// @file
// @author


"use strict";

// =====================================================
// API de lógica
// =====================================================

const biblioteca = globalThis.BibliotecaVideojuegos;

// =====================================================
// Referencias al DOM
// =====================================================

const formJuego = document.getElementById("formJuego");
const inpTitulo = document.getElementById("inpTitulo");
const inpEstudio = document.getElementById("inpEstudio");
const selPlataforma = document.getElementById("selPlataforma");
const selPegi = document.getElementById("selPegi");
const selEstado = document.getElementById("selEstado");
const inpPrecio = document.getElementById("inpPrecio");

const mensaje = document.getElementById("mensaje");
const listaJuegos = document.getElementById("listaJuegos");
const mensajeVacio = document.getElementById("mensajeVacio");

const totalJuegos = document.getElementById("totalJuegos");
const totalJugando = document.getElementById("totalJugando");
const totalFavoritos = document.getElementById("totalFavoritos");
const valorBiblioteca = document.getElementById("valorBiblioteca");

// =====================================================
// Estado de la aplicación
// =====================================================

let siguienteId = 1;

// =====================================================
// Funciones generales de interfaz
// =====================================================

function mostrarMensaje(texto, tipo) {
  mensaje.textContent = texto;
  mensaje.className = "mensaje " + tipo;
}

function limpiarMensaje() {
  mensaje.textContent = "";
  mensaje.className = "mensaje";
}

// =====================================================
// Conversión entre tarjeta y datos
// =====================================================

function obtenerJuegoDesdeTarjeta(tarjeta) {
  return {
    id: Number(tarjeta.getAttribute("data-id")),
    titulo: tarjeta.getAttribute("data-titulo"),
    estudio: tarjeta.getAttribute("data-estudio"),
    plataforma: tarjeta.getAttribute("data-plataforma"),
    pegi: Number(tarjeta.getAttribute("data-pegi")),
    estado: tarjeta.getAttribute("data-estado"),
    precio: Number(tarjeta.getAttribute("data-precio")),
    favorito: tarjeta.getAttribute("data-favorito") === "true"
  };
}

function guardarDatosEnTarjeta(tarjeta, juego) {
  tarjeta.setAttribute("data-id", juego.id);
  tarjeta.setAttribute("data-titulo", juego.titulo);
  tarjeta.setAttribute("data-estudio", juego.estudio);
  tarjeta.setAttribute("data-plataforma", juego.plataforma);
  tarjeta.setAttribute("data-pegi", juego.pegi);
  tarjeta.setAttribute("data-estado", juego.estado);
  tarjeta.setAttribute("data-precio", juego.precio);
  tarjeta.setAttribute("data-favorito", juego.favorito);
}

// =====================================================
// Creación dinámica de tarjetas
// =====================================================

function crearTarjetaJuego(juego) {
  const article = document.createElement("article");
  article.classList.add("juego");

  guardarDatosEnTarjeta(article, juego);

  const cabecera = document.createElement("div");
  cabecera.classList.add("cabecera-juego");

  const bloqueTitulo = document.createElement("div");

  const titulo = document.createElement("h3");
  titulo.classList.add("titulo-juego");
  titulo.textContent = juego.titulo;

  const estudio = document.createElement("p");
  estudio.classList.add("estudio-juego");
  estudio.textContent = juego.estudio;

  bloqueTitulo.appendChild(titulo);
  bloqueTitulo.appendChild(estudio);

  const precio = document.createElement("strong");
  precio.classList.add("precio-juego");
  precio.textContent = biblioteca.formatearEuros(juego.precio);

  cabecera.appendChild(bloqueTitulo);
  cabecera.appendChild(precio);

  const datos = document.createElement("div");
  datos.classList.add("datos-juego");

  const plataforma = document.createElement("span");
  plataforma.classList.add("etiqueta", "plataforma-juego");
  plataforma.textContent = biblioteca.obtenerNombrePlataforma(juego.plataforma);

  const pegi = document.createElement("span");
  pegi.classList.add("etiqueta", "pegi-juego");
  pegi.textContent = "PEGI " + juego.pegi;

  const estado = document.createElement("span");
  estado.classList.add("etiqueta", "estado-juego", "estado-" + juego.estado);
  estado.textContent = biblioteca.obtenerNombreEstado(juego.estado);

  const favorito = document.createElement("span");
  favorito.classList.add("etiqueta", "etiqueta-favorito", "oculta");
  favorito.textContent = "Favorito";

  datos.appendChild(plataforma);
  datos.appendChild(pegi);
  datos.appendChild(estado);
  datos.appendChild(favorito);

  // TODO 4:
  // Crea la zona de botones internos de la tarjeta.
  //
  // Debes crear estos elementos HTML:
  //
  // - Un elemento <div> con la clase CSS "acciones".
  //
  // - Un elemento <button> para cambiar el estado:
  //      type: "button"
  //      clase CSS: "btn-estado"
  //      atributo data-accion: "estado"
  //      atributo title: "Cambiar el estado del videojuego"
  //      texto visible: "Cambiar estado"
  //
  // - Un elemento <button> para marcar o desmarcar como favorito:
  //      type: "button"
  //      clase CSS: "btn-favorito"
  //      atributo data-accion: "favorito"
  //      atributo title: "Marcar o desmarcar como favorito"
  //      texto visible: "Favorito"
  //
  // - Un elemento <button> para eliminar el juego:
  //      type: "button"
  //      clase CSS: "btn-eliminar"
  //      atributo data-accion: "eliminar"
  //      atributo title: "Eliminar el videojuego de la biblioteca"
  //      texto visible: "Eliminar"
  //
  // Añade los tres elementos <button> dentro del <div>.

  article.appendChild(cabecera);
  article.appendChild(datos);
  article.appendChild(acciones);

  return article;
}

// =====================================================
// Actualización visual de tarjetas
// =====================================================

function aplicarClasePlataforma(tarjeta) {
  tarjeta.classList.remove(
    "plataforma-pc",
    "plataforma-playstation",
    "plataforma-xbox",
    "plataforma-switch"
  );

  const plataforma = tarjeta.getAttribute("data-plataforma");

  tarjeta.classList.add("plataforma-" + plataforma);
}

// TODO 5:
// Documenta la función actualizarBotonFavorito(tarjeta) utilizando JSDoc.
// Debes explicar qué ocurre cuando un juego no puede marcarse como favorito
// e incluir las etiquetas:
// @param
// @returns


function actualizarBotonFavorito(tarjeta) {
  const juego = obtenerJuegoDesdeTarjeta(tarjeta);
  const btnFavorito = tarjeta.querySelector("button[data-accion='favorito']");
  const permitido = biblioteca.puedeSerFavorito(juego);

  btnFavorito.disabled = !permitido;

  if (!permitido) {
    tarjeta.setAttribute("data-favorito", "false");
  }
}

function actualizarTarjeta(tarjeta) {
  actualizarBotonFavorito(tarjeta);

  const juego = obtenerJuegoDesdeTarjeta(tarjeta);

  tarjeta.querySelector(".titulo-juego").textContent = juego.titulo;
  tarjeta.querySelector(".estudio-juego").textContent = juego.estudio;
  tarjeta.querySelector(".precio-juego").textContent =
    biblioteca.formatearEuros(juego.precio);

  tarjeta.querySelector(".plataforma-juego").textContent =
    biblioteca.obtenerNombrePlataforma(juego.plataforma);

  tarjeta.querySelector(".pegi-juego").textContent = "PEGI " + juego.pegi;

  const etiquetaEstado = tarjeta.querySelector(".estado-juego");
  etiquetaEstado.textContent = biblioteca.obtenerNombreEstado(juego.estado);
  etiquetaEstado.className = "etiqueta estado-juego estado-" + juego.estado;

  const etiquetaFavorito = tarjeta.querySelector(".etiqueta-favorito");

  if (juego.favorito) {
    etiquetaFavorito.classList.remove("oculta");
    tarjeta.classList.add("favorito");
  } else {
    etiquetaFavorito.classList.add("oculta");
    tarjeta.classList.remove("favorito");
  }

  aplicarClasePlataforma(tarjeta);
}

// =====================================================
// Resumen y selección
// =====================================================

function actualizarResumen() {
  const tarjetas = listaJuegos.querySelectorAll(".juego");
  const juegos = [];

  for (let i = 0; i < tarjetas.length; i++) {
    juegos.push(obtenerJuegoDesdeTarjeta(tarjetas[i]));
  }

  const resumen = biblioteca.calcularResumen(juegos);

  totalJuegos.textContent = resumen.total;
  totalJugando.textContent = resumen.jugando;
  totalFavoritos.textContent = resumen.favoritos;
  valorBiblioteca.textContent = biblioteca.formatearEuros(resumen.valor);

  mensajeVacio.style.display = resumen.total === 0 ? "block" : "none";
}

// TODO 6:
// Implementa seleccionarJuego(tarjeta).
//
// - Localiza todos los elementos <article> con la clase CSS "juego"
//   que existan dentro del elemento <div id="listaJuegos">.
// - Elimina la clase CSS "seleccionado" de todas las tarjetas.
// - Añade la clase CSS "seleccionado" únicamente al
//   elemento <article class="juego"> recibido.

function seleccionarJuego(tarjeta) {

}

// =====================================================
// Acciones sobre videojuegos
// =====================================================

function cambiarEstadoJuego(tarjeta) {
  const estadoActual = tarjeta.getAttribute("data-estado");
  const nuevoEstado = biblioteca.siguienteEstado(estadoActual);

  tarjeta.setAttribute("data-estado", nuevoEstado);

  actualizarTarjeta(tarjeta);
  actualizarResumen();

  mostrarMensaje("Se ha cambiado el estado del videojuego.", "correcto");
}

// TODO 7:
// Implementa alternarFavoritoJuego(tarjeta).
//
// - Obtén los datos del juego almacenados en el
//   elemento recibido con la función obtenerJuegoDesdeTarjeta().
// - Comprueba si puede ser favorito mediante biblioteca.puedeSerFavorito().
// - Si no puede ser favorito, muestra un mensaje de error y termina.
/*
mostrarMensaje(
      "Un juego pendiente o PEGI 18 no puede marcarse como favorito.",
      "error"
    );
*/
// - Si puede ser favorito, modifica el atributo data-favorito del
//   elemento cambiando su valor entre "true" y "false".
// - Actualiza visualmente la tarjeta.
// - Actualiza el resumen.
// - Muestra un mensaje de confirmación.
// mostrarMensaje("Se ha actualizado el estado de favorito.", "correcto");

function alternarFavoritoJuego(tarjeta) {

}

// TODO 8:
// Implementa eliminarJuego(tarjeta).
//
// - Elimina del elemento <div id="listaJuegos"> el
//   elemento <article class="juego"> recibido.
// - Actualiza el resumen.
// - Muestra un mensaje de confirmación.
// mostrarMensaje("El videojuego se ha eliminado de la biblioteca.", "correcto");

function eliminarJuego(tarjeta) {

}

// =====================================================
// Formulario de alta
// =====================================================

// TODO 9:
// Implementa procesarFormulario(evento).
//
// - Cancela el envío del fomulario.
// - Limpia cualquier mensaje anterior.
// Dentro del try:
// - Recoge título, estudio, plataforma, PEGI, estado y precio
//   desde los campos del formulario.
// - Crea un objeto juego llamando a biblioteca.crearJuego().
// - Crea su elemento <article class="juego"> llamando
//   a crearTarjetaJuego().
// - Inserta la tarjeta dentro de "listaJuegos".
// - Incrementa siguienteId.
// - Limpia el formulario y restaura estos valores:
//      PEGI: "12"
//      estado: "jugando"
//      precio: "39.99"
// - Coloca el foco en el inpTitulo.
// - Actualiza el resumen y muestra un mensaje correcto.
// mostrarMensaje("Videojuego añadido correctamente.", "correcto");
// - Si se produce un error, muestra su mensaje en la interfaz.
// mostrarMensaje(error.message, "error");

function procesarFormulario(evento) {

  try {

  } catch (error) {
    
  }
}

// =====================================================
// Delegación de eventos
// =====================================================

// TODO 10:
// Implementa gestionarClickLista(evento).
//
// - Obtén el elemento HTML realmente pulsado.
// - Localiza el elemento <article class="juego"> más cercano.
// - Si el click no se ha producido dentro de una tarjeta, termina.
// - Comprueba si el elemento pulsado es un botón
//   con atributo data-accion.
// - Si se ha pulsado un botón:
//      - Detén la propagación del evento.
//      - Lee el valor de data-accion.
//      - Ejecuta la función correspondiente:
//          "estado"   -> cambiarEstadoJuego(tarjeta)
//          "favorito" -> alternarFavoritoJuego(tarjeta)
//          "eliminar" -> eliminarJuego(tarjeta)
//      - Termina la función.
// - Si no se ha pulsado un botón, selecciona la tarjeta con la función seleccionarJuego.

function gestionarClickLista(evento) {

}

// =====================================================
// Listeners iniciales
// =====================================================

formJuego.addEventListener("submit", procesarFormulario);
listaJuegos.addEventListener("click", gestionarClickLista);

actualizarResumen();