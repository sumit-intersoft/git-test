<?php echo $header; 
$theme_options = $this->registry->get('theme_options');
$config = $this->registry->get('config'); 
include('catalog/view/theme/' . $config->get('config_template') . '/template/new_elements/wrapper_top.tpl'); ?>

<!--<?php if ($attention) { ?>
<div class="attention"><?php echo $attention; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>

<?php if ($success) { ?>
<div class="success"><?php echo $success; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>-->

<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>

<?php //echo $column_left; ?><?php //echo $column_right; ?>
<?PHP //$customer->isLogged();

	
global $registry;
$customer = $registry->get('customer');
//$customer = new Customer();
//echo $customer->isLogged();

$session = $registry->get('session');
//$customer = new Customer();
//print_r($session->data['payment_address']);

//$registry = new Registry();
//$registry->set('language', $language);
        //$action = new Action('checkout/shipping_method');
        //$hanish = $action->execute($registry);
        
        // Registry
       // global $registry;

        //$action = new Action('checkout/shipping_method/save');
        //$sumit = $action->execute($this->registry);
        //$sumit->save();
       // echo '<pre>'; print_r($sumit); echo '</pre>';
       // exit;
       
       /* $files = glob(DIR_APPLICATION . '/controller/total/shipping.php');

			if ($files) {
				foreach ($files as $file) {
					echo $extension = basename($file, '.php');

					echo $data[$extension] = $this->load->controller('total/' . $extension);
				}
			} */
            
       
       
       
       
?>
<div id="content" class="sc-page">
    <?php //echo $content_top; ?>
    <?php if(!$logged) { ?>
    <div  class="sign-up">    
        <div id="sign-up" class="login-sign-up">
            <h4>New Customers</h4>
            <p>
                <label>Email Address<strong style="color:red">*</strong>:</label>
                <input type="text" id="email-address" name="email" class="form_textField">
            </p>
            <p>   
                <label>Password<strong style="color:red">*</strong>:</label>
                <input type="password" id="password-new" name="password" class="form_textField">
            </p>
            <p>
                <label>Confirm Password<strong style="color:red">*</strong>:</label>
                <input type="password" id="password-confirm" name="confirm" class="form_textField">
            </p>
        </div>
        <div class="login-login">
            <span>
                <h4>Existing Customers</h4>
                <p>
                    If you are an existing customer. Please click the sign in below to checkout<br><br>
                <div id="checkout">
                    <div id="login">
                        <label>E-Mail:</label>
                        <input type="text" value="" name="email">
                        <label>Password:</label>
                        &nbsp;&nbsp;&nbsp;&nbsp; <input type="password" value="" name="password">
                        <br><br>
                        <input type="button" class="button" id="button-login" value="Login">
                    </div>
                </div>
                </p>
            </span>
        </div>
    </div>
    <?php } ?>

    <div class="clearfix"></div>
    <div id="cart-2">
        <div class="cart-2-Formbox" id="cart-2-Formbox">
            <div class="Frombox-left">
                
                <h2><?php echo $text_heading_billing; ?></h2>
                <form class="form-horizontal" id="billing-information">
                    <?php if ($addresses) { ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="payment_address" value="existing" checked="checked" />
                            <?php echo $text_address_existing; ?></label>
                    </div>
                    <div id="payment-existing">
                        <select name="payment_address_id" class="form-control">
                            <?php 
                            
                            foreach ($addresses as $address) { ?>
                            <?php if( (isset($payment_address['address_id']))  && ($address['address_id'] == $payment_address['address_id'])) { ?>
                            <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="payment_address" value="new" />
                            <?php echo $text_address_new; ?></label>
                    </div>
                    <?php } ?>
                    <br />
                    <div id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-firstname"><?php echo $entry_firstname; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="firstname" value="" placeholder="<?php echo $entry_firstname; ?>" id="input-payment-firstname" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-lastname"><?php echo $entry_lastname; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="lastname" value="" placeholder="<?php echo $entry_lastname; ?>" id="input-payment-lastname" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-payment-company"><?php echo $entry_company; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="company" value="" placeholder="<?php echo $entry_company; ?>" id="input-payment-company" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-address-1"><?php echo $entry_address_1; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="address_1" value="" placeholder="<?php echo $entry_address_1; ?>" id="input-payment-address-1" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-payment-address-2"><?php echo $entry_address_2; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="address_2" value="" placeholder="<?php echo $entry_address_2; ?>" id="input-payment-address-2" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-city"><?php echo $entry_city; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="city" value="" placeholder="<?php echo $entry_city; ?>" id="input-payment-city" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-postcode"><?php echo $entry_postcode; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="postcode" value="" placeholder="<?php echo $entry_postcode; ?>" id="input-payment-postcode" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-country"><?php echo $entry_country; ?></label>
                            <div class="col-sm-10">
                                <select name="country_id" id="input-payment-country" class="form-control">
                                    <option value=""><?php echo $text_select; ?></option>
                                    <?php foreach ($countries as $country) { ?>
                                    <?php if ($country['country_id'] == $country_id) { ?>
                                    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-payment-zone"><?php echo $entry_zone; ?></label>
                            <div class="col-sm-10">
                                <select name="zone_id" id="input-payment-zone" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>

                </form>

                
            </div>
            <div class="center-seprater"></div>
            <?php if ($shipping_required) { ?>
            <div class="Frombox-right">
                <h2><?php echo $text_heading_shipping; ?> </h2>
                <form class="form-horizontal" id="shipping-information">
                    <?php if ($addresses) { ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="shipping_address" value="existing" checked="checked" />
                            <?php echo $text_address_existing; ?></label>
                    </div>
                    <div id="shipping-existing">
                        <select name="shipping_address_id" class="form-control">
                            <?php foreach ($addresses as $address) { ?>
                            <?php if( (isset($shipping_address['address_id']))  && ($address['address_id'] == $shipping_address['address_id'])) { ?>
                            <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="shipping_address" value="new" />
                            
                            <?php echo $text_address_new; ?></label>
                    </div>
                    <?php } ?>
                    <br />
                    <div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-firstname"><?php echo $entry_firstname; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_firstname" value="" placeholder="<?php echo $entry_firstname; ?>" id="input-shipping-firstname" class="form-control" />
                            </div>                       
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-lastname"><?php echo $entry_lastname; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_lastname" value="" placeholder="<?php echo $entry_lastname; ?>" id="input-shipping-lastname" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-shipping-company"><?php echo $entry_company; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_company" value="" placeholder="<?php echo $entry_company; ?>" id="input-shipping-company" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-address-1"><?php echo $entry_address_1; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_address_1" value="" placeholder="<?php echo $entry_address_1; ?>" id="input-shipping-address-1" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-shipping-address-2"><?php echo $entry_address_2; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_address_2" value="" placeholder="<?php echo $entry_address_2; ?>" id="input-shipping-address-2" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-city"><?php echo $entry_city; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_city" value="" placeholder="<?php echo $entry_city; ?>" id="input-shipping-city" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-postcode"><?php echo $entry_postcode; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_postcode" value="" placeholder="<?php echo $entry_postcode; ?>" id="input-shipping-postcode" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-country"><?php echo $entry_country; ?></label>
                            <div class="col-sm-10">
                                <select name="shipping_country_id" id="input-shipping-country" class="form-control">
                                    <option value=""><?php echo $text_select; ?></option>
                                    <?php foreach ($countries as $country) { ?>
                                    <?php if ($country['country_id'] == $country_id) { ?>
                                    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-shipping-zone"><?php echo $entry_zone; ?></label>
                            <div class="col-sm-10">
                                <select name="shipping_zone_id" id="input-shipping-zone" class="form-control">
                                </select>
                            </div>
                        </div>

                    </div>

                </form>       

            </div>
            <?php } ?>
            <div class="clear"></div>

        </div>
        <div class="payment">
            <h5><?php echo $text_payment_info; ?></h5>
            <ul class="clearfix">
                <li id="li-1"  data-index="credit" data-place_order="place-order-btn"  class="payments_method"><a href="javascript:void(0);"><?php echo $text_credit_cart;  ?></a></li>
                <li id="li-2"  data-index="paypal" data-place_order="paypal-place-order-btn"   class="payments_method active"><a href="javascript:void(0);"><?php echo $text_paypal; ?></a></li>
            </ul>
            <!--input type="hidden" id="cod" value="cod" name="payment_method">-->
            <input type="hidden" id="custom_payment_method" value="credit" name="custom_payment_method">
            <?php if($payment_methods) { ?>
            <div class="" style="display:none">
                <?php foreach ($payment_methods as $payment_method) { ?>
                <?php if ($payment_method['code'] == $code || !$code) {
                $code = $payment_method['code'] ?>
                <input type="radio" class="payment_method_div" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="radio" class="payment_method_div" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
                <?php }  ?>
                <?php } ?>
            </div>

            <?php } else{ ?>
            <?php } ?>

            <div id="payment-div1" class="payment_method_content payment-form">

                <form>
                    <p>
                        <label><?php echo $text_credit_card_type;?> <strong> * </strong> :</label>
                        <select name="card_type" class="credit">
                            <option value="visa">Visa</option>
                            <option value="visa-debit">Visa Debit</option>
                            <option value="mastercard">MasterCard</option>
                            <option value="mastercard-debit">MasterCard Debit</option>
                            <option value="discover">Discover</option>
                            <option value="american-express">American Express</option>
                        </select>
                    </p>
                    <p class="clearfix">
                        <label><?php echo $text_credit_card_number;?><strong> * </strong> : </label>
                        <span id="credit_cart_number">
                            <input type="text" name="card_no">
                        </span>
                    </p>
                    <p class="clearfix">
                        <label><?php echo $text_credit_card_verification; ?><strong> * </strong> : </label>
                        <span id='cvv'>
                            <input type="text" maxlength="4" name="cvv">
                        </span>
                    </p>
                    <?php $months = array(
                    1 => '(01) January',  2 => '(02) February',3 => '(03) March',
                    4 => '(04) April',   5 => '(05) May',     6 => '(06) June',
                    7 => '(07) July',    8 => '(08) August',    9 => '(09) September',
                    10 => '(10) October', 11 => '(11) November',12 => '(12) December'); ?>
                    <p class="clearfix">
                        <label><?php echo $text_credit_card_expires; ?> <strong> * </strong> : </label>
                        <span id="validity">
                            <select name="card_expiry_month">
                                <?php foreach($months as $key_month=>$month) { ?>
                                <option value="<?php echo $key_month; ?>"><?php echo $month; ?></option>
                                <?php } ?>
                            </select>
                            <?php //echo date("Y")+15; ?>
                            <select name="card_expiry_year">
                                <?php for($i = date("Y") ; $i < date("Y")+16; $i++ ) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </span>
                    </p>
                </form>
            </div>   
            <div id="payment-div2" class="payment_method_content pay-pall">
                <center>Click here to use PayPal or Bill Me Later:<br><br>
                    Your discount code and discount will not show when you check out with PayPal, but it will be applied when payment is processed so that you receive the correct price. Thank you for your business and for using PayPal and Bill Me Later.</center><br><div class="paypal-loader">
                    <center>

                        <input class="paypal-place-order-btn  order-buttons" type="submit" />

                    </center><span class="loading_id"></span></div>
            </div>


        </div>
        <div>

            <div class="comment">
                <h5>Your Comments<span>(Optional)</span> </h5>  
                <textarea maxlength="300" style="width: 100%;" rows="4" name="comment"></textarea>
            </div> 
            <div class="place-order" style="display:none">
                <div class="coupon-code">
                    <?php if ($error_warning_shipping) { ?>
                    <div class="warning-custom-module"><?php echo $error_warning_shipping; ?></div>
                    <?php } ?>
                    <div id="shipping-method-div">

                        <?php if ($shipping_methods) { ?>
                        <label> Please select the preferred shipping method to use on this order.</label>
                        <table class="radio">
                            <?php $i=0; foreach ($shipping_methods as $shipping_method) { ?>

                            <?php if (!$shipping_method['error']) { ?>
                            <?php foreach ($shipping_method['quote'] as $quote) { ?>
                            <tr class="highlight">
                                <td><?php if ($quote['code'] == $code || !$code) { ?>
                                    <?php $code = $quote['code']; ?>
                                    <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="radio" <?php if($i==0) { echo 'checked="checked"'; } ?> name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
                                           <?php } ?></td>
                                <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
                                <td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
                            </tr>
                            <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
                            </tr>
                            <?php } ?>
                            <?php $i++; } ?>
                        </table>
                        <br />
                        <?php } ?></div>

                </div>

                <div class="clear"></div>        
            </div>
            <div class="clear"></div>
            <div class="btn clearfix">
                <span class="right-arw"><input type="submit" class="place-order-btn order-buttons btn btn-primary" value="Place your order" /></span><a class="" href="<?php echo $shopping_cart; ?>">
                    Back to cart</a>
                <span class="loading_id"></span>

            </div>
        </div>
    </div>

    <div id="get-cart-content"></div>
    <div id="put-cart"></div>
    <?php echo $content_bottom; ?></div>

<?php if($payment_methods) { ?>
<script type="text/javascript">
    $('input[name=\'payment_method\']').live('click', function() {

        $.ajax({
            url: 'index.php?route=payment/' + $(this).val(),
            dataType: 'html',
            beforeSend: function() {

                $('.place-order-btn').attr('disabled', true);
                $('#payment-div').html('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.place-order-btn').attr('disabled', false);

            },
            success: function(html) {

                $('#payment-div').html(html);


            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });
    $('input[name=\'payment_method\']:first').trigger('click');
</script>
<?php } ?>

<script type="text/javascript"><!--
$('#billing-information select[name=\'country_id\']').bind('change', function() {
        $.ajax({
            url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
                $('#billing-information select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.wait').remove();
            },
            success: function(json) {
                if (json['postcode_required'] == '1') {
                    $('#payment-postcode-required').show();
                } else {
                    $('#payment-postcode-required').hide();
                }

                html = '<option value=""><?php echo $text_select; ?></option>';
                if ((typeof json['zone'] !== 'undefined') && json['zone'] != '') {
                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';
                        if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                            html += ' selected="selected"';
                        }

                        html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                }

                $('#billing-information select[name=\'zone_id\']').html(html).trigger('change');
                setpaymentbilling();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $('#billing-information select[name=\'zone_id\']').bind('change', function() {
        //if (this.value == '')
        //  return;
        $.ajax({
            url: 'index.php?route=checkout/view_checkout/setPaymentAddress',
            type: 'post',
            data: $("#billing-information").serialize(),
            dataType: 'html',
            beforeSend: function() {
                $('.order-buttons').attr('disabled', true);
            },
            complete: function() {
                $('.' + ($('.payments_method:not(".active")').data('place_order'))).attr('disabled', false);
            },
            success: function(html) {
                $('#put-cart').trigger('click');

            }

        });
        setpaymentbilling();

    });
    function setpaymentbilling()
    {

    }
    $('#billing-information input[type=\'text\']').bind('blur', function() {
        $('#billing-information select[name=\'zone_id\']').html(html).trigger('change');
    }); 
    $('#billing-information select[name=\'country_id\']').trigger('change');
//--></script>
<?php  if ($shipping_required) { ?>
<script type="text/javascript">

    $('#shipping-information select[name=\'shipping_zone_id\']').bind('change', function() {
        setpaymentshipping();

    });

    function setpaymentshipping()
    {
        $.ajax({
            url: 'index.php?route=checkout/view_checkout/setShippingAddress',
            type: 'post',
            data: $('form#shipping-information').serialize(), // $('.address-options #shipping-address-new, #shipping-information #is_shipping   , #shipping-information select[name=\'shipping_country_id\'],#shipping-information select[name=\'shipping_zone_id\']'),
            dataType: 'html',
            beforeSend: function() {
                $('.order-buttons').attr('disabled', true);
            },
            complete: function() {
                $('.' + ($('.payments_method:not(".active")').data('place_order'))).attr('disabled', false);
            },
            success: function(html) {
                $('#put-cart').trigger('click');

            }

        });
    }

    $('#shipping-information select[name=\'shipping_country_id\']').bind('change', function() {
        $.ajax({
            url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
                $('.place-order-btn').attr('disabled', true);
                $('#shipping-information select[name=\'shipping_country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.place-order-btn').attr('disabled', false);
                $('.wait').remove();
            },
            success: function(json) {
                if (json['postcode_required'] == '1') {
                    $('#payment-postcode-required').show();
                } else {
                    $('#payment-postcode-required').hide();
                }

                html = '<option value=""><?php echo $text_select; ?></option>';
                if ((typeof json['zone'] !== 'undefined') && json['zone'] != '') {

                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';
                        if (json['zone'][i]['zone_id'] == '<?php echo $shipping_zone_id; ?>') {
                            html += ' selected="selected"';
                        }

                        html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                }

                $('#shipping-information select[name=\'shipping_zone_id\']').html(html);
                $("select[name=\'shipping_zone_id\']").val($("select[name=\'zone_id\']").val());
                setpaymentshipping();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    function get_ship_country_zones(country_id) {

        if (country_id.val() == '')
            return;
        $.ajax({
            url: 'index.php?route=checkout/checkout/country&country_id=' + country_id.val(),
            dataType: 'json',
            beforeSend: function() {
                $('.order-buttons').attr('disabled', true);
                $('#shipping-information select[name=\'shipping_country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.' + ($('.payments_method:not(".active")').data('place_order'))).attr('disabled', false);
                $('.wait').remove();
            },
            success: function(json) {

                if (json['postcode_required'] == '1') {
                    $('#payment-postcode-required').show();
                } else {
                    $('#payment-postcode-required').hide();
                }

                html = '<option value=""><?php echo $text_select; ?></option>';
                if (json['zone'] && json['zone'] != '') {

                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';
                        if (json['zone'][i]['zone_id'] == '<?php echo $shipping_zone_id; ?>') {
                            html += ' selected="selected"';
                        }

                        html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                } else {
                    html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
                }

                $('#shipping-information select[name=\'shipping_zone_id\']').html(html);
                $("select[name=\'shipping_zone_id\']").val($("select[name=\'zone_id\']").val());
                if ($("#shipping_method").is(':checked')) {

                } else {
                    $('#shipping_method').trigger('click');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    get_ship_country_zones($('#shipping-information select[name=\'shipping_country_id\']'));
</script>
<?php  if(!$logged){ ?>
<script type="text/javascript"><!--
        $('#shipping-information select[name=\'shipping_zone_id\']').trigger('change');
//--></script> 
<?php } ?>
<?php } ?>
<script>
    $('.place-order-btn, .paypal-place-order-btn, .afirm-submit_btn').live('click', function(e) {

        e.preventDefault();
        var cur_loader = $($(this).parent().siblings('.loading_id'));
        var order_payment_method = $(this);

        $.ajax({
            url: 'index.php?route=checkout/custom_validation/signUpValidate',
            type: 'post',
            data: 'email=' + $('#sign-up input[name=\'email\']').val() + '&password=' + $('#sign-up input[name=\'password\']').val() + '&confirm=' + $('#sign-up input[name=\'confirm\']').val(),
            dataType: 'json',
            async: true,
            beforeSend: function() {
                order_payment_method.attr('disabled', true);
                cur_loader.append('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {


            },
            success: function(json) {
                $('.success, .warning, .attention, .error').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    $('.custom-wait').remove();
                    order_payment_method.attr('disabled', false);
                    if (json['error']['warning']) {
                        $('#payment-address .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                        $('.warning').fadeIn('slow');
                    }

                    if (json['error']['email']) {
                        $('#sign-up input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
                    }
                    if (json['error']['password']) {
                        $('#sign-up input[name=\'password\']').after('<span class="error">' + json['error']['password'] + '</span>');
                    }
                    if (json['error']['confirm']) {
                        $('#sign-up input[name=\'confirm\']').after('<span class="error">' + json['error']['confirm'] + '</span>');
                    }
                    var el = $('.error');
                    $('html, body').animate({
                        scrollTop: (el.offset().top - 80)
                    }, 500);

                } else {
                    $.ajax({
                        url: 'index.php?route=checkout/custom_validation/registerValidate',
                        type: 'post',
                        data: $('.login-sign-up input[type=\'text\'],.login-sign-up input[type=\'password\'],.payment-form input[type=\'text\'],.payment-form select,input[name=\'payment_method\']:checked,input[name=\'custom_payment_method\'] ,.coupon-code input[type=\'radio\']:checked,.coupon-code input[type=\'checkbox\']:checked,#cart-2-Formbox input[type=\'text\'], #cart-2-Formbox input[type=\'checkbox\']:checked, #cart-2-Formbox input[type=\'radio\']:checked, #cart-2-Formbox select, #cart-2-Formbox input[type=\'hidden\'], .comment textarea'),
                        dataType: 'json',
                        async: true,
                        beforeSend: function() {
                            order_payment_method.attr('disabled', true);
                            cur_loader.html('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
                        },
                        complete: function() {

                        },
                        success: function(json) {
                            $('.success, .warning, .attention, .error').remove();
                            if (json['redirect']) {
                                location = json['redirect'];
                            } else if (json['error']) {

                                $('.custom-wait').remove();
                                order_payment_method.attr('disabled', false);
                                if (json['error']['warning']) {
                                    $('#content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                                    $('.warning').fadeIn('slow');
                                    $('html, body').animate({
                                        scrollTop: $('.warning').offset().top - 50
                                    });
                                }

                                if (json['error']['firstname']) {
                                    $('#billing-information input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
                                }
                                if (json['error']['lastname']) {
                                    $('#billing-information input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
                                }
                                if (json['error']['telephone']) {
                                    $('#billing-information input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
                                }
                                if (json['error']['email']) {
                                    $('#billing-information input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
                                }
                                if (json['error']['address_1']) {
                                    $('#billing-information input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
                                }
                                if (json['error']['city']) {
                                    $('#billing-information input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
                                }
                                if (json['error']['zone']) {
                                    $('#billing-information select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
                                }

                                if (json['error']['country']) {
                                    $('#billing-information select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
                                }

                                if (json['error']['postcode']) {
                                    $('#billing-information input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
                                }

                                if (json['error']['shipping_firstname']) {
                                    $('#shipping-information input[name=\'shipping_firstname\']').after('<span class="error">' + json['error']['shipping_firstname'] + '</span>');
                                }

                                if (json['error']['shipping_lastname']) {
                                    $('#shipping-information input[name=\'shipping_lastname\']').after('<span class="error">' + json['error']['shipping_lastname'] + '</span>');
                                }

                                if (json['error']['shipping_country']) {
                                    $('#shipping-information select[name=\'shipping_country_id\']').after('<span class="error">' + json['error']['shipping_country'] + '</span>');
                                }
                                if (json['error']['shipping_zone']) {
                                    $('#shipping-information select[name=\'shipping_zone_id\']').after('<span class="error">' + json['error']['shipping_zone'] + '</span>');
                                }


                                if (json['error']['shipping_postcode']) {
                                    $('#shipping-information input[name=\'shipping_postcode\']').after('<span class="error">' + json['error']['shipping_postcode'] + '</span>');
                                }

                                if (json['error']['shipping_city']) {
                                    $('#shipping-information input[name=\'shipping_city\']').after('<span class="error">' + json['error']['shipping_city'] + '</span>');
                                }
                                if (json['error']['shipping_address_1']) {
                                    $('#shipping-information input[name=\'shipping_address_1\']').after('<span class="error">' + json['error']['shipping_address_1'] + '</span>');
                                }
                                $('.payment-error-div').html('');
                                if (json['error']['card']) {
                                    $('#credit_cart_number').append('<span class="error" id="error-card">' + json['error']['card'] + '</span>');
                                }
                                if (json['error']['cvv']) {
                                    $('#cvv').append('&nbsp;<span class="error" id="error-cvv">' + json['error']['cvv'] + '</span>');
                                }
                                if (json['error']['validity']) {
                                    $('#validity').append('&nbsp;<span class="error" id="error-validity">' + json['error']['validity'] + '</span>');
                                }
                                if (json['error']['password']) {
                                    location = 'index.php?route=checkout/view_checkout';

                                }
                                var el = $('.error');
                                if (el.length)
                                {
                                    $('html, body').animate({
                                        scrollTop: (el.offset().top - 50)
                                    }, 500);
                                }

                            } else {
                                
                                $.ajax({
                                    url: 'index.php?route=checkout/confirm',
                                    //dataType: 'json',
                                    async: false,
                                    beforeSend: function() {
                                        order_payment_method.attr('disabled', true);
                                        cur_loader.html('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /> </span>');
                                    },
                                    complete: function() {

                                    },
                                    success: function(json) {
                                        
                                        if (json['redirect']) {
                                            location = json['redirect'];
                                        } else {
                                           
                                            //if (order_payment_method.hasClass('place-order-btn'))
                                            //{
                                               // location = 'index.php?route=checkout/custom_success';
                                                $('#get-cart-content').prepend(json);
                                                $('#button-confirm').trigger('click');
                                            //}
                                            /*if (order_payment_method.hasClass('paypal-place-order-btn'))
                                            {
                                                $('.pay-pall').append(json['payment']);
                                                $('.pay-pall .button').trigger('click');
                                            }
                                            if (order_payment_method.hasClass('afirm-submit_btn'))
                                            {
                                                $('.affirm').append(json['payment']);
                                                affirm.checkout.post();
                                            } */
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });
    });

    $('#is_shipping').live('click', function() {

        if ($(this).is(':checked')) {
            $('.address_details').each(function(i, obj) {
                $("input[name=\'shipping_" + obj.name + "\']").val(obj.value);
            });
            $("select[name=\'shipping_country_id\']").val($("select[name=\'country_id\']").val());
            $('#shipping-information select[name=\'shipping_country_id\']').trigger('change');
        } else {
            $('.shipping_address_details').val('');
            setpaymentshipping();
        }
    });


    $('#put-cart').live('click', function() {
        //var p_id = $("#p_id").val();

        $.ajax({
            //url: 'index.php?route=checkout/view_checkout/getCart&p_id='+p_id,
            url: 'index.php?route=checkout/view_checkout/getCart',
            dataType: 'html',
            beforeSend: function() {

                $('#put-cart').html('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            success: function(html) {
                $('#put-cart').html('');
                $('#get-cart-content').html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    $('#put-cart').trigger('click');

    $('#coupon-checkbox').live('click', function() {

        if ($(this).is(':checked')) {
            $("#coupon-div").slideDown('slow');
        } else {

            $("#coupon-div").slideUp('slow');
        }
    });


    $('#validate-coupon').live('click', function() {


        $.ajax({
            url: 'index.php?route=checkout/view_checkout/apllyCoupon',
            type: 'post',
            data: $('#coupon-div input[type=\'text\']'),
            dataType: 'json',
            beforeSend: function() {
                $('#validate-coupon').attr('disabled', true);
                $('#validate-coupon').after('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('#validate-coupon').attr('disabled', false);
                $('.custom-wait').remove();
            },
            success: function(json) {

                $('.success, .warning, .attention, .error').remove();
                if (json['sucess']) {
                    $('#validate-coupon').after('<span class="success">' + json['sucess'] + '</span>');
                    $('#put-cart').trigger('click');
                }
                if (json['error']) {
                    $('#validate-coupon').after('<span class="error">' + json['error'] + '</span>');
                    $('#put-cart').trigger('click');
                }
            }

        });


    });
</script>
<script type="text/javascript"><!--
$('input[name=\'payment_address\']').live('change', function() {
        if (this.value == 'new') {
            $('#payment-existing').slideUp('slow');
            $('#payment-new').slideDown('slow');
            $('#billing-information select[name=\'country_id\']').trigger('change');
        } else {
            $('#payment-existing').slideDown('slow');
            $('#payment-new').slideUp('slow');
            $('#payment-existing select[name=\'payment_address_id\']').trigger('change');

        }
    });
//--></script> 

<script type="text/javascript"><!--
$('input[name=\'shipping_address\']').live('change', function() {
        if (this.value == 'new') {
            $('#shipping-existing').slideUp('slow');
            $('#shipping-new').slideDown('slow');
            $('#shipping-information select[name=\'shipping_country_id\']').trigger('change');
        } else {
            $('#shipping-existing').slideDown('slow');
            $('#shipping-new').slideUp('slow')
            $('#shipping-existing select[name=\'shipping_address_id\']').trigger('change');
        }
    });
//--></script> 


<?php if($free_shipping_method) { ?>

<?php if(isset($this->session->data['shipping_method']) && $this->session->data['shipping_method']['code']!='free.free'){ ?>
<script type="text/javascript"><!--
$('#shipping_method').attr('checked', false);
//--></script> 
<?php } ?>

<script type="text/javascript"><!--
$('#shipping_method').live('click', function() {
        if ($(this).is(':checked')) {
            $('#shipping-method-div').slideUp('slow');
            $('#shipping-method-div').html('');
        } else {

            $.ajax({
                url: 'index.php?route=checkout/cart_custom_shipping_method',
                type: 'post',
                data: $('#shipping-information select,input[name=\shipping_address\]:checked'),
                dataType: 'html',
                beforeSend: function() {
                    $('.place-order-btn').attr('disabled', true);
                    $('#shipping_method').attr('disabled', true);
                    $('#shipping-method-div').html('<span class="custom-wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
                    $('#shipping-method-div').slideDown('slow');
                },
                complete: function() {
                    $('#shipping_method').attr('disabled', false);
                    $('.place-order-btn').attr('disabled', false);

                },
                success: function(html) {

                    $('.success, .warning, .attention, .error').remove();
                    $('#shipping-method-div').html(html);

                }

            });
        }
    });
//--></script> 
<?php } ?>

<script>
    $('#payment-existing select[name=\'payment_address_id\']').bind('change', function() {

        $.ajax({
            url: 'index.php?route=checkout/view_checkout/setPaymentAddress',
            type: 'post',
            data: $('#payment-existing select, #billing-information input[name=\"payment_address\"]:checked'),
            dataType: 'html',
            beforeSend: function() {
                $('.place-order-btn').attr('disabled', true);

            },
            complete: function() {

                $('.place-order-btn').attr('disabled', false);

            },
            success: function(html) {

                $('#put-cart').trigger('click');
            }

        });
    });
    $('#shipping-existing select[name=\'shipping_address_id\']').bind('change', function() {

        $.ajax({
            url: 'index.php?route=checkout/view_checkout/setShippingAddress',
            type: 'post',
            data: $('#shipping-existing select, #shipping-information input[name=\"shipping_address\"]:checked'),
            dataType: 'html',
            beforeSend: function() {
                $('.place-order-btn').attr('disabled', true);

            },
            complete: function() {

                $('.place-order-btn').attr('disabled', false);

            },
            success: function(html) {


                if ($("#shipping_method").is(':checked')) {

                } else {
                    $('#shipping_method').trigger('click');
                }
                $('#put-cart').trigger('click');

            }

        });
    });
</script>
<script>


    $('.payments_method').live('click', function() {
        $('#custom_payment_method').val($(this).data('index'));
        $('.payment_method_div:eq(' + $('.payment li').index(this) + ')').prop('checked', true);
        //$("#payment-div" + ($('.payment li').index(this) + 1)).show();
        $(".payments_method").addClass("active");
        $(this).removeClass("active");
        $(".payment_method_content").hide();

        $("#payment-div" + ($('.payment li').index(this) + 1)).show();
        $('.order-buttons').attr('disabled', true);
        $('.' + ($('.payments_method:not(".active")').data('place_order'))).attr('disabled', false);

    });

    $(document).ready(function() {
        $('.payment_method_content').not($('.payment_method_content').first()).hide();
    });

// Login
    $('#button-login').live('click', function() {
        $.ajax({
            url: 'index.php?route=checkout/login/save',
            type: 'post',
            data: $('#checkout #login :input'),
            dataType: 'json',
            beforeSend: function() {
                $('#button-login').attr('disabled', true);
                $('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('#button-login').attr('disabled', false);
                $('.wait').remove();
            },
            success: function(json) {
                $('.warning, .error').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#login input[name=\'password\']').after('<span class="error">' + json['error']['warning'] + '</span>');
                    }
                    //$('#checkout .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
                    //$('.warning').fadeIn('slow');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
</script>
<?php echo $footer; ?>
