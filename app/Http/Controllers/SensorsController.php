<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Token;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;




class SensorsController extends Controller
{
   
    // Consult all sensors
    public function index(){
        return Sensor::all();
    }

    // Consult a specific sensor
    public function show($id){
        return Sensor::find($id);
    }

    // Create a new sensor
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:sensors',
            'value' => 'required',
        ]);
        
        $sensor = new Sensor;
        $sensor->fill($request->all());
        $sensor->save();

        return $sensor;
    }

    //actualizar un sensor
    public function update(Request $request, $id){
     

        $this->validate($request, [
            'name' => 'filled|unique:sensors',
        ]);
        $sensor = Sensor::find($id);
        if(!$sensor) return response('', 404);
        $an = $sensor->value;
        $t2 = $sensor->updated_at;
        $sensor->update($request->all());
        $sensor->save();
        
   
  

        function obtenerHoraUltimaNotificacionDesdeArchivo9() {
            $archivo = 'fuga.txt';
            if (file_exists($archivo)) {
                $contenido = file_get_contents($archivo);
                $hora = intval($contenido);
                return $hora;
            } else {
                return false;
            }
        }
        
        function guardarHoraUltimaNotificacion9($hora) {
            $archivo = 'fuga.txt';
            $manejador = fopen($archivo, 'w');
            if ($manejador) {
                fwrite($manejador, $hora);
                fclose($manejador);
                return true;
            } else {
                return false;
            }
        }
        
        function saveValueToFile($filename, $value) {
            $data = [
                'timestamp' => Carbon::now()->toDateTimeString(),
                'value' => $value
            ];
            file_put_contents($filename, json_encode($data));
        }
        
        function loadValueFromFile($filename) {
            if (file_exists($filename)) {
                $data = json_decode(file_get_contents($filename), true);
                return [
                    'timestamp' => Carbon::parse($data['timestamp']),
                    'value' => $data['value']
                ];
            }
            return null;
        }
        
        $currentDateTime9 = Carbon::now();
        $filename = 'value.txt';
        $previousData = loadValueFromFile($filename);
        
        // Replace with your actual sensor value
        $valor = $sensor->value;
        require 'config.php';
        if ($id == 1) {
            if ($previousData) {
                $t1 = $previousData['timestamp'];
                $t3 = $t1->diffInSeconds($currentDateTime9);
                
                if ($t3 >= 600) { // If 10 minutes or more have passed since the last save
                    $previousValue = $previousData['value'];
                    $dif =  $previousValue- abs($valor) ;
        
                    echo "La diferencia en minutos de $t1 y $currentDateTime9 es " . ($t3 / 60) . " minutos\n";
                    echo "Anterior: $previousValue\n";
                    echo "El valor es: $valor\n";
                    echo "Diferencia: $dif\n";
        
                    $lastNotificationTime9 = obtenerHoraUltimaNotificacionDesdeArchivo9();
                    $currentHour9 = time();
        
                    if ($dif > 20 && $dif<100) { // If difference > 20
                        echo "Fuga de agua\n";
                        // Assume $this->sendNotification is a method of the class containing this code
                        $result = $this->sendNotification("Tu contenedor perdio mucha agua", "Fuga de agua");
                        if ($result) {
                            echo "<script>console.log('done');</script>";
                        } else {
                            echo "<script>console.log('fail2');</script>";
                        }

   // Código para insertar la notificación en la base de datos
   $title = "Fuga de agua";
   $subtitle = "Tu contenedor perdio mucha agua";
   $date = date("Y-m-d");

$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual

   // Usar $pdo para insertar la notificación en la base de datos
   $query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date,updated_at) VALUES (:title, :subtitle, :date,:updated)");
   $query->bindParam(':title', $title);
   $query->bindParam(':subtitle', $subtitle);
   $query->bindParam(':date', $date);
   $query->bindParam(':updated', $updated);
   $queryResult = $query->execute();



                        guardarHoraUltimaNotificacion9($currentHour9);
                    }
                    
                    // Save the new value and timestamp after the check
                    saveValueToFile($filename, $valor);
                    echo "Datos guardados: Valor actual y timestamp.\n";
                } else {
                    echo "Aún no han pasado 10 minutos desde el último guardado.\n";
                }
            } else {
                saveValueToFile($filename, $valor);
                echo "No previous data, saved the current value.\n";
            }
        }
        
        echo "Ahora: " . $currentDateTime9->toDateTimeString() . "\n";
       
        
// Incluye el archivo de configuración de la base de datos
require 'config.php';


function obtenerHoraUltimaNotificacionDesdeArchivo() {
    $archivo = 'low_water.txt';
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Lee el contenido del archivo
        $contenido = file_get_contents($archivo);
        // Convierte el contenido en un número entero
        $hora = intval($contenido);
        return $hora; // Devuelve la hora como un entero
    } else {
        // Si el archivo no existe, devuelve false para indicar un error
        return false;
    }
}

function guardarHoraUltimaNotificacion($hora) {
    $archivo = 'low_water.txt';
    // Abre el archivo en modo escritura, creándolo si no existe
    $manejador = fopen($archivo, 'w');
    if ($manejador) {
        // Escribe la hora en el archivo
        fwrite($manejador, $hora);
        // Cierra el archivo
        fclose($manejador);
        return true; // Indica que la operación fue exitosa
    } else {
        return false; // Indica que hubo un error al abrir el archivo
    }
}


// Bloque de código donde se ejecuta la lógica
// Obtener la hora de la última notificación enviada
$lastNotificationTime = obtenerHoraUltimaNotificacionDesdeArchivo() ;

// Obtener la hora actual
$currentHour = time();

// Calcular la diferencia en horas desde la última notificación
$hoursDifference = ($currentHour - $lastNotificationTime) / (60 * 60);

// Si ha pasado al menos una hora desde la última notificación
if($id==1){
if ($hoursDifference >= 1) {
 
    // Envía la notificación si se cumplen las condiciones
    if ($valor < 10 && $valor != 0 && $valor>0) {
        $result = $this->sendNotification("Tu agua está a punto de acabarse", "Agua agotada $valor%");

        // Código para insertar la notificación en la base de datos
        $title = "Agua agotada $valor%";
        $subtitle = "Tu agua se acabará";
        $date = date("Y-m-d");
  
$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual

        // Usar $pdo para insertar la notificación en la base de datos
        $query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date,updated_at) VALUES (:title, :subtitle, :date,:updated)");
        $query->bindParam(':title', $title);
        $query->bindParam(':subtitle', $subtitle);
        $query->bindParam(':date', $date);
        $query->bindParam(':updated', $updated);
        $queryResult = $query->execute();

        if ($result) {
            echo "<script>console.log('done');</script>";
        } else {
            echo "<script>console.log('fail2');</script>";
        }

        // Guardar la hora actual como la hora de la última notificación enviada
        guardarHoraUltimaNotificacion($currentHour);
    }
} else {
    echo "<script>console.log('No ha pasado suficiente tiempo desde la última notificación.  low water');</script>";
}}






require 'config.php';


function obtenerHoraUltimaNotificacionDesdeArchivo6() {
    $archivo = 'full_water.txt';
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Lee el contenido del archivo
        $contenido = file_get_contents($archivo);
        // Convierte el contenido en un número entero
        $hora = intval($contenido);
        return $hora; // Devuelve la hora como un entero
    } else {
        // Si el archivo no existe, devuelve false para indicar un error
        return false;
    }
}

function guardarHoraUltimaNotificacion6($hora) {
    $archivo = 'full_water.txt';
    // Abre el archivo en modo escritura, creándolo si no existe
    $manejador = fopen($archivo, 'w');
    if ($manejador) {
        // Escribe la hora en el archivo
        fwrite($manejador, $hora);
        // Cierra el archivo
        fclose($manejador);
        return true; // Indica que la operación fue exitosa
    } else {
        return false; // Indica que hubo un error al abrir el archivo
    }
}


// Bloque de código donde se ejecuta la lógica
// Obtener la hora de la última notificación enviada
$lastNotificationTime6 = obtenerHoraUltimaNotificacionDesdeArchivo6() ;

// Obtener la hora actual
$currentHour6 = time();

// Calcular la diferencia en horas desde la última notificación
$hoursDifference6 = ($currentHour6 - $lastNotificationTime6) / (60 * 60);

// Si ha pasado al menos una hora desde la última notificación
if($id==1){
if ($hoursDifference6 >= 1) {
 
    // Envía la notificación si se cumplen las condiciones
    if ($valor >= 98 && $valor != 0) {
       // Enviar notificación
$result = $this->sendNotification("Tu Tinaco esta lleno", "Tinaco lleno");

// Definir los valores para la inserción en la base de datos
$title = "Tinaco lleno";
$subtitle = "Tu Tinaco esta lleno";
$date = date("Y-m-d");
$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual

// Preparar la consulta para insertar la notificación en la base de datos
$query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date, updated_at) VALUES (:title, :subtitle, :date, :updated)");
$query->bindParam(':title', $title);
$query->bindParam(':subtitle', $subtitle);
$query->bindParam(':date', $date);
$query->bindParam(':updated', $updated);

// Ejecutar la consulta y verificar el resultado
$queryResult = $query->execute();

if ($queryResult) {
    echo "<script>console.log('Notificación insertada correctamente');</script>";
} else {
    echo "<script>console.log('Error al insertar la notificación');</script>";
}


guardarHoraUltimaNotificacion6($currentHour6);
    }
} else {
    echo "<script>console.log('No ha pasado suficiente tiempo desde la última notificación.  full water');</script>";
}}











// Incluye el archivo de configuración de la base de datos
require 'config.php';



function obtenerHoraUltimaNotificacionDesdeArchivo5() {
    $archivo = 'no_water.txt';
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Lee el contenido del archivo
        $contenido = file_get_contents($archivo);
        // Convierte el contenido en un número entero
        $hora = intval($contenido);
        return $hora; // Devuelve la hora como un entero
    } else {
        // Si el archivo no existe, devuelve false para indicar un error
        return false;
    }
}

function guardarHoraUltimaNotificacion5($hora) {
    $archivo = 'no_water.txt';
    // Abre el archivo en modo escritura, creándolo si no existe
    $manejador = fopen($archivo, 'w');
    if ($manejador) {
        // Escribe la hora en el archivo
        fwrite($manejador, $hora);
        // Cierra el archivo
        fclose($manejador);
        return true; // Indica que la operación fue exitosa
    } else {
        return false; // Indica que hubo un error al abrir el archivo
    }
}


// Bloque de código donde se ejecuta la lógica
// Obtener la hora de la última notificación enviada
$lastNotificationTime5 = obtenerHoraUltimaNotificacionDesdeArchivo5() ;

// Obtener la hora actual
$currentHour5 = time();

// Calcular la diferencia en horas desde la última notificación
$hoursDifference5 = ($currentHour5 - $lastNotificationTime5) / (60 * 60);




  if($id==1){
    if ($hoursDifference5 >= 3) {
    if($valor<=2 && $valor>=0){
        $result = $this->sendNotification("Tu agua se acabo ","Sin agua ");

   
// Código para insertar la notificación en la base de datos
$title = "Sin agua";
$subtitle = "Tu agua se acabo";
$date = date("Y-m-d");

$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual
// Usar $pdo para insertar la notificación en la base de datos
$query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date,updated_at) VALUES (:title, :subtitle, :date,:updated)");
$query->bindParam(':title', $title);
$query->bindParam(':subtitle', $subtitle);
$query->bindParam(':date', $date);
$query->bindParam(':updated', $updated);
$queryResult = $query->execute();

if ($result) {
echo "<script>console.log('done');</script>";
} else {
echo "<script>console.log('fail2');</script>";
}


       // Guardar la hora actual como la hora de la última notificación enviada
       guardarHoraUltimaNotificacion5($currentHour5); 
       
        
}

  }else{
    echo "<script>console.log('No ha pasado suficiente tiempo desde la última notificación.  no water');</script>";
  }

}

           
            
         
        // Incluye el archivo de configuración de la base de datos
require 'config.php';


function obtenerHoraUltimaNotificacionDesdeArchivo2() {
    $archivo = 'tds.txt';
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Lee el contenido del archivo
        $contenido = file_get_contents($archivo);
        // Convierte el contenido en un número entero
        $hora = intval($contenido);
        return $hora; // Devuelve la hora como un entero
    } else {
        // Si el archivo no existe, devuelve false para indicar un error
        return false;
    }
}

function guardarHoraUltimaNotificacion2($hora) {
    $archivo = 'tds.txt';
    // Abre el archivo en modo escritura, creándolo si no existe
    $manejador = fopen($archivo, 'w');
    if ($manejador) {
        // Escribe la hora en el archivo
        fwrite($manejador, $hora);
        // Cierra el archivo
        fclose($manejador);
        return true; // Indica que la operación fue exitosa
    } else {
        return false; // Indica que hubo un error al abrir el archivo
    }
}


// Bloque de código donde se ejecuta la lógica
// Obtener la hora de la última notificación enviada
$lastNotificationTime2 = obtenerHoraUltimaNotificacionDesdeArchivo2() ;

// Obtener la hora actual
$currentHour2 = time();

// Calcular la diferencia en horas desde la última notificación
$hoursDifference2 = ($currentHour2 - $lastNotificationTime2) / (60 * 60);
if ($id==2){
if ($hoursDifference2 >= 3) {

    
        $valor = $sensor->value; 
        if($valor<900   ){ 
           echo "<script>console.log('tds bueno');</script>";
       }else{
        echo "<script>console.log('necesitas cambio de filtro');</script>";
    $result = $this->sendNotification("Verifica el filtro", "tds malo $valor");

    // Código para insertar la notificación en la base de datos
    $title = "Tds malo $valor";
    $subtitle = "Verifica el filtro";
    $date = date("Y-m-d");
    
$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual

    // Usar $pdo para insertar la notificación en la base de datos
    $query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date,updated_at) VALUES (:title, :subtitle, :date,:updated)");
    $query->bindParam(':title', $title);
    $query->bindParam(':subtitle', $subtitle);
    $query->bindParam(':date', $date);
    $query->bindParam(':updated', $updated);
    $queryResult = $query->execute();

    if ($result) {
        echo "<script>console.log('done');</script>";
    } else {
        echo "<script>console.log('fail2');</script>";
    }

    if ($queryResult) {
        echo "<script>console.log('Notification logged');</script>";
    } else {
        echo "<script>console.log('Failed to log notification');</script>";
    }
       }
      
   // Guardar la hora actual como la hora de la última notificación enviada
   guardarHoraUltimaNotificacion2($currentHour2);
        
    
}else{
    echo "<script>console.log('No ha pasado suficiente tiempo desde la última notificación. tds');</script>";
}}

       
// Incluye el archivo de configuración de la base de datos
require 'config.php';



function obtenerHoraUltimaNotificacionDesdeArchivo3() {
    $archivo = 'ph.txt';
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Lee el contenido del archivo
        $contenido = file_get_contents($archivo);
        // Convierte el contenido en un número entero
        $hora = intval($contenido);
        return $hora; // Devuelve la hora como un entero
    } else {
        // Si el archivo no existe, devuelve false para indicar un error
        return false;
    }
}

function guardarHoraUltimaNotificacion3($hora) {
    $archivo = 'ph.txt';
    // Abre el archivo en modo escritura, creándolo si no existe
    $manejador = fopen($archivo, 'w');
    if ($manejador) {
        // Escribe la hora en el archivo
        fwrite($manejador, $hora);
        // Cierra el archivo
        fclose($manejador);
        return true; // Indica que la operación fue exitosa
    } else {
        return false; // Indica que hubo un error al abrir el archivo
    }
}


// Bloque de código donde se ejecuta la lógica
// Obtener la hora de la última notificación enviada
$lastNotificationTime3 = obtenerHoraUltimaNotificacionDesdeArchivo3() ;

// Obtener la hora actual
$currentHour3 = time();

// Calcular la diferencia en horas desde la última notificación
$hoursDifference3 = ($currentHour3 - $lastNotificationTime3) / (60 * 60);
if ($id == 3) {
if ($hoursDifference3 >= 3) {
    
        $valor = $sensor->value; 
        if ($valor >= 6.5 && $valor <= 7) { 
            echo "<script>console.log('ph bueno');</script>";
        } else {
            echo "<script>console.log('necesitas cambio de filtro');</script>";
            $result = $this->sendNotification("Verifica el filtro", "Ph malo $valor");
    
            // Código para insertar la notificación en la base de datos
            $title = "Ph malo $valor";
            $subtitle = "Verifica el filtro";
            $date = date("Y-m-d");
    
$updated = date("Y-m-d H:i:s"); // Definir la fecha y hora de actualización actual

            // Usar $pdo para insertar la notificación en la base de datos
            $query = $pdo->prepare("INSERT INTO notifications (title, subtitle, date,updated_at) VALUES (:title, :subtitle, :date,:updated)");
            $query->bindParam(':title', $title);
            $query->bindParam(':subtitle', $subtitle);
            $query->bindParam(':date', $date);
            $query->bindParam(':updated', $updated);
            $queryResult = $query->execute();
    
            if ($result) {
                echo "<script>console.log('done');</script>";
            } else {
                echo "<script>console.log('fail2');</script>";
            }
    
            if ($queryResult) {
                echo "<script>console.log('Notification logged');</script>";
            } else {
                echo "<script>console.log('Failed to log notification');</script>";
            }

               // Guardar la hora actual como la hora de la última notificación enviada
   guardarHoraUltimaNotificacion3($currentHour3);
        
    }    
}else{
    echo "<script>console.log('No ha pasado suficiente tiempo desde la última notificación. ph');</script>";

} }



        
       
        
        return $sensor;

    } 

    // Delete a sensor
    public function destroy($id){
        $sensor = Sensor::find($id);
        if (!$sensor) return response('', 404);
        $sensor->delete();
        return $sensor;
    }

 

    // Send notification to all tokens
    public function sendNotification($body, $title) {
        $tokens = Token::all()->pluck('token')->toArray();
        foreach ($tokens as $token) {
            $fcmNotification = [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ]
            ];
            if (!$this->firebaseNotification($fcmNotification)) {
                Log::error("Failed to send notification to token: {$token}");
                return false;
            }
        }
        return true;
    }

    // Send notification to Firebase
    public function firebaseNotification($fcmNotification) {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $apiKey = 'AAAAMAtM1Eg:APA91bEcfzHlgx9jRrY3Ldgl_6yEHhTp90AQrTXkNUZTAzRBjvN52jeyZLwzucV6amc1_TpIsJh03Akbid0qv6NsR3Z1GeTUODd5thPSqTtpD142zy_esVo6Ui-Nleti8tfhhxa0-NBq'; // Replace with your actual Firebase API key

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($fcmUrl, $fcmNotification);

            if ($response->successful()) {
                Log::info("Notification sent successfully: " . $response->body());
                return true;
            } else {
                Log::error("Failed to send notification: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception caught: " . $e->getMessage());
            return false;
        }
    }

 



}





