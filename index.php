<HTML>
<?PHP
include("./include/membersite_config.php");

include("./include/sensorclass.php");
include("./include/drawerclass.php");

$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 
$creator=new DiagrammScriptCreator($db);
$inSensor=SensorFactory::getInsideSensor();
$inValue=$inSensor->getValue($db);
$inColor=$creator->getValueColor($inSensor, $inValue);
$outSensor=SensorFactory::getOutsideSensor();
$outValue=$outSensor->getValue($db);
$outColor=$creator->getValueColor($outSensor, $outValue);
$box1Sensor=SensorFactory::getBox1Sensor();
$box1Value=$box1Sensor->getValue($db);
$box1Color=$creator->getValueColor($box1Sensor, $box1Value);
$box2Sensor=SensorFactory::getBox2Sensor();
$box2Value=$box2Sensor->getValue($db);
$box2Color=$creator->getValueColor($box2Sensor, $box2Value);
$errorcount=0;
$errorcount+=$inSensor->getErrorCount($db);
$errorcount+=$outSensor->getErrorCount($db);
$errorcount+=$box1Sensor->getErrorCount($db);
$errorcount+=$box2Sensor->getErrorCount($db);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<title>Klima</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
   

</head>
<body>
<div class="jumbotron">
<div class="container">
<?php include 'menu.php';?>
</div>
</div>
<div class="container">
<?php

echo ("Messung: <b>".date("Y-m-d H:i")."</b>");

if ($errorcount>0)
	echo ("<h3>Im Fehlerprotokoll stehen für diese Sensoren $errorcount Fehler an</h3>");

?>

<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   version="1.1"
   id="Layer_1"
   x="0px"
   y="0px"
   viewBox="0 0 600 300"
   xml:space="preserve"
   inkscape:version="0.91 r13725"
   sodipodi:docname="schema.svg"
   width="700"
   height="300"><metadata
     id="metadata3487"><rdf:RDF><cc:Work
         rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type
           rdf:resource="http://purl.org/dc/dcmitype/StillImage" /><dc:title></dc:title></cc:Work></rdf:RDF></metadata><defs
     id="defs3485"><linearGradient
       inkscape:collect="always"
       id="linearGradient15350"><stop
         style="stop-color:#0000ff;stop-opacity:1;"
         offset="0"
         id="stop15352" /><stop
         style="stop-color:#0000ff;stop-opacity:0;"
         offset="1"
         id="stop15354" /></linearGradient><linearGradient
       inkscape:collect="always"
       id="linearGradient15342"><stop
         style="stop-color:#aa4400;stop-opacity:1"
         offset="0"
         id="stop15344" /><stop
         style="stop-color:#00ff00;stop-opacity:1"
         offset="1"
         id="stop15346" /></linearGradient><marker
       inkscape:stockid="Arrow2Lend"
       orient="auto"
       refY="0"
       refX="0"
       id="Arrow2Lend"
       style="overflow:visible"
       inkscape:isstock="true"><path
         id="path4473"
         style="fill:#787878;fill-opacity:1;fill-rule:evenodd;stroke:#000000;stroke-width:0.625;stroke-linejoin:round;stroke-opacity:1"
         d="M 8.7185878,4.0337352 -2.2072895,0.01601326 8.7185884,-4.0017078 c -1.7454984,2.3720609 -1.7354408,5.6174519 -6e-7,8.035443 z"
         transform="matrix(-1.1,0,0,-1.1,-1.1,0)"
         inkscape:connector-curvature="0" /></marker><marker
       inkscape:stockid="Arrow1Lend"
       orient="auto"
       refY="0"
       refX="0"
       id="Arrow1Lend"
       style="overflow:visible"
       inkscape:isstock="true"><path
         id="path4455"
         d="M 0,0 5,-5 -12.5,0 5,5 0,0 Z"
         style="fill:#787878;fill-opacity:1;fill-rule:evenodd;stroke:#000000;stroke-width:1pt;stroke-opacity:1"
         transform="matrix(-0.8,0,0,-0.8,-10,0)"
         inkscape:connector-curvature="0" /></marker><linearGradient
       inkscape:collect="always"
       xlink:href="#linearGradient15342"
       id="linearGradient15348"
       x1="320"
       y1="299.5"
       x2="320"
       y2="283.5"
       gradientUnits="userSpaceOnUse"
       gradientTransform="translate(0,-7.1e-6)" /><linearGradient
       inkscape:collect="always"
       xlink:href="#linearGradient15350"
       id="linearGradient15356"
       x1="500"
       y1="302"
       x2="476"
       y2="-8"
       gradientUnits="userSpaceOnUse" /></defs><sodipodi:namedview
     pagecolor="#ffffff"
     bordercolor="#666666"
     borderopacity="1"
     objecttolerance="10"
     gridtolerance="10"
     guidetolerance="10"
     inkscape:pageopacity="0"
     inkscape:pageshadow="2"
     inkscape:window-width="1270"
     inkscape:window-height="726"
     id="namedview3483"
     showgrid="false"
     inkscape:zoom="1"
     inkscape:cx="316.5942"
     inkscape:cy="201.31281"
     inkscape:window-x="0"
     inkscape:window-y="0"
     inkscape:window-maximized="0"
     inkscape:current-layer="Layer_1" /><rect
     style="fill:url(#linearGradient15356);fill-opacity:1;stroke:none;stroke-width:10;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
     id="rect4836"
     width="600"
     height="300"
     x="0"
     y="0" /><path
     style="fill:#eaeaea;fill-opacity:1;fill-rule:evenodd;stroke:#000000;stroke-width:5;stroke-linecap:butt;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
     d="m 175.41677,273.60247 410.14191,0 0,-192.186632 -198.57014,-64.607425 -213.9357,67.06087 z"
     id="path4378"
     inkscape:connector-curvature="0"
     sodipodi:nodetypes="cccccc" /><g
     id="g3453"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3455"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3457"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3459"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3461"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3463"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3465"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3467"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3469"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3471"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3473"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3475"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3477"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3479"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><g
     id="g3481"
     transform="matrix(1.2142719,0,0,1.071609,1.5676226,-268.32986)" /><rect
     style="fill:url(#linearGradient15348);fill-opacity:1;stroke:none;stroke-width:7;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
     id="rect15340"
     width="603"
     height="25"
     x="0"
     y="275" /><rect
     style="fill:#afafaf;fill-opacity:1;stroke:#000000;stroke-width:3.00000024;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
     id="rect15471"
     width="129"
     height="123.00001"
     x="190"
     y="142" /><text
     xml:space="preserve"
     style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:20px;line-height:100%;font-family:Sans;-inkscape-font-specification:'Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr-tb;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
     x="197"
     y="163"
     id="text15473"
     sodipodi:linespacing="100%"><tspan
       sodipodi:role="line"
       id="tspan15475"
       x="197"
       y="163">großes Zelt</tspan></text>
<rect
     y="142"
     x="442"
     height="123.00001"
     width="129"
     id="rect15477"
     style="fill:#afafaf;fill-opacity:1;stroke:#000000;stroke-width:3.00000024;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /><text
     sodipodi:linespacing="100%"
     id="text15481"
     y="163"
     x="449"
     style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:20px;line-height:100%;font-family:Sans;-inkscape-font-specification:'Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr-tb;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
     xml:space="preserve"><tspan
       y="163"
       x="449"
       id="tspan15483"
       sodipodi:role="line">kleines Zelt</tspan></text>
       <svg x="10" y="10">
       <?php $creator->CreateGauge($outSensor); ?>
       </svg>
       <svg x="325" y="45">
       <?php $creator->CreateGauge($inSensor); ?>
       </svg>
       <svg x="194" y="170">
       <?php $creator->CreateGauge($box1Sensor); ?>
       </svg>
       <svg x="446" y="170">
       <?php $creator->CreateGauge($box2Sensor); ?>
       </svg>
</svg>

<div class="container">
	<?php  $creator->CreateXY(array($outSensor,$inSensor, $box1Sensor,$box2Sensor), 3); ?>
</div>
<div class="container">
	<?php  $creator->CreateXY(array($outSensor,$inSensor, $box1Sensor,$box2Sensor), 24); ?>
</div>
<div class="container">
	<?php  $creator->CreateXY(array($outSensor,$inSensor, $box1Sensor,$box2Sensor), 24*4); ?>
</div>

<div class="container"><hr>
<?php include 'footer.php';?></div>
</BODY> 
</HTML>

