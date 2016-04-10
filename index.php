<html>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="datetimepicker.js"></script> 
<link rel="stylesheet" href="style.css" />

<?php 

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

    $mydat1 = isset($_GET["rest_start"]) ? $_GET["rest_start"] : date('Y-m-d 06:00', time());
    $mydat2 = isset($_GET["rest_end"]) ? $_GET["rest_end"] : date('Y-m-d H:i', time());
    $scale = isset($_GET["scale"]) ? $_GET["scale"] : "30";
    $sens = isset($_GET["sens"]) ? $_GET["sens"] : "1";
    $TV = isset($_GET["TV"]) ? $_GET["TV"] : "false";
    $TIn = isset($_GET["TIn"]) ? $_GET["TIn"] : "false";
    $TOut = isset($_GET["TOut"]) ? $_GET["TOut"] : "false";
    $TV2 = isset($_GET["TV2"]) ? $_GET["TV2"] : "false";
    $Solar = isset($_GET["Solar"]) ? $_GET["Solar"] : "false";
    $Cirkul = isset($_GET["Cirkul"]) ? $_GET["Cirkul"] : "false";
    
    if  (($TV == "false")  AND ($TIn == "false") AND ($TOut == "false") AND ($TV2 == "false") AND ($Solar == "false") AND ($Cirkul == "false"))
    {
     $TV = "true"; $TOut = "true"; $TIn = "true"; $TV2 = "true"; $Solar = "true"; $Cirkul = "true";
    }
    
   // ChromePhp::log($mydat1);
    //ChromePhp::log($mydate2); 
    //ChromePhp::log($scale);
    
    $mydate1 = urlencode($mydat1);
    $mydate2 = urlencode($mydat2);
    
    if ($scale == 5) {
      $imgurl = "graph.php?date1=$mydate1&date2=$mydate2&scale=$scale&sens=$sens&TV=$TV&TIn=$TIn&TOut=$TOut&TV2=$TV2&Solar=$Solar&Cirkul=$Cirkul";
    }
    else {
      $imgurl = "graph3.php?date1=$mydate1&date2=$mydate2&scale=$scale&sens=$sens&TV=$TV&TIn=$TIn&TOut=$TOut&TV2=$TV2&Solar=$Solar&Cirkul=$Cirkul";
    }        
    ChromePhp::log($imgurl);
                              
?>

<script>
  $(function() {
   var startDateTextBox = $('#rest_start');
   var endDateTextBox = $('#rest_end');

    startDateTextBox.datetimepicker({ 
    	timeFormat: 'HH:mm',
      dateFormat: "yy-mm-dd",
      maxDate : (new Date()),
      stepMinute : 5,
      minDate : new Date(2013, 06, 01, 00, 00),
    	onClose: function(dateText, inst) {
    		if (endDateTextBox.val() != '') {
    			var testStartDate = startDateTextBox.datetimepicker('getDate');
    			var testEndDate = endDateTextBox.datetimepicker('getDate');
    			if (testStartDate > testEndDate)
    				endDateTextBox.datetimepicker('setDate', testStartDate);
    		}
    		else {
    			endDateTextBox.val(dateText);
    		}
    	},
    	onSelect: function (selectedDateTime){
    		endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
    	}
    });
    
      endDateTextBox.datetimepicker({ 
      	timeFormat: 'HH:mm',
        dateFormat: "yy-mm-dd",
        stepMinute : 5,
        maxDate : (new Date()),
      	onClose: function(dateText, inst) {
      		if (startDateTextBox.val() != '') {
      			var testStartDate = startDateTextBox.datetimepicker('getDate');
      			var testEndDate = endDateTextBox.datetimepicker('getDate');
      			if (testStartDate > testEndDate)
      				startDateTextBox.datetimepicker('setDate', testEndDate);
      		}
      		else {
      			startDateTextBox.val(dateText);
      		}
      	},
      	onSelect: function (selectedDateTime){
      		startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
      	}
      });
});


</script>

</head>

<body>

<form name="formik" action="index.php" method="GET">

Od: <input type="text" id="rest_start" name="rest_start" />
Do:   <input type="text" id="rest_end" name="rest_end" />

<script>
document.getElementById("rest_start").value = '<?php echo("$mydat1") ?>';
document.getElementById("rest_end").value = '<?php echo("$mydat2") ?>';
</script>

Rozlišení časové osy: 
<select id="scale" name="scale">
  <option value ="5"   <?php if ($scale == "5") echo("selected") ?> >5</option>
  <option value ="10"  <?php if ($scale == "10") echo("selected") ?> >10</option>
  <option value ="15"  <?php if ($scale == "15") echo("selected") ?> >15</option>
  <option value ="30"  <?php if ($scale == "30") echo("selected") ?> >30</option>
  <option value ="60"  <?php if ($scale == "60") echo("selected") ?> >60</option>
  <option value ="120" <?php if ($scale == "120") echo("selected") ?> >120</option>
  <option value ="180" <?php if ($scale == "180") echo("selected") ?> >180</option>
</select>

&nbsp&nbsp
<input type="submit" name="Submit" value="Submit">
<br>
Teplá voda:<input type="checkbox" name="TV" value="true" <?php if ($TV == "true") echo("checked") ?> > &nbsp&nbsp
Teplota prostoru:<input type="checkbox" name="TIn" value="true" <?php if ($TIn == "true") echo("checked") ?> > &nbsp&nbsp
Venkovní teplora:<input type="checkbox" name="TOut" value="true" <?php if ($TOut == "true") echo("checked") ?> > &nbsp&nbsp
Teplá voda dole:<input type="checkbox" name="TV2" value="true" <?php if ($TV2 == "true") echo("checked") ?> > &nbsp&nbsp
Teplota Soláru:<input type="checkbox" name="Solar" value="true" <?php if ($Solar == "true") echo("checked") ?> > &nbsp&nbsp
Teplota Cirkulace:<input type="checkbox" name="Cirkul" value="true" <?php if ($Cirkul == "true") echo("checked") ?> > 

<!--Citlivost na zmenu:
<select id="sens" name="sens">
  <option value ="0,1">0,1</option>
  <option value ="0,2">0,2</option>
  <option value ="0,5">0,5</option>
  <option value ="1" selected>1</option>
  <option value ="1,5">1,5</option>
  <option value ="2">2</option>
  <option value ="2,5">2,5</option>
  <option value ="5">5</option>
</select-->
</form>

<?php
 db_connect();

  $SQLScale = ""; //uvazuji presnost na 5 minut, takze vracim vsechny vysledky

?>

<table border="0" cellpadding="10">
<tr><td align="left">
<ul>
<li>Kotel topil pro ohrev TO:  <?php 
 //$SQLWhat = " AND R_Kotel_sepnuti > R_Kotel AND S_Kotel <> 'Vyp' AND S_QX3 = 'Vyp'";
 
 $SQLWhat = " AND R_Kotel_sepnuti > T_Kotel AND S_Kotel <> 'Vyp' AND S_QX3 = 'Vyp'";
  
  $SQLStatement = "SELECT count(*) as pocet,  SUM(Vykon)/count(*) as vykon from temperatures t WHERE DateTime BETWEEN '$mydat1' AND '$mydat2:59'" . $SQLScale . $SQLWhat;   

  ChromePhp::log($SQLStatement);

  $SQLResult = mysql_query($SQLStatement);
  $row=mysql_fetch_array($SQLResult);
  $Result=intval($row["pocet"])*5;
  $Hodin = floor($Result/60);
  $Minut = $Result%60;
  //$row=mysql_fetch_array($SQLResult);
  $Vykon=intval($row["vykon"]);

  echo ($Hodin . " hodin a " . $Minut . " minut. Průměrný výkon hořáku kotle : " . $Vykon . "%" ); 
?></li>

<li>Kotel topil pro TUV: <?php 
 $SQLWhat = " AND S_QX3 = 'Zap'";
 
  $SQLStatement = "SELECT count(*) as pocet, SUM(Vykon)/count(*) as vykon from temperatures t WHERE DateTime BETWEEN '$mydat1' AND '$mydat2:59'" . $SQLScale . $SQLWhat;   

  ChromePhp::log($SQLStatement);

  $SQLResult = mysql_query($SQLStatement);
  $row=mysql_fetch_array($SQLResult);
  $Result=intval($row["pocet"])*5;
  $Hodin = floor($Result/60);
  $Minut = $Result%60;
  $Vykon=intval($row["vykon"]);

  echo ($Hodin . " hodin a " . $Minut . " minut. Průměrný výkon hořáku kotle : " . $Vykon . "%" ); 
?>
</li>
                                
<li>Solarni čerpadlo běželo: <?php 
 $SQLWhat = " AND S_Solar_Cerp = 'Zap'";
 
  $SQLStatement = "SELECT count(*) as pocet from temperatures t WHERE DateTime BETWEEN '$mydat1' AND '$mydat2:59'" . $SQLScale . $SQLWhat;   

  ChromePhp::log($SQLStatement);

  $SQLResult = mysql_query($SQLStatement);
  $row=mysql_fetch_array($SQLResult);
  $Result=intval($row["pocet"])*5;
  $Hodin = floor($Result/60);
  $Minut = $Result%60;
  
  if ((substr($mydat1, 5,5)) == (substr($mydat2, 5,5))) {
    $SQLStatement = "SELECT min(S_Solar_zisk_den) as zisk1, max(S_Solar_zisk_den) as zisk2  from temperatures t WHERE DateTime BETWEEN
      '$mydat1' AND '$mydat2:59'";
    ChromePhp::log($SQLStatement);
    $SQLResult = mysql_query($SQLStatement);
    $row=mysql_fetch_array($SQLResult);
    $Zisk1 = floatval($row["zisk1"]);
    $Zisk2 = floatval($row["zisk2"]);
    ChromePhp::log($Zisk1);
    ChromePhp::log($Zisk2);
    $Zisk = $Zisk2 - $Zisk1;
    
    $SQLStatement = "SELECT max(S_Solar_zisk_den) as zisk2 from temperatures t WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = '" . substr($mydat2, 0,10) . "'";
    ChromePhp::log($SQLStatement);
    $SQLResult = mysql_query($SQLStatement);
    $row=mysql_fetch_array($SQLResult);
    $ZiskCelek = floatval($row["zisk2"]);
        
    
    echo ($Hodin . " hodin a " . $Minut . " minut. Zisk soláru " . $Zisk . " kWh. Celkový denní zisk " . $ZiskCelek . " kWh");
  }
  else {
     $SQLStatement = "SELECT min(S_Solar_zisk_celkem) as zisk1, max(S_Solar_zisk_celkem) as zisk2  from temperatures t WHERE DateTime BETWEEN
      '$mydat1' AND '$mydat2:59'";
      ChromePhp::log($SQLStatement);
    $SQLResult = mysql_query($SQLStatement);
    $row=mysql_fetch_array($SQLResult);
    $Zisk1 = floatval($row["zisk1"]);
    $Zisk2 = floatval($row["zisk2"]);
    ChromePhp::log($Zisk1);
    ChromePhp::log($Zisk2);
    $Zisk = $Zisk2 - $Zisk1;
    echo ($Hodin . " hodin a " . $Minut . " minut. Zisk soláru " . $Zisk . " kWh");
  }

   
?>
</li>

<li>Cirkulace běžela: <?php 
 $SQLWhat = " AND S_Cirku = 'Zap'";
 
  $SQLStatement = "SELECT count(*) as pocet from temperatures t WHERE DateTime BETWEEN '$mydat1' AND '$mydat2'" . $SQLScale . $SQLWhat;   

  //ChromePhp::log($SQLStatement);

  $SQLResult = mysql_query($SQLStatement);
  $row=mysql_fetch_array($SQLResult);
  $Result=intval($row["pocet"])*5;
  $Hodin = floor($Result/60);
  $Minut = $Result%60;

  echo ($Hodin . " hodin a " . $Minut . " minut"); 
?>
</li>
</ul></td>
<td width="200" align="right" valign="top">
<a href="solar_stat.php">Statistiky soláru</a>
</td>
</table> 

<hr noshade> 
<br>
<img src=<?php echo ($imgurl); ?> id="testPicture" alt="" class="pChartPicture"/>

</body>