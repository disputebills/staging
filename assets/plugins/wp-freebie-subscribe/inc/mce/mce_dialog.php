<?PHP

// Boostrap WP
$wp_include = "../wp-load.php";
$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {
  $wp_include = "../$wp_include";
}

// let's load WordPress
require($wp_include);

// Get Options from DB
//$tc = get_option('');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Subscribe To Download</title>
<style>

body{
	margin:0px !important;
	padding:0px !important;
	border:0px !important;
	font-family:Arial, Helvetica, sans-serif !important;
	font-size:12px;
}

.tc-mce-clear{
	clear:both;
	height:0px;
}

.tc-mce-popup{
	margin:5px 0 0 0;
	padding:20px;
}

.tc-mce-buttons{
	text-align:center;
}

.tc-mce-popup .option{
	padding: 0 0 8px 0;
	border-bottom:1px solid #ededed;
	margin-bottom:10px;
}

.tc-mce-popup .option .mce-option-title{
    clear: both;
    margin:0px;
    padding:7px 0 0 0px;
	width:175px;
	font-weight:bold;
}

.tc-mce-popup .option .section{
    font-size: 11px;
    overflow: hidden;
	width:100%;
}

.tc-mce-popup .option .section .element{
    margin:8px 0px;
    width: 100%;
	display:block;
}

.tc-mce-popup .option .section .description{
    color: #555555;
    font-size: 11px;
    width: 100%;
	clear:both !important;
}

.tc-mce-popup #content select{
    cursor: pointer;
	height:2.5em !important;
}

.tc-mce-popup .option .section .element .textfield{
    background: none repeat scroll 0 0 #FAFAFA;
    border-color: #CCCCCC #EEEEEE #EEEEEE #CCCCCC;
    border-style: solid;
    border-width: 1px;
    color: #888888;
    display: block;
    font-family: "Lucida Grande","Lucida Sans Unicode",Arial,Verdana,sans-serif;
    font-size: 12px;
    margin-bottom: 6px !important;
    padding: 5px;
    resize: none;
    width: 95% !important;
}

.tc-mce-popup .tc-checkbox-wrap{
	width:95px;
	float:left;
}

.tc-mce-popup .tc-checkbox-wrap{
	width:110px;
	float:left;
}

.tc-mce-popup .tc-checkbox{
	margin:0 7px 4px 0;
}

.tc-mce-popup .tc-radio-group{
	padding:3px;
}

.tc-mce-popup .tc-radio-group input{
	margin-top:0px !important;
	vertical-align:baseline !important;
	margin-right:5px;
}

</style>
<script language="javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
<script language="javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script language="javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
<script language="javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
<script language="javascript" type="text/javascript">

	// Start TinyMCE
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}	
	
	// Function to add the like locker shortcode to the editor
	function addShortcode(){
		
		// Cache our form vars
		var download_url = document.getElementById('freebie-url').value;
					
		// If TinyMCE runable
		if(window.tinyMCE) {
			
			// Get the selected text in the editor
			selected = tinyMCE.activeEditor.selection.getContent();
			
			// Send our modified shortcode to the editor with selected content							
			tinyMCE.activeEditor.insertContent('[freebiesub download="'+download_url+'"]');
			
			// Close the TinyMCE popup
			tinyMCEPopup.close();
			
		} // end if
		
		return; // always R E T U R N

	} // end add like locker function
	
</script>
</head>
<body>


<div class="tc-mce-popup">
    
    <div class="tc-mce-form-wrap">
    
    	<form action="" id="freebie-form">
        
            <div class="option">
                <div class="mce-option-title"><?php _e('URL To Download', 'tcsubtodl') ?></div>
                <div class="section">
                    <div class="element"><input class="textfield" name="freebie-url" type="text" id="freebie-url" value="" /></div>
                </div>
                <div class="description">Enter the URL to the download you wish to use with this subscription form. This should be the URL to your download manager, zip file, pdf, etc.</div>
                <br class="tc-mce-clear" />
            </div>  
                       
            <div class="tc-mce-buttons">
            	<input type="button" class="button-primary" value="<?PHP _e('Insert Shortcode', 'tcsubtodl'); ?>" onclick="addShortcode();" />
            </div>  
        
        </form>
    
    </div>

</div>

</body>
</html>