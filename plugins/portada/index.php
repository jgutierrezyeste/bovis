<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2017 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */


/** @file
* @brief
*/

// Check PHP version not to have trouble
// Need to be the very fist step before any include
if (version_compare(PHP_VERSION, '5.6') < 0) {
   die('PHP >= 5.6 required');
}


use Glpi\Event;

//Load GLPI constants
define('GLPI_ROOT', __DIR__);
include (GLPI_ROOT . "/inc/based_config.php");
include_once (GLPI_ROOT . "/inc/define.php");

define('DO_NOT_CHECK_HTTP_REFERER', 1);

// If config_db doesn't exist -> start installation
if (!file_exists(GLPI_CONFIG_DIR . "/config_db.php")) {
   include_once (GLPI_ROOT . "/inc/autoload.function.php");
   Html::redirect("install/install.php");
   die();

} else {
   $TRY_OLD_CONFIG_FIRST = true;
   include (GLPI_ROOT . "/inc/includes.php");
   $_SESSION["glpicookietest"] = 'testcookie';

   // For compatibility reason
   if (isset($_GET["noCAS"])) {
      $_GET["noAUTO"] = $_GET["noCAS"];
   }

   if (!isset($_GET["noAUTO"])) {
      Auth::redirectIfAuthenticated();
   }
   Auth::checkAlternateAuthSystems(true, isset($_GET["redirect"])?$_GET["redirect"]:"");

   // Send UTF8 Headers
   header("Content-Type: text/html; charset=UTF-8");

   // Start the page
   echo "<!DOCTYPE html>\n";
   echo "<html lang=\"{$CFG_GLPI["languages"][$_SESSION['glpilanguage']][3]}\" class='loginpage'>";
   echo '<head><title>'."BOVIS: Portal WEB de Proveedores".'</title>'."\n";
   echo '<meta charset="utf-8"/>'."\n";
   echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
   echo '<link rel="shortcut icon" type="images/x-icon" href="'.$CFG_GLPI["root_doc"].
          '/pics/favicon.ico" />';

   // auto desktop / mobile viewport
   echo "<meta name='viewport' content='width=device-width, initial-scale=1'/>";

   // Appel CSS
   echo '<link rel="stylesheet" href="'.$CFG_GLPI["root_doc"].'/css/styles.css" type="text/css" '.
         'media="screen" />';
   // CSS theme link
      echo Html::css("css/palettes/".$CFG_GLPI["palette"].".css");
   // surcharge CSS hack for IE
   echo "<!--[if lte IE 8]>";
   echo Html::css("css/styles_ie.css");
   echo "<![endif]-->";

   echo "</head>";

   echo "<body >";
   
   $query1="SELECT count(*) as cantidad FROM glpi_plugin_portada_config";
                $result1 = $DB->query($query1);
                $data=$DB->fetch_array($result1);
                $ultimo=$data['cantidad'];
                
   $query="SELECT id, name, comment, route "
                    . "FROM glpi_plugin_portada_config";
                $result = $DB->query($query);

                
                
                $strslider = "<ul>";
                $id=1;
                while ($data=$DB->fetch_array($result)) {
                    
                   
                    if($id==1){
                        $anterior=$ultimo;
                        $siguiente=$id+1;
                    }else if($id==$ultimo){
                        $anterior=$id-1;
                        $siguiente=1;
                    }else{
                        $anterior=$id-1;
                        $siguiente=$id+1;
                    }
                    
                    $strslider.="<li id='no-js-slider-".$id."' class='slide'>
                        <img src='pics/trabajos/".$data['route']."' class='slideimage'>
                        <div id='proyecto".$id."' class='proyectos'>".
                            $data['name']. " <br>" .
                            $data['comment']."</div>
                        <a class='prev' href='#no-js-slider-".$anterior."'>prev</a>
                        <a class='next' href='#no-js-slider-".$siguiente."'>next</a>
                    </li>";
                    $id++;
                    
                }
                $strslider.="</ul>";
				/*
 $strslider = "<ul>
    <li id='no-js-slider-1' class='slide'>
      <img src='pics/trabajos/ciudad.jpg' class='slideimage'>
      <div id='proyecto1' class='proyectos'>PROYECTO 1</div>
      <a class='prev' href='#no-js-slider-10'>prev</a>
      <a class='next' href='#no-js-slider-2'>next</a>
    </li>
    <li id='no-js-slider-2' class='slide'>
      <img src='pics/trabajos/ciudad2.jpg' class='slideimage'> 
      <div id='proyecto2' class='proyectos'>PROYECTO 2</div>
      <a class='prev' href='#no-js-slider-1'>prev</a>
      <a class='next' href='#no-js-slider-3'>next</a>           
    </li>
    <li id='no-js-slider-3' class='slide'>
      <img src='pics/trabajos/ciudad3.jpg' class='slideimage'>
      <div id='proyecto3' class='proyectos'>PROYECTO 3</div>
      <a class='prev' href='#no-js-slider-2'>prev</a>
      <a class='next' href='#no-js-slider-4'>next</a>           
    </li>
    <li id='no-js-slider-4' class='slide'>
      <img src='pics/trabajos/ciudad4.jpg' class='slideimage'>
      <div id='proyecto4' class='proyectos'>PROYECTO 4</div>
      <a class='prev' href='#no-js-slider-3'>prev</a>
      <a class='next' href='#no-js-slider-5'>next</a>          
    </li>
    <li id='no-js-slider-5' class='slide'>
      <img src='pics/trabajos/ciudad5.jpg' class='slideimage'> 
      <div id='proyecto5' class='proyectos'>PROYECTO 5</div>
      <a class='prev' href='#no-js-slider-4'>prev</a> 
      <a class='next' href='#no-js-slider-6'>next</a>
    </li>
    <li id='no-js-slider-6' class='slide'>
      <img src='pics/trabajos/ciudad6.jpg' class='slideimage'> 
      <div id='proyecto6' class='proyectos'>PROYECTO 6</div>
      <a class='prev' href='#no-js-slider-5'>Prev</a> 
      <a class='next' href='#no-js-slider-7'>next</a>
    </li>
    <li id='no-js-slider-7' class='slide'>
      <img src='pics/trabajos/ciudad7.jpg' class='slideimage'> 
      <div id='proyecto7' class='proyectos'>PROYECTO 7</div>
      <a class='prev' href='#no-js-slider-6'>prev</a> 
      <a class='next' href='#no-js-slider-8'>next</a>
    </li>
    <li id='no-js-slider-8' class='slide'>
      <img src='pics/trabajos/ciudad8.jpg' class='slideimage'> 
      <div id='proyecto8' class='proyectos'>PROYECTO 8</div>
      <a class='prev' href='#no-js-slider-7'>prev</a> 
      <a class='next' href='#no-js-slider-9'>next</a>
    </li>
    <li id='no-js-slider-9' class='slide'>
      <img src='pics/trabajos/ciudad9.jpg' class='slideimage'> 
      <div id='proyecto9' class='proyectos'>PROYECTO 9</div>
      <a class='prev' href='#no-js-slider-8'>prev</a> 
      <a class='next' href='#no-js-slider-10'>next</a>
    </li>
    <li id='no-js-slider-10' class='slide'>
      <img src='pics/trabajos/ciudad10.jpg' class='slideimage'>
      <div id='proyecto10' class='proyectos'>PROYECTO 10</div>
      <a id='inicio' class='prev' href='#no-js-slider-9'>prev</a>
      <a class='next' href='#no-js-slider-1'>next</a>          
    </li>
  </ul>";*/
 

   
   
   
   echo "<div id='firstboxlogin' class='slider'>";
   echo "<div id='logo_login' style='background-color:white;'></div>";
   echo "<div id='text-login'>";
   echo nl2br(Toolbox::unclean_html_cross_side_scripting_deep($CFG_GLPI['text_login']));
   echo "</div>";
 
   echo "<div id='boxlogin' style='position:fixed; top:200px; left:100px; width:300px; height:300px; z-index:1000; background-color: #1b2f62; opacity: 0.9'>";
   echo "<form action='".$CFG_GLPI["root_doc"]."/front/login.php' method='post'>";

   $_SESSION['namfield'] = $namfield = uniqid('fielda');
   $_SESSION['pwdfield'] = $pwdfield = uniqid('fieldb');
   $_SESSION['rmbfield'] = $rmbfield = uniqid('fieldc');

   // Other CAS
   if (isset($_GET["noAUTO"])) {
      echo "<input type='hidden' name='noAUTO' value='1' />";
   }
   // redirect to ticket
   if (isset($_GET["redirect"])) {
      Toolbox::manageRedirect($_GET["redirect"]);
      echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'"/>';
   }
   echo '<p class="login_input">
         <input type="text" name="'.$namfield.'" id="login_name" required="required"
                placeholder="'.__('Login').'" autofocus="autofocus" />
         <span class="login_img"></span>
         </p>';
   echo '<p class="login_input">
         <input type="password" name="'.$pwdfield.'" id="login_password" required="required"
                placeholder="'.__('Password').'"  />
         <span class="login_img"></span>
         </p>';
   if ($CFG_GLPI["login_remember_time"]) {
      echo '<p class="login_input">
            <label for="login_remember">
                   <input type="checkbox" name="'.$rmbfield.'" id="login_remember"
                   '.($CFG_GLPI['login_remember_default']?'checked="checked"':'').' />
            '.__('Remember me').'</label>
            </p>';
   }
   echo '<p class="login_input">
         <input type="submit" name="submit" value="'._sx('button', 'Post').'" class="submit" />
         </p>';

   if ($CFG_GLPI["notifications_mailing"]
       && countElementsInTable('glpi_notifications',
                               "`itemtype`='User'
                                AND `event`='passwordforget'
                                AND `is_active`=1")) {
      echo '<a id="forget" href="front/lostpassword.php?lostpassword=1">'.
             __('Forgotten password?').'</a>';
   }
   Html::closeForm();
   
   $scriptjava = "<script type='text/javascript' >
   document.getElementById('login_name').focus();</script>";
   
   echo $scriptjava;
   echo "</div>";  // end login box
   echo $strslider;  

   echo "<div class='error'>";
   echo "<noscript><p>";
   echo __('You must activate the JavaScript function of your browser');
   echo "</p></noscript>";

   if (isset($_GET['error']) && isset($_GET['redirect'])) {
      switch ($_GET['error']) {
         case 1 : // cookie error
            echo __('You must accept cookies to reach this application');
            break;

         case 2 : // GLPI_SESSION_DIR not writable
            echo __('Checking write permissions for session files');
            break;

         case 3 :
            echo __('Invalid use of session ID');
            break;
      }
   }
   echo "</div>";

   // Display FAQ is enable
   if ($CFG_GLPI["use_public_faq"]) {
      echo '<div id="box-faq">'.
            '<a href="front/helpdesk.faq.php">[ '.__('Access to the Frequently Asked Questions').' ]';
      echo '</a></div>';
   }

   echo "<div id='display-login'>";
   Plugin::doHook('display_login');
   echo "</div>";


   echo "</div>"; // end contenu login

   if (GLPI_DEMO_MODE) {
      echo "<div class='center'>";
      Event::getCountLogin();
      echo "</div>";
   }
  echo "<div id='footer-login' class='home'>" . Html::getCopyrightMessage(false) . "</div>";

}
// call cron
if (!GLPI_DEMO_MODE) {
   CronTask::callCronForce();
}
   
echo "</body></html>";
