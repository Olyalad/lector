<?php

namespace frontend\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager as BasePager;


class LinkPager extends BasePager
{
    public $colorSuccess = '#dff0d8';
    public $colorError = '#f2dede';

    /**
     * @var array
     * 1 - правильный ответ
     * -1 - неправильный
     * 0 - нет ответа
     */
    public $resultTest = [];

    /**
     * @var bool
     * Блокировать ссылки?
     */
    public $disabledLink = false;


    /**
     * @inheritdoc
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->pagination->getPage();

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }

        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();

        $array = array_filter($this->resultTest, function ($el) {
            return $el != 0;
        });

        for ($i = $beginPage; $i <= $endPage; ++$i) {

            //раскараска кнопок пейджера
            $style = null;
            if (isset($this->resultTest[$i])) {
                if ($this->resultTest[$i] === 1)
                    $style = 'background-color: ' . $this->colorSuccess . ';';
                elseif ($this->resultTest[$i] === -1)
                    $style = 'background-color: ' . $this->colorError . ';';
            }


            if (!$this->disabledLink) {
                $disabled = false;
            } else {
                $disabled = true;
                if ($array) {
                    $max = max(array_keys($array));
                    if ($i <= $max || ($array[$max] == 1 && $i <= $max + 1) || $i < $currentPage + 1)
                        $disabled = false;
                } else {
                    if ($i < $currentPage + 1)
                        $disabled = false;
                }
            }

            $buttons[] = $this->renderPageButton($i + 1, $i, null, $disabled, $i == $currentPage, $style);
        }

        // next page
        if ($this->nextPageLabel !== false) {

            $disabled = false;
            if ($this->disabledLink)
                $disabled = $currentPage % 2 == 1;

            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1 || $disabled, false);
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }

    /**
     * @inheritdoc
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active, $style = null)
    {
        $options = ['class' => empty($class) ? $this->pageCssClass : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $tag = ArrayHelper::remove($this->disabledListItemSubTagOptions, 'tag', 'span');

            return Html::tag('li', Html::tag($tag, $label, $this->disabledListItemSubTagOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        if ($style) {
            if (isset($linkOptions['style'])) $linkOptions['style'] .= $style;
            else $linkOptions['style'] = $style;
        }

        return Html::tag('li', Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }

}
