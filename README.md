# Foris - Fase 2: Angel Maturana

# Introducción
Proyecto para Foris desarrollado en PHP con framework Laravel con el fin de evaluar la fase 2 de la postulación laboral realizada por Angel Maturana Troncoso. 

El proyecto implica el desafío de ingresar estudiantes desde consola al igual que su horario de ingreso y salida de clases, todo esto con el fin de poder calcular la cantidad de minutos y días que dicho estudiante se encontró en el establecimiento. El resultado debe ser mostrado en el formato indicado en el descriptivo del proyecto y ordenado por los minutos totales del estudiante desde el mayor al menor.

## Comenzando!

A continuación se presentarán los pasos a realizar para poder instalar, ejecutar y realizar testing del proyecto.

### Requerimientos

Necesitas tener instalado lo siguiente:

    PHP >= 7.1.3
    PHP Extensions: mbstring, json, xml, OpenSSL
 
 ### Instalación
 Dentro de la carpeta del proyecto ejecutar lo siguiente:

    composer install

Con esto se instalarán las dependencias requeridas para ejecutar correctamente el proyecto.

### Ejecución
Para ejecutar el proyecto tenemos tres comandos claves los cuales son:

foris:Student : El cual se encarga de almacenar los estudiantes ingresados.

    php artisan foris:Student <Student Name>

foris:Presence: Almacena la asistencia o presencia del estudiante en el establecimiento.

    php artisan foris:Presence <Student Name> <day of week> <Date> <Initial Hour> <Final Hour> <Class Room Number> 

foris:run: Ejecuta en base a un archivo alojado en "storage/app/" todos los comandos dentro de él.

    php artisan foris:run <File Name>

Finalmente debemos ejecutar el siguiente comando para ejecutar el proyecto indicado por Foris:

    php artisan foris:run students_data.txt

Como respuesta se mostrará la cantidad de horas en el establecimiento de educación de cada estudiante indicado en el archivo mencionado (archivo almacenado en storage/app/students_data.txt).

## Resolución del problema
Durante el análisis de la problemática el primer problema que tuve fue "Como almaceno la información del estudiante de forma temporal" ya que solo me interesaba mantenerlo durante la ejecución del comando. Es por ello que pensé en dos posibles escenarios: 

 - Uno era crear una base de datos no relacional que almacenara de forma temporal esta información. 
 - Y otra que era utilizar el componente de Cache integrado directamente en Laravel. 

Se optó por esta segunda opción, el caché, ya que se simplificaba muchísimo la ejecución del proyecto (no requiere instalar servicios externos) y además cumple con la función especifica que requiero, almacenar información de manera temporal.

La segunda problemática que tuve fue la forma en la que almacenaría al estudiante junto con su información, aquí opté por crear un objeto llamado "Student" ubicado en la carpeta "/app" el cual tendría la siguiente estructura:

Student Class:

	private $name; //String Para almacenar el nombre del estudiante.
	private $presence; //Array Para almacenar la presencia o asistencia.
	
	public getters();
	public setters();
	public minutesOfPresence(); //Para obtener los minutos totales que ha estudiando.
	public daysOfPresence(); //Para obtener los días totales que ha estudiado.

Con esta estructura ya almacenaría el estudiante, su nombre y toda la información referente a su asistencia dentro de él mismo.

Posterior a esto procedí a crear los archivos de comando para ejecutarlos desde el Artisan de Laravel. Se requerirían 3 comandos:

 - **app/Console/Commands/StudentCommand:** Para agregar los estudiantes al caché.
 - **app/Console/Commands/PresenceCommand:** Para almacenar la asistencia o presencia del estudiante
 - **app/Console/Commands/RunForisCommand:**  Ejecuta el archivo con los comandos necesarios para llevar a cabo el proyecto indicado por Foris.                                                    

En **StudentCommand** se desarrolló todo el código de ejecución que netamente realiza la creación del estudiante como un objeto y luego lo almacena en el caché del proyecto.

En **PresenceCommand** se desarrolló el código para almacenar la asistencia del estudiante siempre y cuando este haya sido agregado anteriormente al sistema.

En **RunForisCommand** se desarrolló el código para leer el archivo de texto con los comandos y luego ir ejecutándolos uno a uno utilizando los dos comandos anteriormente mencionados.

Finalmente solo quedaba mostrar el mensaje de salida el cual debe ser con el siguiente formato: 
```
Student_1: <number> minutes in <number> days
Student_2: <number> minutes in <number> days
Student_3: <number> minutes in <number> days
```
Todo ordenado por la cantidad de minutos desde el mayor al menor.

Ya obteniendo este resultado el proyecto se encontraría finalizado.

## Testing
Para realizar los test correspondientes a la aplicación se hizo uso de "phpunit" en el cual se indicó los 3 comandos a ejecutar y su respuesta esperada.

En el caso del comando "Student" se creó el archivo "/tests/Feature/StudentTest.php" el cual ejecuta el comando:

    php artisan foris:Student Testing
Donde "Testing" es el estudiante en cuestión para luego esperar recibir como respuesta "The student Testing was added successfully." Si todo funciona correctamente el test de este comando habrá sido ejecutado exitosamente.

Luego para el test del comando "Presence" ubicado en "/tests/Feature/PresenceTest.php" se ejecutan los comandos:

    php artisan foris:Student Testing
   
   y

    php artisan foris::Presence Testing 1 2020-02-10 09:02 10:17 R100

Para agregar el estudiante "Testing" y su respectiva asistencia. Si todo funciona correctamente se obtiene como respuesta "The student presence was added successfully.".

Finalmente se ejecuta el comando que busca el archivo de comandos y los ejecuta uno a uno. Este test se llama "RunForisTest" y está ubicado en "/tests/Feature/RunForisTest.php". Esto ejecuta el siguiente comando:

    php artisan foris:run students_data_testing.txt
El cual utiliza un archivo especial para este test. Si todo es correcto el test esperará "Marco: 142 minutes in 2 days." como respuesta.

Para realizar la ejecución global de estos tres test solo basta ejecutar el siguiente comando dentro del proyecto:

    php vendor/phpunit/phpunit/phpunit

Se indicará el tiempo, la memoria utilizada y si el resultado fue exitoso o erróneo.

## Correcciones personales proyecto
Si bien el proyecto en sí implicaba una serie de indicaciones ya previamente señaladas me encontré con una posible corrección a lo solicitado y esto era básicamente la siguiente pregunta:

>  Se me solicita dentro de los requerimientos obtener la cantidad de días que el estudiante estuvo en el establecimiento pero... ¿Cómo sé que el número, en caso de repetirse, no hace referencia al mismo día? Ejemplo: Marco asiste el día 1 de 10:00 a 12:00 y luego hay otro comando que indica también el día 1 pero de 13:00 a 15:00, cómo sé yo que esto es un día distinto de la semana o nos encontramos en el mismo día.

Para solucionar esto se agregó un nuevo parámetro al comando "Presence" denominado "date" el cual indica la fecha en el cual se está marcando la asistencia, de tal manera que, si el registro del estudiante se encuentra el mismo día (comparado a partir de la fecha) no se considera como un día más sino que se suman sus minutos correspondientes pero la cantidad de días no se aumenta.

## Conclusión
El proyecto implicó para mi buscar una solución en base a los conocimientos técnicos que he manejado durante estos años, se indicó que el lenguaje a utilizar debía ser el que más me acomodara y por ende opté por Laravel (mi segunda opción era nodejs) ya que llevo bastante tiempo trabajando con dicho framework. En resumidas cuentas llegar a la solución más optima desde un punto de vista personal fue lo más desafiante ya que existen mil maneras de realizar esto y rebuscar lo mejor y más rápido en cuanto a desarrollo y efectividad fue lo que más me agradó.

## Componentes y tecnologías utilizadas

 - Lenguaje: PHP
 - Framework: [Laravel v6.2](https://laravel.com/docs/6.x)
 - Componentes de Laravel utilizados:
	 - [Commands with Artisan](https://laravel.com/docs/6.x/artisan)
	 - [Arr (Helpers)](https://laravel.com/docs/6.x/helpers#arrays) 
	 - [Cache](https://laravel.com/docs/6.x/cache)
