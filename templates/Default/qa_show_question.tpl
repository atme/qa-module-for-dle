{title}<br>
{text}<br>

<form method="POST">
[error-text]
<div style="color:red">��������� ���� �����������.</div>
[/error-text]
������� �����������: <textarea name="text">{text-comment}</textarea><br>
[error-captcha]
<div style="color:red">������ �� �����.</div>
[/error-captcha]
[captcha]
<img src="/engine/modules/antibot.php"><br>
<input type="text" name="captcha"><br>
[/captcha]
<input type="submit" name="add_comment" value="�������� �����������">
</form>
{comments}