<html>
<head>
<link rel="stylesheet" href="style.css" />
</head>

<body>
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

    $mesic = isset($_GET["mesic"]) ? $_GET["mesic"] : date('Y-m', time());

    db_connect();
?>

<h3>Statistika za měsíc pro TO:</h3>
<table border="1" cellpadding="3"> 

<?php
     $pocet = 0;
     $zisk_soucet = 0;
     $cas = 0;
     $SQLStatement = "SELECT count(*) as pocet, sum(Vykon)/count(*) as vykon, DATE_FORMAT(DateTime, '%e') as den FROM `temperatures` 
     WHERE '$mesic' = DATE_FORMAT(DateTime, '%Y-%m') AND R_Kotel_sepnuti > T_Kotel AND S_Kotel <> 'Vyp' AND S_QX3 = 'Vyp' 
     group by DATE_FORMAT(DateTime, '%Y-%m-%d') ORDER BY `DateTime";

//echo ($SQLStatement);
ChromePhp::log($SQLStatement);
$SQLResult = mysql_query($SQLStatement);

echo("<tr>");
echo("<td>");
echo("Den v měsící");
echo("</td>");

while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   echo($row["den"]);
   echo (".</td>");
   $pocet = $pocet + 1;
  }
//echo("<td>");
//echo("Průměr na den");
//echo("</td>");
//echo("<td>");
//echo("Celkem za období");
//echo("</td>");
echo("</tr>");  

echo("<tr>");
echo("<td>");
echo("Průměrný výkon kotle [%]");
echo("</td align='center'>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   echo round(($row["vykon"]),2);
   echo ("</td>");
   $zisk_soucet = $zisk_soucet + $row["vykon"];
  }
//echo("<td align='center'>");
//echo(round($zisk_soucet/$pocet, 2));
//echo("</td>");
//echo("<td align='center'>");
//echo($zisk_soucet);
//echo("</td>");
echo("</tr>");
echo("<tr>");
echo("<td>");
echo("Průměrný výkon kotle přepočtený na [kWh]");
echo("</td align='center'>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   echo round(((($row["vykon"])*0.15)+2),2);
   echo ("</td>");
   //$zisk_soucet = $zisk_soucet + $row["vykon"];
  }
//echo("<td align='center'>");
//echo(round($zisk_soucet/$pocet, 2));
//echo("</td>");
//echo("<td align='center'>");
//echo($zisk_soucet);
//echo("</td>");
echo("</tr>");
echo("<tr>");
echo("<td>");
echo("Doba chodu kotle [h:min]");
echo("</td align='center'>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   $Result=intval($row["pocet"])*5;
   $Hodin = floor($Result/60);
   $Minut = $Result%60;
   echo($Hodin.":".$Minut);
   echo ("</td>");
   $zisk_soucet = $zisk_soucet + $row["zisk"];
  }
//echo("<td align='center'>");
//echo(round($zisk_soucet/$pocet, 2));
//echo("</td>");
//echo("<td align='center'>");
//echo($zisk_soucet);
//echo("</td>");
echo("</tr>"); 
echo("<tr>");
echo("<td>");
echo("Celkový dodaný výkon za den dle prumerne </br> hodnoty vykonu [kWh]");
echo("</td align='center'>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td>");
   $Result=intval($row["pocet"])*5;
   //$Hodin = floor($Result/60);
   //$Minut = $Result%60;
   $vykon=round(((($row["vykon"])*0.15)+2),2);
   echo round(($Result*$vykon/60),2);
   echo ("</td>");
   //$zisk_soucet = $zisk_soucet + $row["zisk"];
  }
//echo("<td align='center'>");
//echo(round($zisk_soucet/$pocet, 2));
//echo("</td>");
//echo("<td align='center'>");
//echo($zisk_soucet);
//echo("</td>");
echo("</tr>"); 
echo("</table>");

echo ("&nbsp&nbsp");
//echo ("<br>");
// zobrazeni href jen v pripade ze nemam prvni mesic, tj. 2013-04
{
  $odkaz =  (date('Y-m',strtotime($mesic ."- 1 month")));
  if ($mesic <> '2014-02') {
  echo ("<a href = 'kotel_stat.php?mesic=" .$odkaz. "'>");
  echo ("<< $odkaz");
  echo ("</a>");
  } 
  else {
  echo ("<< $odkaz");
  }  
}
echo ("&nbsp&nbsp");
$odkaz =  (date('Y-m',strtotime($mesic ."+ 1 month")));
if ($mesic <> date('Y-m', time())) {
  echo ("<a href = 'kotel_stat.php?mesic=" .$odkaz. "'>");
  echo ("$odkaz >>");
  echo ("</a>");
  }
  else {
  echo ("$odkaz >>");
  }
?>



<h3>Statistika za rok:</h3>
NENI DOPSANE, TYTO STATISTIKY JSOU PRO SOLAR
<table border="1" cellpadding="3">
<?php
     $pocet = 0;
     $zisk_soucet = 0;
     $SQLStatement = "SELECT sum(zisk) as zisk, den FROM ( SELECT max(S_Solar_zisk_den) as zisk, DATE_FORMAT(DateTime, '%Y-%m') as den FROM `temperatures` WHERE DATE_FORMAT(CURDATE(), '%Y') = DATE_FORMAT(DateTime, '%Y') group by DATE_FORMAT(DateTime, '%Y-%m-%e') ORDER BY DateTime) as T WHERE den <> '2014-03' AND den <> '2014-02' AND den <> '2014-01' group by den";

ChromePhp::log($SQLStatement);
$SQLResult = mysql_query($SQLStatement);

echo("<tr>");
echo("<td>");
echo("Měsíc");
echo("</td>");

while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   echo($row["den"]);
   echo ("</td>");
   $pocet = $pocet + 1;
  }
echo("<td>");
echo("Průměr na měsíc");
echo("</td>");
echo("<td>");
echo("Celkem za období");
echo("</td>");
echo("</tr>");  

echo("<tr>");
echo("<td>");
echo("Výkon solaru [kWh]");
echo("</td>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td align='center'>");
   echo($row["zisk"]);
   echo ("</td>");
   $zisk_soucet = $zisk_soucet + $row["zisk"];
  }
echo("<td align='center'>");
echo($zisk_soucet/$pocet);
echo("</td>");
echo("<td align='center'>");
echo($zisk_soucet);
echo("</td>");
echo("</tr>"); 
?>
</table>
</body>