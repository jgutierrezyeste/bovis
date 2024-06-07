<?php
/*
 * @version version 1.0.0
 -------------------------------------------------------------------------
 portada plugin for GLPI
 Copyright (C) 2014-2016 by the portada Development Team.

 https://www.fotex.es
 -------------------------------------------------------------------------

 LICENSE

 @package   portada
 @author    Fotex: Daniel Torvisco, Maria Rosa Cambero.
 @since     version 1.0
 --------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
        die("Sorry. You can't access directly to this file");
}

 
 
class PluginPortadaConfig extends CommonDBTM {

  
	
   static $rightname = "plugin_portada";

   static function getTypeName($nb=0) { 
      return __("Setup");
   }

   function showForm() {
	  
	  global $DB, $CFG_GLPI;
          
    $directory=GLPI_ROOT."/pics/trabajos";
    $dirint = dir($directory);
    
    while (($archivo = $dirint->read()) !== false)
    {
        if ($archivo != "." && $archivo != ".."){
        $lista[$archivo]=$archivo;
        
        }
    }
    $dirint->close();
    $dropdown = new Dropdown();
    
    $target = self::getFormURL();
    
    echo $this->Script();
    
    
    echo "<form name='form' method='post' action=\"$target\">";
    echo "<table class='tab_cadre_fixe' style='box-shadow: 0px 1px 2px 1px #999; !important'>";
    
        echo "<tbody>";
            echo "<tr class='headerRow'>";
              echo "<th colspan='4'>";
                echo __("Imágenes", "portada");
              echo "</th>";
            echo "</tr>";
     
            echo "<tr class='tab_bg_2'>";
              
              echo "<td>";
                echo __("Título del proyecto", "portada");
              echo "</td>";
              echo "<td>";
                Html::autocompletionTextField($this,'name');
              echo "</td>";
              echo "<td>";
                echo __("Imagen", "portada");
              echo "</td>";
              echo "<td >";
                $dropdown->showFromArray('route',$lista);
              //echo "Dropdown imagenes";     
              echo "</td>";
            echo "</tr>";

            echo "<tr class='tab_bg_2'>";
              echo "<td >";
                echo __("Texto descriptivo", "portada");
              echo "</td>";
              echo "<td colspan='2'>";
                echo "<textarea cols='45' rows='7' id='comment' name='comment' >";
                echo "</textarea>";
                echo "<input type='hidden' name='id' id='id'>";
              echo "</td>";
              echo "<td></td>";

            echo "</tr>";
            echo "<tr class='tab_bg_2'>";
            echo "<td class='center' colspan='4'>";
                echo Html::submit(_sx('button', 'Post'), array('name' => 'add'));
            echo "</td>";
            echo "</tr>";
      
        echo "</tbody>";
        
    echo "</table>";
    Html::closeForm();
    
    echo "<div style='overflow-y: scroll; height:400px;'>";
    echo "<table class='tab_cadre_fixehov' style='box-shadow: 0px 1px 2px 1px #999; border-top:1px solid #cccccc !important'>";
        echo "<tbody>";
            echo "<tr class='headerRow'>";
              echo "<th>";
                echo __("Título del proyecto", "portada");
              echo "</th>";
              echo "<th>";
                echo __("Texto descriptivo", "portada");
              echo "</th>";
              echo "<th>";
                echo __("Imagen", "portada");
              echo "</th>";
              echo "<th colspan='2'>";
                echo __("Opciones", "portada");
              echo "</th>";
            echo "</tr>";
     
            $query="SELECT id, name, comment, route "
                    . "FROM glpi_plugin_portada_config";
                $result = $DB->query($query);

                
                while ($data=$DB->fetch_array($result)) {
                    
                    echo "<tr class='tab_bg_1'>";
                        echo "<td >";
                            echo $data["name"];
                        echo "</td>";
                        echo "<td>";
                            echo $data["comment"];
                        echo "</td>";
                        echo "<td>";
                            echo $data["route"];
                        echo "</td>";
                        echo "<td >";
                            echo "<img src='".$CFG_GLPI['root_doc']."/pics/edit.png' style='cursor:pointer;' onclick='update(".$data['id'].", `". $data['name']. "`,`" . $data['comment']."`,`". $data['route'] . "`)'>";
                            
                        echo "</td>";
                        echo "<td>";// style='pointer-events: none !important'
                            echo "<img src='".$CFG_GLPI['root_doc']."/pics/delete2.png' style='cursor:pointer;' onclick='drop(".$data['id'].")'>";
                            
                        echo "</td>";
                    echo "</tr>";
                    
                };
      
        echo "</tbody>";
    echo "</table>";
    echo "</div>";
        

    
		
   }
   
   function Script(){
       $script="<script type='text/javascript'>
                 
                    function update(id, name, comment, route){

                        // alert('El id es '+ id + ' el name es ' + name + ' el comment es ' + comment + ' y la ruta es ' + route);
                         
                        $('#id').val(id);
                        $(`input[name='name']`).val(name);
                        $('#comment').val(comment);
                        $('#select2-chosen-1').text(route);
                        $(`select[name='route']`).val(route);
                    }
                    
                    function drop(id){

                         //alert('El id es '+ id );
                         
                        $.ajax({ 
                            async: false, 
                            type: 'GET',
                            data: {'drop': 1, 'id': id },                  
                            url:'../front/config.form.php',                    
                            success:function(data){
                                location.reload();
                            },
                            error: function(result) {
                                 alert('Data not found');
                                
                            }
                        });
                    }
                
                    
                </script>";
       
       
       
       return $script;
   }

   
   
   
   
   static function addPortada($item){
       global $DB;
       
     $query="INSERT INTO glpi_plugin_portada_config (name, comment, route) 
			VALUES('".$item['name']."' , '".$item['comment']."' , '".$item['route']."');";
			
	$DB->query($query);	
   }
   
   static function editPortada($item){
       global $DB;
       
     $query="UPDATE glpi_plugin_portada_config SET name='".$item['name']."' , "
             . "comment='".$item['comment']."' , route='".$item['route']."' WHERE id=".$item['id'].";";
        
	$DB->query($query);	
   }
   
   static function dropPortada($item){
       global $DB;
	   
        $query="DELETE FROM `glpi_plugin_portada_config` WHERE `id`=".$item['id'].";";
		
        $DB->query($query);
   }
   

}
?>