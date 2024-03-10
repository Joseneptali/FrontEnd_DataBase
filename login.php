<?php
// Reemplazar "tu_clave_secreta_recaptcha" con tu clave secreta de reCAPTCHA
$recaptcha_secret_key = "tu_clave_secreta_recaptcha";

// Verificar el reCAPTCHA
if (isset($_POST['g-recaptcha-response'])) {
    $captcha = $_POST['g-recaptcha-response'];

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret_key&response=$captcha");

    $response = json_decode($response);

    if (!$response->success) {
        // reCAPTCHA inválido
        header("Location: index.html");
        exit;
    }
}

// Evitar SQL Injection
function sanitize_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Conectar a la base de datos
$servername = "nombre_servidor";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "nombre_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);

    // Encriptar la contraseña (usando Bcrypt)
    $password = password_hash($password, PASSWORD_BCRYPT);

    // Consulta SQL segura para evitar SQL Injection
    $sql = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();

    $result = $sql->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($_POST["password"], $row["password"])) {
            // Login exitoso
            header("Location: dashboard.php");
            exit;
        } else {
            // Login fallido
            header("Location: index.html");
            exit;
        }
    } else {
        // Usuario no encontrado
        header("Location: index.html");
        exit;
    }
}
?>
