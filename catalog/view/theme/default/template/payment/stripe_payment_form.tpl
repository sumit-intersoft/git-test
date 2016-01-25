<?php if ($testmode) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><?php echo $entry_text_testmode; ?></div>
<?php } ?>

<div id="credit-cart" > 

    <!--<div class="payment-vendor">
        <a href="#"><img src="catalog/view/theme/journal2/css/icons/step/visa.png"  /></a>
        <a href="#"><img src="catalog/view/theme/journal2/css/icons/step/mastercard.png"  /></a>
        <a href="#"><img src="catalog/view/theme/journal2/css/icons/step/amex.png"  /></a>
        <a href="#"><img src="catalog/view/theme/journal2/css/icons/step/discover.png"  /></a>
        <a href="#"><img src="catalog/view/theme/journal2/css/icons/step/lock.png"  /></a>
    </div>-->
    <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
            <div class="col-sm-10">
                <input type="text" value="" class="card-input" maxlength="4"   name="value1" class="form-control" />
                <input type="text" value="" class="card-input" maxlength="4"   name="value2" class="form-control" />
                <input type="text" value="" class="card-input" maxlength="4"   name="value3" class="form-control" />
                <input type="text" value="" class="card-input" maxlength="4"  name="value4" class="form-control" />
                <input type="text" value="" class="" maxlength="1"  name="" style="display: none;" />
                <div class="card-loader"></div>
                <div class="card-no">
                        <div calss="clear"></div>
                </div>
            </div>
    </div>
    
    


    <div class="form-group required">
        <label class="col-sm-2 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
        <div class="col-sm-3">
            <select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
                <?php foreach ($months as $month) { ?>
                <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-3">
            <select name="cc_expire_date_year" class="form-control">
                <?php foreach ($year_expire as $year) { ?>
                <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="form-group required">
        <label class="col-sm-2 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
        <div class="col-sm-10">
            <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control" />
        </div>
    </div>

</div>

<script>
    $(function() {
        $('.card-input').autotab({
           tabOnSelect: true, 
        });
        $('.card-input, #input-cc-cvv2').autotab('filter', 'number');
    })
</script>