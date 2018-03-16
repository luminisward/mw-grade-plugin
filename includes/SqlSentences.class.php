<?php
class SqlSentences {
	public static $s1rateRecordTable = 's1rate_records';

	public static $hasRatedSentence = 'SELECT id FROM %s WHERE user_id = %d AND page_id = %d';

	public static $rateWikiInsertSentence = 'INSERT INTO %s (`page_id`, `user_id`, `score`, `date`) VALUES (%d, %d , %d, NOW());';

	public static $getUserLastScoreSentence = 'SELECT score from %s WHERE page_id = %d AND user_id = %d ORDER BY id DESC limit 1;';

}
