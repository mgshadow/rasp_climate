<?php
echo "V1.0.5 last build date: ".date ("Y-m-d H:i:s", filemtime('menu.php'))."<br>";

echo '
<a href="index.php" tooltip title="HOME" alt="HOME">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-home fa-stack-1x fa-inverse"></i>
</span>
</a>
<a href="0.php" title="Raum" alt="Raum">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">Rm</strong>
</span>
</a>
<a href="1.php" title="Zelte" alt="Zelte">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">Ze</strong>
</span>
</a>
<a href="javascript:history.go(0);" title="RELOAD" alt="RELOAD">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-refresh fa-stack-1x fa-inverse"></i>
</span>
</a>
<a href="buttons.php" title="MANUAL CONTROLS" alt="MAN CTRLS">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-th fa-stack-1x fa-inverse"></i>
</span>
</a>
<a href="errorlog.php" title="Errorlog" alt="Errorlog">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-exclamation-triangle fa-stack-1x fa-inverse"></i>
</span>
</a>
';

?>

