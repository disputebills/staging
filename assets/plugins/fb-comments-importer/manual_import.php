<?php
// get data
$fbid = $_GET['fbid'];
$post_id = $_GET['post_id'];

// prepare and import comments
$FBCAPI = new FBCommentsFree();
$GetComments = $FBCAPI->GetFBComments($fbid,$post_id);

$SaveComments = $FBCAPI->SaveCommentsToDatabase($GetComments, $post_id);
// show template
require_once 'templates/import_done_tpl.php';

