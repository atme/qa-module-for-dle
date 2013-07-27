<?php

include_once ('engine/api/api.class.php');

if(!$question = $dle_api->load_from_cache ( 'qa_block', $cache ))
{
	$question = $dle_api->load_table ( 'qa_question', 'id, title', 'id', 'true' , 0, 10 , 'id', 'DESC' );
	$dle_api->save_to_cache('qa_block', $question);
}

foreach ($question as $value) {
	echo '<a href="/qa/question/'.$value['id'].'">'.$value['title'].'</a><br>';
}

?>