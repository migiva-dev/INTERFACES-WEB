<!doctype html>
<html lang="es">
  <head>
    <title>JOCARSApress</title>
    <meta charset="utf-8">
    <style>
      html,body{width:100%;height:100%;overflow:hidden;padding:0px;margin:0px;}
      body{display:flex;flex-direction:column;font-family:sans-serif;}
      header{flex:1;background:SteelBlue;}
      main{flex:9;display:flex;flex-direction:row;}
      nav{background:SteelBlue;flex:1;display:flex;flex-direction:column;padding:10px;gap:10px;}
      section{flex:6;}
      nav a{background:white;color:SteelBlue;padding:10px;border-radius:5px;text-decoration:none;}
    </style>
  </head>
  <body>
    <header>
    </header>
    <main>
      <nav>
        <a href="">Entradas</a>
        <a href="">Páginas</a>
        <a href="">...</a>
      </nav>
      <section>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Titulo</th>
                    <th>Autor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $basededatos = new SQLite3("miguel.db");
                $resultados = $basededatos->query("SELECT * FROM post");
                while ($fila = $resultados->fetchArray()) {
                    echo "<tr>";
                    echo "<td>" . $fila['Fecha'] . "</td>";
                    echo "<td>" . $fila['Titulo'] . "</td>";
                    echo "<td>" . $fila['Autor'] . "</td>";
                    <td>🖋❌</td>
                    "</tr>";
                }
                ?>
            </tbody>
        </table>
        <style>
            <section {display:flex; gap:20px;}
            section table{flex:4;}
            section form{flex:2;display:flex;flex-direction:column;gap:10px;}
            section form input{padding:5px; border:1px solid steelblue;}
        </style>
        <form method="POST" action="?">
            <?php
            $basededatos = new SQLite3("miguel.db");
            $resultados = $basededatos->query("PRAGMA table_info(post)");
            while ($fila = $resultados->fetchArray()) {
                echo
                 '<input type="text" name="' . $fila['name'] . '" placeholder="' . $fila['name'] . '">';
            }
            ?>
            <input type="submit">
            </form>    
      </section>
    </main>
  </body>
</html> 

