<?PHP

// Include Our Custom WP_List_Table Class
require_once('table_class.php');

/*-----------------------------------------------------------------------------------*/
/*	New framework Management Page
/*-----------------------------------------------------------------------------------*/

function freebiesub_manage_page() { ?>

    <div class="wrap">
            
        <div id="icon-users" class="icon32"><br/></div>
        <h2>Manage Subscriber List <a class="add-new-h2" href="<?php echo FREEBIESUB_LOCATION.'/inc/export.php?type=csv'; ?>">Export to CSV</a> <a class="add-new-h2" href="<?php echo FREEBIESUB_LOCATION.'/inc/export.php?type=text'; ?>">Export to TXT</a></h2>
    
        <?PHP
            //Create an instance of our package class...
            $freebieListTable = new freebiesub_Table_List();
            //Fetch, prepare, sort, and filter our data...
            $freebieListTable->prepare_items();
        ?>
                                
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="emails-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $freebieListTable->display() ?>
        </form>
                        
    </div>

<?php } ?>