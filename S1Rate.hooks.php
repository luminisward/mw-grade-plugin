<?php

final class S1RateHooks {
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$pageTitle = $skin->getTitle();
		$request = $skin->getRequest();

		if ( $pageTitle->isSpecialPage()
			|| $pageTitle->getArticleID() == 0
			|| !$pageTitle->canTalk()
			|| $pageTitle->isTalkPage()
			|| method_exists( $pageTitle, 'isMainPage' ) && $pageTitle->isMainPage() // 主页
			|| in_array( $pageTitle->getNamespace(), array( NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_FILE, NS_USER ))
			|| $out->isPrintable()
			|| $request->getVal( 'action', 'view' ) != 'view'
			) {

			return true;
		}

		$html = new S1RateBuildHTML($pageTitle);
		$html->init();

        $out->prependHTML($html->getHtmlContent());
        $out->addModules('ext.S1Rate');

		return true;
	}

	public static function addDatabases( DatabaseUpdater $updater ) {
		$updater->addExtensionUpdate(
		    array( 'addTable', SqlSentences::$s1rateRecordTable, __DIR__  . '/sql/create-tables.sql', true )
        );

		return true;
	}
}
