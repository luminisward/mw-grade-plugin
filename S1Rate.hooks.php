<?php

final class S1RateHooks {
	public static function onSkinAfterContent( &$data, $skin ) {
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

		$data .=<<<EOF
  <div id="s1rate" class="clearfix">
	<table summary="poll panel" cellspacing="0" cellpadding="0" width="100%">
	<tbody>
			<tr>
					<td class="pslt">
							<input class="pr" type="radio" id="option_1" name="pollanswers[]" value="37185" onclick="$('pollsubmit').disabled = false">
					</td>
					<td class="pvt">
							<label for="option_1">+2：极力推荐</label>
					</td>
					<td class="pvts"></td>
			</tr>

			<tr>
					<td>&nbsp;</td>
					<td>
							<div class="pbg">
									<div class="pbr" style="width: 48%;background-color:#E92725;"></div>
							</div>
					</td>
					<td>13.95%
							<em style="color:#E92725">(6)</em>
					</td>
			</tr>
			<tr>
					<td class="pslt">
							<input class="pr" type="radio" id="option_2" name="pollanswers[]" value="37186" onclick="$('pollsubmit').disabled = false">
					</td>
					<td class="pvt">
							<label for="option_2">+1：值得一看</label>
					</td>
					<td class="pvts"></td>
			</tr>

			<tr>
					<td>&nbsp;</td>
					<td>
							<div class="pbg">
									<div class="pbr" style="width: 47%; background-color:#F27B21"></div>
							</div>
					</td>
					<td>46.51%
							<em style="color:#F27B21">(20)</em>
					</td>
			</tr>
			<tr>
					<td class="pslt">
							<input class="pr" type="radio" id="option_3" name="pollanswers[]" value="37187" onclick="$('pollsubmit').disabled = false">
					</td>
					<td class="pvt">
							<label for="option_3">x0：看完就删</label>
					</td>
					<td class="pvts"></td>
			</tr>

			<tr>
					<td>&nbsp;</td>
					<td>
							<div class="pbg">
									<div class="pbr" style="width: 33%; background-color:#F2A61F"></div>
							</div>
					</td>
					<td>32.56%
							<em style="color:#F2A61F">(14)</em>
					</td>
			</tr>
			<tr>
					<td class="pslt">
							<input class="pr" type="radio" id="option_4" name="pollanswers[]" value="37188" onclick="$('pollsubmit').disabled = false">
					</td>
					<td class="pvt">
							<label for="option_4">-1：不太喜欢</label>
					</td>
					<td class="pvts"></td>
			</tr>

			<tr>
					<td>&nbsp;</td>
					<td>
							<div class="pbg">
									<div class="pbr" style="width: 7%; background-color:#5AAF4A"></div>
							</div>
					</td>
					<td>6.98%
							<em style="color:#5AAF4A">(3)</em>
					</td>
			</tr>
			<tr>
					<td class="pslt">
							<input class="pr" type="radio" id="option_5" name="pollanswers[]" value="37189" onclick="$('pollsubmit').disabled = false">
					</td>
					<td class="pvt">
							<label for="option_5">-2：感觉太差</label>
					</td>
					<td class="pvts"></td>
			</tr>

			<tr>
					<td>&nbsp;</td>
					<td>
							<div class="pbg">
									<div class="pbr" style="width: 8px; background-color:#42C4F5"></div>
							</div>
					</td>
					<td>0.00%
							<em style="color:#42C4F5">(0)</em>
					</td>
			</tr>
			<tr>
					<td class="selector">&nbsp;</td>
					<td colspan="2">
							<button class="pn" type="submit" disabled="disabled" name="pollsubmit" id="pollsubmit" value="true">
									<span>提交</span>
							</button>
					</td>
			</tr>
	</tbody>
</table>  </div>
EOF;

		return true;
	}

	public static function addDatabases( DatabaseUpdater $updater ) {
		$updater->addExtensionUpdate(
		    array( 'addTable', SqlSentences::$s1rateRecordTable, __DIR__  . '/sql/create-rating-history-table.sql', true )
        );

		return true;
	}
}
