<?php
include("conexion.php");

$sql = "SELECT * FROM usuario";
$result = mysqli_query($conn, $sql);
?>

<h1>Usuarios</h1>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Acciones</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id'] ?></td>
    <td><?php echo $row['nombre'] ?></td>
    <td><?php echo $row['correo'] ?></td>
    <td>
        <a href="elminar.php?id=<?php echo $row['id'] ?>">Eliminar</a>
    </td>
</tr>
<?php } ?>

</table>

<a href="crear.php">Crear usuario</a>