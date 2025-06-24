# API Portafolio Oscar Cariaga
Este repositorio contiene el código del proyecto de la API de mi portafolio y una descripción general del desarrollo del proyecto.\
El proyecto es una API, que se administra con una interfaz sencilla. Para proteger la API, se implementó verificación de sesión para los métodos **POST**, **DELETE** y **PATCH**, permitiendo a la API solo entregar información por el método **GET** de forma libre.\
[Enlace del proyecto](https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/)

## Tecnologías utilizadas
Este proyecto se desarrolló en PHP y MySQLi.\
Para el diseño se usó Bootstrap, con ajustes mínimos en CSS.

## Uso de IA
Para este proyecto se usó **ChatGPT** de forma general.\
[Chat](https://chatgpt.com/share/685aee54-9bd4-800c-9b6d-d08bb644af52)
El código más relevante es el siguiente:\
\
Este código dentro de *proyectos.php* permitió arreglar un problema interno relacionado al servidor usado, el cual solo permite los métodos **GET** y **POST**. Con este código, una solicitud **POST** puede contener un atributo personalizado, más notoriamente para permitir **DELETE** y **PATCH**.
```php
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
```
En *delete.php* y en *edit.php* se necesitaron usar atributos personalizados por lo mencionado anteriormente, ambos arreglos entregados por **ChatGPT**. A continuación se muestra como se implementa en *delete.php*:
```php
$id = intval($_GET['id']);

session_write_close();

$ch = curl_init("https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php?id=$id");

curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Cookie: PHPSESSID=' . $_COOKIE["PHPSESSID"]],
    CURLOPT_POSTFIELDS => json_encode(['_method' => 'DELETE']),
]);

curl_exec($ch);
curl_close($ch);
```
Por otro lado, el login necesitó ser adaptado a la API. **ChatGPT** entregó el siguiente código:
```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $password = md5($_POST['password']);

  // Prevención de injecciones SQL
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 1) {
    $_SESSION['user'] = $username;
    header("Location: index.php");
    exit;
  } else {
    $error = "Credenciales incorrectas.";
  }
}
```
