<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;

    $id                                 = $_GET['id'];
    $supplier_id                        = $_GET['supplier_id'];    
    $name                               = $_GET['name'];    
    $aceptacion                         = $_GET['aceptacion'];
    $comment                            = $_GET['comment'];
    $empresa_matriz_nombre              = $_GET['empresa_matriz_nombre'];
    $empresa_matriz_direccion           = $_GET['empresa_matriz_direccion'];
    $empresa_matriz_ciudad              = $_GET['empresa_matriz_ciudad'];
    $empresa_matriz_CP                  = $_GET['empresa_matriz_CP'];
    $empresa_matriz_pais                = $_GET['empresa_matriz_pais'];
    $empresa_matriz_provincia           = $_GET['empresa_matriz_provincia'];
        $aux = $_GET['fecha_aceptacion'];
    $fecha_aceptacion                   = $aux;
        $aux = $_GET['fecha_alta'];
    $fecha_alta                         = substr($aux, 6, 4).'-'.substr($aux, 3, 2).'-'.substr($aux, 0, 2);
        $aux = $_GET['fecha_ultima_modificacion'];
    $fecha_ultima_modificacion          = $aux;
    $otros_categoria_numeros_empleados  = $_GET['otros_categoria_numeros_empleados'];
    $personal                           = $_GET['personal'];
    $tecnicos_no_universitarios         = $_GET['tecnicos_no_universitarios'];
    $titulacion_grado_medio             = $_GET['titulacion_grado_medio'];
    $titulacion_superior                = $_GET['titulacion_superior'];
    $usuario_aceptacion                 = $_GET['usuario_aceptacion'];
    $usuario_alta                       = $_GET['usuario_alta'];
    $usuario_ultima_modificacion        = $_GET['usuario_ultima_modificacion'];
    
//    $aceptacion                     = "";
//    $comment                        = "";
//    $empresa_matriz_nombre          = "";
//    $empresa_matriz_direccion       = "";
//    $empresa_matriz_ciudad          = "";
//    $empresa_matriz_CP              = "";
//    $empresa_matriz_pais            = "";
//    $empresa_matriz_provincia       = "";
//    $fecha_aceptacion               = "";
//    $fecha_alta                     = "";
//    $fecha_ultima_modificacion      = "";
//    $id                             = "";
//    $name                           = "";
//    $otros_categoria_numeros_empleados = "";
//    $personal                       = "";
//    $supplier_id                    = "";
//    $tecnicos_no_universitarios     = "";
//    $titulacion_grado_medio         = "";
//    $titulacion_superior            = "";
//    $usuario_aceptacion             = "";
//    $usuario_alta                   = "";
//    $usuario_ultima_modificacion    = "";    
    $sql = "";
    
    
    if($id == 0){
        $sql = "INSERT INTO glpi_plugin_comproveedores_cvs (name,
                supplier_id,
                empresa_matriz_nombre,
                empresa_matriz_direccion,
                empresa_matriz_pais,
                empresa_matriz_ciudad,
                empresa_matriz_provincia,
                empresa_matriz_CP,
                titulacion_superior,
                titulacion_grado_medio,
                tecnicos_no_universitarios,
                personal,
                otros_categoria_numeros_empleados,
                aceptacion,
                usuario_aceptacion,                
                fecha_aceptacion,
                fecha_ultima_modificacion,
                usuario_ultima_modificacion,
                fecha_alta,
                usuario_alta, is_deleted
                ) VALUES ('{$name}',{$supplier_id},'{$empresa_matriz_nombre}','{$empresa_matriz_direccion}','{$empresa_matriz_pais}',
                '{$empresa_matriz_ciudad}','{$empresa_matriz_provincia}','{$empresa_matriz_CP}',{$titulacion_superior},{$titulacion_grado_medio},
                {$tecnicos_no_universitarios},{$personal},{$otros_categoria_numeros_empleados},{$aceptacion},
                {$usuario_aceptacion},'{$fecha_aceptacion}','{$fecha_ultima_modificacion}',{$usuario_ultima_modificacion},'{$fecha_alta}',{$usuario_alta},0)";
                
    }else{
        $sql = "UPDATE glpi_plugin_comproveedores_cvs 
                SET name='{$name}',
                    supplier_id={$supplier_id},
                    empresa_matriz_nombre='{$empresa_matriz_nombre}',
                    empresa_matriz_direccion='{$empresa_matriz_direccion}',
                    empresa_matriz_pais='{$empresa_matriz_pais}',
                    empresa_matriz_ciudad='{$empresa_matriz_ciudad}',
                    empresa_matriz_provincia='{$empresa_matriz_provincia}',
                    empresa_matriz_CP='{$empresa_matriz_CP}',
                    titulacion_superior={$titulacion_superior},
                    titulacion_grado_medio={$titulacion_grado_medio},
                    tecnicos_no_universitarios={$tecnicos_no_universitarios},
                    personal={$personal},
                    otros_categoria_numeros_empleados={$otros_categoria_numeros_empleados},
                    aceptacion={$aceptacion},
                    fecha_aceptacion='{$fecha_aceptacion}',
                    usuario_aceptacion={$usuario_aceptacion},
                    fecha_ultima_modificacion='{$fecha_ultima_modificacion}',
                    usuario_ultima_modificacion={$usuario_ultima_modificacion}
                WHERE id = {$id}";
    }
    if($supplier_id > 0){
        $result = $DB->query($sql);       
        if($id==0){
            $u = "select max(id) as maximo from glpi_plugin_comproveedores_cvs where is_deleted=0 and supplier_id={$supplier_id}";
            $r = $DB->query($u);
            while ($d = $DB->fetch_array($r)) {                           
                 $id = $d[0];
            }            
            $s = "UPDATE glpi_suppliers 
                    SET cv_id = {$id}
                   WHERE id = {$supplier_id}";    
            $modifica = $DB->query($s);
        }
   }
   echo $id;
    //echo json_encode(array("salida"=>$id, "sql"=>$sql));
    //echo $sql;        
    //echo $sql." ".$u."  ".$s;


