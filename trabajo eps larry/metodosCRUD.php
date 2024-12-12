<?php
$host = 'localhost';
$dbname = 'Sisben';
$usuario = 'root';
$contraseña = '';



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contraseña);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("¡Error en la conexión a la base de datos!: " . $e->getMessage());
}

// Crear la tabla Productos si no existe
try {
    $sql = "CREATE TABLE IF NOT EXISTS Productos (
                id_producto INT AUTO_INCREMENT PRIMARY KEY,
                nombre_producto VARCHAR(100) NOT NULL,
                lote_producto VARCHAR(50) NOT NULL,
                valor DECIMAL(10, 2) NOT NULL
            )";
    $pdo->exec($sql);
    echo "Tabla 'Productos' creada o ya existe.<br>";
} catch (PDOException $e) {
    echo "Error al crear la tabla 'Productos': " . $e->getMessage() . "<br>";
}

// Crear la tabla Clientes si no existe (puede que falte en el código original)
try {
    $sql = "CREATE TABLE IF NOT EXISTS Clientes (
                id_cliente INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                apellido VARCHAR(100) NOT NULL,
                tipo_documento VARCHAR(50) NOT NULL,
                numero_documento VARCHAR(50) UNIQUE NOT NULL,
                telefono VARCHAR(50) NOT NULL,
                fechnacimiento DATE NOT NULL
            )";
    $pdo->exec($sql);
    echo "Tabla 'Clientes' creada o ya existe.<br>";
} catch (PDOException $e) {
    echo "Error al crear la tabla 'Clientes': " . $e->getMessage() . "<br>";
}

// Crear la tabla Facturas si no existe
try {
    $sql = "CREATE TABLE IF NOT EXISTS Facturas (
                id_factura INT AUTO_INCREMENT PRIMARY KEY,
                numero_factura VARCHAR(50) UNIQUE NOT NULL,
                id_cliente INT NOT NULL,
                id_producto INT NOT NULL,
                cantidad INT NOT NULL,
                valor_total DECIMAL(12, 2) NOT NULL,
                FOREIGN KEY (id_cliente) REFERENCES Clientes(id_cliente),
                FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
            )";
    $pdo->exec($sql);
    echo "Tabla 'Facturas' creada o ya existe.<br>";
} catch (PDOException $e) {
    echo "Error al crear la tabla 'Facturas': " . $e->getMessage() . "<br>";
}

// Funciones CRUD para Clientes
function crearCliente($pdo, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $fechnacimiento) {
    try {
        $sql = "INSERT INTO Clientes (nombre, apellido, tipo_documento, numero_documento, telefono, fechnacimiento) 
                VALUES (:nombre, :apellido, :tipo_documento, :numero_documento, :telefono, :fechnacimiento)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':fechnacimiento', $fechnacimiento);
        $stmt->execute();
        echo "Cliente creado exitosamente.<br>";
        
    } catch (PDOException $e) {
        echo "Error al crear cliente: " . $e->getMessage() . "<br>";
    }
}

if (isset($_POST['accion']) && $_POST['accion'] === 'create') {
    crearCliente(
        $pdo,
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['tipo_documento'],
        $_POST['numero_documento'],
        $_POST['telefono'],
        $_POST['fechnacimiento']
    );
}


function leerCliente($pdo, $numero_documento) {
    try {
        $sql = "SELECT * FROM Clientes WHERE numero_documento = :numero_documento";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            echo "ID Cliente: " . $cliente['id_cliente'] . "<br>";
            echo "Nombre: " . $cliente['nombre'] . "<br>";
            echo "Apellido: " . $cliente['apellido'] . "<br>";
            echo "Tipo de Documento: " . $cliente['tipo_documento'] . "<br>";
            echo "Número de Documento: " . $cliente['numero_documento'] . "<br>";
            echo "Teléfono: " . $cliente['telefono'] . "<br>";
            echo "Fecha de Nacimiento: " . $cliente['fechnacimiento'] . "<br><br>";
        } else {
            echo "No se encontró un cliente con el número de documento proporcionado.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al buscar cliente: " . $e->getMessage() . "<br>";
    }
}

function actualizarCliente($pdo, $numero_documento, $nombre, $apellido, $telefono) {
    try {
        $sql = "UPDATE Clientes 
                SET nombre = :nombre, apellido = :apellido, telefono = :telefono 
                WHERE numero_documento = :numero_documento";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':fechnacimiento', $fechnacimiento);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->execute();

        if ($stmt->execute()) {
            echo "Cliente actualizado exitosamente.<br>";
        } else {
            echo "Error al actualizar cliente.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al actualizar cliente: " . $e->getMessage() . "<br>";
    }
}

function eliminarCliente($pdo, $numero_documento) {
    try {
        $sql = "DELETE FROM Clientes WHERE numero_documento = :numero_documento";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_documento', $numero_documento);

        if ($stmt->execute()) {
            echo "Cliente eliminado exitosamente.<br>";
        } else {
            echo "Error al eliminar cliente.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al eliminar cliente: " . $e->getMessage() . "<br>";
    }
}

// Funciones CRUD para Productos
function crearProducto($pdo, $nombre_producto, $lote_producto, $valor) {
    try {
        $sql = "INSERT INTO Productos (nombre_producto, lote_producto, valor) 
                VALUES (:nombre_producto, :lote_producto, :valor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':lote_producto', $lote_producto);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        echo "Producto creado exitosamente.<br>";
    } catch (PDOException $e) {
        echo "Error al crear producto: " . $e->getMessage() . "<br>";
    }
}

if (isset($_POST['accion']) && $_POST['accion'] === 'create') {
    crearProducto(
        $pdo,
        $_POST['nombre_producto'],
        $_POST['lote_producto'],
        $_POST['valor']
    );

}    

function leerProducto($pdo, $id_producto) {
    try {
        $sql = "SELECT * FROM Productos WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo "ID Producto: " . $producto['id_producto'] . "<br>";
            echo "Nombre Producto: " . $producto['nombre_producto'] . "<br>";
            echo "Lote Producto: " . $producto['lote_producto'] . "<br>";
            echo "Valor: " . $producto['valor'] . "<br><br>";
        } else {
            echo "No se encontró un producto con el ID proporcionado.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al buscar producto: " . $e->getMessage() . "<br>";
    }
}

function actualizarProducto($pdo, $id_producto, $nombre_producto, $lote_producto, $valor) {
    try {
        $sql = "UPDATE Productos 
                SET nombre_producto = :nombre_producto, lote_producto = :lote_producto, valor = :valor
                WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':lote_producto', $lote_producto);
        $stmt->bindParam(':valor', $valor);

        if ($stmt->execute()) {
            echo "Producto actualizado exitosamente.<br>";
        } else {
            echo "Error al actualizar producto.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al actualizar producto: " . $e->getMessage() . "<br>";
    }
}

function eliminarProducto($pdo, $id_producto) {
    try {
        $sql = "DELETE FROM Productos WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto);

        if ($stmt->execute()) {
            echo "Producto eliminado exitosamente.<br>";
        } else {
            echo "Error al eliminar producto.<br>";
        }
    } catch (PDOException $e) {
        echo "Error al eliminar producto: " . $e->getMessage() . "<br>";
    }
}

// Funciones CRUD para Facturas

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'epsee';
$usuario = 'root';
$contraseña = '';

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contraseña);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("¡Error en la conexión a la base de datos!: " . $e->getMessage());
}

// Funciones CRUD para "factura"
function crearFactura($pdo, $numero_factura, $id_cliente, $id_producto, $cantidad, $valor_total) {
    try {
        $sql = "INSERT INTO Facturas (numero_factura, id_cliente, id_producto, cantidad, valor_total) 
                VALUES (:numero_factura, :id_cliente, :id_producto, :cantidad, :valor_total)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_factura', $numero_factura);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':valor_total', $valor_total);
        $stmt->execute();
        echo "Factura creada exitosamente.";
    } catch (PDOException $e) {
        echo "Error al crear factura: " . $e->getMessage();
    }
}

function leerFactura($pdo, $numero_factura) {
    try {
        $sql = "SELECT * FROM Facturas WHERE numero_factura = :numero_factura";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_factura', $numero_factura);
        $stmt->execute();
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($facturas) {
            foreach ($facturas as $factura) {
                echo "Número de Factura: " . $factura['numero_factura'] . "<br>";
                echo "Cliente ID: " . $factura['id_cliente'] . "<br>";
                echo "Producto ID: " . $factura['id_producto'] . "<br>";
                echo "Cantidad: " . $factura['cantidad'] . "<br>";
                echo "Valor Total: " . $factura['valor_total'] . "<br><br>";
            }
        } else {
            echo "No se encontraron facturas con el número proporcionado.";
        }
    } catch (PDOException $e) {
        echo "Error al buscar factura: " . $e->getMessage();
    }
}

function actualizarFactura($pdo, $numero_factura, $id_cliente, $id_producto, $cantidad, $valor_total) {
    try {
        $sql = "UPDATE Facturas SET id_cliente = :id_cliente, id_producto = :id_producto, 
                cantidad = :cantidad, valor_total = :valor_total WHERE numero_factura = :numero_factura";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_factura', $numero_factura);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':valor_total', $valor_total);

        if ($stmt->execute()) {
            echo "Factura actualizada exitosamente.";
        } else {
            echo "Error al actualizar factura.";
        }
    } catch (PDOException $e) {
        echo "Error al actualizar factura: " . $e->getMessage();
    }
}

function eliminarFactura($pdo, $numero_factura) {
    try {
        $sql = "DELETE FROM Facturas WHERE numero_factura = :numero_factura";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numero_factura', $numero_factura);

        if ($stmt->execute()) {
            echo "Factura eliminada exitosamente.";
        } else {
            echo "Error al eliminar factura.";
        }
    } catch (PDOException $e) {
        echo "Error al eliminar factura: " . $e->getMessage();
    }
}

// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';
    $numero_factura = htmlspecialchars(trim($_POST['numero_factura'] ?? ''));
    $id_cliente = htmlspecialchars(trim($_POST['id_cliente'] ?? ''));
    $id_producto = htmlspecialchars(trim($_POST['id_producto'] ?? ''));
    $cantidad = htmlspecialchars(trim($_POST['cantidad'] ?? ''));
    $valor_total = htmlspecialchars(trim($_POST['valor_total'] ?? ''));

    // Validación básica para asegurarse de que los campos necesarios no estén vacíos
    if (!$numero_factura || !$id_cliente || !$id_producto || !$cantidad || !$valor_total) {
        echo "Error: todos los campos son obligatorios.";
        return;
    }

    switch ($accion) {
        case 'create':
            if ($numero_factura && $id_cliente && $id_producto && $cantidad && $valor_total) {
                crearFactura($pdo, $numero_factura, $id_cliente, $id_producto, $cantidad, $valor_total);
            }
            break;
        case 'read':
            leerFactura($pdo, $numero_factura);
            break;
        case 'update':
            if ($numero_factura) {
                actualizarFactura($pdo, $numero_factura, $id_cliente, $id_producto, $cantidad, $valor_total);
            }
            break;
        case 'delete':
            if ($numero_factura) {
                eliminarFactura($pdo, $numero_factura);
            }
            break;
        default:
            echo "Error: acción no reconocida.";
            break;
    }
}
?>
