<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Miguelpress</title>
    <style>
      html,
      body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        padding: 0px;
        margin: 0px;
        font-family: "Lucida Sans", Arial, sans-serif;
        background: steelblue;
      }
      header,
      footer,
      main {
        background: white;
        padding: 20px;
        width: 600px;
        margin: auto;
      }
      article {
        padding: 20px;
        border-bottom: 1px solid steelblue;
      }
      article * {
        padding: 5px;
        margin: 0px;
      }
    </style>
  </head>
  <body>
    <header>
        <h1>El blog de Miguel Gavilá</h1>
    </header>
    <main>
      <?php
        $basededatos = new SQLite3("miguel.db");
        $resultados = $basededatos->query("SELECT * FROM post");
        while ($fila = $resultados->fetchArray()) {
            echo "<article>";
            echo "<h3>" . $fila['Fecha'] . "</h3>";
             echo "<time>" . $fila['Titulo'] . "</time>";
            echo "<p>" . $fila['Autor'] . "</p>";
            echo "<p>" . $fila['Texto'] . "</p>";
            echo "</article>";
        }

        ?>

    </main>
    <footer>Copyright 2024 - Miguel Gavilá Ibáñez</footer>
    </body>
</html>