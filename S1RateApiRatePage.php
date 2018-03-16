<?php
class S1RateApiRatePage extends ApiBase {
	private $logger;

	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
		$this->logger = new MRLogging( __FILE__ );
	}


	public function execute() {
		$user = $this->getUser();

		if (!isset( $user )) {
			$this->logger->debug( __LINE__, 'User is empty' );
			throw new Exception( 'User is empty' );
		}

		$pageid = (int)$this->getMain()->getVal('pageid');
		$score = $this->getMain()->getVal( 'score' );

		if (!is_int( $pageid )) {
			$this->logger->error( __LINE__, 'pageid 格式不正确' );
			$this->getResult()->addValue( null, $this->getModuleName(), array(
					'isSuccess' => 0,
					'message' => 'pageid 格式不正确'
					));	
			return true;
		}


		$ratingController = new RatingController( $pageid, $user, $this->getRequest()->getIP() );

		try {
			$result = $ratingController->rate( $score );
			$this->getResult()->addValue( null, $this->getModuleName(), $result );

		} catch( Exception $ex) {
			$this->logger->error( __LINE__, 'Cannot get the rating score, pageid %d', $pageid );

			$this->getResult()->addValue( null, $this->getModuleName(), array(
					'isSuccess' => 0,
					'message' => '服务器错误，请稍后再试'
					));

		}	

		return true;
		
	}
	// public function needsToken() {
	// 	return 'csrf';
	// }

    public function isWriteMode() {
        return true;
    }

    public function getAllowedParams() {
		return array(
				'pageid' => array(
					ApiBase::PARAM_TYPE => 'integer',
					ApiBase::PARAM_REQUIRED => true
				),
				'score' => array(
					ApiBase::PARAM_TYPE => 'integer',
					ApiBase::PARAM_REQUIRED => true,
                    ApiBase::PARAM_RANGE_ENFORCE => true,
                    ApiBase::PARAM_MAX => 2,
                    ApiBase::PARAM_MIN => -2
                )
        );
	}

	public function getExample() {
	}

}
