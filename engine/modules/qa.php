<?php

switch ($_GET['action']) {
	case 'add':
		$category = new Category();
		$category_names = $category->getNames();
		foreach ($category_names as $key => $value) {
			$option .= '<option value="'.$key.'">'.$value.'</option>';
		}
		$tpl->load_template('qa_add_question.tpl');
		$tpl->set('categories', $option);
		if($_SESSION['dle_user_id'])
		{
			$tpl->set( '[captcha]', "<!--" );
			$tpl->set( '[/captcha]', "-->" );
		}
		else
		{
			$tpl->set( '[captcha]', "" );
			$tpl->set( '[/captcha]', "" );
		}
		$tpl->set( '[error-title]', "<!--" );
		$tpl->set( '[/error-title]', "-->" );
		$tpl->set( '[error-text]', "<!--" );
		$tpl->set( '[/error-text]', "-->" );
		$tpl->set( '[error-captcha]', "<!--" );
		$tpl->set( '[/error-captcha]', "-->" );
		$tpl->set('{title}', '');
		$tpl->set('{text}', '');
		break;

	case 'show_question':
		$comment = new Comment;
		$comments = $comment->showByQuestionId($_GET['question']);
		if($comments)
		{
			foreach ($comments as $key => $value) {
				$qa_comment = $tpl->sub_load_template('qa_comment.tpl');
				$template .= str_replace('{comment}', $value, $qa_comment);
			}
			$tpl->set('{comments}', $template);
		}
		else
		{
			$tpl->set('{comments}', '');
		}
		$question = new Question;
		$question = $question->showById($_GET['question']);
		$tpl->load_template('qa_question.tpl');
		$tpl->set('{title}', $question['title']);
		$tpl->set('{text}', $question['text']);
		if($_SESSION['dle_user_id'])
		{
			$tpl->set( '[captcha]', "<!--" );
			$tpl->set( '[/captcha]', "-->" );
		}
		else
		{
			$tpl->set( '[captcha]', "" );
			$tpl->set( '[/captcha]', "" );
		}
		$tpl->set( '[error-text]', "<!--" );
		$tpl->set( '[/error-text]', "-->" );
		$tpl->set( '[error-captcha]', "<!--" );
		$tpl->set( '[/error-captcha]', "-->" );
		$tpl->set('{text-comment}', '');
		break;
	
	default:
		# code...
		break;
}

$error = false;

if($_POST['add_question'])
{
	if(empty($_POST['title']))
	{
		$tpl->set( '[error-title]', "" );
		$tpl->set( '[/error-title]', "" );
		$error = true;
	}

	if(empty($_POST['text']))
	{
		$tpl->set( '[error-text]', "" );
		$tpl->set( '[/error-text]', "" );
		$error = true;
	}

	if(!$_SESSION['dle_user_id'] && $_SESSION['sec_code_session'] != $_POST['captcha'])
	{
		$tpl->set( '[error-captcha]', "" );
		$tpl->set( '[/error-captcha]', "" );
		$error = true;
	}

	if($error)
	{
		foreach ($category_names as $key => $value) {
			if ($key == $_POST['category'])
			{
				$categories .= '<option value="'.$key.'" selected>'.$value.'</option>';
			}
			else
			{				
				$categories .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		$tpl->set('categories', $categories);
		$tpl->set('{title}', $_POST['title']);
		$tpl->set('{text}', $_POST['text']);
	}
	else
	{
		$question = new Question;
		$question->add($_POST['title'], $_POST['text'], $_POST['category']);
		header('Location: /qa/question/'.$question->insert_id());
	}
	
}

if(isset($_POST['add_comment']))
{
	if(empty($_POST['text']))
	{
		$tpl->set( '[error-text]', "" );
		$tpl->set( '[/error-text]', "" );
		$error = true;
	}

	if(!$_SESSION['dle_user_id'] && $_SESSION['sec_code_session'] != $_POST['captcha'])
	{
		$tpl->set( '[error-captcha]', "" );
		$tpl->set( '[/error-captcha]', "" );
		$error = true;
	}

	if($error)
	{
		$tpl->set('{text-comment}', $_POST['text']);
	}
	else
	{
		$comment->add($_POST['text'], $_GET['question']);
		$comments = $comment->showByQuestionId($_GET['question']);
		if($comments)
		{
			$template = '';
			foreach ($comments as $key => $value) {
				$qa_comment = $tpl->sub_load_template('qa_comment.tpl');
				$template .= str_replace('{comment}', $value, $qa_comment);
			}
			$tpl->set('{comments}', $template);
		}
	}
}

$tpl->compile('content');

class Question extends db
{
	public function add ($title, $text, $category)
	{
		$this->query("INSERT INTO qa_question (`title`, `text`, `id_category`) VALUES ('{$title}', '{$text}', '{$category}')");
	}

	public function showById ($id)
	{
		$query = $this->query("SELECT `title`, `text` FROM qa_question WHERE id='{$id}'");
		return $this->get_row($query);
	}
}

class Comment extends db
{
	public function add($text, $id_question)
	{
		$this->query("INSERT INTO qa_comment (`text`, `id_question`) VALUES ('{$text}', '{$id_question}')");
	}

	public function showByQuestionId($id_question)
	{
		$query = $this->query("SELECT `id`, `text` FROM qa_comment WHERE id_question='{$id_question}'");
		if($this->num_rows($query))
		{
			while($row = $this->get_row($query))
			{
				$comment[$row['id']] = $row['text'];
			}
			return $comment;
		}
		else
		{
			return false;
		}
	}
}

class Category extends db
{
	public function getNames()
	{
		$query = $this->query('SELECT id, name FROM qa_category');
		while($row = $this->get_row($query))
		{
			$name[$row['id']] = $row['name'];
		}
		return $name;
	}
}
?>