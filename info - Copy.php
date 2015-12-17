<?php 
/*
  Project: CSV Product Import
  Author : karapuz <support@ka-station.com>

  Version: 3 ($Revision: 121 $)

*/


 function showSelector($name, $data, $selected = '', $extra = '') {
		//$template = new Template();
		//$template = array();
		$template->data['name']     = $name;
		$template->data['data']     = $data;
		$template->data['selected'] = $selected;
		$template->data['extra']    = $extra;
		//$text = $template->fetch("tool/ka_selector.tpl");
		$text = '<select name="'.$name. ' '.$extra.'>';
                if (!empty($data)) { ?>
                    <?php foreach($data as $dk => $dv) { ?>
                          <?php $text .= '<option ' . (($dk == $selected) ?  'selected="selected"' : '') . 'value="'.$dk.'">'.$dv.'</option>'; ?>
                        <?php //$text .= '<option'  . 'value="'.$dk.'">'.$dv.'</option>'; ?>
                <?php } ?>
                <?php } ?>
                <?php $text .= ' </select>';
		echo $text;
 	}
        
?>
<?php echo $header; ?><?php echo $column_left; ?>



<div id="content">
    
 

    

    <?php if (!empty($is_wrong_db)) { ?>

      Database is not compatible with the extension. Please re-install the extension on the 'Extensions / Product Feeds' page.
      <br /><br />
			It means you need to click on the 'Uninstall' link and after the page refreshes click on the 'Install' link. 
			That should help make the database up to date for the current version of the import extension.
      
  
    <?php } elseif ($params['step'] == 1) { ?>

    
    
        <div class="container-fluid">
        <div class="panel panel-default">
<div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <input type="hidden" name="mode" value="" />
          
      
            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab"  href="#tab-general">General</a></li>
              <li><a data-toggle="tab"  href="#tab-downloads">Downloads</a></li>
              <li><a data-toggle="tab" href="#tab-extra">Extra</a></li>
          </ul>
        
            <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
              
                </div>

                <div class="tab-pane" id="tab-extra">

            

            </div>

                <div class="tab-pane" id="tab-downloads">      
                
            </div>
            </div>       
      </form>
</div>
        </div>
        </div>
    

<script type="text/javascript"><!--

$(document).ready(function() {
  //$('#tabs a').tabs();
});

function activateLocation(id) {
  if (id == 'server') {
    $('#local_location').hide();
    $('#server_location').show();
  } else if (id == 'local') {
    $('#local_location').show();
    $('#server_location').hide();
  }
}


function activateCharset(id) {
  if (id == 'predefined') {
    $('#predefined_charset_row').show();
    $('#custom_charset_row').hide();
    $('#charset_option').val('predefined');

  } else if (id == 'custom') {
    $('#predefined_charset_row').hide();
    $('#custom_charset_row').show();
    $('#charset_option').val('custom');
  }
}

function activateDelimiter(id) {
  if (id == 'predefined') {
    $('#predefined_delimiter_row').show();
    $('#custom_delimiter_row').hide();
    $('#delimiter_option').val('predefined');

  } else if (id == 'custom') {
    $('#predefined_delimiter_row').hide();
    $('#custom_delimiter_row').show();
    $('#delimiter_option').val('custom');
  }
}


function loadProfile() {

  $("#form input[name='mode']").attr('value', 'load_profile');
  $("#form").submit();
}


function deleteProfile() {

  $("#form input[name='mode']").attr('value', 'delete_profile');
  $("#form").submit();
}

$('input[name=\'tpl_product\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=tool/ka_import/completeTpl&token=<?php echo $token; ?>',
      type: 'POST',
      dataType: 'json',
      data: 'filter_name=' + encodeURIComponent(request.term),
      success: function(data) {   
        response($.map(data, function(item) {
          return {
            label: item.name,
            value: item.product_id
          }
        }));
      }
    });
    
  }, 
  select: function(event, ui) {
    
    $("input[name='tpl_product']").attr('value', ui.item.label).attr('disabled', 'disabled');
    $("input[name='tpl_product_id']").attr('value', ui.item.value);
    $("#view_tpl_product").attr('href', '<?php echo $product_url; ?>' + '&product_id=' + ui.item.value);
    $("#tpl_product_actions").css('display', 'inline');

    return false;
  }
});

function clearTplProduct() {

    $("input[name='tpl_product']").attr('value', '').removeAttr('disabled');
    $("input[name='tpl_product_id']").attr('value', '');
    $("#view_tpl_product").removeAttr('href');
    $("#tpl_product_actions").hide();
}

//--></script> 

    <?php } ?>

  

  <span class="help">'CSV Product Import' extension developed by <a href="mailto:support@ka-station.com?subject=CSV Product Import">karapuz</a></span>
</div>
<style type="text/css">
<!--
/*
span.important_note {
  color: red;
  font-weight: normal;
}

div.scroll {
  height: 200px;
  width: 100%;
  overflow: auto;
  border: 1px solid black;
  background-color: #ccc;
  padding: 8px;
}

span.note {
  font-weight: bold;
}

.list td a.link {
  text-decoration: underline;
  color: blue;
}

#import_status {
  color: black;
}
*/
-->
</style>
<?php echo $footer; ?>