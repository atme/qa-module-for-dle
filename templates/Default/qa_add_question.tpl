<form action="/qa/add/" method="POST">
�������� ���������: <select name="category">{categories}</select><br>
[error-title]
<div style="color:red">��������� ���� ���������.</div>
[/error-title]
������� ���������: <input name="title" type="text" value="{title}"><br>
[error-text]
<div style="color:red">��������� ���� �������.</div>
[/error-text]
������� ����� �������: <textarea name="text">{text}</textarea><br>
[error-captcha]
<div style="color:red">������ �� �����.</div>
[/error-captcha]
[captcha]
<img src="/engine/modules/antibot.php"><br>
<input type="text" name="captcha"><br>
[/captcha]
<input type="submit" name="add_question" value="�������� ������">
</form>