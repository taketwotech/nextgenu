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
 * Strings for component 'theme_remui', language 'en', branch 'MOODLE_3_STABLE'
 *
 * @package   theme_remui
 * @copyright Copyright (c) 2016 WisdmLabs. (http://www.wisdmlabs.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Edwiser RemUI';
$string['region-side-post'] = 'Derecha';
$string['region-side-pre'] = 'Izquierda';
$string['fullscreen'] = 'Pantalla Completa';
$string['closefullscreen'] = 'Cerrar Pantalla Completa';
$string['licensesettings'] = 'Configuracion de licencia';
$string['edwiserremuilicenseactivation'] = 'Edwiser RemUI Activacion de Licencia';
$string['overview'] = 'Revision';
$string['choosereadme'] = '
<div class="about-remui-wrapper text-center">
    <div class="about-remui m-auto" style="max-width: 1000px;">
        <h1 class="text-center">Bienvenido a Edwiser RemUI</h1><br>
        <h4 class="text-muted">
        Edwiser RemUI es la nueva revolucion en experiencia de Moodle. Ha sido disenado para mejorar el aprendizaje con
		disenos personalizados y navegacion simplificada.<br><br>
        Estamos seguros que te gustara el look remodelado!
        </h4>
        <div class="text-center mt-50">
        <img src="' . $CFG->wwwroot . '/theme/remui/pix/screenshot.jpg" alt="Edwiser RemUI screen shot" style="max-width: 100%;"/>
        </div>
        <br><br>
        <div class="text-center">
            <div class="btn-group text-center" role="group" aria-label="...">
              <div class="btn-group mr-1" role="group">
                <a href="https://edwiser.org/remui/#compatible-moodle-version" target="_blank" class="btn btn-primary btn-round">FAQ</a>&nbsp;
              </div>
              <div class="btn-group mr-1" role="group">
                <a href="https://edwiser.org/remui/documentation/" target="_blank" class="btn btn-primary btn-round">Documentation</a>&nbsp;
              </div>
              <div class="btn-group" role="group">
                <a href="https://edwiser.org/contact-us/" target="_blank" class="btn btn-primary btn-round">Support</a>
              </div>
            </div>
        </div>
        <br>
        <h1 class="text-center">Personalice Su Tema</h1>
        <h4 class="text-muted text-center">
			Sabemos que ningun LMS es igual. Trabajaremos con usted para comprender sus necesidades, y disenar y desarrollar una solucion que satisfaga sus objetivos.
        </h4>
        <br><br>
        <div class="row wdm_generalbox">
            <div class="marketing-spots span3 col-lg-6 col-12">
                <div class="iconsquare">
                    <i class="fa fa-cogs"></i>
                </div>
                <div class="iconbox-content">
                    <h4>Personalizacion del Tema</h4>
                </div>
            </div>
            <div class="marketing-spots span3 col-lg-6 col-12">
                <div class="iconsquare">
                    <i class="fa fa-edit"></i>
                </div>
                <div class="iconbox-content">
                    <h4>Diseno de Funcionalidad</h4>
                </div>
            </div>
            <br>
            <div class="marketing-spots span3 col-lg-6 col-12">
                <div class="iconsquare">
                    <i class="fa fa-link"></i>
                </div>
                <div class="iconbox-content">
                    <h4>API Integracion</h4>
                </div>
            </div>
            <div class="marketing-spots span3 col-lg-6 col-12">
                <div class="iconsquare">
                    <i class="fa fa-life-ring"></i>
                </div>
                <div class="iconbox-content">
                    <h4>LMS Consultoria</h4>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="text-center">
            <a class="btn btn-primary btn-lg" target="_blank" href="https://edwiser.org/contact-us/">Contactarnos</a>&nbsp;&nbsp;
        </div>
    </div>
</div>
<br />';

$string['licensenotactive'] = '<strong>Alert!</strong> Su licencia no esta activa, por favor <strong>activela</strong> en RemUI configuraciones.';
$string['licensenotactiveadmin'] = '<strong>Alerta!</strong> Licencia no activada , por favor <strong>active</strong> la licencia <a href="'.$CFG->wwwroot.'/admin/settings.php?section=themesettingremui#remuilicensestatus" > here</a>.';
$string['activatelicense'] = 'Activar Licencia';
$string['deactivatelicense'] = 'Desactivar Licencia';
$string['renewlicense'] = 'Renovar Licencia';
$string['active'] = 'Activa';
$string['notactive'] = 'No Activa';
$string['expired'] = 'Vencida';
$string['licensekey'] = 'Clave de Licencia';
$string['licensestatus'] = 'Estatus de Licencia';
$string['noresponsereceived'] = 'No se pudo contactar al servidor. Por favor intente mas tarde.';
$string['licensekeydeactivated'] = 'Codigo de Licencia desactivado.';
$string['siteinactive'] = 'Sitio inactivo (Presione activar licencia).';
$string['entervalidlicensekey'] = 'Por favor ingrese un codigo de licencia valido.';
$string['licensekeyisdisabled'] = 'Su codigo de licencia esta desactivado.';
$string['licensekeyhasexpired'] = "Su licencia ha expirado. Por favor renovelo.";
$string['licensekeyactivated'] = "Su licencia ha sido activada.";
$string['enterlicensekey'] = "Por favor escriba un codigo de licencia valido.";

$string['activitynextpreviousbutton'] = 'Habilitar el botón de actividad Siguiente / Anterior';
$string['activitynextpreviousbuttondesc'] = 'El botón de actividad siguiente / anterior aparece en la parte superior de la actividad para un cambio rápido';
$string['disablenextprevious'] = 'Inhabilitar';
$string['enablenextprevious'] = 'Habilitar';
$string['enablenextpreviouswithname'] = 'Activar con el nombre de la actividad';

// course
$string['nosummary'] = 'A esta seccion del curso no se le ha agregado un programa o resumen.';
$string['defaultimg'] = 'Imagen por defecto 100 x 100.';
$string['choosecategory'] = 'Elegir Categoria';
$string['allcategory'] = 'Todas las Categorias';
$string['viewcours'] = 'Ver el Curso';
$string['taught-by'] = 'enseñado por';
$string['enroluser'] = 'Inscribir Usuarios';
$string['graderreport'] = 'Informe de graduador';
$string['activityeport'] = 'Informe de actividades';
$string['editcourse'] = 'Editar curso';

// dashboard element -> overview
$string['enabledashboardelements'] = 'Activar Elementos de la Barra de Tareas';
$string['enabledashboardelementsdesc'] = 'Deseleccione para desactivar los widgets de Edwiser RemUI en la Barra de Tareas.';
$string['totaldiskusage'] = 'Uso total del disco';
$string['activemembers'] = 'Usuarios Activos';
$string['newmembers'] = 'Nuevos Usuarios';
$string['coursesdiskusage'] = 'Uso del disco por cursos';
$string['activestudents'] = 'Estudiantes activos';

// Quick meesage
$string['quickmessage'] = 'Mensaje Rapido';
$string['entermessage'] = 'Por favor, escriba algun mensaje!';
$string['selectcontact'] = 'Por favor, seleccione un contacto!';
$string['selectacontact'] = 'Seleccionar Contacto';
$string['sendmessage'] = 'Enviar Mensaje';
$string['yourcontactlisistempty'] = 'Su lista de contactos esta vacia!';
$string['viewallmessages'] = 'Ver todos los mensajes';
$string['messagesent'] = 'Enviado exitosamente!';
$string['messagenotsent'] = 'Mensaje No Enviado, asegurese que ingreso la informacion correcta.';
$string['messagenotsenterror'] = 'Mensaje no enviado! Algo salio mal.';
$string['sendingmessage'] = 'Enviando mensaje...';
$string['sendmoremessage'] = 'Enviar mas mensajes';

// General Seetings.
$string['generalsettings' ] = 'Configuracion General';
$string['navsettings'] = 'Configuracion de Navegacion';
$string['homepagesettings'] = 'Configuracion de Pagina Principal';
$string['colorsettings'] = 'Configurar Colores';
$string['fontsettings' ] = 'Configurar Fuentes';
$string['slidersettings'] = 'Configurar Slider';
$string['configtitle'] = 'Edwiser RemUI';

// Font settings.
$string['fontselect'] = 'Selector del Tipo de Fuente';
$string['fontselectdesc'] = 'Selecciones fuentes estandar o fuentes Google. Por favor guarde los cambios antes de mostrar su seleccion.';
$string['fonttypestandard'] = 'Fuente Estandard';
$string['fonttypegoogle'] = 'Fuente Google';
$string['fontnameheading'] = 'Fuente de Titulo';
$string['fontnameheadingdesc'] = 'Ingrese el nombre exacto de la fuente para el Titulo.';
$string['fontnamebody'] = 'Fuente de Texto';
$string['fontnamebodydesc'] = 'Entre el nombre exacto de la fuente para usar en texto normal.';

/* Dashboard Settings*/
$string['dashboardsetting'] = 'Configuracion de la Barra de Tareas';
$string['themecolor'] = 'Color del Tema';
$string['themecolordesc'] = 'Cual es el color que desea para su tema?.  Este cambio afectara multiples componentes de su sitio Moodle';
$string['themetextcolor'] = 'Color de Texto';
$string['themetextcolordesc'] = 'Establecer color del Texto.';
$string['layout'] = 'Elegir Diseño';
$string['layoutdesc'] = 'Elija Diseño Fijo (el menu se fijara en la parte superior) o Diseño por Defecto.'; // Boxed Layout or
$string['defaultlayout'] = 'Por Defecto';
$string['fixedlayout'] = 'Cabecera Fija';
$string['defaultboxed'] = 'Boxed';
$string['layoutimage'] = 'Boxed Layout Imagen de Fondo';
$string['layoutimagedesc'] = 'Cargar imagen de fondo para aplicar al Boxed Layout.';
$string['sidebar'] = 'Select sidebar';
$string['sidebardesc'] = 'Select sidebar style (Old / New)';
$string['rightsidebarslide'] = 'Cambiar a Barra Lateral Derecha';
$string['rightsidebarslidedesc'] = 'Cambiar Barra Lateral Derecha por Defecto.';
$string['leftsidebarslide'] = 'Cambiar a Barra Lateral Izquierda';
$string['leftsidebarslidedesc'] = 'Cambiar la Barra Lateral Izquierda por defecto.';
$string['leftsidebarmini'] = 'Enable Left Sidebar-mini';
$string['leftsidebarminidesc'] = 'Enable the left sidebar-mini.';
$string['rightsidebarskin'] = 'Cambiar textura de la Barra Lateral Derecha';
$string['rightsidebarskindesc'] = 'Cambiar la Textura de la Barra Lateral Derecha.';

/*color*/
$string['colorscheme'] = 'Escoja un esquema de colores';
$string['colorschemedesc'] = 'Usted puede escoger un esquema de los siguientes - Azul, Negro, Purpura, Verde, Amarillo, Azul-Claro, Negro-Claro, Purpura-Claro, Verde-Claro & Amarillo-Claro. <br /> <b>Claro</b> - pone un color degradado a la izquierda de la barra lateral.';
$string['blue'] = 'Azul';
$string['white'] = 'Blanco';
$string['purple'] = 'Purpura';
$string['green'] = 'Verde';
$string['red'] = 'Rojo';
$string['yellow'] = 'Amarillo';
$string['bluelight'] = 'Azul Claro';
$string['whitelight'] = 'Blanco';
$string['purplelight'] = 'Purpura Claro';
$string['greenlight'] = 'Verde Claro';
$string['redlight'] = 'Rojo Claro';
$string['yellowlight'] = 'Amarillo Claro';
$string['custom'] = 'Oscuro personalizado';
$string['customlight'] = 'Luz personalizada';
$string['customskin_color'] = 'Color de piel';
$string['customskin_color_desc'] = 'Usted puede elegir el color de encargo para su tema aquí.';

/* Course setting*/
$string['courseperpage'] = 'Cursos por Pagina';
$string['courseperpagedesc'] = 'Numero de cursos a mostrar por pagina en la pagina de archivo de cursos.';
$string['enableimgsinglecourse'] = 'Enable image on single course page';
$string['enableimgsinglecoursedesc'] = 'Uncheck to disable formating of image on single course page.';
$string['nocoursefound'] = 'No se encontraron cursos';

/*logo*/
$string['logo'] = 'Logo';
$string['logodesc'] = 'Usted puede agregar el logo que se mostrara en la cabecera. Nota - El alto preferido es 50px. Si desea personalizarlo, Puede hacerlo desde la caja CSS.';
$string['logomini'] = 'LogoMini';
$string['logominidesc'] = 'You may add the logomini to be displayed on the header when sidebar is collapsed. Note- Preferred height is 50px. In case you wish to customise, you can do so from the custom CSS box.';
$string['siteicon'] = 'Icono del sitio';
$string['siteicondesc'] = 'Si no tiene un globo puede elegir uno de <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">list</a>. <br /> Solo ingrese el texto que aparece despues de "fa-".';
$string['logoorsitename'] = 'Elegir el formato del logo';
$string['logoorsitenamedesc'] = 'Usted puede cambiar el logo del sitio las veces que quiera. <br />Las opciones disponibles son: Logo - Sólo se mostrará el logo; <br /> Sitename - Sólo se mostrará el nombre del sitio; <br /> Icon+sitename - Se mostrará el icono y el nombre del sitio.';
$string['onlylogo'] = 'Únicamente logo';
$string['onlysitename'] = 'Sólo nombre del sitio';
$string['iconsitename'] = 'Icono y nombre del sitio';

/*favicon*/
$string['favicon'] = 'Favicon';
$string['favicondesc'] = 'El icono favorito de su sitio. Inserte aquí el icono de su sitio.';
$string['enablehomedesc'] = 'Enable Home Desc';

/*custom css*/
$string['customcss'] = 'Personalizar CSS';
$string['customcssdesc'] = 'Usted puede personalizar las CSS Desde la caja de texto superior. Los cambios se reflejarán en todas las páginas de su sitio.';

/*google analytics*/
$string['googleanalytics'] = 'ID de seguimiento de Google Analytics';
$string['googleanalyticsdesc'] = 'Ingresa tu ID de seguimiento de Google Analytics para habilitar los análisis en tu sitio web. El formato de ID de seguimiento debe ser como [UA-XXXXX-Y].<br />
Tenga en cuenta que al incluir esta configuración, estará enviando datos a Google Analytics y debe asegurarse de que sus usuarios estén advertidos al respecto. Nuestro producto no almacena ninguno de los datos que se envían a Google Analytics.';

/*theme_remUI_frontpage*/

$string['frontpageimagecontent'] = 'Contenido de cabecera';
$string['frontpageimagecontentdesc'] = ' Esta sección pertenece a la parte superior de la página principal.';
$string['frontpageimagecontentstyle'] = 'Estilo';
$string['frontpageimagecontentstyledesc'] = 'Usted puede escoger entre Static & Slider.';
$string['staticcontent'] = 'Estático';
$string['slidercontent'] = 'Slider';
$string['addtext'] = 'Agregar texto';
$string['defaultaddtext'] = 'La educación es el camino comprobado hacia el progreso.';
$string['addtextdesc'] = 'Desde aquí usted puede agregar el texto que se mostrará en la página principal, de preferencia en HTML.';
$string['uploadimage'] = 'Cargar imagen';
$string['uploadimagedesc'] = 'Usted puede cargar una imagen como contenido para la diapositiva';
$string['video'] = 'iframe Embedded code';
$string['videodesc'] = ' Aquí usted puede insertar el código iframe Embedded Del vídeo que desea insertar.';
$string['contenttype'] = 'Seleccione tipo de contenido';
$string['contentdesc'] = 'Usted puede escoger una imagen o insertar una dirección url de vídeo.';
$string['image'] = 'Imagen';
$string['videourl'] = 'Video URL';
$string['frontpageimge'] = '';

$string['slidercount'] = 'Número de diapositivas';
$string['slidercountdesc'] = '';
$string['one'] = '1';
$string['two'] = '2';
$string['three'] = '3';
$string['four'] = '4';
$string['five'] = '5';
$string['eight'] = '8';
$string['twelve'] = '12';

$string['slideimage'] = 'Cargar imágenes para diapositiva';
$string['slideimagedesc'] = 'Usted puede cargar una imagen como contenido para esta diapositiva.';
$string['slidertext'] = 'Agregar texto de diapositiva';
$string['defaultslidertext'] = '';
$string['slidertextdesc'] = 'Usted puede insertar contenido de texto en este lugar, de preferencia en HTML.';
$string['sliderurl'] = 'Agregar enlace para el botón de la diapositiva';
$string['sliderbuttontext'] = 'Agregar texto para el botón en la diapositiva';
$string['sliderbuttontextdesc'] = 'Usted puede agregar texto al botón en esta diapositiva.';
$string['sliderurldesc'] = 'Inserte el enlace de la página hacia donde será dirigido después de hacer clic en el botón.';
$string['slideinterval'] = 'Intervalo de diapositiva';
$string['slideintervaldesc'] = 'Usted puede establecer la transición de tiempo entre diapositivas. Si sólo hay una diapositiva esta opción no tendrá efecto.';
$string['sliderautoplay'] = 'Establecer Slider Autoplay';
$string['sliderautoplaydesc'] = 'Seleccione ‘Si’ Si quiere una transición automática en el show de diapositivas.';
$string['true'] = 'Si';
$string['false'] = 'No';

$string['frontpageblocks'] = 'Contenido del cuerpo';
$string['frontpageblocksdesc'] = 'Usted puede insertar un título para el cuerpo de su sitio.';

$string['enablesectionbutton'] = 'Activar botones en secciones';
$string['enablesectionbuttondesc'] = 'Activar botones en las secciones de página.';
$string['enablefrontpagecourseimg'] = 'Enable Images in Front Page Courses';
$string['enablefrontpagecourseimgdesc'] = 'Enable images in Front Page Courses Available section ';

/* General section descriptions */
$string['frontpageblockiconsectiondesc'] = 'Usted puede elegir cualquier icono desde aquí <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">list</a>. Sólo ingrese el texto después de "fa-". ';
$string['frontpageblockdescriptionsectiondesc'] = 'Una descripción corta sobre el título.';
$string['defaultdescriptionsection'] = 'Tecnologías a tiempo para escenarios corporativos.';
$string['sectionbuttontextdesc'] = 'Ingrese el texto para el botón en esta sección.';
$string['sectionbuttonlinkdesc'] = 'Ingrese la URL Para esta sección.';
$string['frontpageblocksectiondesc'] = 'Agregar un título a esta sección.';

/* block section 1 */
$string['frontpageblocksection1'] = 'Título del texto para la primera sección';
$string['frontpageblockdescriptionsection1'] = 'Descripción de la primera sección';
$string['frontpageblockiconsection1'] = 'Font-Awesome icon Para la primera sección';
$string['sectionbuttontext1'] = 'Texto del botón para la primera sección';
$string['sectionbuttonlink1'] = 'URL link Para la primera sección';


/* block section 2 */
$string['frontpageblocksection2'] = 'Título para la segunda sección';
$string['frontpageblockdescriptionsection2'] = 'Descripción para la segunda sección';
$string['frontpageblockiconsection2'] = 'Font-Awesome icon Para la segunda sección';
$string['sectionbuttontext2'] = 'Texto del botón Para la segunda sección';
$string['sectionbuttonlink2'] = 'URL link Para la segunda sección';


/* block section 3 */
$string['frontpageblocksection3'] = 'Título para la tercera sección';
$string['frontpageblockdescriptionsection3'] = 'Descripción para la tercera sección';
$string['frontpageblockiconsection3'] = 'Font-Awesome icon Para la tercera sección';
$string['sectionbuttontext3'] = 'Texto del botón Para la tercera sección';
$string['sectionbuttonlink3'] = 'URL link Para la tercera sección';


/* block section 4 */
$string['frontpageblocksection4'] = 'Título para la cuarta sección';
$string['frontpageblockdescriptionsection4'] = 'Descripción para la cuarta sección';
$string['frontpageblockiconsection4'] = 'Font-Awesome icon Para la cuarta sección';
$string['sectionbuttontext4'] = 'Texto del botón para la cuarta sección';
$string['sectionbuttonlink4'] = 'URL link Para la cuarta sección';

// Frontpage Aboutus settings
$string['frontpageaboutus'] = 'Página delantera Sobre nosotros';
$string['frontpageaboutusdesc'] = 'Esta sección es para la página "sobre nosotros"';
$string['frontpageaboutustitledesc'] = 'Agregar título a la sección Acerca de nosotros';
$string['frontpageaboutusbody'] = 'Descripción del cuerpo de la sección Acerca de nosotros';
$string['frontpageaboutusbodydesc'] = 'Una breve descripción sobre esta sección';

$string['enablefrontpageaboutus'] = 'Activar la sección sobre nosotros';
$string['enablefrontpageaboutusdesc'] = 'Activar la sección sobre nosotros en la página principal.';
$string['frontpageaboutusheading'] = 'Título sobre nosotros';
$string['frontpageaboutusheadingdesc'] = 'Cabecera de texto de esta sección';
$string['frontpageaboutustext'] = 'Texto sobre nosotros';
$string['frontpageaboutustextdesc'] = 'Ingresar el texto sobre nosotros.';
$string['frontpageaboutusdefault'] = '<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
              Ut enim ad minim veniam.</p>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                  eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.Lorem ipsum dolor sit amet,
                  consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                  labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur
                  adipisicing elit, sed do eiusmod tempor incididunt
                  ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>';
$string['frontpageaboutusimage'] = 'Imagen sobre nosotros';
$string['frontpageaboutusimagedesc'] = 'Cargar la imagen sobre nosotros';

// Social media settings
$string['socialmedia'] = 'Configuración de redes sociales';
$string['socialmediadesc'] = 'Ingrese los enlaces de sus sitios sociales.';
$string['facebooksetting'] = 'Configuración de Facebook';
$string['facebooksettingdesc'] = 'Ingrese la dirección de su página de Facebook. https://www.facebook.com/pagename';
$string['twittersetting'] = 'Configuración de Twitter';
$string['twittersettingdesc'] = 'Ingrese la dirección de su página de Twitter. https://www.twitter.com/pagename';
$string['linkedinsetting'] = 'Configuración del LinkedIn';
$string['linkedinsettingdesc'] = 'Ingrese la dirección de su página LinkedIn. https://www.linkedin.com/in/pagename';
$string['gplussetting'] = 'Google Plus Configuración';
$string['gplussettingdesc'] = 'Ingrese la dirección de Google Plus. https://plus.google.com/pagename';
$string['youtubesetting'] = 'YouTube Configuración';
$string['youtubesettingdesc'] = 'Ingrese la dirección de su sitio YouTube. https://www.youtube.com/channel/UCU1u6QtAAPJrV0v0_c2EISA';
$string['instagramsetting'] = 'Instagram Configuración';
$string['instagramsettingdesc'] = 'Ingrese la dirección de su página de Instagram. https://www.instagram.com/name';
$string['pinterestsetting'] = 'Pinterest Configuración';
$string['pinterestsettingdesc'] = 'Ingrese la dirección de su página de Pinterest. https://www.pinterest.com/name';

// Footer Section Settings
$string['footersetting'] = 'Configuración del pie de página';
// Footer  Column 1
$string['footercolumn1heading'] = 'Contenido de la primera columna (izquierda)';
$string['footercolumn1headingdesc'] = 'Esta sección está relacionada con la porción izquierda del pie de página de su página principal..';

$string['footercolumn1title'] = 'Título de la primera columna';
$string['footercolumn1titledesc'] = 'Agregar título a esta columna.';
$string['footercolumn1customhtml'] = 'Personalizar HTML';
$string['footercolumn1customhtmldesc'] = 'Usted puede personalizar el html de esta columna utilizando la caja de texto superior.';


// Footer  Column 2
$string['footercolumn2heading'] = 'Contenido de la segunda columna (Medio)';
$string['footercolumn2headingdesc'] = 'Esta sección pertenece a la porción del medio del pie de página en la página principal.';

$string['footercolumn2title'] = 'Título de la segunda columna';
$string['footercolumn2titledesc'] = 'Agregar título a esta columna.';
$string['footercolumn2customhtml'] = 'Personalizar HTML';
$string['footercolumn2customhtmldesc'] = 'Usted puede personalizar el html de esta columna utilizando la caja superior.';

// Footer  Column 3
$string['footercolumn3heading'] = 'Contenido de la tercera columna  (Derecha)';
$string['footercolumn3headingdesc'] = 'Esta sección está relacionada con la tercera columna del pie de página de la página principal.';

$string['footercolumn3title'] = 'Titulo de la Tercera Columna';
$string['footercolumn3titledesc'] = 'Agregar Titulo a esta columna.';
$string['footercolumn3customhtml'] = 'Personalizar HTML';
$string['footercolumn3customhtmldesc'] = 'Personalizar HTML de esta columna utilizando la caja superior.';

// Footer Bottom-Right Section
$string['footerbottomheading'] = 'Configuracion de la parte inferior del pie de pagina';
$string['footerbottomdesc'] = 'Aqui puede especificar su propio enlace para mostrar hasta abajo del pie de pagina';
$string['footerbottomtextdesc'] = 'Agregar texto para configurar la parte inferior del Pie de Pagina.';
$string['footerbottomlink'] = 'Enlace del Pie de Pagina';
$string['footerbottomlinkdesc'] = 'Ingresar la URL del enlace en la parte inferior del Pie de Pagina. http://www.yourcompany.com';
$string['poweredbyedwiser'] = 'Powered by Edwiser';
$string['poweredbyedwiserdesc'] = 'Uncheck to remove  \'Powered by Edwiser\' from your site.';

// Login settings page code begin.

$string['loginsettings'] = 'Configuracion de la pagina de ingreso';
$string['navlogin_popup'] = 'Habilitar emergente de inicio de sesión';
$string['navlogin_popupdesc'] = 'Habilitar el popup de inicio de sesión en el encabezado.';
$string['loginsettingpic'] = 'Cargar imagen de fondo';
$string['loginsettingpicdesc'] = 'Cargar una imagen como fondo para el formulario de ingreso.';
$string['signuptextcolor'] = 'Color de texto para el Panel de Registro';
$string['signuptextcolordesc'] = 'Seleccione un color de texto para el Panel de Registro.';
$string['left'] = "Izquierdo";
$string['right'] = "Derecho";
$string['remember_me'] = "Recuérdame";
$string['brandlogopos'] = "Posición del logotipo marca";
$string['brandlogoposdesc'] = "Si está habilitado, el logotipo de la marca estará visible en la barra lateral derecha encima del formulario de inicio de sesión.";
$string['brandlogotext'] = "Sitio descripción";
$string['brandlogotextdesc'] = "Agregar texto para la descripción del sitio que se mostrará en la página de inicio de sesión y registro. Manténgalo en blanco si no desea poner ninguna descripción.";
// Login settings Page code end.

// From theme snap
$string['title'] = 'Titulo';
$string['contents'] = 'Contenidos';
$string['addanewsection'] = 'Crear una nueva Seccion';
$string['createsection'] = 'Crear Seccion';

/* User Profile Page */

$string['blogentries'] = 'Entradas de Blog';
$string['discussions'] = 'Discusiones';
$string['discussionreplies'] = 'Replicas';
$string['aboutme'] = 'Acerca de Mi';

$string['addtocontacts'] = 'Agregar a Contactos';
$string['removefromcontacts'] = 'Remover de Contactos';
$string['block'] = 'Block';
$string['removeblock'] = 'Remover Block';

$string['interests'] = 'Intereses';
$string['institution'] = 'Institucion';
$string['location'] = 'Lugar';
$string['description'] = 'Descripcion';

$string['commoncourses'] = 'Cursos Comunes';
$string['editprofile'] = 'Editar Perfil';

$string['firstname'] = 'Nombre';
$string['surname'] = 'Apellido';
$string['email'] = 'Email';
$string['citytown'] = 'Ciudad';
$string['country'] = 'Pais';
$string['selectcountry'] = 'Seleccionar Pais';
$string['description'] = 'Descripcion';

$string['nocommoncourses'] = 'Usted no comparte ningun curso en comun con este usuario.';
$string['notenrolledanycourse'] = 'Usted no esta inscrito (a) en ningun curso.';
$string['usernotenrolledanycourse'] = '{$a} has not been enrolled in any course.';
$string['nobadgesyetcurrent'] = 'No tienes medallas todavia.';
$string['nobadgesyetother'] = 'Este usuario no tiene medallas todavia.';
$string['grade'] = "Grado";
$string['viewnotes'] = "Ver notas";

// User profile page js

$string['actioncouldnotbeperformed'] = 'No se pudo ejecutar la accion!';
$string['enterfirstname'] = 'Por favor ingrese su nombre.';
$string['enterlastname'] = 'Por favor, ingrese su apellido.';
$string['enteremailid'] = 'Ingrese su identificacion de EMAIL.';
$string['enterproperemailid'] = 'Por favor ingrese una direccion de EMAIL correcta.';
$string['detailssavedsuccessfully'] = 'Detalles Guardados Exitosamente!';

/* Header */

$string['startedsince'] = 'Empezado desde ';
$string['startingin'] = 'Empezando en ';

$string['userimage'] = 'Imagen de Usuario';

$string['seeallmessages'] = 'Ver todos los mensajes';
$string['viewallnotifications'] = 'Ver todas las notificaciones';
$string['viewallupcomingevents'] = 'Ver todos los eventos futuros';

$string['youhavemessages'] = 'Tiene {$a} sin leer (s)';
$string['youhavenomessages'] = 'No hay mensajes nuevos para leer';

$string['youhavenotifications'] = 'Tiene {$a} notificaciones';
$string['youhavenonotifications'] = 'No hay notificaciones';

$string['youhaveupcomingevents'] = 'Usted tiene {$a} eventos futuro(s)';
$string['youhavenoupcomingevents'] = 'No tiene eventos futuros';


/* Dashboard elements */

// Add notes
$string['addnotes'] = 'Agregar Comentario o Nota';
$string['selectacourse'] = 'Seleccionar un Curso';

$string['addsitenote'] = 'Agregar Comentario al Sitio';
$string['addcoursenote'] = 'Agregar Comentario al Curso';
$string['addpersonalnote'] = 'Agregar Nota Personal';
$string['deadlines'] = 'Fecha Limite';

// Add notes js
$string['selectastudent'] = 'Seleccione un estudiante';
$string['total'] = 'Total';
$string['nousersenrolledincourse'] = 'No hay estudiantes inscritos en el {$a} Curso.';
$string['selectcoursetodisplayusers'] = 'Seleccione un curso para mostrar los estudiantes inscritos en el mismo.';


// Deadlines
$string['gotocalendar'] = 'Ir al calendario';
$string['noupcomingdeadlines'] = 'No hay fechas limite proximas!';
$string['in'] = 'En';
$string['since'] = 'Desde';

// Latest Members
$string['latestmembers'] = 'Ultimos miembros';
$string['viewallusers'] = 'Ver todos los usuarios';

// Recently Active Forums
$string['recentlyactiveforums'] = 'Foros Activos Recientemente';

// Recent Assignments
$string['assignmentstobegraded'] = 'Tareas para Calificar';
$string['assignment'] = 'Tarea';
$string['recentfeedback'] = 'Comentarios Recientes';

// Recent Events
$string['upcomingevents'] = 'Eventos Futuros';
$string['productimage'] = 'Imagen del Producto';
$string['noupcomingeventstoshow'] = 'No hay eventos futuros para mostrar!';
$string['viewallevents'] = 'Ver Todos los Eventos';
$string['addnewevent'] = 'Agregar Nuevo Evento';

// Enrolled users stats
$string['enrolleduserstats'] = 'Estadistica de Usuarios Inscritos por Categoria de Curso';
$string['problemwhileloadingdata'] = 'Lo sentimos, ha ocurrido un problema mientras cargabamos la informacion...';
$string['nocoursecategoryfound'] = 'No hay categoria de cursos en el sistema.';
$string['nousersincoursecategoryfound'] = 'Se han hallado usuarios no inscritos en esta categoria de Curso.';

// Quiz stats
$string['quizstats'] = 'Estadistica de intentos del ejercicio';
$string['totalusersattemptedquiz'] = 'Total de usuarios que han iniciado el ejercicio';
$string['totalusersnotattemptedquiz'] = 'Total de Usuarios que no han intentado el ejercicio';

/* Theme Controller */

$string['years'] = 'año(s)';
$string['months'] = 'mes(s)';
$string['days'] = 'dia(s)';
$string['hours'] = 'hora(s)';
$string['mins'] = 'min(s)';

$string['parametermustbeobjectorintegerorstring'] = 'El parametro {$a} debe ser un objeto, un integro o una cadena numerica';
$string['usernotenrolledoncoursewithcapability'] = 'El usuario no tiene capacidad de inscribirse en este curso';
$string['userdoesnothaverequiredcoursecapability'] = 'El usuario no tiene privilegios necesarios';
$string['coursesetuptonotshowgradebook'] = 'El curso esta configurado para no mostrar calificacion a los alumnos';
$string['coursegradeishiddenfromstudents'] = 'La calificacion del curso esta escondida para los alumnos';
$string['feedbackavailable'] = 'Retroalimentacion disponible';
$string['nograding'] = 'No tiene envios para calificar.';


/* Calendar page */
$string['selectcoursetoaddactivity'] = 'Seleccionar el Curso para agregar actividad';
$string['addnewactivity'] = 'Agregar Nueva Actividad';

// Calendar page js
$string['redirectingtocourse'] = 'Redireccionando al  {$a} pagina del Curso..';
$string['nopermissiontoaddactivityinanycourse'] = 'Lo sentimos, usted no tiene privilegios para agregar actividades en cualquier curso.';
$string['nopermissiontoaddactivityincourse'] = 'Lo sentimos, no tiene permisos para agregar actividades en {$a} Curso.';
$string['selectyouroption'] = 'Seleccione su opcion';


/* Blog Archive page */
$string['viewblog'] = 'Ver Blog Completo';


/* Course js */

$string['hidesection'] = 'Colapsar';
$string['showsection'] = 'Expandir';
$string['hidesections'] = 'Colapsar Secciones';
$string['showsections'] = 'Expandir Secciones';
$string['addsection'] = 'Agregar Seccion';

$string['overdue'] = 'Atrasado';
$string['due'] = 'Debido';

/* Footer headings */
$string['quicklinks'] = 'Enlaces rápidos';

/*coruse activity navigation*/
$string['backtocourse'] = 'de vuelta al curso';
$string['sectionnotitle'] = 'General';
$string['sectiondefaulttitle'] = 'Sección';

// latest 3.3 to be arranged later
$string['testimonialcount'] = 'Testimonial Count';
$string['testimonialcountdesc'] = 'Number of testimonials to show.';
$string['testimonialimage'] = 'Testimonial Image';
$string['testimonialimagedesc'] = 'Person image to display with testimonial';
$string['testimonialname'] = 'Person Name';
$string['testimonialnamedesc'] = 'Name of person';
$string['testimonialdesignation'] = 'Person Designation';
$string['testimonialdesignationdesc'] = 'Person\'s designation.';
$string['testimonialtext'] = 'Person\'s Testimonial';
$string['testimonialtextdesc'] = 'What person says';
$string['sectionactivities'] = 'Activities';
$string['showless'] = 'Show Less';
$string['showmore'] = 'Show More';
$string['allcategories'] = 'All categories';
$string['category'] = 'Category';
$string['administrator'] = 'Administrator';
$string['badges'] = 'Badges';
$string['webpage'] = 'Web Page';
$string['contacts'] = 'Contacts';
$string['courses'] = 'Courses';
$string['preferences'] = 'Preferences';
$string['complete'] = 'Complete';
$string['start_date'] = 'Start date';
$string['submit'] = 'Submit';
$string['fontname'] = 'Site Font';
$string['fontnamedesc'] = 'Enter the exact name of the font to use for Moodle.';
$string['followus'] = 'Follow Us';
$string['poweredby'] = 'Powered by Edwiser RemUI';
$string['signin'] = 'Sign In';
$string['forgotpassword'] = 'Forgot Password?';
$string['noaccount'] = 'No Account?';
$string['applysitewide'] = 'Apply Sitewide';

// User profile page js
$string['actioncouldnotbeperformed'] = 'Action could not be performed!';
$string['enterfirstname'] = 'Please enter your First Name.';
$string['enterlastname'] = 'Please enter your Last Name.';
$string['enteremailid'] = 'Please enter your Email ID.';
$string['enterproperemailid'] = 'Please enter a proper Email ID.';
$string['detailssavedsuccessfully'] = 'Details Saved Successfully!';

/* Blog Archive page */
$string['viewblog'] = 'View full Blog';
$string['author'] = 'Author';

$string['createaccount'] = 'Here you can create a new account.';
$string['signup'] = 'Sign Up';
$string['togglesearch'] = 'Toggle Search';
$string['togglefullscreen'] = 'Toggle fullscreen';
$string['navbartype'] = 'Navbar tipo';
$string['sidebarcolor'] = 'barra lateral color';
$string['sitecolor'] = 'sitio Color';
$string['others'] = 'Others';
$string['today'] = 'Today';
$string['yesterday'] = 'Yesterday';
$string['you_do_not_have_permission_to_perform_this_action'] = 'You do not have permission to perform this action';
$string['viewallcourses'] = 'View All Courses';
$string['readmore'] = 'READ MORE';
$string['aboutremui'] = 'About Edwiser RemUI';

$string['remuisettings'] = 'RemUI Settings';
$string['createanewcourse'] = 'Create A New Course';
$string['createarchivepage'] = 'Course Archive Page';
$string['siteblog'] = 'Site Blog';
$string['selectcategory'] = 'Select Category';
$string['nocoursesavail'] = 'Sorry! No courses available at the moment.';
$string['norecentfeedback'] = 'No Recent Feedback !';

// news and updates tab
$string['newsandupdates'] = 'News & Updates';
$string['newupdatemessage'] = 'New Update Available for RemUI.';
$string['currentversionmessage'] = 'Your current version is ';
$string['downloadupdate'] = 'Download Update';
$string['latestversionmessage'] = 'You are using the latest version of RemUI.';
$string['rateremui'] = 'Rate RemUI';
$string['fullname']  = 'Full Name';
$string['providefeedback'] = 'Please provide your feedback about RemUI';
$string['sendfeedback'] = 'Send Feedback';
$string['recentnews'] = 'Recent News';

/* My Course Page */
$string['resume'] = 'Reanudar';
$string['start'] = 'Comienzo';
$string['completed'] = 'Completed';

/* Dashboard Page */
$string['welcome-msg'] = 'Bienvenido a su salpicadero';
$string['coursecompleted'] = 'CURSOS COMPLETADOS';
$string['activitycompleted'] = 'ACTIVIDADES COMPLETADAS';
$string['enrolledcourses'] = 'CURSOS MATRICULADOS';
$string['courseactivities'] = 'ACTIVIDADES DEL CURSO';
$string['noevents'] = "No hay eventos debido";
$string['overdue'] = "Atrasado";
$string['upcoming'] = "Próximo";
$string['expired'] = 'Muerto';
$string['selectcourse'] = "Seleccionar curso";
$string['courseanlytics']="Curso de análisis";
$string['highestgrade']="MÁS ALTO GRADO";
$string['lowestgrade']="MÁS BAJO GRADO";
$string['averagegrade']="PROMEDIO GRADO";
$string['viewcourse'] = "VER CURSO";
$string['mycourses'] = "Mis cursos";
$string['tasks'] = "Tareas para completar";
$string['coursestats'] = "Estadísticas del curso";
$string['allActivities'] = "Todas las actividades";
$string['enabledashboard'] = "Habilitar nuevo tablero de instrumentos";
$string['enabledashboarddesc'] = "Habilitar el nuevo diseño del Panel para todos los usuarios";

/* Footer Setting */
$string['footerbottomtext'] = 'Texto de la parte inferior izquierda del pie de página.';

$string['enableannouncement'] = "Enable Site Announcement";
$string['enableannouncementdesc'] = "Enable a sitewide announcement for site visitors/students.";
$string['announcementtext'] = "Announcement";
$string['announcementtextdesc'] = "Announcement message to be displayed sitewide.";
$string['announcementtype'] = "Announcement type";
$string['announcementtypedesc'] = "info/alert/danger/success";
$string['typeinfo'] = "Information announcement";
$string['typedanger'] = "Urgent announcement";
$string['typewarning'] = "Warning announcement";
$string['typesuccess'] = "Success announcement";

// Teacher Dashboard Strings
$string['courseprogress'] = "Curso Progreso";
$string['course'] = "Curso";
$string['startdate'] = "Fecha de inicio";
$string['enrolledstudents'] = "Estudiantes";
$string['progress'] = "Progreso";
$string['name'] = "Nombre";
$string['status'] = "Estatus";
$string['back'] = "Espalda";


/*Front Page Setting for About Us Block*/
$string['frontpageblockdisplay'] = 'Sobre nosotros Sección';
$string['frontpageblockdisplaydesc'] = 'Puede mostrar u ocultar la sección "Acerca de nosotros", también puede mostrarla en formato de cuadrícula';
$string['donotshowaboutus'] = 'No mostrar';
$string['showaboutusinrow'] = 'Mostrar sección en una fila';
$string['showaboutusingridblock'] = 'Mostrar sección en bloque de cuadrícula';

$string['recentactivityblock'] = 'Actividades recientes Block';
$string['recentactivityblockdesc'] = 'Si está habilitado, el Bloque de actividades recientes estará visible en Dashboard';

$string['enablerecentcourses'] = 'Habilitar cursos recientes';
$string['enablerecentcoursesdesc'] = 'Si está habilitado, el menú desplegable de los cursos recientes se mostrará en el encabezado.';

$string['enabledictionary'] = 'Habilitar diccionario';
$string['enabledictionarydesc'] = 'Si está habilitado, la función Diccionario se activará y mostrará el significado del texto seleccionado.';
$string['more'] = 'Más...';
$string['coursedescimage'] = 'Configuración de imagen de la sección general';
$string['coursedescimagedesc'] = 'Si está habilitado, la imagen de fondo de la descripción de la sección general se obtendrá de la descripción del resumen del curso ( Por defecto primero Imagen ) de lo contrario, se obtendrá de Archivos de resumen del curso.';

/* GDPR compliance */

/* Google analytics help */
// $string['googleanalyticshelp'] = '<a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="left" data-content="<div class=&quot;no-overflow&quot;><p>
// Please be aware that by including this setting, you will be sending data to Google Analytics and you should make sure that your users are warned about this. Our product does not store any of the data being sent to Google Analytics.
// </p>

// </div>" data-html="true" tabindex="0" data-trigger="focus" data-original-title="" title="">
//   <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Choice" aria-label="Help with Choice"></i>
// </a>';
// $string['googleanalyticshelp'] = '<a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="left" data-content="<div class=&quot;no-overflow&quot;><p>Please be aware that by including this setting, you will be sending data to Google Analytics and you should make sure that your users are warned about this. Our product does not store any of the data being sent to Google Analytics.</p>
// </div> " data-html="true" tabindex="0" data-trigger="focus" data-original-title="" title="">
//   <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Display description on course page" aria-label="Help with Display description on course page"></i>
// </a>';
// $string['privacy:metadata:core_files'] = 'Stores Slider images and images for different sections for the home page. Also, a background image uploaded by the admin for the login page is stored by the theme.';

/* Course view preference */
$string['privacy:metadata:preference:course_view_state'] = 'El tipo de pantalla que el usuario prefiere para la lista de cursos';
$string['course_view_state_description_grid'] = 'Para mostrar los cursos en formato de cuadrícula';
$string['course_view_state_description_list'] = 'Para mostrar los cursos en formato de lista';

/* Course view preference */
$string['privacy:metadata:preference:viewCourseCategory'] = 'El tipo de pantalla que el usuario prefiere para la lista de cursos';
$string['viewCourseCategory_grid'] = 'Para mostrar los cursos en formato de cuadrícula';
$string['viewCourseCategory_list'] = 'Para mostrar los cursos en formato de lista';

/* Aside right view preference */
$string['privacy:metadata:preference:aside_right_state'] = 'Si el bloque a un lado de la derecha debe mantenerse abierto o atracado';
$string['aside_right_state_'] = 'Para mostrar el bloque a un lado a la derecha como abierto'; // blank value
$string['aside_right_state_overrideaside'] = 'Para mostrar el bloque a un lado a la derecha como acoplado'; // overrideaside

/* Menu view preference */
$string['privacy:metadata:preference:menubar_state'] = 'El tipo de pantalla que el usuario prefiere para la barra de menú';
$string['menubar_state_fold'] = 'Para mostrar la barra de menú como doblada';
$string['menubar_state_unfold'] = 'Para mostrar la barra de menú como desplegada';
$string['menubar_state_open'] = 'Para mostrar la barra de menú como abierta';
$string['menubar_state_hide'] = 'Para mostrar la barra de menú como oculta';
