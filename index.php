<?php

session_start();

/* ???????????? ???? ??????. ???????? ???? ?????? */
$dbOptions = array(
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'blog'
);

require "DB.class.php"; //?????????? ????? ??? ?????? ? ????? ??????
require "helper.php"; //?????????? ??????????????? ???????


// ?????????? ? ????? ??????
DB::init($dbOptions);

//$appath = realpath(dirname(__FILE__)).'/';
$uploaddir = 'images/avatars'; //????? ?? ???????, ???? ????? ??????????? ????????
$per_page = 10; //???????????? ????? ????????? ?? ????? ????????
$num_page = 2;


//???????? ????? ????? ?????????
$result = DB::query('SELECT COUNT(*) AS numrows FROM articles');
$total = $result->fetch_object()->numrows;

$start_row = (!empty($_GET['p'])) ? intval($_GET['p']) : 0;
if ($start_row < 0) $start_row = 0;
if ($start_row > $total) $start_row = $total;

//Получаем список активных сообщений
$items = DB::query('SELECT * FROM articles ORDER BY data DESC LIMIT ' . $start_row . ',' . $per_page);

$errors = array();


?>

<!DOCTYPE html >
<html>
<head>
    <title>Блог</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
<div id="centered">
    <!--==============================header=================================-->
    <div class="panel">
        <h1><a href="index.php">Мой блог</a></h1>
    </div>

    <!--==============================content================================-->
    <div class="container">
        <div class="content">
            <div class="comments-block">
                <?php if (!empty($items)):foreach ($items as $item): ?>
                    <div class="blog-post">
                        <a href="article.php?id=<?= $item['id'] ?>"><h1
                                    class="blog-post-title"><?= $item['name'] ?></h1></a>
                        <p class="blog-post-meta"><?= $item['data'] ?></p>
                        <div class=""><?= substr($item['content'], 0, 300) ?></div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="com-item"><h2>Статей нет</h2></div>
                <?php endif; ?>

            </div>
        </div>
        <?= pagination($total, $per_page, $num_page, $start_row, '/index.php') ?>


    </div>
    <!--==============================footer=================================-->
    <footer>

    </footer>
</div>
</body>
</html>

