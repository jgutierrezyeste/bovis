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

//!  ProjectTeam Class
/**
 * This class is used to manage the project team
 * @see Project
 * @author Julien Dombre
 * @since version 0.85
 **/
class ProjectTeam extends CommonDBRelation {

   // From CommonDBTM
   public $dohistory                  = true;
   public $no_form_page               = true;

   // From CommonDBRelation
   static public $itemtype_1          = 'Project';
   static public $items_id_1          = 'projects_id';

   static public $itemtype_2          = 'itemtype';
   static public $items_id_2          = 'items_id';
   static public $checkItem_2_Rights  = self::DONT_CHECK_ITEM_RIGHTS;

   static public $available_types     = ['User', 'Group', 'Supplier', 'Contact'];


   /**
    * @see CommonDBTM::getNameField()
   **/
   static function getNameField() {
      return 'id';
   }


   static function getTypeName($nb = 0) {
      return _n('Project team', 'Project teams', $nb);
   }


   function getForbiddenStandardMassiveAction() {

      $forbidden   = parent::getForbiddenStandardMassiveAction();
      $forbidden[] = 'update';
      return $forbidden;
   }


   /**
    * @see CommonGLPI::getTabNameForItem()
   **/
   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if (self::canView()) {
         $nb = 0;
         switch ($item->getType()) {
            case 'Project' :
               if ($_SESSION['glpishow_count_on_tabs']) {
                  $nb = $item->getTeamCount();
               }
               return self::createTabEntry(self::getTypeName(1), $nb);
         }
      }
      return '';
   }


   /**
    * @param $item
    *
    * @return number
   **/
   static function countForProject(Project $item) {

      return countElementsInTable(['glpi_projectteams'], ['glpi_projectteams.projects_id' => $item->getField('id')]);
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      switch ($item->getType()) {
         case 'Project' :
                if(isset($_GET['tab_projecto'])){
                        $item->showElegirGestor($item);
                }else{
                        $item->showTeam($item);
                }
                
            return true;
      }
   }

   /**
    * Duplicate all teams from a project template to his clone
    *
    * @since version 9.2
    *
    * @param integer $oldid        ID of the item to clone
    * @param integer $newid        ID of the item cloned
    **/
   static function cloneProjectTeam ($oldid, $newid) {
      global $DB;

      $query  = "SELECT *
                 FROM `glpi_projectteams`
                 WHERE `projects_id` = '$oldid'";
      foreach ($DB->request($query) as $data) {
         $cd                  = new self();
         unset($data['id']);
         $data['projects_id'] = $newid;
         $data                = Toolbox::addslashes_deep($data);
         $cd->add($data);
      }
   }


   /**
    * Get team for a project
    *
    * @param $projects_id
   **/
   static function getTeamFor($projects_id) {
      global $DB;

      $team = [];
      $query = "SELECT `glpi_projectteams`.*
                FROM `glpi_projectteams`
                WHERE `projects_id` = '$projects_id'";

      foreach ($DB->request($query) as $data) {
         if (!isset($team[$data['itemtype']])) {
            $team[$data['itemtype']] = [];
         }
         $team[$data['itemtype']][] = $data;
      }

      // Define empty types
      foreach (static::$available_types as $type) {
         if (!isset($team[$type])) {
            $team[$type] = [];
         }
      }

      return $team;
   }

}