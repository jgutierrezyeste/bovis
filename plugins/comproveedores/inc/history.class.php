<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

class PluginComproveedoresHistory extends CommonDBTM{

    static $rightname	= "plugin_comproveedores";

    static function getTypeName($nb=0){
            return _n('Historial','Historial',1,'comproveedores');
    }

    function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
        GLOBAL $DB,$CFG_GLPI;

        $USERID = $_SESSION['glpiID'];
        $self = new self();
        $profile_Id=$self->getProfileByUserID($USERID);
        if(in_array($profile_Id, array(3,4,14,15,16))){           
            if($item-> getType()=="Supplier"){
                    return self::createTabEntry('Contratos');
            }
        }
        return '';
    }

    

    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
        //GLOBAL $DB,$CFG_GLPI;
        $self = new self();

        if($item->getType()=="Supplier"){
            if(isset($item->fields['cv_id'])){
                $self->showFormNumPreselecciones($item, $withtemplate);
            }else{
                $self->showFormNoCV($item, $withtemplate);
            }
            
        }else{        
            $self->showFormNumPreselecciones($item, $withtemplate);
        }

    }

    function getSearchOptions(){

        $tab = array();

        $tab['common'] = ('Historial');

        $tab[1]['table']	=$this->getTable();
        $tab[1]['field']	='name';
        $tab[1]['name']		=__('Name');
        $tab[1]['datatype']		='itemlink';
        $tab[1]['itemlink_type']	=$this->getTable();

        return $tab;

    }

    function registerType($type){
        if(!in_array($type, self::$types)){
                self::$types[]= $type;
        }		
    }

    static function getTypes($all=false) {
        if ($all) {
                return self::$types;
        }
// Only allowed types
        $types = self::$types;
        foreach ($types as $key => $type) {
                if (!($item = getItemForItemtype($type))) {
                        continue;
                }

                if (!$item->canView()) {
                        unset($types[$key]);
                }
        }
        return $types;
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
    
    function showFormNoCV($ID, $options=[]) {
            //Aqui entra cuando no tien gestionado el curriculum

            echo "<div>Necesitas gestionar el CV antes de ver los contratos</div>";
            //echo "<br>";
    }    

    
//                replace(concat(format(valor_contrato,0), ''), ',', '.') as presupuesto_objetivo,
//                replace(concat(format(importe_ofertado,0), ''), ',', '.') as importe_ofertado, 
//                replace(concat(format(importe_licitado_ganador(task.id),0), ''), ',', '.') as importe_ofertado_ganador,     
    
    
//                format(valor_contrato, 2) as presupuesto_objetivo,
//                format(importe_ofertado, 2) as importe_ofertado,
//                format(importe_licitado_ganador(task.id), 2) as importe_ofertado_ganador, 
//                
    //AQUÍ ENTRA DESDE EL PROVEEDOR.
    function showFormNumPreselecciones($item, $withtemplate='') {
        GLOBAL $DB, $CFG_GLPI;
    
        $USERID = $_SESSION['glpiID'];
        $self = new self();
        $profile_Id = $self->getProfileByUserID($USERID);

        if(in_array($profile_Id, array(3,4,14,15,16))){    
            $cvid = 0;
            if(isset($item->fields['cv_id'])){
                $cvid = $item->fields['cv_id'];
            }
            if(isset($item->fields['id'])){
                $id = $item->fields['id'];
            }
            
            
            echo "<input id='id' type='hidden' value='{$id}'>";
            echo "<input id='cvid' type='hidden' value='{$cvid}'>";
            $query = "select distinct project.id as projectid, 
                project.name as projectname, 
                task.id as taskid, 
                task.name as taskname, 
                DATE_FORMAT(task.ini,'%d/%m/%Y') as ini, 
                DATE_FORMAT(task.fin,'%d/%m/%Y') as fin, 
                valor_contrato as presupuesto_objetivo,
                importe_ofertado as importe_ofertado,
                importe_licitado_ganador(task.id) as importe_ofertado_ganador,
                calidad_oferta, 
                calidad_oferta_ganador(task.id) as calidad_oferta_ganador,
                comentarios,
                if((select count(teams.id) as num from glpi_projecttaskteams as teams where teams.projecttasks_id=task.id and teams.items_id=supplier.id)>0, 
                                       1, 
                                       if((select count(teams.id) as num from glpi_projecttaskteams as teams where teams.projecttasks_id=task.id and teams.items_id<>supplier.id)>0, 0, 3 )) as resultado            
                from glpi_suppliers as supplier
                    left join glpi_plugin_comproveedores_preselections as pre on supplier.id = pre.suppliers_id 
                    left join glpi_projecttasks as task on task.id = pre.projecttasks_id          
                    left join glpi_projects as project on project.id = task.projects_id
                 where supplier.cv_id = {$cvid} and task.is_delete = 0 order by 1 desc";
            $result = $DB->query($query);
            //echo $query;
            
            echo "<div id='valoraciones' align='center' style='width: 98%; height: 36em; overflow: auto; margin: 4px; position: relative; float: left; border-radius: 4px; padding: 8px; background-color: #e9ecf3;'>";
            echo "<h4>CONTRATOS Y LICITACIONES EN LOS QUE HA PARTICIPADO O PARTICIPA</h4>";
//            echo "<div id='cero' style='float: left; background-color:#f8f7f3; width: 99%; margin-bottom: 4px; padding: 4px;'>
//                <input id='informeHistoricoContratos' type='submit' class='boton_informe' value='' title='Informe del histórico de contratos' style='float:left;'/>
//            </div>";        
            echo "<div style='width: 75%; position: relative; float: left; margin-top: 5px; margin-right: 10px;
                                background-color: #e5e5e5;
                                height: 420px;
                                padding: 10px;
                                border: 2px solid #d0d0d0;
                                border-radius: 4px;'>";
            echo "<table id='tablaContratos' class='display compact'>";                                      
            echo "<thead>";
            echo "<tr>";
                echo "<th>PROYECTO</th>";
                echo "<th>CONTRATO</th>";
                echo "<th>COMIENZO</th>";
                echo "<th>FIN</th>";
                if(in_array($profile_Id, array(3,4,16))){echo "<th>PRESUPUESTO OBJETIVO<br>(miles €)</th>";}
                if(in_array($profile_Id, array(3,4,16))){echo "<th>IMPORTE OFERTADO<br>(miles €)</th>";}
                if(in_array($profile_Id, array(3,4,16))){echo "<th>IMPORTE DE ADJUDICACIÓN<br>(miles €)</th>";}           
                echo "<th>CALIDAD OFERTA</th>";
                //echo "<th>CALIDAD OFERTA GANADOR</th>";
                echo "<th>COMENTARIOS</th>";
                echo "<th>RESULTADO</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($data = $DB->fetch_array($result)) {         
                if($data['projectid']){
                    echo "<tr>";    
                        echo "<td class='left'><a href='project.form.php?id={$data['projectid']} data-hasqtip='0' aria-descripbedby='qtip-0'>".$data['projectname']."</a></td>";
                        echo "<td class='left'><a href='projecttask.form.php?id={$data['taskid']} data-hasqtip='0' aria-descripbedby='qtip-0'>".$data['taskname']."</a></td>";
                        echo "<td class='center'>".$data['ini']."</td>";
                        echo "<td class='center'>".$data['fin']."</td>";
                        $valor1 = number_format($data['presupuesto_objetivo'], 2, ',', '.');
                        $valor2 = number_format($data['importe_ofertado'], 2, ',', '.');
                        $valor3 = number_format($data['importe_ofertado_ganador'], 2, ',', '.');
                        if(in_array($profile_Id, array(3,4,16))){echo "<td class='right'>".$valor1."</td>";}
                        if(in_array($profile_Id, array(3,4,16))){echo "<td class='right'>".$valor2."</td>";}
                        if(in_array($profile_Id, array(3,4,16))){echo "<td class='right'>".$valor3."</td>";}
                        //echo "<td class='center'>".$data['calidad_oferta']."</td>";
                        if($data['calidad_oferta_ganador'] !== null){
                            echo "<td class='center'><img style='width:20px; height:20px;' title='".$data['calidad_oferta_ganador']."' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_".$data['calidad_oferta_ganador'].".png' /></td>";
                        }else{
                            echo "<td class='center'></td>";    
                        }
                        //echo "<td class='center'>".$data['calidad_oferta_ganador']."</td>";                
                        echo "<td class='left'>".$data['comentarios']."</td>";
                        echo "<td class='center'>";
                        switch ($data['resultado']) {
                        case 0:
                            echo "<img src='../pics/CHECK_no.png' style='width:18px; height:20px;' title='NO ADJUDICATARIO'/>";                            
                            break;                        
                        case 1:
                            echo "<img src='../pics/CHECK.png' style='width:18px; height:20px;' title='ADJUDICATARIO'/>";                        
                            break;                        
                        default:
                            break;
                        }
                        echo "</td>";
                    echo "</tr>";
                }
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "<div id='estadisticaLicitaciones' style='width: 260px;
            position: relative;
            float: left;
            border: 2px solid #d0d0d0;
            border-radius: 4px;
            background-color: #e5e5e5;
            margin-top: 5px;
            height: 440px;'>GRÁFICA</div>";
            //echo "<div id='printPDF' style='display: none; width: 100%; position: relative; float: left; border: 1px solid #ccc;  height: 500px;'></div>";

            echo "<script type='text/javascript'>

                        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                            'date-uk-pre': function ( a ) {
                                var ukDatea = a.split('/');
                                return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                            },
                            'date-uk-asc': function ( a, b ) {
                                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                            },
                            'date-uk-desc': function ( a, b ) {
                                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                            }
                        } ); 
                        jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a,b) {
                                var x = (a == '-') ? 0 : a.replace(',', '_').replace( /[.]/g, ',' ).replace( '_', '.');
                                var y = (b == '-') ? 0 : b.replace(',', '_').replace( /[.]/g, ',' ).replace( '_', '.');

                                x = parseFloat( x );
                                y = parseFloat( y );
    

                                //alert('x='+x+' a='+a);
                                return ((x < y) ? -1 : ((x > y) ?  1 : 0));
                        };

                        jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a,b) {
                                var x = (a == '-') ? 0 : a.replace(',', '_').replace( /[.]/g, ',' ).replace( '_', '.');
                                var y = (b == '-') ? 0 : b.replace(',', '_').replace( /[.]/g, ',' ).replace( '_', '.');
                                x = parseFloat( x );
                                y = parseFloat( y );
                                
                                //alert('x='+x+' a='+a);
                                return ((x < y) ?  1 : ((x > y) ? -1 : 0));
                        };                        
                        $('#tablaContratos').DataTable({
                            'searching':      true,
                            'scrollY':        '300px',
                            'scrollCollapse': true,
                            'ordering':       true,
                            'paging':         false,
                            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                            'order': [[ 1, 'desc' ]],
                            'aoColumns': [
                                null,
                                null,
                                { 'sType': 'date-uk' },
                                { 'sType': 'date-uk' },
                                { 'sType': 'numeric-comma'},
                                { 'sType': 'numeric-comma'},
                                { 'sType': 'numeric-comma'},
                                null,
                                null,
                                null
                            ],
                        });    
  
                        ident = $('#id').val();
                        var parametros = {'valor': 3, 'id': ident};
                        $.ajax({  
                            type: 'GET',        		
                            url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchStat.php',
                            data: parametros,
                            success:function(data){
                                $('#estadisticaLicitaciones').html(data);
                            },
                            error: function(result) { alert('Data not found');}
                        });       


                        $('#informeHistoricoContratos').on('click', function(){

                            var parametros = {};
                            $.ajax({ 
                                    async: false, 
                                    type: 'post',
                                    data: parametros,                  
                                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/informeHistoricoContratos.php',
                                    success: function(result) {

                                    }
                            });                           
                        });

            </script>";
        }else{
            echo "No posee permisos suficientes para acceder a esta pestaña, contacte con su administrador.";
        }
    }

    //AQUÍ ENTRA DESDE PROYECTOS.
    function showFormItem($item, $withtemplate='') {	
        echo "<div>ESTO...</div>";
    }


    function getColorValoracion($valor){

        switch ($valor) {
            case $valor<=1:

                $color=1;
                break;
            case $valor<=2 && $valor>1:

                $color=2;
                break;
            case $valor<=3 && $valor>2:

                $color=3;
                break;
            case $valor<=4 && $valor>3:

                $color=4;
                break;
            case $valor<=5 && $valor>4:

                $color=5;
                break;
            default:
                break;
        }

        return $color;
    }

}