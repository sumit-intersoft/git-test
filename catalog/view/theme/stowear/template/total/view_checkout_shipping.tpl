<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed" href="#collapse-shipping"><?php echo $heading_title; ?> <i class="fa fa-caret-down"></i></a></h4>
    </div>
    <div class="panel-collapse collapse" id="collapse-shipping" style="height: 0px;">
        <div class="panel-body">
            <div id="modal-shipping" class=""> 
				   <div class="modal-dialog">
				     <div class="modal-content">
				       <div class="modal-header"> 
				         <h4 class="modal-title"><?php echo $text_shipping_method; ?></h4> 
				       </div> 
				       <div class="modal-body"> 
                                        <?php
				foreach ($shipping_methods as $i => $shipping_method) { 
                                    if (!$shipping_method['error']) { 
						foreach($shipping_method['quote'] as $j => $value) {
                                    ?>
					 <p><strong><?php  echo $value['title'] ?></strong></p> 

					
							<div class="radio"> 
							   <label> 
                                                                <?php     
                                                                     if ($value['code'] == $shiping_method) { ?>
                                                                              <input type="radio" name="shipping_method" value="<?php echo $value['code'] ?>" checked="checked" /> 
                                                                    <?php } else { ?>
                                                                              <input type="radio" name="shipping_method" value="<?php echo $value['code'] ?>" /> 
                                                                     <?php } 
                                                                     echo $value['title'] . ' - ' . $value['text'] ?> 
                                                           </label>
                                                        </div>
                                                <?php        
						}
					} else { ?>
						 <div class="alert alert-danger"><?php echo $shipping_method['error']['error'] ;?></div> 
					<?php }
                                        
				}
?>
				       </div> 
				       
				     </div> 
				   </div> 
				 </div>
            <script type="text/javascript"><!--
        $(document).delegate('input[name=\'shipping_method\']', 'change', function() {
	$.ajax({
		url: 'index.php?route=total/shipping/shipping',
		type: 'post',
		data: 'shipping_method=' + encodeURIComponent($('input[name=\'shipping_method\']:checked').val()),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping').button('loading');
		},
		complete: function() {
			$('#button-shipping').button('reset');
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				$('.center-column > *:first-child').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}

			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script>
        </div>
    </div>
</div>