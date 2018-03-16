<?php

class RatingController {
	protected $pageid = 0;
	protected $userid = 0;
	protected $service;
	protected $currentUser;
	private $logger;
	private $ipAddress;

	public function __construct($pageid, $currentUser, $ipAddress ) {
		$this->currentUser = $currentUser;
		$this->userid = $currentUser->getId();
		$this->pageid = $pageid;
		$this->service = new RatingService();
		$this->ipAddress = $ipAddress;

		$this->logger = new MRLogging( __FILE__ );

		$this->logger->trace( __LINE__, 'create RatingController, ratingId: %d, userId: %d, wikiId %d', $this->ratingId, $this->userid, $this->pageid );
	}

	public function rate( $score ) {
		$data = array();
        $score = intval($score);

		try {
			$this->service->ratePage( $this->pageid, $this->userid, $score);

			$data[ 'isSuccess' ] = 1;

			return $data;
		
		} catch ( Exception $ex ) {
			return array( isSuccess => 0, errorMessage => $ex->getMessage() );
		}
	}

	public function getScore() {
		$data = array();

		if ( !$this->checkRatingContext() ) {
			return array(
				'isSuccess' => 0,
				'message' => '系统错误：无法获取wiki页面信息'
            );
		}

		try {
            $data[ 'isSuccess' ] = 1;
			$data[ 'lastScore' ] = $this->service->getUserLastScore( $this->pageid, $this->userid );
			$this->logger->debug( __LINE__, 'Rating result, wikiId: %d, totalUsers %d, averageScore %d', $this->pageid, $totalUsers, $averageScore );
				
			return $data;
		
		} catch ( Exception $ex ) {
			$this->logger->debug( __LINE__, 'Get Rating total score error: %s', $ex->getMessage() );
			$data[ 'isSuccess' ] = 0;
			$data[ 'message' ] = $ex->getMessage();
	
			return $data;
		}
	}


	private function checkRatingContext() {
		// 
		if ( !isset( $this->pageid )) {
			$this->logger->debug( __LINE__, 'wiki Id is empty' );
			return false;
		}

		if ( $this->pageid <= 0 ) {
			$this->logger->debug( __LINE__, 'Wiki id is less than 0' );
			return false;
		}

		return true;
	}
}
