<?php


    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header("Access-Control-Allow-Headers: *");

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('conexion.php');
        require_once('randonText.php');

        //el triple === significa que la informacion este igual y que sea del mismo tipo 

        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        //var_dump($data);
        
        $numero_ticket = $data["numero_ticket"];
        $nombre_cliente = $data["nombre_cliente"];
        $calificacion = $data["calificacion"];
        $comentario = $data["comentario"];
        $codigo_qr = generateRandomString(20);

        //var_dump($codigo_qr);

        $mysqli->select_db("encuesta");

        $query_insert_encuesta = "insert into tbl_encuesta (numero_ticket, nombre_cliente, calificacion, comentario, codigo_qr) values (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query_insert_encuesta);
        $stmt->bind_param('ssiss', $numero_ticket , $nombre_cliente, $calificacion, $comentario, $codigo_qr);
        $stmt->execute();

        $result_insert_id = $mysqli->query("select LAST_INSERT_ID() as id");
        $row = $result_insert_id->fetch_row();
        $id_inserted = $row[0];

        header('Content-type: application/json; charset=utf-8');
		header("access-control-allow-origin: *");
        http_response_code(200);

        $data_response["numero_ticket"] = $numero_ticket;
        $data_response["nombre_cliente"] = $nombre_cliente;
        $data_response["calificacion"] = $calificacion;
        $data_response["comentario"] = $comentario;
        $data_response["codigo_qr"] = $codigo_qr;
        $data_response["encuesta_id"] = (int)$id_inserted;

        print(json_encode($data_response));

        $mysqli->close();

    }

?>