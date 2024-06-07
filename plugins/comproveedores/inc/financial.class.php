<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresFinancial extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Financiero','Financiero',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Financiero');
			}
			return 'Financiero';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){
                global $CFG_GLPI;
                    $self = new self();
                    //Entrada Administrador
                    if($item->getType()=='Supplier'){	
                        if(isset($item->fields['cv_id'])){
                            $self->showFormItemFinancial($item, $withtemplate);
                        }else{
                            $self->showFormNoCV($item, $withtemplate);
                        }
                    //entrada Proveedores
                    }else if($item->getType()=='PluginComproveedoresCv'){
                        $self->showFormItem($item, $withtemplate);
                    }
		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Financiero');

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
                
                function MantenimientoFinanciero($item){
			GLOBAL $DB,$CFG_GLPI;
                        
                        
                        $USERID = $_SESSION['glpiID'];
                        $self = new self();
                        $profile_Id=$self->getProfileByUserID($USERID);
                        $ver = true;
                        if(in_array($profile_Id, array(3,4,14,16))){    
                            $ver = true;
                            echo "<input type='hidden' id='ver' value='1' />";
                        }else{
                            $ver = false;
                            echo "<input type='hidden' id='ver' value='0' />";
                        }      
			
                        
                        if($item->fields['cv_id']){
                            $CvId = $item->fields['cv_id'];
                        }else{
                            $CvId = $item->fields['id'];
                        }
                        $capital_social = '';
                        $q = "SELECT capital_social FROM glpi_plugin_comproveedores_cvs WHERE id={$CvId}";
                        $r = $DB->query($q);
                        while ($c=$DB->fetch_array($r)) {
                            $capital_social = $c['capital_social'];
                        }
                        
                        
                        if($ver === false){
                            echo "<script type='text/javascript'>"
                            . "$('#c_recherche').css('display', 'none'); "
                            . "$('#language_link').css('display', 'none'); "
                            . "$('#help_link').css('display', 'none'); "
                            . "$('#bookmark_link').css('display', 'none'); "
                            . "$('#debug_mode').css('display', 'none'); "
                            . "$('#page').css('margin', 'auto'); "
                            . "$('#c_ssmenu2').css('display', 'none');"
                            . "$('#goToList').css('display', 'none');"
                            . "$('#preferences_link').css('display', 'none');"
                            . "</script>";
                        }                        
			
			$options = array();
			$options['formtitle']    = "Financiero";
			$options['colspan']=12;
			$options['target']=$CFG_GLPI["root_doc"].'/plugins/comproveedores/front/financial.form.php';
                                                      		
			echo "<div id='contenedorFinancial' style='margin-bottom: 10px; float:left; width:98%; border-radius: 4px; padding: 8px; background-color: #e9ecf3;  overflow: auto; height: 620px; width: 125em;'>";
			echo Html::hidden('id', array('value' => $CvId));
			echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                        
                        echo "<div id='areaTrabajoFinancial' style='margin-left:5px; width: 95%; float: left; position: relative; '>";
                        if($ver){
                        echo "<div id='uno' class='center' style='margin-left:5px; width: 21%; float: left; position: relative;'>";
                            echo "<div id='dos' class='center' style='-webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);  -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); background-color: #fff; padding: 5px; border: 1px solid #ccc; border-radius: 4px; margin-top: 0px; margin-left:5px; width: 298px; height: 240px; float: left; position: relative;'>";
                                $query3 ="SELECT obl_fondos_propios, "
                                        . "obl_apalancamiento*100 as obl_apalancamiento,"
                                        . "parcial_margen_beneficio*100 as parcial_margen_beneficio,"
                                        . "parcial_efectivo_facturacion*100 as parcial_efectivo_facturacion,"
                                        . "parcial_situacion_liquidez*100 as parcial_situacion_liquidez,"
                                        . "parcial_roce*100 as parcial_roce,"
                                        . "anualidad_media_segun_facturacion_anual*100 as anualidad_media_segun_facturacion_anual FROM glpi_plugin_comproveedores_ratios" ;
                                $result3 = $DB->query($query3);
                                echo "<table style='font-size: 11px; border: none;'>";
                                while ($d=$DB->fetch_array($result3)) {
                                    $fondos_propios = $d['obl_fondos_propios'];
                                    $apalancamiento = $d['obl_apalancamiento'];
                                    $margen_beneficio = $d['parcial_margen_beneficio'];
                                    $efectivo_facturacion = $d['parcial_efectivo_facturacion'];
                                    $situacion_liquidez = $d['parcial_situacion_liquidez'];
                                    $roce = $d['parcial_roce'];
                                    $anualidad_media = $d['anualidad_media_segun_facturacion_anual'];

                                    $query4 ="select max(facturacion * ".($anualidad_media/100).") as importe
                                                from glpi_plugin_comproveedores_annualbillings
                                                where cv_id = ".$CvId." 
                                                order by anio desc
                                                limit 3";
                                    $result4 = $DB->query($query4);
                                    while ($a=$DB->fetch_array($result4)) {
                                        $aam = $a['importe'];
                                    }

                                    echo "<tr><td rowspan='2' style='padding: 4px; background-color:#d7fdd7; border: 1px solid #d7fdd7; '>RATIOS OBLIGATORIOS<br>(debe cumplir todos)</td><td class='right' style='padding: 4px; background-color:#d7fdd7; border: 1px solid #d7fdd7; '>fondos propios:</td><td style='background-color:#d7fdd7; border: 1px solid #d7fdd7; '>>".number_format($d['obl_fondos_propios'],2,',','.')."</td></tr>";
                                    echo "<tr><td class='right' style='padding: 4px; background-color:#d7fdd7; border: 1px solid #d7fdd7; '>Apalancamiento:</td><td style='padding: 4px; border: 1px solid #d7fdd7; background-color:#d7fdd7;'><".number_format($d['obl_apalancamiento'],2,',','.')."%</td></tr>";                                
                                    echo "<tr><td rowspan='4' style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>RATIOS PARCIALES<br>(debe cumplir al menos 1)</td><td class='right' style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>margen de beneficio:</td><td style='padding: 4px; border: 1px solid #f7edcf; background-color:#f7edcf;'>>".number_format($d['parcial_margen_beneficio'],2,',','.')."%</td></tr>";
                                    echo "<tr><td class='right' style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>Efectivo sobre facturación:</td><td style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>>".number_format($d['parcial_efectivo_facturacion'],2,',','.')."%</td></tr>";
                                    echo "<tr><td class='right' style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>Sitiación liquidez:</td><td style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>>".number_format($d['parcial_situacion_liquidez'],2,',','.')."%</td></tr>";
                                    echo "<tr><td class='right' style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>ROCE:</td><td style='padding: 4px; background-color:#f7edcf; border: 1px solid #f7edcf;'>".number_format($d['parcial_roce'],2,',','.')."%</td></tr>";                                
                                    echo "<tr><td colspan='2' class='right' style='font-weight: bold; padding-top: 20px;'>Anualidad media según facturación anual</td><td style='padding-top: 20px; font-weight: bold;'>".number_format($d['anualidad_media_segun_facturacion_anual'],2,',','.')."%</td></tr>";
                                }            
                                echo "</table>";
                            echo "</div>";

                            echo "<div id='tres' style='overflow: hidden; background-color: #FFF; border: 1px solid #ccc; border-radius: 4px; margin: 4px 0px 4px 4px; float: left; padding: 5px; position: relative; width: 298px; height: 250px; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
                                echo "<table style='margin-bottom: 10px; margin-left: 4px; margin-top: 10px;'>";
                                echo "<tr>";
                                    echo "<td style='font-size: 14px;'>Gráfica:</td>";
                                    echo "<td>";
                                        echo "<select id='grafica' style='font-size: 14px; padding: 4px; border-radius: 4px;'>";
                                            echo "<option value='1'>Facturación</option>";
                                            echo "<option value='2'>Beneficios AI</option>";
                                            echo "<option value='3'>Resultado</option>";
                                            echo "<option value='4'>Total Activo</option>";
                                            echo "<option value='5'>Activo Circulante</option>";
                                            echo "<option value='6'>Pasivo Circulante</option>";
                                            echo "<option value='7'>Cash flow al final del ejercicio</option>";
                                            echo "<option value='8'>Fondos propios</option>";                            
                                            echo "<option value='9'>Recursos ajenos</option>";
                                        echo "</select>";                            
                                    echo "</td>";
                                echo "</tr>";
                                echo "</table>";
                                echo "<div id='stat' style='height: 320px;'>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                        }
                        
                        //echo "<h4>INFORMACIÓN FINANCIERA</h4>";
                        echo "<table class='tab_cadre_fixe' style='width: 40%; margin-top: 4px; margin-bottom: 10px; padding: 5px; border-radius:4px; background-color:#f8f7f3; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);-moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
			echo "<tr style='font-weight: bold; font-size:12px; '>";
                            echo "<td class='right'>CAPITAL SOCIAL:</td>";
                            echo"<td class='left'>";
                            //Html::autocompletionTextField($this, "capital_social", array('value'=>$capital_social));
                            echo "<input id='capital_social' class='inputModal' value='{$capital_social}' />";
                            echo " miles €</td>";
                            echo "<td class='right' style='width: 100px;'>";
                            echo "<input id='grabarCapitalSocial' type='submit' class='boton_grabar' title='Grabar' value='' />";                            
                            echo "</td>";                                                        
                            echo "<td class='right' style='width: 100px;'>";
                            echo "<input id='addFinanciera' type='submit' class='boton_add' title='Añadir información financiera' value='' />";
                            echo "</td>";
			echo "</tr>";
                        echo "</table>";
                        
                        $query2 ="SELECT *,(100*recursos_ajenos/(fondos_propios+recursos_ajenos)) as apalancamiento, "
                                . " (100*beneficios_impuestos/facturacion) as margen_beneficio,"
                                . " (100*cash_flow/facturacion) as efectivo_sobre_facturacion,"
                                . " activo_circulante/pasivo_circulante as situacion_actual_liquidez,"
                                . " (100*beneficios_impuestos/fondos_propios) as roce FROM glpi_plugin_comproveedores_annualbillings WHERE cv_id=".$CvId." order by anio desc" ;
                        $result2 = $DB->query($query2);                        
                        if($ver){
                            echo "<div id='cuatro' class='center' style='background-color: #e6e6e6; padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 70%; margin-left:18px; height: 320px; float: left; position: relative;'>";                          
                        }else{
                            echo "<div id='cuatro' class='center' style='background-color: #e6e6e6; padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 70%; margin-left:14%; height: 450px; float: left; position: relative;'>";                                                      
                        }
                            
                            echo "<table id='tablaFinanciero' class='compact hover' style='width:100%; max-height: 350px;'>";
                            echo "<thead>";
                                echo "<tr class='tab_bg_1 center'>";
                                    echo "<th></th>";
                                    echo "<th></th>";
                                    echo "<th>Año</th>";
                                    echo "<th title='facturación'>Fact. (miles €)</th>";
                                    echo "<th title='beneficions antes de impuestos'>Beneficios AI (miles €)</th>";
                                    echo "<th title='resultado'>Res. (miles €)</th>";
                                    echo "<th title='total activo'>Tot. Activo (miles €)</th>";
                                    echo "<th title='activo circulante'>Act. circ. (miles €)</th>";
                                    echo "<th title='pasivo circulante'>Pas. circ. (miles €)</th>";
                                    echo "<th title='cash flow al final del ejercicio'>Cash flow (miles €)</th>";
                                    echo "<th title='recursos ajenos'>Rec. ajenos (miles €)</th>";
                                    echo "<th title='fondos propios'>Fondos prop. (miles €)</th>";    
                                    if($ver){
                                        echo "<th title='apalancamiento'>Apa.</th>";                                    
                                        echo "<th title='margen de beneficios'>Margen</th>";
                                        echo "<th title='efectivo sobre facturación'>Efec. sobre Fact.</th>";
                                        echo "<th title='situación actual de liquidez'>Sit. Liq.</th>";
                                        echo "<th title='roce'>Roce</th>";
                                    }
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            $num = 1;
                            $obl = 0;
                            $prc = 0;                            
                            while ($data=$DB->fetch_array($result2)) {
                                echo"<tr id='idannualbilings_".$data['id']."' >";
                                        echo "<td><input type='submit' title='Editar' id='edit_".$data['id']."' class='boton_editar' value=''/></td>";
                                        echo "<td><input type='submit' title='Quitar' id='delete_".$data['id']."' class='boton_borrar' value='' style='margin:0px !important;'/></td>";
                                        echo "<td id='anio_".$data['id']."'>";
                                        echo $data['anio'];
                                        echo "</td>";
                                        echo "<td id='facturacion_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['facturacion']!=''){echo number_format($data['facturacion'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='beneficios_impuestos_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['beneficios_impuestos']!=''){echo number_format($data['beneficios_impuestos'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='resultado_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['resultado']!=''){echo number_format($data['resultado'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='total_activo_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['total_activo']!=''){echo number_format($data['total_activo'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='activo_circulante_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['activo_circulante']!=''){echo number_format($data['activo_circulante'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='pasivo_circulante_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['pasivo_circulante']!=''){echo number_format($data['pasivo_circulante'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='cash_flow_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['cash_flow']!=''){echo number_format($data['cash_flow'],2,',','.');}
                                        echo "</td>";
                                        echo "<td id='recursos_ajenos_".$data['id']."' style='font-size: 10px;'>";
                                        if($data['recursos_ajenos']!=''){echo number_format($data['recursos_ajenos'],2,',','.');}
                                        echo "</td>";
                                        if($ver){
                                            echo "<td id='fondos_propios_".$data['id']."' style='font-size: 10px; background-color: #d7fdd7'>";
                                        }else{
                                            echo "<td id='fondos_propios_".$data['id']."' style='font-size: 10px;'>";
                                        }
                                        if($data['fondos_propios']!=''){
                                            echo number_format($data['fondos_propios'],2,',','.');
                                            if($data['fondos_propios']>$fondos_propios){
                                                if($ver){echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";}
                                                if($num==1)$obl++;
                                            }else{
                                                if($ver){echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}   
                                            }
                                        }
                                        echo "</td>";     
                                        if($ver){
                                            echo "<td id='apalancamiento_".$data['id']."' style='font-size: 10px; background-color: #d7fdd7;'>";
                                            if($data['apalancamiento']!=''){
                                                echo number_format($data['apalancamiento'],2,',','.')."%<br>";
                                                if($data['apalancamiento']<$apalancamiento){
                                                    echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                                                    if($num==1)$obl++;
                                                }else{echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}                                              
                                            }
                                            echo "</td>";                                        
                                            echo "<td id='margen_beneficio_".$data['id']."' style='font-size: 10px; background-color: #f7edcf;'>";
                                            if($data['margen_beneficio']!=''){
                                                echo number_format($data['margen_beneficio'],2,',','.')."%<br>";     
                                                if($data['margen_beneficio']>$margen_beneficio){
                                                    echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                                                    if($num==1)$prc++;
                                                }else{echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}                                            
                                            }
                                            echo "</td>";                                                                      
                                            echo "<td id='efectivo_sobre_facturacion_".$data['id']."' style='font-size: 10px; background-color: #f7edcf;'>";
                                            if($data['efectivo_sobre_facturacion']!=''){
                                                echo number_format($data['efectivo_sobre_facturacion'],2,',','.')."%<br>";
                                                if($data['efectivo_sobre_facturacion']>$efectivo_facturacion){
                                                    echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                                                    if($num==1)$prc++;
                                                }else{echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}                                              
                                            }
                                            echo "</td>";                                                        
                                            echo "<td id='situacion_actual_liquidez_".$data['id']."' style='font-size: 10px; background-color: #f7edcf;'>";
                                            if($data['situacion_actual_liquidez']!=''){
                                                echo number_format($data['situacion_actual_liquidez'],2,',','.')." <br>";
                                                if($data['situacion_actual_liquidez']>$situacion_liquidez){
                                                    echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                                                    if($num==1)$prc++;
                                                }else{echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}                                              
                                            }
                                            echo "</td>";                                                                   
                                            echo "<td id='roce_".$data['id']."' style='font-size: 10px; background-color: #f7edcf;'>";
                                            if($data['roce']!=''){
                                                echo number_format($data['roce'],2,',','.')."%<br>";
                                                if($data['roce']>$roce){
                                                    echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                                                    if($num==1)$prc++;
                                                }else{echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";}                                               
                                            }
                                            echo "</td>";  
                                        }
                                $num++;
                                echo "</tr>";
                            }
                            echo "</tbody>";

                            echo "</table>";
			echo "</div>";
                        if($ver){
                            echo "<div id='formulas'  style='font-size: 12px;text-align: left; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);  -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); background-color: #f8f7f3; padding: 10px 0px 0px 10px; border: 1px solid #ccc; border-radius: 4px; margin-top: 4px; margin-left:15px; width: 380px; height: 100px; float: left; position: relative;'>";
                            echo "Apalancamiento = Rec. ajenos /(Fondos prop. + Rec. ajenos)<br>";
                            echo "Margen = Beneficios AI / Facturación<br>";
                            echo "Efec. sobre Fact. = Cash flow / Fact.<br>";
                            echo "Sit. Liq. = Activo circ. / Pasivo circ.<br>";
                            echo "ROCE = Benef. AI / Fact.";
                            echo "</div>";
                        }


                        if($ver){
                            echo "<div id='presultado' style='-webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);  box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);background-color: #FFF; border: 1px solid #ccc; border-radius: 4px; margin: 4px 0px 4px 16px; float: left; padding: 2px; position: relative; width: 700px; height: 110px'>";
                            echo "<h3>Resultado preliminar de la evaluación del proveedor</h3>";
                            if($obl==2 && $prc>=1){
                                echo "<table class='center' style='margin-left:10%; margin-top: 2px; font-size: 24px; font-weight: bold;'>";
                                echo "<tr>";                            
                                    echo "<td>";
                                        echo "<img src='".$CFG_GLPI["root_doc"]."/pics/si.png'/>";
                                    echo "</td>";
                                    echo "<td class='center' style='font-size: 18px; vertical-align: top; padding: 4px; color: #41ad49;'>";
                                        echo "CUMPLE RATIOS OBLIGATORIOS Y ALGUNO DE LOS OTROS CUATRO";
                                    echo "</td>";
                            }else{
                                echo "<table class='center' style='margin-left:18%; margin-top: 2px; font-size: 24px; font-weight: bold;'>";
                                echo "<tr>";                               
                                    echo "<td style='padding-top: 0px; padding-right: 16px;'>";
                                        echo "<img src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";
                                    echo "</td>";
                                    echo "<td style='vertical-align: top; padding: 4px; color: #ae0d0d; font-size: 30px;'>";
                                        echo "NO CUMPLE LAS CONDICIONES";
                                    echo "</td>";                            
                            }
                            echo "</tr>";
                            echo "<tr style='font-size: 16px; color: #0e52a0; '><td colspan='2' class='center' style='font-weight: bold; padding: 4px;'>ANUALIDAD MEDIA MÁXIMA = ".number_format($aam,2,',','.')." miles €</td></tr>";                        
                            echo "</table>";
                            echo "</div>";
                        }


                        echo "<div id='modalFinanciera' title='INFORMACIÓN FINANCIERA DE LA EMPRESA'>";
                            echo "<input type='hidden' id='idfinancial' value='' >";
                            echo "<input type='hidden' id='cvid' value='{$CvId}' >";
                            echo "<table>";
                            echo "<tr class='tab_bg_1'>";
                                echo "<td class='right'>Año:</td><td><input type='text' class='inputModal' value='' id='anio'>";
                                echo "<td class='right'>Resultado:</td><td><input type='text' class='inputModal' value='' id='resultado'> miles €</td>";                      
                            echo "</tr>";
                            echo "<tr class='tab_bg_1'>";
                                echo "<td class='right'>Facturación:</td><td><input type='text' class='inputModal' value='' id='facturacion'> miles €</td>";
                                echo "<td class='right'>Beneficios AI:</td><td><input type='text' class='inputModal' value='' id='beneficiosai'> miles €</td>";
                            echo "</tr>";                          
                            echo "<tr class='tab_bg_1'>";                                 
                                echo "<td class='right'>Cash Flow al final del ejercicio:</td><td><input type='text' class='inputModal' value='' id='cashflow'> miles €</td>";
                                echo "<td class='right'>Total Activo:</td><td><input type='text' class='inputModal' value='' id='totalactivo'> miles €</td>";                            
                            echo "</tr>";                        
                            echo "<tr class='tab_bg_1'>";                            
                                echo "<td class='right'>Activo Circulante:</td><td><input type='text' class='inputModal' value='' id='activocirculante'> miles €</td>";
                                echo "<td class='right'>Pasivo Circulante:</td><td><input type='text' class='inputModal' value='' id='pasivocirculante'> miles €</td>";                            
                            echo "</tr>";                            
                            echo "<tr class='tab_bg_1'>";                            
                                echo "<td class='right'>Fondos Ajenos:</td><td><input type='text' class='inputModal' value='' id='fondosajenos'> miles €</td>";
                                echo "<td class='right'>Fondos Propios:</td><td><input type='text' class='inputModal' value='' id='fondospropios'> miles €</td>";                            
                            echo "</tr>";                                               
                            echo "</table>";   
                        echo "</div>";
                        echo "</div>";
                        if($ver){
                            echo "<p style='position: relative; float: left; width: 98%; 
                                font-size: 10px; margin-top: 4px; padding: 10px; border-radius: 4px; background-color: #f8f7f3; 
                                -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);
                                -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); 
                                box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>
                                (**) El cumplimiento con los criterios se realizan sobre el último año que se disponga información.<br>
                                </p>";
                        }

                        if($ver === false){
                            echo "<script type='text/javascript'>"
                            . "$('#c_recherche').css('display', 'none'); "
                            . "$('#language_link').css('display', 'none'); "
                            . "$('#help_link').css('display', 'none'); "
                            . "$('#bookmark_link').css('display', 'none'); "
                            . "$('#debug_mode').css('display', 'none'); "
                            . "$('#page').css('margin', 'auto'); "
                            . "$('#c_ssmenu2').css('display', 'none');"
                            . "$('#goToList').css('display', 'none');"
                            . "$('#preferences_link').css('display', 'none');"
                            . "</script>";
                        }                           
                        
                        echo "<script type='text/javascript'>

                            function formateaNumero(numero){
                                var num = numero.replace(/\./g,'');
                                if(!isNaN(num)){
                                    num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
                                    num = num.split('').reverse().join('').replace(/^[\.]/,'');
                                }
                                return num;                            
                            }
                            var num = '0';
                            num = $('#capital_social').val().replace(/[^0-9,.]/g, '').replace(/,/g, '.');
                            $('#capital_social').val(formateaNumero(num));

                            
                            if(({$profile_Id} != 16) && ({$profile_Id} != 3) && ({$profile_Id} != 4) && ({$profile_Id} != 14)){
                                $('.chkProp').prop('disabled', 'true');
                                $('.boton_borrar').css('display', 'none');
                                $('.boton_add').css('display', 'none');
                                $('.boton_editar').css('display', 'none');
                                $('.boton_grabar').css('display', 'none');
                                $('#cero').css('display', 'none');
                                $('textarea').prop('disabled', 'true');
                            }

                            $('#addFinanciera').on('click', function(){
                                $('#idfinancial').val(0);

                                $('#anio').val('');
                                $('#facturacion').val('');
                                $('#beneficiosai').val('');
                                $('#totalactivo').val('');
                                $('#activocirculante').val('');
                                $('#pasivocirculante').val('');
                                $('#cashflow').val('');
                                $('#fondospropios').val('');
                                $('#fondosajenos').val('');
                                $('#resultado').val('');
                                

                                $('#modalFinanciera').dialog('open');

                            });

                            
                            $('#grabarCapitalSocial').on('click', function(){
                                var cs = $('#capital_social').val().split('.').join('').replace(',','.');
                                var cvid = {$CvId};
                                $.ajax({ 
                                        async: false, 
                                        type: 'GET',
                                        data: {'capital_social': cs, 'id': cvid},
                                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateCS.php',  				
                                        success:function(data){
                                            alert(data);
                                        },
                                        error: function(result) {
                                            alert('Error al actualizar el capital social.');
                                        }
                                });                                 
                            });
                            
                            function cambiaGrafica() {
                                var cvid = {$CvId};      
                                var valor = $('#grafica').val();
                                $.ajax({ 
                                        async: false, 
                                        type: 'GET',
                                        data: {'valor': valor,
                                        'id': cvid},                  
                                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchStatBar.php',  				
                                        success:function(data){
                                            $('#stat').html(data);
                                        },
                                        error: function(result) {
                                            alert('No existen datos para la gráfica!');
                                        }
                                });                            
                            }
                            
                            $('#grafica').on('click', function(){
                                cambiaGrafica();
                            });                            
                            
                            $('.inputModal').on('focus', function () {
                                $(event.target).select();
                            });      
                            

                            
                            $('.inputModal').on('input', function(){
                                this.value = this.value.replace(/[^0-9,.-]/g, '').replace(/,/g, '.');
                                this.value = formateaNumero(this.value);
                            });
                            
                            $('.boton_editar').on('click', function(){
                                var id = $(this).prop('id').replace('edit_','');
                                var aux = '';
                                aux = '#anio_'+id;
                                var anio = $(aux).text().trim().replace('€','');
                                aux = '#facturacion_'+id;
                                var facturacion = $(aux).text().trim().replace('€','');
                                aux = '#beneficios_impuestos_'+id;
                                var beneficios_impuestos = $(aux).text().trim().replace('€','');
                                aux = '#resultado_'+id;
                                var resultado = $(aux).text().trim().replace('€','');                                  
                                aux = '#total_activo_'+id;
                                var total_activo = $(aux).text().trim().replace('€','');
                                aux = '#activo_circulante_'+id;
                                var activo_circulante = $(aux).text().trim().replace('€','');
                                aux = '#pasivo_circulante_'+id;
                                var pasivo_circulante = $(aux).text().trim().replace('€','');
                                aux = '#cash_flow_'+id;
                                var cash_flow = $(aux).text().trim().replace('€','');
                                aux = '#fondos_propios_'+id;
                                var fondos_propios = $(aux).text().trim().replace('€','');
                                aux = '#recursos_ajenos_'+id;
                                var recursos_ajenos = $(aux).text().trim().replace('€','');  

                                
                                $('#idfinancial').val(id);
                                $('#anio').val(anio);
                                $('#facturacion').val(facturacion);
                                $('#beneficiosai').val(beneficios_impuestos);
                                $('#totalactivo').val(total_activo);
                                $('#activocirculante').val(activo_circulante);
                                $('#pasivocirculante').val(pasivo_circulante);
                                $('#cashflow').val(cash_flow);
                                $('#fondospropios').val(fondos_propios);
                                $('#fondosajenos').val(recursos_ajenos);
                                $('#resultado').val(resultado);

                                $('#modalFinanciera').dialog('open');
                            });

                            $('.boton_borrar').on('click', function(){
                                var resp = confirm('¿Realmente desea quitar este ejercicio económico?','Quitar año');
                                if(resp){
                                        var idfinancial = $(this).prop('id').replace('delete_','');
                                        $.ajax({ 
                                                async: false, 
                                                type: 'GET',
                                                data: {'idfinancial':idfinancial},                  
                                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarFinancial.php',  				
                                                success:function(data){
                                                        window.location.reload(true);
                                                        //alert(data);
                                                },
                                                error: function(result) {
                                                        alert('Data not found!');
                                                }
                                        });                                
                                }
                            });
                            
                            if($('#ver').val() == 1){
                                $('#tablaFinanciero').DataTable({
                                    'searching':      true,
                                    'scrollY':        '400px',
                                    'scrollCollapse': true,
                                    'paging':         false,
                                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                                    'dom': 'Bfrtip',
                                    'buttons': [
                                        'copyHtml5',
                                        'excelHtml5',
                                        'pdfHtml5'
                                    ]                      
                                });          
                            }else{
                                $('#tablaFinanciero').DataTable({
                                    'searching':      true,
                                    'scrollY':        '400px',
                                    'scrollCollapse': true,
                                    'paging':         false,
                                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                 
                                });                               
                            }
                            
                            $('#modalFinanciera').dialog({
                                autoOpen: false,
                                height: 250,
                                width: 720,
                                modal: true,
                                buttons: {
                                    'Aceptar': function() { 
                                        var anio = null;
                                        if($('#anio').val().length>0){
                                            anio = $('#anio').val().replace('.','');
                                        }
                                        var resultado = null;
                                        if($('#resultado').val().length>0) {
                                            resultado = $('#resultado').val().split('.').join('').replace(',','.');
                                        }                                        
                                        var facturacion = null;
                                        if($('#facturacion').val().length>0) {
                                            facturacion= $('#facturacion').val().split('.').join('').replace(',','.');
                                        }
                                        var beneficiosai = null;
                                        if($('#beneficiosai').val().length>0) {
                                            beneficiosai = $('#beneficiosai').val().split('.').join('').replace(',','.');
                                        }
                                        var cashflow=null;
                                        if($('#cashflow').val().length>0) {
                                            cashflow=$('#cashflow').val().split('.').join('').replace(',','.');
                                        }
                                        var totalactivo=null;
                                        if($('#totalactivo').val().length>0) {
                                            totalactivo=$('#totalactivo').val().split('.').join('').replace(',','.');
                                        }
                                        var activocirculante=null;
                                        if($('#activocirculante').val().length>0) {
                                           activocirculante=$('#activocirculante').val().split('.').join('').replace(',','.');
                                        }
                                        var pasivocirculante=null;
                                        if($('#pasivocirculante').val().length>0) {
                                           pasivocirculante=$('#pasivocirculante').val().split('.').join('').replace(',','.');
                                        }
                                        var fondosajenos = null;
                                        if($('#fondosajenos').val().length>0){
                                            fondosajenos = $('#fondosajenos').val().split('.').join('').replace(',','.');
                                        }                                     
                                        var fondospropios=null;
                                        if($('#fondospropios').val().length>0) {
                                            fondospropios=$('#fondospropios').val().split('.').join('').replace(',','.');
                                        }
                                        var cvid = {$CvId};
                                        var idfinancial = $('#idfinancial').val().split('.').join('').replace(',','.');
                                        var strUrl = '';
                                        
                                        if(idfinancial>0){
                                            strUrl = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateFinancial.php';
                                        }else{
                                            strUrl = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertFinancial.php';
                                        }
                                        $.ajax({ 
                                                async: false, 
                                                type: 'GET',
                                                data: {'anio':anio,	
                                                       'facturacion':facturacion,
                                                       'beneficiosai':beneficiosai,
                                                       'resultado':resultado,
                                                       'totalactivo':totalactivo,
                                                       'activocirculante':activocirculante,
                                                       'pasivocirculante':pasivocirculante,
                                                       'cashflow':cashflow,
                                                       'fondosajenos':fondosajenos,                                                       
                                                       'fondospropios':fondospropios,
                                                       'cvid': cvid,
                                                       'idfinancial': idfinancial},                  
                                                url: strUrl,  				
                                                success:function(data){
                                                        window.location.reload(true);
                                                        //alert(data);
                                                },
                                                error: function(result) {
                                                        alert('Data not found!');
                                                }
                                        });				
                                        $('#modalFinanciera').dialog('close');
                                    },
                                    'Cancelar': function() {
                                      $('#modalFinanciera').dialog('close');
                                    }
                                },
                                close: function() {
                                    $('#modalFinanciera').dialog('close');
                                }
                            });
                            cambiaGrafica();
                        </script>";               
                        
                }
                
                /* solo para USUARIOS de BOVIS */
		function showFormItemFinancial($item, $withtemplate='') {	
                    GLOBAL $DB,$CFG_GLPI;

                    $self = new self();
                    $self->MantenimientoFinanciero($item);
                    
		}


		function showFormNoCV($ID, $options=[]) {
			//Aqui entra cuando no tien gestionado el curriculum

			echo "<div>Necesita gestionar el CV antes de acceder a Financiero</div>";
			echo "<br>";
		}

		function showForm($ID, $options=[]) {
                    
		}

                
                /*para el usuario autorizado del proveedor*/
		function showFormItem($item, $withtemplate='') {	
			GLOBAL $DB,$CFG_GLPI;
                        $self = new self();
                        $self->MantenimientoFinanciero($item);
                        
		}
                
                function Jquery(){
                    GLOBAL $CFG_GLPI;
                    $consulta="<script type='text/javascript'>
                        
                    $(document).ready(function() {
                                                                    
                            //Adaptar la página al tamaño de la tabla
                            $('body').css('display','inline-block');
                            $('#page').css('width','1600px');
                            
                            $('.ui-tabs-anchor').click(function() {
                            
                                if($(this).attr('title')!='Financiero'){
                                   $('body').css('display','block');
                                    $('#page').css('width','auto');
                               }
                               else {
                                     $('body').css('display','inline-block');
                                     $('#page').css('width','1600px');
                               }
                               
                            });
                                  
                        });
                    </script>";
                    
                    return $consulta;
                                  
                }

}