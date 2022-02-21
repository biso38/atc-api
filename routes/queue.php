<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//get all aircrafts waiting to depart
$app->get('/departures/all',function ($request,$responce) {
    $sql="SELECT * FROM departures ORDER BY priority ASC , id ASC;";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $departures = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        $responce->getBody()->write(json_encode($departures));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(200);
    }
    catch (PDOException $e){
        $error=array(
            "message"=>$e->getMessage()
        );
        $responce->getBody()->write(json_encode($error));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(500);
    }
});

// depart air craft by order
$app->post('/departures/depart/',function ($request,$responce,$args) {
    // select lowest piroirty and lowest id as first come 
    $sql="SELECT * FROM departures WHERE status = 'Awaiting Departure'
    ORDER BY priority ASC , id ASC LIMIT 1";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $aircrafts = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        // update the status of the aircraft 
        $aircraft=$aircrafts[0];
        $update="UPDATE departures SET status='Departed' WHERE id =$aircraft->id;
        UPDATE aircrafts SET status='Departed' WHERE id =$aircraft->id;";
        //delete recored from the queue table
        $move="DELETE FROM departures WHERE id = $aircraft->id;";
        $stmt= $conn->prepare($update.' '.$move);
        $stmt->execute();
        $responce->getBody()->write(json_encode($aircrafts));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(200);
    }
    catch (PDOException $e){
        $error=array(
            "message"=>$e->getMessage()
        );
        $responce->getBody()->write(json_encode($error));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(500);
    }
});