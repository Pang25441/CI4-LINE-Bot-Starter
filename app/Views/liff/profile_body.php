<form class="form" action="" method="post">
  <div class="col-sm-12 col-md-6 col-lg-4 m-auto">
  <h2>
    <?php echo $isMember ? 'My Profile' : 'Register'; ?>
  </h2>
    <div class="form-group">
      <label for="firstname">First Name:</label>
      <input id="firstname" name="firstname" placeholder="First Name" type="text" required="required" class="form-control" autocomplete="off" autocapitalize="words" value="<?php echo set_value('firstname', $profile['firstname']); ?>">
    </div>
    <div class="form-group">
      <label for="lastname">Last Name</label>
      <input id="lastname" name="lastname" placeholder="Last Name" type="text" class="form-control" required="required" autocomplete="off" autocapitalize="words" value="<?php echo set_value('firstname', $profile['lastname']); ?>">
    </div>
    <div class="form-group">
      <label for="email">E-Mail</label>
      <input id="email" name="email" placeholder="E-mail" type="text" class="form-control" autocomplete="off" value="<?php echo set_value('firstname', $profile['email']); ?>">
    </div>
    <div class="form-group">
      <button name="submit" type="submit" class="btn btn-primary">Register</button>
    </div>
  </div>
</form>