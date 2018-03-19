<?php

class S1RateApiGetPageScore extends ApiBase {

	public function __construct(ApiMain $mainModule, $moduleName, $modulePrefix= '') {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
	}


	public function execute() {
        $params = $this->extractRequestParams();
        $page = Title::newFromID($params['pageid']);

		if (!isset( $page )) {
            $this->getResult()->addValue( null, 'message', 'Can\'t find page' );
            $this->getResult()->addValue( null, 'code', '1' );
			return true;
		}

		try {
			$resultData = RatingController::getPageScore( $page );
			if( empty($resultData) ){
			    $code = 1;
			    $message = 'No result';
            }else{
                $code = 0;
                $message = 'Success';
            }

            $this->getResult()->addValue( null, 'data', $resultData );
            $this->getResult()->addValue( null, 'code', $code );
            $this->getResult()->addValue( null, 'message', $message);

		} catch (Exception $ex) {
            $this->getResult()->addValue( null, 'message', 'Database Error' );
            $this->getResult()->addValue( null, 'code', '1' );
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
			)
        );
	}
}

