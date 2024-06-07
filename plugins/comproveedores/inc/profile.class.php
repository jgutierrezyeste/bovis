<?php

/* 

 */
 
 class PluginComproveedoresProfile extends Profile{
	 
	static $rigthname= "profile";
	 
	function getTabNameForItem(CommonGLPI $item, $withtemplate=0){
		if ($item -> getType() == "Profile"){
			return PluginComproveedoresComproveedore::getTypeName(2);
		}
		return '';
	}
	
	static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getType()=='Profile') {
         $ID = $item->getID();
         $prof = new self();

         self::addDefaultProfileInfos($ID, array('plugin_comproveedores' => 0,
												'plugin_comproveedores_open_ticket'   => 0));
         $prof->showForm($ID);
      }
      return true;
   }
   
   static function createFirstAccess($ID) {

      self::addDefaultProfileInfos($ID, array('plugin_comproveedores'             => 127,
                                              'plugin_comproveedores_open_ticket' => 1), true);
   }
	
	
	static function addDefaultProfileInfos($profiles_id, $rights, $drop_existing=false) {

      $profileRight = new ProfileRight();
      foreach ($rights as $right => $value) {
         if (countElementsInTable('glpi_profilerights',
                                   "`profiles_id`='$profiles_id' AND `name`='$right'")
             && $drop_existing) {

            $profileRight->deleteByCriteria(array('profiles_id' => $profiles_id,
                                                  'name' => $right));
         }

         if (!countElementsInTable('glpi_profilerights',
                                   "`profiles_id`='$profiles_id' AND `name`='$right'")) {
            $myright['profiles_id'] = $profiles_id;
            $myright['name']        = $right;
            $myright['rights']      = $value;
            $profileRight->add($myright);

            //Add right to the current session
            $_SESSION['glpiactiveprofile'][$right] = $value;
         }
      }
   }
   
   function showForm($profiles_id=0, $openform=TRUE, $closeform=TRUE) {

      echo "<div class='firstbloc'>";
      if (($canedit = Session::haveRightsOr(self::$rightname, array(CREATE, UPDATE, PURGE)))
          && $openform) {
         $profile = new Profile();
         echo "<form method='post' action='".$profile->getFormURL()."'>";
      }

      $profile = new Profile();
      $profile->getFromDB($profiles_id);
      if ($profile->getField('interface') == 'central') {
         $rights = $this->getAllRights();
         $profile->displayRightsChoiceMatrix($rights, array('canedit'       => $canedit,
                                                            'default_class' => 'tab_bg_2',
                                                            'title'         => __('General')));
      }
      echo "<table class='tab_cadre_fixehov'>";
      echo "<tr class='tab_bg_1'><th colspan='4'>".__('Helpdesk')."</th></tr>\n";

      $effective_rights = ProfileRight::getProfileRights($profiles_id,
                                                         array('plugin_comproveedores_open_ticket'));
      echo "<tr class='tab_bg_2'>";
      echo "<td width='20%'>".__('Associable items to a ticket')."</td>";
      echo "<td colspan='5'>";
      Html::showCheckbox(array('name'    => '_plugin_comproveedores_open_ticket',
                               'checked' => $effective_rights['plugin_comproveedores_open_ticket']));
      echo "</td></tr>\n";
      echo "</table>";

      if ($canedit
          && $closeform) {
         echo "<div class='center'>";
         echo Html::hidden('id', array('value' => $profiles_id));
         echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
         echo "</div>\n";
         Html::closeForm();
      }
      echo "</div>";
   }
   
   static function getAllRights($all=false) {

      $rights = array(array('itemtype'  => 'PluginComproveedoresComproveedore',
                            'label'     => _n('Gestion de CV de proveedores', 'Gestion de CV de proveedores', 2, 'comproveedores'),
                            'field'     => 'plugin_comproveedores'));
      if ($all) {
         $rights[] = array('itemtype' => 'PluginComproveedoresComproveedore',
                           'label'    =>  __('Associable items to a ticket'),
                           'field'    => 'plugin_comproveedores_open_ticket');
      }
      return $rights;
   }
   
   static function translateARight($old_right) {

      switch ($old_right) {
         case '':
            return 0;

         case 'r' :
            return READ;

         case 'w':
            return ALLSTANDARDRIGHT + READNOTE + UPDATENOTE;

         case '0':
         case '1':
            return $old_right;

         default :
            return 0;
      }
   }
   
   static function initProfile() {
      global $DB;

      $profile = new self();

      //Add new rights in glpi_profilerights table
      foreach ($profile->getAllRights(true) as $data) {
         if (countElementsInTable("glpi_profilerights",
                                  "`name` = '".$data['field']."'") == 0) {
            ProfileRight::addProfileRights(array($data['field']));
         }
      }

      //Migration old rights in new ones
      foreach ($DB->request("SELECT `id` FROM `glpi_profiles`") as $prof) {
         //self::migrateOneProfile($prof['id']);
      }
      foreach ($DB->request("SELECT *
                             FROM `glpi_profilerights`
                             WHERE `profiles_id`='".$_SESSION['glpiactiveprofile']['id']."'
                                   AND `name` LIKE '%plugin_comproveedores%'") as $prof) {
         $_SESSION['glpiactiveprofile'][$prof['name']] = $prof['rights'];
      }
   }
   
   static function removeRightsFromSession() {

      foreach (self::getAllRights(true) as $right) {
         if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
            unset($_SESSION['glpiactiveprofile'][$right['field']]);
         }
      }
   }
   
   
	 
 }