<?php
$ajax_query = false;

// Detection du type de requete
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') $ajax_query = true;

// JS
//$registry->addJS('mustache.js');

// System de notification pour le bootstrap
$registry->load_web_lib('scrollup/css/themes/image.css','css');
$registry->load_web_lib('pnotify/jquery.pnotify.min.js','js','footer');
$registry->load_web_lib('scrollup/jquery.scrollUp.min.js','js','footer');
$registry->load_web_lib('pnotify/jquery.pnotify.default.css','css');
$registry->load_web_lib('moment-2.4.0.js','js','footer');
$registry->load_web_lib('bt3_datapicker/css/bootstrap-datetimepicker.min.css','css');
$registry->load_web_lib('bt3_datapicker/js/bootstrap-datetimepicker.min.js','js','footer');
$registry->load_web_lib('fw/fw.js','js','footer');