<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "perez@knights.ucf.edu" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "860230" );

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
			'38A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYQximMLQiiwVMYW1lCGWYiqKyVaTR0dEhFEUMqI61IQCmF+yklVErw5auilqK4j5UdXDzXEOxiKGpC8CiF+RmoFhowCAIPypCLO4DAI6gzMoIHxomAAAAAElFTkSuQmCC',
			'E408' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMEx1QBILaGCYyhDKEBCAKhbK6OjoIIIixujK2hAAUwd2UmjU0qVLV0VNzUJyX0CDSCuSOqiYaKhrQyCaeQytmHYA3YfmFmxuHqjwoyLE4j4AFDfM89lxqqkAAAAASUVORK5CYII=',
			'F841' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZQxgaHVqRxQIaWFsZWh2mooqJNDpMdQjFUBcI1wt2UmjUyrCVmVlLkd0HUseKYYdIo2toAIaYAza3YIiB3RwaMAjCj4oQi/sA9wXOiyOQoOYAAAAASUVORK5CYII=',
			'CDEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7WENEQ1hDHUNDkMREWkVaWRsYHZDVBTSKNLqiizWgiIGdFLVq2srU0JWhWUjuQ1OHWwyLHdjcAnUzithAhR8VIRb3AQAPPMpcQfffiAAAAABJRU5ErkJggg==',
			'B832' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGaY6IIkFTGFtZW10CAhAFmsVaXRoCHQQQVPHABQVQXJfaNTKsFVTV62KQnIfVF2jA4Z5Aa0MmGJTGLC4BdPNjKEhgyD8qAixuA8ApBvO7G5W/vIAAAAASUVORK5CYII=',
			'9588' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEEVC0FSB3bStKlTl64KXTU1C8l9rK4MjY5o5jG0MjS6opkn0CqCISYyhbUV3S2sAYwh6G4eqPCjIsTiPgDdUMvqtEy1rwAAAABJRU5ErkJggg==',
			'BB26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtCHQQQFPHABRDdl9o1NSwVSszU7OQ3AdW18qIYZ7DFEYHEXSxADQxkFscGFD0gtzMGhqA4uaBCj8qQizuAwBVGs0VvAtkzAAAAABJRU5ErkJggg==',
			'AAD4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGRoCkMRYAxhDWBsdGpHFRKawtrI2BLQiiwW0ijS6NgRMCUByX9TSaStTV0VFRSG5D6Iu0AFZb2ioaChQLDQE07wGDDsaHTDF0Nw8UOFHRYjFfQBhMdAnn3JBPQAAAABJRU5ErkJggg==',
			'C16A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGVqRxURaGQMYHR2mOiCJBTSyBrA2OAQEIIs1MADFGB1EkNwXBURLp67MmobkPrA6R0eYOiS9gaEhKHaAxVDUibQyAN2Cqpc1hDWUIZQRRWygwo+KEIv7AACSyYF5I1f/AAAAAElFTkSuQmCC',
			'DC89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGaY6IIkFTGFtdHR0CAhAFmsVaXBtCHQQQRNjBCoUQXJf1NJpq1aFrooKQ3IfRJ3DVHS9rA0BDehirg0BqHZgcQs2Nw9U+FERYnEfAHUVzhCfseuZAAAAAElFTkSuQmCC',
			'62B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoEWl0bQh0EEEWa2BodEWoAzspMmrV0qWhq6ZmIbkvZArDFAzzWhkCWNHNa2V0QBcDuqUBXS9rgGioK5qbByr8qAixuA8Arz/NXeRbw+UAAAAASUVORK5CYII=',
			'3809' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYQximMEx1QBILmMLayhDKEBCArLJVpNHR0dFBBFkMqI61IRAmBnbSyqiVYUtXRUWFIbsPrC5gqgiaea4NAQ3oYkArUOzA5hZsbh6o8KMixOI+AIIry7qBL0ZqAAAAAElFTkSuQmCC',
			'9AFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6YGIImJTGEMYW1gCBBBEgtoZW1lbWB0YEERE2l0BYohu2/a1GkrU0NXZiG7j9UVRR0EtoqGoosJQM1DtkNkCkgM1S2sAWAxFDcPVPhREWJxHwBhA8rn5cYU0AAAAABJRU5ErkJggg==',
			'9CB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDGaY6IImJTGFtdG10CAhAEgtoFWlwbQh0EEETY210aBBBct+0qdNWLQ1dtSoKyX2srmB1jch2MID0Ak1AdosA2I6AKQxY3ILpZsbQkEEQflSEWNwHAPwpzX87SP3jAAAAAElFTkSuQmCC',
			'1BE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxOoi0sjYwBAQgiYk6iDS6AlWLoOhFUQd20sqsqWFLQ1dNzUJyHyMW8xixm0fIDohbQjDdPFDhR0WIxX0AVXzJKrc4iIgAAAAASUVORK5CYII=',
			'B1B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGRoCkMQCpjAGsDY6NKKItbIGsAJJVHUMIHVTApDcFxq1Kmpp6KqoKCT3QdQ5OqCaBxRrCAwNwRALaMBiB4pYKNDF6G4eqPCjIsTiPgA+e83fvpVsHQAAAABJRU5ErkJggg==',
			'670F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIaGIImJTGFodAhldEBWF9DC0Ojo6Igq1sDQytoQCBMDOykyatW0pasiQ7OQ3BcyhSEASR1EbyujA6YYawMjmh0iU0QaGNDcwhoAFJuCKjZQ4UdFiMV9AH72yctWk2aUAAAAAElFTkSuQmCC',
			'E3BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUNDkMQCGkRaWRsdHRhQxBgaXRsC0cWQ1YGdFBq1Kmxp6MrQLCT3oanDZx4WMUy3QN2MIjZQ4UdFiMV9AH74y4wyzwV2AAAAAElFTkSuQmCC',
			'FCDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7QkMZQ1mBOABJLKCBtdG10dGBAUVMpMG1IRBDjBUhBnZSaNS0VUtXRYZmIbkPTR1eMUw7sLkF080DFX5UhFjcBwADhszjW7/UogAAAABJRU5ErkJggg==',
			'CC56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDHaY6IImJtLI2ujYwBAQgiQU0ijS4NjA6CCCLNYg0sE5ldEB2X9SqaauWZmamZiG5D6SOoSEQ1TyImIMIhh2oYiC3ODo6oOgFuZkhlAHFzQMVflSEWNwHANEXzLjYfMNWAAAAAElFTkSuQmCC',
			'3C52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDHaY6IIkFTGFtdG1gCAhAVtkq0uDawOgggiw2RaSBdSpDgwiS+1ZGTVu1NDNrVRSy+6aAVAQ0OqCZBxRrZcCwI2AKA5pbHB0dAtDdzBDKGBoyCMKPihCL+wDfKcyrZSefpgAAAABJRU5ErkJggg==',
			'8BB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGaY6IImJTBFpZW10CAhAEgtoFWl0bQh0EMFQ5wgTAztpadTUsKWhq6LCkNwHNW+qCIZ5AQ1YxLDYgeoWbG4eqPCjIsTiPgCmgs13iaQ7DwAAAABJRU5ErkJggg==',
			'444B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjC0MjQ6hjogi4UwTGVodXQIQBJjDGEIZZjq6CCCJMY6hdGVIRCuDuykadOWLl2ZmRmaheS+gCkirayNqOaFhoqGuoYGopgHdQtWsQBMMVQ3D1T4UQ9icR8AXKDLsC1u9FAAAAAASUVORK5CYII=',
			'69BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMdUASC2gRaXRtCAgIQBZrAIo1OjqIILkvMmrp0tTQlVnTkNwXMoUxEEkdRG8rA9C8wNAQFDEWkBiKOohbUPVC3MyIIjZQ4UdFiMV9AFX6zPN8t+7fAAAAAElFTkSuQmCC',
			'BA2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUNDkMQCpjCGMDo6OiCrC2hlbWVtCEQVmyLS6IAQAzspNGrayqyVmaFZSO4Dq2tlRDNPNNRhCroYUF0AI4Ydjg6oYqEBIo2uoahuGajwoyLE4j4A/KPLUZc2b7QAAAAASUVORK5CYII=',
			'665D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUMdkMREprC2sjYwOgQgiQW0iDSCxESQxRpEGlinwsXAToqMmha2NDMzaxqS+0KmiLYyNASi6m0VaXTAIuaKJgZyC6OjI4pbQG5mCGVEcfNAhR8VIRb3AQDkCss97cqFywAAAABJRU5ErkJggg==',
			'C9B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGVqRxURaWVtZGx2mIosFNIo0ujYEhKKINQDFGh1gesFOilq1dGlq6KqlyO4LaGAMRFIHFWMAmYcq1siCIQZ1C4oY1M2hAYMg/KgIsbgPAAsszbqe6KrvAAAAAElFTkSuQmCC',
			'DF5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUMdkMQCpog0sDYwOgQgi7VCxETQxabC1YGdFLV0atjSzMzQLCT3gdQxNARimAcSwzAPXQzoFkZHRxS9oQFAFaGMKG4eqPCjIsTiPgAisczp+uum3wAAAABJRU5ErkJggg==',
			'D975' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0MDkMQCprC2MjQEOiCrC2gVaXTAJtbo6OqA5L6opUuXZi1dGRWF5L6AVsZAhykMDSIoehkaHQLQxVgaHR0YHUTQ3MLawBCA7D6wmxsYpjoMgvCjIsTiPgAPRc2VA4MCMgAAAABJRU5ErkJggg==',
			'3CAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMIYGIIkFTGFtdAhldEBR2SrS4OjoiCo2RaSBtSEQJgZ20sqoaauWrooMzUJ2H6o6uHmsoZhirmjqQG5BFwO5GWgeipsHKvyoCLG4DwCmwMsrhFBAyQAAAABJRU5ErkJggg==',
			'E30C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNYQximMEwNQBILaBBpZQhlCBBBEWNodHR0dGBBFWtlbQh0QHZfaNSqsKWrIrOQ3YemDm6eKxYxTDsw3YLNzQMVflSEWNwHABBszB3IGcAtAAAAAElFTkSuQmCC',
			'09A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLo6OjoIIIkFtAq0ujaEAgTAzspaunSpamroqLCkNwX0MoY6NoQMBVVL0Oja2hAgwiKHSxA8wJQ7AC5hbUhAMUtIDcDxVDcPFDhR0WIxX0AScfMi7PpfroAAAAASUVORK5CYII=',
			'FE9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUMDkMQCGkQaGB0dHRjQxFgbAvGJgZ0UGjU1bGVmZGgWkvtA6hhCMPUyYDGPEZsYhlsw3TxQ4UdFiMV9AGGOysmIdx6RAAAAAElFTkSuQmCC',
			'B322' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaY6IIkFTBFpZXR0CAhAFmtlaHRtCHQQQVHH0AokG0SQ3BcatSps1cqsVVFI7gOrA+p3QDPPAawfTSyAYQoDulscGALQ3cwaGhgaMgjCj4oQi/sAsUvNR96n5bgAAAAASUVORK5CYII=',
			'E412' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMEx1QBILaGCYyhDCEBCAKhbKGMLoIIIixugK1NsgguS+0KilS1dNW7UqCsl9AQ0iIDsaUe0QDXWYArQb1Q6QuilYxALQ3cwY6hgaMgjCj4oQi/sAqlLMpHCFH7EAAAAASUVORK5CYII=',
			'3127' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGUNDkMQCpjAGMDo6NIggq2xlDWBtCEAVmwLUCxQLQHLfyqhVUatWZq3MQnYfSF0rEKKYBxSbAoToYiCI4haGAEYHRgdUN7OGsoYGoogNVPhREWJxHwApncio5yG2mwAAAABJRU5ErkJggg==',
			'0595' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2QsQ2AMAwE7SIbmH2cgt5ISUE2YAunyAZkBAqYkpSOoAQp/u5k60+G6zEKI+UXP+QpQsQohjkhRe/Z7tFO6nTpmBQKjc1s/NJRj3NbUzJ+UiBzEKXutjHtWevIvnVQ5+IKehbrh4wBIlQe4H8f5sXvBngHywvJnLmcAAAAAElFTkSuQmCC',
			'7470' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZWllDA1pRRFsZpjI0BEx1QBULBYoFBCCLTWF0ZWh0dBBBdl/U0qWrlq7MmobkPkYHkVagWpg6MGRtEA11CEAVA7JbGR0YUOwAsltZGxhQ3AIVQ3XzAIUfFSEW9wEAeJLLh4EblMcAAAAASUVORK5CYII=',
			'5DD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGVqRxQIaRFpZGx2mOqCKNbo2BAQEIIkFBoDEAh1EkNwXNm3aytRVkVnTkN3XiqIOp1hAK6YdIlMw3cIagOnmgQo/KkIs7gMAzuDOLUDfvmUAAAAASUVORK5CYII=',
			'A3B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGVqRxVgDRFpZGx2mIouJTGFodG0ICEUWC2hlAKmD6QU7KWrpqrCloauWIrsPTR0YhoaCzWtFMw+LmAiG3oBWsJtDAwZB+FERYnEfAFAzzXc/KS4cAAAAAElFTkSuQmCC',
			'25FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA1qRxUSmiDSwNjBMdUASC2gFiwUEIOtuFQlhbWB0EEF237SpS5eGrsyahuy+AIZGV4Q6MGR0AIuFhiC7pUEEQx3Q1lZWNLHQUMYQdLGBCj8qQizuAwAqqcpi38ArKQAAAABJRU5ErkJggg==',
			'0E56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHaY6IImxBog0sDYwBAQgiYlMAYkxOgggiQW0AsWmMjoguy9q6dSwpZmZqVlI7gOpY2gIRDEPKuYggmEHqhjILYyODih6QW5mCGVAcfNAhR8VIRb3AQA0mcqnynnbBwAAAABJRU5ErkJggg==',
			'F310' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNZQximMLQiiwU0iLQyhDBMdUARY2h0DGEICEAVa2WYwuggguS+0KhVYaumrcyahuQ+NHVw8xywiqHbAXTLFHS3sIYwhjqguHmgwo+KEIv7AOP+zOifQBidAAAAAElFTkSuQmCC',
			'0985' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2gVaXR0dHR1QHJf1NKlS7NCV0ZFIbkvoJUxEGhcgwiKXgageQEoYiJTWMB2iGC4xSEA2X0QNzNMdRgE4UdFiMV9AHAZyvzOG5TBAAAAAElFTkSuQmCC',
			'94EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYWllDHUMDkMREpjBMZW1gdEBWF9DKEIopxuiKJAZ20rSpS5cuDV0ZmoXkPlZXkVZ0vQytoqGuaGICrQwY6oBuwRDD5uaBCj8qQizuAwDEmsibBVk0lAAAAABJRU5ErkJggg==',
			'41DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpI37pjAEsIYyhoYgi4UwBrA2Ojogq2MMYQ1gbQhEEWMF6UWIgZ00bdqqqKWrIkOzkNwXgKoODENDMcUYsKgDi6G5hWEKayjQzahiAxV+1INY3AcA8NPH/ZzwcqkAAAAASUVORK5CYII=',
			'AF84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGRoCkMRYA0QaGB0dGpHFRKaINLA2BLQiiwW0gtVNCUByX9TSqWGrQldFRSG5D6LO0QFZb2goyLzA0BA084B2NGCxA0OMAc3NAxV+VIRY3AcAO8/OIylYWj4AAAAASUVORK5CYII=',
			'5869' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujY4OoggiQUGsLayNjDCxMBOCpu2Mmzp1FVRYcjuawWqc3SYiqyXoRVkHtBUZDsgYih2iEzBdAtrAKabByr8qAixuA8ANhLMLnE/72QAAAAASUVORK5CYII=',
			'E071' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA1qRxQIaGEOA5FRUMVagmoBQVDGRRodGB5hesJNCo6atzFq6aimy+8DqpjC0YugNQBdjbWV0QBdjDGFtQBUDu7mBITRgEIQfFSEW9wEAda3NB8OFV/kAAAAASUVORK5CYII=',
			'FBC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHaY6IIkFNIi0MjoEBASgijW6Ngg6iKCpY21ghImBnRQaNTVs6apVUWFI7oOoY5gqgmEeQwOmmACGHZhuwXTzQIUfFSEW9wEAz1fNl5BNBEMAAAAASUVORK5CYII=',
			'A06D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxhDGB0dHQKQxESmsLayNjg6iCCJBbSKNLoCTRBBcl/U0mkrU6euzJqG5D6wOkdUvaGhIL2BaOaB7EAXw3RLQCummwcq/KgIsbgPAJKuyznv34mnAAAAAElFTkSuQmCC',
			'CB28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQxhCGaY6IImJtIq0Mjo6BAQgiQU0ijS6NgQ6iCCLAVUCSZg6sJOiVk0NW7Uya2oWkvvA6loZUM1rEGl0mMKIah7QDocAVDGwWxxQ9YLczBoagOLmgQo/KkIs7gMAxTbMmkMsOaIAAAAASUVORK5CYII=',
			'F59F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUNDkMQCGkQaGB0dHRjQxFgbAtHFQpDEwE4KjZq6dGVmZGgWkvsCGhgaHULQ9QLFMM1rdMQQY23FdAtjCNDNKGIDFX5UhFjcBwDJTcsHI6f9ogAAAABJRU5ErkJggg==',
			'5A36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGaY6IIkFNDCGsDY6BASgiLG2MjQEOgggiQUGiDQ6NDo6ILsvbNq0lVlTV6ZmIbuvFawOxTyGVtFQB6B5Ish2gNShiYlMEWl0RXMLK9BeRzQ3D1T4URFicR8AxEHNpjDBrS8AAAAASUVORK5CYII=',
			'800A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGWY6oAkFtDK2sro6BAQgKJOpNG1IdBBBMl9S6OmrUxdFZk1Dcl9aOqg5oHFQkMw7HBEUQdxCyOKGMTNqGIDFX5UhFjcBwAoJss51GYIiAAAAABJRU5ErkJggg==',
			'EAB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGaY6IIkFNDCGsDY6BASgiLG2sjYEOoigiIk0uiLUgZ0UGjVtZWroqqlZSO5DUwcVEw11xWYefjugbgaKobl5oMKPihCL+wBrHM8Uli9rUwAAAABJRU5ErkJggg==',
			'5F65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMDkMQCGkQaGB0dHRjQxFgbUMUCA0BijK4OSO4LmzY1bOnUlVFRyO5rBapzdACagKQbJAY2FckOsFigA7KYyBSQWxwCkN3HCrSXIZRhqsMgCD8qQizuAwBbkcuRjHIUfQAAAABJRU5ErkJggg==',
			'1C53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHUIdkMRYHVgbXYEyAUhiog4iDa5AWgRFr0gD61SGhgAk963MmrZqaWbW0iwk94HUgVQFoOkFiaGb54ohxtro6OiI6pYQxlAGIER280CFHxUhFvcBAP7OynyC93L3AAAAAElFTkSuQmCC',
			'A08C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjCGMDo6BIggiYlMYW1lbQh0YEESC2gVaXR0dHRAdl/U0mkrs0JXZiG7D00dGIaGijS6As1jQDEPmx2YbgloxXTzQIUfFSEW9wEAUeXLA4B0Hb4AAAAASUVORK5CYII=',
			'49DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37pjCGsIYyhjogi4WwtrI2OjoEIIkxhog0ujYEOoggibFOgYgFILlv2rSlS1NXRYZmIbkvYApjIJI6MAwNZcAwj2EKCxYxTLdgdfNAhR/1IBb3AQBMP8xgpSrIJAAAAABJRU5ErkJggg==',
			'097C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM3QwQ2AIAxA0TaBDRioblASvTgCU5QDG+AIHHRK0VMbPWqU3n5SeAG2yxH407ziQ8LRT7ywap59AWEOqoUaMkkkpxqX3vJA2je31lJbk/ZxwUgVCcwuZGLbQnX9NjRvHBYvYCynWcCYv/q/B+fGtwP1vMr0hCisBAAAAABJRU5ErkJggg==',
			'5D3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhDGUNDkMQCGkRaWRsdHRhQxRodGgJRxAIDgGIIdWAnhU2btjJr6srQLGT3taKoQ4ihmReARUxkCqZbWAPAbkY1b4DCj4oQi/sA2pTLrEQcLKQAAAAASUVORK5CYII=',
			'13A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1YQximMLQii7E6iLQyhDJMRRYTdWBodHR0CEXVy9DK2hAA0wt20sqsVWFLV0UtRXYfmjqYWKNrKBYxDHUiGHpFQ1hDgGKhAYMg/KgIsbgPAP3Lyfuo1xFcAAAAAElFTkSuQmCC',
			'C2EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHUNDkMREWllbWRsYHZDVBTSKNLqiizUwIIuBnRS1atXSpaErQ7OQ3AdUNwXDvAaGAEw7GB3QxYBuaUAXYw0RDXUNdUQRG6jwoyLE4j4AyCnJSO5DCogAAAAASUVORK5CYII=',
			'E45A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYWllDHVqRxQIaGKayArEDqlgoUCwgAEWM0ZV1KqODCJL7QqOWLl2amZk1Dcl9AQ0iQPMDYeqgYqKhDg2BoSGodrSyYqhjaGV0dEQRA7mZIZQRRWygwo+KEIv7AD1XzBtKllM+AAAAAElFTkSuQmCC',
			'F7EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUMDkMSA7EbXBkYHBsJirawIMbCTQqNWTVsaujI0C8l9QHUBrBh6GR0wxVgbMMVEsIuhuXmgwo+KEIv7AF36yp8CytyQAAAAAElFTkSuQmCC',
			'7BC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHVpRRFtFWhkdAqY6oIo1ujYIBAQgi00RaWVtYHQQQXZf1NSwpatWZk1Dch9QBbI6MGRtAJmHKibSgGlHQAOmWwIasLh5gMKPihCL+wD16Mwh781+UAAAAABJRU5ErkJggg==',
			'7257' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUNDkEVbWVtZgbQIiphIoyu62BSGRtepDA0ByO6LWrV0aWbWyiwk9zE6AFU2BLQi2ws0PwAoNgVZTASokrUhIABZLACoktHR0QFVTDTUIZQRRWygwo+KEIv7AE3yy0vB/dHzAAAAAElFTkSuQmCC',
			'EDF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA6Y6IIkFNIi0sjYwBASgijW6NjA6CGARQ3ZfaNS0lamhK1OzkNwHVYfVPBHCYhhuAbu5gQHFzQMVflSEWNwHAHZVzTMK4AiVAAAAAElFTkSuQmCC',
			'1AC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHUMDkMRYHRhDGB0CHZDViTqwtrI2CDqg6hVpdG1gdHVAct/KrGkrU1etjIpCch9EHUODCIpe0VBMMZA6QQd0MUeHgABk94mGiDQ6hDpMdRgE4UdFiMV9ANvJyTkO/QxcAAAAAElFTkSuQmCC',
			'9DC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCHVqRxUSmiLQyOgRMRRYLaBVpdG0QCMUUY4DpBTtp2tRpK1NXrVqK7D5WVxR1ENiKKSYAsQObW1DEoG4ODRgE4UdFiMV9AMr1zKTcyWvDAAAAAElFTkSuQmCC',
			'5C14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMDQEIIkFNLA2OoQwNKKKiTQ4hjC0IosFBog0APVOCUByX9i0aatWTVsVFYXsvlaQOkYHZL1QsdAQZDuAYg5obhGZAnQLmhhrAGMoY6gDithAhR8VIRb3AQDAcM4zwIXt9AAAAABJRU5ErkJggg==',
			'F6D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUNDkMQCGlhbWRsdGkRQxEQaWUEkqlgDSCwAyX2hUdPClq6KWpmF5L6ABtFWoLpWBjTzXBsCpmARC2DAcIujA6oY2M0oYgMVflSEWNwHAIMnze1nJg3mAAAAAElFTkSuQmCC',
			'FBB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGaY6IIkFNIi0sjY6BASgijW6NgQ6iOBWB3ZSaNTUsKWhq6ZmIbmPBPMI2QEVw3TzQIUfFSEW9wEAbS7O25Gi/EkAAAAASUVORK5CYII=',
			'383F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7RAMYQxhDGUNDkMQCprC2sjY6OqCobBVpdGgIRBUDqmNAqAM7aWXUyrBVU1eGZiG7D1UdbvOwiGFzC9TNqHoHKPyoCLG4DwAWxMpqJ1QhSwAAAABJRU5ErkJggg==',
			'5838' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGaY6IIkFNLC2sjY6BASgiIk0OjQEOoggiQUGsLYyINSBnRQ2bWXYqqmrpmYhu68VRR1UDNO8ACxiIlMw3cIagOnmgQo/KkIs7gMA/8jNiK9poYgAAAAASUVORK5CYII=',
			'D1A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIaGIIkFTGEMYAhlaBBBFmtlDWB0dEATYwhgbQgAQoT7opaC0cosJPdB1bUyoOsNDZiCIdYQEIAiNgUkFuiA6mbWUHSxgQo/KkIs7gMA5uHL8DflSLwAAAAASUVORK5CYII=',
			'A13F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUNDkMRYAxgDWBsdHZDViUxhDWBoCEQRC2hlCGBAqAM7KWrpqqhVU1eGZiG5D00dGIaGMmA3D4sYulsCWlmBLmZEERuo8KMixOI+AK+XyMJifryOAAAAAElFTkSuQmCC',
			'7352' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AQAgA+YINfiAs7CkeCzfQKfiCDXQEm59SOvloqYlQEC4BLkC7hcKf8hM/ESwotFOklg0VmDsGddREObINDHfQHP3mNh3L6uXyS+TTyjXe8P3es0UX3+M3eIuMNVsaiHuGBSRJ+cH/XswHvxPXA8v6m6m4bAAAAABJRU5ErkJggg==',
			'8E14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQxmmMDQEIImJTBFpYAhhaEQWC2gVaWAMYWjFUDeFYUoAkvuWRk0NWzVtVVQUkvsg6hgd0M0DioWGYIhhcQuaGMjNjKEOKGIDFX5UhFjcBwBTA8048fvKDgAAAABJRU5ErkJggg==',
			'72E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHVpRRFtZW1kbGKY6oIiJNLo2MAQEIItNYQCKMTqIILsvatXSpaErs6YhuY/RgWEKK0IdGALND0AXEwHyWdHsCACqRHdLQINoqCu6mwco/KgIsbgPAL3syxZ0VqdDAAAAAElFTkSuQmCC',
			'AC9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGVqRxVgDWBsdHR2mOiCJiUwRaXBtCAgIQBILaBVpYG0IdBBBcl/U0mmrVmZGZk1Dch9IHUMIXB0YhoaCeIGhIWjmOTagqgtoBbnFEU0M5GZGFLGBCj8qQizuAwB/6syMZQBbUgAAAABJRU5ErkJggg==',
			'8285' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsRGEQAhFIaAD7AcCc5yRZKvBwA7WEgzcKt2Q9S68m5Gf/fnAmw/tYwLepL/wkeEKjm7J40o7qkrO2c7bHMvgcYVNVWdJfGdpZ/OrlMTXcxVVgod7YBT28FCo/+CRJfquZT6yycXhkBf090N94bsB7QfLWFCiggIAAAAASUVORK5CYII=',
			'24C6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHaY6IImJTGGYyugQEBCAJBYAVMXaIOgggKy7ldGVtYHRAcV905YuXbpqZWoWsvsCRFqB6lDMY3QQDXUFkiLIbgGaCLIDWUwEZAuaW0JDMd08UOFHRYjFfQAh/cqacX/NlAAAAABJRU5ErkJggg==',
			'FB26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6CKCpYwCKIbsvNGpq2KqVmalZSO4Dq2tlxDDPYQqjgwi6WACGWCujAwOaXtEQ1tAAFDcPVPhREWJxHwALcszmoMCvYwAAAABJRU5ErkJggg==',
			'5329' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGaY6IIkFNIi0Mjo6BASgiDE0ujYEOoggiQUGMLQyIMTATgqbtips1cqsqDBk97WC4VRkvUB+o8MUhgZksQCQWAADih0iU4BucWBAcQtrAGsIa2gAipsHKvyoCLG4DwCTUsuQKGsq/gAAAABJRU5ErkJggg==',
			'CBC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQxhCHVqRxURaRVoZHQKmIosFNIo0ujYIhKKIAVWyNjDA9IKdFLVqatjSVauWIrsPTR1MDGgemhjEDmxuQRGDujk0YBCEHxUhFvcBAPVCzN/iFqqzAAAAAElFTkSuQmCC',
			'5DBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGUMdkMQCGkRaWRsdHQJQxRpdGwIdRJDEAgOAYkB1IkjuC5s2bWVq6Mqsacjua0VRhxBDMy8Ai5jIFEy3sAZgunmgwo+KEIv7AGl4zQ586zH5AAAAAElFTkSuQmCC',
			'082D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbSytjIgxMBOilq6MmzVysysaUjuA6trZUTTK9LoMAVVDGSHQwCqGNgtDowobgG5mTU0EMXNAxV+VIRY3AcAm47KGLtbdAUAAAAASUVORK5CYII=',
			'AA9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjCGMDo6BIggiYlMYW1lbQh0YEESC2gVaXQFiiG7L2rptJWZmZFZyO4DqXMIgasDw9BQ0VCHBlQxkDpHLHY4orkFbB6amwcq/KgIsbgPAKeOzFc1wT4AAAAAAElFTkSuQmCC',
			'0613' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQximMIQ6IImxBrC2MoQwOgQgiYlMEWkEqmwQQRILaAXypgBpJPdFLZ0WtmraqqVZSO4LaBVtRVIH09voMAXVPJAd6GJgt0xBdQvIzYyhDihuHqjwoyLE4j4AXajLtDP9dh8AAAAASUVORK5CYII=',
			'2192' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2Quw2AMAwF7SIbZCCzwUOKm2zAFqHIBoEdyJQ4nRGUIOHXnfw5mfqtCv0pn/gFEEhpE8diY/AkgGOoAaHMEv10JWMo0fvtPR9L7tn7jRsJq7/BYsy2XlyskwuaZ3Ewc/FMNSgpa/rB/17Mg98JtHDJWS80DbUAAAAASUVORK5CYII=',
			'8834' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGRoCkMREprC2sjY6NCKLBbSKNDoASXR1DI0OUwKQ3Lc0amXYqqmroqKQ3AdR5+iAaV5gaAimHdjcgiKGzc0DFX5UhFjcBwDXlM8vjxpBkgAAAABJRU5ErkJggg==',
			'A6E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHRoCkMRYA1hbWRsYGpHFRKaINALFWpHFAlpFGoBiUwKQ3Be1dFrY0tBVUVFI7gtoFQWax+iArDc0VKTRtYExNATVPKAYQwOqHWC3oIlhunmgwo+KEIv7AI4hzaYlblDeAAAAAElFTkSuQmCC',
			'590F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIaGIIkFNLC2MoQyOjCgiIk0Ojo6oogFBog0ujYEwsTATgqbtnRp6qrI0Cxk97UyBiKpg4oxNKKLBbSyYNghMgXTLawBYDejmjdA4UdFiMV9AL2JyfXcBA8BAAAAAElFTkSuQmCC',
			'2071' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ3AIAwETeENGIgRvsBLZApTsAFhA4owZZCiSFhJmUj4u5NfPpn6Y5RWyi9+DAIL8sx8cZEU+8yQeexATDv7FFK4u5dTrcfWejN+GHuFzA0XBoNlrJxdsMyri6yWiQxnJcEC//swL34nQOjLQbXJIrYAAAAASUVORK5CYII=',
			'F6A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIkFNLC2MoQyOjCgiIk0Mjo6oos1sDYEujoguS80alrY0lWRUVFI7gtoEG1lBatGNc81FItYQ6CDCJpbgHoDUN3HGAIUm+owCMKPihCL+wAkOc11JlcFoAAAAABJRU5ErkJggg==',
			'6AB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaY6IImJTGEMYW10CAhAEgtoYW1lbQh0EEEWaxBpdEWoAzspMmraytTQVVOzkNwXMgVFHURvq2ioK7p5rUB1aGIiWPSyBgDF0Nw8UOFHRYjFfQBkg85GQdgYuAAAAABJRU5ErkJggg==',
			'4BEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGsIY6TA1AFgsRaWVtYAgQQRJjDBFpdG1gdGBBEmOdAlLH6IDsvmnTpoYtDV2Zhey+AFR1YBgaCjEP1S2YdjBMwXQLVjcPVPhRD2JxHwAasMqkdX7eGQAAAABJRU5ErkJggg==',
			'FB7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0MDkMQCGkRaGRoCHRhQxRodMMVaGRodYWJgJ4VGTQ1btXRlaBaS+8DqpjBimheAKebogCHWytqALgZ0cwMjipsHKvyoCLG4DwDpFsvIjqMs5gAAAABJRU5ErkJggg=='        
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