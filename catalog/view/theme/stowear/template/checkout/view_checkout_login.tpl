<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div id="sign-up" class="col-sm-6 login-sign-up"> 
                    <h2><?php echo $text_new_customer; ?></h2>
                    <p><?php echo $text_register; ?></p>
                    <div class="form-group">
                      <label class="control-label" for="input-email-address"><?php echo $entry_email; ?></label>
                      <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email-address" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-password-new"><?php echo $entry_password; ?></label>
                      <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-password-new" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-password-confirm"><?php echo $entry_confirm; ?></label>
                      <input type="password" name="confirm" value="" placeholder="<?php echo $entry_confirm; ?>" id="input-password-confirm" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-6 login-section">
                    <h2><?php echo $text_returning_customer; ?></h2>
                    <p><?php echo $text_i_am_returning_customer; ?></p>
                    <div class="form-group">
                      <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                      <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                      <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
                      <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
                    </div>
                    <input type="button" value="<?php echo $button_login; ?>" id="button-login" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

