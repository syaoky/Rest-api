<?php
require_once "db.php";
$connect = new Db();

$sql = "SHOW TABLES LIKE 'Tasks'";
$table = $connect->conn->query($sql);
$res = $table->fetch(PDO::FETCH_ASSOC);
if ($res == false) {
    $sql = "CREATE TABLE Tasks2 (ID bigint(20) NOT NULL AUTO_INCREMENT, TITLE varchar(255) not null, DESCRIPTION  text, DUE_DATE  datetime, create_date TIMESTAMP, priority varchar(20), category varchar(20), status varchar(20), PRIMARY KEY (ID))";
    $res = $connect->conn->query($sql);
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
    if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
        $method = 'DELETE';
    } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
        $method = 'PUT';
    } else {
        throw new Exception("Unexpected Header");
    }
}

//POST



if ($method == 'POST') {
    $params = [
        'TITLE' => 'title',
        'DESCRIPTION' => 'description',
        'DUE_DATE' => 'due_date',
        'PRIORITY' => 'priority',
        'CATEGORY' => 'category',
        'STATUS' => 'status'
    ];
    if (isset($_POST["title"]) && $_POST["title"] != '') {
        $date = date('Y.m.d H:i:s');
        $sqlOne = "INSERT INTO Tasks ( CREATE_DATE";
        $sqlTwo = " VALUES( '" . $date . "'";

        $count = 0;
        foreach ($params as $key => $param) {
            if (isset($_POST[$param])) {
                $sqlOne .= "," . $key;
                $sqlTwo .= ",'" . $_POST[$param] . "'";
            }

        }
        $sqlOne .= ")";
        $sqlTwo .= ");";

        $res = $connect->conn->query($sqlOne . $sqlTwo);
        $id = $connect->conn->lastInsertId();
        echo json_encode(["id" => $id, "message" => "Task created successfully"]);


    } else {
        echo json_encode(["error" => '', "message" => "укажите title"]);
    }

    //GET

} elseif ($method == 'GET') {

    if ($_GET == false) {
        $sql = "SELECT * FROM Tasks;";
        $res = $connect->conn->query($sql);
        $data = [];
        while ($result = $res->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $result;
        }
        echo json_encode($data);
    } elseif (isset($_GET["id"])) {
        $sql = "SELECT * FROM Tasks WHERE ID = '" . $_GET["id"] . "';";
        $res = $connect->conn->query($sql);
        $result = $res->fetch(PDO::FETCH_ASSOC);
        if ($result == false || empty($_GET["id"])) {
            echo json_encode(["messege" => "id is no have by Data base"]);
        } else {
            echo json_encode($result);
        }
    } elseif (isset($_GET["search"]) || isset($_GET["sort"])) {
        if (isset($_GET["search"]) && isset($_GET["sort"])) {
            $sql = "  SELECT * FROM Tasks  WHERE TITLE LIKE '%" . $_GET["search"] . "%' ORDER BY " . $_GET["sort"] . " ASC;  ";
        } elseif (isset($_GET["sort"])) {
            $sql = "  SELECT * FROM Tasks  ORDER BY " . $_GET["sort"] . " ASC;  ";
        } else {
            $sql = " SELECT * FROM Tasks  WHERE TITLE LIKE '%" . $_GET["search"] . "%'";
        }
        $res = $connect->conn->query($sql);
        $result = $res->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
        $data = [];
        while ($result = $res->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $result;
        }
        echo json_encode($data);


    }

    // PUT

} elseif ($method == 'PUT') {
    $_REQUEST["due_date"] = date('Y.m.d H:i:s', strtotime('2022-01-10T10:20:22'));
    $sql = "UPDATE Tasks
    SET TITLE = '" . $_REQUEST["title"] . "', DESCRIPTION = '" . $_REQUEST["description"] . "', DUE_DATE =  '" . $_REQUEST["due_date"] . "', PRIORITY = '" . $_REQUEST["priority"] . "', CATEGORY = '" . $_REQUEST["category"] . "', STATUS = '" . $_REQUEST["status"] . "'
    WHERE ID = '" . $_REQUEST["id"] . "';";
    $res = $connect->conn->query($sql);
    if ($res == true) {
        echo json_encode(["message" => "Task updated successfully"]);
    } else {
        echo json_encode(["message" => "Error updated task"]);
    }



    // DELETE

} elseif ($method == 'DELETE') {
    print_r($_REQUEST);
    $sql = "DELETE FROM Tasks WHERE ID = '" . $_REQUEST["id"] . "';";
    $res = $connect->conn->query($sql);
    if ($res == true) {
        echo json_encode(["message" => "Task deleted successfully"]);
    } else {
        echo json_encode(["message" => "Error deleted task"]);
    }
}


