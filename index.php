<?php

require_once "vendor/autoload.php";

$app = new \Slim\Slim();

$dbconect = new mysqli("localhost","root","","curso_angular4");

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