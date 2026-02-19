
        
    <?php

        class Libro {
            public $titulo;
            public $autor;
            public $disponible;

         public function __construct($titulo, $autor, $disponible) {
            $this->titulo = $titulo;
            $this->autor = $autor;
            $this->disponible = $disponible;
            }
        }


     $biblioteca = [
        new Libro("Cien años de soledad", "Gabriel García Márquez", true),
        new Libro("1984", "George Orwell", false),
        new Libro("Rayuela", "Julio Cortázar", true),
        new Libro("El nombre de la rosa", "Umberto Eco", false),
        new Libro("Los detectives salvajes", "Roberto Bolaño", true)

        ];


        foreach ($biblioteca as $libro) {
            echo "<br><br>"; 
    
        foreach ($libro as $variable => $valor) {
            echo $variable . ": " . $valor . "<br>";
        }
    }
         
        


    ?>


