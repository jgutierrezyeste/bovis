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
 * Supplier class (suppliers)
**/
class Supplier extends CommonDBTM {

   // From CommonDBTM
   public $dohistory           = true;

   static $rightname           = 'contact_enterprise';
   protected $usenotepad       = true;



   /**
    * Name of the type
    *
    * @param $nb : number of item in the type
   **/
   static function getTypeName($nb = 0) {
      return _n('Supplier', 'Suppliers', $nb);
   }

   /*
Los valores devueltos son:
Tipo:     ???     NIF     CIF     NIE
Correcto:         1       2       3
Incorrecto:0     -1      -2      -3
    */
    public function valida_documento($cif) {
	$cif = strtoupper($cif);
	for ($i = 0; $i < 9; $i ++)	{
		$num[$i] = substr($cif, $i, 1);
	}
	//si no tiene un formato valido devuelve error
	if (!preg_match("/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/", $cif)){
		return 0;
	}
	//comprobacion de NIFs estandar
	if (preg_match("/(^[0-9]{8}[A-Z]{1}$)/", $cif)){
		if ($num[8] == substr("TRWAGMYFPDXBNJZSQVHLCKE", substr($cif, 0, 8) % 23, 1)){
			return 1;
		}else{
			return -1;
		}
	}
	//algoritmo para comprobacion de codigos tipo CIF
	$suma = $num[2] + $num[4] + $num[6];
	for ($i = 1; $i < 8; $i += 2){
		$suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]), 1, 1);
	}
	$n = 10-substr($suma, (strlen($suma))-1, 1);
	//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
	if (preg_match("/^[KLM]{1}/", $cif)){
		if ($num[8] == chr(64 + $n) || $num[8] == substr("TRWAGMYFPDXBNJZSQVHLCKE", substr($cif, 1, 8) % 23, 1)){
			return 1;
		}else{
			return -1;
		}
	}
	//comprobacion de CIFs
	if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)){
		if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n)-1, 1)){
			return 2;
		}else{
			return -2;
		}	
	}
	//comprobacion de NIEs
	if (preg_match('/^[XYZ]{1}/', $cif)){
		if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1)){
			return 3;
		}else{
			return -3;
		}
	}
	//si todavia no se ha verificado devuelve error
	return 0;
    }   

   function cleanDBonPurge() {
      global $DB;

      $supplierjob = new Supplier_Ticket();
      $supplierjob->cleanDBonItemDelete($this->getType(), $this->fields['id']);

      $ps = new Problem_Supplier();
      $ps->cleanDBonItemDelete($this->getType(), $this->fields['id']);

      $cs = new Change_Supplier();
      $cs->cleanDBonItemDelete($this->getType(), $this->fields['id']);

      $query1 = "DELETE
                 FROM `glpi_projecttaskteams`
                 WHERE `items_id` = '".$this->fields['id']."'
                       AND `itemtype` = '".__CLASS__."'";
      $DB->query($query1);

      $query1 = "DELETE
                 FROM `glpi_projectteams`
                 WHERE `items_id` = '".$this->fields['id']."'
                       AND `itemtype` = '".__CLASS__."'";
      $DB->query($query1);

      $cs  = new Contract_Supplier();
      $cs->cleanDBonItemDelete($this->getType(), $this->fields['id']);

      $cs  = new Contact_Supplier();
      $cs->cleanDBonItemDelete($this->getType(), $this->fields['id']);

      // Ticket rules use suppliers_id_assign
      Rule::cleanForItemAction($this, 'suppliers_id%');
   }


   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('Document_Item', $ong, $options);
      
      //$this->addStandardTab('ProjectTask', $ong, $options);
      //$this->addStandardTab('Contract_Supplier', $ong, $options);
     /* $this->addStandardTab('Contact_Supplier', $ong, $options);      
      $this->addStandardTab('Infocom', $ong, $options);      
      $this->addStandardTab('Ticket', $ong, $options);
      $this->addStandardTab('Item_Problem', $ong, $options);
      $this->addStandardTab('Change_Item', $ong, $options);
      $this->addStandardTab('Link', $ong, $options);
      $this->addStandardTab('Notepad', $ong, $options);
      $this->addStandardTab('KnowbaseItem_Item', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);*/
    //$('input[title=Evaluaciones]').closest('li').css('background-color', '#ff0000');
        echo "<script type='text/javascript'>
            $(document).ready(function() {
                $('#ui-id-11').closest('li').css('background-color', 'rgb(255, 170, 170)');
                $('#ui-id-11').closest('li').css('background-image', 'none');
                $('#ui-id-11').css('background-color', 'rgb(255, 170, 170)');
                $('#ui-id-12').closest('li').css('background-color', 'rgb(255, 170, 170)');
                $('#ui-id-12').css('background-color', 'rgb(255, 170, 170)');
                $('#ui-id-12').closest('li').css('background-image', 'none');                
            });
        </script>";
      return $ong;
   }

    static function getProfileByUserID($UsuarioID){
            global $DB;

            $query ="SELECT profiles_id as profile FROM glpi_users WHERE id=$UsuarioID";

            $result=$DB->query($query);
            $id=$DB->fetch_array($result);

            if($id['profile']<>''){
                    $options['profile']=$id['profile'];
            }
            return $options['profile'];
    }
                
                
   /**
    * Print the enterprise form
    *
    * @param $ID Integer : Id of the computer or the template to print
    * @param $options array
    *     - target form target
    *     - withtemplate boolean : template or basic item
    *
    *@return Nothing (display)
   **/
   function showForm($ID, $options = []) {
   global $CFG_GLPI, $DB;
   
        $options['cv'] = false;
        $this->initForm($ID, $options);     
        $this->showFormHeader($options);      
        
        //DATOS DEL PERFIL Y USUARIO
        $user_Id             = $_SESSION['glpiID'];
        $profile_Id          = $this->getProfileByUserID($user_Id);
        $ver                 = true;
        $opcion              = 0;
        if(in_array($profile_Id, array(3,4,16))){    
            $ver = true;
            $opcion = 1;
        }else{
            $opcion = 0;
            $ver = false;
        }         

        //CCAA
        $opt3['comments']       = false;
        $opt3['addicon']        = false;
        $opt3['width']          = '203px';
        $opt3['specific_tags']  = array('onchange' => 'cambiarProvincia(value, false)');
        $opt3['value']          = $this->fields["plugin_comproveedores_communities_id"];
        
        //provincia
        $opt4['comments']       = false;
        $opt4['addicon']        = false;
        $opt4['width']          = '203px';
        $opt4['value']          = $this->fields["plugin_comproveedores_provinces_id"];

        //tier
        $opt5['comments']       = false;
        $opt5['addicon']        = false;
        $opt5['width']          = '203px';
        $opt5['value']          = $this->fields["plugin_comproveedores_tiers_id"];        

        //AMBITO
        $opt6['comments']       = false;
        $opt6['addicon']        = false;
        $opt6['width']          = '203px';
        //$opt6['value']          = $this->fields["plugin_comproveedores_ambitos_id"];      

        
        echo "<tr class='tab_bg_1'>";
              echo "<td rowspan='9'><img src='../pics/boton_proveedor_grande.png' style='margin-right: 40px;width: 100px;'/></td>";
              echo "<td>". __('CIF/NIF')."(*)</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "cif", ['required' => 'true']);
              echo "</td>";        
              
              echo "<td style='text-align: right;'>";
              if($ver){echo "Extinta / en concurso:</td>";}
              echo "<td style='vertical-align: middle;'>";
              if($ver){Dropdown::showYesNo("extinta", $this->fields["extinta"]);}
              echo "</td>";       
              
        echo "</tr>";
        echo "<tr class='tab_bg_1'>";
              echo "<td>".__('Name')." / Empresa</td>";
              echo "<td colspan='2'>";
              Html::autocompletionTextField($this, "name", ['style' => 'width:400px;']);
              echo "</td>";    
              echo "<td>".__('Tipo')."</td>";
              echo "<td>";
              ProveedorType::dropdown(['value' => $this->fields["proveedortypes_id"]]);
              echo "</td>";    
        echo "</tr>";              
        echo "<tr class='tab_bg_1'>";
              echo "<td>". __('Phone')."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "phonenumber");
              echo "</td>";
              echo "<td>" . __('Forma Juridica') . "</td>";         
              echo "<td>";
              SupplierType::dropdown(['value' => $this->fields["suppliertypes_id"]]);
              echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td>".__('Fax')."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "fax");
              echo "</td>";
              echo "<td>".__('Website')."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "website");    
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td>"._n('Email', 'Emails', 1)."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "email");
              echo "</td>";
              echo "<td>".__('Postal code')."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "postcode", ['size' => 10]);
              echo "</td>";                   
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td class='middle'>".__('Address')."</td>";
              echo "<td class='middle' colspan='3'>";
              echo "<textarea cols='110' rows='3' name='address'>".$this->fields["address"]."</textarea>";
              echo "</td>";
              /*
              echo "<td class='top'>".__('Comments')."</td>";
              echo "<td class='top' rowspan='3'>";
              echo "<textarea cols='50' rows='9' name='comment' >".$this->fields["comment"]."</textarea>";
              echo "</td>";     */        
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td>".__('CCAA')."</td>";
              echo "<td>";
              PluginComproveedoresCommunity::dropdown($opt3);
              echo "</td>";
              echo "<td>".__('Provincia')."</td>";
              echo "<td>";
              echo "<div id='idprovincia' style='width: 203px; display:inline-block; position:relative;'>";
              PluginComproveedoresProvince::dropdown($opt4);
              echo "</div>";
              echo "</td>";              
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td>".__('City')."</td>";
              echo "<td>";
              Html::autocompletionTextField($this, "town", ['size' => 23]);    
              echo "</td>";
              echo "<td>".__('Tier')."</td>";
              echo "<td>";
              PluginComproveedoresTier::dropdown($opt5);
              echo "</td>";
        echo "</tr>";

//        echo "<tr class='tab_bg_1'>";
//              echo "<td>".__('Ámbito')."</td>";
//              echo "<td colspan='3'>";
//              PluginComproveedoresAmbito::dropdown($opt6); 
//
//              echo "</td>";
//        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
              echo "<td colspan='4'></td>";
        echo "</tr>";
        $options['colspan']=3;

        $this->showFormButtons($options);


        
        echo "<script type='text/javascript'>

                

                if({$opcion} == 0){
                    $('.boton_grabar').css('display', 'none');
                    $('.boton_borrar').css('display', 'none');                
                }
                
                function validateCIF(cif)
                {
                        cif = cif.replace(/[^a-z0-9\s]/gi, '').replace(' ','');
                        //Quitamos el primer caracter y el ultimo digito
                        var valueCif=cif.substr(1,cif.length-2);

                        var suma=0;

                        //Sumamos las cifras pares de la cadena
                        for(i=1;i<valueCif.length;i=i+2)
                        {
                                suma=suma+parseInt(valueCif.substr(i,1));
                        }

                        var suma2=0;

                        //Sumamos las cifras impares de la cadena
                        for(i=0;i<valueCif.length;i=i+2)
                        {
                                result=parseInt(valueCif.substr(i,1))*2;
                                if(String(result).length==1)
                                {
                                        // Un solo caracter
                                        suma2=suma2+parseInt(result);
                                }else{
                                        // Dos caracteres. Los sumamos...
                                        suma2=suma2+parseInt(String(result).substr(0,1))+parseInt(String(result).substr(1,1));
                                }
                        }

                        // Sumamos las dos sumas que hemos realizado
                        suma=suma+suma2;

                        var unidad=String(suma).substr(1,1)
                        unidad=10-parseInt(unidad);

                        var primerCaracter=cif.substr(0,1).toUpperCase();

                        if(primerCaracter.match(/^[FJKNPQRSUVW]$/))
                        {
                                //Empieza por .... Comparamos la ultima letra
                                if(String.fromCharCode(64+unidad).toUpperCase()==cif.substr(cif.length-1,1).toUpperCase())
                                        return true;
                        }else if(primerCaracter.match(/^[XYZ]$/)){
                                //Se valida como un dni
                                var newcif;
                                if(primerCaracter=='X')
                                        newcif=cif.substr(1);
                                else if(primerCaracter=='Y')
                                        newcif='1'+cif.substr(1);
                                else if(primerCaracter=='Z')
                                        newcif='2'+cif.substr(1);
                                return validateDNI(newcif);
                        }else if(primerCaracter.match(/^[ABCDEFGHLM]$/)){
                                //Se revisa que el ultimo valor coincida con el calculo
                                if(unidad==10)
                                        unidad=0;
                                if(cif.substr(cif.length-1,1)==String(unidad))
                                        return true;
                        }else{
                                //Se valida como un dni
                                return validateDNI(cif);
                        }
                        return false;
                }

                function validateDNI(dni)
                {
                        dni = dni.replace(/[^a-z0-9\s]/gi, '').replace(' ','');

                        var lockup = 'TRWAGMYFPDXBNJZSQVHLCKE';
                        var valueDni=dni.substr(0,dni.length-1);
                        var letra=dni.substr(dni.length-1,1).toUpperCase();

                        if(lockup.charAt(valueDni % 23)==letra)
                                return true;
                        return false;
                }
                    
                $('input[name=cif]').on('focusout', function() {
                    var cif = $('input[name=cif]').val();
                    if(validateCIF(cif)==false){
                        $('input[name=cif]').css('color', '#FF0000');
                    }else{
                        $('input[name=cif]').css('color', '#000000');
                    }
                    cif = cif.replace(/[^a-z0-9\s]/gi, '').replace(' ','').toUpperCase();       
                    $('input[name=cif]').val(cif);
                    duplicadosCIF(cif);

                });

                function duplicadosCIF(doc){
                    var parametros = {
                        'valor': doc
                    };

                    $.ajax({  
                        type: 'GET',        		
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/buscadorCIFSupplier.php',
                        data: parametros,
                        success: function(data){

                            if(data>0){
                                $('input[name=cif]').css('color', '#FF0000');
                                alert('El CIF '+doc+' se encuentra repetido '+data+' veces');
                                $('input[name=cif]').val('');
                            }else{
                                $('input[name=cif]').css('color', '#000000');
                            }
                        },
                        error: function(result) { alert('Data not found');}
                    });                
                }
                
                function cambiarProvincia(valor, cargar_pagina){
                   var provincia = null;
                   if('#idprovincia' != '' && cargar_pagina){
                        provincia = '28'; 
                    }
                    var parametros = {
                        'idComunidad': valor,
                        'idProvincia': provincia
                    };

                    $.ajax({  
                        type: 'GET',        		
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/select_provinces.php',
                        data: parametros,
                        success: function(data){
                            $('#idprovincia').html(data);
                        },
                        error: function(result) { alert('Data not found');}
                    });
                }	
               
        </script>";      
        return true;
   }


   /**
    * @see CommonDBTM::getSpecificMassiveActions()
   **/
   function getSpecificMassiveActions($checkitem = null) {

      $isadmin = static::canUpdate();
      $actions = parent::getSpecificMassiveActions($checkitem);
      if ($isadmin) {
         $actions['Contact_Supplier'.MassiveAction::CLASS_ACTION_SEPARATOR.'add']
               = _x('button', 'Add a contact');

         MassiveAction::getAddTransferList($actions);
      }
      return $actions;
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
         'table'              => $this->getTable(),
         'field'              => 'id',
         'name'               => __('ID'),
         'massiveaction'      => false,
         'datatype'           => 'number'
      ];

      $tab[] = [
         'id'                 => '3',
         'table'              => $this->getTable(),
         'field'              => 'address',
         'name'               => __('Address'),
         'datatype'           => 'text'
      ];

      $tab[] = [
         'id'                 => '10',
         'table'              => $this->getTable(),
         'field'              => 'fax',
         'name'               => __('Fax'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '11',
         'table'              => $this->getTable(),
         'field'              => 'town',
         'name'               => __('City'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '14',
         'table'              => $this->getTable(),
         'field'              => 'postcode',
         'name'               => __('Postal code'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '12',
         'table'              => $this->getTable(),
         'field'              => 'state',
         'name'               => _x('location', 'State'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '13',
         'table'              => $this->getTable(),
         'field'              => 'country',
         'name'               => __('Country'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '4',
         'table'              => $this->getTable(),
         'field'              => 'website',
         'name'               => __('Website'),
         'datatype'           => 'weblink'
      ];

      $tab[] = [
         'id'                 => '5',
         'table'              => $this->getTable(),
         'field'              => 'phonenumber',
         'name'               => __('Phone'),
         'datatype'           => 'string'
      ];

      $tab[] = [
         'id'                 => '6',
         'table'              => $this->getTable(),
         'field'              => 'email',
         'name'               => _n('Email', 'Emails', 1),
         'datatype'           => 'email'
      ];

      $tab[] = [
         'id'                 => '9',
         'table'              => 'glpi_suppliertypes',
         'field'              => 'name',
         'name'               => __('Third party type'),
         'datatype'           => 'dropdown'
      ];

/*añado aqui para nuestro tipo*/
      $tab[] = [
         'id'                 => '200',
         'table'              => 'glpi_proveedortypes',
         'field'              => 'name',
         'name'               => __('Proveedor Type'),
         'datatype'           => 'dropdown'
      ];

/*hasta aqui*/

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

//      if ($_SESSION["glpinames_format"] == User::FIRSTNAME_BEFORE) {
//         $name1 = 'firstname';
//         $name2 = 'name';
//      } else {
//         $name1 = 'name';
//         $name2 = 'firstname';
//      }

      $tab[] = [
         'id'                 => '8',
         'table'              => 'glpi_contacts',
         'field'              => 'completename',
         'name'               => _n('Associated contact', 'Associated contacts', Session::getPluralNumber()),
         'forcegroupby'       => true,
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'computation'        => "CONCAT(TABLE.`$name1`, ' ', TABLE.`$name2`)",
         'computationgroupby' => true,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_contacts_suppliers',
               'joinparams'         => [
                  'jointype'           => 'child'
               ]
            ]
         ]
      ];

      $tab[] = [
         'id'                 => '16',
         'table'              => $this->getTable(),
         'field'              => 'comment',
         'name'               => __('Comments'),
         'datatype'           => 'text'
      ];

      $tab[] = [
         'id'                 => '80',
         'table'              => 'glpi_entities',
         'field'              => 'completename',
         'name'               => __('Entity'),
         'massiveaction'      => false,
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
         'id'                 => '29',
         'table'              => 'glpi_contracts',
         'field'              => 'name',
         'name'               => _n('Associated contract', 'Associated contracts', Session::getPluralNumber()),
         'forcegroupby'       => true,
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_contracts_suppliers',
               'joinparams'         => [
                  'jointype'           => 'child'
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
    * Get links for an enterprise (website / edit)
    *
    * @param $withname boolean : also display name ? (false by default)
   **/
   function getLinks($withname = false) {
      global $CFG_GLPI;

      $ret = '&nbsp;&nbsp;&nbsp;&nbsp;';

      if ($withname) {
         $ret .= $this->fields["name"];
         $ret .= "&nbsp;&nbsp;";
      }

      if (!empty($this->fields['website'])) {
         $ret .= "<a href='".Toolbox::formatOutputWebLink($this->fields['website'])."' target='_blank'>
                  <img src='".$CFG_GLPI["root_doc"]."/pics/web.png' class='middle' alt=\"".
                   __s('Web')."\" title=\"".__s('Web')."\"></a>&nbsp;&nbsp;";
      }

      if ($this->can($this->fields['id'], READ)) {
         $ret .= "<a href='".$CFG_GLPI["root_doc"]."/front/supplier.form.php?id=".
                   $this->fields['id']."'>
                  <img src='".$CFG_GLPI["root_doc"]."/pics/edit.png' class='middle' alt=\"".
                   __s('Update')."\" title=\"".__s('Update')."\"></a>";
      }
      return $ret;
   }


   /**
    * Print the HTML array for infocoms linked
    *
    *@return Nothing (display)
    *
   **/
   function showInfocoms() {
      global $DB, $CFG_GLPI;

      $instID = $this->fields['id'];
      if (!$this->can($instID, READ)) {
         return false;
      }

      $query = "SELECT DISTINCT `itemtype`
                FROM `glpi_infocoms`
                WHERE `suppliers_id` = '$instID'
                      AND `itemtype` NOT IN ('ConsumableItem', 'CartridgeItem', 'Software')
                ORDER BY `itemtype`";

      $result = $DB->query($query);
      $number = $DB->numrows($result);

      echo "<div class='spaced'><table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='2'>";
      Html::printPagerForm();
      echo "</th><th colspan='3'>";
      if ($DB->numrows($result) == 0) {
         echo __('No associated item');
      } else {
         echo _n('Associated item', 'Associated items', $DB->numrows($result));
      }
      echo "</th></tr>";
      echo "<tr><th>".__('Type')."</th>";
      echo "<th>".__('Entity')."</th>";
      echo "<th>".__('Name')."</th>";
      echo "<th>".__('Serial number')."</th>";
      echo "<th>".__('Inventory number')."</th>";
      echo "</tr>";

      $num = 0;
      for ($i=0; $i < $number; $i++) {
         $itemtype = $DB->result($result, $i, "itemtype");

         if (!($item = getItemForItemtype($itemtype))) {
            continue;
         }

         if ($item->canView()) {
            $linktype  = $itemtype;
            $linkfield = 'id';
            $itemtable = getTableForItemType($itemtype);

            $query = "SELECT `glpi_infocoms`.`entities_id`, `NAME_FIELD`, `$itemtable`.*
                      FROM `glpi_infocoms`
                      INNER JOIN `$itemtable` ON (`$itemtable`.`id` = `glpi_infocoms`.`items_id`) ";

            // Set $linktype for entity restriction AND link to search engine
            if ($itemtype == 'Cartridge') {
               $query .= "INNER JOIN `glpi_cartridgeitems`
                            ON (`glpi_cartridgeitems`.`id`=`glpi_cartridges`.`cartridgeitems_id`) ";

               $linktype  = 'CartridgeItem';
               $linkfield = 'cartridgeitems_id';
            }

            if ($itemtype == 'Consumable') {
               $query .= "INNER JOIN `glpi_consumableitems`
                            ON (`glpi_consumableitems`.`id`=`glpi_consumables`.`consumableitems_id`) ";

               $linktype  = 'ConsumableItem';
               $linkfield = 'consumableitems_id';
            }

            if ($itemtype == 'Item_DeviceControl') {
               $query .= "INNER JOIN `glpi_devicecontrols`
                           ON (`glpi_items_devicecontrols`.`devicecontrols_id`=`glpi_devicecontrols`.`id`)";
               $linktype = 'DeviceControl';
               $linkfield = 'devicecontrols_id';
            }

            $linktable = getTableForItemType($linktype);

            $query = str_replace('NAME_FIELD', $linktype::getNameField(), $query);
            $query .= "WHERE `glpi_infocoms`.`itemtype` = '$itemtype'
                             AND `glpi_infocoms`.`suppliers_id` = '$instID'".
                             getEntitiesRestrictRequest(" AND", $linktable) ."
                       ORDER BY `glpi_infocoms`.`entities_id`,
                                `$linktable`.`" . $linktype::getNameField() . "`";

            $result_linked = $DB->query($query);
            $nb            = $DB->numrows($result_linked);

            if ($nb > $_SESSION['glpilist_limit']) {
               echo "<tr class='tab_bg_1'>";
               $title = $item->getTypeName($nb);
               if ($nb > 0) {
                  $title = sprintf(__('%1$s: %2$s'), $title, $nb);
               }
               echo "<td class='center'>".$title."</td>";
               echo "<td class='center' colspan='2'>";
               $opt = ['order'      => 'ASC',
                            'is_deleted' => 0,
                            'reset'      => 'reset',
                            'start'      => 0,
                            'sort'       => 80,
                            'criteria'   => [0 => ['value'      => '$$$$'.$instID,
                                                             'searchtype' => 'contains',
                                                             'field'      => 53]]];
               $link = $linktype::getSearchURL();
               $link.= (strpos($link, '?') ? '&amp;':'?');

               echo "<a href='$link" .
                     Toolbox::append_params($opt). "'>" . __('Device list')."</a></td>";

               echo "<td class='center'>-</td><td class='center'>-</td></tr>";

            } else if ($nb) {
               for ($prem=true; $data=$DB->fetch_assoc($result_linked); $prem=false) {
                  $name = $data[$linktype::getNameField()];
                  if ($_SESSION["glpiis_ids_visible"] || empty($data["name"])) {
                     $name = sprintf(__('%1$s (%2$s)'), $name, $data["id"]);
                  }
                  $link = $linktype::getFormURLWithID($data[$linkfield]);
                  $name = "<a href='$link'>".$name."</a>";

                  echo "<tr class='tab_bg_1";
                  if (isset($data['is_template']) && $data['is_template'] == 1) {
                     echo " linked-template";
                  }
                  echo "'>";
                  if ($prem) {
                     $title = $item->getTypeName($nb);
                     if ($nb > 0) {
                        $title = sprintf(__('%1$s: %2$s'), $title, $nb);
                     }
                     echo "<td class='center top' rowspan='$nb'>".$title."</td>";
                  }
                  echo "<td class='center'>".Dropdown::getDropdownName("glpi_entities",
                                                                       $data["entities_id"])."</td>";
                  echo "<td class='center";
                  echo ((isset($data['is_deleted']) && $data['is_deleted']) ?" tab_bg_2_2'" :"'").">";
                  echo $name."</td>";
                  echo "<td class='center'>".
                         (isset($data["serial"])?"".$data["serial"]."":"-")."</td>";
                  echo "<td class='center'>".
                         (isset($data["otherserial"])? "".$data["otherserial"]."" :"-")."</td>";
                  echo "</tr>";
               }
            }
            $num += $nb;
         }
      }
      echo "<tr class='tab_bg_2'>";
      echo "<td class='center'>".(($num > 0) ? sprintf(__('%1$s = %2$s'), __('Total'), $num)
                                             : "&nbsp;")."</td>";
      echo "<td colspan='4'>&nbsp;</td></tr> ";
      echo "</table></div>";
   }
}
