<?php

class S1RateBuildHTML
{

    public $htmlContent = '';
    private $title;

    private $textArr = array(
        '1' => '+2 - 极力推荐',
        '2' => '+1 - 值得一看',
        '3' => 'x0 - 看完就删',
        '4' => '-1 - 不太喜欢',
        '5' => '-2 - 感觉太差'
    );

    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    public function init()
    {
        global $wgRateInterval;
        $items = array(
            '1' => '',
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => ''
        );

        $resultData = RatingController::getPageScore($this->title);
        if ( empty($resultData) ){
            $resultData = array(
                'item1' => 0,
                'item2' => 0,
                'item3' => 0,
                'item4' => 0,
                'item5' => 0
            );
        }else{
            $resultData = $resultData['results'];
        }

        $totalCount = $resultData['item1'] + $resultData['item2'] + $resultData['item3'] + $resultData['item4'] + $resultData['item5'];

        // construct radio
        foreach ($this->textArr as $key => $item)
        {
            $items[$key] .= Html::radio('s1rateoption', false, ['value' => 3 - $key, 'id' => $item]);
        };

        // construct label
        foreach ($this->textArr as $key => $item)
        {
            $items[$key] .= Html::rawElement('label', ['for' => $item], $item);
        };

        foreach ($this->textArr as $key => $item)
        {
            $items[$key] = Html::rawElement(
                'div',
                [
                    'class' => 'option',
                    'style' => 'float: left'
                ],
                $items[$key]
            );
        };

        // construct result
        foreach ($items as $key => $item)
        {
            $items[$key] .= Html::rawElement('div',
                [
                    'class' => 'percent',
                ]
            );
        }

        // construct progress bar
        foreach ($this->textArr as $key => $item)
        {
            $percent = $totalCount ? ($resultData['item'.$key]/$totalCount*100) : 0;

            $progressBar = HTML::rawElement('div',
                [
                    'class' => 'meter',
                ]
            );
            $progressBar = Html::rawElement('div',
                [
                    'class' => 'progress',
                    'style' => 'overflow: hidden;'
                ],
                $progressBar
            );

            $items[$key] .= $progressBar;
        }

        // construct result
        foreach ($items as $key => $item)
        {
            $items[$key] = Html::rawElement(
                'div',
                [
                    'class' => 'item',
                ],
                $items[$key]
            );
        }

        // merge items
        $this->htmlContent .= array_reduce($items, array($this, 'mergeText'));

        // add submit button
        $this->htmlContent .= Html::submitButton('提交',['class' => 'button']);

        $this->htmlContent .= Html::rawElement('span',['class' => 'totalcount']);

        $this->htmlContent = Html::rawElement(
            'form',
            [
                'id' => 's1rateform',
            ],
            $this->htmlContent
        );

        $this->htmlContent .= $this->buildCommonModal();
        $this->htmlContent .= $this->buildAskModal();

        $this->htmlContent .= Html::inlineScript('var RateInterval = '.$wgRateInterval);
    }

    public function getHtmlContent(){
        return $this->htmlContent;
    }


    private function buildAskModal()
    {
        $html = '';

        $html .= Html::rawElement('p',[],'您已发表过评分，是否修改评分？');

        $html .= '<ul class="button-group">
          <li><button type="button" class="button" name="yes">Yes</button></li>
          <li><button type="button" class="button" name="no">No</button></li>
        </ul>';

        $html .= '<a class="close-reveal-modal">&times;</a>';

        $html = Html::rawElement(
            'div',
            [
                'id' => 'askModal',
                'class' => 'reveal-modal',
                'data-reveal' => ''
            ],
            $html
        );

        return $html;
    }

    private function buildCommonModal(){
        $html = '';


        $html .= Html::rawElement('p',['id' => 'commonModal']);

        $html .= '<a class="close-reveal-modal">&times;</a>';

        $html = Html::rawElement(
            'div',
            [
                'id' => 'commonModal',
                'class' => 'reveal-modal',
                'data-reveal' => ''
            ],
            $html
        );

        return $html;
    }
    private function mergeText($carry, $item)
    {
        return $carry . $item;
    }

    // Use browser to load module
    private function loadModule()
    {
        $loadJs = '(window.RLQ=window.RLQ||[]).push(function(){mw.loader.load(\'ext.S1Rate\')});';
        $loadJs = Html::rawElement(
            'script',
            [],
            $loadJs
        );
        $this->htmlContent .= $loadJs;
    }


}