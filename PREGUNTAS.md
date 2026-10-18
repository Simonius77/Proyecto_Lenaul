# Preguntas para entender Daw2mvc

Guía de estudio orientada a alumnos. El objetivo es comprender **el 100 % del proyecto actual** y pensar **ampliaciones futuras** sin memorizar código a ciegas.

**Cómo usarla**

1. Intenta responder mirando el código (no hace falta internet).
2. Prueba en el navegador las URLs y “qué pasaría si…”.
3. Las preguntas marcadas con ★ son especialmente recomendables en clase.
4. Las de la sección *Ampliaciones* se pueden usar como parte del projecto.

---

## A. Visión general y estructura del proyecto

1. ★ ¿Qué es el patrón MVC y qué carpeta del proyecto corresponde a cada letra (Model, View, Controller)?
2. ¿Para qué sirve `index.php` en este proyecto? ¿Es una vista, un modelo o algo distinto?
3. ¿Qué papel tienen `config/parameters.php` y `config/dataBase.php`? ¿Pertenecen al MVC “puro” o son infraestructura?
4. ¿Por qué hay un fichero `sql/init.sql` si el PHP ya “habla” con la base de datos?
5. ★ Dibuja (en papel o mentalmente) el árbol de carpetas y explica en una frase cada una: `controllers/`, `models/`, `views/`, `config/`, `sql/`.
6. ¿Qué diferencia hay entre una **entidad** (`Camiseta`, `Producto`) y un **DAO** (`CamisetaDAO`)?
7. ¿Por qué `Producto` es `abstract` y `Camiseta` no lo es?
8. ¿Qué significan las constantes `TYPE_CAMISETA` y `TYPE_PANTALONES` si de momento solo usamos camisetas?

---

## B. Flujo de la aplicación (petición → respuesta)

9. ★ Explica paso a paso qué ocurre cuando abres `http://localhost:8000/` (sin parámetros).
10. ★ Explica paso a paso qué ocurre con `?controller=producto&action=index`.
11. ¿Qué ocurre si entras a `?controller=producto` **sin** `action`? ¿Quién decide la action por defecto?
12. ¿Dónde se define `default_controller` y `default_action`? ¿Qué pasa si las cambias?
13. ★ Sigue el flujo completo de **crear un producto**: desde el enlace “Crear producto” hasta que vuelve a aparecer en el listado. Nombra ficheros y métodos.
14. Sigue el flujo de **ver un producto** (`show`) y el de **eliminar** (`destroy`). ¿En qué se parecen y en qué se diferencian?
15. ¿Qué hace `header('Location:...')` seguido de `exit`? ¿Qué pasaría si olvidas el `exit`?
16. ¿Por qué `store` y `destroy` **no** incluyen una vista (`main.php`) y en cambio hacen redirect?
17. ★ ¿Qué es el patrón Post-Redirect-Get (PRG) y dónde lo ves aplicado en este código?
18. Si dos alumnos abren la misma URL a la vez, ¿comparten variables PHP `$productos`? ¿Por qué?

---

## C. Front controller (`index.php`)

19. ★ ¿Qué es un *front controller* y por qué casi todo pasa por `index.php`?
20. ¿Cómo se construye el nombre de la clase a partir de `$_GET['controller']`? Ejemplo con `producto`.
21. ¿Cómo se decide qué fichero incluir en `controllers/`?
22. ¿Qué hace `showError()`? ¿Por qué usa `http_response_code` y después `include` de `main.php`?
23. ★ ¿Qué diferencia hay entre un error 404 y un error 500 en este `index.php`? Pon un ejemplo de cada uno.
24. ¿Qué hace `class_exists` y por qué se comprueba **después** de hacer `include_once` del fichero?
25. ¿Qué hace `is_callable([$controller, $action])`? ¿Por qué no basta con `method_exists`?
26. ★ ¿Qué pasaría si alguien pide `?controller=producto&action=__construct`? ¿Está permitido? ¿Por qué?
27. ¿Qué pasaría si el fichero `productoController.php` existiera pero la clase se llamara `ProductoCtrl`?
28. ¿Qué riesgo hay si no validas el nombre del `controller` antes de usarlo en una ruta de fichero? (piensa en `../`)
29. ¿Por qué `include_once` y no `include` o `require`? ¿Cuándo preferirías `require_once`?

---

## D. Controlador (`productoController`)

30. ★ ¿Qué responsabilidades tiene el controlador? ¿Qué **no** debería hacer (SQL directo, HTML largo…)?
31. Explica qué hace cada action: `index`, `create`, `store`, `show`, `destroy`.
32. ¿Por qué `create` solo muestra el formulario y `store` es quien guarda?
33. ★ ¿Para qué sirve `TALLAS_VALIDAS`? ¿Qué pasa si envías una talla `XYZ` por POST?
34. ¿Por qué se usa `trim` en el nombre y `(float)` en el precio?
35. ¿Qué comprueba `ctype_digit` en `show` y `destroy`? ¿Qué pasaría con `?id=3abc` o `?id=-1`?
36. ★ En `show`, si el `id` no existe en la BD, ¿qué hace el código? ¿Te parece buena UX? ¿Cómo lo mejorarías sin complicar mucho?
37. ¿Por qué `store` lee de `$_POST` y `destroy`/`show` de `$_GET`?
38. ★ ¿Qué problema de seguridad/usabilidad tiene eliminar con un enlace GET (`action=destroy`)? ¿Cómo lo harías con un formulario POST?
39. Si mañana añades `edit` y `update`, ¿qué haría cada uno y qué vistas necesitarías?

---

## E. Modelo, herencia y propiedades

40. ★ ¿Por qué los nombres de las propiedades (`nombre`, `talla`, `precio`, `id`) deben coincidir con las columnas de la tabla?
41. ¿Qué es un getter y un setter? ¿Por qué no se accede a `$producto->nombre` desde la vista?
42. ★ Explica el constructor de `Producto`: ¿por qué los parámetros por defecto son `null` y solo asigna si no son `null`?
43. ★ ¿Qué pasaría si el constructor hiciera siempre `$this->nombre = $nombre` con `$nombre = ''` por defecto, y luego usas `fetch_object("Camiseta")`?
44. ¿Qué hace `parent::__construct(...)` en `Camiseta`?
45. ¿`Camiseta` añade atributos propios ahora mismo? ¿Qué atributo nuevo pondrías (ej. `manga_corta`) y qué habría que tocar (clase, SQL, vistas, DAO)?
46. ¿Para qué sirve `setId` si el `id` lo genera la base de datos con `AUTO_INCREMENT`?

---

## F. DAO, mysqli y `fetch_object`

47. ★ ¿Qué significa DAO (Data Access Object)? ¿Qué ganas separándolo del controlador?
48. ¿Por qué los métodos del DAO son `static`? ¿Se podría hacer sin `static`? Ventajas/inconvenientes.
49. Explica línea a línea `getAll()`: `connect` → `prepare` → `execute` → `get_result` → bucle → `close`.
50. ★ ¿Qué hace exactamente `$result->fetch_object("Camiseta")`?
51. ★ ¿En qué orden ocurre: rellenar propiedades desde la fila SQL y llamar al constructor? ¿Por qué importa?
52. ¿Qué diferencia hay entre `fetch_object("Camiseta")` y `fetch_assoc()`? ¿Cuándo usarías cada uno?
53. En `getById` y `destroy`, ¿para qué sirve `bind_param("i", $id)`? ¿Qué evita respecto a concatenar el id en el SQL?
54. En `store`, ¿qué significan los tipos `"ssd"` de `bind_param`?
55. ★ ¿Por qué en `store` se copian `$nombre`, `$talla`, `$precio` a variables locales antes de `bind_param`?
56. ¿Qué devolvería `getById(99999)` si no hay fila? ¿Cómo lo representa el código (`?: null`)?
57. ¿Qué pasaría si en el SQL la columna se llamara `name` pero la propiedad de la clase fuera `$nombre`?
58. ¿Por qué se cierra `$stmt` y `$con` al final de cada método? ¿Qué pasaría a largo plazo si no los cierras?
59. ★ Compara responsabilidad: ¿quién conoce SQL, quién conoce HTML, quién conoce reglas de validación de talla?

---

## G. Vistas y layout (header / footer)

60. ★ ¿Qué es `views/main.php` y qué hace `<?php include_once $view; ?>`?
61. ¿Quién define la variable `$view` antes de incluir `main.php`?
62. ★ ¿Cómo pondrías un **header** y un **footer** comunes en toda la web sin repetir HTML en cada vista? Describe al menos dos formas (ej. bloques en `main.php`, o `header.php`/`footer.php`).
63. Si el menú de navegación debe salir en listado, create y show, ¿dónde lo colocarías y por qué?
64. ¿Qué ventaja tiene que `listado.php`, `create.php` y `show.php` **no** traigan `<html>` completo?
65. ★ ¿Qué hace `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` en las vistas? ¿Qué riesgo evitas?
66. ¿Qué mostraría el listado si `$productos` es un array vacío? ¿Dónde está ese mensaje?
67. ¿Cómo cambiarías el título de la pestaña (`<title>`) según la página (Listado / Crear / Detalle) sin complicar demasiado?
68. Si mueves el CSS de `main.php` a `public/css/estilo.css`, ¿qué enlaces habría que añadir y dónde?

---

## H. Base de datos y configuración

69. ¿Qué crea `sql/init.sql`? ¿Por qué usa `DROP TABLE IF EXISTS`? ¿Es peligroso en producción?
70. ¿Qué tipo de dato es `talla` en SQL (`ENUM`) y qué implicación tiene al insertar valores?
71. ★ Si cambias la contraseña de MySQL, ¿qué fichero tocas? ¿Deberías subir esa contraseña a Git? ¿Por qué?
72. ¿Para qué sirve `set_charset("utf8mb4")`?
73. Si la base se llama distinto a `daw2mvc`, ¿qué hay que cambiar?
74. ¿Qué diferencia hay entre conectar a `localhost` y a `127.0.0.1`? (nivel introductorio)

---

## I. “¿Qué pasaría si…?” (depuración y comprensión)

75. ★ ¿Qué pasaría si renombras la clase a `ProductoController` (P mayúscula) pero la URL sigue siendo `controller=producto`?
76. ¿Qué pasaría si en `listado.php` escribes `$producto->nombre` en lugar de `getNombre()`?
77. ★ ¿Qué pasaría si borras `include_once 'views/main.php'` en `index()` y solo dejas el path en `$view`?
78. ¿Qué pasaría si en el formulario de create el `action` del form apunta a `action=index` en vez de `store`?
79. ¿Qué pasaría si `DataBase::connect()` falla? ¿Dónde se ve el error?
80. ★ ¿Qué pasaría si alguien entra a `?controller=producto&action=store` con GET (sin formulario)?
81. ¿Qué pasaría si dos productos se llaman igual? ¿Lo impide el código actual?
82. ¿Qué pasaría si el precio llega como `"12,50"` (coma) en vez de `"12.50"`?
83. ★ Abre `?controller=noexiste`. ¿Qué ves? ¿Qué código HTTP debería devolver?
84. Abre `?controller=producto&action=foo`. ¿Qué ocurre y por qué?

---

## J. MVC en la práctica (diseño)

85. ★ Si metes un `echo "<h1>Hola</h1>"` dentro de `CamisetaDAO`, ¿rompiendo qué principio del MVC estás?
86. Si metes la consulta SQL dentro de `productoController::index`, ¿qué problema tendrás cuando haya otro controlador que también liste camisetas?
87. ★ ¿La validación de tallas debería vivir en el controlador, en el modelo o en ambos? Razona una respuesta sencilla para clase.
88. ¿Por qué este proyecto no usa un framework (Laravel, Symfony) y aun así es útil para aprender?

---

## K. Ampliaciones: otro apartado (administrador) y otras vistas

89. ★ Quieres un área **administrador** con otra “cara” (otro layout). ¿Crearías `views/admin/main.php` distinto de `views/main.php`? ¿Cómo decidiría el controlador qué layout usar?
90. ★ Diseña a alto nivel un `adminController` (o `usuarioController`): ¿qué actions tendrías (`login`, `logout`, `dashboard`)?
91. ¿Cómo protegerías las rutas de admin para que un visitante normal no pueda entrar? (sesión PHP, idea general)
92. Si el admin lista productos pero con botones extra (editar, estadísticas), ¿reutilizas `CamisetaDAO::getAll()` o creas otro DAO? ¿Por qué?
93. ★ Propón la estructura de carpetas para: tienda pública + panel admin, manteniendo un solo `index.php`.
94. ¿Cómo harías que `?controller=admin&action=index` use CSS distinto sin copiar todas las vistas?
95. Si añades la entidad `Pantalones`, ¿qué ficheros nuevos creas y cuáles reutilizas gracias a `Producto`?
96. ¿Cómo implementarías `edit` + `update` reutilizando casi el mismo formulario que `create`?
97. ★ Cómo añadirías un campo `stock` (entero) de punta a punta: SQL → modelo → DAO → formulario → listado.
98. Cómo añadirías búsqueda/filtro por talla en el listado (`?talla=M`). ¿En qué capa pondrías el `WHERE`?
99. Cómo paginarías el listado (10 por página). ¿Qué parámetros GET usarías?
100. ★ Cómo sacarías header y footer a `views/partials/header.php` y `footer.php` y los incluirías desde `main.php`. Escribe el esqueleto mental del `main.php` resultante.

---

## L. Ampliaciones: calidad, seguridad y “siguiente nivel”

101. ¿Qué es XSS y dónde estáis ya protegiendo (parcialmente) en las vistas?
102. ¿Qué es SQL Injection y cómo os ayudan los *prepared statements*?
103. ¿Qué es un token CSRF (idea) y en qué formularios lo pondrías primero?
104. ¿Cómo mostrarías un mensaje “Producto creado correctamente” tras el redirect? (idea de *flash* con `$_SESSION`)
105. ¿Qué mejorarías del `README.md` para que un compañero monte el proyecto en 5 minutos?
106. ★ Si el proyecto crece a 10 controladores, ¿qué problema tiene cargar includes a mano? ¿Qué es un autoload (idea)?
107. ¿Qué testras a mano antes de dar por buena una práctica? Lista un checklist de 5 pruebas en el navegador.

---

# Respuestas

Las respuestas describen el código actual. Cuando una pregunta plantea una ampliación, se indica una posible solución; no significa que ya esté implementada.

## A. Visión general y estructura

1. **MVC** separa datos y acceso a ellos (**Model**, `models/`), presentación (**View**, `views/`) y coordinación de peticiones (**Controller**, `controllers/`). `index.php` actúa como enrutador frontal.
2. `index.php` es el punto de entrada y el *front controller*: interpreta los parámetros de la URL, carga el controlador, comprueba la acción y la ejecuta. No es una vista ni una entidad del modelo.
3. `parameters.php` define la URL base y las acciones por defecto; `dataBase.php` configura y abre la conexión MySQL. Son configuración/infraestructura, no parte del MVC de dominio.
4. `sql/init.sql` crea la base de datos, la tabla y unos datos de ejemplo. PHP usa la base ya preparada; no crea automáticamente su esquema.
5. `controllers/`: lógica que coordina petición, modelo y vista. `models/`: entidades y acceso a datos. `views/`: HTML de presentación. `config/`: parámetros y conexión. `sql/`: scripts de creación y datos iniciales.
6. Una entidad representa un producto y sus datos (`Producto`, `Camiseta`); un DAO (`CamisetaDAO`) contiene las operaciones para leer y modificar esos datos en la base de datos.
7. `Producto` es abstracta para servir como clase base común y no instanciar el concepto genérico directamente. `Camiseta` es concreta y se puede crear como objeto.
8. Son constantes que identifican tipos de producto (camiseta y pantalones). Por ahora son solo etiquetas declaradas en `Producto`; no se usan en el flujo actual.

## B. Flujo de la aplicación

9. Al abrir `/`, `index.php` detecta que falta `controller` y responde con una redirección a la URL configurada en `url` más `?controller=producto`. En la petición siguiente se ejecuta la acción por defecto, `index`, que lista los productos.
10. `index.php` toma `producto`, forma el nombre `productoController` y la ruta `controllers/productoController.php`, carga el fichero, crea el controlador, valida que `index` sea invocable y lo ejecuta. La acción llama a `CamisetaDAO::getAll()` y muestra `views/productos/listado.php` dentro de `views/main.php`.
11. Se usa `default_action`, cuyo valor actual es `index` en `config/parameters.php`. Lo selecciona `index.php`.
12. `config/parameters.php` define `default_controller` (`producto`) y `default_action` (`index`). Al cambiarlos, cambian el controlador al que se redirige la raíz y/o la acción usada cuando no se especifica `action`.
13. El enlace de `views/productos/listado.php` abre `producto/create`; `productoController::create()` presenta el formulario. Al enviarlo por POST, `productoController::store()` lee y valida los datos, construye un `Camiseta` y llama a `CamisetaDAO::store()`. Después redirige al listado; `index()` vuelve a obtener los productos y la vista los muestra.
14. `show` valida el `id`, pide el producto a `CamisetaDAO::getById()` y presenta `views/productos/show.php`. `destroy` valida el `id`, llama a `CamisetaDAO::destroy()` y redirige al listado. Ambas reciben un id; `show` consulta y representa, mientras que `destroy` modifica la base de datos. Actualmente eliminar se solicita mediante GET.
15. `header('Location:...')` envía al navegador una respuesta de redirección. `exit` detiene el script para que no continúe ejecutando la acción ni genere contenido después de redirigir.
16. `store` y `destroy` realizan una operación y luego redirigen para que el navegador cargue otra petición que sí presenta una vista. Así evitan mostrar una respuesta duplicada del formulario o del borrado.
17. **Post-Redirect-Get (PRG)** es el patrón de responder a un POST con una redirección y luego cargar una página con GET. `store` lo aplica al guardar y redirigir al listado; evita que al recargar se reenvíe el formulario. `destroy` también redirige, aunque la petición de borrado actual es GET, no POST.
18. No. Cada petición PHP tiene su propio contexto de ejecución y sus variables locales. Los alumnos consultan la misma base de datos, pero no comparten la variable `$productos` en memoria.

## C. Front controller

19. Un *front controller* es una única entrada que recibe y enruta las peticiones. Aquí `index.php` carga y valida el controlador y la acción, centralizando la selección de rutas y los errores.
20. Se concatena el valor recibido con `Controller`: `producto` produce `productoController`.
21. `index.php` concatena `controllers/`, el nombre construido y `.php`; para `producto` resulta `controllers/productoController.php`.
22. `showError()` fija el código HTTP, prepara el código y mensaje, selecciona `views/error.php` y lo incluye desde `views/main.php`. El código HTTP informa correctamente al navegador/cliente y el layout mantiene una presentación común.
23. **404** indica que el recurso solicitado no existe; por ejemplo, un fichero de controlador inexistente o una acción no invocable. **500** indica un error interno de la aplicación; aquí, por ejemplo, que el fichero sí existe pero no define la clase esperada.
24. `class_exists()` comprueba si la clase está definida. Se comprueba después del `include_once` porque el fichero del controlador es el que debería declararla.
25. `is_callable()` comprueba que el método se puede invocar en ese objeto. `method_exists()` solo informa de que existe un método; no garantiza por sí solo que sea accesible/invocable. El código no usa una lista permitida de acciones, por lo que convendría restringirlas explícitamente.
26. `__construct` es un método público y, con esta implementación, puede resultar invocable: el enrutador podría volver a ejecutar el constructor del controlador. Los constructores no deberían exponerse como acciones; lo correcto es usar una lista explícita de acciones permitidas.
27. El enrutador busca la clase `productoController`. Si el fichero carga pero solo declara `ProductoCtrl`, `class_exists()` no encuentra la clase esperada y se muestra un error 500.
28. Como el parámetro se concatena directamente en una ruta, un valor con segmentos como `../` podría intentar salir de `controllers/` e incluir otro fichero PHP accesible. Hay que validar el controlador con una lista permitida o un mapeo fijo, además de no confiar en una ruta construida desde la entrada del usuario.
29. `include_once` incluye el fichero como máximo una vez, evitando inclusiones duplicadas. `include` también permite continuar tras un fallo (con aviso); `require` detiene la ejecución si falla. Para un fichero imprescindible, como configuración o una clase requerida, suele ser más apropiado `require_once`.

## D. Controlador

30. El controlador interpreta la petición, valida entradas, llama al modelo/DAO y elige vista o redirección. No debería contener consultas SQL ni grandes bloques de HTML, que corresponden al DAO/modelo y a las vistas.
31. `index`: obtiene y muestra el listado. `create`: muestra el formulario. `store`: valida y guarda los datos recibidos por POST, y redirige. `show`: valida el id, consulta y muestra el detalle. `destroy`: valida el id, elimina y redirige.
32. `create` solo presenta la interfaz; `store` procesa la petición de escritura. Separarlos distingue la petición que muestra el formulario de la que modifica datos y permite aplicar validación al enviar.
33. `TALLAS_VALIDAS` define las tallas admitidas por la aplicación. Una talla `XYZ` falla la validación y `store` redirige a `create` sin guardar el producto.
34. `trim()` elimina espacios al principio/final del nombre para que un nombre compuesto solo por espacios no pase como válido. `(float)` convierte el precio validado a número decimal antes de construir el objeto.
35. `ctype_digit()` comprueba que la representación del id contiene solo dígitos. `3abc` y `-1` no pasan y la acción redirige al listado sin consultar ni eliminar.
36. Si `getById()` no encuentra fila, devuelve `null`; `show()` entonces redirige al listado. Es simple, pero no informa de lo ocurrido. Se podría mostrar un error 404 o un mensaje flash de “producto no encontrado”.
37. `store` recibe los campos del formulario por POST. `show` y `destroy` reciben el identificador mediante parámetros de URL GET. Para modificar o borrar datos debe preferirse POST (o un método HTTP adecuado), especialmente para evitar efectos secundarios por enlaces o peticiones automáticas.
38. Un enlace GET puede activar el borrado al abrirse, compartirse, rastrearse o ser solicitado por otra página; además no expresa que se trata de una operación de escritura y no tiene protección CSRF. Se usaría un `<form method="POST">` con botón de confirmación, token CSRF y validación del token en el servidor.
39. `edit` buscaría el producto y mostraría el formulario con sus valores actuales. `update` validaría el id y los nuevos datos, actualizaría mediante el DAO y redirigiría. Se podría reutilizar un formulario compartido entre `create` y `edit`.

## E. Modelo, herencia y propiedades

40. `fetch_object("Camiseta")` asigna los valores de las columnas a propiedades con nombres correspondientes. Si los nombres no coinciden, la propiedad esperada no se rellenaría automáticamente y los getters podrían devolver un valor ausente o incorrecto.
41. Un *getter* lee una propiedad y un *setter* la modifica. Las propiedades son `protected`, así que la vista no debería acceder directamente; usa getters como `getNombre()` para respetar la encapsulación.
42. El constructor acepta `null` como “no se proporcionó valor” y solo asigna los argumentos que no sean `null`. Así una instancia creada por `fetch_object`, que ya ha recibido los campos de la fila, no los sobrescribe con valores por defecto.
43. Si el constructor asignara siempre `''` u otros valores por defecto, la llamada al constructor hecha al hidratar el objeto podría sobrescribir los valores que `fetch_object` acaba de leer de la fila. El objeto perdería los datos de la base de datos.
44. `parent::__construct(...)` llama al constructor de `Producto` para inicializar las propiedades heredadas sin repetir esa lógica en `Camiseta`.
45. No; `Camiseta` no añade atributos propios actualmente. Para añadir `manga_corta` habría que definir la propiedad y su acceso en el modelo, añadir la columna en SQL, incluirla en las consultas del DAO y presentarla/recogerla en las vistas y el controlador.
46. `setId()` permite asignar el id cuando se hidrata el objeto desde la base de datos o cuando se necesita construir/configurar el objeto desde código. Para un registro nuevo, normalmente el id lo asigna `AUTO_INCREMENT`.

## F. DAO, mysqli y `fetch_object`

47. DAO significa *Data Access Object*: objeto de acceso a datos. Separarlo permite que el controlador no conozca SQL y concentra las consultas y operaciones de persistencia.
48. Son `static` para poder llamarlos como `CamisetaDAO::getAll()` sin crear una instancia; el DAO actual no conserva estado. Se podrían hacer de instancia para inyectar una conexión u otras dependencias, lo que facilita pruebas y flexibilidad, a cambio de tener que gestionar esas instancias.
49. `getAll()` abre una conexión, prepara el `SELECT`, lo ejecuta, obtiene el resultado, recorre cada fila convirtiéndola en un `Camiseta` y la guarda en un array. Al final cierra la sentencia y la conexión y devuelve el array.
50. Obtiene la siguiente fila como un objeto de la clase `Camiseta`: los campos de la fila se asignan a las propiedades que correspondan y se ejecuta el constructor de la clase según el comportamiento de `mysqli`.
51. En este patrón de `mysqli`, primero se asignan los campos de la fila al objeto y después se llama al constructor. Importa porque un constructor que escriba valores por defecto podría sobrescribir esos campos; por eso `Producto` solo asigna argumentos no nulos.
52. `fetch_object("Camiseta")` devuelve objetos con comportamiento y métodos de esa clase; resulta útil para trabajar con entidades y sus getters. `fetch_assoc()` devuelve un array asociativo por nombres de columna, conveniente para resultados puntuales sin necesitar una entidad.
53. `"i"` indica que `$id` se enlaza como entero. El marcador `?` hace que el valor se envíe separado de la consulta SQL; evita que el contenido del id se interprete como parte de la sentencia, a diferencia de concatenarlo.
54. En `"ssd"`, `s` es cadena (*string*), `s` es cadena y `d` es número decimal (*double*): nombre, talla y precio.
55. `bind_param()` enlaza variables por referencia. Las variables locales `$nombre`, `$talla` y `$precio` proporcionan referencias válidas a los valores de los getters para enlazarlos a la sentencia.
56. Si no hay fila, `fetch_object()` devuelve `false`; la expresión `$producto ?: null` convierte ese valor falso en `null`, que es lo que recibe el controlador.
57. Si la columna se llamara `name`, `fetch_object()` intentaría asignar ese campo a `name`, no a `nombre`. Habría que usar un alias (`name AS nombre`) o ajustar el mapeo/propiedad.
58. Se liberan los recursos de la sentencia y la conexión al terminar cada método. Si se dejasen abiertos repetidamente, podrían acumularse recursos y conexiones ocupadas, causar límites alcanzados y degradar o impedir el servicio.
59. `CamisetaDAO` conoce SQL y persistencia. Las vistas conocen HTML. El controlador valida la talla en `TALLAS_VALIDAS`; la base también limita valores con `ENUM`. Para un diseño más sólido, la regla de dominio debería centralizarse o validarse en la capa de dominio además de comprobar entradas en el controlador.

## G. Vistas y layout

60. `views/main.php` es el layout común: contiene el documento HTML, estilos y el cuerpo. `include_once $view` inserta en él la vista concreta seleccionada por el controlador.
61. El controlador define `$view` antes de incluir `main.php`; `showError()` también la define para mostrar la vista de error.
62. Se puede escribir el header y footer comunes directamente alrededor de `include $view` en `main.php`, o extraerlos a `views/partials/header.php` y `footer.php` e incluir esos parciales desde el layout. Así no se repite el HTML en cada página.
63. Lo colocaría en `main.php` o en un parcial incluido por este, porque listado, create y show comparten la navegación y el layout.
64. Las vistas parciales solo contienen el contenido específico. El layout se ocupa de `<html>`, `<head>`, estilos y estructura común, evitando duplicación y manteniendo una presentación consistente.
65. Convierte caracteres especiales en entidades HTML, con comillas incluidas y codificación UTF-8. Evita que valores introducidos por usuarios se interpreten como HTML/JavaScript y ayuda a prevenir XSS en ese contexto HTML.
66. Aparece la fila “No hay productos todavía.”; el mensaje está escrito en `views/productos/listado.php`, dentro de la comprobación `empty($productos)`.
67. Se puede definir `$title` en el controlador antes de incluir `main.php` y usarlo en `<title>`, con un valor por defecto. Cada acción asignaría “Listado”, “Crear” o “Detalle”.
68. Se añadiría un `<link rel="stylesheet" href="...">` dentro de `<head>` en `views/main.php`, apuntando a `public/css/estilo.css`. La ruta debe ser accesible desde la URL base del sitio.

## H. Base de datos y configuración

69. `sql/init.sql` crea la base `daw2mvc`, selecciona la base, elimina y vuelve a crear `camisetas`, y añade tres registros. `DROP TABLE IF EXISTS` permite repetir la inicialización sin error por tabla existente, pero borra sus datos: es peligroso en producción.
70. `talla` es un `ENUM` con los valores permitidos `XXL`, `XL`, `L`, `M` y `S`. Solo se deben insertar esos valores; otro puede producir un error o un valor no válido según el modo SQL.
71. Se cambia `DB_PASS` en `config/dataBase.php` (y la credencial correspondiente en MySQL). No conviene subir una contraseña real a Git; se debe guardar en variables de entorno o configuración local excluida del repositorio.
72. `set_charset("utf8mb4")` configura la conexión para intercambiar correctamente texto Unicode, incluidos acentos, caracteres especiales y emojis, evitando problemas de codificación.
73. Hay que cambiar el nombre en `DB_NAME` de `config/dataBase.php` y adaptar `sql/init.sql`/la base que se cree para que coincidan.
74. Ambos designan normalmente la máquina local. `127.0.0.1` es una dirección IPv4 y fuerza una conexión TCP; `localhost` es un nombre que se resuelve localmente y, según sistema/configuración de MySQL, puede usar otro mecanismo. El comportamiento exacto depende del entorno.

## I. “¿Qué pasaría si…?”

75. En PHP los nombres de clase no distinguen mayúsculas/minúsculas: `ProductoController` y `productoController` se consideran el mismo nombre de clase. En Windows el nombre del fichero también suele ser insensible a mayúsculas; en sistemas como Linux, la ruta del fichero sí puede distinguirlas.
76. Las propiedades de `Producto` son `protected`, así que la vista no debe leer `$producto->nombre` directamente. Puede causar un error de acceso (o no obtener el dato esperado); la forma correcta es `$producto->getNombre()`.
77. Si no se incluye `main.php`, el controlador solo asigna la ruta en `$view` pero no renderiza la vista: normalmente se verá una página vacía.
78. Si el formulario envía a `action=index`, se ejecuta el listado y no `store`; los datos POST no se guardan y el producto no se crea.
79. `DataBase::connect()` ejecuta `die()` si falla y muestra el mensaje de conexión junto con el error de MySQL. En producción no debería exponerse el detalle; conviene registrar el error y presentar un mensaje genérico.
80. Sin POST, `store` lee valores vacíos, falla la validación y redirige a `create`; no inserta un producto. Aun así, conviene restringir `store` al método POST explícitamente.
81. El código no impide nombres repetidos: la tabla no tiene una restricción `UNIQUE` sobre `nombre`, por lo que se pueden guardar dos productos iguales.
82. `is_numeric("12,50")` devuelve falso en PHP; `store` rechaza el dato y redirige al formulario. El campo numérico y el servidor deben acordar el formato, normalmente con punto decimal.
83. Se muestra el error “No existe el controller productoController” con HTTP 404, usando el layout de error.
84. `is_callable()` no encuentra la acción `foo`; `index.php` llama a `showError()` con HTTP 404 y muestra el mensaje de acción inexistente.

## J. MVC en la práctica

85. Se mezcla presentación (HTML) con acceso a datos y se rompe la separación de responsabilidades del MVC; el DAO debería devolver datos, no imprimir la página.
86. Se acopla la consulta al controlador y habría que duplicarla para otro controlador, dificultando mantenimiento y reutilización. El DAO debería centralizar el acceso a camisetas.
87. El controlador debe validar pronto los datos recibidos para responder correctamente a entradas inválidas. Las reglas esenciales del dominio también deberían protegerse en el modelo/servicio o en la base de datos para que no se puedan saltar desde otra ruta de código. Ahora la talla se valida en el controlador y además está limitada por el `ENUM` SQL.
88. Al no usar framework, el proyecto deja visibles las piezas básicas de MVC, enrutamiento, conexión y consultas, lo que facilita aprenderlas. A cambio, muchas funciones habituales de seguridad y estructura deben implementarse y mantenerse manualmente.

## K. Ampliaciones: administrador y otras vistas

89. Sí: un `views/admin/main.php` puede proporcionar otro layout. El controlador admin puede seleccionarlo al renderizar, o se puede centralizar la selección en un mecanismo de layouts según el área.
90. `login` mostraría/procesaría el acceso; `logout` cerraría la sesión; `dashboard` mostraría el panel. También habría acciones para listar/editar productos. Las contraseñas se verificarían con hashes y las acciones privadas requerirían autenticación.
91. Iniciar sesión con `session_start()`, guardar en `$_SESSION` un indicador/identidad autenticada tras validar credenciales y comprobarlo al principio de cada acción protegida. Si no está autenticado, redirigir a login. No basta con ocultar el enlace en la vista.
92. Reutilizaría `CamisetaDAO::getAll()` si los datos son los mismos; los botones extra son presentación/acciones de administración, no razón para duplicar consultas. Solo añadiría métodos DAO para operaciones nuevas.
93. Por ejemplo: `index.php`; `controllers/productoController.php` y `controllers/adminController.php`; `models/Producto.php`, `Camiseta.php`, `CamisetaDAO.php`; `views/main.php`, `views/partials/`, `views/productos/` y `views/admin/`; además de `config/` y `sql/`. Un solo front controller enruta ambos controladores.
94. Se puede hacer que las acciones admin incluyan `views/admin/main.php`, que cargue su CSS, y desde ahí reutilizar vistas parciales compartidas. También se puede elegir el CSS desde un layout común según el área, sin duplicar cada vista.
95. Añadiría una entidad `Pantalones.php` que extienda `Producto`, un DAO y, según el diseño, controlador y vistas para pantalones; también su tabla/script SQL. Se reutilizan `Producto`, configuración, front controller y layouts/parciales comunes.
96. `edit` obtiene el producto y pasa sus valores al formulario; el formulario compartido puede recibir el modo y la acción destino. `create` lo usa vacío y `edit` precargado; `update` valida y persiste los cambios mediante un método del DAO.
97. Añadiría `stock INT` a la tabla SQL; propiedad y getters/setters en el modelo; lo incluiría en consultas de lectura/escritura y operaciones del DAO; recogería y validaría un entero no negativo en el controlador; agregaría el campo al formulario y una columna en el listado.
98. El controlador leería y validaría `$_GET['talla']`; el DAO recibiría el filtro y añadiría `WHERE talla = ?` con sentencia preparada. El `WHERE` pertenece a la consulta de acceso a datos (DAO), no a la vista.
99. Usaría `?pagina=1`, calcularía `LIMIT 10 OFFSET (pagina - 1) * 10` tras validar que la página sea un entero positivo. El DAO consultaría el segmento y la vista mostraría enlaces anterior/siguiente y, si se desea, el total de páginas.
100. El `main.php` quedaría conceptualmente así:

   ```php
   <!DOCTYPE html>
   <html lang="es">
   <head>
       <meta charset="UTF-8">
       <title><?= htmlspecialchars($title ?? 'Daw2mvc', ENT_QUOTES, 'UTF-8') ?></title>
       <link rel="stylesheet" href="public/css/estilo.css">
   </head>
   <body>
       <?php include 'views/partials/header.php'; ?>
       <main>
           <?php include $view; ?>
       </main>
       <?php include 'views/partials/footer.php'; ?>
   </body>
   </html>
   ```

## L. Calidad, seguridad y siguiente nivel

101. XSS es la ejecución de contenido/script inyectado en una página vista por otra persona. En listado y detalle se escapan nombre y talla con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`; el precio se formatea como número y los ids se convierten a entero. Hay que escapar según el contexto de salida.
102. SQL Injection ocurre cuando una entrada se interpreta como parte de la sintaxis SQL. Las sentencias preparadas (`?` y `bind_param`) mantienen valores como datos separados del SQL; se usan en consultas por id y en el INSERT.
103. Un token CSRF es un valor aleatorio ligado a la sesión que el servidor exige al recibir una operación, para comprobar que procede de un formulario legítimo. Se pondría primero en `store` y en el borrado (que debería pasar a POST), y también en cualquier futura actualización.
104. Tras `session_start()`, `store` podría guardar `$_SESSION['flash'] = 'Producto creado correctamente'` antes de redirigir. El listado leería y mostraría el mensaje una sola vez y luego eliminaría esa clave de sesión.
105. Añadiría pasos concretos para requisitos e instalación de PHP/MySQL, habilitar `mysqli`, importar `sql/init.sql`, configurar credenciales y URL, arrancar con `php -S localhost:8000`, abrir rutas de ejemplo y resolver errores comunes de conexión.
106. Con muchos controladores, las rutas y los `include_once` manuales son repetitivos y propensos a errores. Un *autoload* carga automáticamente el fichero de una clase cuando se usa; Composer con PSR-4 puede relacionar namespaces y clases con carpetas.
107. Checklist de cinco pruebas en navegador:
   1. Abrir el listado y comprobar que aparecen los datos iniciales.
   2. Crear un producto válido y confirmar que aparece tras la redirección.
   3. Intentar enviar datos inválidos (por ejemplo, precio negativo o talla no permitida) y comprobar que no se guardan.
   4. Abrir el detalle de un id existente y probar también un id inválido/inexistente.
   5. Eliminar un producto y comprobar que desaparece del listado; además, probar un controlador/acción inexistente y verificar la respuesta 404.