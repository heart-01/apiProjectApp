<?php

use Slim\Http\Request;
use Slim\Http\Response;

//ล็อกอิน
$app->get('/login/[{username},{password}]',function($request,$response,$args){
    $sth=$this->db->prepare('SELECT * FROM student WHERE username=:a AND password=:b');
    $sth->bindParam("a",$args['username']);  
    $p=md5($args['password']);
    $sth->bindParam("b",$p);  
    $sth->execute();
    $student=$sth->fetchObject(); 
    if($student!=""){
        return $this->response->withJson($student);
    }else if($student==""){
        return $this->response->withJson('Register');
    }
});

//ฟังชั่น api เพิ่มข้อมูลสมาชิก
$app->post('/addStudent',function($request,$response){
    $input=$request->getParsedBody(); //รับการส่งข้อมูลจาก Body

    $sql="SELECT idStu FROM student WHERE idStu=:idStu";
    $sth=$this->db->prepare($sql);
    $sth->bindParam("idStu",$input['idStu']);
    $sth->execute();
    $ckID=$sth->fetchObject(); 
    if($ckID!=""){ //ถ้า fetchObject แล้วมีข้อมูล
        return $this->response->withJson('repeat idStu'); //ให้ส่งค่า json เป็น repeat idStu
    }else if($ckID==""){

        $sql="SELECT username FROM student WHERE username=:username";
        $sth=$this->db->prepare($sql);
        $sth->bindParam("username",$input['username']);
        $sth->execute();
        $ckUser=$sth->fetchObject(); 
        if($ckUser!=""){
            return $this->response->withJson('repeat username');
        }else{

            $sql="INSERT INTO student (idStu,idStatus,nameStu,emailStu,telStu,username,password) 
            VALUES (:idStu,'2',:nameStu,:emailStu,:telStu,:username,:password)";
            $sth=$this->db->prepare($sql);
            $sth->bindParam("idStu",$input['idStu']);  //$input[ข้างในนี้คือค่า name ที่ส่งมาจากฟร์อม]
            $sth->bindParam("nameStu",$input['nameStu']);
            $sth->bindParam("emailStu",$input['emailStu']);
            $sth->bindParam("telStu",$input['telStu']);
            $sth->bindParam("username",$input['username']);
            $p=md5($input['password']);
            $sth->bindParam("password",$p);
            $sth->execute();
            
            $result=array('msg'=>true); //แสดง massage true
            return $this->response->withJson($result); //แสดงผลตัวแปร result มาเป็น Json        
        }
    }
});

//แสดงห้อง
$app->get('/showRoom',function($request,$response,$args){
    $sth = $this->db->prepare('SELECT * FROM room');
    $sth->execute();
    $room=$sth->fetchAll(); 
    return $this->response->withJson($room); 
});



/*
//เพิ่ม เมธอท เพื่อ 
//prepare เป็นการเตรียมคำสั่ง sql
//execute เปรียบเหมือนการ query
//fetchAll เปรียบเหมือนการ fetch_array

$app->get('/ShowCustomer',function($request,$response,$args){
    $sth = $this->db->prepare('SELECT * FROM customer');
    $sth->execute();
    $customers=$sth->fetchAll(); // fetchAll คือ การ fetch ทั้งหมดคืนค่าเป็น  array
    return $this->response->withJson($customers); // return ค่าเป็น Json
});

$app->get('/customer/[{id}]',function($request,$response,$args){
    $sth = $this->db->prepare('SELECT * FROM customer WHERE ID_customer=:a'); // หลัง : คือพารามิเตอร์ a คือ พารามิเตอร์
    $sth->bindParam("a",$args['id']); // bindParam คือการใส่ค่า $args['id'] ให้กับพารามิเตอร์ a 
    $sth->execute();
    $customer=$sth->fetchObject(); // fetchObject คือ fetch แค่ค่าเดียว ดึงมาใช้ได้เลยไม่ต้องวน loop
    return $this->response->withJson($customer);
});

//ฟังชั่นค้นหา
$app->get('/ShowCustomer/searching/[{query}]',function($request,$response,$args){
    $sth=$this->db->prepare('SELECT * FROM customer WHERE name_customer LIKE :q');
    $querys="%".$args['query']."%";
    $sth->bindParam("q",$querys);
    $sth->execute();
    $customer=$sth->fetchAll();
    return $this->response->withJson($customer);
});

//ฟังชั่น api เพิ่มข้อมูล
$app->post('/AddCustomer',function($request,$response){
    $input=$request->getParsedBody(); //รับการส่งข้อมูลจาก Body
    $sql="INSERT INTO customer (ID_customer, ID_status, name_customer, tel_customer, username_customer,password_customer) 
    VALUES (:ID_customer,:ID_status,:name_customer,:tel_customer,:username_customer,:password_customer)";
    $sth=$this->db->prepare($sql);
    $sth->bindParam("ID_customer",$input['ID_customer']);  //$input[ข้างในนี้คือค่า name ที่ส่งมาจากฟร์อม]
    $sth->bindParam("ID_status",$input['ID_status']);
    $sth->bindParam("name_customer",$input['name_customer']);
    $sth->bindParam("tel_customer",$input['tel_customer']);
    $sth->bindParam("username_customer",$input['username_customer']);
    $sth->bindParam("password_customer",$input['password_customer']);
    $sth->execute();
    
    $input['id']=$this->db->lastInsertID(); //แสดงข้อมูล auto id ที่เราได้เพิ่มล่าสุด ถ้าใน ฐานข้อมูลไม่มี auto number ไม่ต้องใส่
    $result=array('msg'=>true); //แสดง massage true
    return $this->response->withJson($result); //แสดงผลตัวแปร result มาเป็น Json
});

//ฟังชั่น api delete
$app->delete('/DelCustomer/[{id}]',function($request,$response,$args){
    $sth=$this->db->prepare("DELETE FROM customer WHERE ID_customer=:id");
    $sth->bindParam("id",$args['id']);
    $sth->execute();
    
    $result=array('msg'=>true); //แสดง massage true
    return $this->response->withJson($result);
});

//ฟั่งชั่น api update
$app->post('/UpdateCustomer/[{id}]',function($request,$response,$args){
    $input=$request->getParsedBody();
    //return $this->response->withJson($input);
    $sql="UPDATE customer SET ID_status=:ID_status,name_customer=:name_customer,tel_customer=:tel_customer,username_customer=:username_customer,password_customer=:password_customer 
    WHERE ID_customer=:id";
    $sth=$this->db->prepare($sql);
    $sth->bindParam("id",$args['id']);
    $sth->bindParam("ID_status",$input['ID_status']); //$input[ข้างในนี้คือค่า name ที่ส่งมาจากฟร์อม]
    $sth->bindParam("name_customer",$input['name_customer']);
    $sth->bindParam("tel_customer",$input['tel_customer']);
    $sth->bindParam("username_customer",$input['username_customer']);
    $sth->bindParam("password_customer",$input['password_customer']);
    $sth->execute();

    $result=$args['id']; //return id ของ row ที่ถูก update
    return $this->response->withJson($result); //แสดงผลตัวแปร result มาเป็น Json
});
*/