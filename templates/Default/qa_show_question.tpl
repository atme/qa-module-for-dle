<div id="question_{id}">{title}<br>
{text}<br>
[admin]
<textarea class="title">{title}</textarea><br>
<textarea class="text">{text}</textarea><br>
<a href="javascript:void(0)" class="edit_question">Редактировать</a> 
<a href="javascript:void(0)" class="remove_question">Удалить</a>
[/admin]
</div>

<form method="POST">
[error-text]
<div style="color:red">Поле коментария не заполнено.</div>
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
<script>
$('.remove_comment').click(function () {
	var id = $(this).parent().attr('id');
	id = /^comment_(\d+)$/.exec(id);
	var comment_id = id[1];
	if(confirm('Удалить комментарий?'))
	{
		var pathname = $(location).attr('href');
		post_to_url(pathname, {admin_action: 'delete_comment', id: comment_id});
	}
});

$('.remove_question').click(function () {
	var id = $(this).parent().attr('id');
	id = /^question_(\d+)$/.exec(id);
	var question_id = id[1];
	if(confirm('Удалить вопрос?'))
	{
		post_to_url('/qa/', {admin_action: 'delete_question', id: question_id});
	}
});

$('.edit_comment').click(function(e) {
	var id = $(this).parent().attr('id');
	id = /^comment_(\d+)$/.exec(id);
	var comment_id = id[1];
	if(confirm('Изменить комментарий?'))
	{
		var pathname = $(location).attr('href');
		var text = $(e.target).siblings('.comment').val();
		post_to_url(pathname, {admin_action: 'edit_comment', id: comment_id, text: text});
	}
});

$('.edit_question').click(function (e) {
	var id = $(this).parent().attr('id');
	id = /^question_(\d+)$/.exec(id);
	var question_id = id[1];
	if(confirm('Изменить вопрос?'))
	{
		var pathname = $(location).attr('href');
		var title = $(e.target).siblings('.title').val();
		var text = $(e.target).siblings('.text').val();
		post_to_url(pathname, {admin_action: 'edit_question', id: question_id, text: text, title: title});
	}
});

function post_to_url(path, params, method) {
    method = method || "post"; 
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}
</script>