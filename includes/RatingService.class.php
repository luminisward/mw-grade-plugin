<?php
class RatingService {
	private $logger;	


	public function __construct() {
		$this->logger = new MRLogging( __FILE__ );
    }

	public function getUserLastScore($pageid, $userid ) {
		try {
			$dbr = wfGetDB( DB_SLAVE );
			$sql = sprintf( SqlSentences::$getUserLastScoreSentence, $dbr->tableName( SqlSentences::$s1rateRecordTable ), $pageid, $userid );

			$output = $dbr->query( $sql, __METHOD__ );
			
			if ( $output->numRows() > 0 ) {
				$result = $output->current();
				return $result->score;
			} else {
				$this->logger->debug( __LINE__ , 'ERROR: Can\'t fetch rating scores from database.' );
			}
		} catch ( DBQueryError $ex ) {
			$this->logger->fatal( __LINE__, 'Database error: ' . $ex->getMessage() );
			throw $ex;
		}
	}

	public function ratePage($pageid, $userid, $score ) {
	    $page = Article::newFromID($pageid);
	    $item = 'item'.(string)($score + 3);

		try {

			$dbw = wfGetDB( DB_MASTER );

            $dbw->startAtomic(__METHOD__);
            $dbw->insert(
                's1rate_records',
                [
                    'page_id' => $pageid,
                    'user_id' => $userid,
                    'score' => $score,
                ],
                __METHOD__
            );
            $dbw->upsert(
                's1rate_results',
                [
                    'page_id' => $pageid,
                    'title' => $page->getTitle(),
                    $item => 1
                ],
                [ 'page_id' ],
                [
                    $item.' = '.$item.' + 1'
                ],
                __METHOD__
            );

            $dbw->endAtomic( __METHOD__ );


		} catch ( DBUnexpectedError $ex ) {
			$this->logger->fatal( __LINE__, 'Database error: ' . $ex->getMessage() );
			throw $ex;
		}
	}
}
