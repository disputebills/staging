<div class="wrap">
    
    <div class="postbox green-back">
        <div class="inside">
            <p>
                This option is created for fetching all posts from your facebook page or group. Use it only in case if you need to import 
                comments from very old posts. Only manual import is available for this option.
            </p>
            <form method="post" action="?page=fbsync_comments" id="fetch_all_posts_form_free">
                Show posts from (Facebook page ID): 
                <input type="text" name="fb_page_id" id="fb_page_id" value="<?php echo $fb_page_id;?>">
                <input type="hidden" name="access_token" id="access_token" value="<?php echo $access_token;?>">
                <input type="submit" id="fetch_all_posts_button" value="Fetch Now!">
                <input type="button" onclick="location.reload();" value="New Check">
            </form>
        </div>
    </div>
    
    <h2>List of posts:</h2>
    To connect facebook post with wordpress automatically, post must contain link to some wordpress article. 
    (You can post link on facebook, or include it into description, message and etc...)<hr> 
    * You can use <a href="http://wp-resources.com/facebook-comments-importer/" target="_blank"> PRO Version of plugin</a> to connect posts manually
    <table class="widefat" id="table_with_posts" style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="200">Title</th>
                <th>URL</th>
                <th>Type</th>
                <th>Date</th>
                <th width="120">Post ID / Import</th>
            </tr>
        </thead>
        <tbody id="table_with_posts_body">
        </tbody>
    </table>
    
    
</div>