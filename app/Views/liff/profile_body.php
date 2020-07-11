<form class="form pt-2" action="" method="post" onsubmit="return submitForm()">
  <input type="hidden" id="accessToken" name="accessToken">
  <input type="hidden" id="id" name="profile[id]" value="<?php echo set_value('id', $profile['id']); ?>">
  <div class="col-sm-12 col-md-6 col-lg-4 m-auto">
    <h2>
      <?php echo $isMember ? lang('Liff.title.my_profile') : lang('Liff.title.register'); ?>
    </h2>
    <div class="form-group">
      <label for="firstname"><?php echo lang('Liff.label.firstname') ?>:</label>
      <input id="firstname" name="profile[firstname]" placeholder="<?php echo lang('Liff.label.firstname') ?>" type="text" required="required" class="form-control" autocomplete="off" autocapitalize="words" value="<?php echo set_value('firstname', $profile['firstname']); ?>">
    </div>
    <div class="form-group">
      <label for="lastname"><?php echo lang('Liff.label.lastname') ?></label>
      <input id="lastname" name="profile[lastname]" placeholder="<?php echo lang('Liff.label.lastname') ?>" type="text" class="form-control" required="required" autocomplete="off" autocapitalize="words" value="<?php echo set_value('lastname', $profile['lastname']); ?>">
    </div>
    <div class="form-group">
      <label for="email"><?php echo lang('Liff.label.email') ?></label>
      <input id="email" name="profile[email]" placeholder="<?php echo lang('Liff.label.email') ?>" type="email" class="form-control" autocomplete="off" value="<?php echo set_value('email', $profile['email']); ?>">
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col">
          <button name="submit" type="submit" class="btn btn-block btn-primary"><?php echo $isMember ? lang('Liff.btn.update') : lang('Liff.btn.register'); ?></button>
        </div>
        <div class="col">
          <button type="button" class="btn btn-block btn-secondary" onclick="closeLiff()"><?php echo lang('Liff.btn.cancel') ?></button>
        </div>
      </div>
    </div>
  </div>
</form>