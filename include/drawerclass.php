<?php
class ValueColor
	{
	var $colText;
	var $colTemp;
	var $colHum;
	var $colBack;
	var $colStatus;
	function ValueColor($t, $b, $s, $tt, $hh)
		{
		$this->colText=$t;
		$this->colBack=$b;
		$this->colStatus=$s;
		$this->colTemp=$tt;
		$this->colHum=$hh;
		}
	}

class DiagrammScriptCreator
	{
	var $conn;
	
	function DiagrammScriptCreator($c)
		{
		$this->conn=$c;
		}
		
	function getValueColor($sensor, $ovalue)
		{
		if ($ovalue->isValid())
			{
			$colorText='#222222';
			$colorTemp=$colorText;
			$colorHum=$colorText;
			$colorBack='#AAAAAA';
			$colorStatus='#0000AA';
			if ($ovalue->temp>=$sensor->tempGreenFrom && $ovalue->temp<=$sensor->tempGreenTo)
				{
				$colorText='#00AA00';
				$colorBack='#AAFFAA';
				$colorTemp=$colorText;
				}
			if ($ovalue->hum>=$sensor->humGreenFrom && $ovalue->hum<=$sensor->humGreenTo)
				{
				$colorText='#00AA00';
				$colorBack='#AAFFAA';
				$colorHum=$colorText;				
				}

			if ($ovalue->temp >= $sensor->tempYellowFrom && $ovalue->temp < $sensor->tempYellowTo)
				{
				$colorText='#D4AA00';
				$colorBack='#FFEEAA';
				$colorTemp=$colorText;
				}
			if ($ovalue->hum>=$sensor->humYellowFrom && $ovalue->hum<=$sensor->humYellowTo)
				{
				$colorText='#D4AA00';
				$colorBack='#FFEEAA';
				$colorHum=$colorText;				
				}
				
			if ($ovalue->temp > $sensor->tempRedFrom && $ovalue->temp < $sensor->tempRedTo)
				{
				$colorText='#AA0000';
				$colorBack='#FFAAAA';
				$colorTemp=$colorText;
				}
			if ($ovalue->hum>=$sensor->humRedFrom && $ovalue->hum<=$sensor->humRedTo)
				{
				$colorText='#AA0000';
				$colorBack='#FFAAAA';
				$colorHum=$colorText;				
				}
			
			}
		else
			{
			$colorText='#FF0000';
			$colorBack='#CE8888';
			$colorStatus='#FF0000';			
			$colorTemp=$colorText;
			$colorHum=$colorText;				

			}
			
			return new ValueColor($colorText, $colorBack, $colorStatus, $colorTemp, $colorHum);
		}
		
	function generateRandomString($length = 10) 
		{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
	    $randomString = '';
    	for ($i = 0; $i < $length; $i++) 
    		{
        	$randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
	    return $randomString;
		}
	
		function CreateXY($sensors, $duration)
			{
			$this->intCreateXY(1,$sensors,$duration);
			$this->intCreateXY(2,$sensors,$duration);
			}
		
		function intCreateXY($type, $sensors, $duration)
		{
		switch ($type)
			{
			case 1:
				$field="temperature";
				$title="Temperatur";				
				break;
			case 2:
				$field="humidity";
				$title="Luftfeuchtigkeit";
				break;
			default:
				return;
			}
		$div=$duration*100;#here is the resolution 60equals 3min
		$title="$title der letzten $duration Stunden";
		$divname=$this->generateRandomString();
		$db=$this->conn;

		$q="";
		$q=$q."SELECT date_time, ";
		for ($i=0;$i<count($sensors);$i++)
			{
				$p=$sensors[$i]->pin;
				$q=$q."(select round(avg(t$i.$field),1) from datalogger as t$i where t$i.sensor=$p and t$i.measureid=m.id group by t$i.measureid) as tt$i ";
			#$q=$q."round(avg(CASE WHEN h.sensor = ".$sensors[$i]->pin." THEN h.$field END),1) as t$i ";
			if ($i+1<count($sensors))
				$q=$q.", ";		
			}
	$q=$q."FROM measure m ";
	$q=$q. "WHERE m.active=1 and TIMESTAMPDIFF(HOUR,date_time,NOW())<$duration ";		
	#$q=$q."GROUP BY UNIX_TIMESTAMP(date_time) DIV $div ";
	$q=$q."order by m.date_time"; 

	$ds=mysqli_query($db, $q); 
	$rows=mysqli_num_rows($ds);
	#echo($q);
	if ($rows==0)
		return ;
		?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
  
        var data = google.visualization.arrayToDataTable([
          ['TIME',<?php
	for ($i=0;$i<count($sensors);$i++)
		echo("'".$sensors[$i]->name."',");

	?> ],<?php 
	$min=99;
	$max=0;
	while($r = mysqli_fetch_array($ds)) 
	{ 
		echo "['".$r[0]."', "; 
		for ($i=0;$i<count($sensors);$i++)
			{
			switch ($type)
			{
			case 1:
				$v=$sensors[$i]->tempDelta+$r[$i+1];			
				break;
			case 2:
				$v=$sensors[$i]->humDelta+$r[$i+1];
				break;
			}
			if ($v>$max)
				$max=$v;
			if ($v<$min)
				$min=$v;
			echo " $v,"; 
			}
		echo "],"; 
	} 
	
	$ticks="[";
	$div=5;
	$minorCount=4;
	if ($max-$min>40)
		{
		$minorCount=1;
		$div=10;
		}
		
	$min=floor($min/$div)*$div;
	$max=ceil($max/$div)*$div;

	while ($min<=$max)
		{
		$ticks=$ticks.$min;
		if ($min+$div<=$max)
			$ticks=$ticks.", ";
		$min=$min+$div;
		}
	$ticks=$ticks."]";
	
	?>]);

	var options = {
	colors: [<?php
	for ($i=0;$i<count($sensors);$i++)
		echo " '".$sensors[$i]->color."',"; 
	?>],
	title: '<?php echo($title) ?>',
	curveType: 'none',
	legend: { position: 'bottom' },
	vAxis: { ticks: <?php echo($ticks) ?>, minorGridlines: {count: <?php echo($minorCount)?>} },
	hAxis: { textPosition: 'none', direction: '1' },
        };

        var chart = new google.visualization.LineChart(document.getElementById('<?php echo($divname) ?>'));

        chart.draw(data, options);
options['pagingSymbols'] = {prev: 'prev', next: 'next'}; options['pagingButtonsConfiguration'] = 'auto';
      }
    </script>
<div id="<?php echo($divname); ?>" style="width: 850px; height: 400px;"></div>

<?php
		}
	
	
		
	
	function CreateGauge( $sensor)
		{
		$ovalue=$sensor->getValue($this->conn);
		$col=$this->GetValueColor($sensor, $ovalue);
		
		?>
		
		
		<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   version="1.1"
   id="svg6133"
   viewBox="0 0 200 150"
   height="90" width="120">
  <defs
     id="defs6135">
    <linearGradient
       id="humStatus<?php echo ($sensor->pin);?>">
      <stop
         style="stop-color:#ffffff;stop-opacity:1"
         id="stop12596"
         offset="0" />
      <stop
         id="stop12598"
         offset="1"
         style="stop-color:<?php echo($col->colHum); ?>;stop-opacity:1" />
    </linearGradient>
    <linearGradient
       id="sensorStatus<?php echo ($sensor->pin);?>">
      <stop
         style="stop-color:#ffffff;stop-opacity:1"
         offset="0"
         id="stop12586" />
      <stop
         style="stop-color:<?php echo($col->colStatus); ?>;stop-opacity:1"
         offset="1"
         id="stop12588" />
    </linearGradient>
    <linearGradient
       id="tempStatus<?php echo ($sensor->pin);?>">
      <stop
         style="stop-color:#ffffff;stop-opacity:1"
         id="stop12408"
         offset="0" />
      <stop
         id="stop12410"
         offset="1"
         style="stop-color:<?php echo($col->colTemp); ?>;stop-opacity:1" />
    </linearGradient>
    <linearGradient
       id="Background<?php echo ($sensor->pin);?>">
      <stop
         style="stop-color:#ffffff;stop-opacity:1"
         id="stop6687"
         offset="0" />
      <stop
         id="stop6689"
         offset="1"
         style="stop-color:<?php echo($col->colBack); ?>;stop-opacity:1" />
    </linearGradient>
    <linearGradient
       gradientUnits="userSpaceOnUse"
       y2="2.2690167"
       x2="118.64719"
       y1="148.74113"
       x1="119.73861"
       id="linearGradient6691<?php echo ($sensor->pin);?>"
       xlink:href="#Background<?php echo ($sensor->pin);?>" />
    <radialGradient
       gradientUnits="userSpaceOnUse"
       gradientTransform="matrix(0.47152805,-0.05516851,0.04656592,0.3980013,14.327575,75.666582)"
       r="22.708128"
       fy="-98.751106"
       fx="27.568445"
       cy="-98.751106"
       cx="27.568445"
       id="radialGradient12412<?php echo ($sensor->pin);?>"
       xlink:href="#tempStatus<?php echo ($sensor->pin);?>" />
    <radialGradient
       r="22.708128"
       fy="0.0021040719"
       fx="16.744888"
       cy="0.0021040719"
       cx="16.744888"
       gradientTransform="matrix(0.47152805,-0.05516851,0.04656592,0.3980013,14.327575,75.666582)"
       gradientUnits="userSpaceOnUse"
       id="radialGradient12576<?php echo ($sensor->pin);?>"
       xlink:href="#humStatus<?php echo ($sensor->pin);?>" />
    <radialGradient
       r="22.708128"
       fy="126.58994"
       fx="5.3148093"
       cy="126.58994"
       cx="5.3148093"
       gradientTransform="matrix(0.47152805,-0.05516851,0.04656592,0.3980013,14.327575,75.666582)"
       gradientUnits="userSpaceOnUse"
       id="radialGradient12592<?php echo ($sensor->pin);?>"
       xlink:href="#sensorStatus<?php echo ($sensor->pin);?>" />
  </defs>
  <g
     id="layer1">
    <rect
       y="0"
       x="0"
	   rx="20"
	   ry="20"
       height="150"
       width="200"
       id="rect6143"
       style="fill:url(#linearGradient6691<?php echo ($sensor->pin);?>);fill-opacity:1;stroke:#000000;stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1;stroke-linejoin:round;stroke-linecap:butt" />
    <text
       id="text6693"
       y="44.944134"
       x="44.365494"
       style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:35px;line-height:125%;font-family:'Gill Sans';-inkscape-font-specification:'Gill Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;"
       xml:space="preserve"><tspan
         y="54.944134"
         x="44.365494"
         id="tspan6695"><tspan
   id="tspan12568"
   style="-inkscape-font-specification:'Gill Sans, Normal';font-family:'Gill Sans';font-weight:normal;font-style:normal;font-stretch:normal;font-variant:normal;font-size:35px;text-anchor:start;text-align:start;writing-mode:lr;line-height:125%;"><?php echo(number_format($ovalue->temp,1)); ?></tspan> &#8451;</tspan></text>
    <text
       xml:space="preserve"
       style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:35px;line-height:125%;font-family:'Gill Sans';-inkscape-font-specification:'Gill Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;"
       x="44.365494"
       y="86.791794"
       id="text6697"><tspan
         id="tspan6699"
         x="44.365494"
         y="96.791794"><?php echo(number_format($ovalue->hum,1)); ?> %</tspan></text>
    <ellipse
       ry="12.626907"
       rx="13.307865"
       cy="44.090084"
       cx="22.322277"
       id="path12404"
       style="fill:url(#radialGradient12412<?php echo ($sensor->pin);?>);fill-opacity:1;stroke:#787878;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" />
    <ellipse
       style="fill:url(#radialGradient12576<?php echo ($sensor->pin);?>);fill-opacity:1;stroke:#787878;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
       id="ellipse12574"
       cx="22.322277"
       cy="84.090088"
       rx="13.307865"
       ry="12.626907" />
    <ellipse
       ry="12.626907"
       rx="13.307865"
       cy="125.10279"
       cx="22.322277"
       id="ellipse12590"
       style="fill:url(#radialGradient12592<?php echo ($sensor->pin);?>);fill-opacity:1;stroke:#787878;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" />
    <text
       id="text12608"
       y="10"
       x="44.365494"
       style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:22.5px;line-height:125%;font-family:'Gill Sans';-inkscape-font-specification:'Gill Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr-tb;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       xml:space="preserve"><tspan
         y="20"
         x="60"
         id="tspan12604"><?php echo($sensor->name); ?></tspan></text>
	<text
       id="text12602"
       y="133.27911"
       x="44.365494"
       style="font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:22.5px;line-height:125%;font-family:'Gill Sans';-inkscape-font-specification:'Gill Sans, Normal';text-align:start;letter-spacing:0px;word-spacing:0px;writing-mode:lr-tb;text-anchor:start;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
       xml:space="preserve"><tspan
         y="133.27911"
         x="44.365494"
         id="tspan12604"><?php echo($ovalue->age); ?> Minuten</tspan></text>
    <?php if ($ovalue->tempTrend>0 || $ovalue->tempTrend==3) {?>
    <path
       d="m 194.91884,42.923828 -12.53204,0.164311 -12.53205,0.16431 6.12373,-10.935223 6.12372,-10.935222 6.40832,10.770912 z"
       id="tempUp"
       style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" />
    <?php } ?>
    <?php if ($ovalue->humTrend>0 || $ovalue->humTrend==3) {?>
    <path
       style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
       id="humUp"
       d="m 194.91884,86.923828 -12.53204,0.164311 -12.53205,0.16431 6.12373,-10.935223 6.12372,-10.935222 6.40832,10.770912 z" />
    <?php } ?>
    <?php if ($ovalue->tempTrend<0 || $ovalue->tempTrend==3) {?>
    <path
       transform="scale(-1,-1)"
       style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
       id="tempDown"
       d="m -169.66502,-22.781692 -12.53205,0.16431 -12.53204,0.16431 6.12373,-10.935222 6.12372,-10.935223 6.40832,10.770913 z" />
    <?php } ?>
    <?php if ($ovalue->humTrend<0 || $ovalue->tempTrend==3) {?>
    <path
       transform="scale(-1,-1)"
       d="m -169.66502,-66.781692 -12.53205,0.16431 -12.53204,0.16431 6.12373,-10.935222 6.12372,-10.935223 6.40832,10.770913 z"
       id="humDown"
       style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:4;stroke-linecap:butt;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" />
    <?php } ?>
  </g>
  <metadata
     id="metadata15264">
    <rdf:RDF>
      <cc:Work
         rdf:about="">
        <dc:title></dc:title>
      </cc:Work>
    </rdf:RDF>
  </metadata>
</svg>
		
		
		<?php
		
		}
		
		
	}
	

?>
