Creación de módulos:
* php artisan module:make "Nombre del módulo", en singular. Ejm: Usuario

Pages Vue:
* Los archivos Vue deben inicializar con el nombre de la carpeta seguida de la acción:
  pages/Dashboard/DashboardIndex

Observaciones:
* Las funciones deben ser CamelCase. "obtenerDatos"
* Las variables deben ser en singular, separadas por guiones bajo.
* Los modelos serán en singular.

Estructura de un módulo:
app
    DataTables
    Http
        Controllers
        Requests
        Resources (Nombre de carpetas internas serán en Singular según el modelo al cual hace referencia)
    Models
    Observers
    Providers

Actualizar PDF refcon:
php artisan referenciapdf:descargar

Envio SMS recordatorio:
php artisan sms:recordatorio

Recibir Cita Refcon:
php artisan refcon:recibir.cita

Recibir Referencia Paciente Refcon:
php artisan refcon:recibir.referencia.paciente

Consultar bandeja referidos recibidos desde Refcon:
php artisan refcon:consultar.bandeja.referidos.recibidos 
                {fecha_inicial? : Fecha inicial en formato Y-m-d (opcional, por defecto hoy)}
                {fecha_final? : Fecha final en formato Y-m-d (opcional, por defecto hoy)}
