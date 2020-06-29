<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Register</title>
</head>
<body>
    <div class="container-fluid">
        <form class="form" action="" method="post">
            <div class="form-group">
                <label for="">First Name: </label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="<?php echo set_value('firstname', $profile['firstname']); ?>">
            </div>
        </form>
    </div>
</body>
</html>