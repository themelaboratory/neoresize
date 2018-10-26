<?php
$dr_global["path"] = "assets/images/enlargeable/";
$dr_local["tpl"]["all"] = <<<HTML

<span class="caption [+dr.source.class+]" style="width:[+dr.source.width+]px;">
<a href="[+dr.source.src+]" class="lightbox" data-fancybox-group="lightbox_group">
<img src="[[phpthumb? &input=`[+dr.source.src+]` &options=`w=[+dr.source.width+],h=[+dr.source.height+],fltr=usm|40|1|3,zc=1,q-90`]]" width="[+dr.source.width+]" height="[+dr.source.height+]" alt="[+dr.source.alt+]" title="Click to enlarge" />
<span class="description">[[if? &is=`[+dr.source.alt+]:notempty` &then=`[+dr.source.alt+]`]]<span class="zoom-link">Zoom</span></span></a></span>
HTML;
?>