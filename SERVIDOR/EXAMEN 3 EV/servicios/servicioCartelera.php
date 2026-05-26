<?php
// =====================================================
// SERVICIO 1: CARTELERA
// =====================================================
// El servicio lee los datos desde JSON y devuelve HTML.
// El cliente recibe directamente etiquetas HTML que
// insertará dentro de su página.

function mostrarCartelera()
{
    header("Content-Type: text/html; charset=utf-8");

    $rutaDatos = __DIR__ . "/../datos/cartelera.json";

    $peliculas = leerPeliculas($rutaDatos);

    if ($peliculas === null) {
        http_response_code(500);

        echo '<p class="error">No se pudo leer el fichero de cartelera.</p>';

        return;
    }

    $genero = trim($_GET["genero"] ?? "");

    $peliculasFiltradas = [];

    foreach ($peliculas as $pelicula) {
        if ($genero === "" || $pelicula["genero"] === $genero) {
            $peliculasFiltradas[] = $pelicula;
        }
    }

    if (count($peliculasFiltradas) === 0) {
        echo '<p class="mensaje">';
        echo 'No hay películas disponibles para el género seleccionado.';
        echo '</p>';

        return;
    }

    // TODO 2:
    // Generar la respuesta HTML de la cartelera.
    // Recorrer las películas filtradas y escribir una tarjeta HTML ("article")
    // por cada una, mostrando título ("h3"), género, duración y sala ("p").
    echo '<div class="cartelera">';

    foreach ($peliculasFiltradas as $pelicula) {
        echo '<article class="pelicula">';

        echo '<h3 class="titulo">' . htmlspecialchars($pelicula["titulo"],) . '</h3>';

        echo '<p class="genero">Género: ' . htmlspecialchars($pelicula["genero"],) . '</p>';

        echo '<p class="duracion">Duración: ' . htmlspecialchars($pelicula["duracion"],) . '</p>';

        echo '<p class="sala">Sala: ' . htmlspecialchars($pelicula["sala"],) . '</p>';

        echo '</article>';
    }
    

    echo '</div>';
}

function leerPeliculas($rutaDatos)
{
    if (!file_exists($rutaDatos)) {
        return null;
    }

    $texto = file_get_contents($rutaDatos);

    $peliculas = json_decode($texto, true);

    if (!is_array($peliculas)) {
        return null;
    }

    return $peliculas;
}