<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "wenothe@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "6be97f" );

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
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
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
    if( !phpfmg_user_isLogin() ){
        exit;
    };

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
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

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

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

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
			'628E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gRaXRtCEQVa2BodESoAzspMmrV0lWhK0OzkNwXMoVhCoZ5rQwBrOjmtTI6oIsB3dKArpc1QDTUAc3NAxV+VIRY3AcAZK3J8W1me8gAAAAASUVORK5CYII=',
			'3EDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RANEQ1lDGUMdkMQCpog0sDY6OgQgq2wFijUEOoggi01BEQM7aWXU1LClqyKzpiG7bwoWvdjMwyKGzS3Y3DxQ4UdFiMV9ADSty2rrbrcUAAAAAElFTkSuQmCC',
			'3C96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYQxlCGaY6IIkFTGFtdHR0CAhAVtkq0uDaEOgggCw2RaSBFSiG7L6VUdNWrcyMTM1Cdh9QHUNIIIZ5DEC9Imhijmhi2NyCzc0DFX5UhFjcBwAAkswR0WHKywAAAABJRU5ErkJggg==',
			'CA85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUMDkMREWhlDGB0dHZDVBTSytrI2BKKKNYg0Ojo6ujoguS9q1bSVWaEro6KQ3AdR59AggqJXNNQVJINih0ijK9AOERS3gPUGILuPNUSk0SGUYarDIAg/KkIs7gMA5XbMc2THTGwAAAAASUVORK5CYII=',
			'BE8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMdUAWaxVpYG0ICAjAUOfoIILkvtCoqWGrQldmTUNyH5o6JPMCQ0MwxVDVYdELcTMjithAhR8VIRb3AQB4AsxE0J3WrAAAAABJRU5ErkJggg==',
			'9474' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ6AIBAEl4LeAv9DQ3+FNLzmLO4H4A9oeKXR6kBLjd4mW0yyyeTQLsf4U17xswSxkZgUcxkFTKtmJIhH98wErD6T8ttKra22lJSfDU6QjddbyBw9mbgoNgnEeIwuYrlnp/PAvvrfg7nx2wHPhs07DH64TgAAAABJRU5ErkJggg==',
			'B450' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYWllDHVqRxQKmMExlbWCY6oAs1soQChQLCEBRx+jKOpXRQQTJfaFRS5cuzczMmobkvoApIkDzA2HqoOaJhjpgiAHd0hCAZgdDK6OjA4pbQG5mCGVAcfNAhR8VIRb3AQBBDM0YagN+GQAAAABJRU5ErkJggg==',
			'17C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHaY6IImxOjA0OjoEBAQgiYkCxVwbBB1EUPQytLICaREk963MWjVtKZCOQnIfUF0AUF2jA4peRgegWCuqW1gbWBsEpqCKiQBxQACymGgI0MZQx9CQQRB+VIRY3AcAxL3JSs8u+RAAAAAASUVORK5CYII=',
			'9AF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDAxoCkMREpjCGsDYwNCKLBbSytgLFWlHFRBpdGximBCC5b9rUaStTQ1dFRSG5j9UVpI7RAVkvQ6toKFAsNARJTABiHppbMMVYAzDFBir8qAixuA8A0RvNrlJ3pKIAAAAASUVORK5CYII=',
			'7D9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMdkEVbRVoZHR0dAlDFGl0bAh1EkMWmQMQCkN0XNW1lZmZkaBaS+xgdRBodQgJRzGNtAIqhmScCFHNEEwtowHRLQAMWNw9Q+FERYnEfAMBFy/NTaYZgAAAAAElFTkSuQmCC',
			'FC45' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQxkaHUMDkMQCGlgbHVodHRhQxEQaHKZiijEEOro6ILkvNGraqpWZmVFRSO4DqQOaCFKNopc1NABDzKHR0UEE3S2NDgGo7gO52WGqwyAIPypCLO4DALjVzm4s5JyJAAAAAElFTkSuQmCC',
			'F5F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA0NDkMQCGkQaWIG0CGGxEFYwjXBfaNTUpUtDV63MQnIfUL7RtYGhlQFFL1hsCqqYCEgsAFWMtZW1gdEBVYwxBF1soMKPihCL+wADgsykW/Gv4wAAAABJRU5ErkJggg==',
			'DAB7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUNDkMQCpjCGsDY6NIggi7WytrI2BKCJiTS6AtUFILkvaum0lamhq1ZmIbkPqq6VAUWvaKgr0CYGdPMaAgJQxKaA9Do6oLoZKBbKiCI2UOFHRYjFfQC8qs8V8Vi0DAAAAABJRU5ErkJggg==',
			'5637' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUNDkMQCGlhbWRsdGkRQxEQawSSSWGAAkAdUF4DkvrBp08JWTV21MgvZfa2irUB1rSg2t4qAdE5BFguAiAUgi4lMAbnF0QFZjDUA7GYUsYEKPypCLO4DAE2pzMemi0jQAAAAAElFTkSuQmCC',
			'2A7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA1qRxUSmMIYwNARMdUASC2hlBaoJCAhA1t0q0ujQ6Oggguy+adNWZi1dmTUN2X0BQHVTGGHqwJDRQTTUIYAxNATZLQ0iQNNQ1YkAxVwbUMVCQzHFBir8qAixuA8AwSfLtq6btB8AAAAASUVORK5CYII=',
			'58B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaY6IIkFNLC2sjY6BASgiIk0ujYEOoggiQUGoKgDOyls2sqwpaGrpmYhu68V0zyGVkzzArCIiUzB1MsagOnmgQo/KkIs7gMA0CTNZJ+tSqQAAAAASUVORK5CYII=',
			'FE96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIkFNIg0MDo6BASgibE2BDoIYBFDdl9o1NSwlZmRqVlI7gOpYwgJxDCPAahXBN1ebGIYbsF080CFHxUhFvcBAIoUzI1K5wmOAAAAAElFTkSuQmCC',
			'CD92' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WENEQxhCGaY6IImJtIq0Mjo6BAQgiQU0ijS6NgQ6iCCLNYDEgCSS+6JWTVuZmRkFpBHuA6lzCAlodEDT69AQ0MqAZodjQ8AUBixuwXQzY2jIIAg/KkIs7gMAHorNsCzX2RQAAAAASUVORK5CYII=',
			'DFCD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHUMdkMQCpog0MDoEOgQgi7WKNLA2CDqIYIgxwsTATopaOjVs6aqVWdOQ3IemjoAYmh1Y3BIaAFSB5uaBCj8qQizuAwDcesyu/uD3UwAAAABJRU5ErkJggg==',
			'29C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHaY6IImJTGFtZXQICAhAEgtoFWl0bRB0EEHWDRZjhIlB3DRt6dLUVauiwpDdF8AY6NrAMBVZL6MDA1Av0C5ktzSwAMUEUOwQacB0S2goppsHKvyoCLG4DwBnNMuTAPvL0wAAAABJRU5ErkJggg==',
			'A440' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YWhkaHVqRxVgDGKYytDpMdUASE5nCEMow1SEgAEksoJXRlSHQ0UEEyX1RS5cuXZmZmTUNyX0BrSKtrI1wdWAYGioa6hoaiCIW0Ap2C5odYDEUt0DFUNw8UOFHRYjFfQALM805QqxY+AAAAABJRU5ErkJggg==',
			'7F3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQx1DGVpRRFtFGlgbHaY6oIkxNAQEBCCLTQGKNTo6iCC7L2pq2KqpK7OmIbmP0QFFHRiyNoB4gaEhSGIiEDEUdQENILc4YogxhjKiiA1U+FERYnEfAD4RzCXdcKSJAAAAAElFTkSuQmCC',
			'1B72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6Y6IImxOoi0MjQEBAQgiYk6iDQ6NAQ6iKDoBaoDiooguW9l1tSwVUtXrYpCch9Y3RSQShS9jQ4BDK0MaGKODkCVaHawNjAEIIuJhgDd3MAYGjIIwo+KEIv7AKocyfDIAwlfAAAAAElFTkSuQmCC',
			'E150' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHVqRxQIaGANYGximOqCIsYLEAgJQxIB6pzI6iCC5LzRqVdTSzMysaUjuA6ljaAiEqcMrxtoQgGEHo6MDiltCQ1hDGUIZUNw8UOFHRYjFfQB2q8rU1uwIIQAAAABJRU5ErkJggg==',
			'2876' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZWgICAhAEgtoFWl0aAh0EEDW3QpU1+jogOK+aSvDVi1dmZqF7L4AoLopjCjmMToAzQsAkshuaRABmoYqJtLA2soKNAFZb2go0M0NDChuHqjwoyLE4j4AUrzLTuSnTdwAAAAASUVORK5CYII=',
			'24FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYWllDA0MdkMREpjBMZW1gdAhAEgtoZQgFiYkg625ldEUSg7hp2tKlS0NXZk1Ddl+ASCu6XkYH0VBXNDFWoIno6kSgYshuCQ0Fi6G4eaDCj4oQi/sAxjTJc+Zux9gAAAAASUVORK5CYII=',
			'BF2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUNDkMQCpog0MDo6OiCrC2gVaWBtCEQVA6pjQIiBnRQaNTVs1crM0Cwk94HVtTJimMcwBYtYACOGHYwOqGKhAUC3hKK6ZaDCj4oQi/sAYujKhd2PT5QAAAAASUVORK5CYII=',
			'FD03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNFQximMIQ6IIkFNIi0MoQyOgSgijU6Ojo0iKCJuQLJACT3hUZNW5m6KmppFpL70NShiKGbh8UOLG7BdPNAhR8VIRb3AQClxs8FaODMSAAAAABJRU5ErkJggg==',
			'1020' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgdGEMYHR2mOiCJiTqwtrI2BAQEoOgVaXRoCHQQQXLfyqxpK7NWZmZNQ3IfWF0rI0wdQmwKuhhrK9A1aHYA3eLAgOqWEIYA1tAAFDcPVPhREWJxHwBIb8hC8boDqAAAAABJRU5ErkJggg==',
			'FFCC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHaYGIIkFNIg0MDoEBIigibE2CDqwYIgxOiC7LzRqatjSVSuzkN2Hpo6AGKYd2NzCgObmgQo/KkIs7gMATvvMSY2TrCsAAAAASUVORK5CYII=',
			'42A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2Quw2AMAwFncIbhH2SIr2R4oZpnCIbGDagyZSECvMpQcKvOz3ZJ0O7jcCf8o2fugwKc7AsYwUGIsNc9iXGGLxhqFCSkHjjtyxtXdvUc/iRgqJQsTe470emenEJuLdPDKUzOrOBk4yc//C/9/LgtwGqwszM5VEMggAAAABJRU5ErkJggg==',
			'B3AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNYQximMEwNQBILmCLSyhDKECCCLNbK0Ojo6OjAgqKOoZW1IdAB2X2hUavClq6KzEJ2H5o6uHmuoVjEgOpQ7RAB6g1AcQvIzUAxFDcPVPhREWJxHwBb6c1djr75wQAAAABJRU5ErkJggg==',
			'4E23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37poiGMgChA7JYiEgDo6OjQwCSGCNQjLUhoEEESYx1CogX0BCA5L5p06aGrVqZtTQLyX0BIHWtDA3I5oWGAsWmMKCYxwBSF4ApxujAiOIWkJtZQwNQ3TxQ4Uc9iMV9AHSzy6bLh09pAAAAAElFTkSuQmCC',
			'A62D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSCeHAxsJOilk4LW7UyM2sakvsCWkVbGVoZUfSGhoo0OkxhRDev0SEAXQzoFgdGFLcEtDKGsIYGorh5oMKPihCL+wBMk8r1rz1FAwAAAABJRU5ErkJggg==',
			'9364' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANYQxhCGRoCkMREpoi0Mjo6NCKLBbQyNLo2OLSiibWyNjBMCUBy37Spq8KWTl0VFYXkPlZXoDpHRwdkvQxg8wJDQ5DEBMBiAdjcgiKGzc0DFX5UhFjcBwDGjs103EVBMQAAAABJRU5ErkJggg==',
			'A62E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaWRtCEQRC2gVAZJwMbCTopZOC1u1MjM0C8l9Aa2irQytjCh6Q0NFGh2mMKKb1+gQgC4GdIsDuhhjCGtoIIqbByr8qAixuA8A4PLJ0EgfqvEAAAAASUVORK5CYII=',
			'CC1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQxmmMLQii4m0sjY6hDBMdUASC2gUaXAMYQgIQBZrEGlgmMLoIILkvqhV01atmrYyaxqS+9DUIYuFhqDZ4YCmDuwWNDGQmxlDHVHEBir8qAixuA8AJKHMDl0iXpgAAAAASUVORK5CYII=',
			'DB03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQximMIQ6IIkFTBFpZQhldAhAFmsVaXR0dGgQQRVrZW0IaAhAcl/U0qlhS4FkFpL70NTBzXMFiogQsgOLW7C5eaDCj4oQi/sAe7jO2ojL3ikAAAAASUVORK5CYII=',
			'4479' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37pjC0soYGTHVAFgthmMrQEBAQgCTGGMIQytAQ6CCCJMY6hdGVodERJgZ20rRpS5euWroqKgzJfQFTRFqB9kxF1hsaKhrqEMDQIILmFkYHBgd0MdYGBhS3QMVQ3TxQ4Uc9iMV9AEy9y3AALY3AAAAAAElFTkSuQmCC',
			'9EF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDAxoCkMREpog0sDYwNCKLBbSCxVqxiE0JQHLftKlTw5aGroqKQnIfqytIHaMDsl4GsF7G0BAkMQGIedjcgiIGdjOa2ECFHxUhFvcBAKrszHhViyM7AAAAAElFTkSuQmCC',
			'4634' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM3QzQ2AIAyA0XJgA9wHN6hJ8cA07YENKkMwpT+noh412t6+QPJSaJdh+NO+41NHLgGjbeSLlyi2OQqyvSq2eQ0MEhWNr9Y6t6XlbHyoQwEZo/2bUpDIU6LOsjfsLXpYTu3G/NX9ntsb3wrLaM56RYZ+owAAAABJRU5ErkJggg==',
			'8FA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMLQii4lMEWlgCGWYiiwW0CrSwOjoEIqujhUog+y+pVFTw5auilqK7D40dXDzWEOxiKGpw6aXNQAsFhowCMKPihCL+wAR9c0rj7yv5QAAAABJRU5ErkJggg==',
			'B0F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDAxoCkMQCpjCGsDYwNKKItbK2AsVaUdWJNLo2MEwJQHJfaNS0lamhq6KikNwHUcfogGoeWCw0BNMObG5BEQO7GU1soMKPihCL+wDwI85e3GtFBQAAAABJRU5ErkJggg==',
			'65FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0NDkMREpog0sDYwOiCrC2jBItYgEoIkBnZSZNTUpUtDV4ZmIbkvZApDoyu63lZsYiIYYiJTWFvR7WUNYAxBFxuo8KMixOI+APb3yX6oEEMnAAAAAElFTkSuQmCC',
			'93C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANYQxhCHaY6IImJTBFpZXQICAhAEgtoZWh0bRB0EEEVa2VtYISJgZ00beqqsKWrVkWFIbmP1RWkjmEqsl4GsHlAu5DEBMBiAih2YHMLNjcPVPhREWJxHwBcZsuCKrYktgAAAABJRU5ErkJggg==',
			'BCA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMEx1QBILmMLa6BDKEBCALNYq0uDo6OgggKJOpIG1IdAB2X2hUdNWLV0VmZqF5D6oOgzzWEMDHUTQxFwb0MSAbnFtCEDRC3Iza0MAipsHKvyoCLG4DwD67M6rEGT/BQAAAABJRU5ErkJggg==',
			'30C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHaY6IIkFTGEMYXQICAhAVtnK2sraIOgggiw2RaTRtYEBpg7spJVR01amrlo1NQvZfajqoOaBxBhRzcNiBza3YHPzQIUfFSEW9wEAdobLmSFB7pEAAAAASUVORK5CYII=',
			'D124' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGRoCkMQCpjAGMDo6NKKItbIGsAJJVDGgXqDqACT3RS1dFbVqZVZUFJL7wOpaGR0w9E5hDA1BFwtAdwtDAKMDqlhoAGsoa2gAithAhR8VIRb3AQDhXsyvL9yyzQAAAABJRU5ErkJggg==',
			'BA9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaYGIIkFTGEMYXR0CBBBFmtlbWVtCHRgQVEn0ugKFEN2X2jUtJWZmZFZyO4DqXMIgauDmica6tCALibS6IjFDkc0t4QGAM1Dc/NAhR8VIRb3AQA6Ps1PNp3HGAAAAABJRU5ErkJggg==',
			'56CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHaYGIIkFNLC2MjoEBIigiIk0sjYIOrAgiQUCVbA2MDoguy9s2rSwpatWZqG4r1W0FUkdVEyk0RVNLAAshmqHyBRMt7AGYLp5oMKPihCL+wDUyMsEyp5x7AAAAABJRU5ErkJggg==',
			'6698' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoEWlkbQh0EEEWaxBpYG0IgKkDOykyalrYysyoqVlI7guZItrKEBKAal6rSKMDunlAMUc0MWxuwebmgQo/KkIs7gMAbDvMdpusfgoAAAAASUVORK5CYII=',
			'EFF9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA6Y6IIkFNIg0sDYwBARgiDE6iOAWAzspNGpq2NLQVVFhSO6DmjcVUy9DAxYxLHaguiU0BGIespsHKvyoCLG4DwCUtcx8lVOjJwAAAABJRU5ErkJggg==',
			'F072' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA6Y6IIkFNDCGAMmAABQx1laGhkAHERQxkUaHRocGEST3hUZNW5m1dNWqKCT3gdVNYWh0QNcbwNDKgGYHowPDFAY0t7A2MASgigHd3MAYGjIIwo+KEIv7AAdnzWl6Ub2kAAAAAElFTkSuQmCC',
			'6B7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA0MdkMREpoi0MjQEOgQgiQW0iDQ6AMVEkMUagOoaHWHqwE6KjJoatmrpytAsJPeFgMybwohqXivQvABGVPOAYo4OqGIgt7A2oOoFu7mBEcXNAxV+VIRY3AcAbvzMLiEM+JoAAAAASUVORK5CYII=',
			'B5FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA0NDkMQCpog0sDYwOiCrC2jFIjZFJARJDOyk0KipS5eGrgzNQnJfwBSGRlcM87CJiWCKTWFtRbc3NIAxBF1soMKPihCL+wBsXsqjaWgENQAAAABJRU5ErkJggg==',
			'917C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6YGIImJTGEMYGgICBBBEgtoBapsCHRgQRFjCGBodHRAdt+0qauiVi1dmYXsPlZXoLopjA4oNoP0BqCKCbSCRBhR7BCZAnRfAwOKW1iBLgaKobh5oMKPihCL+wAD28iHt9XBpgAAAABJRU5ErkJggg==',
			'4557' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37poiGsoY6hoYgi4WINLACaREkMUYsYqxTREJYpzI0BCC5b9q0qUuXZmatzEJyX8AUhkaHhoBWZHtDQ8FiU1DdItLo2hAQgCrG2sro6OiAKsYYwhDKiCo2UOFHPYjFfQCdN8ujzMyu6QAAAABJRU5ErkJggg==',
			'96EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUNDkMREprC2sjYwOiCrC2gVacQi1oAkBnbStKnTwpaGrgzNQnIfq6sohnkMQPNc0cQEsIhhcwvUzajmDVD4URFicR8AwlXIgBl2N0YAAAAASUVORK5CYII=',
			'ED0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQximMLQiiwU0iLQyhDJMdUAVa3R0dAgIQBNzbQh0EEFyX2jUtJWpqyKzpiG5D00dslhoCIYdjujqgG5hRBGDuBlVbKDCj4oQi/sA9VPNcu6fy7EAAAAASUVORK5CYII=',
			'270B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTGFodAhldAhAEgtoZWh0dHR0EEHW3crQytoQCFMHcdO0VdOWrooMzUJ2XwBDAJI6MGR0YHQAiSGbxwqEjGh2iAAhA5pbQkOBYmhuHqjwoyLE4j4AVUfKkBHVA68AAAAASUVORK5CYII=',
			'2441' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYWhkaHVqRxUSmMExlaHWYiiwW0MoQyjDVIRRFdyujK0MgXC/ETdOWLl2ZmbUUxX0BIq2saHYwOoiGuoYGoIixNmBxCxax0FCwWGjAIAg/KkIs7gMA+JnMHovyw+YAAAAASUVORK5CYII=',
			'E43B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYWhlDGUMdkMQCGhimsjY6OgSgioUyNAQ6iKCIMboyINSBnRQatXTpqqkrQ7OQ3BfQINLKgGGeKNBOdPMYWjHtYGhFdws2Nw9U+FERYnEfADOYzQ3U0NLPAAAAAElFTkSuQmCC',
			'A7BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGUMdkMRYAxgaXRsdHQKQxESmAMUaAh1EkMQCWhlaWRHqwE6KWrpq2tLQlaFZSO4DqgtgRTMvNJTRgRXDPNYGTDGRBnS9YDE0Nw9U+FERYnEfAJB2zKZOLrVkAAAAAElFTkSuQmCC',
			'0690' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mOiCJiUwRaWRtCAgIQBILaBVpYG0IdBBBcl/U0mlhKzMjs6YhuS+gVbSVIQSuDqa30aEBVQxkhyOaHdjcgs3NAxV+VIRY3AcAAcfLV9gyCgMAAAAASUVORK5CYII=',
			'37C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RANEQx1CHVqRxQKmMDQ6OgRMdUBW2crQ6NogEBCALDaFoZW1gdFBBMl9K6NWTVu6amXWNGT3TWEIQFIHNY/RAVOMtYEVzY6AKSJAVahuEQ0A6kJz80CFHxUhFvcBAMWXy7bzkuE3AAAAAElFTkSuQmCC',
			'F95E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMDkMQCGlhbWRsYHRhQxEQaXbGJTYWLgZ0UGrV0aWpmZmgWkvsCGhgDHRoC0fQyNGKKsQDtQBdjbWV0dEQTYwxhCGVEcfNAhR8VIRb3AQBO08uBMUndSQAAAABJRU5ErkJggg==',
			'596C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaYGIIkFNLC2Mjo6BIigiIk0ujY4OrAgiQUGgMQYHZDdFzZt6dLUqSuzUNzXyhjo6ujogGJzKwNQbyCKWEArC1gM2Q6RKZhuYQ3AdPNAhR8VIRb3AQCDKcuFfiOV/AAAAABJRU5ErkJggg==',
			'5952' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAfElEQVR4nM2QsQ3DMAwEScDcQAPRRfoXIKVI78KZgi64gZwN3HjKqGQQlzZgfvXX/IG0/53RnXKJXy1cpOqqgcHExQj4YWl5GGsKLKOzlSwFv+dn26b5vb+in3NWwxI3yKl3eHSBD30DLbLUxHlURCbgQpVrucH/TsyB3xclpsy/BBDDdgAAAABJRU5ErkJggg==',
			'6C5C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHaYGIImJTGFtdG1gCBBBEgtoEWlwbWB0YEEWaxBpYJ3K6IDsvsioaauWZmZmIbsvZArIpEAHZHsDWrGLuQLFkO0AucXR0QHFLSA3M4QyoLh5oMKPihCL+wDvd8v+8xBCtAAAAABJRU5ErkJggg==',
			'DB72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA6Y6IIkFTBFpBZIBAchirSKNDg2BDiKoYq0MQFERJPdFLZ0atmopkEZyH1jdFJBKNPMCGFoZ0MQcHYAq0dzC2sAQgOHmBsbQkEEQflSEWNwHAIKmzo4+vsyvAAAAAElFTkSuQmCC',
			'27A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQx2mMLQii4lMYWh0CGWYiiwW0MrQ6OgIFEXW3crQygqSQXbftFXTlq6KWorivgCGACR1YMjowOjAGooqxgqGqGIiQIguFhoKFgsNGAThR0WIxX0AIqXMSEgEDUsAAAAASUVORK5CYII=',
			'E5B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGUMDkMQCGkQaWBsdHRjQxRoC0cVCgOpcHZDcFxo1denS0JVRUUjuA5rd6NroADQBWS9QDGwqinlAsUAHVDHWVtZGhwBk94WGMIawhjJMdRgE4UdFiMV9AL0ezahH3SWdAAAAAElFTkSuQmCC',
			'B863' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUIdkMQCprC2Mjo6OgQgi7WKNLo2ODSIoKljBdFI7guNWhm2dOqqpVlI7gOrc3RowDQvANU8bGJY3ILNzQMVflSEWNwHAN92zmYZjUs6AAAAAElFTkSuQmCC',
			'E64F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHUNDkMQCGlhbGVodHRhQxEQaGaZiiDUwBMLFwE4KjZoWtjIzMzQLyX0BDaKtrI2Y5rmGBmKIOWCoA7oFTQzqZhSxgQo/KkIs7gMAiPLLnlcSFWMAAAAASUVORK5CYII=',
			'31F3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA0IdkMQCpjAGsDYwOgQgq2xlBYoxNIggi01hAIsFILlvZdSqqKWhq5ZmIbsPVR3UPAZM87CIBYD1orpFFOhioDoUNw9U+FERYnEfABExyaj3AbtBAAAAAElFTkSuQmCC',
			'3157' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUNDkMQCpjAGsAJpEWSVrayYYlOAeqcC1SO5b2XUqqilmVkrs5DdB1QHVNWKYnMrWGwKuhhrQ0AAA4pbGAIYHR0dUN3MGsoQyogiNlDhR0WIxX0Ao8vJHUZar6wAAAAASUVORK5CYII=',
			'EA02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEx1QBILaGAMYQhlCAhAEWNtZXR0dBBBERNpdAWRSO4LjZq2MnVVFBAi3AdV14hqh2goUKyVAc08oBVT0MUcgG5BdTNQbApjaMggCD8qQizuAwBjdc4xOSRb+AAAAABJRU5ErkJggg==',
			'ACB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDGRoCkMRYA1gbXRsdGpHFRKaINLg2BLQiiwW0ijSwNjpMCUByX9TSaauWhq6KikJyH0SdowOy3tBQoFhDYGgImnlAOxpQ7QC7BU0M080DFX5UhFjcBwBmRM/fR1Pq4wAAAABJRU5ErkJggg==',
			'EE4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHUMDkMQCGkQaGFodHRjQxaZiEQuEi4GdFBo1NWxlZmZoFpL7QOpYGzH1soYGYpqHRR26GDY3D1T4URFicR8AkaHLswdKkLMAAAAASUVORK5CYII=',
			'17E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxOjA0ujYwBAQgiYmCxRgdRFD0MrSyItSBnbQya9W0paGrpmYhuQ+oLoAVzTxGB0YHVgzzWBswxUQa0PWKhgDF0Nw8UOFHRYjFfQD8TMi5jRT72wAAAABJRU5ErkJggg==',
			'3673' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA0IdkMQCprC2MjQEOgQgq2wVaQTKNIggi00B8hodGgKQ3LcyalrYqqWrlmYhu2+KaCvDFIYGdPMcAhhQzQOKOTqgioHcwtrAiOIWsJsbGFDcPFDhR0WIxX0A6GrMmEqIHqMAAAAASUVORK5CYII=',
			'C0C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCHUIdkMREWhlDGB0CHQKQxAIaWVtZGwQaRJDFGkQaXcE0wn1Rq6atTF21amkWkvvQ1KGIiRCwA5tbsLl5oMKPihCL+wD8yMzWd04nawAAAABJRU5ErkJggg==',
			'51A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYAhimMLQiiwU0MAYwhDJMRRVjDWB0dAhFFgsMYAhgbQiA6QU7KWzaqqilIITsvlYUdQixUFSxACzqRKZgirECdQLFQgMGQfhREWJxHwA4hMrEmqTiaAAAAABJRU5ErkJggg==',
			'DAE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHRoCkMQCpjCGsDYwNKKItbK2AsVaUcVEGl0bGKYEILkvaum0lamhq6KikNwHUcfogKpXNBQoFhqCaR6aWzDFQgOAYmhuHqjwoyLE4j4APdfPumZoKBIAAAAASUVORK5CYII=',
			'754C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNFQxkaHaYGIIu2igCxQ4AIuthURwcWZLEpIiEMgY4OKO6Lmrp0ZWZmFrL7GB0YGl0b4erAkLUBKBYaiCIm0iDS6NCIakdAA2sr0H0obgloYAzBcPMAhR8VIRb3AQDPacwIPwmpvwAAAABJRU5ErkJggg==',
			'388A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGVqRxQKmsLYyOjpMdUBW2SrS6NoQEBCALAZW5+ggguS+lVErw1aFrsyahuw+VHVI5gWGhmCKoagLwKIX4mZGVPMGKPyoCLG4DwDqc8sNIu6I7AAAAABJRU5ErkJggg==',
			'DF03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIQ6IIkFTBFpYAhldAhAFmsVaWB0dGgQQRNjbQhoCEByX9TSqWFLgWQWkvvQ1KGIoZuHYQcWt4QGAMXQ3DxQ4UdFiMV9AAUCzm9iDkJCAAAAAElFTkSuQmCC',
			'B2B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUIdkMQCprC2sjY6OgQgi7WKNLo2BDSIoKhjaHRtdGgIQHJfaNSqpUtDVy3NQnIfUN0UVoQ6qHkMAazo5rUyOmCITWFtQHdLaIBoqCuamwcq/KgIsbgPANK2zxFSrj46AAAAAElFTkSuQmCC',
			'C4E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYWllDHaY6IImJtDJMZW1gCAhAEgtoZAhlbWB0EEEWa2B0ZQWpR3Jf1KqlS5eGgmiE+wKAJgLVNTqg6BUNdW1gaGVAtQOkbgoDqltAYgGYbnYMDRkE4UdFiMV9ALKFy8dlagX+AAAAAElFTkSuQmCC',
			'1009' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEx1QBJjdWAMYQhlCAhAEhN1YG1ldHR0EEHRK9Lo2hAIEwM7aWXWtJWpq6KiwpDcB1EXMBVTb0ADqhjIDgc0O7C4JQTTzQMVflSEWNwHAIrhyJbvc66JAAAAAElFTkSuQmCC',
			'904C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaYGIImJTGEMYWh1CBBBEgtoZW1lmOrowIIiJtLoEOjogOy+aVOnrczMzMxCdh+rq0ijayNcHQQC9bqGBqKICYDsaES1A+yWRlS3YHPzQIUfFSEW9wEASUbLZ5AVw30AAAAASUVORK5CYII=',
			'7658' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHaY6IIu2srayNjAEBKCIiTSyNjA6iCCLTRFpYJ0KVwdxU9S0sKWZWVOzkNzH6CDaytAQgGIea4NIo0NDIIp5IkAxVzSxgAbWVkZHBxS9AQ2MIQyhDKhuHqDwoyLE4j4AhX/L0tXaLqkAAAAASUVORK5CYII=',
			'A4CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCHUMdkMRYAximMjoEOgQgiYlMYQhlbRB0EEESC2hldGUFmiCC5L6opUCwamXWNCT3BbSKtCKpA8PQUNFQVzSxgFaGVkw7GFrR3QISQ3fzQIUfFSEW9wEAdezLIJ0j75EAAAAASUVORK5CYII=',
			'D4CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWhlCHUNDkMQCpjBMZXQIdEBWFwBUxdogiCbG6MrawAgTAzspaikQrFoZmoXkvoBWkVYkdVAx0VBXDDGGVgw7pjC0orsF6mYUsYEKPypCLO4DAIsjysRLLl5bAAAAAElFTkSuQmCC',
			'431B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37prCGMExhDHVAFgsRaWUIYXQIQBJjDGFodASKiSCJsU5haAXqhakDO2natFVhq6atDM1Ccl8AqjowDA1laHSYgmoewxRsYiIYekFuZgx1RHXzQIUf9SAW9wEA7HLKll8DxEgAAAAASUVORK5CYII=',
			'10DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGaYGIImxOjCGsDY6BIggiYk6sLayNgQ6sKDoFWl0BYohu29l1rSVqasis5Ddh6YOjxg2O7C4JQTTzQMVflSEWNwHANiyyMfSixlxAAAAAElFTkSuQmCC',
			'3B86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RANEQxhCGaY6IIkFTBFpZXR0CAhAVtkq0ujaEOgggCwGVufogOy+lVFTw1aFrkzNQnYfRB1W80QIiGFzCzY3D1T4URFicR8AaSTLplg5oHsAAAAASUVORK5CYII=',
			'E72D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMdkMQCGhgaHR0dHQLQxFwbAh1EUMVaGRBiYCeFRq2atmplZtY0JPcB1QUwtDKi6WV0YJiCLsYKVIkuJgJUyYjiltAQkQbW0EAUNw9U+FERYnEfANA3y65PXzqwAAAAAElFTkSuQmCC',
			'0870' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgDWIH8gKkOSGIiU0QaHRoCAgKQxAJageoaHR1EkNwXtXRl2KqlK7OmIbkPrG4KI0wdVAxoXgCqGMgORwcGFDtAbmFtYEBxC9jNDQwobh6o8KMixOI+ANoEy7oFkO9hAAAAAElFTkSuQmCC',
			'842A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCgRhJTGQKw1RGR4epDkhiAUBVrA0BAQEo6hhdGRoCHUSQ3Lc0aunSVSszs6YhuU9kikgrQysjTB3UPNFQhymMoSGodrQyBKCqA7oFqBNVDORm1tBAFLGBCj8qQizuAwCbOsq9M7NwxwAAAABJRU5ErkJggg=='        
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