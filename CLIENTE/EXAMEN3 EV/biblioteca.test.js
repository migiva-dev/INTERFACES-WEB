// Para ejecutar estas pruebas:
// npm.cmd test

const Biblioteca = require("./js/biblioteca");

// TODO 11:
// Implementa un bloque de pruebas unitarias utilizando Jest.
//
// 1. Comprueba que Biblioteca.siguienteEstado("jugando")
//    devuelve "completado".
//
// 2. Comprueba que un juego con estado "jugando" y PEGI 12
//    puede ser favorito.
//
// 3. Comprueba que un juego con estado "pendiente" y PEGI 7
//    no puede ser favorito.
//
// 4. Comprueba que Biblioteca.crearJuego() lanza un error
//    si se intenta crear un juego con precio negativo.
//    El mensaje esperado es:
//    "El precio debe ser un número mayor o igual que cero."

describe("Lógica principal de la biblioteca de videojuegos", () => {
  test("un juego que está jugando pasa a completado", () => {
    expect(Biblioteca.siguienteEstado("jugando")).toBe("completado");
  });

  test("un juego jugando y PEGI 12 puede ser favorito", () => {
    const juego = {
      estado: "jugando",
      pegi: 12
    };

    expect(Biblioteca.puedeSerFavorito(juego)).toBe(true);
  });

  test("un juego pendiente no puede ser favorito", () => {
    const juego = {
      estado: "pendiente",
      pegi: 7
    };

    expect(Biblioteca.puedeSerFavorito(juego)).toBe(false);
  });

  test("no se puede crear un videojuego con precio negativo", () => {
    expect(() => {
      Biblioteca.crearJuego(
        1,
        "The Legend of Zelda",
        "Nintendo",
        "switch",
        12,
        "jugando",
        -10
      );
    }).toThrow("El precio debe ser un número mayor o igual que cero.");
  });
});