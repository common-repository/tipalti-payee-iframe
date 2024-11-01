
<iframe id='<?php echo $iframe_id; ?>' class="tipalti_iframe" frameBorder='0' style="min-width: 1020px" src="<?php echo $iframe_url; ?>"> </iframe>

<script>
    
(function () {
    tipaltiHandler = function (evt) {
        if (evt.data && evt.data.TipaltiIframeInfo) {
            if (evt.data.TipaltiIframeInfo.height)
                document.getElementById("<?php echo $iframe_id; ?>").style.minHeight = evt.data.TipaltiIframeInfo.height +"px";
        }

    }

    if (window.addEventListener)
        window.addEventListener("message", tipaltiHandler, false);
    else
        window.attachEvent("onmessage", tipaltiHandler);
  
}());


</script>