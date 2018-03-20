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
        $items = [];

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

        $resultData = RatingController::getPageScore($this->title);

        // construct result
        for ($i = 1; $i<=5; $i++)
        {
            $items[$i] .= Html::rawElement(
                'span',
                ['id' => 'sri'.$i],
                $resultData['item'.$i]
            );
        }

        $items = array_map(
            array($this,'wrapDiv'),
            $items
        );

        $this->htmlContent .= array_reduce($items, array($this, 'mergeText'));

        $this->htmlContent .= Html::submitButton('sm',[]);

        $this->htmlContent = Html::rawElement(
            'form',
            [
                'id' => 's1rateform'
            ],
            $this->htmlContent
        );
        $this->htmlContent .= '<script>var RateInterval = '.$wgRateInterval.'</script>';
    }

    public function getHtmlContent(){
        return $this->htmlContent;
    }

    private function wrapSpan($content)
    {
        return Html::rawElement('span', [], $content);
    }

    private function wrapLabel($content)
    {
        return Html::rawElement('label', [], $content);
    }

    private function wrapDiv($content)
    {
        return Html::rawElement('div', [], $content);
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