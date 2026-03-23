<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="002-estilo.css">
</head>
<body>


    <!--
    id PK
    Titulo
    isbn
    autor
    editorial FK
    categoria FK
    num paginas
    -->

    <!-- CREATE -->
    <form action="002-procesar.php" method="POST">

        <label for="Titulo">Titulo:</label>
        <input type="text" name="Titulo" id="Titulo">
        
        <label for="isbn">ISBN:</label>
        <input type="text" name="isbn" id="isbn">
        
        <label for="autor">Autor:</label>
        <input type="text" name="autor" id="autor">
        
        <label for="editorial">Editorial:</label>
        <input type="text" name="editorial" id="editorial">
        
        <label for="categoria">Categoria:</label>
        <input type="text" name="categoria" id="categoria">
        
        <label for="num_paginas">Numero de paginas:</label>
        <input type="number" name="num_paginas" id="num_paginas">
        
        <input type="submit" value="Enviar">

    </form>
    <section>
        <table>
            <thead>
                <tr>
                    <th>Titulo</th>
                    <th>ISBN</th>
                    <th>Autor</th>
                    <th>Editorial</th>
                    <th>Categoria</th>
                    <th>Numero de paginas</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $db = new SQLite3('biblioteca.db'); //Conectate a la base de datos
            $results = $db->query('SELECT * FROM libros'); //Dame un listado de todos los libros

            while ($row = $results->fetchArray()) { //Y para cada uno de los resultados
                echo "<tr>";
                echo "<td>" . $row['Titulo'] . "</td>";
                echo "<td>" . $row['isbn'] . "</td>";
                echo "<td>" . $row['autor'] . "</td>";
                echo "<td>" . $row['editorial'] . "</td>";
                echo "<td>" . $row['categoria'] . "</td>";
                echo "<td>" . $row['num_paginas'] . "</td>";
                echo "</tr>";
            }                                               //Pinta una nueva fila de tabla
        ?>
            </tbody>
        </table>
    </section>


       
</body>
</html>