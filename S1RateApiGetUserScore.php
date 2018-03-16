<?php

class S1RateApiGetUserScore extends ApiBase {

	public function __construct(ApiMain $mainModule, $moduleName, $modulePrefix= '') {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
	}


	public function execute() {
        $params = $this->extractRequestParams();
        $user = User::newFromName($params['target']);
        $pageid = $params['pageid'];

        if (!isset( $user )) {
			throw new Exception( 'Can\'t get user' );
		}

		if (!is_int( $pageid )) {
			$this->getResult()->addValue( null, $this->getModuleName(), array(
					'isSuccess' => 0,
					'message' => 'pageid 格式不正确'
					));	
			return true;
		}

		try {
			$resultData = RatingController::getUserLastScore( $pageid, $user->getId() );
            $message = 'Success';
            $code = 0;

            $this->getResult()->addValue( null, 'data', $resultData );
            $this->getResult()->addValue( null, 'code', $code );
            $this->getResult()->addValue( null, 'message', $message);

		} catch (Exception $ex) {
		    $this->getResult()->addValue( null, $this->getModuleName(), array(
					'isSuccess' => 0,
					'message' => '服务器错误，请稍后再试'
					));
		}
		return true;
	}

	public function getDescription() {
		return 'Get the rating average score and total rating users';
	}

	public function getAllowedParams() {
		return array(
			'pageid' => array (
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true
			),
            'target' => array(
                ApiBase::PARAM_TYPE => 'user',
                ApiBase::PARAM_REQUIRED => true
            )
        );
	}
}

