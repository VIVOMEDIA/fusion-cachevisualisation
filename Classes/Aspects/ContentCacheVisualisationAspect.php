<?php

namespace VIVOMEDIA\Fusion\CacheVisualisation\Aspects;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\ObjectManagement\Configuration\Configuration;

/**
 * @Flow\Aspect()
 *
 * @package VIVOMEDIA\Fusion\CacheVisualisation\Aspects
 */
class ContentCacheVisualisationAspect
{
    const TYPE_CACHED = 'cached';
    const TYPE_UNCACHED = 'uncached';


    /**
     * @Flow\InjectConfiguration()
     * @var array
     */
    protected $_configuration;

    /**
     *
     * @param JoinPointInterface $joinPoint The current join point
     *
     * @return mixed Result of the target method
     * @Flow\Before("method(Neos\Fusion\Core\Cache\ContentCache->createCacheSegment()) && setting(VIVOMEDIA.Fusion.CacheVisualisation.enabled)")
     */
    public function wrapContentCacheCachedSegment(JoinPointInterface $joinPoint)
    {
        $content = $joinPoint->getMethodArgument('content');
        $path = $joinPoint->getMethodArgument('typoScriptPath');
        $lifetime = $joinPoint->getMethodArgument('lifetime') ? (new \DateTime)->setTimestamp($joinPoint->getMethodArgument('lifetime'))
            ->format('U = c') : 'null';

        if (!$this->_checkBlacklistedPath($path)) {
            $parameter = [
                'path' => $path,
                'tags' => implode(',', $joinPoint->getMethodArgument('tags')),
                'lifetime' => $lifetime,
            ];
            $content = $this->_wrapContent(self::TYPE_CACHED, $content, $parameter);

            $joinPoint->setMethodArgument('content', $content);
        }
    }

    private function _checkBlacklistedPath($path)
    {
        $blacklist = $this->_configuration['pathBlacklist'];

        return in_array($path, $blacklist);
    }

    private function _wrapContent($type, $content, $parameter)
    {
        $wrapperClasses = [
            'default' => $this->_configuration['output']['wrapperClassName'],
            'item' => $this->_configuration['output']['itemClassName'][$type],
        ];
        $content = '<div class="' . implode(' ', $wrapperClasses) . '" data-vivomedia-cache-visualisation=\'' . json_encode($parameter) . '\'>' . $content . '</div>';

        return $content;
    }

    /**
     *
     * @param JoinPointInterface $joinPoint The current join point
     *
     * @return mixed Result of the target method
     * @Flow\Around("method(Neos\Fusion\Core\Cache\ContentCache->createUncachedSegment()) && setting(VIVOMEDIA.Fusion.CacheVisualisation.enabled)")
     */
    public function wrapContentCacheUncachedSegment(JoinPointInterface $joinPoint)
    {
        $content = $joinPoint->getAdviceChain()->proceed($joinPoint);
        $path = $joinPoint->getMethodArgument('typoScriptPath');

        if (!$this->_checkBlacklistedPath($path)) {
            $parameter = [
                'path' => $path,
                'context' => array_keys($joinPoint->getMethodArgument('contextVariables')),
            ];
            $content = $this->_wrapContent(self::TYPE_UNCACHED, $content, $parameter);
        }

        return $content;
    }
}
