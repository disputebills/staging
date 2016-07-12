<?php
Class FBCommentsFree{
    
    public function __construct(){
        $this->appID = get_option('fbsync_comments_appID');
        $this->pageID = get_option('fbsync_comments_pageID');
        $this->appSecret = get_option('fbsync_comments_appSecret');
        $this->comments_status = get_option('commentes_importer_comments_status');
        $this->WPBaseURL = get_option('commentes_importer_website_base_url');
    }
    
    
    /**
     * Generate avatar image
     * @param type $avatar
     * @param type $id_or_email
     * @param type $size
     * @return type
     */
    public function GenerateAvatar($avatar, $id_or_email, $size = '50'){
        if (!is_object($id_or_email)) {
            $id_or_email = get_comment($id_or_email);
        }

        if (is_object($id_or_email)) {
            
            $ag_part = explode("__",$id_or_email->comment_agent); /// get only agent, remove hash
            if ($ag_part[0] == 'facebook-comment-importer-plugin' OR $id_or_email->comment_agent == 'facebook-comment-importer plugin') {
                $alt = '';
                $fb_url = $id_or_email->comment_author_url;
                $fb_array = explode("/", $fb_url);
                $fb_id = $fb_array[count($fb_array) - 1];
                if (strlen($fb_id) > 1) {
                    $img = "http://graph.facebook.com/" . $fb_id . "/picture?type=large";
                    return "<img alt='{$alt}' src='{$img}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
                }
            }
        }
        return $avatar;
    }
    
    /**
     * Explode string on multiple roles
     * @param type $delimiters
     * @param type $string
     * @return type
     */
    public function multiexplode ($delimiters,$string) {
    
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    } 
    
    /**
     * Curl get content - https enabled
     * @param type $fullurl
     * @return type
     */
    public function GetHTTPSData($fullurl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $fullurl);
        $returned = curl_exec($ch);

        return $returned;
    }
    
    /**
     * Get post ID from post title
     * @global type $wpdb
     * @global type $wpdb
     * @global type $wpdb
     * @param type $post_title
     * @param type $fid
     * @return string
     */
    function post_id_from_name($post_title,$fid){
        global $wpdb;
        $post_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_name = '".$post_title."'" );
        $post_id = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = '".$post_title."'");

        if($post_count == 1){
            return $post_id;
        }
        else {
            global $wpdb;
            $post_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."fb_comments_image_data WHERE imgid = '".$fid."'" );
            if($post_count >=1){
                global $wpdb;
                $post_id = $wpdb->get_var("SELECT postid FROM ".$wpdb->prefix."fb_comments_image_data WHERE imgid = '".$fid."'");
                return $post_id;
            }
            else {
                return "-";
            }
        }
    }
    
    
    /**
     * Return total number of comments
     * @global type $wpdb
     * @param type $post_id
     * @return int
     */
    function total_comments_f($post_id){
        if($post_id == "-"){
            return 0;
        }
        else {
            global $wpdb;
            $commentes_num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '".$post_id."'" );
            return $commentes_num;
        }
    }
    
    
    
    /////////// WORKING WITH FB API ////////////////////////////////////////////
    
    
    
    /**
     * Genrate token
     * @return type
     */
    public function GenerateAccessToken(){
        $fullurl = "https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=$this->appID&client_secret=$this->appSecret";
        $ret = $this->GetHTTPSData($fullurl);
        return $ret;
    }
    
    
    public function GetFbPageItems($api_url){
        
        $data = $this->GetHTTPSData($api_url);
        $obj = json_decode($data);        

        // parse and prepare data from api
        if($obj->data){
            
            // genrate array
            foreach ($obj->data as $element) {

                // set post ID to null
                $wp_post_id = null;

                if(isset($element->name) || isset($element->message)){
                    
                    
                    // filter facebook redirect link
                    if(strpos($element->link,"facebook.com/l.php?u=")!== false){
                        $element->link = preg_replace('/.*?facebook.com\/l.php\?u=(.*?)\&h=.*/i', "$1", $element->link,1);
                        $element->link = urldecode($element->link);
                    }
                    
                    // check post id
                    $wp_post_id = url_to_postid($element->link);
                    if (empty($wp_post_id)) {
                        $wp_post_id = "-";
                    }
                    
                    // connect post with url inside message
                    $post_message = (isset($element->message)) ? $element->message : null;
                    $link_in_status = explode("http://", $post_message);
                    
                    $get_only_link = (isset($link_in_status[1])) ? $this->multiexplode(array(" ","\n","\t"), $link_in_status[1]) : null;
                    if(isset($get_only_link[0])){
                        
                        // is there link to my site, or some other site
                        $full_link_to_site = "http://".$get_only_link[0];
                        
                        // compare link with site url
                        if(strpos($full_link_to_site,$this->WPBaseURL)!== false && $full_link_to_site!="http://"){
                            $wp_post_id = url_to_postid( $full_link_to_site );
                            if(!$wp_post_id){
                                $wp_post_id = "-";
                            }
                        }
                    }
                    
                                        
                    
                    // if id does not exist skip this
                    $mydata['id'] = (isset($element->id)) ? $element->id : null;
                    $mydata['name'] = (isset($element->name)) ? $element->name : null;
                    $mydata['link'] = (isset($element->link)) ? $element->link : null;
                    $mydata['type'] = (isset($element->type)) ? $element->type : null;
                    $mydata['picture'] = (isset($element->picture)) ? $element->picture : null;
                    $mydata['message'] = (isset($element->message)) ? $element->message : null;
                    $mydata['total_comments'] = $this->total_comments_f($wp_post_id);
                    $mydata['wp_post_id'] = $wp_post_id;
                    $FBPosts[] = $mydata;

                }
                else {
                    $mydata['id'] = (isset($element->id)) ? $element->id : null;
                    $mydata['name'] = (isset($element->name)) ? $element->name : null;
                    $mydata['link'] = (isset($element->link)) ? $element->link : null;
                    $mydata['type'] = (isset($element->type)) ? $element->type : null;
                    $mydata['picture'] = (isset($element->picture)) ? $element->picture : null;
                    $mydata['message'] = (isset($element->message)) ? $element->message : null;
                    $mydata['wp_post_id'] = "-";
                    $mydata['total_comments'] = "0";
                    $FBPosts[] = $mydata;
                }
                
            }

            if($obj->paging->next){
                $next_page_url = $obj->paging->next;
            } else{
                $next_page_url = false;
            }
            
            return array("data"=>$FBPosts,"next_url"=>$next_page_url);
        }
        else  {
            return false;
        }
    }
    
    
    
    /**
     * Get items from fb page
     * @param int $limit
     * @param string $token
     * @param string $fields
     * @return type
     */
    public function GetListOfFBPosts($limit,$token,$fields = 'id,message,created_time,picture,type,name,source,link'){
        
        
        // set number of pages
        if(!$limit){
            $number_of_pages = 1;
        } else {
            $number_of_pages = round($limit/25);
            if($number_of_pages < 1){
                $number_of_pages = 1;
            }
        }
        // generate first url
        $page_url = "https://graph.facebook.com/$this->pageID/feed?$token&limit=25&fields=$fields";
        // set page counter
        $i = 0;
        $FBPage = null;
        $Items = array();
        do{
            if($i == $number_of_pages){
                break;
            }
            
            $page_url_enter = (filter_var($FBPage['next_url'])) ? filter_var($FBPage['next_url']) : $page_url;
            $FBPage = $this->getFBPageItems($page_url_enter);
            // check is it array
            if(is_array($FBPage['data']) && is_array($Items)){
                $Items = array_merge($Items,$FBPage['data']);
            } else{
                break;
            }
            $i++;
        } while($FBPage['next_url']);

        
        return $Items;
    }
    
    
    /*
     * Get posts from facebook group, without limit, just send next page url
     */
    public function fetchGroupPostsUnlimited($api_url,$fb_page_id,$limit,$token,$fields = 'id,message,created_time,picture,type,name,source,link'){
        
        // API URI
        if($api_url=='null'){
            $page_url = "https://graph.facebook.com/v2.4/".$fb_page_id."/feed?limit=50&$token&fields=$fields";
        } else {
            $page_url = $api_url;
        }
        $FBPage = $this->GetFbPageItems($page_url);
        return $FBPage;
        
    }
    
    
    /**
     * Get number of comments for specific item
     * @param num $item_id
     * @param string $token
     * @return int
     */
    public function CountCommentsForSingleItem($item_id,$token){
        if($item_id && $token){
            $data = $this->GetHTTPSData("https://graph.facebook.com/$item_id/comments?$token&limit=$limit&fields=id");
            $obj = json_decode($data);
            $num = count($obj->data);
            return $num;
        }
        else {
            return 0;
        }
    }
    
    
    ////////// WORKING WITH WP Comments ////////////////////////////////////////
    
    /**
     * Get username from facebook
     * @param type $userid
     * @return type
     */
    public function FBUsername($userid){
        $file = $this->GetHTTPSData("http://graph.facebook.com/$userid");
        $obj = json_decode($file);
        $username = $obj->username;
        return $username;
    }
    
    /**
     * Generate array with all comments for single post
     * @param type $postid
     * @return type
     */
    public function GenerateCommentsArrayForPost($postid){
        
        $all_my_comments = array();
        
        $my_comments = get_comments( array("post_id"=>$postid) );
        foreach($my_comments as $comment){
            $all_my_comments[] = $comment->comment_content;
        }
        return $all_my_comments;
    }
    public function GenerateCommentsArrayForPostAgent($postid){
        
        $all_my_comments = array();
        
        $my_comments = get_comments( array("post_id"=>$postid) );
        foreach($my_comments as $comment){
            $all_my_comments[] = $comment->comment_agent;
        }
        return $all_my_comments;
    }
     
    /**
     * Check is this double comment
     * @global type $wpdb
     * @param type $cid
     * @param type $mess
     * @return type
     */
    public function IsDoubleComment($cid, $user_agent,$mess,$comments_array){
        //$mess = addslashes($mess);
        
        if(in_array($user_agent,$comments_array)){
            return "1";
        }
        else {
            
            global $wpdb;
            $num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '" . $cid . "' AND comment_content = '" . $mess . "'");
            if (!$num) {
                return "0";
            } else {
                return $num;
            }
        }
    }
    /**
     * Check is this double comment meta
     * @global type $wpdb
     * @param type $cid
     * @param type $mv
     * @return type
     */
    public function IsDoubleCommentmeta($cid, $mv){
        global $wpdb;
        $num = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."commentmeta WHERE comment_id = '" . $cid . "' AND meta_value = '" . $mv . "'");
        if(!$num){
            return "0";
	} else {
            return $num;
	}
    }
    
    /**
     * Get comment ID from useragent
     * @global type $wpdb
     * @param type $postid
     * @param type $user_agent
     * @return type
     */
    public function GetTopCommentID($postid, $user_agent){
        global $wpdb;
        $num = $wpdb->get_var( "SELECT comment_ID FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '" . $postid . "' AND comment_agent = '" . $user_agent . "'");
        if(!$num){
            return "0";
	} else {
            return $num;
	}
    }
    
    public function replace4byte($string) {
        return preg_replace('%(?:
              \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )%xs', '', $string);    
    }
    
    /**
     * get comments and replies for single post
     * @param type $id
     * @param type $post_id
     * @return type
     */
    public function GetFBComments($id,$post_id){
        $id = trim($id);
        $token = $this->GenerateAccessToken();        
        $file = $this->GetHTTPSData('https://graph.facebook.com/v2.5/'.$id.'/comments?'.$token.'&limit=10000&fields=id,from,message,created_time,attachment,comments.limit(10000){id,from,message,created_time,attachment}');
        $FBobject = json_decode($file);
        
        if($FBobject->data){
            $FBCommentsArray = array_reverse($FBobject->data);
        
            // get all comments for this post
            $all_comments_for_post = $this->GenerateCommentsArrayForPostAgent($post_id);

            // parse FB object, and create array for db insert
            if($FBCommentsArray){
                foreach($FBCommentsArray as $SingleCommentData){

                    $data['comment_id'] = $SingleCommentData->id;
                    $data['from_name'] = $SingleCommentData->from->name;
                    $data['from_id'] = $SingleCommentData->from->id;
                    $data['message'] = $this->replace4byte($SingleCommentData->message);
                    $data['created_time'] = $SingleCommentData->created_time;
                    $data['timestamp'] = strtotime($SingleCommentData->created_time);
                    $data['wp_date'] = $this->GenerateTimeFromString($SingleCommentData->created_time);
                    $data['profile_link'] = 'http://www.facebook.com/'.$SingleCommentData->from->id;
                    
                    if(isset($SingleCommentData->attachment->media->image->src) && isset($SingleCommentData->attachment->url)){
                        $data['image'] = serialize(array("image"=>$SingleCommentData->attachment->media->image->src,"url"=>$SingleCommentData->attachment->url));
                    }
                    
                    $data['double_comment_check'] = $this->IsDoubleComment($post_id, 'facebook-comment-importer-plugin__'.$SingleCommentData->id,$SingleCommentData->message,$all_comments_for_post);
                    if($data['double_comment_check'] == "1"){
                        $data['current_comment_id'] = $this->GetTopCommentID($post_id, 'facebook-comment-importer-plugin__'.$SingleCommentData->id);
                    }
                    if(isset($SingleCommentData->comments->data)){
                        $data['replies'] = $this->GetCommentsRepliesFromObject($SingleCommentData->comments->data,$post_id,$all_comments_for_post);
                    } else {
                        $data['replies'] = null;
                    }
                    $Comments[] = $data;
                    unset($data);
                    $SingleCommentData->comments = false;
                    unset($SingleCommentData->comments);
                    unset($SingleCommentData);
                }
            }

        }
        // return complete array with replays
        return $Comments;
    }
    
    /**
     * Get only object with replies and create array
     * @param type $FBCommentsArray
     * @return boolean
     */
    public function GetCommentsRepliesFromObject($FBCommentsArray,$post_id,$all_comments_for_post){
        if($FBCommentsArray){
            foreach($FBCommentsArray as $SingleCommentData){
                
                $data['comment_id'] = $SingleCommentData->id;
                $data['from_name'] = $SingleCommentData->from->name;
                $data['from_id'] = $SingleCommentData->from->id;
                $data['message'] = $this->replace4byte($SingleCommentData->message);
                $data['created_time'] = $SingleCommentData->created_time;
                $data['timestamp'] = strtotime($SingleCommentData->created_time);
                $data['wp_date'] = $this->GenerateTimeFromString($SingleCommentData->created_time);
                $data['profile_link'] = 'http://www.facebook.com/'.$SingleCommentData->from->id;
                if(isset($SingleCommentData->attachment->media->image->src) && isset($SingleCommentData->attachment->url)){
                    $data['image'] = serialize(array("image"=>$SingleCommentData->attachment->media->image->src,"url"=>$SingleCommentData->attachment->url));
                }
                $data['double_comment_check'] = $this->IsDoubleComment($post_id, 'facebook-comment-importer-plugin__'.$SingleCommentData->id,$SingleCommentData->message,$all_comments_for_post);
                
                $Comments[] = $data;
                unset($SingleCommentData);
            }
            return $Comments;
        }
        else {
            return false;
        }
    }
    
    /**
     * Generate time from wp string
     * @param type $created_time
     * @return type
     */
    private function GenerateTimeFromString($created_time){
        $date = explode("T", $created_time);
        $d = $date[0];
        $t = explode("+", $date[1]);
        $t = $t[0];
        $dt = "$d $t";
        return $dt;
    }
    
    
    
    /**
     * Save comments to database
     * @param type $comments
     * @param type $post_id
     */
    public function SaveCommentsToDatabase($comments,$post_id){
        
        // check comment status
        $comments_status_value = $this->comments_status;
        if(!$comments_status_value){
            $comments_status_value = "0";
        }

        $i=0;
        if($comments){
            // parse comments
            foreach($comments as $comment){

                // check is this double comment, if yes skip it
                if($comment['double_comment_check']=="0"){

                    $ag = "facebook-comment-importer-plugin__".$comment['comment_id']; // set comment agent
                    
                    $data = array(
                        'comment_post_ID' => $post_id,
                        'comment_author' => $comment['from_name'],
                        'comment_author_url' => $comment['profile_link'],
                        'comment_content' => $comment['message'],
                        'comment_agent' => $ag,
                        'comment_date' => $comment['wp_date'],
                        'comment_date_gmt' =>$comment['wp_date'], 
                        'comment_approved' => $comments_status_value,
                    );
                    $parent_comment = wp_insert_comment($data);
                    // add comment meta for images
                    if(isset($comment['image'])){
                        add_comment_meta( $parent_comment, 'fb_comments_importer_comment_image', $comment['image'] );
                    }
                    $i++;
                }
                else {
                    $parent_comment = $comment['current_comment_id'];
                }

                // insert replies if exist
                if($comment['replies']){
                    
                    // parse replies
                    foreach($comment['replies'] as $replay){
                        
                        if($comment['double_comment_check']=="0"){
                            $ag = "facebook-comment-importer-plugin__".$replay['comment_id']; // set comment agent
                            $data = array(
                                'comment_post_ID' => $post_id,
                                'comment_author' => $replay['from_name'],
                                'comment_author_url' => $replay['profile_link'],
                                'comment_content' => $replay['message'],
                                'comment_parent'=> $parent_comment,
                                'comment_agent' => $ag,
                                'comment_date' => $replay['wp_date'],
                                'comment_date_gmt' => $replay['wp_date'], 
                                'comment_approved' => $comments_status_value,
                            );
                            $reply_id = wp_insert_comment($data);
                            
                            // add comment meta for images
                            if(isset($replay['image'])){
                                add_comment_meta( $reply_id, 'fb_comments_importer_comment_image', $replay['image'] );
                            }
                            $i++;
                            
                        }
                    }

                }

                


            }
        
        }
        // update comment count
        wp_update_comment_count($post_id);
        
        return $i;
    }
    
    
    
    
}
