<?php    
// pChart temperature graph by L.Renner
// version 3.0 - pocita prumerne hodnoty ze vsech hodnot v DB v danem casovem intervalu
// minula verze brala v uvahu pouze exaktni hodnoty v danem case, ostatni ignorovala
//TO-DO:
// 1. vyresit v jakem case (timestamp) zobrazit prumernou hodnotu, aktualne se zobrazuje prumerna hodnota napr. za pul hodinu k prvni minute dane pulhodiny
//


// 26/3/2016 ChromePHP is not funcionall, I dont know why, all rows with chomephp commented 
 
 /* pChart library inclusions */ 
 include("class/pData.class.php"); 
 include("class/pDraw.class.php"); 
 include("class/pImage.class.php");
 include ("ChromePhp.php");
 
 
 
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
  
  // vzdy vyberu vsechny hodnoty
  $SQLScale = "";
  
  if ($scale == "5") $Multi='1';
   else if ($scale == "10")  $Multi='2';
    else if ($scale == "15") $Multi='3';
    else if ($scale == "30") $Multi='6';
    else if ($scale == "60") $Multi='12';
    else if ($scale = "120") $Multi='24';
    else if ($scale = "180") $Multi='48';
  
  // zde jsem chtel filtrovat SQL dotaz na dana data, ale radeji vyberu vse a pouze nepidam data do datasetu obrazku
   $SQLWhat = "";
     
  //ChromePhp::log($TIn);
  
  /*if ($TV == "true") $SQLWhat = $SQLWhat . ", Tepl_TV";
  if ($TIn == "true") $SQLWhat = $SQLWhat . ", Tepl_prost_TO2"; 
  if ($TOut == "true") $SQLWhat = $SQLWhat . ", Tepl_venk";           */
  
  $SQLStatement = "SELECT DATE_FORMAT(DateTime,'%d.%m %H:%i') as DateTime, T_TV, T_TO2_prostor, T_venkovni, T_TV2, T_Solar, T_Cirku, S_Cirku, S_Solar_Cerp, Vykon" . $SQLWhat . " from temperatures t WHERE 
DateTime BETWEEN '$date1' AND '$date2:59'" . $SQLScale;   
  ChromePhp::log($SQLStatement);
  
  $SQLResult = mysql_query($SQLStatement);
  
  $timestamp="";$Tepl_venk="";$Tepl_prost_TO2="";$Tepl_TV="";$Tepl_TV2="";$Tepl_Solar="";$Tepl_Cirkul="";$S_Cirkul="";$S_Solar="";$Vykon="";
  $timestamp_check="none";$Tepl_venk_prev="0";$Tepl_prost_TO2_prev="0";$Tepl_TV_prev="0";$Tepl_TV2_prev="0";$Tepl_Solar_prev="0";$Tepl_Cirkul_prev="0";
    
  $Tepl_venk_temp=$Tepl_prost_TO2_temp="none";
  
 // $row=mysql_fetch_array($SQLResult);
 // ChromePhp::log($row);
  
 while ($row=mysql_fetch_array($SQLResult))
    { 
  $Tepl_venk_temp=$Tepl_prost_TO2_temp=$Tepl_TV_temp=$Tepl_TV_temp=$Tepl_TV2_temp=$Tepl_Cirkul_temp=$Tepl_Solar_temp=$Vykon_temp=$S_Cirkul_temp2=
  $S_Cirkul_temp=$S_Solar_temp=$S_Solar_temp2="none";
  $i=0;
  
      // v pripade ze je zmena mensi jak $sens, pak hodnoty nepreberu
      //tohle je podle me zbytecne, davam do poznamky pro pozdejsi vyvoj
       //if (((abs(floatval($Tepl_TV_prev) - floatval($row["T_TV"]))) > floatval($sens)) ||
        // ((abs(floatval($Tepl_venk_prev) - floatval($row["T_venkovni"]))) > floatval($sens)) ||
        // ((abs(floatval($Tepl_prost_TO2_prev) - floatval($row["T_TO2_prostor"]))) > floatval($sens)) ||
        // ((abs(floatval($Tepl_TV2_prev) - floatval($row["T_TV2"]))) > floatval($sens)) ||
        // ((abs(floatval($Tepl_Cirkul_prev) - floatval($row["T_Cirku"]))) > floatval($sens)) ||
        // ((abs(floatval($Tepl_Solar_prev) - floatval($row["T_Solar"]))) > floatval($sens)))
   // { */
        //ChromePhp::log("Predchozi: ". $Tepl_TV_prev);
       //ChromePhp::log("Aktualni: " . $row["T_TV"]);  
       
       
       
    for ($x=1;$x<=$Multi;$x++) {
    //$timestamp_temp = (row["DateTime"]+$timestamp_temp)/2; 
    if ($Tepl_venk_temp <> "none") {
    $i++;
      $Tepl_venk_temp = ($row["T_venkovni"] + $Tepl_venk_temp );
      $Tepl_prost_TO2_temp = ($row["T_TO2_prostor"] + $Tepl_prost_TO2_temp);
      $Tepl_TV_temp = ($row["T_TV"] + $Tepl_TV_temp);
      $Tepl_TV2_temp = ($row["T_TV2"] + $Tepl_TV2_temp);
      $Tepl_Cirkul_temp = ($row["T_Cirku"] + $Tepl_Cirkul_temp);
      $Tepl_Solar_temp = ($row["T_Solar"] + $Tepl_Solar_temp);
      $Vykon_temp = ($row["Vykon"] + $Vykon_temp);
      
      if ($row["S_Cirku"] == "Zap") { $S_Cirkul_temp2="100"; }
      else { $S_Cirkul_temp2="0"; }
      
      $S_Cirkul_temp = ($S_Cirkul_temp + $S_Cirkul_temp2);
      
      if ($row["S_Solar_Cerp"] == "Zap") { $S_Solar_temp2 ="100"; }
        else { $S_Solar_temp2 ="0"; }
      
      $S_Solar_temp = ($S_Solar_temp + $S_Solar_temp2);
    }
    // jeslti mam prvni hodnotu, pak nepocitam prumer, to by mi kazilo vypocet
    else  {
    $i++;
      $Tepl_venk_temp = $row["T_venkovni"];
      $Tepl_prost_TO2_temp = $row["T_TO2_prostor"];
      $Tepl_TV_temp = $row["T_TV"];
      $Tepl_TV2_temp =  $row["T_TV2"];
      $Tepl_Cirkul_temp = $row["T_Cirku"];
      $Tepl_Solar_temp = $row["T_Solar"];
      $Vykon_temp = $row["Vykon"];
      
      if ($row["S_Solar_Cerp"] == "Zap") { $S_Solar_temp ="100"; }
        else { $S_Solar_temp ="0"; }
        
      if ($row["S_Cirku"] == "Zap") { $S_Cirkul_temp = "100"; }
      else { $S_Cirkul_temp = "0"; }
      }
      
    if ($x==1) $DateTimePicked = $row["DateTime"];
    
    //ChromePhp::log($x.":".$row["DateTime"].":".$row["T_venkovni"]);
    
    // jestli mam pocitat prumer, tak pokracuji, jinak koncim
    if ($x<$Multi) $row=mysql_fetch_array($SQLResult);
    
    //vyhozeni pro jiz neexistujici datum, tj. dorazil jsem na konec odpovedi v selectu
    if  ($row["DateTime"] == null) break;
    
    } 
    
    //ChromePhp::log($DateTimePicked);  
    
    // provadim upravu formatu datumu, urezavam DD MM YYYY v pripade ze se nemeni         
    if (substr($DateTimePicked,1,4) <> substr($timestamp_check,1,4)) 
        { 
         
            $timestamp[] = $timestamp_check = $DateTimePicked;
        }
        else
        {
            $timestamp[]=substr($DateTimePicked,5,strlen($DateTimePicked));
        }
         
        //$Multi2=$Multi+1;  
        $Tepl_venk[]= round($Tepl_venk_temp/$i,1);                              
        $Tepl_prost_TO2[]= round($Tepl_prost_TO2_temp/$i,1);
        $Tepl_TV[]= round($Tepl_TV_temp/$i,1);
        $Tepl_TV2[]= round($Tepl_TV2_temp/$i,1);
        $Tepl_Cirkul[] = round($Tepl_Cirkul_temp/$i,1);
        $Tepl_Solar[]= round($Tepl_Solar_temp/$i,1);
        $Vykon[]= round($Vykon_temp/$i,1);
        $S_Cirkul[] = round($S_Cirkul_temp/$i);
        $S_Solar[] = round($S_Solar_temp/$i);
        
  }
 /* Create and populate the pData object */ 
 
 
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
 $MyData->setSerieOnAxis("Vykon kotle",1);
 $MyData->setAxisName(1,"Výkon");
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
 $serieSettings = array("R"=>89,"G"=>206,"B"=>65,"Alpha"=>40);
 $MyData->setPalette("Vykon kotle",$serieSettings);
 $serieSettings = array("R"=>35,"G"=>35,"B"=>235,"Alpha"=>30);
 $MyData->setPalette("Stav Solaru",$serieSettings);
 
 $myPicture->drawLineChart();
 $MyData->setSerieDrawable("Teplota TV",TRUE);
 $MyData->setSerieDrawable("Vnitøní teplota",TRUE);
 $MyData->setSerieDrawable("Venkovní teplota",TRUE);
 $MyData->setSerieDrawable("Teplota TV dole",TRUE);
 $MyData->setSerieDrawable("Teplota Solaru",TRUE);
 $MyData->setSerieDrawable("Teplota Cirkulace",TRUE);
 $MyData->setSerieDrawable("Stav Cirkulace",FALSE);
 $MyData->setSerieDrawable("Stav Solaru",FALSE);
 $MyData->setSerieDrawable("Vykon kotle",FALSE);
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