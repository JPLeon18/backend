<?php

require_once "vendor/autoload.php";

$app = new \Slim\Slim();

$dbconect = new mysqli("localhost","root","","curso_angular4");


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

$app->run();