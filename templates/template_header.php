<title><?php echo $title;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="js/head.min.js"></script>

<script type="text/javascript">
var _sf_startpt = (new Date()).getTime();
var mpq=[];mpq.push(["init","72f32cec03db2fd93a4710f5889422b9"]);
(function(){var b,a,e,d,c;b=document.createElement("script");b.type="text/javascript";b.async=true;b.src=(document.location.protocol==="https:"?"https:":"http:")+"//api.mixpanel.com/site_media/js/api/mixpanel.js";a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(b,a);e=function(f){return function(){mpq.push([f].concat(Array.prototype.slice.call(arguments,0)))}};d=["init","track","track_links","track_forms","register","register_once","identify","name_tag","set_config"];for(c=0;c<d.length;c++){mpq[d[c]]=e(d[c])}})();
<?php 
    $clientid = defined('COOKIE_CLIENTID') ? COOKIE_CLIENTID : null;
    //echo "/* ".print_r($_SESSION, true)." */";
    //echo "//client_id = $clientid<br/>\n";
        if(!empty($clientid)) { ?>
            mpq.identify('<?php echo $clientid;?>');
            mpq.name_tag('<?php echo 'USER_'.$clientid;?>');
<?php   } ?>


head.ready("typekit", function(){
    try { Typekit.load(); } catch(e) {}
});
 
// load scripts by assigning a label for them
head.js(
    {typekit:           "//use.typekit.com/vpf2iwr.js"},
    {jquery:            "//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"},
    {jquery_validate:   "//ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"},
    {jquery_colorbox:   "js/jquery.colorbox-min.js"},
    {browser_detect:    "js/browserdetect.js"},
    {local_script:      "js/scripts.js"},
    {google_plus:       "//apis.google.com/js/plusone.js"}
);
</script>

<link type="text/css" rel="stylesheet" media="screen" href="css/style.css"/>
<link type="text/css" rel="stylesheet" media="screen" href="css/colorbox.css"/>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

<?php   if(ENVIRONMENT == 'prod') { ?>
<!-- Start Google Analytics -->
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-23978837-1']);
    _gaq.push(['_setDomainName', '.powerinbox.com']);
    _gaq.push(['_trackPageview']);
    _gaq.push(['_trackPageLoadTime']);

    (function () {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
<!-- End Google Analytics -->

<!-- GetClicky -->
<script type="text/javascript">
head.js("//static.getclicky.com/js", function() {
    try { clicky.init(66364474); } catch (err) {}
});
</script>
<!-- End GetClicky -->

<!-- Start of Woopra Code -->
<script type="text/javascript">
function woopraReady(tracker) {
    tracker.setDomain('powerinbox.com');
    tracker.setIdleTimeout(300000);
    tracker.track();
    return false;
}
(function() {
    var wsc = document.createElement('script');
    wsc.src = document.location.protocol+'//static.woopra.com/js/woopra.js';
    wsc.type = 'text/javascript';
    wsc.async = true;
    var ssc = document.getElementsByTagName('script')[0];
    ssc.parentNode.insertBefore(wsc, ssc);
})();
</script>
<!-- End of Woopra Code -->

<?php   } // end prod tracking codes ?>
