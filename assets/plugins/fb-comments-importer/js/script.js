function CheckComNumFree(id,token){
    jQuery.ajax({
        type: "GET",
        url: "https://graph.facebook.com/v2.5/"+id+"?fields=comments.summary(true).limit(0)&"+token,
        async: true,
        success: function(resp) {
            num = resp.comments.summary.total_count;
            jQuery("#countcomm_"+id).html('['+num+']');
        }
    });
}
jQuery("document").ready(function() {
    jQuery( ".hideme" ).hide();
});

function ShowHideRows(){
    jQuery( ".hideme" ).toggle();
}

function substr(string, from, to ){
    return string.substr(from,to);
}

function ImportCommentsAjaxFree(fbid){
    var post_id = jQuery("#post_id_for_"+fbid).val();
    var data = {
        action: 'fbsync_comments_free_all_posts_import',
        post_id: post_id,
        fbid: fbid
    };
    jQuery.post(ajaxurl, data, function(response) {
        // working
    }).done(function(response) {
        response = jQuery.parseJSON(response);
        if(response.status){
            alert(response.num+'  Comments imported!');
        } else {
            alert(response.msg);
        }
    });
    
    
}


/*
 * Recursive function for fetching big number of entries
 */
function FetchFacebookPostsFree(page){
    
    
    // disable fetch now button
    jQuery("#fetch_all_posts_button").attr('disabled','disabled').attr('value', 'Working...');
    
    var data = {
        action: 'fbsync_comments_free_all_posts',
        group_id: jQuery("#fb_page_id").val(),
        limit: jQuery("#limit").val(),
        access_token: jQuery("#access_token").val(),
        api_page: page
    };
    jQuery.post(ajaxurl, data, function(response) {
        //console.log(response);
    }).done(function(response) {
        response = jQuery.parseJSON(response);
        if(response.next_url){
            
            var i = 1;
            jQuery.each(response.data, function(key,value){
                
                var message = '';
                if(value.type === "photo"){
                    message = '<a href="'+value.picture+'" target="_blank">Image</a>';
                } else if(value.type === "status"){
                    if(value.name != null){
                        message = substr(value.name,0,50)+'...';
                    } else {
                        message = substr(value.message,0,50)+'...';
                    }
                } else {
                    message = value.name;
                }
                
                var url  = '';
                var url = (value.link === "empty" || value.link === null) ? 'No link' : '<a href="'+value.link+'" target="_blank">'+substr(value.link,0,50)+'</a>';
                
                var type = value.type;
                var post_id = '';
                
                var import_field = (value.wp_post_id === '-') ? '<span style="color:red;">Unable to connect automatically! *</span>' : '<input type="text" name="post_id" style="width: 50px;" id="post_id_for_'+value.id+'" value="'+value.wp_post_id+'"><input type="submit" value="Import!" onclick="ImportCommentsAjaxFree(\''+value.id+'\')">';
                
                var date = '';
                date = value.created_time;
                
                jQuery("#table_with_posts_body").append('<tr><td>'+message+'</td><td>'+url+'</td><td>'+type+'</td><td>'+date+'</td><td>'+import_field+'</td></tr>');
                i++;
            });
            FetchFacebookPostsFree(response.next_url);
        } else {
            jQuery("#fetch_all_posts_button").removeAttr('disabled').attr('value', 'Fetch Now!');
        }
    });

}



jQuery(function() {

    //hang on event of form with id=myform
    jQuery("#fetch_all_posts_form_free").submit(function(e) {
        //prevent Default functionality
        e.preventDefault();
        jQuery("#table_with_posts_body").html('');
        FetchFacebookPostsFree('null');
    });

});
