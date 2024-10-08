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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Project Class
 *
 * @since version 0.85
**/
class Project extends CommonDBTM {

   // From CommonDBTM
   public $dohistory                   = true;
   static protected $forward_entity_to = ['ProjectTask'];
   static $rightname                   = 'project';
   protected $usenotepad               = true;

   const READMY                        = 1;
   const READALL                       = 1024;

   protected $team                     = [];



   /**
    * Name of the type
    *
    * @param $nb : number of item in the type (default 0)
   **/
   static function getTypeName($nb = 0) {
      return _n('Project', 'Projects', $nb);
   }


   static function canView() {
      return Session::haveRightsOr(self::$rightname, [self::READALL, self::READMY]);
   }


   /**
    * Is the current user have right to show the current project ?
    *
    * @return boolean
   **/
   function canViewItem() {

      if (!Session::haveAccessToEntity($this->getEntityID())) {
         return false;
      }
      return (Session::haveRight(self::$rightname, self::READALL)
              || (Session::haveRight(self::$rightname, self::READMY)
                  && (($this->fields["users_id"] === Session::getLoginUserID())
                      || $this->isInTheManagerGroup()
                      || $this->isInTheTeam()
                  ))
              );
   }

    function getProfileByUserID($Id){
            global $DB;

            $query ="SELECT profiles_id as profile FROM glpi_users WHERE id=$Id";

            $result=$DB->query($query);
            $id=$DB->fetch_array($result);

            if($id['profile']<>''){
                    $options['profile']=$id['profile'];
            }
            return $options['profile'];
    }
   /**
    * Is the current user have right to create the current change ?
    *
    * @return boolean
   **/
   function canCreateItem() {

      if (!Session::haveAccessToEntity($this->getEntityID())) {
         return false;
      }
      return Session::haveRight(self::$rightname, CREATE);
   }


   /**
    * @since version 0.85
    *
    * @see commonDBTM::getRights()
    **/
   function getRights($interface = 'central') {

      $values = parent::getRights();
      unset($values[READ]);

      $values[self::READALL] = __('See all');
      $values[self::READMY]  = __('See (actor)');

      return $values;
   }


   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if (static::canView() && !$withtemplate) {
         $nb = 0;
         switch ($item->getType()) {
            //case '':
            case __CLASS__ :
               $ong    = [];
               //if ($_SESSION['glpishow_count_on_tabs']) {
                    $nb = countElementsInTable(
                        $this->getTable(),
                        [
                           $this->getForeignKeyField() => $item->getID(),
                           'is_deleted'                => 0
                        ]
                    );
               //}
               $ong[1] = self::createTabEntry($this->getTypeName(Session::getPluralNumber()), $nb);
               $ong[2] = __('GANTT');
               
               return $ong;
         }
      }

      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      switch ($item->getType()) {
         case __CLASS__ :
            switch ($tabnum) {
               case 1 :
                  $item->showChildren();
                  break;

               case 2 :
                  $item->showGantt($item->getID());
                  break;
            }
            break;
      }
      return true;
   }


   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      //$this->addStandardTab('Document_Item', $ong, $options);

      $this->addStandardTab('ProjectTeam', $ong, $options);      
      $this->addStandardTab('ProjectTask', $ong, $options);
      $this->addStandardTab('PluginComproveedoresValuation', $ong, $options);      
      //$this->addStandardTab(__CLASS__, $ong, $options); --> DIAGRAMAS DE GANTT
     // $this->addStandardTab('ProjectCost', $ong, $options);
      //$this->addStandardTab('Change_Project', $ong, $options);
      //$this->addStandardTab('Item_Project', $ong, $options);
      //$this->addStandardTab('Document_Item', $ong, $options);
      //$this->addStandardTab('Contract_Item', $ong, $options);
      //$this->addStandardTab('Notepad', $ong, $options);
      //$this->addStandardTab('KnowbaseItem_Item', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);
      
      //Quitamos el número al lado del tab paquete
      $ong['ProjectTask$1']=substr($ong['ProjectTask$1'],0, 9);
      //Eliminamos el tab proyectos
      unset($ong['Project$1']);
      
      return $ong;
   }


   static function getAdditionalMenuContent() {

      // No view to project by right on tasks add it
      if (!static::canView()
          && Session::haveRight('projecttask', ProjectTask::READMY)) {
         $menu['project']['title']                    = Project::getTypeName(Session::getPluralNumber());
         $menu['project']['page']                     = ProjectTask::getSearchURL(false);

         $links = static::getAdditionalMenuLinks();
         if (count($links)) {
            $menu['project']['links'] = $links;
         }
         $menu['project']['options']['task']['title'] = __('My tasks');
         $menu['project']['options']['task']['page']  = ProjectTask::getSearchURL(false);
         return $menu;
      }
      return false;
   }


   static function getAdditionalMenuOptions() {

      return ['task' => ['title' => __('My tasks'),
                                   'page'  => ProjectTask::getSearchURL(false)]];
   }


   /**
    * @see CommonGLPI::getAdditionalMenuLinks()
   **/
   static function getAdditionalMenuLinks() {
      global $CFG_GLPI;

      $links = [];
      if (static::canView()
          || Session::haveRight('projecttask', ProjectTask::READMY)) {
         $pic_validate = "<img title=\"".__s('My tasks')."\" alt=\"".__('My tasks')."\" src='".
                           $CFG_GLPI["root_doc"]."/pics/menu_showall.png' class='pointer'>";

         $links[$pic_validate] = '/front/projecttask.php';

         $links['summary'] = '/front/project.form.php?showglobalgantt=1';
      }
      if (count($links)) {
         return $links;
      }
      return false;
   }


   function post_updateItem($history = 1) {
      global $CFG_GLPI;

      if (!isset($this->input['_disablenotif']) && $CFG_GLPI["use_notifications"]) {
         // Read again project to be sure that all data are up to date
         $this->getFromDB($this->fields['id']);
         NotificationEvent::raiseEvent("update", $this);
      }
   }


   function post_addItem() {
      global $DB, $CFG_GLPI;

      // Manage add from template
      if (isset($this->input["_oldID"])) {
         ProjectCost::cloneProject($this->input["_oldID"], $this->fields['id']);

         // ADD Task
         ProjectTask::cloneProjectTask($this->input["_oldID"], $this->fields['id']);

         // ADD Documents
         Document_Item::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);

         // ADD Team
         ProjectTeam::cloneProjectTeam($this->input["_oldID"], $this->fields['id']);

         // ADD Change
         Change_Project::cloneChangeProject($this->input["_oldID"], $this->fields['id']);

         // ADD Contract
         Contract::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);

         // ADD Notepad
         Notepad::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);

         //Add KB links
         KnowbaseItem_Item::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);
      }
      if (!isset($this->input['_disablenotif']) && $CFG_GLPI["use_notifications"]) {
         // Clean reload of the project
         $this->getFromDB($this->fields['id']);

         NotificationEvent::raiseEvent('new', $this);
      }
   }


   function post_getEmpty() {

      $this->fields['priority']     = 3;
      $this->fields['percent_done'] = 0;

      // Set as manager to be able to see it after creation
      if (!Session::haveRight(self::$rightname, self::READALL)) {
         $this->fields['users_id'] = Session::getLoginUserID();
      }
   }


   function post_getFromDB() {
      // Team
      $this->team    = ProjectTeam::getTeamFor($this->fields['id']);
   }


   function pre_deleteItem() {
      global $CFG_GLPI;

      if (!isset($this->input['_disablenotif']) && $CFG_GLPI['use_notifications']) {
         NotificationEvent::raiseEvent('delete', $this);
      }
      return true;
   }


   function cleanDBonPurge() {
      global $DB;

      $pt = new ProjectTask();
      $pt->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      $cp = new Change_Project();
      $cp->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      $ip = new Item_Project();
      $ip->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      $pt = new ProjectTeam();
      $pt->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      parent::cleanDBonPurge();
   }


   /**
    * Is the current user in the team?
    *
    * @return boolean
   **/
   function isInTheTeam() {

      if (isset($this->team['User']) && count($this->team['User'])) {
         foreach ($this->team['User'] as $data) {
            if ($data['items_id'] == Session::getLoginUserID()) {
               return true;
            }
         }
      }

      if (isset($_SESSION['glpigroups']) && count($_SESSION['glpigroups'])
          && isset($this->team['Group']) && count($this->team['Group'])) {
         foreach ($_SESSION['glpigroups'] as $groups_id) {
            foreach ($this->team['Group'] as $data) {
               if ($data['items_id'] == $groups_id) {
                  return true;
               }
            }
         }
      }
      return false;
   }


   /**
    * Is the current user in manager group?
    *
    * @return boolean
   **/
   function isInTheManagerGroup() {

      if (isset($_SESSION['glpigroups']) && count($_SESSION['glpigroups'])
          && $this->fields['groups_id']) {
         foreach ($_SESSION['glpigroups'] as $groups_id) {
            if ($this->fields['groups_id'] == $groups_id) {
               return true;
            }
         }
      }
      return false;
   }


   /**
    * Get team member count
    *
    * @return number
   **/
   function getTeamCount() {

      $nb = 0;
      if (is_array($this->team) && count($this->team)) {
         foreach ($this->team as $val) {
            $nb +=  count($val);
         }
      }
      return $nb;
   }


   function getSearchOptionsNew() {
      $tab = [];

      $tab[] = [
         'id'                 => 'common',
         'name'               => __('Characteristics')
      ];

      $tab[] = [
         'id'                 => '1',
         'table'              => $this->getTable(),
         'field'              => 'name',
         'name'               => __('Name'),
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'forcegroupby'       => true
      ];

      $tab[] = [
         'id'                 => '2',
         'table'              => $this->getTable(),
         'field'              => 'id',
         'name'               => __('ID'),
         'massiveaction'      => false,
         'datatype'           => 'number'
      ];

      $tab[] = [
         'id'                 => '4',
         'table'              => $this->getTable(),
         'field'              => 'code',
         'name'               => __('Code'),
         'massiveaction'      => false,
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '13',
         'table'              => $this->getTable(),
         'field'              => 'name',
         'name'               => __('Father'),
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'joinparams'         => [
            'condition'          => 'AND 1=1'
         ]
      ];

      $tab[] = [
         'id'                 => '21',
         'table'              => $this->getTable(),
         'field'              => 'content',
         'name'               => __('Description'),
         'massiveaction'      => false,
         'datatype'           => 'text'
      ];

      $tab[] = [
         'id'                 => '3',
         'table'              => $this->getTable(),
         'field'              => 'priority',
         'name'               => __('Priority'),
         'searchtype'         => 'equals',
         'datatype'           => 'specific'
      ];

      $tab[] = [
         'id'                 => '14',
         'table'              => 'glpi_projecttypes',
         'field'              => 'name',
         'name'               => __('Type'),
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '12',
         'table'              => 'glpi_projectstates',
         'field'              => 'name',
         'name'               => __('State'),
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '15',
         'table'              => $this->getTable(),
         'field'              => 'date',
         'name'               => __('Creation date'),
         'datatype'           => 'datetime',
         'massiveaction'      => false
      ];

      $tab[] = [
         'id'                 => '5',
         'table'              => $this->getTable(),
         'field'              => 'percent_done',
         'name'               => __('Percent done'),
         'datatype'           => 'number',
         'unit'               => '%',
         'min'                => 0,
         'max'                => 100,
         'step'               => 5
      ];

      $tab[] = [
         'id'                 => '6',
         'table'              => $this->getTable(),
         'field'              => 'show_on_global_gantt',
         'name'               => __('Show on global GANTT'),
         'datatype'           => 'bool'
      ];

      $tab[] = [
         'id'                 => '24',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'linkfield'          => 'users_id',
         'name'               => __('Manager'),
         'datatype'           => 'dropdown',
         'right'              => 'see_project'
      ];

      $tab[] = [
         'id'                 => '49',
         'table'              => 'glpi_groups',
         'field'              => 'completename',
         'linkfield'          => 'groups_id',
         'name'               => __('Manager group'),
         'condition'          => '`is_manager`',
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '7',
         'table'              => $this->getTable(),
         'field'              => 'plan_start_date',
         'name'               => __('Planned start date'),
         'datatype'           => 'datetime'
      ];

      $tab[] = [
         'id'                 => '8',
         'table'              => $this->getTable(),
         'field'              => 'plan_end_date',
         'name'               => __('Planned end date'),
         'datatype'           => 'datetime'
      ];

      $tab[] = [
         'id'                 => '17',
         'table'              => $this->getTable(),
         'field'              => '_virtual_planned_duration',
         'name'               => __('Planned duration'),
         'datatype'           => 'specific',
         'nosearch'           => true,
         'massiveaction'      => false,
         'nosort'             => true
      ];

      $tab[] = [
         'id'                 => '9',
         'table'              => $this->getTable(),
         'field'              => 'real_start_date',
         'name'               => __('Real start date'),
         'datatype'           => 'datetime'
      ];

      $tab[] = [
         'id'                 => '10',
         'table'              => $this->getTable(),
         'field'              => 'real_end_date',
         'name'               => __('Real end date'),
         'datatype'           => 'datetime'
      ];

      $tab[] = [
         'id'                 => '18',
         'table'              => $this->getTable(),
         'field'              => '_virtual_effective_duration',
         'name'               => __('Effective duration'),
         'datatype'           => 'specific',
         'nosearch'           => true,
         'massiveaction'      => false,
         'nosort'             => true
      ];

      $tab[] = [
         'id'                 => '16',
         'table'              => $this->getTable(),
         'field'              => 'comment',
         'name'               => __('Comments'),
         'datatype'           => 'text'
      ];

      $tab[] = [
         'id'                 => '19',
         'table'              => $this->getTable(),
         'field'              => 'date_mod',
         'name'               => __('Last update'),
         'datatype'           => 'datetime',
         'massiveaction'      => false
      ];
	  
      $tab[] = [
         'id'                 => '121',
         'table'              => $this->getTable(),
         'field'              => 'date_creation',
         'name'               => __('Creation date'),
         'datatype'           => 'datetime',
         'massiveaction'      => false
      ];

      $tab[] = [
         'id'                 => '80',
         'table'              => 'glpi_entities',
         'field'              => 'completename',
         'name'               => __('Entity'),
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '86',
         'table'              => $this->getTable(),
         'field'              => 'is_recursive',
         'name'               => __('Child entities'),
         'datatype'           => 'bool'
      ];

      $tab[] = [
         'id'                 => 'project_team',
         'name'               => ProjectTeam::getTypeName(),
      ];

      $tab[] = [
         'id'                 => '87',
         'table'              => User::getTable(),
         'field'              => 'name',
         'name'               => User::getTypeName(2),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'jointype'          => 'itemtype_item_revert',
            'specific_itemtype' => 'User',
            'beforejoin'        => [
               'table'      => ProjectTeam::getTable(),
               'joinparams' => [
                  'jointype' => 'child',
               ]
            ]
         ]
      ];
    

      $tab[] = [
         'id'                 => '88',
         'table'              => Group::getTable(),
         'field'              => 'completename',
         'name'               => Group::getTypeName(2),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'jointype'          => 'itemtype_item_revert',
            'specific_itemtype' => 'Group',
            'beforejoin'        => [
               'table'      => ProjectTeam::getTable(),
               'joinparams' => [
                  'jointype' => 'child',
               ]
            ]
         ]
      ];

      $tab[] = [
         'id'                 => '89',
         'table'              => Supplier::getTable(),
         'field'              => 'name',
         'name'               => Supplier::getTypeName(2),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'jointype'          => 'itemtype_item_revert',
            'specific_itemtype' => 'Supplier',
            'beforejoin'        => [
               'table'      => ProjectTeam::getTable(),
               'joinparams' => [
                  'jointype' => 'child',
               ]
            ]
         ]
      ];

      $tab[] = [
         'id'                 => '90',
         'table'              => Contact::getTable(),
         'field'              => 'name',
         'name'               => Contact::getTypeName(2),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'jointype'          => 'itemtype_item_revert',
            'specific_itemtype' => 'Contact',
            'beforejoin'        => [
               'table'      => ProjectTeam::getTable(),
               'joinparams' => [
                  'jointype' => 'child',
               ]
            ]
         ]
      ];

      $tab[] = [
         'id'                 => '91',
         'table'              => User::getTable(),
         'field'              => 'plugin_comproveedores_communities_id',
         'name'               => Project::getTypeName(2),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'jointype'          => 'glpi_plugin_comproveedores_communities',
            'specific_itemtype' => 'communities',
            'beforejoin'        => [
               'table'      => 'glpi_plugin',
               'joinparams' => [
                  'jointype' => 'child',
               ]
            ]
         ]
      ];       

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::getSearchOptionsToAddNew(get_class($this)));

      $tab = array_merge($tab, Notepad::getSearchOptionsToAddNew());

      return $tab;
   }


   /**
    * @param $output_type     (default 'Search::HTML_OUTPUT')
    * @param $mass_id         id of the form to check all (default '')
    */
   static function commonListHeader($output_type = Search::HTML_OUTPUT, $mass_id = '') {

      // New Line for Header Items Line
      echo Search::showNewLine($output_type);
      // $show_sort if
      $header_num                      = 1;

      $items                           = [];
      $items[(empty($mass_id) ? '&nbsp' : Html::getCheckAllAsCheckbox($mass_id))] = '';
      $items[__('ID')]                 = "id";
      $items[__('Status')]             = "glpi_projectstates.name";
      $items[__('Date')]               = "date";
      $items[__('Last update')]        = "date_mod";

      if (count($_SESSION["glpiactiveentities"]) > 1) {
         $items[_n('Entity', 'Entities', Session::getPluralNumber())] = "glpi_entities.completename";
      }

      $items[__('Priority')]         = "priority";
      $items[__('Manager')]          = "users_id";
      $items[__('Manager group')]    = "groups_id";
      $items[__('Name')]             = "name";

      foreach ($items as $key => $val) {
         $issort = 0;
         $link   = "";
         echo Search::showHeaderItem($output_type, $key, $header_num, $link);
      }

      // End Line for column headers
      echo Search::showEndLine($output_type);
   }


   /**
    * Display a line for an object
    *
    * @since version 0.85 (befor in each object with differents parameters)
    *
    * @param $id                 Integer  ID of the object
    * @param $options            array    of options
    *      output_type            : Default output type (see Search class / default Search::HTML_OUTPUT)
    *      row_num                : row num used for display
    *      type_for_massiveaction : itemtype for massive action
    *      id_for_massaction      : default 0 means no massive action
    *      followups              : only for Tickets : show followup columns
    */
   static function showShort($id, $options = []) {
      global $CFG_GLPI, $DB;

      $p['output_type']            = Search::HTML_OUTPUT;
      $p['row_num']                = 0;
      $p['type_for_massiveaction'] = 0;
      $p['id_for_massiveaction']   = 0;

      if (count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      $rand = mt_rand();

      // Prints a job in short form
      // Should be called in a <table>-segment
      // Print links or not in case of user view
      // Make new job object and fill it from database, if success, print it
      $item        = new static();

      $candelete   = static::canDelete();
      $canupdate   = Session::haveRight(static::$rightname, UPDATE);
      $align       = "class='center";
      $align_desc  = "class='left";

      $align      .= "'";
      $align_desc .= "'";

      if ($item->getFromDB($id)) {
         $item_num = 1;
         $bgcolor  = $_SESSION["glpipriority_".$item->fields["priority"]];

         echo Search::showNewLine($p['output_type'], $p['row_num']%2);

         $check_col = '';
         if (($candelete || $canupdate)
             && ($p['output_type'] == Search::HTML_OUTPUT)
             && $p['id_for_massiveaction']) {

            $check_col = Html::getMassiveActionCheckBox($p['type_for_massiveaction'],
                                                        $p['id_for_massiveaction']);
         }
         echo Search::showItem($p['output_type'], $check_col, $item_num, $p['row_num'], $align);

         $id_col = $item->fields["id"];
         echo Search::showItem($p['output_type'], $id_col, $item_num, $p['row_num'], $align);
         // First column
         $first_col = '';
         $color     = '';
         if ($item->fields["projectstates_id"]) {
            $query = "SELECT `color`
                      FROM `glpi_projectstates`
                      WHERE `id` = '".$item->fields["projectstates_id"]."'";
            foreach ($DB->request($query) as $color) {
               $color = $color['color'];
            }
            $first_col = Dropdown::getDropdownName('glpi_projectstates', $item->fields["projectstates_id"]);
         }
         echo Search::showItem($p['output_type'], $first_col, $item_num, $p['row_num'],
                               "$align bgcolor='$color'");

         // Second column
         $second_col = sprintf(__('Opened on %s'),
                               ($p['output_type'] == Search::HTML_OUTPUT?'<br>':'').
                                 Html::convDateTime($item->fields['date']));

         echo Search::showItem($p['output_type'], $second_col, $item_num, $p['row_num'],
                               $align." width=130");

         // Second BIS column
         $second_col = Html::convDateTime($item->fields["date_mod"]);
         echo Search::showItem($p['output_type'], $second_col, $item_num, $p['row_num'],
                               $align." width=90");

         // Second TER column
         if (count($_SESSION["glpiactiveentities"]) > 1) {
            $second_col = Dropdown::getDropdownName('glpi_entities', $item->fields['entities_id']);
            echo Search::showItem($p['output_type'], $second_col, $item_num, $p['row_num'],
                                  $align." width=100");
         }

         // Third Column
         echo Search::showItem($p['output_type'],
                               "<span class='b'>".
                                 CommonITILObject::getPriorityName($item->fields["priority"]).
                                 "</span>",
                               $item_num, $p['row_num'], "$align bgcolor='$bgcolor'");

         // Fourth Column
         $fourth_col = "";

         if ($item->fields["users_id"]) {
            $userdata    = getUserName($item->fields["users_id"], 2);
            $fourth_col .= sprintf(__('%1$s %2$s'),
                                   "<span class='b'>".$userdata['name']."</span>",
                                    Html::showToolTip($userdata["comment"],
                                                      ['link'    => $userdata["link"],
                                                            'display' => false]));
         }

         echo Search::showItem($p['output_type'], $fourth_col, $item_num, $p['row_num'], $align);

         // Fifth column
         $fifth_col = "";

         if ($item->fields["groups_id"]) {
            $fifth_col .= Dropdown::getDropdownName("glpi_groups", $item->fields["groups_id"]);
            $fifth_col .= "<br>";
         }

         echo Search::showItem($p['output_type'], $fifth_col, $item_num, $p['row_num'], $align);

         // Eigth column
         $eigth_column = "<span class='b'>".$item->fields["name"]."</span>&nbsp;";

         // Add link
         if ($item->canViewItem()) {
            $eigth_column = "<a id='".$item->getType().$item->fields["id"]."$rand' href=\"".
                              $item->getLinkURL()."&amp;forcetab=Project$\">$eigth_column</a>";
         }

         if ($p['output_type'] == Search::HTML_OUTPUT) {
            $eigth_column = sprintf(__('%1$s %2$s'), $eigth_column,
                                    Html::showToolTip($item->fields['content'],
                                                      ['display' => false,
                                                            'applyto' => $item->getType().
                                                                           $item->fields["id"].
                                                                           $rand]));
         }

         echo Search::showItem($p['output_type'], $eigth_column, $item_num, $p['row_num'],
                               $align_desc."width='200'");

         // Finish Line
         echo Search::showEndLine($p['output_type']);
      } else {
         echo "<tr class='tab_bg_2'>";
         echo "<td colspan='6' ><i>".__('No item in progress.')."</i></td></tr>";
      }
   }

   function prepareInputForAdd($input) {

      if (isset($input["id"]) && ($input["id"] > 0)) {
         $input["_oldID"] = $input["id"];
      }
      unset($input['id']);
      unset($input['withtemplate']);

      return $input;
   }


   function prepareInputForUpdate($input) {
      return self::checkPlanAndRealDates($input);
   }


   static function checkPlanAndRealDates($input) {

      if (isset($input['plan_start_date']) && !empty($input['plan_start_date'])
          && isset($input['plan_end_date']) && !empty($input['plan_end_date'])
          && (($input['plan_end_date'] < $input['plan_start_date'])
              || empty($input['plan_start_date']))) {
         Session::addMessageAfterRedirect(__('Invalid planned dates. Dates not updated.'), false,
                                          ERROR);
         unset($input['plan_start_date']);
         unset($input['plan_end_date']);
      }
      if (isset($input['real_start_date']) && !empty($input['real_start_date'])
          && isset($input['real_end_date']) && !empty($input['real_end_date'])
          && (($input['real_end_date'] < $input['real_start_date'])
              || empty($input['real_start_date']))) {
         Session::addMessageAfterRedirect(__('Invalid real dates. Dates not updated.'), false,
                                          ERROR);
         unset($input['real_start_date']);
         unset($input['real_end_date']);
      }
      return $input;
   }


   /**
    * Print the HTML array children of a TreeDropdown
    *
    * @return Nothing (display)
    **/
   function showChildren() {
      global $DB, $CFG_GLPI;

      $ID   = $this->getID();
      $this->check($ID, READ);
      $rand = mt_rand();

      $query = "SELECT *
                FROM `".$this->getTable()."`
                WHERE `".$this->getForeignKeyField()."` = '$ID'
                AND `is_deleted`=0";
      if ($result = $DB->query($query)) {
         $numrows = $DB->numrows($result);
      }

      if ($this->can($ID, UPDATE)) {
         echo "<div class='firstbloc'>";
         echo "<form name='project_form$rand' id='project_form$rand' method='post'
         action='".Toolbox::getItemTypeFormURL(__CLASS__)."'>";

         echo "<a href='".Toolbox::getItemTypeFormURL('Project')."?projects_id=$ID'>";
         echo __('Create a sub project from this project');
         echo "</a>";
         Html::closeForm();
         echo "</div>";
      }

      echo "<div class='spaced'>";
      echo "<table class='tab_cadre_fixehov'>";
      echo "<tr class='noHover'><th colspan='12'>".Project::getTypeName($numrows)."</th></tr>";
      if ($numrows) {
         Project::commonListHeader();
         Session::initNavigateListItems('Project',
                                 //TRANS : %1$s is the itemtype name,
                                 //        %2$s is the name of the item (used for headings of a list)
                                         sprintf(__('%1$s = %2$s'), Project::getTypeName(1),
                                                 $this->fields["name"]));

         $i = 0;
         while ($data = $DB->fetch_assoc($result)) {
            Session::addToNavigateListItems('Project', $data["id"]);
            Project::showShort($data['id'], ['row_num' => $i]);
            $i++;
         }
         Project::commonListHeader();
      }
      echo "</table>";
      echo "</div>\n";
   }


   /**
    * Print the computer form
    *
    * @param $ID        integer ID of the item
    * @param $options   array
    *     - target for the Form
    *     - withtemplate template or basic computer
    *
    *@return Nothing (display)
   **/
   function showForm($ID, $options = []) {
      global $CFG_GLPI, $DB;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);
      
        //Sector
        $opt2['comments']= false;
        $opt2['addicon']= false;
        $opt2['width']='203px';
        if(!empty($this->fields["plugin_comproveedores_experiencestypes_id"])){
              $opt2['value']= $this->fields["plugin_comproveedores_experiencestypes_id"];      
        }
              
        //Comunidades
        $opt3['comments']= false;
        $opt3['addicon']= false;
        $opt3['width']='203px';
        $opt3['specific_tags']=array('onchange' => 'cambiarProvincia(value, false)');
        if(!empty($this->fields["plugin_comproveedores_communities_id"])){
                $opt3['value']= $this->fields["plugin_comproveedores_communities_id"];
        }
        
        //Tipo de  servicio
        $opt5['comments']= false;
        $opt5['width']='203px';
        $opt5['addicon']= false;
        if(!empty($this->fields["plugin_comproveedores_servicetypes_id"])){
                $opt5['value']= $this->fields["plugin_comproveedores_servicetypes_id"];
        }

        echo"<script type='text/javascript'>

                $(function() {
                        
                    if('".$this->fields['plugin_comproveedores_communities_id']."'!=''){
                        cambiarProvincia('".$this->fields['plugin_comproveedores_communities_id']."', true);        
                    }

                    //Añadimos gestor de proyecto a la página principal de proyecto solo si ya esta creado el proyecto
                    if('".$ID."'!=''){
                        $.ajax({  
                            type: 'GET',        		
                            url:'".$CFG_GLPI["root_doc"]."/ajax/common.tabs.php?_target=".$CFG_GLPI["root_doc"]."/front/project.form.php&_itemtype=Project&_glpi_tab=ProjectTeam$1&id=".$ID."&tab_projecto=true&withtemplate=',
                            success:function(data){
                                $('#usuarioGestor').html(data);
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                        });
                    }
                });
                
                function numberFormat(numero){
                    var resultado = '';

                    if(numero[0]=='-')
                    {  
                        nuevoNumero=numero.replace(/\./g,'').substring(1);
                    }else{
                        nuevoNumero=numero.replace(/\./g,'');
                    }
                    if(numero.indexOf(',')>=0)
                        nuevoNumero=nuevoNumero.substring(0,nuevoNumero.indexOf(','));

                    for (var j, i = nuevoNumero.length - 1, j = 0; i >= 0; i--, j++)
                        resultado = nuevoNumero.charAt(i) + ((j > 0) && (j % 3 == 0)? '.': '') + resultado;

                    if(numero.indexOf(',')>=0)
                        resultado+=numero.substring(numero.indexOf(','));

                    if(numero[0]=='-')
                    {
                        return '-'+resultado;
                    }else{
                        return resultado;
                    }
                }              
                function cambiarProvincia(valor, cargar_pagina){

                    provincia=null;
                    //Comprobamos si tiene provincia el proyecto solo al entrar al cargar la pagina
                    if('".$this->fields['plugin_comproveedores_provinces_id']."'!='' && cargar_pagina){
                        provincia='".$this->fields['plugin_comproveedores_provinces_id']."'; 
                    }

                    var parametros = {
                        'idComunidad': valor,
                        'idProvincia':provincia
                    };

                    $.ajax({  
                            type: 'GET',        		
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/select_provinces.php',
                            data: parametros,
                            success:function(data){
                                $('#id_provincia').html(data);
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                    });
	}
      </script>";
         
      echo "<tr class='tab_bg_1'>";
      echo "<td rowspan='8'><img src='../pics/proyectos.png' style='margin-right: 40px;width: 100px;'/></td>";
      echo "<td>".__('Name')."</td>";
      echo "<td>";
      echo "<textarea id='name' name='name' cols='60' rows='4' style='resize: none' >".$this->fields["name"].
           "</textarea>";
      
      echo "</td>";
      
      echo "<td>".__('Code')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, 'code');
      echo "</td>";
      
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
         echo "<td>"._x('item', 'State')."</td>";
      echo "<td>";
      ProjectState::dropdown(['value' => $this->fields["projectstates_id"]]);
      echo "</td>";
      
        echo "<td>".__('Show on global GANTT')."</td>";
      echo "<td>";
      Dropdown::showYesNo("show_on_global_gantt", $this->fields["show_on_global_gantt"]);
      echo "</td>";

      echo "<tr class='tab_bg_1'>";
      
       echo "<td>".__('Fecha de comienzo')."</td>";
      echo "<td >";
      Html::showDateMounth("plan_start_date", $this->fields['plan_start_date'], 1997, 2100, '', true);
      //Html::showDateTimeField("plan_start_date", ['value' => $this->fields['plan_start_date'], 'hidetime' => true], 1);
      echo "</td>";
      
      echo "<td>Ubicación </td>";
      echo "<td>";
      Dropdown::show('PluginComproveedoresCommunity',$opt3);
      echo "</td>";
      
      echo "</tr>";
      echo "<tr>";
      echo "</td>";
      
      echo "<tr class='tab_bg_1'>";
      
      echo "<td>".__('Fecha de finalización')."</td>";
      echo "<td>";
        //Html::showDateTimeField("plan_end_date", ['value' => $this->fields['plan_end_date'], 'hidetime' => true], 0);
        Html::showDateMounth("plan_end_date", $this->fields['plan_end_date'], 1997, 2100, '', true);
      echo "</td>";
     
       echo "<td>Provincia</td>";
        echo "<td>";
                echo "<div id='id_provincia'>";
                        echo "<span class='no-wrap'><div class='select2-container'><a class='select2-choice'><span class='select2-chosen'>------</span></a></div></span>";
                echo "</div>";
        echo "</td>";
      
      echo"</tr>";
      
      echo "<tr class='tab_bg_1'>";
     
      echo "<td>Sector</td>";
      echo "<td>";
          Dropdown::show('PluginComproveedoresExperiencestype', $opt2);
      echo "</td>";     
  
      echo "<td>Localidad</td>";
      echo "<td>";
      Html::autocompletionTextField($this, 'localidad');
      echo "</td>";
	  echo "</tr>";
      
      $rand_importe = '2854679';
      echo "<tr>";
           echo "<td>Tipo de servicio</td>";
           echo "<td>";
                   Dropdown::show('PluginComproveedoresServicetype',$opt5);
           echo "</td>";
           echo "<td>Coste de Construcción</td>";
           echo "<td>";

                    $importe='0';
                    if(!empty($this->fields['importe_proyecto'])){
                       $importe=number_format($this->fields['importe_proyecto'], 0, '', '.');
                    }else{$importe='0';}           
                    //echo "<input id='textfield_importe_proyecto{$rand_importe}' name='importe_proyecto' value='{$importe}' />";
                    Html::autocompletionTextField($this, "importe_proyecto", ['rand' => $rand_importe, 'width' => '80']);
           echo " €</td>";		   
      echo "</tr>";
      

      echo "<script type='text/javascript'>
                $('#textfield_importe_proyecto{$rand_importe}').val('{$importe}');
                $('#textfield_importe_proyecto{$rand_importe}').on('blur', function() {
                    var valor = numberFormat($('#textfield_importe_proyecto{$rand_importe}').val());
                    $('#textfield_importe_proyecto{$rand_importe}').val(valor);
                });	
            </script>";
                
      echo"</tr>";
      
      echo"<tr>";
      echo"<td>Cliente</td><td>";
	  ProjectCliente::dropdown(['value' => $this->fields["projectclientes_id"]], ['style' => 'width: 200px;']);
      echo"</td>";
      echo "<td></td>";
      echo "<td>";

      echo "</td>";	  
      echo"</tr>";
      $options['colspan']=6;
      $this->showFormButtons($options);
      return true;
   }


   static function getSpecificValueToDisplay($field, $values, array $options = []) {

      if (!is_array($values)) {
         $values = [$field => $values];
      }
      switch ($field) {
         case 'priority':
            return CommonITILObject::getPriorityName($values[$field]);
      }
      return parent::getSpecificValueToDisplay($field, $values, $options);
   }


   /**
    * @since version 0.85
    *
    * @param $field
    * @param $name            (default '')
    * @param $values          (default '')
    * @param $options   array
   **/
   static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = []) {

      if (!is_array($values)) {
         $values = [$field => $values];
      }
      $options['display'] = false;

      switch ($field) {
         case 'priority' :
            $options['name']      = $name;
            $options['value']     = $values[$field];
            $options['withmajor'] = 1;
            return CommonITILObject::dropdownPriority($options);
      }
      return parent::getSpecificValueToSelect($field, $name, $values, $options);
   }

    
     static function showElegirGestor(Project $project) {
       GLOBAL $DB,$CFG_GLPI;
       
       $id_projecto=$project->fields['id'];

        //Buscamos el usuario gestor de este proyecto     
       $query ="SELECT usuarios.name as nombre_usuario,  gestor_proyecto.itemtype
                    FROM glpi_projectteams as gestor_proyecto 
                    LEFT JOIN glpi_users as usuarios on usuarios.id=gestor_proyecto.items_id
                    LEFT JOIN glpi_profiles_users as perfil_usuario on usuarios.id=perfil_usuario.users_id
                    where gestor_proyecto.projects_id=".$id_projecto." and perfil_usuario.profiles_id=16 and gestor_proyecto.gerente=1";
          
     
        $result = $DB->query($query);
        
        //Si no tiene usuario gestor, que visualice un desplegable con los usuarios de perfil estrategico
        if($result->num_rows==0){
                $query2 ="SELECT usuarios.id as id_usuario, usuarios.name as nombre_usuario
                        FROM glpi_profiles_users as perfil_usuario
                        LEFT JOIN glpi_users as usuarios on usuarios.id=perfil_usuario.users_id
                        where perfil_usuario.profiles_id=16";
                
                $result2 = $DB->query($query2);
                $arrayUsuarios=array();
                
                while ($data=$DB->fetch_array($result2)) {
                               
                        $arrayUsuarios[$data['id_usuario']]=$data['nombre_usuario'];
                }
                
                echo"<form action=".$CFG_GLPI["root_doc"]."/front/projectteam.form.php method='post'>";
                
                        echo Html::hidden('projects_id', array('value' => $id_projecto));
                        echo Html::hidden('gerente', array('value' => 1));
                        echo Html::hidden('itemtype', array('value' => 'User'));
                        echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                
                        echo "<div style='display: inline-block; margin-right: 10px;'>Gestor del proyecto</div>";
                        Dropdown::showFromArray('items_id',$arrayUsuarios);

                        echo "<input style='margin-left:20px;' type='submit' class='submit' name='add' value='AÑADIR' />";
                
                echo"</form>";
        }
        
        //Si tiene un usuario gesto que se visualize en una lista
        while ($data=$DB->fetch_array($result)) {
                 if($result->num_rows!=0){
                    echo "<div  align='left'><table class='tab_cadre_fixehov'>";
                                echo"<span style='font-weight:bold; margin-left:10px; margin-right:10px;'>Usuario Gestor:</span>".$data['nombre_usuario'];
                    echo"</div>";
                }
        }
   }

   function proyectosUsuario($id){
       global $CFG_GLPI, $DB;
       
       $prj = [];
       //unset($_SESSION['proyectosUsuario']);
       $objCommonDBT=new CommonDBTM;
       $sql = "select projects_id from glpi_projectteams where items_id = ".$id;
       $result = $DB->query($sql);
       while ($data = $DB->fetch_array($result)) {       
           array_push($prj , $data['projects_id'] );
       }
       return $prj;
   }     
   
   /**
    * Show team for a project
   **/
   function showTeam(Project $project) {
      global $DB, $CFG_GLPI;

    $ID      = $project->fields['id'];
    $canedit = $project->can($ID, UPDATE);
      
    $user_Id             = $_SESSION['glpiID'];
    $profile_Id          = $this->getProfileByUserID($user_Id);
    $ver                 = true;
    $arrayPrj            = $this->proyectosUsuario($user_Id);    
    
    if(in_array($profile_Id, array(3,4,16)) || ($profile_Id == 14 && in_array($ID, $arrayPrj))){    
        $ver = true;
        echo "<input id='verMiembros' type='hidden' value='1' />";
    }else{
        $ver = false;
        echo "<input id='verMiembros' type='hidden' value='0' />";
    }       
      
      echo "<div class='center' style='height: 450px;
    margin-bottom: 5px;
    float: left;
    position: relative;
    width: 90%;
    background-color: #e9ecf3;
    border-radius: 4px;
    padding: 10px;
    overflow-y: auto;'>";

      $rand = mt_rand();
      $nb   = 0;
      $nb   = $project->getTeamCount();

      if ($canedit && $ver) {
          
         echo "<div class='firstbloc'>";
         echo "<form name='projectteam_form$rand' id='projectteam_form$rand' ";
         echo " method='post' action='".Toolbox::getItemTypeFormURL('ProjectTeam')."'>";
         echo "<input type='hidden' name='projects_id' value='$ID'>";
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_1'><th colspan='2'>".__('Add a team member')."</tr>";
         echo "<tr class='tab_bg_2'><td>";

          $params = ['itemtypes'       => array(0 => 'User'),
                         'entity_restrict' => ($project->fields['is_recursive']
                                               ? getSonsOf('glpi_entities',
                                                           $project->fields['entities_id'])
                                               : $project->fields['entities_id']),
                         ];
         
         /*$params = ['itemtypes'       => ProjectTeam::$available_types,
                         'entity_restrict' => ($project->fields['is_recursive']
                                               ? getSonsOf('glpi_entities',
                                                           $project->fields['entities_id'])
                                               : $project->fields['entities_id']),
                         ];*/

         $addrand = Dropdown::showSelectItemFromItemtypes($params);

         echo "</td>";
         echo "<td width='20%'>";
         echo "<input type='submit' name='add' value=\""._sx('button', 'Add')."\"
               class='submit'>";
         echo "</td>";
         echo "</tr>";
         echo "</table>";
         Html::closeForm();
         echo "</div>";
      }
      echo "<div class='spaced'>";
      if ($canedit && $nb && $ver) {
         Html::openMassiveActionsForm('mass'.__CLASS__.$rand);
         $massiveactionparams = ['num_displayed' => min($_SESSION['glpilist_limit'], $nb),
                                      'container'     => 'mass'.__CLASS__.$rand];
         Html::showMassiveActions($massiveactionparams);
      }
     
      echo "<table class='tab_cadre_fixehov'>";
      $header_begin  = "<tr>";
      $header_top    = '';
      $header_bottom = '';
      $header_end    = '';
      if ($canedit && $nb) {
         $header_begin    .= "<th width='10'>";
         $header_top    .= Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_bottom .= Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_end    .= "</th>";
      }
      $header_end .= "<th>".__('Type')."</th>";
      $header_end .= "<th>"._n('Member', 'Members', Session::getPluralNumber())."</th>";
      $header_end .= "<th>Gerente</th>";
      $header_end .= "</tr>";
      echo $header_begin.$header_top.$header_end;

      foreach (ProjectTeam::$available_types as $type) {
         if (isset($project->team[$type]) && count($project->team[$type])) {
            if ($item = getItemForItemtype($type)) {
               foreach ($project->team[$type] as $data) {
                  $item->getFromDB($data['items_id']);
                  echo "<tr class='tab_bg_2'>";
                  if ($canedit) {
                     echo "<td>";
                     Html::showMassiveActionCheckBox('ProjectTeam', $data["id"]);
                     echo "</td>";
                  }                
                  echo "<td>".$item->getTypeName(1)."</td>";
                  echo "<td style='font-size: 14px!important;'>".$item->getLink()."</td>";
                  if($ver){
                    if($data['gerente']==1){
                          echo "<td class='center'><input id='chkGerente_{$data['id']}' class='chkgerencia' type='checkbox'  style='cursor: pointer; vertical-align:middle; width: 18px; height: 18px;' checked></td>";
                    }
                    else{
                          echo "<td class='center'><input id='chkGerente_{$data['id']}'  class='chkgerencia' type='checkbox' style='cursor: pointer; vertical-align:middle; width: 18px; height: 18px;'></td>";
                    }
                  }else{
                      if($data['gerente']==1){
                          echo "<td class='center'><image style='width: 15px;margin:10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK.png' /></td>";
                      }else{
                          echo "<td class='center'></td>";
                      }
                  }
                  echo "</tr>";
               }
            }
         }
      }
     /* if ($nb) {
         echo $header_begin.$header_bottom.$header_end;
      }*/

      echo "</table>";
      /*if ($canedit && $nb) {
         $massiveactionparams['ontop'] =false;
         Html::showMassiveActions($massiveactionparams);
         Html::closeForm();
      }*/

      echo "</div>";
      
      echo "<script type='text/javascript'>
          $('.chkgerencia').on('click', function() {
                var x = $('#verMiembros').val();
                if(x == 1){
                    var ger = 0;
                    var contador = 0;
                    if($(this).prop('checked')){
                        ger = 1;
                    }
                    $('.chkgerencia').each(function(){
                            if($(this).is(':checked'))
                                    contador++;
                    });                    
                    if(contador<2){
                        var idteam = $(this).attr('id').replace('chkGerente_','');
                        var parametros = {
                            id: idteam,
                            gerente: ger};
                        $.ajax({  
                            type: 'GET',        		
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/cambiarGerente.php',
                            data: parametros,   		
                            success:function(data){},
                            error: function(result) {
                                alert('Data not found');
                            }
                        });      
                    }else{
                        alert('Solo puede existir un gerente por proyecto.');
                    }
                }else{
                    alert('No posee permisos para esta funcionalidad');
                }
          });
      </script>";
      // Add items

      return true;
   }


   /** Get data to display on GANTT
    *
   * @param $ID        integer   ID of the project
   * @param $showall   boolean   show all sub items (projects / tasks) (true by default)
   */
   static function getDataToDisplayOnGantt($ID, $showall = true) {
      global $DB;

      $todisplay = [];
      $project   = new self();
      if ($project->getFromDB($ID)) {
         $projects = [];
         foreach ($DB->request('glpi_projects', ['projects_id' => $ID]) as $data) {
            $projects += static::getDataToDisplayOnGantt($data['id']);
         }
         ksort($projects);
         // Get all tasks
         $tasks      = ProjectTask::getAllForProject($ID);

         $real_begin = null;
         $real_end   = null;
         // Use real if set
         if (is_null($project->fields['real_start_date'])) {
            $real_begin = $project->fields['real_start_date'];
         }

         // Determine begin / end date of current project if not set (min/max sub projects / tasks)
         if (is_null($real_begin)) {
            if (!is_null($project->fields['plan_start_date'])) {
               $real_begin = $project->fields['plan_start_date'];
            } else {
               foreach ($tasks as $task) {
                  if (is_null($real_begin)
                      || (!is_null($task['plan_start_date'])
                          && ($real_begin > $task['plan_start_date']))) {
                     $real_begin = $task['plan_start_date'];
                  }
               }
               foreach ($projects as $p) {
                  if (is_null($real_begin)
                      || (($p['type'] == 'project')
                          && !is_null($p['from'])
                          && ($real_begin > $p['from']))) {
                     $real_begin = $p['from'];
                  }
               }
            }
         }

         // Use real if set
         if (!is_null($project->fields['real_end_date'])) {
            $real_end = $project->fields['real_end_date'];
         }
         if (is_null($real_end)) {
            if (!is_null($project->fields['plan_end_date'])) {
               $real_end = $project->fields['plan_end_date'];
            } else {
               foreach ($tasks as $task) {
                  if (is_null($real_end)
                      || (!is_null($task['plan_end_date'])
                          && ($real_end < $task['plan_end_date']))) {
                     $real_end = $task['plan_end_date'];
                  }
               }
               foreach ($projects as $p) {
                  if (is_null($real_end)
                      || (($p['type'] == 'project')
                          && !is_null($p['to'])
                          && ($real_end < $p['to']))) {
                     $real_end = $p['to'];
                  }
               }
            }
         }

         // Add current project
         $todisplay[$real_begin.'#'.$real_end.'#project'.$project->getID()]
                      = ['id'       => $project->getID(),
                              'name'     => $project->fields['name'],
                              'link'     => $project->getLink(),
                              'desc'     => $project->fields['content'],
                              'percent'  => isset($project->fields['percent_done'])?$project->fields['percent_done']:0,
                              'type'     => 'project',
                              'from'     => $real_begin,
                              'to'       => $real_end];

         if ($showall) {
            // Add current tasks
            $todisplay += ProjectTask::getDataToDisplayOnGanttForProject($ID);

            // Add ordered subprojects
            foreach ($projects as $key => $val) {
               $todisplay[$key] = $val;
            }
         }
      }

      return $todisplay;
   }


   /** show GANTT diagram for a project or for all
    *
   * @param $ID ID of the project or -1 for all projects
   */
   static function showGantt($ID) {
      global $DB;

      if ($ID > 0) {
         $project = new Project();
         if ($project->getFromDB($ID) && $project->canView()) {
            $todisplay = static::getDataToDisplayOnGantt($ID);
         } else {
            return false;
         }
      } else {
         $todisplay = [];
         // Get all root projects
         $query = "SELECT *
                   FROM `glpi_projects`
                   WHERE `projects_id` = '0'
                        AND `show_on_global_gantt` = '1'
                        AND NOT `is_template`
                         ".getEntitiesRestrictRequest("AND", 'glpi_projects', "", '', true);
         foreach ($DB->request($query) as $data) {
            $todisplay += static::getDataToDisplayOnGantt($data['id'], false);
         }
         ksort($todisplay);
      }

      $data    = [];
      $invalid = [];
      if (count($todisplay)) {

         // Prepare for display
         foreach ($todisplay as $key => $val) {
            if (!empty($val['from']) && !empty($val['to'])) {
               $temp  = [];
               $color = 'ganttRed';
               if ($val['percent'] > 50) {
                  $color = 'ganttOrange';
               }
               if ($val['percent'] == 100) {
                  $color = 'ganttGreen';
               }
               switch ($val['type']) {
                  case 'project' :
                     $temp = ['name'   => $val['link'],
                                   'desc'   => '',
                                   'values' => [['from'
                                                            => "/Date(".strtotime($val['from'])."000)/",
                                                           'to'
                                                            => "/Date(".strtotime($val['to'])."000)/",
                                                           'desc'
                                                            => $val['desc'],
                                                         'label'
                                                            => $val['percent']."%",
                                                         'customClass'
                                                            => $color]]
                                 ];
                     break;

                  case 'task' :
                     if (isset($val['is_milestone']) && $val['is_milestone']) {
                        $color = 'ganttMilestone';
                     }
                     $temp = ['name'   => ' ',
                                   'desc'   => str_repeat('-', $val['parents']).$val['link'],
                                   'values' => [['from'
                                                            => "/Date(".strtotime($val['from'])."000)/",
                                                           'to'
                                                            => "/Date(".strtotime($val['to'])."000)/",
                                                           'desc'
                                                            => $val['desc'],
                                                           'label'
                                                            => strlen($val['percent']==0)?'':$val['percent']."%",
                                                           'customClass'
                                                            => $color]]
                                 ];
                     break;
               }
               $data[] = $temp;
            } else {
               $invalid[] = $val['link'];
            }
         }
         // Html::printCleanArray($data);
      }

      if (count($invalid)) {
         echo sprintf(__('Invalid items (no start or end date): %s'), implode(',', $invalid));
         echo "<br><br>";
      }

      if (count($data)) {
         $months = [__('January'), __('February'), __('March'), __('April'), __('May'),
                         __('June'), __('July'), __('August'), __('September'),
                         __('October'), __('November'), __('December')];

         $dow    = [Toolbox::substr(__('Sunday'), 0, 1), Toolbox::substr(__('Monday'), 0, 1),
                         Toolbox::substr(__('Tuesday'), 0, 1), Toolbox::substr(__('Wednesday'), 0, 1),
                         Toolbox::substr(__('Thursday'), 0, 1), Toolbox::substr(__('Friday'), 0, 1),
                         Toolbox::substr(__('Saturday'), 0, 1)
                     ];

         echo "<div class='gantt'></div>";
         $js = "
                           $(function() {
                              $('.gantt').gantt({
                                    source: ".json_encode($data).",
                                    navigate: 'scroll',
                                    maxScale: 'months',
                                    itemsPerPage: 20,
                                    months: ".json_encode($months).",
                                    dow: ".json_encode($dow).",
                                    onItemClick: function(data) {
                                    //    alert('Item clicked - show some details');
                                    },
                                    onAddClick: function(dt, rowId) {
                                    //    alert('Empty space clicked - add an item!');
                                    },
                              });
                           });";
         echo Html::scriptBlock($js);
      } else {
         echo __('No item to display');
      }
   }

   /**
    * Display debug information for current object
   **/
   function showDebug() {
      NotificationEvent::debugEvent($this);
   }
}
