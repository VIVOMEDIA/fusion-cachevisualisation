<?php

namespace VIVOMEDIA\Fusion\CacheVisualisation\Aspects;

use Neos\Cache\CacheAwareInterface;
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
    const TYPE_DYNAMIC = 'dynamic';


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
        $path = $joinPoint->getMethodArgument('fusionPath');

        $lifetime = null;
        if ($joinPoint->getMethodArgument('lifetime')) {
            $lifetime = (new \DateTime)->modify(
                sprintf('+%d seconds',$joinPoint->getMethodArgument('lifetime'))
            )->format('U = c');
        }

        if (!$this->_checkBlacklistedPath($path)) {
            $parameter = [
                'path' => $path,
                'tags' => implode(',', $joinPoint->getMethodArgument('tags')),
                'lifetime' => $lifetime,
                'identifiers' => $this->getCacheIdentifiers($joinPoint->getMethodArgument('cacheIdentifierValues')),
            ];
            $content = $this->_wrapContent(self::TYPE_CACHED, $content, $parameter);

            $joinPoint->setMethodArgument('content', $content);
        }
    }

    private function getCacheIdentifiers(array $cacheIdentifierValues)
    {
        $strings = array_map(
            function($value) {
                if ($value instanceof CacheAwareInterface) {
                    return $value->getCacheEntryIdentifier();
                }
                return (string) $value;
            },
            $cacheIdentifierValues
        );
        return json_encode($strings);
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
        $path = $joinPoint->getMethodArgument('fusionPath');

        if (!$this->_checkBlacklistedPath($path)) {
            $parameter = [
                'path' => $path,
                'context' => array_keys($joinPoint->getMethodArgument('contextVariables')),
            ];
            $content = $this->_wrapContent(self::TYPE_UNCACHED, $content, $parameter);
        }

        return $content;
    }

    /**
     *
     * @param JoinPointInterface $joinPoint The current join point
     *
     * @return mixed Result of the target method
     * @Flow\Around("method(Neos\Fusion\Core\Cache\ContentCache->createDynamicCachedSegment()) && setting(VIVOMEDIA.Fusion.CacheVisualisation.enabled)")
     */
    public function wrapContentCacheDynamicSegment(JoinPointInterface $joinPoint)
    {
        $content = $joinPoint->getAdviceChain()->proceed($joinPoint);
        $path = $joinPoint->getMethodArgument('fusionPath');

        if (!$this->_checkBlacklistedPath($path)) {
            $parameter = [
                'path' => $path,
                'context' => array_keys($joinPoint->getMethodArgument('contextVariables')),
                'discriminator' => $joinPoint->getMethodArgument('cacheDiscriminator')
            ];
            $content = $this->_wrapContent(self::TYPE_DYNAMIC, $content, $parameter);
        }

        return $content;
    }
}
