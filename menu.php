<?php
echo "V3.0.0 last build date: ".date ("Y-m-d H:i:s", filemtime('menu.php'))."<br>";

echo '
<a href="index.php" tooltip title="HOME" alt="HOME">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-home fa-stack-1x fa-inverse"></i>
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

