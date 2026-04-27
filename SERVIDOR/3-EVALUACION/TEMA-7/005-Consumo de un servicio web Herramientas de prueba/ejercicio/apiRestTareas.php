<?php
header("Content-Type: application/json; charset=utf-8");

$archivoDatos = __DIR__ . "/tareas.json";

// ----------------------------------------------------
// Crear datos iniciales si no existe el fichero JSON
// ----------------------------------------------------
if (!file_exists($archivoDatos)) {
    $tareasIniciales = [
        [
            "id" => 1,
            "titulo" => "Estudiar PHP",
            "descripcion" => "Repasar creación de APIs REST con PHP y JSON.",
            "completada" => false,
            "prioridad" => "alta",
            "fechaCreacion" => date("Y-m-d H:i:s")
        ],
        [
            "id" => 2,
            "titulo" => "Hacer ejercicios",
            "descripcion" => "Completar los ejercicios de servicios web.",
            "completada" => true,
            "prioridad" => "media",
            "fechaCreacion" => date("Y-m-d H:i:s")
        ]
    ];

    file_put_contents(
        $archivoDatos,
        json_encode($tareasIniciales, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

// ----------------------------------------------------
// Funciones auxiliares
// ----------------------------------------------------
function leerTareas($archivoDatos) {
    $contenido = file_get_contents($archivoDatos);
    return json_decode($contenido, true) ?? [];
}

function guardarTareas($archivoDatos, $tareas) {
    file_put_contents(
        $archivoDatos,
        json_encode($tareas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function responder($codigo, $datos = null) {
    http_response_code($codigo);

    if ($datos !== null) {
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    exit;
}

function leerJSONBody() {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if ($raw !== "" && $data === null) {
        responder(400, [
            "error" => "JSON inválido"
        ]);
    }

    return $data ?? [];
}

function normalizarPrioridad($prioridad) {
    $prioridad = strtolower(trim($prioridad));
    $permitidas = ["baja", "media", "alta"];

    if (!in_array($prioridad, $permitidas)) {
        responder(400, [
            "error" => "La prioridad debe ser baja, media o alta"
        ]);
    }

    return $prioridad;
}

// ----------------------------------------------------
// Obtener método HTTP y ruta
// ----------------------------------------------------
$metodo = $_SERVER["REQUEST_METHOD"];

// Ejemplo de PATH_INFO:
// /tareas
// /tareas/1
$ruta = $_SERVER["PATH_INFO"] ?? "";

$partesRuta = explode("/", trim($ruta, "/"));
$recurso = $partesRuta[0] ?? "";
$id = $partesRuta[1] ?? null;

// Solo aceptamos el recurso tareas
if ($recurso !== "tareas") {
    responder(404, [
        "error" => "Recurso no encontrado. Usa /tareas o /tareas/{id}"
    ]);
}

$tareas = leerTareas($archivoDatos);

// ----------------------------------------------------
// GET /tareas
// Devuelve todas las tareas
// ----------------------------------------------------
if ($metodo === "GET" && $id === null) {
    responder(200, [
        "total" => count($tareas),
        "tareas" => $tareas
    ]);
}

// ----------------------------------------------------
// GET /tareas/{id}
// Devuelve una tarea concreta
// ----------------------------------------------------
if ($metodo === "GET" && $id !== null) {
    foreach ($tareas as $tarea) {
        if ($tarea["id"] == $id) {
            responder(200, $tarea);
        }
    }

    responder(404, [
        "error" => "Tarea no encontrada"
    ]);
}

// ----------------------------------------------------
// POST /tareas
// Crea una tarea nueva
// ----------------------------------------------------
if ($metodo === "POST" && $id === null) {
    $data = leerJSONBody();

    $titulo = trim($data["titulo"] ?? "");
    $descripcion = trim($data["descripcion"] ?? "");
    $completada = $data["completada"] ?? false;
    $prioridad = normalizarPrioridad($data["prioridad"] ?? "media");

    if ($titulo === "" || $descripcion === "") {
        responder(400, [
            "error" => "Debes enviar titulo y descripcion"
        ]);
    }

    $ids = array_column($tareas, "id");
    $nuevoId = empty($ids) ? 1 : max($ids) + 1;

    $nuevaTarea = [
        "id" => $nuevoId,
        "titulo" => $titulo,
        "descripcion" => $descripcion,
        "completada" => (bool)$completada,
        "prioridad" => $prioridad,
        "fechaCreacion" => date("Y-m-d H:i:s")
    ];

    $tareas[] = $nuevaTarea;
    guardarTareas($archivoDatos, $tareas);

    responder(201, $nuevaTarea);
}

// ----------------------------------------------------
// PATCH /tareas/{id}
// Actualiza parcialmente una tarea
// ----------------------------------------------------
if ($metodo === "PATCH" && $id !== null) {
    $data = leerJSONBody();

    foreach ($tareas as $indice => $tarea) {
        if ($tarea["id"] == $id) {

            if (isset($data["titulo"])) {
                $titulo = trim($data["titulo"]);
                if ($titulo === "") {
                    responder(400, ["error" => "El titulo no puede estar vacío"]);
                }
                $tareas[$indice]["titulo"] = $titulo;
            }

            if (isset($data["descripcion"])) {
                $descripcion = trim($data["descripcion"]);
                if ($descripcion === "") {
                    responder(400, ["error" => "La descripcion no puede estar vacía"]);
                }
                $tareas[$indice]["descripcion"] = $descripcion;
            }

            if (isset($data["completada"])) {
                $tareas[$indice]["completada"] = (bool)$data["completada"];
            }

            if (isset($data["prioridad"])) {
                $tareas[$indice]["prioridad"] = normalizarPrioridad($data["prioridad"]);
            }

            // No se modifica fechaCreacion porque representa cuándo se creó la tarea.
            guardarTareas($archivoDatos, $tareas);
            responder(200, $tareas[$indice]);
        }
    }

    responder(404, [
        "error" => "Tarea no encontrada"
    ]);
}

// ----------------------------------------------------
// DELETE /tareas/{id}
// Elimina una tarea
// ----------------------------------------------------
if ($metodo === "DELETE" && $id !== null) {
    foreach ($tareas as $indice => $tarea) {
        if ($tarea["id"] == $id) {
            array_splice($tareas, $indice, 1);
            guardarTareas($archivoDatos, $tareas);
            responder(204);
        }
    }

    responder(404, [
        "error" => "Tarea no encontrada"
    ]);
}

// ----------------------------------------------------
// Si el método no encaja con ningún caso
// ----------------------------------------------------
responder(405, [
    "error" => "Método no permitido para esta ruta"
]);
?>
