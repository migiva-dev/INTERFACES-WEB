<?php

/*
    id PK
    Titulo
    isbn
    autor
    editorial FK
    categoria FK
    num paginas
*/

$Titulo = $_POST['Titulo'];
$isbn = $_POST['isbn'];
$autor = $_POST['autor'];
$editorial = $_POST['editorial'];
$categoria = $_POST['categoria'];
$num_paginas = $_POST['num_paginas'];

?>
<?php

$db = new SQLite3('biblioteca.db'); //Creamos la base de datos y conexion

//
$db->exec("CREATE TABLE IF NOT EXISTS libros(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    Titulo TEXT, isbn TEXT, autor TEXT,
    editorial TEXT, categoria TEXT, num_paginas INTEGER
)");

$Titulo=$_POST['Titulo'];
$isbn=$_POST['isbn'];
$autor=$_POST['autor'];
$editorial=$_POST['editorial'];
$categoria=$_POST['categoria'];
$num_paginas=$_POST['num_paginas'];


$db->exec("INSERT INTO libros (Titulo,isbn,autor,editorial,categoria,num_paginas)
VALUES ('$Titulo','$isbn','$autor','$editorial','$categoria',$num_paginas)");


echo "Libro insertado correctamente";

?>
