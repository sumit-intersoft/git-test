<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		url: 'index.php?route=payment/stripe/confirm',
                dataType: 'json',
                type: 'post',
                data: $('.payment-methods input, .payment-methods select'),
                cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(json) {
                    if (json['redirect']) {
                            location = json['redirect'];
                    }
			
                    location = '<?php echo $continue; ?>';
		}
	});
});
//--></script>