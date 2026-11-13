

<?php

include_once __DIR__ . '/Usuario.php';
include_once __DIR__ . '/../config/dataBase.php';

class UsuarioDAO {

    // Obtener usuario por ID
    public static function getUsuarioByID($id) {
        $con = DataBase::connect();

        $sql = "SELECT * FROM usuarios WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_object('Usuario');

        $stmt->close();
        $con->close();

        return $usuario;
    }

    // Obtener todos los usuarios
    public static function getUsuarios() {
        $con = DataBase::connect();

        $sql = "SELECT * FROM usuarios";

        $stmt = $con->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $listaUsuarios = [];

        while ($usuario = $resultado->fetch_object('Usuario')) {
            $listaUsuarios[] = $usuario;
        }

        $stmt->close();
        $con->close();

        return $listaUsuarios;
    }

    // Iniciar sesión
    public static function login($username, $password) {
        $con = DataBase::connect();

        $sql = "SELECT * FROM usuarios
                WHERE nombre = ? OR email = ?
                LIMIT 1";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = false;

        if ($row = $resultado->fetch_object('Usuario')) {
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

        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_object('Usuario');

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

        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('si', $hashed_password, $id);
        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
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

        $rol = 'usuario';

        $sql = "INSERT INTO usuarios
                (nombre, email, password, telefono, direccion, fecha_nacimiento, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);

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

        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }

    // Asignar rol por email
    public static function setUserRoleByEmail($email, $role) {
        $con = DataBase::connect();

        $sql = "UPDATE usuarios SET rol = ? WHERE email = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('ss', $role, $email);
        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }

    // Actualizar usuario
    public static function updateUsuario($id, $nombre, $email, $rol) {
        $con = DataBase::connect();

        $sql = "UPDATE usuarios
                SET nombre = ?, email = ?, rol = ?
                WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('sssi', $nombre, $email, $rol, $id);
        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }

    // Eliminar usuario
    public static function deleteUsuario($id) {
        $con = DataBase::connect();

        $sql = "DELETE FROM usuarios WHERE id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $id);
        $resultado = $stmt->execute();

        $stmt->close();
        $con->close();

        return $resultado;
    }
}

?>
