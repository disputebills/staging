<?php  
if(!function_exists('curl_version')){
    echo '<div style="border: 1px solid #000000; padding: 5px; background: #FF0D00; border-radius: 5px; color: #fff">';
    echo "<b>ERROR: cURL is NOT installed on this server. Please enable curl to use this plugin.</b>";
    echo '</div>';
} 
?>

<div class="wrap">
    <div class="infodiv_fbcommentsimp">
        <h2>Configuration: </h2>
        <table width="100%">
            <tr>
                <td width="50%" valign="top">
                    <form action ="?page=fbsync_comments_free&action=save_data" method="POST">
                        <table>
                            <tr>
                                <td>
                                    <b>Facebook Fan Page ID :</b><br>
                                    <input name="pageID" type="text" value="<?php echo $pageID; ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="https://developers.facebook.com/apps" target="_blank"><b>APP ID:</b></a><br>
                                    <input name="appID" type="text" value="<?php echo $appID; ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="https://developers.facebook.com/apps" target="_blank"><b>APP Secret Code:</b></a><br>
                                    <input name="appSecret" type="text" value="<?php echo $appSecret; ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Website base URL:</b><br>
                                    <input name="ws_base_url" type="text" value="<?php echo $website_base_url;?>" class="regular-text"><br>
                                    <small>Please do not change this option if you are not sure what you are doing.</small>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <hr>
                                    <b>Comments status:</b><br>
                                    <input type="radio" <?php if($comments_status_value==1){echo "checked";}?> name="comments_status" value="1" id="comments_status_1"> <label for="comments_status_1">Approved </label>
                                    <input type="radio" <?php if($comments_status_value==0){echo "checked";}?> name="comments_status" value="0" id="comments_status_0"> <label for="comments_status_0">Not approved</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Follow url shortener redirects:</b><br>
                                    <input type="radio" <?php if($follow_redirects==1){echo "checked";}?> name="follow_redirects" id="follow_redirects_yes" value="1"> <label for="follow_redirects_yes">Yes</label> 
                                    <input type="radio" <?php if($follow_redirects==0){echo "checked";}?> name="follow_redirects" id="follow_redirects_no" value="0"> <label for="follow_redirects_no">No</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Disable images in imported comments:</b><br>
                                    <input type="radio" <?php if($disable_images==1){echo "checked";}?> name="disable_images" id="disable_images_yes" value="1"> <label for="disable_images_yes">Yes</label> 
                                    <input type="radio" <?php if($disable_images==0){echo "checked";}?> name="disable_images" id="disable_images_no" value="0"> <label for="disable_images_no">No</label>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="submit" value="Save"></td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td valign="top">
                    <a href="http://wp-resources.com/facebook-comments-importer/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ );?>advert_3.png"></a>
                </td>
            </tr>
        </table>
    
    
    </div>
    
