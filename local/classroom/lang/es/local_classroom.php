<?php
/**
 * This file is part of eAbyas
 *
 * Copyright eAbyas Info Solutons Pvt Ltd, India
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author eabyas  <info@eabyas.in>
 * @package Bizlms 
 * @subpackage local_classroom
 */

$string['pluginname']='Aulas';
$string['browse_classrooms']='Administrar aulas';
$string['my_classrooms']='Mis Aulas';
$string['classrooms']='Ver el aula';
$string['costcenter']='Organización';
$string['department']='Cliente';
$string['shortname']='Nombre corto';
$string['shortname_help']='Ingrese el nombre corto del socio';
$string['classroom_offline']='Cursos sin conexión';
$string['classroom_online']='Cursos online';
$string['classroom_type']='Tipo de aula';
$string['course']='Curso';
$string['assign_test']='Asignar prueba';
$string['course_tests']='Cuestionarios';
$string['trainers']='Capacitadores';
$string['description']='Descripción';
$string['traning_help']='Buscar y seleccionar formadores que formarán parte de la formación presencial.';
$string['description_help']='Ingresa la descripción de Classroom. Esto se mostrará en la lista de Aulas.';
$string['internal']='Interno';
$string['external']='Externo';
$string['institute_type']='Tipo de ubicacion';
$string['institutions']='Instituciones';
$string['startdate']='Fecha de inicio';
$string['enddate']='Fecha final';
$string['create_classroom']='Crear aula';
$string['classroom_header']='Ver aulas';
$string['select_course']='--Seleccione Curso--';
$string['select_quiz']='--Seleccione Prueba--';
$string['select_trainers']='--Seleccione Capacitador--';
$string['select_institutions']='--Seleccionar ubicación--';
$string['classroom_name']='Nombre del aula';
$string['allow_multi_session']='Permitir múltiples sesiones';
$string['allow_multiple_sessions_help'] ='* Solucionado: si se selecciona, el sistema creará una sesión de 8 horas por día según la fecha de inicio y finalización seleccionada. * Personalizado: si se selecciona, no se creará ninguna sesión. El usuario puede crear una sesión en base a sus requisitos.';
$string['allow_multiple_sessions'] = "allow_multiple_session";
$string['fixed'] ='Fijo';
$string['custom'] ='Personalizado';
$string['need_manage_approval'] ='Necesita la aprobación del administrador ';
$string['need_manager_approval_help'] = "Select 'Yes', if you want to enforce manager approval workflow. All the enrollments into classroom training will be sent as requests to corresponding reporting manager's approval / rejection. If manager approves the request, users will be enrolled, if rejected users will not be enrolled.";
$string['capacity'] ='Capacidad';
$string['capacity_check_help'] ='Número total de usuarios que pueden participar en Classroom ';
$string['need_manager_approval'] ='need_manager_approval ';
$string['manage_classroom'] ='Administrar Classroom ';
$string['manage_classrooms'] ='Administrar aulas';
$string['assign_course'] ='Otros detalles';
$string['session_management'] ='Gestión de sesiones ';
$string['location_date'] ='Ubicación y fecha ';
$string['certification'] ='Certificación';
$string['learningplan'] ='Plan de aprendizaje ';
$string['classroom'] ='Aula';
$string['capacity_positive'] ='La capacidad debe ser superior a cero (0). ';
$string['capacity_limitexceeded'] ='La capacidad no puede exceder {$a}';
$string['missingclassroom'] ='Datos del aula perdidos ';
$string['courseduration'] ='Duración del curso';
$string['session_name'] ='Nombre de sesión ';
$string['session_type'] ='Tipo de sesión ';
$string['clrm_location_type'] ='Tipo de ubicación del aula ';
$string['classroom_locationtype_help'] ='Seleccione * Interno: si desea buscar y seleccionar ubicaciones internas como Sala de capacitación, Sala de conferencias, etc.que sean internas de su organización y donde se planea realizar la capacitación. * Externa: si desea buscar y seleccionar ubicaciones externas como Salones de baile de un hotel, sala de capacitación del instituto de capacitación, etc.que son externos a su organización y donde se planea realizar el capacitación. ';
$string['classroom_location'] ='Ubicación del aula ';
$string['location_room'] ='Ubicación del aula Habitación ';
$string['nomination_startdate'] ='Fecha de inicio de la nominación ';
$string['nomination_enddate'] ='Fecha de finalización de la nominación ';
$string['type'] ='Tipo';
$string['select_category'] ='--Selecciona una categoría--';
$string['deleteconfirm'] ='Estás seguro que quieres eliminar esto "<b>{$a}</b>" ¿aula?';
$string['deleteallconfirm'] ='¿Estas seguro que quieres borrarlo?';
$string['deletecourseconfirm'] ='¿Está seguro de que desea anular la asignación?';
$string['createclassroom'] ='<span class="classroom_icon_wrap"></div></span> Crear aula <div class="popupstring">Aquí creará aulas basadas en el cliente ';
$string['updateclassroom']='<span class="classroom_icon_wrap"></div></span> Actualizar Classroom <div class="popupstring">Aquí actualizará las aulas según el cliente ';
$string['save_continue'] ='Guardar Continuar';
$string['enddateerror'] ='La fecha de finalización debe ser mayor que la fecha de inicio. ';
$string['sessionexisterror']='Hay otras sesiones en este momento ';
$string['nomination_startdateerror'] ='El calendario de la fecha de inicio de la nominación debe ser menor que la fecha de inicio del salón de clases. ';
$string['nomination_enddateerror'] ='El calendario de la fecha de finalización de la nominación debe ser inferior a la fecha de inicio del salón de clases. ';
$string['nomination_error'] ='La fecha de finalización de la nominación debe ser mayor que la fecha de inicio de la nominación. ';
$string['cs_timestart'] ='Fecha de inicio de la sesión ';
$string['showentries'] ='Ver respuesta ';
$string['cs_timefinish'] ='Fecha de finalización de la sesión ';
$string['select_room'] ='--Seleccione HABITACIÓN-- ';
$string['select_costcenter'] ='--Seleccione Organización ...';
$string['select_department'] ='Todos los Clientes';
$string['classroom_active_action'] ='¿Estás seguro de que quieres publicar esto?<b>{$a}</b>" ¿aula?';
$string['classroom_release_hold_action'] ='¿Estás seguro de que quieres publicar este "<b>{$a}</b>" ¿aula?';
$string['classroom_hold_action'] ='¿Estás seguro de que quieres mantener este "<b>{$a}</b>" ¿aula?';
$string['classroom_close_action'] ='¿Estás seguro de que quieres cancelar este "<b>{$a}</b>" ¿aula?';
$string['classroom_cancel_action'] ='¿Estás seguro de que quieres cancelar este "<b>{$a}</b>" ¿aula?';
$string['classroom_complete_action'] ='¿Estás seguro de que quieres completar este "<b>{$a}</b>" ¿aula?';
$string['classroom_activate'] ='¿Estás seguro de que quieres publicar esto?<b>{$a}</b>" ¿aula?';
$string['classroom'] ='Aula';
$string['learningplan'] ='Plan de aprendizaje ';
$string['certificate'] ='Certificado';
$string['completed'] ='Completado';
$string['pending'] ='Pendiente';
$string['attendace'] ='Marcar asistencia ';
$string['attended_sessions'] ='Sesiones asistidas ';
$string['attended_sessions_users'] ='Usuarios atendidos';
$string['attended_hours'] ='Horas atendidas ';
$string['supervisor'] ='Informar a';
$string['employee'] ='Nombre de empleado';
$string['room'] ='Habitación';
$string['status'] ='Estado';
$string['trainer'] ='Capacitador';
$string['faculty'] ='Capacitador';
$string['addcourse'] ='Agregar cursos ';
$string['selfenrol'] ='Self Enroll ';
$string['classroom:createclassroom'] ='Crear aula ';
$string['classroom:viewclassroom'] ='Ver Classroom ';
$string['classroom:editclassroom'] ='Editar Classroom ';
$string['classroom:deleteclassroom'] ='Eliminar Classroom ';
$string['classroom:manageclassroom'] ='Administrar Classroom ';
$string['classroom:createsession'] ='Crear sesión ';
$string['classroom:viewsession'] ='Ver sesión ';
$string['classroom:editsession'] ='Editar sesión ';
$string['classroom:deletesession'] ='Eliminar sesión ';
$string['classroom:managesession'] ='Administrar sesión ';
$string['classroom:assigntrainer'] ='Asignar capacitador ';
$string['classroom:managetrainer'] ='Administrar capacitador ';
$string['classroom:addusers'] ='Agregar usuarios';
$string['classroom:removeusers'] ='Eliminar usuarios';
$string['classroom:manageusers'] ='Administrar usuarios';
$string['classroom:cancel'] ='Cancelar Classroom ';
$string['classroom:classroomcompletion'] ='Configuración de finalización del aula ';
$string['classroom:complete'] ='Aula completa ';
$string['classroom:createcourse'] ='Asignar curso presencial ';
$string['classroom:createfeedback'] ='Asignar comentarios en el aula ';
$string['classroom:deletecourse'] ='Anular la asignación del curso presencial ';
$string['classroom:deletefeedback'] ='Anular la asignación de comentarios en el aula ';
$string['classroom:editcourse'] ='Editar curso de Classroom ';
$string['classroom:editfeedback'] ='Editar comentarios de Classroom ';
$string['classroom:hold'] ='Mantenga Classroom ';
$string['classroom:managecourse'] ='Administrar curso en el aula ';
$string['classroom:managefeedback'] ='Administrar los comentarios de Classroom ';
$string['classroom:publish'] ='Publicar Classroom ';
$string['classroom:release_hold'] ='Liberar Hold Classroom ';
$string['classroom:takemultisessionattendance'] ='Asistencia a clases multisesión ';
$string['classroom:manage_owndepartments'] ='Administrar las aulas propias del cliente.';
$string['classroom:manage_multiorganizations'] ='Administrar aulas de organizaciones múltiples';
$string['classroom:takesessionattendance'] ='Asistencia a la sesión en el aula ';
$string['classroom:trainer_viewclassroom'] ='Capacitador View Classroom ';
$string['classroom:viewcourse'] ='Ver curso presencial ';
$string['classroom:viewfeedback'] ='Ver comentarios de Classroom ';
$string['classroom:viewusers'] ='Ver usuarios de Classroom';
$string['classroom:view_activeclassroomtab'] ='Ver la pestaña Aulas activas ';
$string['classroom:view_allclassroomtab'] ='Ver pestaña Todas las aulas ';
$string['classroom:view_cancelledclassroomtab'] ='Ver la pestaña Aulas canceladas ';
$string['classroom:view_completedclassroomtab'] ='Ver pestaña Aulas completadas ';
$string['classroom:view_holdclassroomtab'] ='Ver pestaña Hold Classrooms ';
$string['classroom:view_newclassroomtab'] ='Ver nueva pestaña de aulas ';
$string['institute_name'] ='Nombre del lugar';
$string['building'] ='Nombre del edificio';
$string['roomname'] ='Nombre de la habitación';
$string['address'] ='Dirección';
$string['capacity'] ='Capacidad';
$string['capacity_help'] ='Capacity help ';
$string['onlinesession'] ='Sesión en línea ';
$string['onlinesession_help'] ='Si marca esta opción y se envía la sesión en línea, se creará. ';
$string['addasession'] ='Agregar una sesión más ';
$string['addsession'] ='<i class="fa fa-graduation-cap popupstringicon" aria-hidden="true"></i> Crear una sesión <div class="popupstring"></div>';
$string['session_dates'] ='Fechas de la sesión ';
$string['attendance_status'] ='Estado de asistencia ';
$string['sessiondatetime'] ='Fecha y hora de la sesión ';
$string['session_details'] ='Detalles de la sesión ';
$string['cs_capacity_number'] ='La capacidad debe ser numérica y positiva ';
$string['select_cr_room'] ='Seleccione una sala ';
$string['noclassrooms'] ='Aulas no disponibles ';
$string['nosessions'] ='Sesiones no disponibles ';
$string['noclassroomusers'] ='Usuarios de Classroom no disponibles ';
$string['noclassroomcourses'] ='Cursos no asignados ';
$string['select_all'] ='Seleccionar todo';
$string['remove_all'] ='Un Seleccionar ';
$string['not_enrolled_users'] ='<b>Usuarios no inscritos ({$a})</b>';
$string['enrolled_users'] ='<b> Usuarios registrados ({$a})</b>';
$string['remove_selected_users'] ='<b> Anular la inscripción de usuarios </b><i class="fa fa-arrow-right" aria-hidden="true"></i><i class="fa fa-arrow-right" aria-hidden="true"></i>';
$string['remove_all_users'] ='<b> Anular la inscripción de todos los usuarios </b><i class="fa fa-arrow-right" aria-hidden="true"></i><i class="fa fa-arrow-right" aria-hidden="true"></i>';
$string['add_selected_users'] ='<i class="fa fa-arrow-left" aria-hidden="true"></i><i class="fa fa-arrow-left" aria-hidden="true"></i><b> Inscribir usuarios</b>';
$string['add_all_users'] =' </div><i class="fa fa-arrow-left" aria-hidden="true"></i><i class="fa fa-arrow-left" aria-hidden="true"></i> </div><b> Inscribir a todos los usuarios </b>';
$string['addusers'] ='Agregar usuarios';
$string['addusers_help'] ='Agregar usuarios Ayuda ';
$string['potusers'] ='Usuarios potenciales';
$string['potusersmatching'] ='Usuarios potenciales que coinciden con \'{$a}\'';
$string['extusers'] ='Usuarios existentes';
$string['extusersmatching'] ='Usuarios existentes que coinciden con \'{$a}\'';
$string['noclassroomevaluations'] ='¡Comentarios del salón de clases no disponibles! ';
$string['training_feeddback'] ='Comentarios sobre la formación ';
$string['trainer_feedback'] ='Comentarios del capacitador ';
$string['allclasses'] ='Todas';
$string['newclasses'] ='Nuevo';
$string['activeclasses'] ='Activos';
$string['holdclasses'] ='Sostener';
$string['completedclasses'] ='Completado';
$string['cancelledclasses'] ='Cancelado';
$string['sessions'] ='Sesiones ';
$string['courses'] ='Cursos ';
$string['users'] ='Usuarios';
$string['activate'] ='Activar';
$string['classroomstatusmsg'] ='¿Estás seguro de que quieres activar el aula?';
$string['viewclassroom_assign_users']='Asignar usuarios';
$string['assignusers']="Assign Users";
$string['continue']='Seguir';
$string['assignusers']="Assign Users";
$string['assignusers_heading']='Inscribir usuarios en el aula <b>\'{$a}\'</b>';
$string['session_attendance_heading']='Asistencia al aula <b>\'{$a}\'</b>';
$string['online_session_type']='Tipo de sesión en línea ';
$string['online_session_type_desc']="online session type for online sessions on Classroom.";
$string['online_session_plugin_info']='No se encontraron complementos de tipo de sesión en línea. ';
$string['select_session_type']='Seleccione el tipo de sesión ';
$string['join']='Unirse';
$string['view_classroom'] ='ver el aula ';
$string['addcourses'] ='<i class="fa fa-graduation-cap popupstringicon" aria-hidden="true"></i> Asignar curso <div class="popupstring"></div>';
$string['completion_status'] ='Estado de finalización';
$string['completion_status_per'] ='Estado de finalización (%)';
$string['type'] ='Tipo';
$string['trainer'] ='Capacitador';
$string['submitted'] ='Presentada';
$string['classroom_self_enrolment'] ='<div class="pl-15 pr-15 pb-15">¿Está seguro de que desea inscribir este "<b>{$a}</b>" ¿aula?';
$string['classroom_enrolrequest_enrolment'] ='<div class="pl-15 pr-15 pb-15">¿Está seguro de que desea solicitar la inscripción?<b>{$a}</b>" ¿aula?';
$string['alert_capacity_check'] = "<div class='alert alert-danger text-center'> Todos los asientos están ocupados.</div>";
$string['updatesession'] ='<i class="fa fa-graduation-cap popupstringicon" aria-hidden="true"></i> Actualizar sesión <div class="popupstring"></div>';
$string['addnewsession'] ='Agregar una nueva sesión ';
$string['createinstitute'] ='Crear ubicación ';
$string['employeeid'] ='ID de empleado';
$string['classroom_info'] ='Información del aula';
$string['classroom_info'] ='Información del aula';
$string['sessionstartdateerror1'] ='La fecha de inicio de la sesión debe ser mayor que la fecha de inicio del aula. ';
$string['sessionstartdateerror2'] ='La fecha de inicio de la sesión debe ser inferior a la fecha de finalización de Classroom. ';
$string['sessionenddateerror1'] ='La fecha de finalización de la sesión debe ser superior a la fecha de inicio de Classroom. ';
$string['sessionenddateerror2'] ='La fecha de finalización de la sesión debe ser inferior a la fecha de finalización de Classroom. ';
$string['confirmation'] ='Confirmación';
$string['unassign'] ='Desasignar ';
$string['roomid'] ='Enumere las habitaciones del aula. Si encuentra habitaciones vacías, asigne una ubicación para el aula';
$string['roomid_help'] ='Enumere las habitaciones del aula';
$string['classroomcompletion'] ='Criterios de finalización del aula ';
$string['classroom_anysessioncompletion'] ='El aula está completo cuando CUALQUIERA de las siguientes sesiones está completa ';
$string['classroom_allsessionscompletion'] ='El aula está completo cuando TODAS las sesiones están completas ';
$string['classroom_anycoursecompletion'] ='El aula está completo cuando CUALQUIERA de los cursos siguientes está completo ';
$string['classroom_allcoursescompletion'] ='El aula está completo cuando TODOS los cursos están completos ';
$string['classroom_completion_settings'] ='Configuración de finalización del aula ';
$string['sessiontracking'] ='Requisitos de las sesiones ';
$string['session_completion'] ='Seleccionar sesión ';
$string['coursetracking'] ='Requisitos de los cursos ';
$string['course_completion'] ='Seleccione Finalizaciones de curso ';
$string['classroom_donotsessioncompletion'] ='No indique sesiones finalizadas en el aula ';
$string['classroom_donotcoursecompletion'] ='No indique cursos finalizados en el aula ';
$string['select_courses']='Seleccionar cursos ';
$string['select_sessions']='Seleccionar sesiones ';
$string['eventclassroomcreated'] ='Aula local creada ';
$string['eventclassroomupdated'] ='Aula local actualizada ';
$string['eventclassroomcancel'] ='Aula local cancelada ';
$string['eventclassroomcompleted'] ='Aula local completada ';
$string['eventclassroomcompletions_settings_created'] ='Se agregaron configuraciones de finalización del aula local. ';
$string['eventclassroomcompletions_settings_updated'] ='Se actualizó la configuración de finalización del aula local ';
$string['eventclassroomcourses_created'] ='Se agregó el curso en el aula local ';
$string['eventclassroomcourses_deleted'] ='Se eliminó el curso del aula local ';
$string['eventclassroomcourses_deleted'] ='Se eliminó el curso del aula local ';
$string['eventclassroomdeleted'] ='Aula local eliminada ';
$string['eventclassroomhold'] ='Aula local retenida ';
$string['eventclassroompublish'] ='Aula local publicada ';
$string['eventclassroomsessions_created'] ='Se crearon sesiones en el aula local ';
$string['eventclassroomsessions_deleted'] ='Se eliminaron las sesiones del aula local ';
$string['eventclassroomsessions_updated'] ='Se actualizaron las sesiones del aula local ';
$string['eventclassroomusers_created'] ='Usuarios del aula local inscritos ';
$string['eventclassroomusers_deleted'] ='Usuarios del aula local no inscritos ';
$string['eventclassroomusers_updated'] ='Usuarios del aula local actualizados ';
$string['eventclassroomfeedbacks_created'] ='Se crearon comentarios del aula local ';
$string['eventclassroomfeedbacks_updated'] ='Comentarios de los salones de clase locales actualizados ';
$string['eventclassroomfeedbacks_deleted'] ='Comentarios de las aulas locales eliminados ';
$string['eventclassroomattendance_created_updated'] ='Asistencia a las sesiones del aula local presente / ausente ';
$string['publish'] ='Registro automático ';
$string['release_hold'] ='Suelte Hold ';
$string['cancel'] ='Cancelar';
$string['hold'] ='Sostener';
$string['mark_complete'] ='Marcar como completo ';
$string['enroll'] ='Inscribirse';
$string['valnamerequired'] ='Falta el nombre del aula ';
$string['numeric'] ='Solo valores numéricos ';
$string['positive_numeric'] ='Solo valores numéricos positivos ';
$string['capacity_enroll_check'] ='La capacidad debe ser mayor que los asientos asignados. ';
$string['vallocationrequired'] ='Seleccione la ubicación en el tipo de ubicación seleccionado. ';
$string['vallocation'] ='Seleccione solo la ubicación en el tipo de ubicación seleccionado. ';
$string['new_classroom'] ='Nuevo';
$string['active_classroom'] ='Activos';
$string['cancel_classroom'] ='Cancelado';
$string['hold_classroom'] ='Sostener';
$string['completed_classroom'] ='Completado';
$string['completed_user_classroom'] ='No has completado este aula ';
$string['classroomlogo'] ='Imagen de banner ';
$string['bannerimage_help'] ='Busque y seleccione una imagen de banner para la capacitación en el aula ';
$string['completion_settings_tab'] ='Criterios de finalización ';
$string['target_audience_tab'] ='Público objetivo';
$string['requested_users_tab'] ='Usuarios solicitados';
$string['waitinglist_users_tab'] ='Usuarios en lista de espera';
$string['classroom_completion_tab_info'] ='No se encontraron criterios de aula.';
$string['classroom_completion_tab_info_allsessions'] ='Este salón de clases se completará cuando se indique a continuación <b> todas las sesiones </b> debe ser completado. ';
$string['classroom_completion_tab_info_anysessions'] ='Este salón de clases se completará cuando se indique a continuación <b> cualquier sesión </b> debe ser completado. ';
$string['classroom_completion_tab_info_allsessions_allcourses'] =  'This classroom will completed when the below listed </div><b>all courses </div></b> and </div><b> all sessions </div></b> should be completed.';
$string['classroom_completion_tab_info_allsessions_anycourses'] =  'This classroom will completed when the below listed </div><b>any courses </div></b> and </div><b> all sessions </div></b> should be completed.';
$string['classroom_completion_tab_info_anysessions_allcourses'] =  'This classroom will completed when the below listed </div><b>all courses </div></b> and </div><b> any sessions </div></b> should be completed.';
$string['classroom_completion_tab_info_anysessions_anycourses'] =  'This classroom will completed when the below listed </div><b>any courses </div></b> and </div><b> any sessions </div></b> should be completed.';
$string['classroom_completion_tab_info_allcourses'] ='Este salón de clases se completará cuando se indique a continuación <b> todos los cursos </b> debe ser completado. ';
$string['classroom_completion_tab_info_anycourses'] ='Este salón de clases se completará cuando se indique a continuación <b> cualquier curso </b> debe ser completado. ';
$string['audience_department'] ='<p>Este salón de clases será elegible para el público objetivo que figura a continuación.</p><p></p><p> </p><b>Clientes:</b> {$a}<p></p>';
$string['audience_group'] ='<p> </p><b>Grupos:</b> {$a}<p></p>';
$string['audience_hrmsrole'] ='<p> </p><b>Roles de HRMS:</b> {$a}<p></p>';
$string['audience_designation'] ='<p> </p><b>Designaciones:</b> {$a}<p></p>';
$string['audience_location'] ='<p> </p><b>Ubicaciones:</b> {$a}<p></p>';
$string['no_trainer_assigned'] ='No hay capacitadores asignados ';
$string['requestforenroll'] ='Solicitud';
$string['requestavail'] ='Usuarios solicitados no disponibles ';
$string['nocoursedesc'] ='No se proporcionó una descripción ';
$string['enrolluserssuccess'] ='<b>{$a->changecount}</b> Empleados inscritos con éxito en este <b>"{$a->classroom}"</b> aula .';
$string['unenrolluserssuccess'] ='<b>{$a->changecount}</b> Empleado (s) se anuló con éxito de este <b>"{$a->classroom}"</b> aula .';
$string['enrollusers'] ='Aula <b>"{$a}"</b> la inscripción está en proceso ...';
$string['un_enrollusers'] ='Aula <b>"{$a}"</b> Un registro está en proceso ...';
$string['click_continue'] ='Haga clic en continuar ';
$string['manage_br_classrooms'] ='Administrar <br> aulas ';
$string['noclassroomsavailiable'] ='No hay aulas disponibles ';
$string['employeerolestring'] ='Empleado';
$string['trainerrolestring'] ='Capacitador';
$string['taskclassroomreminder'] ='Recordatorio de clase ';
$string['unenrollclassroom'] ='¿Está seguro de que desea cancelar la inscripción de este "<b>{$a}</b>" ¿aula?';
$string['unenroll'] ='Un Enroll ';
$string['eventclassroomusers_waitingcreated'] ='Se agregó la lista de espera de usuarios del aula local ';
$string['sortorder'] ='Esperando orden ';
$string['enroltype'] ='Tipo';
$string['waitingtime'] ='Fecha y hora';
$string['allow_waitinglistusers'] ='Permitir lista de espera de usuarios ';
$string['allowuserswaitinglist_help'] ='Permitir que los usuarios se unan a la lista de espera después de que la capacidad para la capacitación en el aula esté completa. ';
$string['classroom:viewwaitinglist_userstab'] ='Permitir lista de espera de usuarios ';
$string['classroomwaitlistinfo'] ='<div class="p-2 text-center"></div><b>Esta "{$a->classroom}"el aula está actualmente reservada</b>. <br><br>Gracias por su solicitud de aplicación. Se le coloca en lista de espera con orden "{$a->classroomwaitinglistno}"y se le informará por correo electrónico en caso de que se inscriba en el aula cuando esté disponible.';
$string['otherclassroomwaitlistinfo'] ='<div class="p-2 text-center"></div><b>Esta "{$a->classroom}"el aula está actualmente reservada</b>. <br><br>Gracias por su solicitud de aplicación.<b>"{$a->username}"</b> se coloca en lista de espera con pedido "{$a->classroomwaitinglistno}"y se le informará por correo electrónico en caso de que el usuario se registre en el aula cuando esté disponible.';
$string['capacity_waiting_check'] ='Se requiere capacidad para permitir una lista de espera de usuarios ';
$string['submit'] ='Enviar';
$string['capacity_check'] ='verificación_capacidad ';
$string['allowuserswaitinglist'] ='allowuserswaitinglist ';
$string['traning'] ='capacitación ';
$string['classroom_locationtype'] ='class_locationtype ';
$string['bannerimage'] ='bannerimage ';
$string['messageprovider:classroomenrolment'] ='Inscripción en el aula ';
$string['classroomenrolmentsub'] ='Inscripción en el aula ';
$string['classroomenrolment'] ='<p>Te han inscrito en el aula "</p>{$a->name}"!<p></p><p>Puede ver más información sobre "</p>{$a->classroomurl}" página.<p></p>';
$string['tagarea_classroom'] ='Aula';
$string['enrolled'] ='Inscrito ';
$string['deleted_classroom'] ='Aula eliminada ';
$string['points'] ='Puntos';
$string['open_pointsclassroom'] ='puntos';
$string['open_pointsclassroom_help'] ='Puntos por defecto de Classroom (0) ';
$string['enrolusers'] ='Inscribir usuarios';
$string['enableplugin'] ='Actualmente, el método de inscripción en Classroom está inhabilitado.<a href="{$a} "target =" _ blank "> <u>haga clic aquí</u></a> para habilitar el método de inscripción';
$string['manageplugincapability'] ='Actualmente, el método de inscripción en Classroom está inhabilitado. Por favor contacte al administrador del sitio.';
$string['attendance'] ='Asistencia';
$string['add_certificate'] ='Agregar certificado ';
$string['add_certificate_help'] ='Si desea emitir un certificado cuando el usuario complete este aula, habilite aquí y seleccione la plantilla en el siguiente campo (Plantilla de certificado)';
$string['select_certificate'] ='Seleccionar certificado ';
$string['certificate_template'] ='Plantilla de certificado ';
$string['certificate_template_help'] ='Seleccione la plantilla de certificado para esta clase ';
$string['err_certificate'] ='Falta la plantilla de certificado ';
$string['eventclassroomusercompleted'] ='Aula completada por el usuario ';
$string['downloadcertificate'] ='Certificado';
$string['download_certificate'] ='Descargar certificado ';
$string['unableto_download_msg'] = "Still you didn't complete this classroom so you cannot download the certificate";
$string['pluginname'] ='Aulas ';
$string['messageprovider:classroom_cancel'] ='Classroom_cancel ';
$string['messageprovider:classroom_complete'] ='Classroom_complete ';
$string['messageprovider:classroom_enrol'] ='Classroom_enrol ';
$string['messageprovider:classroom_enrolwaiting'] ='Classroom_enrolwaiting ';
$string['messageprovider:classroom_hold'] ='Classroom_hold ';
$string['messageprovider:classroom_invitation'] ='Classroom_invitation ';
$string['messageprovider:classroom_reminder'] ='Classroom_reminder ';
$string['messageprovider:classroom_unenroll'] ='Classroom_unenroll ';
$string['notassigned'] ='N / A';
$string['inprogress_classroom'] ='Aula en progreso ';
$string['completed_classroom'] ='Aula completa ';
$string['classroomname'] ='Nombre del aula ';
$string['savecontinue'] ='Guardar Continuar';
$string['assign'] ='Asignar';
$string['save'] ='Salvar';
$string['previous'] ='Anterior';
$string['skip'] ='Omitir';
$string['cancel'] ='Cancelar';
$string['requestprocessing'] ='Solicitud de procesamiento ... ';
$string['information'] ='Información ';
$string['remove_all'] ='Eliminar todo';
$string['remove_selected_users'] ='Eliminar usuarios seleccionados';
$string['add_selected_users'] ='Agregar usuarios seleccionados';
$string['scheduled_date'] ='Cita agendada';
$string['code'] ='Código';
$string['enrolledusers'] ='Usuarios inscritos';
$string['seats_allocation'] ='Asignación de asientos ';
$string['edit_course'] ='Editar curso ';
$string['user_enrollments'] ='Inscripciones de usuarios ';
$string['classroom_completion'] ='Finalización del aula ';
$string['users_completions'] ='Finalizaciones de usuarios ';
$string['user_waiting_list'] ='Lista de espera de usuarios ';
$string['scheduled'] ='Programado ';
$string['classroom_code'] ='Código de clase ';
$string['employee_location'] ='Ubicación del empleado ';
$string['total_seats'] ='Asientos totales ';
$string['whats_next'] ='¿Que sigue?';
$string['do_you_want_create_session'] ='Quieres <b>Crear sesión</b>';
$string['do_you_want_add_course'] ='Quieres <b>Agregar curso</b>';
$string['departments'] ='Clientes ';
$string['sub_departments'] ='LOB ';
$string['designations'] ='Designaciones ';
$string['groups'] ='Grupos ';
$string['hrms_roles'] ='Roles de HRMS ';
$string['locations'] ='Ubicaciones ';
$string['name'] ='Nombre';
$string['session_timings'] ='Tiempos de sesión ';
$string['duration'] ='Duración';
$string['trainersoccupiedrequired'] ='Capacitadores {$a} ya agregado a otra aula en esta duración';
$string['search'] = 'Buscar';
$string['create_session'] = 'Crear una sesión';
$string['location'] = 'Ubicación';

