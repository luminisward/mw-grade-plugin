<?php

final class S1RateHooks {
	public static function onSkinAfterContent( &$data,Skin $skin ) {
		$pageTitle = $skin->getTitle();
		$output = $skin->getOutput();
		$request = $skin->getRequest();

		if ( $pageTitle->isSpecialPage()
			|| $pageTitle->getArticleID() == 0
			|| !$pageTitle->canTalk()
			|| $pageTitle->isTalkPage()
			|| method_exists( $pageTitle, 'isMainPage' ) && $pageTitle->isMainPage() // 主页
			|| in_array( $pageTitle->getNamespace(), array( NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_FILE, NS_USER ))
			|| $output->isPrintable()
			|| $request->getVal( 'action', 'view' ) != 'view' 
			) {

			return true;
		}


		$articleId = $skin->getTitle()->getArticleID();

		global $wgScriptPath;

		$htmlContent = '';

		$arr = array(1,2,3,4,5);
		$textArr = array(
		    '1' => '+2 - 极力推荐',
            '2' => '+1 - 值得一看',
            '3' => 'x0 - 看完就删',
            '4' => '-1 - 不太喜欢',
            '5' => '-2 - 感觉太差'
        );
		$items = array_map(
		    function($item){return Html::rawElement('span',[],$item);},
            $textArr
        );

        $resultData = RatingController::getPageScore( $pageTitle );

        for($i=1; $i<=5; $i++) {
            $items[$i] .= Html::rawElement(
                'span',
                [
                    'id' => 'sri'.$i
                ],
                $resultData['item'.$i]
            );
		}

		$items = array_map(
            function($item){return Html::rawElement('div',[],$item);},
            $items
        );

        $htmlContent .= array_reduce($items, function($carry, $item){$carry .= $item;return $carry;});

        $htmlContent = Html::rawElement(
		    'form',
            [
                'id' => 's1rateform'
            ],
            $htmlContent
        );







        $loadJs = '(window.RLQ=window.RLQ||[]).push(function(){mw.loader.load(\'ext.S1Rate\')});';
		$loadJs = Html::rawElement(
		    'script',
            [],
            $loadJs
        );
		$htmlContent .= $loadJs;

        $output->addModules('ext.S1Rate');
		$data .= $htmlContent;

		return true;
	}

	public static function addDatabases( DatabaseUpdater $updater ) {
		$updater->addExtensionUpdate(
		    array( 'addTable', SqlSentences::$s1rateRecordTable, __DIR__  . '/sql/create-rating-history-table.sql', true )
        );

		return true;
	}
}
