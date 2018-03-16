<?php

class S1RateApiRatePage extends ApiBase {

	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
	}


	public function execute() {
        $params = $this->extractRequestParams();
		$user = $this->getUser();
        $pageId = $params['pageid'];
        $score = $params['score'];

		if (!isset( $user )) {
			throw new Exception( 'User is empty' );
		}

		$userId = $user->getId();

		if (!is_int( $pageId )) {
			$this->getResult()->addValue( null, $this->getModuleName(), array(
					'isSuccess' => 0,
					'message' => 'pageid 格式不正确'
					));	
			return true;
		}


		try {
			$result = RatingController::ratePage( $pageId, $userId, $score );
			$this->getResult()->addValue( null, $this->getModuleName(), $result );

		} catch( Exception $ex) {
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

}
