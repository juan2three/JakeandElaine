<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "jakeandelaine2016@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "fc3b59" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'197A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgdWIH8gKkOSGKiDiKNDg0BAQEoeoFijY4OIkjuW5m1dGnW0pVZ05DcB7Qj0GEKI0wdVIyh0SGAMTQERYwFaBq6OtZW1gZUMdEQoJvRxAYq/KgIsbgPAJvzyPC8CtiFAAAAAElFTkSuQmCC',
			'8221' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMRRYLaBVpdG0ICEVVx9DoAJRBdt/SqFVLV63MWorsPqC6KUAbWlHNYwgAi6KIMToARdHd0gAURRFjDRANdQ0NCA0YBOFHRYjFfQCl3cu0Aio2wQAAAABJRU5ErkJggg==',
			'9B04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQximMDQEIImJTBFpZQhlaEQWC2gVaXR0dGhFE2tlbQiYEoDkvmlTp4YtXRUVFYXkPlZXkLpAB2S9DEDzXBsCQ0OQxAQgdmBzC4oYNjcPVPhREWJxHwBGwc3VDN2sCAAAAABJRU5ErkJggg==',
			'88AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoFWl0dHR0EEFTx9oQCBMDO2lp1Mqwpasis6YhuQ9NHdw811AsYmjqYHqR3QJyM1AMxc0DFX5UhFjcBwDtA8xAwOVt+QAAAABJRU5ErkJggg==',
			'F167' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUNDkMQCGhgDGB0dGkRQxFgDWBvQxRiAYmAa7r7QqFVRS6euWpmF5D6wOkeHVgYMvQFTsIgFoIsxOjo6oIqxhgLdjCI2UOFHRYjFfQCJTMq0Xw5c6AAAAABJRU5ErkJggg==',
			'C882' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGaY6IImJtLK2Mjo6BAQgiQU0ijS6NgQ6iCCLNYDVNYgguS9q1cqwVaEgGuE+qLpGBxS9IPMCWhkw7AiYwoDFLZhuZgwNGQThR0WIxX0AwofMpe8ajQMAAAAASUVORK5CYII=',
			'0488' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGaY6IImxBjBMZXR0CAhAEhOZwhDK2hDoIIIkFtDK6IqkDuykqKVLl64KXTU1C8l9Aa0irejmBbSKhrqimQe0oxXdDqBbMPRic/NAhR8VIRb3AQC3QcsYX/1aYgAAAABJRU5ErkJggg==',
			'7AA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMEx1QBZtZQxhAIoHoIixtjI6OjqIIItNEWl0bQiAqYO4KWraytRVUVOzkNzH6ICiDgxZG0RDXUMDUcwTaQCpQxULaMDUCxVDdfMAhR8VIRb3AQA3182hyrP/LAAAAABJRU5ErkJggg==',
			'D02D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUMdkMQCpjCGMDo6OgQgi7WytrI2BDqIoIiJNDogxMBOilo6bWXWysysaUjuA6trZcTUOwVdjLWVIQBNDOQWB0YUt4DczBoaiOLmgQo/KkIs7gMA6K3L5Flrvy0AAAAASUVORK5CYII=',
			'8176' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6Y6IImJTGEMYGgICAhAEgtoBapsCHQQQFHHEMDQ6OiA7L6lUauiVi1dmZqF5D6wOqCZqOYBxQIYHUTQxBgdUMVAelkbGFD0sgJdDBRDcfNAhR8VIRb3AQBffsm/amXD8AAAAABJRU5ErkJggg==',
			'3445' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYWhkaHUMDkMQCpjBMZWh1dEBR2coQyjAVTWwKoytDoKOrA5L7VkYtXboyMzMqCtl9U0RaWRsdGkRQzBMNdQXaiioGdouDCKpbgGIOAcjug7jZYarDIAg/KkIs7gMAeADL1nMJaVsAAAAASUVORK5CYII=',
			'186A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mOiCJiTqINLo2OAQEoOhlbWUFkiJI7luZtTJs6dSVWdOQ3AdW5+gIUwcVA5kXGBqCKYamDuQWVL2iISA3M6KIDVT4URFicR8AvK7Ij30Eb18AAAAASUVORK5CYII=',
			'3BC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RANEQxhCHUNDkMQCpoi0MjoENIggq2wVaXRtEEAVA6pjBalHct/KqKlhS4FUFrL7IOpaGTDMY5iCKSYQwIDhlkAHLG5GERuo8KMixOI+AAjOy9V8Je3UAAAAAElFTkSuQmCC',
			'0E89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxBog0MDo6BAQgiYlMEWlgbQh0EEESC2gFqXOEiYGdFLV0atiq0FVRYUjug6hzmIqul7UhoEEEw44AFDuwuQWbmwcq/KgIsbgPAEQFyr+X2jxVAAAAAElFTkSuQmCC',
			'9890' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaXRtCAgIQBFjbWVtCHQQQXLftKkrw1ZmRmZNQ3IfqytrK0MIXB0EAs1zaEAVEwCKOaLZgc0t2Nw8UOFHRYjFfQC6lsvJsFksSwAAAABJRU5ErkJggg==',
			'25B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDqIIOtuFQlhbXSEiUHcNG3q0qWhq6LCkN0XwNDo2ugwFVkvowNQrCGgAVmMtUEEJIZiB9DWVnS3hIYyhqC7eaDCj4oQi/sAUSjMZJKONHUAAAAASUVORK5CYII=',
			'E0FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6YGIIkFNDCGsDYwBIigiLG2sjYwOrCgiIk0ugLFkN0XGjVtZWroyixk96GpwyOGzQ5Mt4Dd3MCA4uaBCj8qQizuAwARTMtAiQffEgAAAABJRU5ErkJggg==',
			'417E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjAEsIYGhgYgi4UwBjA0BDogq2MMYcUQYwXqZWh0hImBnTRt2qqoVUtXhmYhuS8ApG4KI4re0FCgWACqGMgtjA6YYqwN6GKsoUAxVDcPVPhRD2JxHwB+q8d/+o2yRAAAAABJRU5ErkJggg==',
			'D4CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWhlCHUNDkMQCpjBMZXQIdEBWFwBUxdogiCbG6MrawAgTAzspaikQrFoZmoXkvoBWkVYkdVAx0VBXDDGGVgw7pjC0orsF6mYUsYEKPypCLO4DAIsjysRLLl5bAAAAAElFTkSuQmCC',
			'838B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUMdkMREpoi0Mjo6OgQgiQW0MjS6NgQ6iKCoY0BWB3bS0qhVYatCV4ZmIbkPTR1O87DbgekWbG4eqPCjIsTiPgD9GctLQT1asgAAAABJRU5ErkJggg==',
			'2C2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGUNDkMREprA2Ojo6OiCrC2gVaXBtCEQRYwCKMSDEIG6aNm3VqpWZoVnI7gsAqmtlRNHL6AAUm4Iqxtog0uAQgCom0gB0iwOqWCjQvayhaG4ZoPCjIsTiPgCLXckPxGwAlgAAAABJRU5ErkJggg==',
			'B024' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGRoCkMQCpjCGMDo6NKKItbK2sgJJVHUijQ5AMgDJfaFR01ZmrcyKikJyH1hdK6MDqnlAsSmMoSFodgBdg+kWB1QxkJtZQwNQxAYq/KgIsbgPABT9znXF/aCtAAAAAElFTkSuQmCC',
			'3CC6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYQxlCHaY6IIkFTGFtdHQICAhAVtkq0uDaIOgggCw2RaSBtYHRAdl9K6OmrVq6amVqFrL7IOowzAPpFcFihwgBt2Bz80CFHxUhFvcBAPiyzAZc0T5HAAAAAElFTkSuQmCC',
			'FA13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIQ6IIkFNDCGMIQwOgSgiLG2AkUbRFDERBodpoBohPtCo6atzJq2amkWkvvQ1EHFRENBYtjMwxRDd4tIo2OoA4qbByr8qAixuA8Ah3zOffyinGUAAAAASUVORK5CYII=',
			'FB90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGVqRxQIaRFoZHR2mOqCKNbo2BAQEoKljbQh0EEFyX2jU1LCVmZFZ05DcB1LHEAJXBzfPoQFTzBGLHZhuwXTzQIUfFSEW9wEACJbNv3E6efEAAAAASUVORK5CYII=',
			'8AD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaY6IImJTGEMYW10CAhAEgtoZW1lbQh0EEFRJ9Lo2hAAUwd20tKoaStTV0VNzUJyH5o6qHmioa5o5gW0gtRhsQPNLawBQDE0Nw9U+FERYnEfAHBnzk4MwcISAAAAAElFTkSuQmCC',
			'5DFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA1qRxQIaRFpZGximOqCKNbo2MAQEIIkFBoDEGB1EkNwXNm3aytTQlVnTkN3XiqIOWSw0BNkOLOpEpoDcgirGGgB0M7p5AxR+VIRY3AcASdfL66ziBcEAAAAASUVORK5CYII=',
			'7816' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMEx1QBZtZW1lCGEICEARE2l0DGF0EEAWmwJUN4XRAcV9USvDVk1bmZqF5D5GB7A6FPNYG0QaHYB6RZDERLCIBTSA9KK6JaCBMYQx1AHVzQMUflSEWNwHAILnyxfFJCT8AAAAAElFTkSuQmCC',
			'C9D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUMDkMREWllbWRsdHZDVBTSKNLo2BKKKNYDFXB2Q3Be1aunS1FWRUVFI7gtoYAx0BalG0cvQiCHWyAK2QwTDLQ4ByO6DuJlhqsMgCD8qQizuAwA/0c009EYm/QAAAABJRU5ErkJggg==',
			'AE03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIQ6IImxBog0MIQyOgQgiYlMEWlgdHRoEEESC2gVaWBtCGgIQHJf1NKpYUuBZBaS+9DUgWFoKEQM3TxsdqC7JaAV080DFX5UhFjcBwCkz8zbCKJ3GQAAAABJRU5ErkJggg==',
			'430B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37prCGMExhDHVAFgsRaWUIZXQIQBJjDGFodHR0dBBBEmOdwtDK2hAIUwd20rRpq8KWrooMzUJyXwCqOjAMDWVodAWKiaC4BdMOhimYbsHq5oEKP+pBLO4DAHj8yvO2eWncAAAAAElFTkSuQmCC',
			'4BCE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpI37poiGMIQ6hgYgi4WItDI6BDogq2MMEWl0bRBEEWOdItLK2sAIEwM7adq0qWFLV60MzUJyXwCqOjAMDQWZhyrGMAXTDqAYhluwunmgwo96EIv7AFejygaG3moIAAAAAElFTkSuQmCC',
			'387F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA0NDkMQCprC2MjQEOqCobBVpdEAXA6lrdISJgZ20Mmpl2KqlK0OzkN0HUjeFEdO8AEwxRwdUMZBbWBtQxcBuRhMbqPCjIsTiPgBPLcmKJv2hKQAAAABJRU5ErkJggg==',
			'0C37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ0EwBEmMNYC10bXRoUEESUxkikiDQ0MAilhAK5DXCBJFuC9q6bRVq6auWpmF5D6oulYGdL0NAVMYMO0IYMBwi6MDFjejiA1U+FERYnEfAEX4zNQAcNbvAAAAAElFTkSuQmCC',
			'F173' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0IdkMQCGhgDGBoCHQJQxFgDQKQIihhDAEOjQ0MAkvtCo1ZFrVq6amkWkvvA6qYwNASg6wWaiG4eowOmGGsDI7pbQlkbGFDcPFDhR0WIxX0AHjPL8lO8UHkAAAAASUVORK5CYII=',
			'5084' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGRoCkMQCGhhDGB0dGlHFWFtZGwJakcUCA0QaHR0dpgQguS9s2rSVWaGroqKQ3dcKUufogKwXJObaEBgagmxHK9gOFLeITAG7BUWMNQDTzQMVflSEWNwHANL/zWwI0E9CAAAAAElFTkSuQmCC',
			'F446' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZWhkaHaY6IIkFNDBMZWh1CAhAFQtlmOroIIAixujKEOjogOy+0KilS1dmZqZmIbkvoEGklbXREc080VDX0EAHEVQ7gG5xxCKG4RYMNw9U+FERYnEfANjUzaUwOjsrAAAAAElFTkSuQmCC',
			'504B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYAhgaHUMdkMQCGhhDGFodHQJQxFhbGaY6OoggiQUGiDQ6BMLVgZ0UNm3ayszMzNAsZPe1ijS6NqKaBxYLDUQxL6AVaEcjqh0iU4BuQdPLGoDp5oEKPypCLO4DAEL5zCF6Jh/FAAAAAElFTkSuQmCC',
			'F896' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOgigqWMFiiG7LzRqZdjKzMjULCT3gdQxhARimOcA1CuCJuaIIYbNLZhuHqjwoyLE4j4AKDrNEnCK4D0AAAAASUVORK5CYII=',
			'0BAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMIaGIImxBoi0MoQyOiCrE5ki0ujo6IgiFtAq0sraEAgTAzspaunUsKWrIkOzkNyHpg4m1ugaGohhhyuaOpBb0PWC3IwuNlDhR0WIxX0AkqjKPJX19nsAAAAASUVORK5CYII=',
			'4F52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGuoY6THVAFgsRaWBtYAgIQBJjBIsxOoggibFOAYpNZWgQQXLftGlTw5ZmZq2KQnJfwBSQioBGZDtCQ8FirahuAdkRMAVdjNHRIQBdjCGUMTRkMIQf9SAW9wEA79vMBKCcpvoAAAAASUVORK5CYII=',
			'24B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDGVqRxUSmMExlbXSY6oAkFtDKEMraEBAQgKy7ldGVtdHRQQTZfdOWLl0aujJrGrL7AkRakdSBIaODaKhrQyCKGCvQRHQ7REBiaG4JDcV080CFHxUhFvcBAMFoy+gvXSbCAAAAAElFTkSuQmCC',
			'5046' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYAhgaHaY6IIkFNDCGMLQ6BASgiLG2Mkx1dBBAEgsMEGl0CHR0QHZf2LRpKzMzM1OzkN3XKtLo2uiIYh5YLDTQQQTZjlagHY2OKGIiU4BuaUR1C2sAppsHKvyoCLG4DwC56cyJtHnSpAAAAABJRU5ErkJggg==',
			'BD4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHUNDkMQCpoi0MrQ6OiCrC2gVaXSYiiY2BSgWCBcDOyk0atrKzMzM0Cwk94HUuTZimucaGohpB7o6kFvQxKBuRhEbqPCjIsTiPgBld80KYrbLpwAAAABJRU5ErkJggg==',
			'9883' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUIdkMREprC2Mjo6OgQgiQW0ijS6NgQ0iKCIgdQ5NAQguW/a1JVhq0JXLc1Cch+rK4o6CMRingAWMWxuwebmgQo/KkIs7gMAbuDMXyB0X6sAAAAASUVORK5CYII=',
			'FD09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEETc20IhImBnRQaNW1l6qqoqDAk90HUBUzF1AskMexwQLcDi1sw3TxQ4UdFiMV9AH4BziNOqJiQAAAAAElFTkSuQmCC',
			'A781' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgDGBodHR2mIouJTGFodG0ICEUWC2hlaGV0dIDpBTspaumqaatCVy1Fdh9QXQCSOjAMDWV0YAXJoJjH2oApJtKArhckxhDKEBowCMKPihCL+wAiqcxgE8Gp3AAAAABJRU5ErkJggg==',
			'0C24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGRoCkMRYA1gbHR0dGpHFRKaINLg2BLQiiwW0ioDIKQFI7otaOm3VqpVZUVFI7gOra2V0wNA7hTE0BM0OhwAsbnFAFQO5mTU0AEVsoMKPihCL+wDDNM1nI619CgAAAABJRU5ErkJggg==',
			'3E13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7RANEQxmmMIQ6IIkFTBFpYAhhdAhAVtkq0sAYwtAggiwGUjcFqB7JfSujpoatmrZqaRay+1DVwc0DiYkQEAO7ZQqqW0BuZgx1QHHzQIUfFSEW9wEAf0/Luqj+W8gAAAAASUVORK5CYII=',
			'17EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHUNDkMRYHRgaXYEyyOpEsYgBea2sCDGwk1ZmrZq2NHRlaBaS+4AqAlgx9DI6YIqxNmCKiWCIiYYAxUIdUcQGKvyoCLG4DwC9QMYBbk1nmQAAAABJRU5ErkJggg==',
			'D3FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDA0NDkMQCpoi0sjYwOiCrC2hlaHTFFENWB3ZS1NJVYUtDV4ZmIbkPTR0+8zDFsLgF7GY0sYEKPypCLO4DAGveypx3JFW7AAAAAElFTkSuQmCC',
			'D962' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtcHQQwRBjaBBBcl/U0qVLU6cCaST3BbQyBro6OjSi2NHKANQLJFHEWEBiUxiwuAXTzYyhIYMg/KgIsbgPAOvXzlEcehg8AAAAAElFTkSuQmCC',
			'4999' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjCGMIQyTHVAFgthbWV0dAgIQBJjDBFpdG0IdBBBEmOdgiIGdtK0aUuXZmZGRYUhuS9gCmOgQ0jAVGS9oaEMjQ4NAQ0iKG5haXRsCHBAFcN0C1Y3D1T4UQ9icR8Aw53L/41be6sAAAAASUVORK5CYII=',
			'4774' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37poiGuoYGNAQgi4UwNDo0BDQiizFCxFqRxVinMLQCRacEILlv2rRV01YtXRUVheS+gCkMAQxTGB2Q9YaGMjowBDCGhqC4hbUBKIrqlikiDawNRIgNVPhRD2JxHwBjos2yMVBYcwAAAABJRU5ErkJggg==',
			'5915' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AMAwEbYlskIFMQW+khIIRmCIpvIFhAwqYEuhMoAQp/90V9ulhfyRBTf3FLwYMoBjZME5OICDBjfncFqxnn0mxI+M3LOs6Lds4Wj/BnhSSt58FcslYmuseWeb1dFFg6+cYA0aaqYL9PuyL3wEtpct7qQ/UsgAAAABJRU5ErkJggg==',
			'8C1C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEwNQBITmcLa6BDCECCCJBbQKtLgGMLowIKiDqhiCqMDsvuWRk1btWrayixk96Gpg5uHTcxhCrodQLdMQXULyM2MoQ4obh6o8KMixOI+ADtYy4L0BlzCAAAAAElFTkSuQmCC',
			'AEC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHVqRxVgDRIDiAVORxUSmiDSwNgiEIosFtILEGGB6wU6KWjo1bOmqVUuR3YemDgxDQzHFIOoEMMSAbkETA7s5NGAQhB8VIRb3AQCy68wgBi6EKAAAAABJRU5ErkJggg==',
			'4383' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpI37prCGMIQyhDogi4WItDI6OjoEIIkxhjA0ujYENIggibFOYQCqc2gIQHLftGmrwlaFrlqaheS+AFR1YBgaimkewxRsYphuwermgQo/6kEs7gMALYnMWbP4RScAAAAASUVORK5CYII=',
			'B30E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNYQximMIYGIIkFTBFpZQhldEBWF9DK0Ojo6IgqNoWhlbUhECYGdlJo1KqwpasiQ7OQ3IemDm6eKxYxTDsw3YLNzQMVflSEWNwHABZhy1NbKKe5AAAAAElFTkSuQmCC',
			'6BAC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQximMEwNQBITmSLSyhDKECCCJBbQItLo6OjowIIs1iDSytoQ6IDsvsioqWFLV0VmIbsvZAqKOojeVpFG11AsYkB1yHaIgPUGoLgF5GagGIqbByr8qAixuA8AcJXMp8wzBGUAAAAASUVORK5CYII=',
			'9321' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANYQxhCGVqRxUSmiLQyOjpMRRYLaGVodG0ICEUTawWTSO6bNnVV2KqVWUuR3cfqytAKhsg2A81zmIIqJgASC8DiFgdUMZCbWUMDQgMGQfhREWJxHwASIss4h+ISrQAAAABJRU5ErkJggg==',
			'6E7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MdkMREpogAyUCHACSxgBaImAiyWAOQ1+gIEwM7KTJqatiqpSuzpiG5LwRk3hRGVL2tQF4AphijA6oYyC2sQFFkt4Dd3MCI4uaBCj8qQizuAwAl3csi0QT9fAAAAABJRU5ErkJggg==',
			'06BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUMdkMRYA1hbWRsdHQKQxESmiDSyNgQ6iCCJBbSKNIDUiSC5L2rptLCloSuzpiG5L6BVtBVJHUxvoyuaeSA70MWwuQWbmwcq/KgIsbgPAOXMy0E6rp9QAAAAAElFTkSuQmCC',
			'815B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjAGsDYwOgQgiQW0soLFRFDUAfVOhasDO2lp1KqopZmZoVlI7gOpY2gIRDMPIiaCJsaKJgbSy+joiKIX6JJQhlBGFDcPVPhREWJxHwBcg8kmSxlJLQAAAABJRU5ErkJggg==',
			'C183' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUIdkMREWhkDGB0dHQKQxAIaWQNYGwIaRJDFGhiA6hwaApDcFwVCoauWZiG5D00dXAzDvEZMMZFWBgy3sIawhqK7eaDCj4oQi/sATd7KuhkAet4AAAAASUVORK5CYII=',
			'76AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMIaGIIu2srYyhDI6oKhsFWlkdHREFZsi0sDaEAgTg7gpalrY0lWRoVlI7mN0EG1FUgeGrA0ija6hqGIiIDE0dQENrBh6AxoYQ9DFBir8qAixuA8AHNTJ/6DsVoAAAAAASUVORK5CYII=',
			'4045' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37pjAEMDQ6hgYgi4UwhjC0Ojogq2MMYW1lmIoqxjpFpNEh0NHVAcl906ZNW5mZmRkVheS+AKA610aHBhEkvaGhQDGgrSIobgHa0ejogCoGdEujQwCK+8BudpjqMBjCj3oQi/sAbcbL4X90M+4AAAAASUVORK5CYII=',
			'942D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUMdkMREpjBMZXR0dAhAEgsAqmJtCHQQQRFjdGVAiIGdNG3q0qWrVmZmTUNyH6urSCtDKyOKXoZW0VCHKahiAkBVDAGoYkC3AHUyorgF5GbW0EAUNw9U+FERYnEfAFv9ydLOciLlAAAAAElFTkSuQmCC',
			'1727' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUNDkMRYHRgaHR0dGkSQxESBYq4NAShijA4MrQxAsQAk963MWjUNSAAphPuA6gKAKltR7QWKTgFCFDHWBqDKAFQxEYhaZLeEiDSwhgaiiA1U+FERYnEfAMnOyENgvoMDAAAAAElFTkSuQmCC',
			'4A3D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37pjAEMIYyhjogi4UwhrA2OjoEIIkBRVoZGgIdRJDEWKeINDoA1YkguW/atGkrs6auzJqG5L4AVHVgGBoqCrQT1TwGkDosYq5obgGJOaK7eaDCj3oQi/sAOg/MofPVKN0AAAAASUVORK5CYII=',
			'B404' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYWhmmMDQEIIkFTGGYyhDK0Igi1soQyujo0IqqjtGVFUgGILkvNGrp0qWroqKikNwXMEWklbUh0AHVPNFQ14bA0BBUO1qBdqC7BWgzqvuwuXmgwo+KEIv7AG/Cztu/iZPdAAAAAElFTkSuQmCC',
			'BCEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDHaYGIIkFTGFtdG1gCBBBFmsVaXBtYHRgQVEn0sAKFEN2X2jUtFVLQ1dmIbsPTR3cPGximHZgugWbmwcq/KgIsbgPAL7fzJzQivJQAAAAAElFTkSuQmCC',
			'D782' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGaY6IIkFTGFodHR0CAhAFmtlaHRtCHQQQRVrZXR0aBBBcl/U0lXTVoUCaST3AdUFANU1otjRyujACpJBEWNtYAXZjuIWkQag3gBUNwNtDGUMDRkE4UdFiMV9AGL8zcSAwbBUAAAAAElFTkSuQmCC',
			'113B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUMdkMRYHRgDWBsdHQKQxEQdWAMYGgIdRND0MiDUgZ20MmtV1KqpK0OzkNyHpg4hhs08LGIYbglhDUV380CFHxUhFvcBAK9Jxwv2pnJYAAAAAElFTkSuQmCC',
			'4FE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpI37poiGuoY6THVAFgsRaWBtYAgIQBJjBIsxOoggibFOQVEHdtK0aVPDloaumpqF5L6AKZjmhYZimscwBZcYql6wGLqbByr8qAexuA8AJ0bLbxtcJM0AAAAASUVORK5CYII=',
			'0417' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIaGIImxBjBMZQhhaBBBEhOZwhDKiCYW0MroyjAFSCO5L2rp0qWrpq1amYXkvoBWEaAdQHtQ9IqGOkwB6UaxA6QugAHVLSD3OaC7mTHUEUVsoMKPihCL+wAAp8pXs4ccZwAAAABJRU5ErkJggg==',
			'7F04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMDQEIIu2ijQwhDI0oosxOjq0oohNEWlgbQiYEoDsvqipYUtXRUVFIbmP0QGkLtABWS9rA1gsNARJTKQBbAeKWwIawG7BFEN38wCFHxUhFvcBAK4bzX0y8SnSAAAAAElFTkSuQmCC',
			'AC4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxkaHUMdkMRYA1gbHVodHQKQxESmiDQ4THV0EEESC2gF8gLhYmAnRS2dtmplZmbWNCT3gdSxNqLqDQ0FioUGYpjn0IhuB9AtjahuCWjFdPNAhR8VIRb3AQAxzc1I4sSgxQAAAABJRU5ErkJggg==',
			'0E5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMDkMRYA0QaWIEyyOpEpmCKBbQCxabCxcBOilo6NWxpZmZoFpL7QOoYGgIx9KKLQexAFQO5hdHREUUM5GaGUEYUNw9U+FERYnEfAAwTyOMhKchUAAAAAElFTkSuQmCC',
			'C971' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA1qRxURaWYH8gKnIYgGNIo0ODQGhKGINQLFGB5hesJOiVi1dmrV01VJk9wU0MAY6TGFoRdXL0OgQgCbWyNLo6MCA4RbWBlQxsJsbGEIDBkH4URFicR8AcznM/nGmxeQAAAAASUVORK5CYII=',
			'2EBF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUNDkMREpog0sDY6OiCrC2gFijUEoogxtKKog7hp2tSwpaErQ7OQ3ReAaR6jA6Z5rA2YYiINmHpDQ8FuRnXLAIUfFSEW9wEA0Q3JYIr7iAUAAAAASUVORK5CYII=',
			'BCB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGaY6IIkFTGFtdG10CAhAFmsVaXBtCHQQQFEn0sDa6OiA7L7QqGmrloauTM1Cch9UHYZ5rEDzRLDYIULALdjcPFDhR0WIxX0ASInOswEOvfYAAAAASUVORK5CYII=',
			'D731' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1DGVqRxQKmMDS6NjpMRRFrZWh0aAgIRRMDicL0gp0UtXTVtFVTVy1Fdh9QXQCSOqgYowNYBkWMtQFDbIpIAyua3tAAkQbGUIbQgEEQflSEWNwHAIR5zsMh7CTEAAAAAElFTkSuQmCC',
			'6123' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUIdkMREpjAGMDo6OgQgiQW0sAawNgQ0iCCLNQD1AskAJPdFRq2KWrUya2kWkvtCpgDVtTI0oJjXChSbwoBqHkgsAFVMBKiX0YERxS1Al4SyhgaguHmgwo+KEIv7AOw7ylYkAYm0AAAAAElFTkSuQmCC',
			'EC9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUMdkMQCGlgbHR0dHQJQxEQaXBsCHUTQxFiBYgFI7guNmrZqZWZkaBaS+0DqGEICMcxjwGKeI4YYpluwuXmgwo+KEIv7AEZ6zQ7YmGBwAAAAAElFTkSuQmCC',
			'EEF8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA6Y6IIkFNIg0sDYwBARgiDE6iOBWB3ZSaNTUsKWhq6ZmIbmPNPPw2oFwcwMDipsHKvyoCLG4DwCB9sxlOZherQAAAABJRU5ErkJggg==',
			'1E13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIQ6IImxOog0MIQwOgQgiYkCxRhDGBpEUPQCeVMYGgKQ3Lcya2rYqmmrlmYhuQ9NHYoYNvMwxdDcEiIayhjqgOLmgQo/KkIs7gMAE4nJCr8QroQAAAAASUVORK5CYII=',
			'DABF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUNDkMQCpjCGsDY6OiCrC2hlbWVtCEQTE2l0RagDOylq6bSVqaErQ7OQ3IemDiomGuqKzTx0sSmYekMDgGKhjChiAxV+VIRY3AcA297M5eTMwo4AAAAASUVORK5CYII=',
			'FE38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlDGaY6IIkFNIg0sDY6BASgiTE0BDqIoIsh1IGdFBo1NWzV1FVTs5Dch6YOv3lYxDDdgunmgQo/KkIs7gMAmajOKvT2jnoAAAAASUVORK5CYII=',
			'0629' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWlkbQh0EEESC2gF8eBiYCdFLZ0WtmplVlQYkvsCWkVbGVoZpqLpbXSYAjQXzQ6HAAYUO8BucWBAcQvIzayhAShuHqjwoyLE4j4ATo3Kvma9ykAAAAAASUVORK5CYII=',
			'5544' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHRoCkMQCGkQaGFodGjHEpjq0IosFBoiEMAQ6TAlAcl/YtKlLV2ZmRUUhu6+VodG10dEBWS9YLDQwNATZjlaRRgc0t4hMYW1Fdx9rAGMIuthAhR8VIRb3AQASZ887cvCZpgAAAABJRU5ErkJggg==',
			'2557' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUNDkMREpog0sIJoJLGAVkwxhlaRENapQDlk902bunRpZtbKLGT3BTA0OgBNQLaX0QEsNgXFLQ0ija4NAQHIYkBbWxkdHR2QxUJDGUMYQhlRxAYq/KgIsbgPAEify0LPbI5xAAAAAElFTkSuQmCC',
			'3EB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7RANEQ1lDGaY6IIkFTBFpYG10CAhAVtkKFGsIdBBBFkNVB3bSyqipYUtDV03NQnYfseZhEcPmFmxuHqjwoyLE4j4AiAbMeddj0SsAAAAASUVORK5CYII=',
			'29BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUNDkMREprC2sjY6OiCrC2gVaXRtCEQRYwCJIdRB3DRt6dLU0JWhWcjuC2AMdEUzj9GBAcM81gYWDDGRBky3hIaC3YzqlgEKPypCLO4DAMXwyh/cVeQIAAAAAElFTkSuQmCC',
			'223F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6OiCrC2gVaXRoCEQRY2hlaHRAqIO4adqqpaumrgzNQnZfAMMUBjTzGB2AomjmsYJE0cREgKLobgkNFQ11DGVEdcsAhR8VIRb3AQBxq8nRcOlD4QAAAABJRU5ErkJggg==',
			'74D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZWllDGaY6IIu2MkxlbXQICEAVC2VtCHQQQRabwuiKJAZxU9TSpUtXRUWFIbmP0UGklbUhYCqyXtYG0VDXhoAGZDEgG6QOxY4AkBiaW8Bi6G4eoPCjIsTiPgAMUcxJ5IfvAAAAAABJRU5ErkJggg==',
			'E3B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGaY6IIkFNIi0sjY6BASgiDE0ujYEOoigigHVOcLEwE4KjVoVtjR0VVQYkvsg6hymimCYB7QJUwzNDky3YHPzQIUfFSEW9wEARF7N8R/IsPoAAAAASUVORK5CYII=',
			'7F0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIaGIIu2ijQwhDI6MKCJMTo6oopNEWlgbQiEiUHcFDU1bOmqyNAsJPcxOqCoA0PWBkwxkQZMOwIaMN0CFpuC5r4BCj8qQizuAwCCQ8k0oOjRqAAAAABJRU5ErkJggg==',
			'5228' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoggiQUGMDQ6NATA1IGdFDZt1dJVK7OmZiG7r5VhChCjmAfiM0xhRDEvoJXRgSEAVUxkCmsDSBRZL2uAaKhraACKmwcq/KgIsbgPAATby8qHEK0aAAAAAElFTkSuQmCC',
			'B633' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUIdkMQCprC2sjY6OgQgi7WKNALJBhEUdUBeo0NDAJL7QqOmha2aumppFpL7AqaItiKpg5vngG4eNjEsbsHm5oEKPypCLO4DANunzzStCJwgAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>