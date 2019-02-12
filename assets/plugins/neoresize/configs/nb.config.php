<?php
$dr_global["path"] = "assets/images/enlargeable/";
$dr_local["tpl"]["all"] = <<<HTML

<span class="caption [[if? &is=`[+dr.source.class+]:notempty` &then=`[+dr.source.class+]`]]" style="width:[+dr.source.width+]px;[[if? &is=`[+dr.source.style+]:notempty` &then=`[+dr.source.style+]`]]">
<a href="[+dr.source.src+]" class="lightbox" data-fancybox="images" data-caption="[[if? &is=`[+dr.source.alt+]:notempty` &then=`[+dr.source.alt+]`]]">
<img src="[[phpthumb? &cacheFolder=`assets/cache/images` &input=`[+dr.source.src+]` &options=`w=[+dr.source.width+],h=[+dr.source.height+],fltr=usm|30|1|3,zc=1,q-90`]]" width="[+dr.source.width+]" height="[+dr.source.height+]" alt="[+dr.source.alt+]" title="Click to enlarge" />
<span class="description"><span>[[if? &is=`[+dr.source.alt+]:notempty` &then=`[+dr.source.alt+]`]]<span class="zoom-link">Zoom</span></span></a></span></span>
HTML;
?>