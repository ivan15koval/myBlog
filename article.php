<?php
$label = 'id';
$id = false;
if (!empty($_GET[$label])) {
    $id = $_GET[$label];
}


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

$article = DB::query("SELECT * FROM articles WHERE id='$id'");


$num_page = 2;


//Получаем общее число сообщений
$result = DB::query("SELECT COUNT(*) AS numrows FROM comments WHERE id_a='$id'");
$total = $result->fetch_object()->numrows;
$per_page = $total;

$start_row = (!empty($_GET['p'])) ? intval($_GET['p']) : 0;
if ($start_row < 0) $start_row = 0;
if ($start_row > $total) $start_row = $total;


//Получаем список активных сообщений
$items = DB::query("SELECT * FROM comments WHERE id_a='$id' ORDER BY addtime DESC LIMIT " . $start_row . ',' . $per_page);


$now = time();
$antiflood = 120;//Время в секундах для блокировки повторной отправки сообщения

$errors = array();

$name = (!empty($_POST['name'])) ? trim(strip_tags($_POST['name'])) : false;
$text = (!empty($_POST['text'])) ? trim(strip_tags($_POST['text'])) : false;
// ANTIFLOOD
//if (!$antiflood || (!isset($_SESSION['time']) || $now - $antiflood >= $_SESSION['time']) )  {
if (isset($_POST['submit'])) {


    if (empty($name)) $errors[] = '<div class="error">Вы не заполнили поле "Представьтесь"!</div>';
    if (empty($text)) $errors[] = '<div class="error">Вы не заполнили поле "Текст"!</div>';
    //Если ошибок нет пишем отзыв в базу
    if (!$errors) {

        DB::query("INSERT INTO comments (id_a, name1,text) VALUES ('" . DB::esc($id) . "','" . DB::esc($name) . "','" . DB::esc($text) . "')");


        if (DB::getMySQLiObject()->affected_rows == 1) {
            $errors[] = '<div class="error">Ваш отзыв успешно добавлен!</div>';
        } else {
            $errors[] = '<div class="error">Ваш отзыв не добавлен. Попробуйте позже!</div>';
        }
    }
}


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
                <?php foreach ($article as $item): ?>
                    <div class="blog-post">
                        <h1 class="blog-post-title"><?= $item['name'] ?></h1>
                        <p class="blog-post-meta"><?= $item['data'] ?></p>
                        <div class=""><?= $item['content'] ?></div>
                    </div>
                <?php endforeach; ?>


            </div>
        </div>

    </div>

    <div class="add_com_block" id="add_com_block" style="display:<?= (!empty($errors)) ? 'block' : 'block' ?>;">
        <?= (!empty($errors)) ? '<div class="errors">' . implode($errors) . '</div>' : '' ?>
        <form action="article.php?id=<?= $id ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <label>Представьтесь:</label>
            <input class="text" name="name" value="<?= set_value('name'); ?>" type="text">
            <label>Сообщение:</label>
            <textarea cols="15" rows="5" name="text" id="com_text"><?= set_value('text'); ?></textarea>
            <div><input class="but" name="submit" value="Отправить" type="submit"></div>

            <!--            <input name="name" value="" type="hidden">-->
            <!--            <input name="text" value="" type="hidden">-->
            <input name="form" value="blog" type="hidden">
        </form>

    </div>


    <div class="comments-block">
        <?php if (!empty($items)):foreach ($items as $item): ?>
            <a name="comments-<?= $item['id'] ?>"></a>
            <div class="com-item-pad" id="com_<?= $item['id'] ?>">

                <div class="com-item">
                    <div class="user_info">
                        <div class="info_panel">
                            <div class="fl-left">
                                <strong><?= $item['name1'] ?></strong>
                                <span class="date"><?= $item['addtime'] ?></span>
                            </div>
                        </div>
                        <div class="com_body"><?= $item['text'] ?></div>
                    </div>
                </div>
            </div>
            <div id="com-form-wrap"></div>
        <?php endforeach; else: ?>
            <div class="com-item"><h2>На данный момент нет активных отзывов!</h2></div>
        <?php endif; ?>

    </div>

    <?= pagination($total, $per_page, $num_page, $start_row, "/article.php?id=$id") ?>

    <!--==============================footer=================================-->

</div>
<footer>
    <div class="footer">
        <p style="text-align: center">© Ковалёв Иван 2018</p>

    </div>
</footer>
</body>
</html>
