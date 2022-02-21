<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//get all aircrafts in the system
$app->get('/aircrafts/all',function ($request,$responce,$args) {
    
    $sql="SELECT * FROM aircrafts ORDER BY priority ASC , id ASC;";
    if($_GET['standby']=='true'?? null){
     $sql="SELECT * FROM aircrafts WHERE status='standby' ORDER BY priority ASC , id ASC;";
    }
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $aircrafts = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
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



//get specfic aircraft 
$app->get('/aircrafts/{id}',function ($request,$responce,$args) {
    $id=$args['id'];
    $sql="SELECT * FROM aircrafts WHERE id = $id";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $aircrafts = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
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

//add aircraft into the system

$app->post('/aircrafts/add',function ($request,$responce,$args) {
    // Get all POST parameters
    $params = $request->getParsedBody();
    $name=$params['name'];
    $type=$params['type'];
    $size=$params['size'];
    $status='standby';//default status
    //validate input 
    $type_values=array('emergency','vip','passenger','cargo');
    $size_values=array('small','large');
    if(!in_array($type,$type_values)||!in_array($size,$size_values)){
        $responce->getBody()->write(json_encode('Check Values'));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(500);
    }
    //decide priority based on type / size # we added a zero to avoid confustion with size score
    switch ($type){
        case "emergency";
        $type_score=10;
        break;
        case "vip";
        $type_score=20;
        break;
        case "passenger";
        $type_score=30;
        break;
        case "cargo";
        $type_score=40;
        break;  
    }
    switch ($size){
        case "large";
        $size_score=1;
        break;
        case "small";
        $size_score=2;
        break; 
    }
    $priority_score = $type_score + $size_score; //calcualte by adding the two scores lower is higher in sort 

    $sql="INSERT INTO aircrafts (name,type,size,status,priority) VALUE (:name,:type,:size,:status,:priority)";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->prepare($sql);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':size',$size);
        $stmt->bindParam(':type',$type);
        $stmt->bindParam(':status',$status);
        $stmt->bindParam(':priority',$priority_score);
        
        $result=$stmt->execute();
        $db=null;
        $responce->getBody()->write(json_encode($result));
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

// dequeue air craft by order
$app->post('/aircrafts/dequeue/',function ($request,$responce,$args) {
    // select lowest piroirty and lowest id as first come 
    $sql="SELECT * FROM aircrafts WHERE status = 'standby'
    ORDER BY priority ASC , id ASC LIMIT 1";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $aircrafts = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        // update the status of the aircraft 
        $aircraft=$aircrafts[0];
        $update="UPDATE aircrafts SET status='Awaiting Departure' WHERE id =$aircraft->id;";
        //add recored to the queue table
        $move="INSERT INTO departures SELECT * FROM aircrafts WHERE id = $aircraft->id;";
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
// dequeue air craft by id
$app->post('/aircrafts/dequeue/{id}',function ($request,$responce,$args) {
    $id=$args['id'];
    // select lowest piroirty and lowest id as first come 
    $sql="SELECT * FROM aircrafts WHERE id = $id ;";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->query($sql);
        $aircrafts = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        // update the status of the aircraft 
        $aircraft=$aircrafts[0];
        $update="UPDATE aircrafts SET status='Awaiting Departure' WHERE id =$aircraft->id;";
        //add recored to the queue table
        $move="INSERT INTO departures SELECT * FROM aircrafts WHERE id = $aircraft->id;";
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