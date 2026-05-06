<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <h1>liste etudiant</h1>
    <?php foreach( $lists as $list ):?>
        <li><?= $list ?></li>
        <?php endforeach ?>
</body>
</html>