<?php

//error_reporting(0);

if(isset($_POST['admin_action']) && $member_id['user_group'] == 1)
{
	switch ($_POST['admin_action']) {
		case 'delete_comment':
			$comment = new Comment;
			$comment->delete($_POST['id']);
			break;

		case 'delete_question':
			$question = new Question;
			$question->delete($_POST['id']);
			break;

		case 'edit_comment':
			$comment = new Comment;
			$comment->edit($_POST['text'], $_POST['id']);
			break;

		case 'edit_question':
			$question = new Question;
			$question->edit($_POST['title'], $_POST['text'], $_POST['id']);
			break;
	}
}

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
				$template = str_replace('{id}', $key, $template);
				if($member_id['user_group'] == 1)
				{
					$template = str_replace('[admin]', '', $template);
					$template = str_replace('[/admin]', '', $template);
				}
				else
				{
					$template = str_replace('[admin]', '<!--', $template);
					$template = str_replace('[/admin]', '-->', $template);
				}
			}
			$tpl->set('{comments}', $template);
		}
		else
		{
			$tpl->set('{comments}', '');
		}
		$question = new Question;
		$question = $question->showById($_GET['question']);
		$tpl->load_template('qa_show_question.tpl');
		$tpl->set('{title}', $question['title']);
		if($member_id['user_group'] == 1)
		{
			$tpl->set( '[admin]', "" );
			$tpl->set( '[/admin]', "" );
		}
		else
		{
			$tpl->set( '[admin]', "<!--" );
			$tpl->set( '[/admin]', "-->" );
		}
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
		$tpl->set('{id}', $_GET['question']);
		$tpl->set('{text-comment}', '');
		break;

	case '':
		$tpl->load_template('qa_main.tpl');
		$category = new Category;
		$categories = $category->getNamesAndLinks();
		$category_template = $tpl->sub_load_template('qa_category.tpl');
		foreach ($categories as $key => $value) {
			$compiled_category_template .= str_replace('{link}', '/qa/'.$key.'/', $category_template);
			$compiled_category_template = str_replace('{text}', $value, $compiled_category_template);
		}
		$tpl->set('{categories}', $compiled_category_template);

		$question = new Question;
		if(empty($_GET['page']))
		{
			$questions = $question->showAll();
		}
		else
		{
			$questions = $question->showAll($_GET['page']);
		}
		$question_template = $tpl->sub_load_template('qa_question.tpl');
		foreach ($questions as $key => $value) {
			$compiled_question_template .= str_replace('{link}', '/qa/question/'.$key, $question_template);
			$compiled_question_template = str_replace('{title}', $value['title'], $compiled_question_template);
			$compiled_question_template = str_replace('{text}', $value['text'], $compiled_question_template);
		}
		$tpl->set('{questions}', $compiled_question_template);

		$page = new Page;
		if(!empty($_GET['page'])) $page->setCurrentPage($_GET['page']);
		$pages = $page->generateHTMLCode();
		$tpl->set('{pages}', $pages);
		break;

	default:
		$category = new Category;
		$categories = $category->getLinksAndIDs();
		if(!isset($categories[$_GET['action']])) header('Location: /qa/');
		$tpl->set('{categories}', '');

		$tpl->load_template('qa_main.tpl');
		
		$question = new Question;
		if(empty($_GET['page']))
		{
			$questions = $question->showAll(1, $categories[$_GET['action']]);
		}
		else
		{
			$questions = $question->showAll($_GET['page'], $categories[$_GET['action']]);
		}
		$question_template = $tpl->sub_load_template('qa_question.tpl');
		foreach ($questions as $key => $value) {
			$compiled_question_template .= str_replace('{link}', '/qa/question/'.$key, $question_template);
			$compiled_question_template = str_replace('{title}', $value['title'], $compiled_question_template);
			$compiled_question_template = str_replace('{text}', $value['text'], $compiled_question_template);
		}
		$tpl->set('{questions}', $compiled_question_template);

		$page = new Page;
		if(!empty($_GET['page'])) $page->setCurrentPage($_GET['page']);
		$page->setCategory($categories[$_GET['action']], $_GET['action']);
		$pages = $page->generateHTMLCode();
		$tpl->set('{pages}', $pages);
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
	protected $number_of_questions_on_page = 10;

	public function add ($title, $text, $category)
	{
		$this->query("INSERT INTO qa_question (`title`, `text`, `id_category`) VALUES ('{$title}', '{$text}', '{$category}')");
	}

	public function showById ($id)
	{
		$query = $this->query("SELECT `title`, `text` FROM qa_question WHERE id='{$id}'");
		return $this->get_row($query);
	}

	public function showAll($page = 1, $category = '')
	{ 
		$start_limit = $this->number_of_questions_on_page * ($page - 1);
		if($category) $category = '='.$category;
		$query = $this->query("SELECT id, title, `text`
		                       FROM qa_question
		                       WHERE id_category{$category}
		                       ORDER BY id
		                       LIMIT {$start_limit},{$this->number_of_questions_on_page}");
		if($this->num_rows($query))
		{
			while($row = $this->get_row($query))
			{
				$question[$row['id']] = array('title' => $row['title'], 'text' => $row['text']);
			}
			return $question;
		}
		else
		{
			return false;
		}
	}

	public function edit($title, $text, $id)
	{
		$this->query("UPDATE qa_question SET title='{$title}', `text`='{$text}' WHERE id={$id}");
	}

	public function delete($id)
	{
		$this->query("DELETE FROM qa_question WHERE id={$id}");
		$this->query("DELETE FROM qa_comment WHERE id_question={$id}");
	}

	protected function getNumber()
	{
		if($this->id_category)
		{ echo $category;
			$query = $this->query("SELECT COUNT(*) AS numrows FROM qa_question WHERE id_category={$this->id_category}");
		}
		else
		{
			$query = $this->query('SELECT COUNT(*) AS numrows FROM qa_question');
		}
		$result = $this->get_row($query);
		return $result['numrows'];
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

	public function edit($text, $id)
	{
		$this->query("UPDATE qa_comment SET `text`='{$text}' WHERE id={$id}");
	}

	public function delete($id)
	{
		$this->query("DELETE FROM qa_comment WHERE id={$id}");
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

	public function getNamesAndLinks()
	{
		$query = $this->query('SELECT name, link FROM qa_category');
		while($row = $this->get_row($query))
		{
			$name[$row['link']] = $row['name'];
		}
		return $name;
	}

	public function getLinksAndIDs()
	{
		$query = $this->query('SELECT link, id FROM qa_category');
		while($row = $this->get_row($query))
		{
			$name[$row['link']] = $row['id'];
		}
		return $name;
	}
}

class Page extends Question
{
	protected $id_category = false;
	protected $category = '';
	private $current_page = 1;

	public function setCategory($id_category, $category)
	{
		$this->category = $category.'/';
		$this->id_category = $id_category;
	}

	public function setCurrentPage($current_page)
	{
		$this->current_page = $current_page;
	}
	
	public function generateHTMLCode()
	{
		$number_of_pages_without_intentation = 7;
		$number_of_pages_with_indentation = 5;
		$number_of_pages_between_indentation_and_current_page = 2;

		$number_of_questions = $this->getNumber();
		$number_of_pages = ceil($number_of_questions/$this->number_of_questions_on_page);

		if($number_of_pages == 1) return '';
		if($number_of_pages <= $number_of_pages_without_intentation)
		{
			for ($i = 1; $i <= $number_of_pages; $i++) { 
				if($i == $this->current_page)
				{
					$result .= '<span>'.$i.'</span> ';
				}
				else
				{
					$result .= '<a href="/qa/'.$this->category.$i.'">'.$i.'</a> ';
				}
			}
		}
		else
		{
			if ($this->current_page < $number_of_pages_with_indentation)
			{
				for ($i = 1; $i <= $number_of_pages_with_indentation; $i++) { 
					if($i == $this->current_page)
					{
						$result .= '<span>'.$i.'</span> ';
					}
					else
					{
						$result .= '<a href="/qa/'.$this->category.$i.'">'.$i.'</a> ';
					}
				}
				$result .= '... <a href="'.$number_of_pages.'">'.$number_of_pages.'</a>';
			}
			elseif ($this->current_page > $number_of_pages - $number_of_pages_with_indentation)
			{
				$result .= '<a href="1">1</a> ... ';
				for ($i = $number_of_pages - $number_of_pages_with_indentation; $i <= $number_of_pages; $i++) { 
					if($i == $this->current_page)
					{
						$result .= '<span>'.$i.'</span> ';
					}
					else
					{
						$result .= '<a href="/qa/'.$this->category.$i.'">'.$i.'</a> ';
					}
				}
			}

			if ($this->current_page >= $number_of_pages_with_indentation &&
				$this->current_page <= $number_of_pages - $number_of_pages_with_indentation)
			{
				$result .= '<a href="1">1</a> ... ';
				for ($i = $this->current_page - $number_of_pages_between_indentation_and_current_page;
					 $i <= $this->current_page + $number_of_pages_between_indentation_and_current_page;
					 $i++) { 
					if($i == $this->current_page)
					{
						$result .= '<span>'.$i.'</span> ';
					}
					else
					{
						$result .= '<a href="/qa/'.$this->category.$i.'">'.$i.'</a> ';
					}
				}
				$result .= '... <a href="'.$number_of_pages.'">'.$number_of_pages.'</a>';
			}
		}

		return $result;
	}
}
?>