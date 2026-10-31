# Publicación de la presentación en Render

El Blueprint de la raíz crea un servidor Docker con PHP 8.3/Apache y una base PostgreSQL separada, ambos en la misma región y en el plan `free`. No utiliza el Dockerfile de desarrollo Sail ni necesita PHP, Docker o MySQL en los equipos de los visitantes.

Antes de aplicarlo, configura dos secretos en Render:

- `ADMIN_PASSWORD`: contraseña administrativa de al menos 12 caracteres. Usuario inicial `admin`.
- `PRESENTATION_PASSWORD`: contraseña de al menos 12 caracteres para las cuentas ficticias de profesores, estudiantes y encargados (`docente.1`, `alumno.1`, `encargado.1`, etc.). No se usa la contraseña local publicada en ACCESO.md.

Render genera APP_KEY y proporciona DB_URL. No introduzcas estas claves ni contraseñas en el repositorio. El arranque configura APP_URL a partir del dominio que asigna Render, fuerza HTTPS, ejecuta migraciones y carga los datos ficticios solo si `PRESENTATION_DATA=true`.

Las cuentas y registros ya existentes se conservan: un nuevo despliegue no restablece sus contraseñas ni elimina las modificaciones del grupo. El comando `academic:prepare-presentation` puede repetirse. `PRESENTATION_DATA=false` desactiva esta carga en otros entornos.

La base de datos no permite conexiones públicas (`ipAllowList: []`); el servidor se conecta por la red privada de Render. Los registros de aplicación van a stderr, sin APP_DEBUG.

## Límites del plan gratuito

La base gratuita expira 30 días después de crearse. El servidor puede suspenderse tras 15 minutos sin uso y tardar aproximadamente un minuto en responder a la próxima visita. No se almacena SQLite ni se confía en archivos locales persistentes. Este despliegue sirve para el taller, con personas ficticias; no es un alojamiento permanente para datos reales.

Referencias verificadas: https://render.com/docs/free y https://render.com/docs/blueprint-spec.

El conjunto de presentación corresponde a 2026 y sus períodos tienen fechas reales: a partir del 31 de octubre no admite notas nuevas para la cuarta unidad cerrada por fecha. Para practicar el registro de notas, configura un ciclo y período vigentes desde el aplicativo, respetando el historial.

## Estado de la validación en esta cuenta

La comprobación con Render CLI 2.34.0 fue rechazada con `need_payment_info` para el servidor, la base y el Blueprint. No se crearon recursos ni se inició ningún despliegue. Debe completarse la verificación de facturación de la cuenta en Render y repetirse `render blueprints validate render.yaml` antes de aplicar el Blueprint. La sintaxis PHP y de shell se verificó localmente; la imagen Docker y PostgreSQL aún no se han ejecutado en Render.
