<?php
class S1RateApiGetUserScore extends ApiBase {
	public $logger;

	public function __construct(ApiMain $mainModule, $moduleName, $modulePrefix= '') {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
		$this->logger = new MRLogging( __FILE__ );
		$this->logger->trace( __LINE__, 'create logger');
	}


	public function execute() {
//		$user = $this->getUser();

        $params = $this->extractRequestParams();
        $user = User::newFromName($params['target']);
        $pageid = $params['pageid'];

        if (!isset( $user )) {
			$this->logger->debug( __LINE__, "Can't get user" );
			throw new Exception( 'Can\'t get user' );
		}

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
			$result = $ratingController->getScore();

			$this->getResult()->addValue( null, $this->getModuleName(), $result );

		} catch (Exception $ex) {

			$this->logger->error( __LINE__, 'Cannot get the rating score, pageid %d', $pageid );

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

	public function getParamDescription() {
	}

	public function getExample() {
	}
}

