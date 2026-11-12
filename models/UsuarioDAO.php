
<?php

include_once __DIR__ . '/Usuario.php';
include_once __DIR__ . '/../config/dataBase.php';

class UsuarioDAO {

    // Obtener usuario por ID
    public static function getUsuarioByID($id) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "SELECT * FROM usuarios WHERE id = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $results = $stmt->get_result();
        $usuario = $results->fetch_object('Usuario');

        $stmt->close();
        $con->close();

        return $usuario;
    }

    // Obtener todos los usuarios
    public static function getUsuarios() {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "SELECT * FROM usuarios"
        );

        $stmt->execute();
        $results = $stmt->get_result();

        $listaUsuarios = [];

        while ($usuario = $results->fetch_object('Usuario')) {
            $listaUsuarios[] = $usuario;
        }

        $stmt->close();
        $con->close();

        return $listaUsuarios;
    }

    // Autenticar usuario (LOGIN)
    public static function login($username, $password) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "SELECT * FROM usuarios
             WHERE nombre = ? OR email = ?
             LIMIT 1"
        );

        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();

        $results = $stmt->get_result();

        $usuario = false;

        if ($row = $results->fetch_object('Usuario')) {

            // Comprobar contraseña
            if (password_verify($password, $row->getPassword())) {
                $usuario = $row;
            }
        }

        $stmt->close();
        $con->close();

        return $usuario;
    }

    // Buscar usuario por email
    public static function findByEmail($email) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "SELECT * FROM usuarios WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $results = $stmt->get_result();
        $usuario = $results->fetch_object('Usuario');

        $stmt->close();
        $con->close();

        return $usuario;
    }

    // Actualizar contraseña
    public static function updatePassword($id, $new_password) {
        $con = DataBase::connect();

        $hashed_password = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );

        $stmt = $con->prepare(
            "UPDATE usuarios SET password = ? WHERE id = ?"
        );

        $stmt->bind_param('si', $hashed_password, $id);
        $result = $stmt->execute();

        $stmt->close();
        $con->close();

        return $result;
    }

    // Insertar nuevo usuario
    public static function insertUsuario(
        $nombre,
        $email,
        $password,
        $telefono = '',
        $direccion = '',
        $fecha_nacimiento = null
    ) {
        $con = DataBase::connect();

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // El rol se asigna desde el servidor
        $rol = 'usuario';

        $stmt = $con->prepare(
            "INSERT INTO usuarios
            (nombre, email, password, telefono, direccion, fecha_nacimiento, rol)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssssss',
            $nombre,
            $email,
            $hashed_password,
            $telefono,
            $direccion,
            $fecha_nacimiento,
            $rol
        );

        $result = $stmt->execute();

        $stmt->close();
        $con->close();

        return $result;
    }

    // Asignar rol a un usuario por email
    public static function setUserRoleByEmail($email, $role) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "UPDATE usuarios SET rol = ? WHERE email = ?"
        );

        $stmt->bind_param('ss', $role, $email);
        $result = $stmt->execute();

        $stmt->close();
        $con->close();

        return $result;
    }

    // Actualizar usuario
    public static function updateUsuario($id, $nombre, $email, $rol) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "UPDATE usuarios
             SET nombre = ?, email = ?, rol = ?
             WHERE id = ?"
        );

        $stmt->bind_param('sssi', $nombre, $email, $rol, $id);
        $result = $stmt->execute();

        $stmt->close();
        $con->close();

        return $result;
    }

    // Eliminar usuario
    public static function deleteUsuario($id) {
        $con = DataBase::connect();

        $stmt = $con->prepare(
            "DELETE FROM usuarios WHERE id = ?"
        );

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        $stmt->close();
        $con->close();

        return $result;
    }
}

?>
