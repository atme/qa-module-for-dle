<form action="/qa/add/" method="POST">
Выберите категорию: <select name="category">{categories}</select><br>
[error-title]
<div style="color:red">Заполните поле заголовка.</div>
[/error-title]
Введите заголовок: <input name="title" type="text" value="{title}"><br>
[error-text]
<div style="color:red">Заполните поле вопроса.</div>
[/error-text]
Ввыдите текст вопроса: <textarea name="text">{text}</textarea><br>
[error-captcha]
<div style="color:red">Каптча не верна.</div>
[/error-captcha]
[captcha]
<img src="/engine/modules/antibot.php"><br>
<input type="text" name="captcha"><br>
[/captcha]
<input type="submit" name="add_question" value="Добавить вопрос">
</form>