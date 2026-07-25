<?php
define('PAGANATION_COUNT',100);
define('API_PAGANATION_COUNT',20);
  function getFolderAssestLocale()
 {
return app()->getLocale() == 'ar' ? 'css-rtl' :'css';
 }
 function getSidebarfooter()
 {
return app()->getLocale() == 'ar' ? 'sidebar-rtl' :'sidebar';
 }

 function getNotiftcss()
 {
return app()->getLocale() == 'ar' ? 'notifyar' :'notify';
 }

   function slug_ar($string, $separator = '-') {
  if (is_null($string)) {
      return "";
  }
  $string = trim($string);
  $string = mb_strtolower($string, "UTF-8");
  // '/' and/or '\' if found and not remoeved it will change the get request route
  $string = str_replace('/', $separator, $string);
  $string = str_replace('\\', $separator, $string);
  $string = preg_replace("/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "",
  $string);
  $string = preg_replace("/[\s-]+/", " ", $string);
  $string = preg_replace("/[\s_]/", $separator, $string);
  return $string;
}
