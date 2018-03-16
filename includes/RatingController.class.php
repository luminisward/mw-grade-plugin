<?php

class RatingController {
	protected $pageid = 0;
	protected $userid = 0;
	protected $service;
	protected $currentUser;
	private $ipAddress;

	public function __construct($pageid, $currentUser, $ipAddress ) {
		$this->currentUser = $currentUser;
		$this->userid = $currentUser->getId();
		$this->pageid = $pageid;
		$this->ipAddress = $ipAddress;
	}

	public static function ratePage( $pageId, $userId, $score ) {
		$data = array();
        $score = intval($score);
        $page = Article::newFromID($pageId);
        $item = 'item'.(string)($score + 3);

		try {
            $dbw = wfGetDB( DB_MASTER );

            $dbw->startAtomic(__METHOD__);
            $dbw->insert(
                's1rate_records',
                [
                    'page_id' => $pageId,
                    'user_id' => $userId,
                    'score' => $score,
                ],
                __METHOD__
            );
            $dbw->upsert(
                's1rate_results',
                [
                    'page_id' => $pageId,
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

			$data[ 'isSuccess' ] = 1;

			return $data;
		
		} catch ( Exception $ex ) {
			return array( isSuccess => 0, errorMessage => $ex->getMessage() );
		}
	}

	public static function getUserLastScore($pageId, $userId) {
		$ret = array();

		try {
            $dbr = wfGetDB( DB_SLAVE );

            $output = $dbr->select(
                's1rate_records',
                'score',
                [
                    'page_id = '.$pageId,
                    'user_id = '.$userId
                ],
                __METHOD__,
                [
                    'ORDER BY' => 'id DESC',
                    'LIMIT' => 1
                ]
            );

            if ( $output->numRows() > 0 ) {
                $result = $output->current();
                $ret = array(
                    'lastScore' => $result->score
                );
            }else{
                $ret['isSuccess'] = 0;
            }


			return $ret;
		
		} catch ( Exception $ex ) {
			$ret[ 'isSuccess' ] = 0;
			$ret[ 'message' ] = $ex->getMessage();
	
			return $ret;
		}
	}


	private function checkRatingContext() {
		// 
		if ( !isset( $this->pageid )) {
			return false;
		}

		if ( $this->pageid <= 0 ) {
			return false;
		}

		return true;
	}
}
