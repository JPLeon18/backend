<?php

require_once "vendor/autoload.php";

require_once "piramide-uploader/PiramideUploader.php";

$app = new \Slim\Slim();

$dbconect = new mysqli("localhost","root","","curso_angular4");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}



// ---- LISTAR PRODUCTOS ---------------------------------

$app->get("/products", function () use ($app,$dbconect){

    $query = $dbconect ->query("SELECT * FROM productos ORDER BY id DESC");

    $productos = array();
    while ($producto = $query ->fetch_assoc()){
            $productos[]=$producto;
    }

    $result = array(
        'status' => 'success',
        'code' => 200,
        'data' => $productos
    );


    echo json_encode($result);
});


// ---- ACTUALIZAR DATOS --------------------------------

$app->post("/update-product/:id", function ($id)use ($dbconect, $app){

    $json = $app ->request->post('json');
    $data = json_decode($json, true);

    $sql = "UPDATE productos SET ".
        "nombre = '{$data['nombre']}',".
        "descripcion = '{$data['descripcion']}',";
    if (isset($data['imagen'])){
        $sql .= "imagen = '{$data['imagen']}',";
    }
        $sql.= "precio ='{$data['precio']}' WHERE id = {$id}";

    $query = $dbconect->query($sql);

    if ($query){
        $result =array(
            'status' => 'Success',
            'code' => 200,
            'message' => 'Actualizado'
        );
    }else{
        $result = array(
            'status' => 'Error',
            'code' => 404,
            'message' => 'No actualizado'
        );
    }

    echo json_encode($result);
});

// ---- DEVOLVER UN PRODUCTO -----------------------------

$app->get("/products/:id",function ($id)use ($app,$dbconect){

    $query = $dbconect->query( 'SELECT * FROM productos WHERE id ='. $id);

    if ($query->num_rows == 1){
        $producto = $query->fetch_assoc();

        $result = array(
            'status' => 'success',
            'code' => 200,
            'data' => $producto
        );

    }else{
        $result = array(
            'status' => 'Error',
            'code' => 404,
            'message' => 'Producto no encontrado'
        );
    }

    echo json_encode($result);
});


// ---- ELIMINAR PRODUCTO --------------------------------

$app->get("/delete-product/:id", function ($id)use ($app,$dbconect){

    $query = $dbconect->query('DELETE FROM productos WHERE id ='.$id);


    if ($query){
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto eliminado correctamente'
        );
    }else{
        $result = array(
            'status' => 'Error',
            'code' => 404,
            'message' => 'Producto no encontrado'
        );
    }

    echo json_encode($result);

});



// ----- GUARDAR PRODUCTO --------------------------------

$app->post("/add_product", function ()use($app, $dbconect) {

    $json = $app->request->post('json');
    $data = json_decode($json, true);


    if (!isset($data['nombre'])){
        $data['nombre'] = null;
    }
    if (!isset($data['descripcion'])){
        $data['descripcion'] = null;
    }
    if (!isset($data['precio'])){
        $data['precio'] = null;
    }
    if (!isset($data['imagen'])){
        $data['imagen'] = null;
    }

        $query = "INSERT INTO productos VALUES (NULL,".
                "'{$data['nombre']}',".
                "'{$data['descripcion']}',".
                "'{$data['precio']}',".
                "'{$data['imagen']}'".
                ");";

        $insert = $dbconect->query($query);

        if ($insert){
            $result = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Producto ingresado'
            );
        }else{
            $result = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Producto No indestado'
            );
        }

        echo json_encode($result);
});


// GUARDAR UNA IMAGEN ----------------------------------

$app->post("/upload-file", function ()use ($app,$dbconect){


    if (isset($_FILES['upload'])){

        $piramideUploader = new PiramideUploader();

        $upload = $piramideUploader->upload('img','upload','uploads', array('image/jpg', 'image/png', 'image/jpeg'));

        $file = $piramideUploader->getInfoFile();

        $file_name = $file['complete_name'];

        if (isset($upload) && $upload["uploaded"] == false){
            $result = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Imagen No subida'
            );
        }else{
            $result = array(
                'status' => 'Success',
                'code' => 200,
                'filename' => $file_name
            );
        }

    }

    echo json_encode($result);

});


$app->run();