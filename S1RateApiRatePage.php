<?php

class S1RateApiRatePage extends ApiBase {

	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
	}


	public function execute() {
        $params = $this->extractRequestParams();
		$user = $this->getUser();
        $page = Title::newFromID($params['pageid']);
        $score = $params['score'];

        if ( $user->getId() == 0 ) {
            $this->getResult()->addValue( null, 'code', '1' );
            $this->getResult()->addValue( null, 'message', 'Can\'t get user' );
            return true;
        }

        if (!isset( $page )) {
            $this->getResult()->addValue( null, 'code', '1' );
            $this->getResult()->addValue( null, 'message', 'Can\'t find page' );
            return true;
        }

		try {
            if ( RatingController::ratePage( $page, $user, $score ) ){
                $code = 0;
                $message = 'Success';
            }else{
                $code = 1;
                $message = 'Request interval too short';
            }



            $this->getResult()->addValue( null, 'code', $code );
            $this->getResult()->addValue( null, 'message', $message);

		} catch( Exception $ex) {
            $this->getResult()->addValue( null, 'code', '1' );
            $this->getResult()->addValue( null, 'message', 'Database Error' );
		}

		return true;
		
	}
	 public function needsToken() {
	 	return 'csrf';
	 }

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
