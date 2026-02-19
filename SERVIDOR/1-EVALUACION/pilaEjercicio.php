<?php
    class Pila {
    private $elementos = [];

    public function push($item) {
    array_push($this->elementos, $item);
    }

    public function pop() {
    if (!$this->estaVacia()) {
    return array_pop($this->elementos);
    }
    return null;
    }

    public function peek() {
    if (!$this->estaVacia()) {
    return end($this->elementos);
    }
    return null;
    }

    public function estaVacia() {
    return empty($this->elementos);
    }

    public function size() {
    return count($this->elementos);
    }

    public function mostrar() {
    print_r($this->elementos);
    }
    }

    // ------------------------------
    // Simulación de un navegador
    // ------------------------------
    class Navegador {
    private $historial;
    private $saltos = 0;

    public function __construct() {
    $this->historial = new Pila();
    }

    // Agrega una página al historial
    public function abrirPagina($url) {
    $this->historial->push([
    "url" => $url,
    "contenido" => "Contenido de la página " . $url
    ]);
    $this->saltos++;
    echo "Página abierta: $url<br>";
    }

    // Retrocede una página
    public function retroceder() {
    if ($this->historial->size() <= 1) {
    echo "No hay más páginas para retroceder.<br>";
    return;
    }

    // Quitamos la página actual
    $paginaCerrada = $this->historial->pop();
    $this->saltos++;

    // La nueva página actual es la que está ahora en el tope
    $paginaActual = $this->historial->peek();
    echo "Retrocediendo ahora estás en: " . $paginaActual["url"] . "<br>";
    }

    // Muestra la página actual
    public function verPaginaActual() {
    if ($this->historial->estaVacia()) {
    echo "No hay ninguna página abierta.<br>";
    return;
    }

    $pagina = $this->historial->peek();
    echo "Página actual: " . $pagina["url"] . "<br>";
    echo "Contenido: " . $pagina["contenido"] . "<br>";
    echo "Saltos realizados: " . $this->saltos . "<br>";
    }

    // Muestra todas las páginas del historial
    public function historialCompleto() {
    echo "Historial completo:<br>";
    $this->historial->mostrar();
    }
}

$navegador = new Navegador();

$navegador->abrirPagina("google.com");
$navegador->abrirPagina("jocarsa.es");
$navegador->abrirPagina("wikipedia.org");

echo "<br>--- Ver página actual ---<br>";
$navegador->verPaginaActual();

echo "<br>--- Retroceder una página ---<br>";
$navegador->retroceder();

echo "<br>--- Ver página actual ---<br>";
$navegador->verPaginaActual();

echo "<br>--- Historial completo ---<br>";
$navegador->historialCompleto();
?>


