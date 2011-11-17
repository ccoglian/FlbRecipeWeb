<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<!--        <base href="<?php //echo (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://'; echo HOST;?>/" />-->
        <?php   //require('template_header.php');  ?>
    </head>
    
    <body>
        
        <?php   //require('template_nav.php');     ?>
        
        <div id="wrapper">
    
            <div class="wrapper-inner">
                
                <?php   
                        $view = 'views/view_'.$view.'.php';
                        require($view);
                ?>
        
            </div><!-- end #wrapper_inner -->
    
        </div><!-- end #wrapper -->

        <?php   //require('template_footer.php');  ?>
        
    </body>
</html>

