<?php    
 
 /* pChart library inclusions */ 
 include("class/pData.class.php"); 
 include("class/pDraw.class.php"); 
 include("class/pImage.class.php");
 include ('ChromePhp.php');
 
 function db_connect(){
    $db_name = "ozw"; // nazov DB
    $db_host = "localhost"; // umiestnenie
    $db_user = "ozw"; // uzivatel
    $db_pass = "gramotka11"; // heslo
    @$db_link = mysql_pconnect($db_host,$db_user,$db_pass) or mysql_errno() + mysql_error();
    mysql_query("SET NAMES 'cp1250'"); 
    @$db = mysql_select_db($db_name,$db_link);
    
    if (!$db){
      echo "Chyba v spojení s DB! KONEC...";
      exit();
    }
} 

  $date1 = isset($_GET["date1"]) ? $_GET["date1"] : (date('Y-m-d', time()) + strtotime('6 hours'));
  $date2 = isset($_GET["date2"]) ? $_GET["date2"] : date('Y-m-d', time());
  $scale = isset($_GET["scale"]) ? $_GET["scale"] : "30";
  $sens = isset($_GET["sens"]) ? $_GET["sens"] : "1"; 
  $TV = isset($_GET["TV"]) ? $_GET["TV"] : "true";
  $TIn = isset($_GET["TIn"]) ? $_GET["TIn"] : "true";
  $TOut = isset($_GET["TOut"]) ? $_GET["TOut"] : "true";
  $TV2 = isset($_GET["TV2"]) ? $_GET["TV2"] : "true";
  $Solar = isset($_GET["Solar"]) ? $_GET["Solar"] : "true";
  $Cirkul = isset($_GET["Cirkul"]) ? $_GET["Cirkul"] : "true";

  //hardcode vypnuti citlivosti na zmenu
  $sens = "0,0001";

  db_connect();
  
  $date1 = urldecode($date1);
  $date2 = urldecode($date2);
  
  if ($scale == "5") $SQLScale = "";
  else if ($scale == "10") $SQLScale = "AND ((MINUTE(DateTime) = '00') OR (MINUTE(DateTime) = '10') OR (MINUTE(DateTime) = '20') OR (MINUTE(DateTime) = '30')
  OR (MINUTE(DateTime) = '40') OR (MINUTE(DateTime) = '50'))";
  else if ($scale == "15") $SQLScale = "AND ((MINUTE(DateTime) = '00') OR (MINUTE(DateTime) = '15') OR (MINUTE(DateTime) = '30') OR (MINUTE(DateTime) = '45'))";
  else if ($scale == "30") $SQLScale = "AND ((MINUTE(DateTime) = '00') OR (MINUTE(DateTime) = '30'))";
  else if ($scale == "60") $SQLScale = "AND ((MINUTE(DateTime) = '00'))";
  else if ($scale = "120") $SQLScale = "AND (MINUTE(DateTime) = '00') AND ((HOUR(DateTime) = '02') OR (HOUR(DateTime) = '04') OR (HOUR(DateTime) = '06') OR (HOUR(DateTime) = '08')
  OR (HOUR(DateTime) = '10') OR (HOUR(DateTime) = '12') OR (HOUR(DateTime) = '14') OR (HOUR(DateTime) = '16') OR (HOUR(DateTime) = '18') OR (HOUR(DateTime) = '20')
  OR (HOUR(DateTime) = '22') OR (HOUR(DateTime) = '00'))";
  else if ($scale = "180") $SQLScale = "AND (MINUTE(DateTime) = '00') AND ((HOUR(DateTime) = '03') OR (HOUR(DateTime) = '06') OR (HOUR(DateTime) = '09') OR (HOUR(DateTime) = '12')
  OR (HOUR(DateTime) = '15') OR (HOUR(DateTime) = '18') OR (HOUR(DateTime) = '21'))";
  
  // zde jsem chtel filtrovat SQL dotaz na dana data, ale radeji vyberu vse a pouze nrpidam data do datasetu obrazku
   $SQLWhat = "";
     
  /*ChromePhp::log($TIn);
  
  if ($TV == "true") $SQLWhat = $SQLWhat . ", Tepl_TV";
  if ($TIn == "true") $SQLWhat = $SQLWhat . ", Tepl_prost_TO2"; 
  if ($TOut == "true") $SQLWhat = $SQLWhat . ", Tepl_venk";           */
  
  $SQLStatement = "SELECT DATE_FORMAT(DateTime,'%d.%m %H:%i') as DateTime, T_TV, T_TO2_prostor, T_venkovni, T_TV2, T_Solar, T_Cirku, S_Cirku, S_Solar_Cerp, Vykon" . $SQLWhat . " from temperatures t WHERE 
DateTime BETWEEN '$date1' AND '$date2:59'" . $SQLScale;   

  ChromePhp::log($SQLStatement);

  $SQLResult = mysql_query($SQLStatement);
  
  $timestamp="";$Tepl_venk="";$Tepl_prost_TO2="";$Tepl_TV="";$Tepl_TV2="";$Tepl_Solar="";$Tepl_Cirkul="";$S_Cirkul="";$S_Solar="";$Vykon="";
  $timestamp_check="none";$Tepl_venk_prev="0";$Tepl_prost_TO2_prev="0";$Tepl_TV_prev="0";$Tepl_TV2_prev="0";$Tepl_Solar_prev="0";$Tepl_Cirkul_prev="0";
   
   //**//**$row=mysql_fetch_array($SQLResult);
   // ChromePhp::log($row);
                                            
   /*
  while ($row=mysql_fetch_array($SQLResult))
  {
  // v pripade ze je zmena mensi jak $sens, pak hodnoty nepreberu
  if (((abs(floatval($Tepl_TV_prev) - floatval($row["T_TV"]))) > floatval($sens)) ||
     ((abs(floatval($Tepl_venk_prev) - floatval($row["T_venkovni"]))) > floatval($sens)) ||
     ((abs(floatval($Tepl_prost_TO2_prev) - floatval($row["T_TO2_prostor"]))) > floatval($sens)) ||
     ((abs(floatval($Tepl_TV2_prev) - floatval($row["T_TV2"]))) > floatval($sens)) ||
     ((abs(floatval($Tepl_Cirkul_prev) - floatval($row["T_Cirku"]))) > floatval($sens)) ||
     ((abs(floatval($Tepl_Solar_prev) - floatval($row["T_Solar"]))) > floatval($sens)))

    {
        //ChromePhp::log("Predchozi: " . $Tepl_TV_prev);
        //ChromePhp::log("Aktualni: " . $row["Tepl_TV"]);
  
        // provadim upravu formatu datumu, urezavam DD MM YYYY v pripade ze se nemeni
        if (substr($row["DateTime"],1,4) <> substr($timestamp_check,1,4)) 
        { 
          $timestamp[]=$row["DateTime"];
          $timestamp_check = $row["DateTime"];
        }
        else
        {
          $timestamp[]=substr($row["DateTime"],5,strlen($row["DateTime"]));
        }
          
        $Tepl_venk[]=$row["T_venkovni"];
        $Tepl_venk_prev=$row["T_venkovni"];
          
        $Tepl_prost_TO2[]=$Tepl_prost_TO2_prev=$row["T_TO2_prostor"];
         
        $Tepl_TV[]=$row["T_TV"];
        $Tepl_TV_prev=$row["T_TV"];
          
        $Tepl_TV2[]=$row["T_TV2"];
        $Tepl_TV2_prev=$row["T_TV2"];
          
        $Tepl_Cirkul[]=$row["T_Cirku"];
        $Tepl_Cirkul_prev=$row["T_Cirku"];
                   
        $Tepl_Solar[]=$row["T_Solar"];
        $Tepl_Solar_prev=$row["T_Solar"];
        
        $Vykon[]=$row["Vykon"];
        
        if ($row["S_Cirku"] == "Zap") 
        {
          $S_Cirkul[]="100";
        }
        else 
        {
          $S_Cirkul[]="0";
        }
        
        if ($row["S_Solar_Cerp"] == "Zap") 
        {
          $S_Solar[]="100";
        }
        else 
        {
          $S_Solar[]="0";
        }
        
      }
  else
    {
      //ChromePhp::log("Predchozi: " . $Tepl_TV_prev);
      //ChromePhp::log("Vynechavam - Aktualni: " . $row["Tepl_TV"]);
    }
  }

 /* Create and populate the pData object */ 
 /*
 $MyData = new pData();   
 
 $MyData->addPoints($timestamp,"Timestamp");
 
 if ($TV == "true")  $MyData->addPoints($Tepl_TV,"Teplota TV");
 if ($TIn == "true") $MyData->addPoints($Tepl_prost_TO2,"Vnitøní teplota");
 if ($TOut == "true") $MyData->addPoints($Tepl_venk,"Venkovní teplota");
 if ($TV2 == "true") $MyData->addPoints($Tepl_TV2,"Teplota TV dole");
 if ($Solar == "true") $MyData->addPoints($Tepl_Solar,"Teplota Solaru");
 if ($Cirkul == "true") $MyData->addPoints($Tepl_Cirkul,"Teplota Cirkulace");
 
 $MyData->setAbscissa("Timestamp");
 
 //$MyData->setXAxisName("Time");
 //$MyData->setXAxisDisplay(AXIS_FORMAT_TIME,"H:i");
 
 $MyData->setAxisName(0,"Teplota");
 $MyData->setAxisUnit(0,"°C");
 
 if ($Cirkul == "true") $MyData->addPoints($S_Cirkul,"Stav Cirkulace");
 if ($Solar == "true") $MyData->addPoints($S_Solar,"Stav Solaru");
 $MyData->addPoints($Vykon,"Vykon kotle");
 
 $MyData->setSerieOnAxis("Stav Cirkulace",1);
 $MyData->setSerieOnAxis("Stav Solaru",1);
 $MyData->setSerieOnAxis("Výkon kotle",1);
 $MyData->setAxisName(1,"Výkon èerpadla");
 $MyData->setAxisUnit(1,"%");
 
 $myPicture = new pImage(1400,800,$MyData); 

 $myPicture->setFontProperties(array("FontName"=>"fonts/Forgotte.ttf","FontSize"=>11)); 

 $myPicture->setGraphArea(110,40,1400,700); 

 $myPicture->drawScale(array("LabelRotation"=>45)); //$scaleSettings); 
 $myPicture->drawLegend(450,9,array("BoxSize"=>10,"Surrounding"=>20,"Family"=>LEGEND_FAMILY_LINE,"Mode"=>LEGEND_HORIZONTAL,"Style"=>LEGEND_BOX));

 $MyData->setSerieDrawable("Teplota TV",FALSE);
 $MyData->setSerieDrawable("Vnitøní teplota",FALSE);
 $MyData->setSerieDrawable("Venkovní teplota",FALSE);
 $MyData->setSerieDrawable("Teplota TV dole",FALSE);
 $MyData->setSerieDrawable("Teplota Solaru",FALSE);
 $MyData->setSerieDrawable("Teplota Cirkulace",FALSE);
 $MyData->setSerieDrawable("Stav Cirkulace",TRUE);
 $MyData->setSerieDrawable("Stav Solaru",TRUE);
 $MyData->setSerieDrawable("Vykon kotle",TRUE);
 
 $serieSettings = array("R"=>235,"G"=>35,"B"=>35,"Alpha"=>30);
 $MyData->setPalette("Stav Cirkulace",$serieSettings);
 $serieSettings = array("R"=>35,"G"=>35,"B"=>235,"Alpha"=>30);
 $MyData->setPalette("Stav Solaru",$serieSettings);
 $serieSettings = array("R"=>89,"G"=>206,"B"=>65,"Alpha"=>40);
 $MyData->setPalette("Vykon kotle",$serieSettings);
 
 $myPicture->drawLineChart();

 //$MyData->setSerieDrawable("Teplota TV",TRUE);
 //$MyData->setSerieDrawable("Vnitøní teplota",TRUE);
 //$MyData->setSerieDrawable("Venkovní teplota",TRUE);
 //$MyData->setSerieDrawable("Teplota TV dole",TRUE);
 //$MyData->setSerieDrawable("Teplota Solaru",TRUE);
 //$MyData->setSerieDrawable("Teplota Cirkulace",TRUE);
 //$MyData->setSerieDrawable("Stav Cirkulace",FALSE);
 //$MyData->setSerieDrawable("Stav Solaru",FALSE);
 //$MyData->setSerieDrawable("Vykon kotle",FALSE);

if (((strtotime($date2) - strtotime($date1))/60/60/24) > 2) {
  $myPicture->drawSplineChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_AUTO,"Surrounding"=>100));
 }
 else
 {
  $myPicture->drawSplineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Surrounding"=>100));
 }
  
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10)); 
 
 $myPicture->autoOutput("pictures/example.drawLineChart.plots.png"); 
?>