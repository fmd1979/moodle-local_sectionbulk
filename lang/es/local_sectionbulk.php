<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cadenas en español para local_sectionbulk.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Gestor masivo de secciones y cuestionarios';
$string['sectionbulk:manage'] = 'Gestionar secciones y cuestionarios de forma masiva';
$string['intro'] = 'Permite previsualizar y aplicar cambios masivos en secciones y cuestionarios. Siempre se exige una vista previa antes de aplicar cambios.';
$string['credits'] = 'Desarrollado por SiteEcuador · Msg. Franklin Moya';
$string['scope'] = 'Alcance';
$string['scope_course'] = 'IDs de cursos específicos';
$string['scope_category'] = 'Categoría de cursos';
$string['courseids'] = 'IDs de cursos';
$string['courseids_help'] = 'Uno o varios IDs separados por comas, por ejemplo: 12,15,18.';
$string['categoryid'] = 'Categoría';
$string['recursive'] = 'Incluir subcategorías';
$string['sectiontarget'] = 'Sección objetivo';
$string['sectionmode'] = 'Buscar sección por';
$string['sectionmode_number'] = 'Número de sección';
$string['sectionmode_name'] = 'Nombre exacto de la sección';
$string['sectionnumber'] = 'Número de sección';
$string['sectionname'] = 'Nombre de la sección';
$string['allmatches'] = 'Procesar todas las secciones coincidentes si un curso tiene nombres duplicados';
$string['operation'] = 'Operación';
$string['op_date_from_set'] = 'Sección — agregar/actualizar Date from';
$string['op_date_until_set'] = 'Sección — agregar/actualizar Date until';
$string['op_date_range_set'] = 'Sección — agregar/actualizar Date from + Date until';
$string['op_date_from_remove'] = 'Sección — eliminar Date from';
$string['op_date_until_remove'] = 'Sección — eliminar Date until';
$string['op_date_all_remove'] = 'Sección — eliminar todas las restricciones de fecha';
$string['op_profile_set'] = 'Sección — agregar/actualizar restricción de campo de perfil';
$string['op_completion_remove'] = 'Sección — eliminar restricciones de Activity completion';
$string['op_section_create'] = 'Sección — crear una nueva sección';
$string['op_quiz_open_set'] = 'Quiz — configurar Open the quiz';
$string['op_quiz_close_set'] = 'Quiz — configurar Close the quiz';
$string['op_quiz_range_set'] = 'Quiz — configurar Open + Close';
$string['op_quiz_open_remove'] = 'Quiz — retirar Open the quiz';
$string['op_quiz_close_remove'] = 'Quiz — retirar Close the quiz';
$string['op_quiz_dates_remove'] = 'Quiz — retirar Open + Close';
$string['op_quiz_attempts_set'] = 'Quiz — configurar número de intentos';
$string['op_quiz_grademethod_set'] = 'Quiz — configurar método de calificación';
$string['op_quiz_highest_multiattempt'] = 'Quiz — usar Highest grade cuando tenga 2 o más intentos';
$string['fromdate'] = 'Fecha desde';
$string['untildate'] = 'Fecha hasta';
$string['profilefield'] = 'Campo personalizado de perfil';
$string['profileoperator'] = 'Operador del perfil';
$string['profilevalue'] = 'Valor del perfil';
$string['showcondition'] = 'Mostrar información de la restricción cuando no esté disponible';
$string['newsectionname'] = 'Nombre de la nueva sección';
$string['newsectionsummary'] = 'Resumen de la nueva sección';
$string['newsectionposition'] = 'Posición';
$string['newsectionposition_help'] = 'Usa 0 para agregarla al final. Usa 1 o más para insertarla en ese número de sección.';
$string['newsectionvisible'] = 'Nueva sección visible';
$string['allowduplicates'] = 'Permitir crear una sección con nombre duplicado';
$string['quiztarget'] = 'Cuestionario objetivo';
$string['quiztargetmode'] = 'Buscar cuestionarios por';
$string['quiztarget_all'] = 'Todos los cuestionarios de los cursos seleccionados';
$string['quiztarget_name'] = 'Nombre exacto del cuestionario';
$string['quiztarget_idnumber'] = 'ID number de la actividad';
$string['quizname'] = 'Nombre exacto del cuestionario';
$string['quizidnumber'] = 'ID number de la actividad Quiz';
$string['quizsectionnumber'] = 'Filtro opcional por número de sección';
$string['quizsectionnumber_help'] = 'Déjalo vacío para buscar en todas las secciones. Escribe un número para limitar la búsqueda del quiz a esa sección.';
$string['quizallmatches'] = 'Procesar todos los quizzes coincidentes si un curso contiene más de uno';
$string['quizopen'] = 'Open the quiz';
$string['quizclose'] = 'Close the quiz';
$string['quizattempts'] = 'Intentos permitidos';
$string['quizattempts_help'] = 'Usa 0 para intentos ilimitados, 1 para un intento, 2 para dos intentos, etc.';
$string['quizgrademethod'] = 'Método de calificación';
$string['quizgrademethod_highest'] = 'Highest grade / Calificación más alta';
$string['quizgrademethod_average'] = 'Average grade / Calificación promedio';
$string['quizgrademethod_first'] = 'First attempt / Primer intento';
$string['quizgrademethod_last'] = 'Last attempt / Último intento';
$string['quizincludeunlimited'] = 'Tratar intentos ilimitados como múltiples intentos';
$string['quizregrade'] = 'Recalcular notas finales del quiz y actualizar el libro de calificaciones al cambiar el método';
$string['preview'] = 'Vista previa';
$string['applychanges'] = 'Aplicar cambios';
$string['cancel'] = 'Cancelar';
$string['previewtitle'] = 'Vista previa';
$string['resulttitle'] = 'Resultado';
$string['course'] = 'Curso';
$string['target'] = 'Objetivo';
$string['section'] = 'Sección';
$string['status'] = 'Estado';
$string['details'] = 'Detalle';
$string['changed'] = 'Requiere cambio';
$string['unchanged'] = 'Sin cambios';
$string['skipped'] = 'Omitido';
$string['error'] = 'Error';
$string['applied'] = 'Aplicado';
$string['confirmation'] = 'Revisa cuidadosamente la vista previa. El botón siguiente aplicará estos cambios en Moodle.';
$string['nothingtoapply'] = 'No hay cambios para aplicar.';
$string['invalidpreview'] = 'La vista previa expiró o no es válida. Genérala nuevamente.';
$string['coursesfound'] = 'Cursos encontrados: {$a}';
$string['changesfound'] = 'Cambios propuestos: {$a}';
$string['changesapplied'] = 'Cambios aplicados: {$a}';
$string['dryrunnote'] = 'La vista previa no escribe ningún cambio en la base de datos.';
$string['profile_contains'] = 'contiene';
$string['profile_doesnotcontain'] = 'no contiene';
$string['profile_isequalto'] = 'es igual a';
$string['profile_startswith'] = 'empieza con';
$string['profile_endswith'] = 'termina con';
$string['profile_isempty'] = 'está vacío';
$string['profile_isnotempty'] = 'no está vacío';
$string['validation_courseids'] = 'Ingresa al menos un ID de curso válido.';
$string['validation_category'] = 'Selecciona una categoría.';
$string['validation_sectionnumber'] = 'Ingresa un número de sección válido.';
$string['validation_sectionname'] = 'Ingresa el nombre exacto de la sección.';
$string['validation_profilefield'] = 'Selecciona un campo personalizado de perfil.';
$string['validation_profilevalue'] = 'Ingresa un valor para este operador.';
$string['validation_daterange'] = 'La fecha desde debe ser anterior a la fecha hasta.';
$string['validation_newsectionname'] = 'Ingresa el nombre de la nueva sección.';
$string['validation_quizname'] = 'Ingresa el nombre exacto del quiz.';
$string['validation_quizidnumber'] = 'Ingresa el ID number de la actividad Quiz.';
$string['validation_quizsectionnumber'] = 'Ingresa un número de sección válido o deja el campo vacío.';
$string['validation_quizrange'] = 'Open the quiz debe ser anterior a Close the quiz.';
$string['validation_quizattempts'] = 'El número de intentos debe ser 0 o mayor.';
$string['eventbulkchangeapplied'] = 'Cambios masivos de secciones y cuestionarios aplicados';
$string['eventdescription'] = 'El usuario con id {$a} aplicó cambios masivos mediante el Gestor masivo de secciones y cuestionarios.';
$string['detail_sectionnotfound'] = 'No se encontró la sección objetivo en este curso.';
$string['detail_duplicate'] = 'Se encontraron {$a} secciones coincidentes; se omitió por seguridad.';
$string['detail_formatnosections'] = 'El formato de curso {$a} no utiliza secciones.';
$string['detail_sectionexists'] = 'Ya existe una sección con ese nombre.';
$string['detail_sectioncreate'] = 'Se crearía una nueva sección en la posición {$a} (0 significa al final).';
$string['detail_sectioncreated'] = 'Nueva sección creada.';
$string['detail_datefromset'] = 'Se agregará o actualizará Date from; se conservan las demás restricciones.';
$string['detail_dateuntilset'] = 'Se agregará o actualizará Date until; se conservan las demás restricciones.';
$string['detail_daterangeset'] = 'Se agregarán o actualizarán Date from y Date until; se conservan las demás restricciones.';
$string['detail_alreadyconfigured'] = 'El valor solicitado ya está configurado.';
$string['detail_removedcount'] = 'Se eliminarán {$a} restricción(es) de fecha coincidentes; se conservan las demás.';
$string['detail_removedcompletion'] = 'Se eliminarán {$a} restricción(es) de Activity completion; se conservan las demás.';
$string['detail_nothingfound'] = 'No se encontró una restricción coincidente.';
$string['detail_profileset'] = 'Se agregará o actualizará la restricción de campo de perfil; se conservan las demás.';
$string['detail_quiznotfound'] = 'No se encontró ningún quiz que coincida con el objetivo seleccionado en este curso.';
$string['detail_quizduplicate'] = 'Se encontraron {$a} quizzes coincidentes; se omitió por seguridad. Activa procesar todas las coincidencias si corresponde.';
$string['detail_quizopenset'] = 'Se actualizará Open the quiz.';
$string['detail_quizcloseset'] = 'Se actualizará Close the quiz.';
$string['detail_quizrangeset'] = 'Se actualizarán Open the quiz y Close the quiz.';
$string['detail_quizopenremoved'] = 'Se retirará Open the quiz.';
$string['detail_quizcloseremoved'] = 'Se retirará Close the quiz.';
$string['detail_quizdatesremoved'] = 'Se retirarán Open the quiz y Close the quiz.';
$string['detail_quizattemptsset'] = 'Los intentos permitidos se configurarán en {$a} (0 significa ilimitados).';
$string['detail_quizgrademethodset'] = 'El método de calificación del quiz cambiará a {$a}.';
$string['detail_quizhighestset'] = 'El método de calificación cambiará a Highest grade / Calificación más alta.';
$string['detail_quiznotmultiattempt'] = 'Este quiz no cumple el criterio de 2 o más intentos.';
$string['detail_quizinvalidrange'] = 'La fecha resultante de Open the quiz debe ser anterior a Close the quiz.';
$string['quizlabel'] = 'Quiz: {$a->quiz} · {$a->section}';
$string['detail_unknownoperation'] = 'Operación desconocida.';
$string['error_invalidavailability'] = 'availability contiene JSON inválido: {$a}';
$string['error_encodeavailability'] = 'No se pudo generar el JSON de availability.';
$string['privacy:metadata'] = 'El Gestor masivo de secciones y cuestionarios no almacena datos personales.';
