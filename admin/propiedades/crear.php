<?php 

    require '../../includes/funciones.php';
    $auth = usuarioAutenticado();

    if (!$auth) {
        header('Location: /');
    }

    //Base de datos
    require '../../includes/config/database.php';
    $db = conectarDB();

    // Consultar para obtener los vendedores 
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);
    
    // Arreglo con mensajes de errores
    $errores = [];

    $titulo = '';
    $precio = '';
    $habitaciones = '';
    $wc = '';
    $estacionamiento = '';
    $vendedorId= '';
    $descripcion = '';


    // Ejecutar el codigo despues de que el usuario envia el formulario
    // Enviar datos nuevo a base de datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  echo '<pre>';
    //  var_dump($_POST);
    //  echo '</pre>';

    //  echo '<pre>';
    //  var_dump($_FILES);
    //  echo '</pre>';

     

    // Sanitizar 
    $titulo = mysqli_real_escape_string( $db, $_POST['titulo'] );
    $precio = mysqli_real_escape_string( $db, $_POST['precio'] );
    $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones'] );
    $wc = mysqli_real_escape_string( $db, $_POST['wc'] );
    $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento'] );
    $vendedorId= mysqli_real_escape_string( $db, $_POST['vendedor'] );
    $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion'] );
    $creado = date('Y/m/d');

    // Alerts Validaciones
    //Asignar Files a una variable
    
    $imagen = $_FILES['imagen'];
    

    if (!$titulo) {
        $errores[] = "Debes añadir un titulo";
    }
    if (!$precio) {
        $errores[] = "Debes añadir un precio";
    }
    if ( strlen($descripcion) < 50 ) {
        $errores[] = "Debes añadir una descripcion de al menos 50 caracteres";
    }
    if (!$habitaciones) {
        $errores[] = "Debes añadir un num de habitaciones";
    }
    if (!$wc) {
        $errores[] = "Debes añadir un num de baños";
    }
    if (!$estacionamiento) {
        $errores[] = "Debes añadir un num de estacionamientos";
    }
    if (!$vendedorId) {
        $errores[] = "Debes seleccionar a un vendedor";
    }
    if ( !($imagen['name']) || $imagen['error'] ) {
        $errores[] = "La imagen es obligatoria";
    }

    // Validad tamaño de imagenes (100kb maximo)
    $medida = 1000 * 2000;
    
    if ($imagen['size'] > $medida) {
        $errores[] = "La imagen es muy pesada";
    }

    // echo '<pre>';
    // var_dump($errores);
    // echo '</pre>';

    // Revisar que el array este vacio
    if (empty($errores)) {
        // Subida de archivos


        // Crear una carpeta
        $carpetaImagenes = '../../imagenes/';

        if ( !is_dir($carpetaImagenes) ) {
            mkdir($carpetaImagenes);
        }

        // Generar un nombre unico
        $nombreUnico = md5( uniqid( rand(), true )) . ".jpg";

        // Subir la imagen
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreUnico );
   

        // Insertar en la base de datos
        $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) 
        VALUES ( '$titulo', '$precio', '$nombreUnico', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId' ) ";
    
        $resultado = mysqli_query($db, $query);
    
        if ($resultado) {
            // Redireccionar Al Usuario
            header('Location: /admin?resultado=1');
        }
    }


}

    incluirTemplate('header');
?>


    <main class="contenedor seccion">
        <h1>Crear</h1>


        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion General</legend>

                <label for="titulo">Titulo:</label>
                <input 
                type="text" 
                id="titulo" 
                name="titulo" 
                placeholder="Titulo Propiedad" 
                value="<?php echo $titulo; ?>"
                >

                <label for="precio">Precio:</label>
                <input 
                type="number" 
                id="precio" 
                name="precio" 
                placeholder="Precio Propiedad" 
                value="<?php echo $precio; ?>"
                >

                <label for="imagen">Imagen:</label>
                <input 
                type="file" 
                id="imagen" 
                accept="image/jpeg, image/png" name="imagen">

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
                
            </fieldset>

            <fieldset>

                <legend>Informacion de la propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input 
                type="number" 
                id="habitaciones" 
                name="habitaciones" 
                placeholder="Ej:3" 
                min="1" 
                max="9" 
                value="<?php echo $habitaciones; ?>"
                >

                <label for="wc">Baños:</label>
                <input 
                type="number" 
                id="wc" 
                name="wc" 
                placeholder="Ej:2" 
                min="1" 
                max="9" 
                value="<?php echo $wc; ?>"
                >

                <label for="estacionamiento">Estacionamiento:</label>
                <input 
                type="number" 
                id="estacionamiento" 
                name="estacionamiento" 
                placeholder="Ej:3" 
                min="1" 
                max="9" 
                value="<?php echo $estacionamiento; ?>"
                >

            </fieldset>

            <fieldset>

                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">--Seleccione--</option>

                    <?php  while( $vendedor = mysqli_fetch_assoc($resultado) ): ?>
                        <option
                        <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> 
                        value="<?php echo $vendedor['id']; ?>">
                        <?php echo $vendedor['nombre']. " " . $vendedor['apellido']; ?>
                        </option>
                    <?php endwhile; ?>

                </select>

            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

    <?php 
    incluirTemplate('footer');
?>