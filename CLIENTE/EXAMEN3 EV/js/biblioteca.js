// TODO 1:
// Documenta la introducción general de este archivo utilizando JSDoc.
// Debes explicar brevemente qué tipo de lógica contiene e incluir las etiquetas:

/**
* @file biblioteca.js
* @author Miguel Gavila ~ DAW
* @description

* Archivo que contiene la lógica principal del gestor de biblioteca de videojuegos.
* Incluye constantes con datos válidos, funciones de validación, creación de videojuegos,
* gestión de estados, favoritos, resumen de la biblioteca y funciones auxiliares.

*/

(function (global) {
  "use strict";

  // =====================================================
  // Datos válidos de la aplicación
  // =====================================================

  const ESTADOS_VALIDOS = ["pendiente", "jugando", "completado"];
  const PLATAFORMAS_VALIDAS = ["pc", "playstation", "xbox", "switch"];
  const PEGI_VALIDOS = [3, 7, 12, 16, 18];

  // =====================================================
  // Validaciones y creación de juegos
  // =====================================================

  function esTextoValido(texto) {
    return typeof texto === "string" && texto.trim() !== "";
  }

  function esPrecioValido(precio) {
    if (String(precio).trim() === "") {
      return false;
    }

    const numero = Number(precio);

    return Number.isFinite(numero) && numero >= 0;
  }

  function crearJuego(id, titulo, estudio, plataforma, pegi, estado, precio) {
    const pegiNumerico = Number(pegi);
    const precioNumerico = Number(precio);

    if (!esTextoValido(titulo) || !esTextoValido(estudio)) {
      throw new Error("El título y el estudio son obligatorios.");
    }

    if (!PLATAFORMAS_VALIDAS.includes(plataforma)) {
      throw new Error("La plataforma seleccionada no es válida.");
    }

    if (!PEGI_VALIDOS.includes(pegiNumerico)) {
      throw new Error("La clasificación PEGI no es válida.");
    }

    if (!ESTADOS_VALIDOS.includes(estado)) {
      throw new Error("El estado seleccionado no es válido.");
    }

    if (!esPrecioValido(precio)) {
      throw new Error("El precio debe ser un número mayor o igual que cero.");
    }

    return {
      id: Number(id),
      titulo: titulo.trim(),
      estudio: estudio.trim(),
      plataforma: plataforma,
      pegi: pegiNumerico,
      estado: estado,
      precio: precioNumerico,
      favorito: false
    };
  }

  // =====================================================
  // Funciones auxiliares de presentación y estado
  // =====================================================

  function obtenerNombrePlataforma(plataforma) {
    if (plataforma === "pc") return "PC";
    if (plataforma === "playstation") return "PlayStation";
    if (plataforma === "xbox") return "Xbox";
    if (plataforma === "switch") return "Nintendo Switch";

    return plataforma;
  }

  function obtenerNombreEstado(estado) {
    if (estado === "pendiente") return "Pendiente";
    if (estado === "jugando") return "Jugando";
    if (estado === "completado") return "Completado";

    return estado;
  }

  function siguienteEstado(estadoActual) {
    if (estadoActual === "pendiente") return "jugando";
    if (estadoActual === "jugando") return "completado";
    if (estadoActual === "completado") return "pendiente";

    throw new Error("No se puede cambiar un estado no válido.");
  }

  // TODO 2:
  // Documenta la función puedeSerFavorito(juego) utilizando JSDoc.
  // Debes explicar cuándo un videojuego puede marcarse como favorito
  // e incluir las etiquetas:

  /**
   * Comprueba si un videojuego puede marcarse como favorito.
   * Un videojuego puede ser favorito cuando no está pendiente
   * y su clasificación PEGI no es 18.
   * 
   * @param {object} juego - Videojuego que se quiere comprobar. 
   * @param {string} - juego.estado - Estado actual del videojuego.
   * @param {number} - juego.pegi - Clasificación PEGI del videojuego.
   * @returns {boolean} - Devuelve true si el videojuego puede marcarse como favorito, false en caso contrario.
   * 
   * @example 
   * const juego = {estado: "jugando", pegi: 16};
   * puedeSerFavorito(juego); //true
   * 
   */
  

  function puedeSerFavorito(juego) {
    return juego.estado !== "pendiente" && Number(juego.pegi) !== 18;
  }

  function calcularResumen(juegos) {
    let totalJugando = 0;
    let totalFavoritos = 0;
    let valorTotal = 0;

    for (let i = 0; i < juegos.length; i++) {
      const juego = juegos[i];

      if (juego.estado === "jugando") {
        totalJugando++;
      }

      if (juego.favorito) {
        totalFavoritos++;
      }

      valorTotal += Number(juego.precio);
    }

    return {
      total: juegos.length,
      jugando: totalJugando,
      favoritos: totalFavoritos,
      valor: valorTotal
    };
  }

  function formatearEuros(cantidad) {
    return Number(cantidad).toFixed(2) + " €";
  }

  // =====================================================
  // Exportación de la API
  // =====================================================

  const api = {
    ESTADOS_VALIDOS,
    PLATAFORMAS_VALIDAS,
    PEGI_VALIDOS,
    esTextoValido,
    esPrecioValido,
    crearJuego,
    obtenerNombrePlataforma,
    obtenerNombreEstado,
    siguienteEstado,
    puedeSerFavorito,
    calcularResumen,
    formatearEuros
  };

  if (typeof module !== "undefined" && module.exports) {
    module.exports = api;
  } else {
    global.BibliotecaVideojuegos = api;
  }
})(typeof window !== "undefined" ? window : globalThis);