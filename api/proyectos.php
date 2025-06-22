<?php
session_start();

include 'config.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

function getInput()
{
    $raw = file_get_contents("php://input");
    return json_decode($raw, true) ?? [];
}

// Verificar atributo personalizado en caso de PATCH o DELETE
$method = $_SERVER['REQUEST_METHOD'];
$input = [];
if ($method === 'POST') {
    $input = getInput();
    if (isset($input['_method'])) {
        $method = strtoupper($input['_method']);
    }
} elseif (in_array($method, ['PATCH', 'DELETE'])) {
    $input = getInput();
}

// Bloquea llamadas que no sean GET si el usuario no está ingresado.
if (!isset($_SESSION['user']) && $method !== 'GET') {
    http_response_code(403);
    echo json_encode(["error" => "Necesita permisos."]);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $res = $conn->query("SELECT * FROM proyectos WHERE id=$id");
            $data = $res->fetch_assoc();
            echo json_encode($data ?: []);
        } else {
            $res = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
            $out = [];
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
            echo json_encode($out);
        }
        break;

    case 'POST':
        $d = $input;
        if (!isset($d['titulo'], $d['descripcion'], $d['imagen'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing fields"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $d['titulo'], $d['descripcion'], $d['url_github'], $d['url_produccion'], $d['imagen']);
        $stmt->execute();
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        break;

    case 'PATCH':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing ID"]);
            exit;
        }
        $sets = [];
        foreach ($input as $k => $v) {
            if ($k === '_method') continue;
            $k = $conn->real_escape_string($k);
            $v = $conn->real_escape_string($v);
            $sets[] = "$k='$v'";
        }
        if (!$sets) {
            echo json_encode(["error" => "No fields to update"]);
            exit;
        }
        $sql = "UPDATE proyectos SET " . implode(",", $sets) . " WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'DELETE':
        // Código actualizado por ChatGPT
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing ID"]);
            exit;
        }

        // Se extrae el nombre de la imágen 
        $res = $conn->query("SELECT imagen FROM proyectos WHERE id = $id");
        $img = $res && $res->num_rows ? $res->fetch_assoc()['imagen'] : null;

        // Se elimina el projecto de la bd
        $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Si la imágen se encuentra, se elimina
        if ($stmt->affected_rows > 0 && $img) {
            $imgPath = __DIR__ . "/../uploads/$img";
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
            echo json_encode(["success" => true]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
