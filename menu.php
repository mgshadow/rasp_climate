<?php
echo "V1.0.1 last build date: ".date ("Y-m-d H:i:s", filemtime('index.php'))."<br>";

echo '
<a href="index.php" tooltip title="HOME" alt="HOME">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-home fa-stack-1x fa-inverse"></i>
</span>
</a>
<a href="outside.php" title="Außen" alt="Außen">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">out</strong>
</span>
</a>
<a href="0.php" title="Raum" alt="Raum">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">in</strong>
</span>
</a>
<a href="1.php" title="großes Zelt" alt="großes Zelt">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">gr</strong>
</span>
</a>
<a href="2.php" title="kleines Zelt" alt="kleines Zelt">
<span class="fa-stack fa-3x">
  <i class="fa fa-circle fa-stack-2x"></i>
  <strong class="fa-stack-1x fa-stack-text fa-inverse">kl</strong>
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

