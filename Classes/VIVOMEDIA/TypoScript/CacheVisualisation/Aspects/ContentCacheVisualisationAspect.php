<?php


namespace VIVOMEDIA\TypoScript\CacheVisualisation\Aspects;


use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Aspect()
 *
 * @package VIVOMEDIA\TypoScript\CacheVisualisation\Aspects
 */
class ContentCacheVisualisationAspect
{
    const TYPE_CACHED = 'cached';
    const TYPE_UNCACHED = 'uncached';

    /**
     *
     * @param \TYPO3\Flow\AOP\JoinPointInterface $joinPoint The current join point
     * @return mixed Result of the target method
     * @Flow\Before("method(TYPO3\TypoScript\Core\Cache\ContentCache->createCacheSegment())")
     */
    public function enrichContentCacheCachedSegment(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint)
    {
        $content = $joinPoint->getMethodArgument('content');
        $parameter = [
            'path' => $joinPoint->getMethodArgument('typoScriptPath'),
            'tags' => implode(',', $joinPoint->getMethodArgument('tags')),
            'lifetime' => $joinPoint->getMethodArgument('lifetime')
        ];
        $content = $this->_wrapContent(self::TYPE_CACHED, $content, $parameter);

        $joinPoint->setMethodArgument('content', $content);
    }

    /**
     *
     * @param \TYPO3\Flow\AOP\JoinPointInterface $joinPoint The current join point
     * @return mixed Result of the target method
     * @Flow\Around("method(TYPO3\TypoScript\Core\Cache\ContentCache->createUncachedSegment())")
     */
    public function enrichContentCacheUncachedSegment(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint)
    {
        $content = $joinPoint->getAdviceChain()->proceed($joinPoint);
        $parameter = [
            'Path' => $joinPoint->getMethodArgument('typoScriptPath')
        ];
        $content = $this->_wrapContent(self::TYPE_CACHED, $content, $parameter);

        return $content;
    }

    private function _wrapContent($type, $content, $parameter)
    {
        // default class
        // class cached
        // class uncached
        // style cached
        // style uncached

        $wrappedContent = '<div class="vivomedia-cachevisualisation vivomedia-cachevisualisation-cached" style="border: thin solid green;">';
        $wrappedContent .= '<div class="vivomedia-cachevisualisation-info-container" style="display: node">'.var_export($parameter, true).'</div>';
        $wrappedContent .= $content;
        $wrappedContent .= '</div>';
        return $wrappedContent;
    }
}
