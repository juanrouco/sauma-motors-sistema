<?php
//We've included ../Includes/FusionCharts_Gen.php, which contains FusionCharts PHP Class
//to help us easily embed the charts.
include("Code/PHPClass/Includes/FusionCharts_Gen.php");
?>
<HTML>
<HEAD>
  <TITLE>
    FusionCharts Free - Simple Column 3D Chart 
  </TITLE>

  <?php
  //You need to include the following JS file, if you intend to embed the chart using JavaScript.
  //Embedding using JavaScripts avoids the "Click to Activate..." issue in Internet Explorer
  //When you make your own charts, make sure that the path to this JS file is correct. 
  //Else, you would get JavaScript errors.
  ?> 
  <SCRIPT LANGUAGE="Javascript" SRC="Code/FusionCharts/FusionCharts.js"></SCRIPT>
</HEAD> 

<BODY>

  <?php
  //This page demonstrates the ease of generating charts using FusionCharts PHP Class.
  //For this chart, we've created an instance of FusionCharts PHP Class,
  //fed chart data and configuration parameters to it and rendered chart using the instance.

  //Here, we've kept this example very simple.

  # Create object for Column 3D chart
  $FC = new FusionCharts("Column3D","600","300"); 

  # Setting Relative Path of chart swf file.
  $FC->setSwfPath("Code/FusionCharts/");

  # Store chart attributes in a variable
  $strParam="caption=El nombre de la edicion;xAxisName=;yAxisName=Cantidad;decimalPrecision=0; formatNumberScale=0";

  # Set chart attributes
  $FC->setChartParams($strParam);

  # Add chart data along with category names 
  $FC->addChartData("462","name=Jan");
  $FC->addChartData("857","name=Feb");
  $FC->addChartData("671","name=Mar");
  $FC->addChartData("494","name=Apr");
  $FC->addChartData("761","name=May");
  $FC->addChartData("960","name=Jun");
  $FC->addChartData("629","name=Jul");
  $FC->addChartData("622","name=Aug");
  $FC->addChartData("376","name=Sep");
  $FC->addChartData("494","name=Oct");
  $FC->addChartData("761","name=Nov");
  $FC->addChartData("960","name=Dec");


  # Render chart 
  $FC->renderChart();

  ?>

</BODY>
</HTML>
