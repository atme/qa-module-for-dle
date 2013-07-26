{title}<br>
{text}<br>

<form method="POST">
[error-text]
<div style="color:red">Заполните поле комментария.</div>
[/error-text]
Введите комментарий: <textarea name="text">{text-comment}</textarea><br>
[error-captcha]
<div style="color:red">Каптча не верна.</div>
[/error-captcha]
[captcha]
<img src="/engine/modules/antibot.php"><br>
<input type="text" name="captcha"><br>
[/captcha]
<input type="submit" name="add_comment" value="Добавить комментарий">
</form>
{comments}