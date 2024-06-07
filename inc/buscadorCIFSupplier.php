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

        $valor = $_GET['valor'];
        $sql = "select id from glpi_suppliers where cif='{$valor}'";
        $result = $DB->query($sql);
        if($result->num_rows>0){
            echo $result->num_rows;
        }else{
            echo 0;
        }

        