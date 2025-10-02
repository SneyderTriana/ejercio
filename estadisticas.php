<link rel="stylesheet" href="style.css">
<?php
/**
 * Programa para calcular la media y desviación estándar de un conjunto de números reales
 * Implementado con lista enlazada según PSP0
 * 
 * @author Angel Gonzalez
 * @version 1.2
 */

// Definición del nodo para la lista enlazada
class Nodo
{
    public $dato;
    public $siguiente;

    public function __construct($dato)
    {
        $this->dato = $dato;
        $this->siguiente = null;
    }
}

// Definición de la lista enlazada
class ListaEnlazada
{
    private $cabeza;
    private $tamano;

    public function __construct()
    {
        $this->cabeza = null;
        $this->tamano = 0;
    }

    public function insertar($dato)
    {
        $nuevoNodo = new Nodo($dato);

        if ($this->cabeza === null) {
            $this->cabeza = $nuevoNodo;
        } else {
            $actual = $this->cabeza;
            while ($actual->siguiente !== null) {
                $actual = $actual->siguiente;
            }
            $actual->siguiente = $nuevoNodo;
        }

        $this->tamano++;
    }

    public function obtenerTamano()
    {
        return $this->tamano;
    }

    public function obtenerDatos()
    {
        $datos = array();
        $actual = $this->cabeza;

        while ($actual !== null) {
            $datos[] = $actual->dato;
            $actual = $actual->siguiente;
        }

        return $datos;
    }

    public function iterar($callback)
    {
        $actual = $this->cabeza;

        while ($actual !== null) {
            $callback($actual->dato);
            $actual = $actual->siguiente;
        }
    }
}

// Función para calcular la media
function calcularMedia($lista)
{
    if ($lista->obtenerTamano() === 0) {
        return 0;
    }

    $suma = 0;
    $lista->iterar(function ($dato) use (&$suma) {
        $suma += $dato;
    });

    return $suma / $lista->obtenerTamano();
}

// Función para calcular la desviación estándar
function calcularDesviacionEstandar($lista, $media)
{
    if ($lista->obtenerTamano() <= 1) {
        return 0;
    }

    $sumaCuadrados = 0;
    $lista->iterar(function ($dato) use ($media, &$sumaCuadrados) {
        $sumaCuadrados += pow($dato - $media, 2);
    });

    return sqrt($sumaCuadrados / ($lista->obtenerTamano() - 1));
}

// Función para leer números desde un archivo
function leerNumerosDesdeArchivo($nombreArchivo)
{
    $lista = new ListaEnlazada();

    if (!file_exists($nombreArchivo)) {
        throw new Exception("El archivo '$nombreArchivo' no existe.");
    }

    $contenido = file_get_contents($nombreArchivo);
    $lineas = explode("\n", $contenido);

    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if (!empty($linea)) {
            $numeros = preg_split('/\s+/', $linea);
            foreach ($numeros as $numero) {
                if (is_numeric($numero)) {
                    $lista->insertar((float) $numero);
                }
            }
        }
    }

    return $lista;
}

// Función para mostrar resultados en CLI
function mostrarResultados($media, $desviacion, $cantidad)
{
    echo "Resultados del cálculo:\n";
    echo "Cantidad de números: $cantidad\n";
    echo "Media: " . number_format($media, 6) . "\n";
    echo "Desviación estándar: " . number_format($desviacion, 6) . "\n";
    echo "----------------------------------------\n";
}

// Función para verificar si estamos en CLI
function esLineaComandos()
{
    return php_sapi_name() === 'cli';
}

// Función principal para CLI
function mainCLI()
{
    echo "========================================\n";
    echo "CÁLCULO DE MEDIA Y DESVIACIÓN ESTÁNDAR\n";
    echo "========================================\n\n";

    // Prueba con datos de la columna 1
    echo "PRUEBA 1 - Columna 1:\n";
    try {
        $lista1 = leerNumerosDesdeArchivo("columna1.txt");
        $media1 = calcularMedia($lista1);
        $desviacion1 = calcularDesviacionEstandar($lista1, $media1);
        mostrarResultados($media1, $desviacion1, $lista1->obtenerTamano());
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Creando archivo de ejemplo columna1.txt...\n";

        // Crear archivo de ejemplo con datos de la columna 1
        $datosColumna1 = "160 591 114 229 230 270 128 1657 624 1503";
        file_put_contents("columna1.txt", $datosColumna1);
        echo "Archivo columna1.txt creado. Por favor, ejecute el programa nuevamente.\n";
    }

    // Prueba con datos de la columna 2
    echo "PRUEBA 2 - Columna 2:\n";
    try {
        $lista2 = leerNumerosDesdeArchivo("columna2.txt");
        $media2 = calcularMedia($lista2);
        $desviacion2 = calcularDesviacionEstandar($lista2, $media2);
        mostrarResultados($media2, $desviacion2, $lista2->obtenerTamano());
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Creando archivo de ejemplo columna2.txt...\n";

        // Crear archivo de ejemplo con datos de la columna 2
        $datosColumna2 = "15.0 69.9 6.5 22.4 28.4 65.9 19.4 198.7 38.8 138.2";
        file_put_contents("columna2.txt", $datosColumna2);
        echo "Archivo columna2.txt creado. Por favor, ejecute el programa nuevamente.\n";
    }

    // Prueba adicional con entrada manual (solo en CLI)
    echo "PRUEBA 3 - Entrada manual:\n";
    echo "Ingrese números separados por espacios (ej: 1 2 3 4 5): ";
    $entrada = trim(fgets(STDIN));

    if (!empty($entrada)) {
        $lista3 = new ListaEnlazada();
        $numeros = preg_split('/\s+/', $entrada);

        foreach ($numeros as $numero) {
            if (is_numeric($numero)) {
                $lista3->insertar((float) $numero);
            }
        }

        if ($lista3->obtenerTamano() > 0) {
            $media3 = calcularMedia($lista3);
            $desviacion3 = calcularDesviacionEstandar($lista3, $media3);
            mostrarResultados($media3, $desviacion3, $lista3->obtenerTamano());
        } else {
            echo "No se ingresaron números válidos.\n";
        }
    }

    echo "¡Programa terminado!\n";
}

// Función principal para Web
function mainWeb()
{
    $resultados = array();

    // Procesar columna 1
    try {
        $lista1 = leerNumerosDesdeArchivo("columna1.txt");
        $media1 = calcularMedia($lista1);
        $desviacion1 = calcularDesviacionEstandar($lista1, $media1);
        $resultados['columna1'] = [
            'media' => $media1,
            'desviacion' => $desviacion1,
            'cantidad' => $lista1->obtenerTamano(),
            'error' => false
        ];
    } catch (Exception $e) {
        // Crear archivo si no existe
        $datosColumna1 = "160 591 114 229 230 270 128 1657 624 1503";
        file_put_contents("columna1.txt", $datosColumna1);
        $resultados['columna1'] = [
            'error' => true,
            'mensaje' => 'Archivo creado, recargue la página'
        ];
    }

    // Procesar columna 2
    try {
        $lista2 = leerNumerosDesdeArchivo("columna2.txt");
        $media2 = calcularMedia($lista2);
        $desviacion2 = calcularDesviacionEstandar($lista2, $media2);
        $resultados['columna2'] = [
            'media' => $media2,
            'desviacion' => $desviacion2,
            'cantidad' => $lista2->obtenerTamano(),
            'error' => false
        ];
    } catch (Exception $e) {
        // Crear archivo si no existe
        $datosColumna2 = "15.0 69.9 6.5 22.4 28.4 65.9 19.4 198.7 38.8 138.2";
        file_put_contents("columna2.txt", $datosColumna2);
        $resultados['columna2'] = [
            'error' => true,
            'mensaje' => 'Archivo creado, recargue la página'
        ];
    }

    return $resultados;
}

// Determinar cómo ejecutar
if (esLineaComandos()) {
    // Ejecución por línea de comandos
    mainCLI();
} else {
    // Ejecución en navegador web
    $resultados = mainWeb();

    // Mostrar resultados en HTML
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Cálculo Estadístico</title>
        
    </head>
    <body>
        <div class="container">
            <h1>Cálculo de Media y Desviación Estándar</h1>';

    foreach (['columna1', 'columna2'] as $columna) {
        echo '<div class="resultado">
                <h2>' . ucfirst($columna) . '</h2>';

        if ($resultados[$columna]['error']) {
            echo '<div class="error">' . $resultados[$columna]['mensaje'] . '</div>';
        } else {
            echo '<p><strong>Cantidad de números:</strong> ' . $resultados[$columna]['cantidad'] . '</p>
                 <p><strong>Media:</strong> <span class="numero">' . number_format($resultados[$columna]['media'], 6) . '</span></p>
                 <p><strong>Desviación estándar:</strong> <span class="numero">' . number_format($resultados[$columna]['desviacion'], 6) . '</span></p>';
        }

        echo '</div>';
    }

    echo '<div class="resultado">
            <h2>Datos</h2>
            <p>Tablas de Datos</p>
            <ul>
                <li></strong> 160 591 114 229 230 270 128 1657 624 1503</li>
                <li></strong> 15.0 69.9 6.5 22.4 28.4 65.9 19.4 198.7 38.8 138.2</li>
            </ul>
          </div>
        </div>
    </body>
    </html>';
}
?>