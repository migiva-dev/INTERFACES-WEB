<?php

    class Entrada{
        
    }

?>

<?php

    class Entrada{
        function __construct(){
            $this->titulo;
            $this->fecha;
            $this->autor;
            $this->contenido;
            $this->imagen;
        }
        
    }

?>
GI
<?php

    class Entrada{
        function __construct(){
            $this->titulo = "";
            $this->fecha = "";
            $this->autor = "";
            $this->contenido = "";
            $this->imagen = "";
        }
        
    }

    $entrada1 = new Entrada();
    var_dump($entrada1);
    $entrada2 = new Entrada();
    var_dump($entrada2);
?>