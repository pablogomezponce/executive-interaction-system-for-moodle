# Executive Interaction System for Moodle

Cliente y API que permiten, una vez desplegados, extraer datos sobre la interacción de los usuarios de Moodle en relación a asignaturas específicas. En caso que el usuario sea profesor, puede acceder a la información de sus alumnos en la materia; en caso que sea alumno solo accede a su información; de esta manera se mantiene regulado el acceso a los datos de todos los usuarios y se permite que trabajen por separado.

## Instalación

Este proyecto está configurado para validar la información de sesión con los credenciales de [eStudy](https://estudy.salle.url.edu/). Si no se desea modificar los credenciales, se deben realizar los siguientes pasos:

1. Ejecutar eistudy_views.sql ubicado en `scripts_to_install/eistudy_views.sql`. Se debe substituir el origen de los datos a partir de la línea 7, incluida. `origin. --> %nombre_bbdd_origen`.
2. Ejecutar eistudy_user_creation.sql ubicado en `scripts_to_install/eistudy_user_creation.sql`. Descomentar y substituir contraseña.
3. Modificar credenciales en `app/dependencies.php`; en el bloque de líneas desde 22 hasta 25.
4. Iniciar el servidor y acceder a la dirección conveniente (si se ha empleado la imagen de Docker ofrecida, se debe acceder a [localhost](localhost) o [127.0.0.1](127.0.0.1)).
