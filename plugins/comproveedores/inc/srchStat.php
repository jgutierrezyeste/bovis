        <?php

        /* 
         * RSU es una aplicación desarrollada por el equipo TI de 
        FOMENTO DE TÉCNICAS EXTREMEÑAS S.L. (FOTEX)
        +34924207328
        http://www.fotex.es
        Comienzo del desarrollo: enero de 2019
         */


        include ("../../../inc/includes.php");
        GLOBAL $DB,$CFG_GLPI;
        $objCommonDBT=new CommonDBTM;
        $cad = "";
        $sql = "";
        $valor = $_GET['valor'];
        if(isset($_GET['id'])){ $id = $_GET['id']; }

        if($valor==1){
            $sql = "select if(st.name is null, 'NO CONSTA', st.name) as tipo, if(color is null, '#CCCCCC', color) as color, count(prj.id) as numero
                    from glpi_projects as prj
                            left join glpi_plugin_comproveedores_servicetypes as st on st.id = prj.plugin_comproveedores_servicetypes_id
                    where prj.is_deleted = 0
                    group by 1,2
                    order by 1";

        }else{
            if($valor==2){
                $sql = "select if(clie.name is null, 'NO CONSTA', clie.name) as tipo, if(clie.color is null, '#CCCCCC', clie.color) as color, count(prj.id) as numero
                        from glpi_projects as prj
                                left join glpi_projectclientes as clie on clie.id = prj.projectclientes_id
                        where prj.is_deleted = 0
                        group by 1,2
                        order by 1";
            }else{
                if($valor==3){
                    $sql = "SELECT ganados({$id}) as numero, 'GANADO' as tipo, '#61bb61' as color 
                            UNION
                            select perdidos({$id}) as numero, 'PERDIDOS' as tipo, '#e44040' as color
                            UNION
                            select licitando({$id}) as numero, 'LICITANDO' as tipo, '#feaf04' as color";
                }
            }
        }
        
        if($sql!=""){
            $result = $DB->query($sql);

            if ($result->num_rows>0) {	
                $scr.="<script type='text/javascript'>";
                $scr.="var options = {};
                       var dataset = [];
                       var dato = [];
                       var label = [];
                       var color = [];
                       ";                
                $cad = "GRÁFICA<BR><div id='flot-placeholder' style='width: 200px; height: 200px; position: relative; border: none; padding: 10px; margin-top: 10px'></div>";
                $i = 0;
                while ($data=$DB->fetch_array($result)) { 
                    $scr.=" dato[{$i}]={$data['numero']};";
                    $scr.=" label[{$i}]='{$data['tipo']}';";
                    $scr.=" color[{$i}]='{$data['color']}';";
                    $i++;
                }   
                            
                $scr.="
                        function setVariables () {
                            for (var k in dato){
                                dataset[k] = {
                                    label: label[k], 
                                    data: dato[k], 
                                    color: color[k]
                                };
                            }

                            options = {
                                series: {
                                    pie: {
                                        show: true,    
                                        innerRadius: 0.4,
                                        label: {
                                            show: true,
                                            radius: 0.8,
                                            background: {
                                                opacity: 0.8,
                                                color: '#fff'
                                            }
                                        }
                                    }
                                },
                                legend: {
                                    show: false
                                },
                                grid: {
                                    hoverable: true,
                                    clickable: false
                                }                            
                            };
                        } 
                        setVariables();

                        $(document).ready(function () {
                            $('#flot-placeholder').plot(dataset, options);
                        });";
                
                $scr.="</script>";
                
            }else{
                $cad.="";
            }

            
        }else{
            $cad = $sql;
        }
        echo $cad.$scr;