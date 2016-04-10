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
    $rok = substr($mesic, 0, 4);

    db_connect();
?>

<h3>Statistika za měsíc: <?php echo $mesic ?></h3>
<table border="1" cellpadding="3"> 

<?php
     $pocet = 0;
     $zisk_soucet = 0;
     $SQLStatement = "SELECT max(S_Solar_zisk_den) as zisk, DATE_FORMAT(DateTime, '%e') as den FROM `temperatures` 
     WHERE '$mesic' = DATE_FORMAT(DateTime, '%Y-%m') group by DATE_FORMAT(DateTime, '%Y-%m-%d') ORDER BY `DateTime`";

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
echo("<td>");
echo("Průměr na den");
echo("</td>");
echo("<td>");
echo("Celkem za období");
echo("</td>");
echo("</tr>");  

echo("<tr>");
echo("<td>");
echo("Výkon solaru [kWh]");
echo("</td align='center'>");
mysql_data_seek($SQLResult, 0);
while ($row=mysql_fetch_array($SQLResult))
  {
   echo ("<td>");
   echo($row["zisk"]);
   echo ("</td>");
   $zisk_soucet = $zisk_soucet + $row["zisk"];
  }
echo("<td align='center'>");
echo(round($zisk_soucet/$pocet, 2));
echo("</td>");
echo("<td align='center'>");
echo($zisk_soucet);
echo("</td>");
echo("</tr>"); 
echo("</table>");

echo ("&nbsp&nbsp");
echo ("<br>");
// zobrazeni href jen v pripade ze nemam prvni mesic, tj. 2013-04
{
  $odkaz =  (date('Y-m',strtotime($mesic ."- 1 month")));
  if ($mesic <> '2014-04') {
  echo ("<a href = 'solar_stat.php?mesic=" .$odkaz. "'>");
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
  echo ("<a href = 'solar_stat.php?mesic=" .$odkaz. "'>");
  echo ("$odkaz >>");
  echo ("</a>");
  }
  else {
  echo ("$odkaz >>");
  }
?>



<h3>Statistika za rok: <?php echo $rok?> </h3>
<table border="1" cellpadding="3">
<?php
     $pocet = 0;
     $zisk_soucet = 0;
     
     $SQLStatement = "SELECT sum(zisk) as zisk, den FROM ( SELECT max(S_Solar_zisk_den) as zisk, DATE_FORMAT(DateTime, '%Y-%m') as den 
     FROM `temperatures` WHERE '$rok' = DATE_FORMAT(DateTime, '%Y') group by DATE_FORMAT(DateTime, '%Y-%m-%e') ORDER BY DateTime) as T WHERE den <> '2014-03' AND den <> '2014-02' AND den <> '2014-01' group by den";

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
echo(round(($zisk_soucet/$pocet),2));
echo("</td>");
echo("<td align='center'>");
echo($zisk_soucet);
echo("</td>");
echo("</tr>"); 
?>


</table>
<br>
<?php
  if ($rok == '2015')
{
?>

<a href = 'solar_stat.php?mesic=2014-12'><< 2014-12</a>

<?php
}
else {
?>
<a href = 'solar_stat.php?mesic=2015-01'>2015-01 >></a>

<?php
}
?>
</body>