
## WordPress Clima  Aplicacion Plugin

Es una prueba tecnica para la construccion de un plugin que consume sirvicios de 
https://openweathermap.org se guardan en una base de datos con una paramitrizacion basica
Igual se listan utilizan ajax para su visualización con un maximo de 5 registros para su paginacion
Igualmente se actualiza los datos recargando la informacion del servicio meterologico

INSTALACCION

Para instalar el plugin debe bajar el proyecto bien sea comprimido o por git
Una vez activado le aparece un Opcion en de "CLIMA" el cual contiene tres submenus

1. Parametros : Se debe parametrizar primero se requiere API KEY de servicio https://openweathermap.org
   y una descripcion de este codigo.
    https://github.com/ispardoa/clima_plugin/blob/master/parametros.JPG
    
2. Nuevas entradas : Se registra los diferentes ciudades para cargar las variables del clima.
   Ejemplo {Bogotá, Datos de Bogotá} El sistema le indicara que "los datos fueron ingresados correctamente"
   https://github.com/ispardoa/clima_plugin/blob/master/entradasclima.JPG
      
3. Visualizar entradas: En esta opcion por lote podra ver las entradas realizadas. Para ver la paginación por
   medio de  AJAX. Se recomienda el ingreso de minimo 6 ciudades. 
   En esta cuadricula maneja acciones para varios registros en la parte superior el cual podra 
   borrar uno o varios registros
   https://github.com/ispardoa/clima_plugin/blob/master/visualizarentradas.JPG
   
Nota: Para visualizar el contenido de la tabla se realiza por medio de un [shortcode] a una post_type 
Ejempo : [dbtable from="wp_historico"] 
