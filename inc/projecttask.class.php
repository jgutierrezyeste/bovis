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
 * ProjectTask Class
 *
 * @since version 0.85
**/
class ProjectTask extends CommonDBChild {

   // From CommonDBTM
   public $dohistory = true;

   // From CommonDBChild
   static public $itemtype     = 'Project';
   static public $items_id     = 'projects_id';

   protected $team             = [];
   static $rightname           = 'projecttask';
   protected $usenotepad       = true;

   public $can_be_translated   = true;

   const READMY      = 1;
   const UPDATEMY    = 1024;



   static function getTypeName($nb=0) {
           return _n('Contrato', 'Contratos', $nb);
   }


   static function canPurge() {
      return static::canChild('canUpdate');
   }


   static function canView() {

      return (Session::haveRightsOr('project', [Project::READALL, Project::READMY])
              || Session::haveRight(self::$rightname, ProjectTask::READMY));
   }



   /**
    * Is the current user have right to show the current task ?
    *
    * @return boolean
   **/
   function canViewItem() {

      if (!Session::haveAccessToEntity($this->getEntityID())) {
         return false;
      }
      $project = new Project();
      if ($project->getFromDB($this->fields['projects_id'])) {
         return (Session::haveRight('project', Project::READALL)
                 || (Session::haveRight('project', Project::READMY)
                     && (($project->fields["users_id"] === Session::getLoginUserID())
                         || $project->isInTheManagerGroup()
                         || $project->isInTheTeam()))
                 || (Session::haveRight(self::$rightname, self::READMY)
                     && (($this->fields["users_id"] === Session::getLoginUserID())
                         || $this->isInTheTeam())));
      }
      return false;
   }


   static function canCreate() {
      return (Session::haveRight('project', UPDATE));
   }


   static function canUpdate() {

      return (parent::canUpdate()
              || Session::haveRight(self::$rightname, self::UPDATEMY));
   }


   /**
    * Is the current user have right to edit the current task ?
    *
    * @return boolean
   **/
   function canUpdateItem() {

      if (!Session::haveAccessToEntity($this->getEntityID())) {
         return false;
      }
      $project = new Project();
      if ($project->getFromDB($this->fields['projects_id'])) {
         return (Session::haveRight('project', UPDATE)
                 || (Session::haveRight(self::$rightname, self::UPDATEMY)
                     && (($this->fields["users_id"] === Session::getLoginUserID())
                         || $this->isInTheTeam())));
      }
      return false;
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
    
   function cleanDBonPurge() {
      global $DB;

      $pt = new ProjectTaskTeam();
      $pt->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      $pt = new ProjectTask_Ticket();
      $pt->cleanDBonItemDelete(__CLASS__, $this->fields['id']);

      parent::cleanDBonPurge();
   }

   /**
    * Duplicate all tasks from a project template to his clone
    *
    * @since version 9.2
    *
    * @param integer $oldid        ID of the item to clone
    * @param integer $newid        ID of the item cloned
    **/
   static function cloneProjectTask ($oldid, $newid) {
      global $DB;

      foreach ($DB->request('glpi_projecttasks',
                            ['WHERE' => "projects_id = '$oldid'"]) as $data) {
         $cd                  = new self();
         unset($data['id']);
         $data['projects_id'] = $newid;
         $data                = Toolbox::addslashes_deep($data);
         $cd->add($data);
      }
   }

   /**
    * @see commonDBTM::getRights()
    **/
   function getRights($interface = 'central') {

      $values = parent::getRights();
      unset($values[READ], $values[CREATE], $values[UPDATE], $values[DELETE], $values[PURGE]);

      $values[self::READMY]   = __('See (actor)');
      $values[self::UPDATEMY] = __('Update (actor)');

      return $values;
   }


   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginComproveedoresSelectionSupplier', $ong, $options);            
      $this->addStandardTab('PluginComproveedoresValuation', $ong, $options);       
      //$this->addStandardTab('ProjectTask_Ticket', $ong, $options);      
      $this->addStandardTab('PluginComproveedoresSubpaquete', $ong, $options);
      $this->addStandardTab('Document_Item', $ong, $options);
      
      //$this->addStandardTab('ProjectTaskTeam', $ong, $options);
      //$this->addStandardTab('Notepad', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);


      return $ong;
   }


   function post_getFromDB() {
      // Team
      $this->team    = ProjectTaskTeam::getTeamFor($this->fields['id']);
   }

   function post_getEmpty() {
      $this->fields['percent_done'] = 0;
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

      // ADD Documents
      Document_Item::cloneItem('ProjectTaskTemplate',
                               $this->input["projecttasktemplates_id"],
                               $this->fields['id'],
                               $this->getType());

      if (!isset($this->input['_disablenotif']) && $CFG_GLPI["use_notifications"]) {
         // Clean reload of the project
         $this->getFromDB($this->fields['id']);

         NotificationEvent::raiseEvent('new', $this);
      }
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
    * Get team member count
    *
    * @return number
    */
   function getTeamCount() {

      $nb = 0;
      if (is_array($this->team) && count($this->team)) {
         foreach ($this->team as $val) {
            $nb +=  count($val);
         }
      }
      return $nb;
   }


   function pre_deleteItem() {
      global $CFG_GLPI;

      if (!isset($this->input['_disablenotif']) && $CFG_GLPI['use_notifications']) {
         NotificationEvent::raiseEvent('delete', $this);
      }
      return true;
   }


   function prepareInputForUpdate($input) {

      if (isset($input["plan"])) {
         $input["plan_start_date"] = $input['plan']["begin"];
         $input["plan_end_date"]   = $input['plan']["end"];
      }

      if (isset($input['is_milestone'])
            && $input['is_milestone']) {
         $input['plan_end_date'] = $input['plan_start_date'];
         $input['real_end_date'] = $input['real_start_date'];
      }
      return Project::checkPlanAndRealDates($input);
   }


   function prepareInputForAdd($input) {

      if (!isset($input['users_id'])) {
         $input['users_id'] = Session::getLoginUserID();
      }
      if (!isset($input['date'])) {
         $input['date'] = $_SESSION['glpi_currenttime'];
      }

      if (isset($input['is_milestone'])
            && $input['is_milestone']) {
         $input['plan_end_date'] = $input['plan_start_date'];
         $input['real_end_date'] = $input['real_start_date'];
      }

      return Project::checkPlanAndRealDates($input);
   }


    /**
    * Get all tasks for a project
    *
    * @param $ID        integer  Id of the project
    *
    * @return array of tasks ordered by dates
   **/
   static function getAllForProject($ID) {
      global $DB;

      $tasks = [];
      foreach ($DB->request('glpi_projecttasks',
                            ["projects_id" => $ID,
                                  'ORDER'       => ['plan_start_date',
                                                         'real_start_date']]) as $data) {
         $tasks[] = $data;
      }
      return $tasks;
   }


    /**
    * Get all linked tickets for a project
    *
    * @param $ID        integer  Id of the project
    *
    * @return array of tickets
   **/
   static function getAllTicketsForProject($ID) {
      global $DB;

      $tasks = [];
      foreach ($DB->request(['glpi_projecttasks_tickets', 'glpi_projecttasks'],
                            ["glpi_projecttasks.projects_id"
                                          => $ID,
                                  "glpi_projecttasks_tickets.projecttasks_id"
                                          => "glpi_projecttasks.id",
                                  'FIELDS' =>  "tickets_id" ])
                        as $data) {
         $tasks[] = $data['tickets_id'];
      }
      return $tasks;
   }


    function showFormSupplier($item) {
  
    }
   
   /**
    * Print the Project task form
    *
    * @param $ID        integer  Id of the project task
    * @param $options   array    of possible options:
    *     - target form target
    *     - projects_id ID of the software for add process
    *
    * @return true if displayed  false if item not found or not right to display
   **/
   function showForm($ID, $options = []) {
      global $CFG_GLPI;

      $rand_template           = mt_rand();
      $rand_name               = mt_rand();
      $rand_content            = mt_rand();
     // $rand_comment            = mt_rand();
      $rand_project            = mt_rand();
      $rand_state              = mt_rand();
      $rand_type               = mt_rand();
      $rand_ini                = mt_rand();
      $rand_fin                = mt_rand();
     // $rand_percent            = mt_rand();
     // $rand_milestone          = mt_rand();
      //$rand_plan_start_date    = mt_rand();
      //$rand_plan_end_date      = mt_rand();
    //  $rand_real_start_date    = mt_rand();
     // $rand_real_end_date      = mt_rand();
    //  $rand_effective_duration = mt_rand();
    //  $rand_planned_duration   = mt_rand();
      $rand_valor_contrato     = mt_rand();
      $rand_code               = mt_rand();

      if ($ID > 0) {
         $this->check($ID, READ);
         $projects_id     = $this->fields['projects_id'];
         $projecttasks_id = $this->fields['projecttasks_id'];
      } else {
         $this->check(-1, CREATE, $options);
         $projects_id     = $options['projects_id'];
         $projecttasks_id = $options['projecttasks_id'];
         $recursive       = $this->fields['is_recursive'];
      }

      $this->showFormHeader($options);
      
      echo Html::scriptBlock('
         function projecttasktemplate_update(value) {
            $.ajax({
               url: "' . $CFG_GLPI["root_doc"] . '/ajax/projecttask.php",
               type: "POST",
               data: {
                  projecttasktemplates_id: value
               }
            }).done(function(data) {
               // set input name
               $("#textfield_name'.$rand_name.'").val(data.name);
               // set textarea description
               $("#content'.$rand_content.'").val(data.content);
                // set textarea comment
               $("#comment'.$rand_comment.'").val(data.comments);
               // set project
               $("#dropdown_projects_id'.$rand_project.'").select2("val", data.projects_id);
               // set state
               $("#dropdown_projectstates_id'.$rand_state.'").select2("val", data.projectstates_id);
               // set type
               //$("#dropdown_projecttasktypes_id'.$rand_type.'").select2("val", data.projecttasktypes_id);
               // set plan_start_date
               //$("#plan_start_date_show'.$rand_plan_start_date.'").val(data.plan_start_date);
               $("#ini'.$rand_ini.'").val(data.ini);
               $("#fin'.$rand_ini.'").val(data.fin);
               // set plan_end_date
               //$("#plan_end_date_show'.$rand_plan_end_date.'").val(data.plan_end_date);		   
               // set effective_duration
               //$("#dropdown_effective_duration'.$rand_effective_duration.'").select2("val", data.effective_duration);
               // set planned_duration
               //$("#dropdown_planned_duration'.$rand_planned_duration.'").select2("val", data.planned_duration);

            });
         }
      ');

      echo "<tr class='tab_bg_1'><td>"._n('Project', 'Projects', Session::getPluralNumber())."</td>";
      echo "<td>";
      if ($this->isNewID($ID)) {
         echo "<input type='hidden' name='projects_id' value='$projects_id'>";
         echo "<input type='hidden' name='is_recursive' value='$recursive'>";
      }
      echo "<a href='project.form.php?id=".$projects_id."'>".
             Dropdown::getDropdownName("glpi_projects", $projects_id)."</a>";
      echo "</td>";
      
      $showuserlink = 0;
      if (Session::haveRight('user', READ)) {
         $showuserlink = 1;
      }
      
      if ($ID) {
        echo "<td>".__('Creation date')."</td>";
        echo "<td>";
        echo sprintf(__('%1$s by %2$s'), Html::convDateTime($this->fields["date"]),getUserName($this->fields["users_id"], $showuserlink));
        echo "</td>";
      }
      echo "<tr class='tab_bg_1'><td>".__('Name')."</td>";
      echo "<td>";
      echo "<textarea id='name$rand_name' name='name' cols='60' rows='4' style='resize: none'>".$this->fields["name"]."</textarea>";
      echo "</td>";
      echo"<td>".__('Code')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, "code", ['size' => 80, 'rand' => $rand_code]);
      echo "</td>";
      echo"</tr>\n";

      echo "<script type='text/javascript'>
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
      </script>";
      echo "<tr class='tab_bg_1'>";      
      echo"<td>".__('Presupuesto objetivo')."</td>";
      echo "<td>";

      $valor_contrato='0';
      if(!empty($this->fields['valor_contrato'])){
         $valor_contrato=number_format($this->fields['valor_contrato'], 0, '', '.');
      }else{$valor_contrato='0';}
      Html::autocompletionTextField($this, "valor_contrato", ['rand' => $rand_valor_contrato, 'width' => '80']);
      echo "  €</td>";

      echo "<script type='text/javascript'>
          
            $('#textfield_valor_contrato{$rand_valor_contrato}').val('{$valor_contrato}');
            $('#textfield_valor_contrato{$rand_valor_contrato}').on('blur', function() {
                var valor = numberFormat($('#textfield_valor_contrato{$rand_valor_contrato}').val());
                $('#textfield_valor_contrato{$rand_valor_contrato}').val(valor);
            });		
      </script>";
                
                
      echo "<td>"._x('item', 'State')."</td>";
      echo "<td>";
      $estado = 1;
      if($this->fields['projectstates_id']){
          $estado = $this->fields['projectstates_id'];
      }
      ProjectState::dropdown(['value' => $estado, 'rand'  => $rand_state]);
      echo "</td>";
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Fecha de comienzo')."</td>";
      echo "<td style='vertical-align:top;'>";
      Html::showDateMounth("ini", $this->fields['ini'], 1997, 2100, $rand_ini, true);
      echo "</td>";
      echo "<td>".__('Fecha de fin')."</td>";
      echo "<td>";
      Html::showDateMounth("fin", $this->fields['fin'], 1997, 2100, $rand_fin, true);											
      echo "</td></tr>";
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Descripción')."</td>";
      echo "<td>";
      echo "<textarea id='content$rand_content' name='content' cols='60' rows='4' style='resize: none'>".$this->fields["content"]."</textarea>";      
      echo"</td>";
      echo"<td>".__('Tipo de especialidad')."</td><td>";
      if($this->fields['id']!=''){
          $valorEspecialidad = "";
          $tipo_especialidad=$this->fields['tipo_especialidad'];
          switch ($tipo_especialidad) {
		Case 3: 
			$valorEspecialidad = "Servicios Profesionales / Consultores";
                  break;				
                Case 2:
                        $valorEspecialidad = "Contratistas";
                  break;

                Case 1:
                        $valorEspecialidad = "Proveedores";
                  break;
                Default:
                  break;
          }
          echo "<input id='tipoespecialidad' type='text' value='{$valorEspecialidad}' style='width: 200px;' readonly />";
          
      }else{
          Dropdown::showFromArray('tipo_especialidad',array(3 =>'Servicios Profesionales / Consultores' , 2 =>'Contratistas' , 1 =>'Proveedores'),array('value' => $this->fields['tipo_especialidad']));
      }
      echo "</td>";      
      echo "</tr>";
      
         echo "<tr class='tab_bg_1'>";
      echo "<td style='width:100px; visibility:hidden;'>"._n('Project task template', 'Project task templates', 1)."</td><td style='visibility:hidden;'>";  
           ProjectTaskTemplate::dropdown(['value'     => $this->fields['projecttasktemplates_id'],
                                     'entity'    => $this->getEntityID(),
                                     'rand'      => $rand_template,
                                     'on_change' => 'projecttasktemplate_update(this.value)']);
      echo "</td>";
      echo "</tr>";
	  echo "
	  <style>
		 .ui-datepicker-calendar{
			 display: none;
		 }
		 .ui-timepicker-div{
			 display: none;
		 }
	  </style>";
        /**
        echo "<script type='text/javascript'>
            $(document).ready(function(){
                $('#tipo').html('especialidad: {$valorEspecialidad}');
            });
         </script>";**/
      $options['colspan']=2;
      $this->showFormButtons($options);

      return true;
   }


   /**
    * Get total effective duration of a project task (sum of effective duration + sum of action time of tickets)
    *
    * @param $projecttasks_id    integer    $projecttasks_id ID of the project task
    *
    * @return integer total effective duration
   **/
   static function getTotalEffectiveDuration($projecttasks_id) {
      global $DB;

      $item = new static();
      $time = 0;

      if ($item->getFromDB($projecttasks_id)) {
         $time += $item->fields['effective_duration'];
      }
      $query = "SELECT SUM(glpi_tickets.actiontime)
                FROM glpi_projecttasks
                LEFT JOIN glpi_projecttasks_tickets
                   ON (glpi_projecttasks.id = glpi_projecttasks_tickets.projecttasks_id)
                LEFT JOIN glpi_tickets
                   ON (glpi_projecttasks_tickets.tickets_id = glpi_tickets.id)
                WHERE glpi_projecttasks.id = '$projecttasks_id';";

      if ($result = $DB->query($query)) {
         if ($DB->numrows($result)) {
            $time += $DB->result($result, 0, 0);
         }
      }
      return $time;
   }


   /**
    * Get total effective duration of a project (sum of effective duration + sum of action time of tickets)
    *
    * @param $projects_id    integer    $project_id ID of the project
    *
    * @return integer total effective duration
   **/
   static function getTotalEffectiveDurationForProject($projects_id) {
      global $DB;

      $query = "SELECT id
                FROM glpi_projecttasks
                WHERE glpi_projecttasks.projects_id = '$projects_id';";
      $time = 0;
      foreach ($DB->request($query) as $data) {
         $time += static::getTotalEffectiveDuration($data['id']);
      }
      return $time;
   }


   /**
    * Get total planned duration of a project
    *
    * @param $projects_id    integer    $project_id ID of the project
    *
    * @return integer total effective duration
   **/
   static function getTotalPlannedDurationForProject($projects_id) {
      global $DB;

      $query = "SELECT SUM(planned_duration) as SUM
                FROM glpi_projecttasks
                WHERE glpi_projecttasks.projects_id = '$projects_id';";
      foreach ($DB->request($query) as $data) {
         return $data['SUM'];
      }
      return 0;
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
         'massiveaction'      => false
      ];

      $tab[] = [
         'id'                 => '2',
         'table'              => 'glpi_projects',
         'field'              => 'name',
         'name'               => __('Project'),
         'massiveaction'      => false,
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '13',
         'table'              => $this->getTable(),
         'field'              => 'name',
         'name'               => __('Father'),
         'datatype'           => 'dropdown',
         'massiveaction'      => false,
         // Add virtual condition to relink table
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
         'id'                 => '12',
         'table'              => 'glpi_projectstates',
         'field'              => 'name',
         'name'               => _x('item', 'State'),
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '14',
         'table'              => 'glpi_projecttasktypes',
         'field'              => 'name',
         'name'               => __('Type'),
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '15',
         'table'              => $this->getTable(),
         'field'              => 'date',
         'name'               => __('Opening date'),
         'datatype'           => 'datetime',
         'massiveaction'      => false
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
         'id'                 => '24',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'linkfield'          => 'users_id',
         'name'               => __('Creator'),
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

      $tab = array_merge($tab, Notepad::getSearchOptionsToAddNew());

      return $tab;
   }


   /**
    * Show tasks of a project
    *
    * @param $item Project or ProjectTask object
    *
    * @return nothing
   **/
   static function showFor($item) {
        global $DB, $CFG_GLPI;

        $ID = $item->getField('id');
        $sin_proveedor='';
        if (!$item->canViewItem()) {
           return false;
        }
        $columns = [
                  'name_paquete'              =>__('Name') ,
                  'code'                      => __('Código paquete'),
                  'tipo'                      => __('Tipo'),
                  'valor_contrato'            => __('Ppto Objetivo'),
                  'importe_licitado'          => __('Importe Licitado'),
                  'proveedor_id'              => __('Supplier'),
                  'proveedor_cif'             => __('CIF'),
                  'ini'                       => __('Inicio'),
                  'fin'                       => __('Fin'),
                  'state'                     => __('State')];    
        //Si se ha pulsado el boton visualizar borrados que se añada a la cabecera el campo borrar
        if(isset($_GET["vis_delete"]) && $_GET["vis_delete"]){
          $columns['is_delete']= __('Delete');
        }
        if(get_class($item)=='ProjectTask'){
            unset($columns['fname']);
        }
        if (isset($_GET["order"]) && ($_GET["order"] == "DESC")) {
           $order = "DESC";
        } else {
           $order = "ASC";
        }        
        if (!isset($_GET["sort"]) || empty($_GET["sort"])) {
           $_GET["sort"] = "plan_start_date";
        }

        if (isset($_GET["sort"]) && !empty($_GET["sort"]) && isset($columns[$_GET["sort"]])) {
           $sort = "".$_GET["sort"]."";
        } else {
           $sort = "plan_start_date $order, name";
        }

        $canedit = false;
        if ($item->getType() =='Project') {
           $canedit = $item->canEdit($ID);
        }       
        echo "<input type='hidden' id='ID' value={$ID} >";
        switch ($item->getType()) {
            case 'Project' :
                $where = "WHERE glpi_projecttasks.projects_id = '$ID' ";

                //Si no se ha pulsado el boton de visualizar borrados, que se añada al where
                if(!isset($_GET["vis_delete"])){
                     $where .="and glpi_projecttasks.is_delete = '0' ";
                }
                $sql = "SELECT * FROM glpi_projects WHERE id =".$ID;
                $resultado = $DB->query($sql);
                while ($d=$DB->fetch_assoc($resultado)) {
                    $importe_proyecto = $d['importe_proyecto'];
                }             
                break;

            case 'ProjectTask' :
                $where = "WHERE glpi_projecttasks.projecttasks_id = '$ID'";
                break;

           default : // Not available type
              return;
        }        
        switch ($item->getType()) {
            case 'Project' :
                $where = "WHERE glpi_projecttasks.projects_id = '$ID' ";

                //Si no se ha pulsado el boton de visualizar borrados, que se añada al where
                if(!isset($_GET["vis_delete"])){
                    $where .="and glpi_projecttasks.is_delete = '0' ";
                }
                $sql = "SELECT * FROM glpi_projects WHERE id =".$ID;
                $resultado = $DB->query($sql);
                while ($d=$DB->fetch_assoc($resultado)) {
                    $importe_proyecto = $d['importe_proyecto'];
                }             
              break;

           case 'ProjectTask' :
              $where = "WHERE glpi_projecttasks.projecttasks_id = '$ID'";
              break;

           default : // Not available type
              return;
        }        
        $USERID = $_SESSION['glpiID'];
        $self = new self();
        $profile_Id = $self->getProfileByUserID($USERID);
        $arrayPrj = $self->proyectosUsuario($USERID);

        $ver = true;
        //en el caso de que sea bovis_proveedores o bovis_general o que sea bovis_usuario_proyectos y que no sea un proyecto suyo 
        //no podrá editar nada
        //if(in_array($profile_Id, array(9,15)) || ($profile_Id == 14 && (in_array($ID, $arrayPrj) == false))){  
        if (in_array($profile_Id, array(9,14,15))){  
            $ver = false;
            echo "<input id='ver' type='hidden' value='0' />";
        }else{
            echo "<input id='ver' type='hidden' value='1' />";
        }        

        echo "<div id='marco_gral' style='height: 600px; overflow-y: auto; margin-bottom: 5px; float: left; position: relative; width:90%; background-color:#e9ecf3; border-radius: 4px; padding: 10px;'>";
      
        if ($canedit || in_array($ID, $arrayPrj)) {
          echo "<div class='center' style='float: left; position:relative; width: 60%; margin-bottom: 0px; margin-top: 4px; margin-left: 20%; padding: 5px; border-radius: 4px; background-color: #f8f7f3; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
              if($ver || in_array($ID, $arrayPrj)){
                  echo "<input id='nuevoContrato' type='submit' title='NUEVO CONTRATO' class='boton_add_contrato' value='' style='float:left; margin-left: 2em;' />";
              }
              //"<a href='".Toolbox::getItemTypeFormURL('ProjectTask')."?projecttasks_id=$ID&amp;projects_id=$projet'>"
              echo "Importe del proyecto = ".number_format($importe_proyecto,0,'','.')." €"; 
          echo "</div>";
        }

        if (($item->getType() == 'ProjectTask') && $item->can($ID, UPDATE)) {
           $rand = mt_rand();
           echo "<div class='firstbloc'>";
           echo "<form name='projecttask_form$rand' id='projecttask_form$rand' method='post'
                  action='".Toolbox::getItemTypeFormURL('ProjectTask')."'>";
           $projet = $item->fields['projects_id'];
           echo "<a href='".Toolbox::getItemTypeFormURL('ProjectTask')."?projecttasks_id=$ID&amp;projects_id=$projet'>";
           echo __('Create a sub task from this task of project');
           echo "</a>";
           Html::closeForm();
           echo "</div>";
        }        

        $addselect = '';
        $addjoin = '';
        if (Session::haveTranslations('ProjectTaskType', 'name')) {
           $addselect .= ", namet2.value AS transname2";
           $addjoin   .= " LEFT JOIN glpi_dropdowntranslations AS namet2
                             ON (namet2.itemtype = 'ProjectTaskType'
                                 AND namet2.items_id = glpi_projecttasks.projecttasktypes_id
                                 AND namet2.language = '".$_SESSION['glpilanguage']."'
                                 AND namet2.field = 'name')";
        }

        if (Session::haveTranslations('ProjectState', 'name')) {
           $addselect .= ", namet3.value AS transname3";
           $addjoin   .= "LEFT JOIN glpi_dropdowntranslations AS namet3
                             ON (namet3.itemtype = 'ProjectState'
                                 AND namet3.language = '".$_SESSION['glpilanguage']."'
                                 AND namet3.field = 'name')";
           $where     .= " AND namet3.items_id = glpi_projectstates.id ";
        }        
        $total_licitado = 0;
        $total_ppto = 0;
        $query = "SELECT 
                IF(glpi_projecttasks.projecttasks_id!=0  or (select (Select count(*) as numero from glpi_projecttasks as subpaquetes1 where paquetes1.id=subpaquetes1.projecttasks_id) as 'numero' 
                                                             from glpi_projecttasks as paquetes1 
                                                            where paquetes1.id=glpi_projecttasks.id)=0, '1', '0') as 'visualizar',
                IF(glpi_projecttasks.projecttasks_id=0, glpi_projecttasks.name, father.name) as 'name_paquete',
                roltypes.name AS tipo,
                importe_licitado_ganador(glpi_projecttasks.id) as importe_licitado, 
               glpi_projecttasks.id,
               glpi_projecttasks.name,
               glpi_projecttasks.code,
               glpi_projecttasks.valor_contrato,
               glpi_projecttasktypes.name AS tname,
               glpi_projectstates.name AS sname,
               glpi_projectstates.color,
               proveedor.id AS proveedor_id, 
               proveedor.name AS proveedor_name, 
               proveedor.cif AS proveedor_cif, 
               father.name AS fname,
               father.id AS fID
                ".$addselect."
                FROM glpi_projecttasks
                ".$addjoin."
                LEFT JOIN glpi_projecttasktypes
                   ON (glpi_projecttasktypes.id = glpi_projecttasks.projecttasktypes_id)
                LEFT JOIN glpi_projectstates
                   ON (glpi_projectstates.id = glpi_projecttasks.projectstates_id)
                LEFT JOIN glpi_projecttasks as father
                   ON (father.id = glpi_projecttasks.projecttasks_id)
                LEFT JOIN glpi_projecttaskteams as asig_proveedor
                   ON (asig_proveedor.projecttasks_id = glpi_projecttasks.id)
                LEFT JOIN glpi_suppliers as proveedor
                   ON (proveedor.id = asig_proveedor.items_id)
                LEFT JOIN glpi_plugin_comproveedores_roltypes as roltypes ON (roltypes.id = glpi_projecttasks.tipo_especialidad)
                ".$where;        
        $result = $DB->query($query);
        //echo $query;
        if ($DB->numrows($result)>0) {          
            echo "<div style='float:left; position:relative; width: 98%; background-color: #E5E5E5; margin-top: 10px;'>";
            echo "<table id='tablaContratos' class='display compact dataTable' style='margin-top: 10px; width: 80%; padding: 0px;'>";
            echo "<thead>";
            echo "<tr>";
                echo "<th>NOMBRE</th>";
                echo "<th>CÓDIGO</th>";
                echo "<th>TIPO</th>";
                if($ver){echo "<th>PPTO. OBJETIVO (€)</th>";}
                if($ver){echo "<th>IMPORTE ADJUDICACIÓN (€)</th>";}
                echo "<th>PROVEEDOR</th>";
                echo "<th>CIF</th>";
                echo "<th>INICIO</th>";
                echo "<th>FIN</th>";
                echo "<th>ESTADO</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";      
            while ($data=$DB->fetch_assoc($result)) {     
                //Visualizaremos los Subpaquetes solo en la lista de un paquete especifico, 
                //en la de proyecto aparecera solo el nombre del subpaquete en la ultima columna
                if($data["projecttasks_id"]==0 || get_class($item)=='ProjectTask'){
                    Session::addToNavigateListItems('ProjectTask', $data['id']);
                    $rand = mt_rand();
                        $total_licitado+=$data['importe_licitado'];
                        $total_ppto+=$data['valor_contrato'];
                        echo "<tr>";
                            echo "<td class='left'>";
                            $link = "<a id='ProjectTask".$data["id"]."' href='projecttask.form.php?id=".$data['id']."'>".$data['name'].(empty($data['name'])?"(".$data['id'].")":"")."</a>";
                            echo sprintf(__('%1$s %2$s'), $link, Html::showToolTip($data['content'], ['display' => false,'applyto' => "ProjectTask".$data["id"]]));
                            echo "</td>";
                            $name = !empty($data['transname2'])?$data['transname2']:$data['code'];
                            echo "<td class='left'>".$name."</td>";
                            echo "<td class='left'>".$data['tipo']."</td>";
                            if($ver){echo "<td class='right'>".number_format($data['valor_contrato'],0,'','.')."</td>";}
                            if($ver){echo "<td class='right'>".number_format($data['importe_licitado'],0,'','.')."</td>";}
                            echo "<td class='left'>";
                            $link2 = "<a id='Supplier".$data["id"]."' href='supplier.form.php?id=".$data['proveedor_id']."'>".$data['proveedor_name'].(empty($data['proveedor_name'])?"(".$data['proveedor_id'].")":"")."</a>";
                            echo sprintf(__('%1$s %2$s'), $link2, Html::showToolTip($data['content'], ['display' => false,'applyto' => "Supplier".$data["proveedor_id"]]));
                            //echo $data["proveedor_name"];
                            echo "</td>";
                            echo "<td class='center'>".$data['proveedor_cif']."</td>";
                            echo "<td class='center'>".Html::convDateTime($data['ini'], 3)."</td>";
                            echo "<td class='center'>".Html::convDateTime($data['fin'], 3)."</td>";
                            echo "<td class='center'>".$data['sname']."</td>";

                            //Si se ha pulsado el boton visualizar borrados, que se añada la columna con el boton Borrar, solo los que tenga is_delete=1
                            if(isset($_GET["vis_delete"]) && $_GET["vis_delete"]){
                                    echo "<td>";
                                            if($data['is_delete']==1){
                                                    echo"<form action=".$CFG_GLPI["root_doc"]."/front/projecttask.form.php method='post'>";
                                                    echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                                                    echo Html::hidden('id', array('value' => $data['id']));    
                                                    echo Html::submit(_x('button', 'BORRAR'),
                                                      ['name'    => 'purge_final',
                                                            'confirm' => __('Confirm the final deletion?')]);
                                                    echo"</form>";
                                            }
                                    echo"</td>";
                            }
                        echo "</tr>";
                }                
            }
            
            echo "</tbody>";     
            $tot1 = number_format($total_ppto,0,'','.')." €";
            $tot2 = number_format($total_licitado,0,'', '.')." €";
            if($ver){echo "<tfoot style='background-color: #f8f8f8;font-weight: bold;'>";}
            if($ver){echo "<tr><td class='center'>TOTALES</td><td></td><td></td><td class='right'>{$tot1}</td><td class='right'>{$tot2}</td><td></td><td></td><td></td><td></td><td></td></tr>";}
            if($ver){echo "</tfoot>";}
            echo "</table>";
            echo "</div>";
           
        }else{
            echo "<table class='tab_cadre_fixe'>";
            echo "<tr><th>".__('No item found')."</th></tr>";
            echo "</table>\n";            
        }
        echo "</div>";
            echo "<script type='text/javascript'>

                if($('#ver').val() == '1'){
                     $('#tablaContratos').DataTable({
                         'searching':      true,
                         'scrollY':        '380px',
                         'scrollCollapse': true,
                         'paging':         false,
                         'dom': 'Bfrtip',
                         'buttons': [
                             'copyHtml5',
                             'excelHtml5',
                             'pdfHtml5'
                         ],
                         'language': {
                             'info': 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                             'search': 'Buscar:',
                             'decimal': ',',
                             'thousands': '.'
                         },                                    
                         'columnDefs': [
                             { 'width': '100px', 'targets': 3 },
                             { 'width': '100px', 'targets': 4 },
                             { 'width': '300px', 'targets': 5 },
                        ]  
                     }); 
                }else{
                     $('#tablaContratos').DataTable({
                         'searching':      true,
                         'scrollY':        '200px',
                         'scrollCollapse': true,
                         'paging':         false,
                         'language': {
                             'decimal': ',',
                             'thousands': '.'
                         },                                    
                     });                       
                 }

                $('#nuevoContrato').on('click', function(){
                    var id = $('#ID').val();
                    window.open('projecttask.form.php?projects_id='+id,'_self');
                });

            </script>";     
   }


   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      $nb = 0;
      switch ($item->getType()) {
         case 'Project' :
            if ($_SESSION['glpishow_count_on_tabs']) {
               $nb = countElementsInTable($this->getTable(),
                                          ['projects_id' => $item->getID()]);
            }
            return self::createTabEntry(self::getTypeName(Session::getPluralNumber()), $nb);

         case 'Supplier' :
            if ($_SESSION['glpishow_count_on_tabs']) {
               $nb = countElementsInTable($this->getTable(),
                                          ['cv_id' => $item->getID()]);
            }
            
           //si entramos en un subpaquete que no aparecta el tab para añadir paquetes al subpaquete
            if($item->fields['cv_id']==0){
                return self::createTabEntry(self::getTypeName(Session::getPluralNumber()), $nb);
            }             
             
         case __CLASS__ :
            if ($_SESSION['glpishow_count_on_tabs']) {
               $nb = countElementsInTable($this->getTable(),
                                          ['projecttasks_id' => $item->getID()]);
            }
            
           //si entramos en un subpaquete que no aparecta el tab para añadir paquetes al subpaquete
            if($item->fields['projecttasks_id']==0){
                return self::createTabEntry(self::getTypeName(Session::getPluralNumber()), $nb);
            }
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        GLOBAL $DB,$CFG_GLPI;
        
       $id_usuario=$_SESSION['glpiID'];
      
        $query = "select distinct projectteams.items_id 
                        from glpi_projectteams as projectteams 
                        where projectteams.projects_id=".$item->fields['id']." and projectteams.items_id=".$id_usuario;
        
        $result = $DB->query($query);
                                                     
        //Si un usuario de equipo de proyecto o tiene premisos de super-Admin, que entre
        if($result->num_rows!=0 || $_SESSION['glpiactiveprofile']['id']==4){
                switch ($item->getType()) {
                   case 'Project' :
                      self::showFor($item);
                      break;
                   case 'Supplier':
                      self::showFormSupplier($item);
                      break;
                   case __CLASS__ :
                      self::showFor($item);
                      break;
                }
        }
        else{
                self::showFormNoPermiso($item, $withtemplate);
        }
      return true;
   }


    function showFormNoPermiso($ID, $options=[]) {
                        
                echo "<div><bold>Solo pueden acceder los usuarios que este en Equipo de proyecto o sean Administrador</bold></div>";
                echo "<br>";
    }
    
    
   /**
    * Show team for a project task
    *
    * @param $task   ProjectTask object
    *
    * @return boolean
   **/
   function showTeam(ProjectTask $task) {
      global $DB, $CFG_GLPI;

      /// TODO : permit to simple add member of project team ?

      $ID      = $task->fields['id'];
      $canedit = $task->canEdit($ID);

      $rand = mt_rand();
      $nb   = 0;
      $nb   = $task->getTeamCount();

      if ($canedit) {
         echo "<div class='firstbloc'>";
         echo "<form name='projecttaskteam_form$rand' id='projecttaskteam_form$rand' ";
         echo " method='post' action='".Toolbox::getItemTypeFormURL('ProjectTaskTeam')."'>";
         echo "<input type='hidden' name='projecttasks_id' value='$ID'>";
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_1'><th colspan='2'>".__('Add a team member')."</tr>";
         echo "<tr class='tab_bg_2'><td>";

         $params = ['itemtypes'       => ProjectTeam::$available_types,
                         'entity_restrict' => ($task->fields['is_recursive']
                                               ? getSonsOf('glpi_entities',
                                                           $task->fields['entities_id'])
                                               : $task->fields['entities_id']),
                         'checkright'      => true];
         $addrand = Dropdown::showSelectItemFromItemtypes($params);

         echo "</td>";
         echo "<td width='20%'>";
         echo "<input type='submit' name='add' value=\""._sx('button', 'Add')."\" class='submit'>";
         echo "</td>";
         echo "</tr>";
         echo "</table>";
         Html::closeForm();
         echo "</div>";
      }
      echo "<div class='spaced'>";
      if ($canedit && $nb) {
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
         $header_top    .= "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_top    .= "</th>";
         $header_bottom .= "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_bottom .= "</th>";
      }
      $header_end .= "<th>".__('Type')."</th>";
      $header_end .= "<th>"._n('Member', 'Members', Session::getPluralNumber())."</th>";
      $header_end .= "</tr>";
      echo $header_begin.$header_top.$header_end;

      foreach (ProjectTaskTeam::$available_types as $type) {
         if (isset($task->team[$type]) && count($task->team[$type])) {
            if ($item = getItemForItemtype($type)) {
               foreach ($task->team[$type] as $data) {
                  $item->getFromDB($data['items_id']);
                  echo "<tr class='tab_bg_2'>";
                  if ($canedit) {
                     echo "<td>";
                     Html::showMassiveActionCheckBox('ProjectTaskTeam', $data["id"]);
                     echo "</td>";
                  }
                  echo "<td>".$item->getTypeName(1)."</td>";
                  echo "<td>".$item->getLink()."</td>";
                  echo "</tr>";
               }
            }
         }
      }
      if ($nb) {
         echo $header_begin.$header_bottom.$header_end;
      }
      echo "</table>";
      if ($canedit && $nb) {
         $massiveactionparams['ontop'] =false;
         Html::showMassiveActions($massiveactionparams);
         Html::closeForm();
      }

      echo "</div>";
      // Add items

      return true;
   }


   /** Get data to display on GANTT for a project task
    *
   * @param $ID ID of the project task
   */
   static function getDataToDisplayOnGantt($ID) {
      global $DB;

      $todisplay = [];

      $task = new self();
      // echo $ID.'<br>';
      if ($task->getFromDB($ID)) {
         $subtasks = [];
         foreach ($DB->request('glpi_projecttasks',
                               ['projecttasks_id' => $ID,
                                     'ORDER'           => ['plan_start_date',
                                                                'real_start_date']]) as $data) {
            $subtasks += static::getDataToDisplayOnGantt($data['id']);
         }

         $real_begin = null;
         $real_end   = null;
         // Use real if set
         if (!is_null($task->fields['real_start_date'])) {
            $real_begin = $task->fields['real_start_date'];
         }

         // Determine begin / end date of current task if not set (min/max sub projects / tasks)
         if (is_null($real_begin)) {
            if (!is_null($task->fields['plan_start_date'])) {
               $real_begin = $task->fields['plan_start_date'];
            } else {
               foreach ($subtasks as $subtask) {
                  if (is_null($real_begin)
                      || (!is_null($subtask['from'])
                          && ($real_begin > $subtask['from']))) {
                     $real_begin = $subtask['from'];
                  }
               }
            }
         }

         // Use real if set
         if (!is_null($task->fields['real_end_date'])) {
            $real_end = $task->fields['real_end_date'];
         }
         if (is_null($real_end)) {
            if (!is_null($task->fields['plan_end_date'])) {
               $real_end = $task->fields['plan_end_date'];
            } else {
               foreach ($subtasks as $subtask) {
                  if (is_null($real_end)
                      || (!is_null($subtask['to'])
                          && ($real_end < $subtask['to']))) {
                     $real_end = $subtask['to'];
                  }
               }
            }
         }

         $parents = 0;
         if ($task->fields['projecttasks_id'] > 0) {
            $parents = count(getAncestorsOf("glpi_projecttasks", $ID));
         }

         if ($task->fields['is_milestone']) {
            $percent = "";
         } else {
            $percent = isset($task->fields['percent_done'])?$task->fields['percent_done']:0;
         }

         // Add current task
         $todisplay[$real_begin.'#'.$real_end.'#task'.$task->getID()]
                        = ['id'    => $task->getID(),
                              'name'    => $task->fields['name'],
                              'desc'    => $task->fields['content'],
                              'link'    => $task->getlink(),
                              'type'    => 'task',
                              'percent' => $percent,
                              'from'    => $real_begin,
                              'parents' => $parents,
                              'to'      => $real_end,
                              'is_milestone' => $task->fields['is_milestone']];

         // Add ordered subtasks
         foreach ($subtasks as $key => $val) {
            $todisplay[$key] = $val;
         }
      }
      return $todisplay;
   }


   /** Get data to display on GANTT for a project
    *
   * @param $ID ID of the project
   */
   static function getDataToDisplayOnGanttForProject($ID) {
      global $DB;

      $todisplay = [];

      $task      = new self();
      // Get all tasks without father
      foreach ($DB->request('glpi_projecttasks',
                            ['projects_id'     => $ID,
                                  'projecttasks_id' => 0,
                                  'ORDER'           => ['plan_start_date',
                                                             'real_start_date']]) as $data) {
         if ($task->getFromDB($data['id'])) {
            $todisplay += static::getDataToDisplayOnGantt($data['id']);
         }
      }

      return $todisplay;
   }


   /**
    * Display debug information for current object
   **/
   function showDebug() {
      NotificationEvent::debugEvent($this);
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
    * Populate the planning with planned project tasks
    *
    * @since version 0.85
    *
    * @param $options   array of possible options:
    *    - who ID of the user (0 = undefined)
    *    - who_group ID of the group of users (0 = undefined)
    *    - begin Date
    *    - end Date
    *    - color
    *    - event_type_color
    *
    * @return array of planning item
   **/
   static function populatePlanning($options = []) {

      global $DB, $CFG_GLPI;

      $interv = [];
      $ttask  = new self;

      if (!isset($options['begin']) || ($options['begin'] == 'NULL')
          || !isset($options['end']) || ($options['end'] == 'NULL')) {
         return $interv;
      }

      $default_options = [
         'genical'             => false,
         'color'               => '',
         'event_type_color'    => '',
      ];
      $options = array_merge($default_options, $options);

      $who       = $options['who'];
      $who_group = $options['who_group'];
      $begin     = $options['begin'];
      $end       = $options['end'];

      // Get items to print
      $ASSIGN = "";

      if ($who_group === "mine") {
         if (!$options['genical']
             && count($_SESSION["glpigroups"])) {
            $groups = implode("','", $_SESSION['glpigroups']);
            $ASSIGN = "glpi_projecttaskteams.itemtype = 'Group'
                       AND glpi_projecttaskteams.items_id
                           IN (SELECT DISTINCT groups_id
                               FROM glpi_groups
                               WHERE groups_id IN ('$groups')
                                     AND glpi_groups.is_assign)
                                     AND ";
         } else { // Only personal ones
            $ASSIGN = "glpi_projecttaskteams.itemtype = 'User'
                       AND glpi_projecttaskteams.items_id = '$who'
                       AND ";
         }

      } else {
         if ($who > 0) {
            $ASSIGN = "glpi_projecttaskteams.itemtype = 'User'
                       AND glpi_projecttaskteams.items_id = '$who'
                       AND ";
         }

         if ($who_group > 0) {
            $ASSIGN = "glpi_projecttaskteams.itemtype = 'Group'
                       AND glpi_projecttaskteams.items_id
                           IN ('$who_group')
                       AND ";
         }
      }
      if (empty($ASSIGN)) {
         $ASSIGN = "glpi_projecttaskteams.itemtype = 'User'
                       AND glpi_projecttaskteams.items_id
                        IN (SELECT DISTINCT glpi_profiles_users.users_id
                            FROM glpi_profiles
                            LEFT JOIN glpi_profiles_users
                                 ON (glpi_profiles.id = glpi_profiles_users.profiles_id)
                            WHERE glpi_profiles.interface = 'central' ".
                                  getEntitiesRestrictRequest("AND", "glpi_profiles_users", '',
                                                             $_SESSION["glpiactive_entity"], 1).")
                     AND ";
      }

      $DONE_EVENTS = '';
      if (!isset($options['display_done_events']) || !$options['display_done_events']) {
         $DONE_EVENTS = "glpi_projecttasks.percent_done < 100
                         AND (glpi_projectstates.is_finished = 0
                              OR glpi_projectstates.is_finished IS NULL)
                         AND ";
      }

      $query = "SELECT glpi_projecttasks.*
                FROM glpi_projecttaskteams
                INNER JOIN glpi_projecttasks
                  ON (glpi_projecttasks.id = glpi_projecttaskteams.projecttasks_id)
                LEFT JOIN glpi_projectstates
                  ON (glpi_projecttasks.projectstates_id = glpi_projectstates.id)
                WHERE $ASSIGN
                      $DONE_EVENTS
                      '$begin' < glpi_projecttasks.plan_end_date
                      AND '$end' > glpi_projecttasks.plan_start_date
                ORDER BY glpi_projecttasks.plan_start_date";

      $result = $DB->query($query);

      $interv = [];
      $task   = new self();

      if ($DB->numrows($result) > 0) {
         for ($i=0; $data=$DB->fetch_assoc($result); $i++) {
            if ($task->getFromDB($data["id"])) {
               $key = $data["plan_start_date"]."$$$"."ProjectTask"."$$$".$data["id"];
               $interv[$key]['color']            = $options['color'];
               $interv[$key]['event_type_color'] = $options['event_type_color'];
               $interv[$key]['itemtype']         = 'ProjectTask';
               if (!$options['genical']) {
                  $interv[$key]["url"] = Project::getFormURLWithID($task->fields['projects_id']);
               } else {
                  $interv[$key]["url"] = $CFG_GLPI["url_base"].
                                         Project::getFormURLWithID($task->fields['projects_id'], false);
               }
               $interv[$key]["ajaxurl"] = $CFG_GLPI["root_doc"]."/ajax/planning.php".
                                          "?action=edit_event_form".
                                          "&itemtype=ProjectTask".
                                          "&id=".$data['id'].
                                          "&url=".$interv[$key]["url"];

               $interv[$key][$task->getForeignKeyField()] = $data["id"];
               $interv[$key]["id"]                        = $data["id"];
               $interv[$key]["users_id"]                  = $data["users_id"];

               if (strcmp($begin, $data["plan_start_date"]) > 0) {
                  $interv[$key]["begin"] = $begin;
               } else {
                  $interv[$key]["begin"] = $data["plan_start_date"];
               }

               if (strcmp($end, $data["plan_end_date"]) < 0) {
                  $interv[$key]["end"]   = $end;
               } else {
                  $interv[$key]["end"]   = $data["plan_end_date"];
               }

               $interv[$key]["name"]     = $task->fields["name"];
               $interv[$key]["content"]  = Html::resume_text($task->fields["content"],
                                                             $CFG_GLPI["cut"]);
               $interv[$key]["status"]   = $task->fields["percent_done"];

               $ttask->getFromDB($data["id"]);
               $interv[$key]["editable"] = $ttask->canUpdateItem();
            }
         }
      }

      return $interv;
   }

   /**
    * Display a Planning Item
    *
    * @since version 9.1
    *
    * @param $val       array of the item to display
    * @param $who             ID of the user (0 if all)
    * @param $type            position of the item in the time block (in, through, begin or end)
    *                         (default '')
    * @param $complete        complete display (more details) (default 0)
    *
    * @return Nothing (display function)
    **/
   static function displayPlanningItem(array $val, $who, $type = "", $complete = 0) {
      global $CFG_GLPI;

      $html = "";
      $rand     = mt_rand();
      $users_id = "";  // show users_id project task
      $img      = "rdv_private.png"; // default icon for project task

      if ($val["users_id"] != Session::getLoginUserID()) {
         $users_id = "<br>".sprintf(__('%1$s: %2$s'), __('By'), getUserName($val["users_id"]));
         $img      = "rdv_public.png";
      }

      $html.= "<img src='".$CFG_GLPI["root_doc"]."/pics/".$img."' alt='' title=\"".
             self::getTypeName(1)."\">&nbsp;";
      $html.= "<a id='project_task_".$val["id"].$rand."' href='".
             $CFG_GLPI["root_doc"]."/front/projecttask.form.php?id=".$val["id"]."'>";

      switch ($type) {
         case "in" :
            //TRANS: %1$s is the start time of a planned item, %2$s is the end
            $beginend = sprintf(__('From %1$s to %2$s'), date("H:i", strtotime($val["begin"])),
                                date("H:i", strtotime($val["end"])));
            $html.= sprintf(__('%1$s: %2$s'), $beginend, Html::resume_text($val["name"], 80));
            break;

         case "through" :
            $html.= Html::resume_text($val["name"], 80);
            break;

         case "begin" :
            $start = sprintf(__('Start at %s'), date("H:i", strtotime($val["begin"])));
            $html.= sprintf(__('%1$s: %2$s'), $start, Html::resume_text($val["name"], 80));
            break;

         case "end" :
            $end = sprintf(__('End at %s'), date("H:i", strtotime($val["end"])));
            $html.= sprintf(__('%1$s: %2$s'), $end, Html::resume_text($val["name"], 80));
            break;
      }

      $html.= $users_id;
      $html.= "</a>";

      $html.= "<div class='b'>";
      $html.= sprintf(__('%1$s: %2$s'), __('Percent done'), $val["status"]."%");
      $html.= "</div>";
      $html.= "<div class='event-description'>".html_entity_decode($val["content"])."</div>";
      return $html;
   }
}
